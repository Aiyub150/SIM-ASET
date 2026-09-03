<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBorrowerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $safeTextPattern = '/^(?=.*\pL)[\pL\pN\s\.\-\/&(),\'+]+$/u';

        return [
            'institution_name' => [
                'required',
                'string',
                'max:255',
                'regex:' . $safeTextPattern,
                Rule::unique('borrowers')->ignore($this->borrower),
            ],
            'pic_name'         => [
                'required',
                'string',
                'max:255',
                'regex:' . $safeTextPattern,
            ],
            'contact_number'   => ['required', 'string', 'max:20', 'regex:/^[0-9+\-()\s]{4,20}$/'],
            'address'          => ['nullable', 'string', 'max:1000', 'regex:/^[\pL\pN\s\.\-\/&(),\':;#+]+$/u'],
        ];
    }
}