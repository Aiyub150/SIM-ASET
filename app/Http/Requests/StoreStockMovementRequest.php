<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // unique memastikan tidak ada admin yang menginput ulang surat BAST yang sama
            'reference_code' => [
                'required',
                'string',
                'max:255',
                'regex:/^BAST\/\d{4}\/\d{2}\/\d{3}$/',
                'unique:stock_movements,reference_code',
            ],
            'item_id'        => ['required', 'integer', 'exists:items,id'],
            'type'           => ['required', 'in:in,out,broken,lost'],
            'qty'            => ['required', 'integer', 'min:1'],
            'notes'          => ['nullable', 'string', 'max:1000', 'regex:/^(?!.*(?:<|>|%3[CcEe]|on[a-z]+\s*=|javascript:|script|alert\(|SELECT\s|INSERT\s+INTO|UPDATE\s+.*SET|DELETE\s+FROM|DROP\s+TABLE)).*$/u'],
        ];
    }

    public function messages(): array
    {
        return [
            'reference_code.regex'  => 'Format nomor referensi tidak valid. Gunakan format contoh: BAST/2026/09/001.',
            'reference_code.unique' => 'Nomor Referensi/Surat ini sudah pernah diinput ke dalam sistem.',
            'type.in'               => 'Tipe mutasi tidak valid. Pilih antara masuk, keluar, rusak, atau hilang.',
            'qty.min'               => 'Jumlah barang yang dimutasi minimal 1.',
        ];
    }
}
