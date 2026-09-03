<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBorrowerRequest extends FormRequest
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
            ],
            'pic_name'         => [
                'required',
                'string',
                'max:255',
                'regex:' . $safeTextPattern,
            ],
            'contact_number'   => ['required', 'string', 'max:50', 'regex:/^[0-9+\-()\s]{4,50}$/'],
            'address'          => ['nullable', 'string', 'max:500', 'regex:/^[\pL\pN\s\.\-\/&(),\':;#+]+$/u'],
        ];
    }

    public function messages(): array
    {
        return [
            'institution_name.required' => 'Nama instansi wajib diisi.',
            'pic_name.required'         => 'Nama penanggung jawab wajib diisi.',
            'contact_number.required'   => 'Nomor kontak wajib diisi.',
        ];
    }
}
