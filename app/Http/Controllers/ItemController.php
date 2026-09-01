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
        // Paksa stok awal menjadi 0
        $item = Item::create([
            'name'          => $request->name,
            'sku'           => $request->sku,
            'total_qty'     => 0,
            'available_qty' => 0,
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
        // Hanya update identitas, JANGAN PERNAH menyentuh qty di sini
        $item->update([
            'name' => $request->name,
            'sku'  => $request->sku,
        ]);

        return redirect()
            ->route('items.index')
            ->with('success', "Data Barang {$item->name} berhasil diperbarui.");
    }
}