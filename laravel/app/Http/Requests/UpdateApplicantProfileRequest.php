<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApplicantProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->code === 'applicant';
    }

    public function rules(): array
    {
        $applicantId = $this->user()?->applicant?->id;

        return [
            'full_name' => ['required', 'string', 'max:150'],
            'nik' => ['nullable', 'string', 'max:20', Rule::unique('applicants', 'nik')->ignore($applicantId)],
            'nisn' => ['nullable', 'string', 'max:20', Rule::unique('applicants', 'nisn')->ignore($applicantId)],
            'gender' => ['required', 'in:L,P'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'religion' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'rt_rw' => ['nullable', 'string', 'max:20'],
            'village' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'phone' => ['nullable', 'string', 'max:20'],
            'previous_school' => ['nullable', 'string', 'max:150'],
            'previous_school_address' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
}
