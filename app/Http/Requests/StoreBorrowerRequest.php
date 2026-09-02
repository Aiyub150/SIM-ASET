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
        return [
            'institution_name' => ['required', 'string', 'max:255'],
            'pic_name'         => ['required', 'string', 'max:255'],
            'contact_number'   => ['required', 'string', 'max:50'],
            'address'          => ['nullable', 'string', 'max:500'],
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
