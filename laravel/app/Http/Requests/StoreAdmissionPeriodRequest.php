<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdmissionPeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role?->code, ['admin'], true);
    }

    public function rules(): array
    {
        return [
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'name' => ['required', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:draft,open,closed'],
            'quotas' => ['nullable', 'array'],
            'quotas.*' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
