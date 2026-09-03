<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Services\StockService;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Picqer\Barcode\BarcodeGeneratorSVG;

class ItemController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function index(): View
    {
        $items = Item::with('category')->orderBy('name', 'asc')->paginate(15);
        return view('items.index', compact('items'));
    }

    public function lookupBySku(Request $request)
    {
        $sku = trim((string) $request->query('sku', ''));

        if ($sku === '') {
            return response()->json([
                'success' => false,
                'message' => 'SKU tidak boleh kosong.',
            ], 422);
        }

        $item = Item::whereRaw('LOWER(sku) = ?', [strtolower($sku)])
            ->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Barang dengan SKU tersebut tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'item' => [
                'id' => $item->id,
                'sku' => $item->sku,
                'name' => $item->name,
                'available_qty' => $item->available_qty,
                'total_qty' => $item->total_qty,
            ],
        ]);
    }

    public function printLabel(Item $item, Request $request)
    {
        $type = strtolower((string) $request->query('type', 'both'));
        $labelData = $this->buildLabelData($item, $type);

        $pdf = Pdf::loadView('items.label', ['items' => $labelData]);
        $pdf->setPaper([0, 0, 190, 240], 'portrait');

        return $pdf->stream('Label_' . $item->sku . '.pdf');
    }

    public function printLabels(Request $request)
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $request->query('items', [])))));

        if (empty($ids)) {
            return back()->with('error', 'Pilih minimal satu barang untuk mencetak label.');
        }

        $items = Item::whereIn('id', $ids)->orderBy('name', 'asc')->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'Barang yang dipilih tidak ditemukan.');
        }

        $type = strtolower((string) $request->query('type', 'both'));
        $labelData = $items->flatMap(function (Item $item) use ($type) {
            return $this->buildLabelData($item, $type);
        })->all();

        $pdf = Pdf::loadView('items.label', ['items' => $labelData]);
        $pdf->setPaper([0, 0, 190, 240], 'portrait');

        return $pdf->stream('Label_Barang_' . now()->format('Ymd_His') . '.pdf');
    }

    private function buildLabelData(Item $item, string $type): array
    {
        $types = ['1d', '2d'];
        if (!in_array($type, $types, true)) {
            $type = 'both';
        }

        $barcodeImg = null;
        if ($type === '1d' || $type === 'both') {
            $generator = new BarcodeGeneratorSVG();
            $barcodeSvg = $generator->getBarcode($item->sku, $generator::TYPE_CODE_128, 2, 60);
            $barcodeImg = 'data:image/svg+xml;base64,' . base64_encode($barcodeSvg);
        }

        $qrImg = null;
        if ($type === '2d' || $type === 'both') {
            $qrImg = (new QRCode())->render($item->sku);
        }

        return [[
            'sku' => $item->sku,
            'name' => $item->name,
            'barcodeImg' => $barcodeImg,
            'qrImg' => $qrImg,
        ]];
    }

    public function create(): View
    {
        $categories = $this->categoriesWithNextSku();
        return view('items.form', compact('categories'));
    }

    public function store(StoreItemRequest $request): RedirectResponse
    {
        $category = Category::findOrFail($request->category_id);
        $totalQty = $request->filled('total_qty') ? (int) $request->total_qty : 0;

        $item = DB::transaction(function () use ($category, $totalQty, $request) {
            // Generate SKU di dalam transaksi agar autonumber aman
            $sku = $this->generateSkuInsideTransaction($category);

            // Buat item dengan stok 0 terlebih dahulu
            $item = Item::create([
                'category_id'   => $category->id,
                'location_id'   => 1, // default; bisa ditambah field lokasi nanti
                'name'          => $request->name,
                'sku'           => $sku,
                'total_qty'     => 0,
                'available_qty' => 0,
            ]);

            // Jika ada stok awal, catat sebagai stock movement tipe 'in'
            if ($totalQty > 0) {
                $this->stockService->adjustStock([
                    'item_id'        => $item->id,
                    'type'           => 'in',
                    'qty'            => $totalQty,
                    'reference_code' => 'INIT/' . $sku,
                    'notes'          => 'Stok awal saat data barang dibuat.',
                ], Auth::id());
            }

            return $item;
        });

        return redirect()
            ->route('items.index')
            ->with('success', "Barang \"{$item->name}\" berhasil ditambahkan dengan SKU: {$item->sku}");
    }

    public function edit(Item $item): View
    {
        $categories = $this->categoriesWithNextSku();
        return view('items.form', compact('item', 'categories'));
    }

    public function update(UpdateItemRequest $request, Item $item): RedirectResponse
    {
        // Nama boleh diperbarui; SKU dan category_id tidak berubah
        $item->update(['name' => $request->name]);

        if ($request->filled('total_qty')) {
            $newTotal = (int) $request->total_qty;
            $onLoan   = $item->total_qty - $item->available_qty; // qty yang masih dipinjam

            // Tolak jika total baru lebih kecil dari yang sedang dipinjam —
            // ini akan membuat available_qty negatif yang tidak masuk akal secara fisik.
            if ($newTotal < $onLoan) {
                return back()
                    ->withInput()
                    ->with('error', "Tidak dapat mengubah total menjadi {$newTotal}. Saat ini masih ada {$onLoan} unit yang sedang dipinjam. Total minimal yang diizinkan adalah {$onLoan}.");
            }

            $item->update([
                'total_qty'     => $newTotal,
                'available_qty' => $newTotal - $onLoan,
            ]);
        }

        return redirect()
            ->route('items.index')
            ->with('success', "Data Barang \"{$item->name}\" berhasil diperbarui.");
    }

    // ── Helper: daftar kategori beserta prefix & nomor SKU berikutnya ──
    private function categoriesWithNextSku()
    {
        return Category::orderBy('name')->get()->map(function (Category $cat) {
            $prefix = $cat->sku_prefix ?: strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $cat->name), 0, 4));
            $last   = Item::where('category_id', $cat->id)
                          ->where('sku', 'like', $prefix . '-%')
                          ->orderByDesc('sku')
                          ->value('sku');

            $nextNum = 1;
            if ($last) {
                $parts   = explode('-', $last);
                $lastNum = (int) end($parts);
                $nextNum = $lastNum + 1;
            }

            $cat->sku_prefix       = $prefix;
            $cat->next_sku_number  = $nextNum;
            return $cat;
        });
    }

    // ── Helper: generate SKU unik — HARUS dipanggil dari dalam DB::transaction() ──
    private function generateSkuInsideTransaction(Category $category): string
    {
        $prefix = $category->sku_prefix ?: strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $category->name), 0, 4));

        $last = Item::where('category_id', $category->id)
                    ->where('sku', 'like', $prefix . '-%')
                    ->lockForUpdate()
                    ->orderByDesc('sku')
                    ->value('sku');

        $nextNum = 1;
        if ($last) {
            $parts   = explode('-', $last);
            $nextNum = (int) end($parts) + 1;
        }

        return $prefix . '-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }
}
