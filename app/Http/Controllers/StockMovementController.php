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

            return redirect()
                ->route('stocks.index')
                ->with('success', "Mutasi Stok ({$action}) berhasil dicatat. Ref: <strong>{$movement->reference_code}</strong>");

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    // ── Helper: generate reference code BAST/YYYY/MM/XXX ──────────────
    private function generateReferenceCode(): string
    {
        $year  = now()->format('Y');
        $month = now()->format('m');

        $countThisMonth = StockMovement::whereYear('created_at', $year)
                                       ->whereMonth('created_at', $month)
                                       ->count();

        return 'BAST/' . $year . '/' . $month . '/' . str_pad($countThisMonth + 1, 3, '0', STR_PAD_LEFT);
    }
}
