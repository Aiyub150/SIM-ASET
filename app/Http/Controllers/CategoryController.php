<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('items')->latest()->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'sku_prefix' => 'nullable|string|max:10|unique:categories',
            'description' => 'nullable|string',
        ]);

        Category::create([
            'name' => $request->name,
            'sku_prefix' => strtoupper($request->sku_prefix),
            'description' => $request->description,
            'is_active' => true,
        ]);

        \App\Services\ActivityLogger::log(
            'CREATE',
            'Kategori',
            "Menambahkan kategori baru: {$request->name}",
            ['name' => $request->name, 'sku_prefix' => $request->sku_prefix],
            $request
        );

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'sku_prefix' => 'nullable|string|max:10|unique:categories,sku_prefix,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $beforeData = [
            'name'        => $category->name,
            'sku_prefix'  => $category->sku_prefix,
            'description' => $category->description,
        ];

        $category->update([
            'name' => $request->name,
            'sku_prefix' => strtoupper($request->sku_prefix),
            'description' => $request->description,
        ]);

        $afterData = [
            'name'        => $category->name,
            'sku_prefix'  => $category->sku_prefix,
            'description' => $category->description,
        ];

        \App\Services\ActivityLogger::log(
            'UPDATE',
            'Kategori',
            "Memperbarui kategori: {$category->name}",
            [
                'category_id' => $category->id,
                'before'      => $beforeData,
                'after'       => $afterData,
            ],
            $request
        );

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function toggleStatus(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);
        
        $status = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';

        \App\Services\ActivityLogger::log(
            'UPDATE',
            'Kategori',
            "Mengubah status kategori {$category->name} menjadi {$status}",
            ['category_id' => $category->id, 'is_active' => $category->is_active],
            request()
        );

        return redirect()->route('categories.index')->with('success', "Kategori berhasil {$status}.");
    }
}
