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
        return [
            // Memastikan nama unik, tapi mengecualikan ID yang sedang diupdate
            'institution_name' => [
                'required', 
                'string', 
                'max:255', 
                Rule::unique('borrowers')->ignore($this->borrower)
            ],
            'pic_name'         => ['required', 'string', 'max:255'],
            'contact_number'   => ['required', 'string', 'max:20'],
            'address'          => ['nullable', 'string', 'max:1000'],
        ];
    }
}