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
    public function index(): View
    {
        // Eager load untuk mencegah N+1 Query
        $movements = StockMovement::with(['item', 'user'])
                                  ->orderBy('created_at', 'desc')
                                  ->paginate(20);

        return view('stocks.index', compact('movements'));
    }

    /**
     * Menampilkan form input mutasi stok
     */
    public function create(): View
    {
        // Ambil semua barang untuk dropdown
        $items = Item::orderBy('name', 'asc')->get();
        return view('stocks.create', compact('items'));
    }

    /**
     * Proses penyimpanan mutasi stok
     */
    public function store(StoreStockMovementRequest $request): RedirectResponse
    {
        try {
            // Hardcode ID Admin sementara (utang teknis yang harus segera kita bayar nanti)
            $userId = 1; 

            // Lempar ke Service
            $movement = $this->stockService->adjustStock($request->validated(), $userId);

            $action = $movement->type === 'in' ? 'Penambahan' : 'Pengurangan';

            return redirect()
                ->route('stocks.index')
                ->with('success', "Mutasi Stok ($action) berhasil dicatat dengan Nomor Referensi: {$movement->reference_code}");

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}