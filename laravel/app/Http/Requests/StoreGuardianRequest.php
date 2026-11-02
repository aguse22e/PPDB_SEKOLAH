<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuardianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'relationship' => ['required', 'in:ayah,ibu,wali'],
            'name' => ['required', 'string', 'max:150'],
            'nik' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'education' => ['nullable', 'string', 'max:50'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'income_range' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string'],
            'is_emergency_contact' => ['nullable', 'boolean'],
        ];
    }
}
