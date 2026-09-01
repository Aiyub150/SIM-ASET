<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(): View
    {
        $items = Item::orderBy('name', 'asc')->paginate(15);
        return view('items.index', compact('items'));
    }

    public function create(): View
    {
        return view('items.form');
    }

    public function store(StoreItemRequest $request): RedirectResponse
    {
        // Jika total_qty diberikan oleh Super Admin, gunakan sebagai stok awal.
        $totalQty = $request->input('total_qty');

        $item = Item::create([
            'name'          => $request->name,
            'sku'           => $request->sku,
            'total_qty'     => $totalQty !== null ? (int)$totalQty : 0,
            'available_qty' => $totalQty !== null ? (int)$totalQty : 0,
        ]);

        return redirect()
            ->route('items.index')
            ->with('success', "Master Barang {$item->name} berhasil dibuat. Silakan ke menu Mutasi Stok untuk memasukkan saldo fisik awal.");
    }

    public function edit(Item $item): View
    {
        return view('items.form', compact('item'));
    }

    public function update(UpdateItemRequest $request, Item $item): RedirectResponse
    {
        // Update identitas
        $item->update([
            'name' => $request->name,
            'sku'  => $request->sku,
        ]);

        // Jika Super Admin mengubah total_qty, sesuaikan available_qty proporsional
        if ($request->filled('total_qty')) {
            $newTotal = (int)$request->input('total_qty');
            $oldTotal = (int)$item->getOriginal('total_qty');
            $oldAvailable = (int)$item->getOriginal('available_qty');

            if ($oldTotal === 0) {
                // Jika sebelumnya nol, treat available = newTotal
                $newAvailable = $newTotal;
            } else {
                // Menyesuaikan available secara proporsional berdasarkan perubahan total
                $ratio = $oldAvailable / $oldTotal;
                $newAvailable = (int) round($ratio * $newTotal);
            }

            $item->update([
                'total_qty' => $newTotal,
                'available_qty' => max(0, $newAvailable),
            ]);
        }

        return redirect()
            ->route('items.index')
            ->with('success', "Data Barang {$item->name} berhasil diperbarui.");
    }
}