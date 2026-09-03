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
        $blockedPattern = '/(?:<|>|%3[CcEe]|on[a-z]+\s*=|javascript:|vbscript:|data:text\/html|script\s*[:=]|SELECT\s+|INSERT\s+INTO|UPDATE\s+.*SET|DELETE\s+FROM|DROP\s+TABLE|UNION\s+SELECT|ALTER\s+TABLE|CREATE\s+TABLE)/i';

        return [
            'institution_name' => [
                'required',
                'string',
                'max:255',
                'not_regex:' . $blockedPattern,
            ],
            'pic_name'         => [
                'required',
                'string',
                'max:255',
                'not_regex:' . $blockedPattern,
            ],
            'contact_number'   => ['required', 'string', 'max:50', 'regex:/^[0-9+\-()\s]{4,50}$/'],
            'address'          => ['nullable', 'string', 'max:500', 'not_regex:' . $blockedPattern],
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
