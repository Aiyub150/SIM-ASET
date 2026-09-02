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
            // category_id tidak boleh diubah, tapi tetap dikirim via hidden input untuk audit
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name'        => ['required', 'string', 'max:255'],
            // SKU tidak berubah saat edit — tidak perlu di-validate dari input
            'total_qty'   => ['nullable', 'integer', 'min:0'],
        ];
    }
}
