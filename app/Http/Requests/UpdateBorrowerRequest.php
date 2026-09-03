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
        $blockedPattern = '/(?:<|>|%3[CcEe]|on[a-z]+\s*=|javascript:|vbscript:|data:text\/html|script\s*[:=]|SELECT\s+|INSERT\s+INTO|UPDATE\s+.*SET|DELETE\s+FROM|DROP\s+TABLE|UNION\s+SELECT|ALTER\s+TABLE|CREATE\s+TABLE)/i';

        return [
            'institution_name' => [
                'required',
                'string',
                'max:255',
                'not_regex:' . $blockedPattern,
                Rule::unique('borrowers')->ignore($this->borrower),
            ],
            'pic_name'         => [
                'required',
                'string',
                'max:255',
                'not_regex:' . $blockedPattern,
            ],
            'contact_number'   => ['required', 'string', 'max:20', 'regex:/^[0-9+\-()\s]{4,20}$/'],
            'address'          => ['nullable', 'string', 'max:1000', 'not_regex:' . $blockedPattern],
        ];
    }
}