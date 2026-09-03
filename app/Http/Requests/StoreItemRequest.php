<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $blockedPattern = '/(?:<|>|%3[CcEe]|on[a-z]+\s*=|javascript:|vbscript:|data:text\/html|script\s*[:=]|SELECT\s+|INSERT\s+INTO|UPDATE\s+.*SET|DELETE\s+FROM|DROP\s+TABLE|UNION\s+SELECT|ALTER\s+TABLE|CREATE\s+TABLE)/i';

        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name'        => [
                'required',
                'string',
                'max:255',
                'not_regex:' . $blockedPattern,
            ],
            'total_qty'   => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori barang wajib dipilih.',
            'category_id.exists'   => 'Kategori yang dipilih tidak valid.',
        ];
    }
}
