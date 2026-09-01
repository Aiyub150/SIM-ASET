<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReturnLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Memaksa format array untuk pengembalian massal/parsial
            'items'                 => ['required', 'array', 'min:1'],
            
            // Validasi mutlak: loan_item_id harus ada di tabel loan_items dan tidak boleh ganda di satu request
            'items.*.loan_item_id'  => ['required', 'integer', 'exists:loan_items,id', 'distinct'],
            
            // Tidak boleh mengembalikan barang dengan jumlah 0 atau minus
            'items.*.return_qty'    => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.*.loan_item_id.exists'   => 'Data item peminjaman tidak valid atau sudah dihapus.',
            'items.*.loan_item_id.distinct' => 'Terdapat duplikasi item dalam form pengembalian Anda.',
            'items.*.return_qty.min'        => 'Jumlah barang yang dikembalikan minimal 1.',
        ];
    }
}