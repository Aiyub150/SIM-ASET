<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreStockMovementRequest;
use App\Models\Item;
use App\Models\StockMovement;
use App\Services\StockService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function __construct(private StockService $stockService)
    {
    }

    /**
     * Menampilkan Kartu Stok (Ledger)
     */
    public function index(\Illuminate\Http\Request $request): View
    {
        $user = auth()->user();
        if ($user && !$user->can('stocks.view') && !$user->hasAnyRole(['Super Admin', 'Admin'])) {
            abort(403, 'Akses dilarang. Anda tidak memiliki izin untuk melihat kartu stok barang.');
        }

        $search = $request->query('search');
        $type = $request->query('type');

        $movements = StockMovement::with(['item', 'user'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($subQ) use ($search) {
                    $subQ->where('reference_code', 'like', "%{$search}%")
                         ->orWhereHas('item', function ($q) use ($search) {
                             $q->where('name', 'like', "%{$search}%")
                               ->orWhere('sku', 'like', "%{$search}%");
                         });
                });
            })
            ->when($type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $items         = Item::with('category')->orderBy('name', 'asc')->get();
        $suggestedCode = $this->generateReferenceCode();

        return view('stocks.index', compact('movements', 'search', 'type', 'items', 'suggestedCode'));
    }

    /**
     * Menampilkan form input mutasi stok
     */
    public function create(): View
    {
        $items         = Item::with('category')->orderBy('name', 'asc')->get();
        $suggestedCode = $this->generateReferenceCode();
        return view('stocks.create', compact('items', 'suggestedCode'));
    }

    /**
     * Proses penyimpanan mutasi stok
     */
    public function store(StoreStockMovementRequest $request): RedirectResponse
    {
        try {
            $userId = auth()->id();
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('images/stock_movements', 'public');
            }

            $movement = $this->stockService->adjustStock($data, $userId);

            $action = $movement->type === 'in' ? 'Penambahan' : 'Pengurangan';

            \App\Services\ActivityLogger::log(
                'STOCK_ADJUST',
                'Mutasi Stok',
                "Mencatat mutasi stok ({$action}) dengan kode referensi {$movement->reference_code}" . ($movement->image ? ' disertai bukti dokumentasi' : ''),
                ['movement_id' => $movement->id, 'type' => $movement->type, 'ref' => $movement->reference_code, 'image' => $movement->image],
                $request
            );

            return redirect()
                ->route('stocks.index')
                ->with('success', "Mutasi Stok ({$action}) berhasil dicatat. Ref: <strong>{$movement->reference_code}</strong>");

        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('StockMovementController@store error: ' . $e->getMessage(), ['exception' => $e]);
            $userMessage = ($e instanceof \Illuminate\Database\QueryException || str_contains($e->getMessage(), 'SQLSTATE'))
                ? 'Terjadi kesalahan sistem saat memproses mutasi stok. Silakan coba lagi.'
                : $e->getMessage();

            return back()
                ->withInput()
                ->with('error', $userMessage);
        }
    }

    /**
     * Upload bukti dokumentasi susulan untuk mutasi stok yang sudah ada
     */
    public function uploadProof(\Illuminate\Http\Request $request, StockMovement $movement): RedirectResponse
    {
        $user = auth()->user();
        if ($user && !$user->can('stocks.adjust') && !$user->hasAnyRole(['Super Admin', 'Admin'])) {
            abort(403, 'Akses dilarang. Anda tidak memiliki izin untuk mengunggah bukti mutasi stok.');
        }

        $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'image.required' => 'File gambar bukti dokumentasi wajib dipilih.',
            'image.image'    => 'File harus berupa gambar.',
            'image.mimes'    => 'Format gambar yang diperbolehkan: JPG, JPEG, PNG, WEBP.',
            'image.max'      => 'Ukuran gambar maksimal 2MB.',
        ]);

        if ($movement->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($movement->image)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($movement->image);
        }

        $imagePath = $request->file('image')->store('images/stock_movements', 'public');
        $movement->update(['image' => $imagePath]);

        \App\Services\ActivityLogger::log(
            'UPLOAD_PROOF',
            'Mutasi Stok',
            "Mengunggah bukti dokumentasi susulan untuk mutasi stok Ref: {$movement->reference_code}",
            [
                'movement_id'    => $movement->id,
                'reference_code' => $movement->reference_code,
                'image'          => $imagePath
            ],
            $request
        );

        return back()->with('success', "Bukti dokumentasi untuk Ref: {$movement->reference_code} berhasil diunggah.");
    }

    // ── Helper: generate reference code dinamis dari CodeFormatSetting ──────────────
    private function generateReferenceCode(): string
    {
        $setting = \App\Models\CodeFormatSetting::where('entity', 'stock_bast')->first();

        $prefix = ($setting && !empty($setting->prefix)) ? $setting->prefix : 'BAST';
        $sep = $setting->separator ?? '/';

        $year  = now()->format('Y');
        $month = now()->format('m');
        $datePart = '';

        if ($setting) {
            if ($setting->date_format === 'YYYYMMDD') $datePart = now()->format('Ymd');
            elseif ($setting->date_format === 'YYYY-MM') $datePart = now()->format('Y-m');
            elseif ($setting->date_format === 'YYYY/MM') $datePart = now()->format('Y/m');
            elseif ($setting->date_format === 'YYYY') $datePart = now()->format('Y');
        } else {
            $datePart = $year . $sep . $month;
        }

        $countThisMonth = StockMovement::whereYear('created_at', $year)
                                       ->whereMonth('created_at', $month)
                                       ->count();

        $padLength = ($setting && $setting->suffix_type === 'INCREMENT_4_DIGIT') ? 4 : 3;
        $suffix = ($setting && $setting->suffix_type === 'RANDOM_ALPHANUMERIC_5')
            ? strtoupper(\Illuminate\Support\Str::random(5))
            : str_pad($countThisMonth + 1, $padLength, '0', STR_PAD_LEFT);

        $parts = array_filter([$prefix, $datePart, $suffix]);
        return implode($sep, $parts);
    }
}
