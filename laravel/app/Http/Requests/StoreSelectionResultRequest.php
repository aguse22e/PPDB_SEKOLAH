<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSelectionResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role?->code, ['admin', 'verifikator', 'guru'], true);
    }

    public function rules(): array
    {
        return [
            'stage' => ['required', 'string', 'max:50'], // tes_tulis, wawancara, seleksi_berkas, prestasi
            'subject' => ['nullable', 'string', 'max:100'],
            'score' => ['nullable', 'numeric', 'min:0'],
            'max_score' => ['nullable', 'numeric', 'min:1'],
            'rank' => ['nullable', 'integer', 'min:1'],
            'result_status' => ['required', 'in:pending,passed,failed,waitlisted'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
