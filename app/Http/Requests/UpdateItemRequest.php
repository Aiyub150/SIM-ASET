<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $safeTextPattern = '/^(?=.*\pL)[\pL\pN\s\.\-\/&(),\'+]+$/u';

        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name'        => [
                'required',
                'string',
                'max:255',
                'regex:' . $safeTextPattern,
            ],
            'total_qty'   => ['nullable', 'integer', 'min:0'],
        ];
    }
}
