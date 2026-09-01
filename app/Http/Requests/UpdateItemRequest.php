<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku'  => [
                'required', 
                'string', 
                'max:50', 
                Rule::unique('items')->ignore($this->item) // Abaikan SKU ini sendiri
            ],
            'total_qty' => ['nullable', 'integer', 'min:0'],
        ];
    }
}