<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $safeTextPattern = '/^(?=.*\pL)[\pL\pN\s\.\-\/&(),\'+]+$/u';

        return [
            'category_id'      => ['required', 'integer', 'exists:categories,id'],
            'ownership_status' => ['nullable', 'in:internal,vendor,external'],
            'owner_name'       => [
                'nullable',
                'string',
                'max:255',
                'required_if:ownership_status,vendor,external',
                'regex:' . $safeTextPattern,
            ],
            'sku'              => [
                'nullable',
                'string',
                'max:100',
                'required_if:ownership_status,vendor,external',
                'unique:items,sku',
            ],
            'name'             => [
                'required',
                'string',
                'max:255',
                'regex:' . $safeTextPattern,
            ],
            'total_qty'        => ['required', 'integer', 'min:1'],
            'image'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'total_qty.required'         => 'jumlah barang minimal 1 silahkan cek kembali pada jumlah barang',
            'total_qty.min'              => 'jumlah barang minimal 1 silahkan cek kembali pada jumlah barang',
            'total_qty.integer'          => 'jumlah barang minimal 1 silahkan cek kembali pada jumlah barang',
            'category_id.required'       => 'Kategori barang wajib dipilih.',
            'category_id.exists'         => 'Kategori yang dipilih tidak valid.',
            'ownership_status.required'  => 'Status kepemilikan barang wajib dipilih.',
            'ownership_status.in'        => 'Status kepemilikan tidak valid.',
            'owner_name.required_if'     => 'Nama vendor atau instansi pemilik wajib diisi jika bukan milik sendiri.',
            'sku.required_if'            => 'Kode SKU / barcode bawaan wajib diisi untuk barang milik vendor atau instansi lain.',
            'sku.unique'                 => 'Kode SKU tersebut sudah terdaftar di sistem inventaris.',
        ];
    }
}
