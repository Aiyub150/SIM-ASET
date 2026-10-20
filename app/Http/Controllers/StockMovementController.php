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

        return view('stocks.index', compact('movements', 'search', 'type'));
    }

    /**
     * Menampilkan form input mutasi stok
     */
    public function create(): View
    {
        $items         = Item::orderBy('name', 'asc')->get();
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

            $movement = $this->stockService->adjustStock($request->validated(), $userId);

            $action = $movement->type === 'in' ? 'Penambahan' : 'Pengurangan';

            \App\Services\ActivityLogger::log(
                'STOCK_ADJUST',
                'Mutasi Stok',
                "Mencatat mutasi stok ({$action}) dengan kode referensi {$movement->reference_code}",
                ['movement_id' => $movement->id, 'type' => $movement->type, 'ref' => $movement->reference_code],
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
