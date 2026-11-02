<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationFinalStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role?->code, ['admin', 'verifikator'], true);
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:accepted,waitlisted,rejected'],
        ];
    }
}
