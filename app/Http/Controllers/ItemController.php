<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(): View
    {
        $items = Item::with('category')->orderBy('name', 'asc')->paginate(15);
        return view('items.index', compact('items'));
    }

    public function create(): View
    {
        $categories = $this->categoriesWithNextSku();
        return view('items.form', compact('categories'));
    }

    public function store(StoreItemRequest $request): RedirectResponse
    {
        $category = Category::findOrFail($request->category_id);

        // Generate SKU server-side: PREFIX-NNN (autonumber per kategori)
        $sku = $this->generateSku($category);

        $totalQty = $request->filled('total_qty') ? (int) $request->total_qty : 0;

        $item = Item::create([
            'category_id'   => $category->id,
            'location_id'   => 1, // default; bisa ditambah field lokasi nanti
            'name'          => $request->name,
            'sku'           => $sku,
            'total_qty'     => $totalQty,
            'available_qty' => $totalQty,
        ]);

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

    // ── Helper: generate SKU unik ───────────────────────────────────────
    private function generateSku(Category $category): string
    {
        $prefix = $category->sku_prefix ?: strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $category->name), 0, 4));

        return DB::transaction(function () use ($prefix, $category) {
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
        });
    }
}
