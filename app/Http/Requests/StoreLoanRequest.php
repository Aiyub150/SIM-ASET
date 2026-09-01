<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
    /**
     * Tentukan apakah user diizinkan melakukan request ini.
     * Untuk sekarang kita set true, nanti bisa digabung dengan Spatie Permission.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi mutlak.
     */
    public function rules(): array
    {
        return [
            'borrower_id'     => ['required', 'integer', 'exists:borrowers,id'],
            'borrow_date'     => ['required', 'date'],
            'due_date'        => ['required', 'date', 'after_or_equal:borrow_date'],
            'notes'           => ['nullable', 'string', 'max:1000'],
            
            // Validasi Array Items
            'items'           => ['required', 'array', 'min:1'],
            
            // Kritis: 'distinct' mencegah user memanipulasi form dengan 
            // mengirimkan item_id yang sama dua kali untuk mengakali stok
            'items.*.item_id' => ['required', 'integer', 'exists:items,id', 'distinct'],
            'items.*.qty'     => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Kustomisasi pesan error agar mudah dipahami user (bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'items.*.item_id.distinct' => 'Ada barang yang terduplikasi di dalam daftar pinjaman Anda.',
            'items.*.qty.min' => 'Jumlah barang yang dipinjam minimal 1.',
            'due_date.after_or_equal' => 'Tanggal pengembalian tidak boleh lebih kecil dari tanggal pinjam.',
        ];
    }
}