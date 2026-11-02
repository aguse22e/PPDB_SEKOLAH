<?php

namespace App\Http\Requests;

use App\Models\AdmissionPeriod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->applicant !== null;
    }

    public function rules(): array
    {
        return [
            'period_id' => ['required', 'exists:admission_periods,id'],
            'path_id' => ['required', 'exists:admission_paths,id'],
            'major_id' => ['nullable', 'exists:majors,id'],
            'major_choice_2_id' => ['nullable', 'exists:majors,id', 'different:major_id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $period = AdmissionPeriod::find($this->input('period_id'));

            if ($period && $period->status !== 'open') {
                $validator->errors()->add('period_id', 'Periode pendaftaran ini belum dibuka atau sudah ditutup.');
            }

            if ($period && now()->lt($period->start_date)) {
                $validator->errors()->add('period_id', 'Periode pendaftaran belum dimulai.');
            }

            if ($period && now()->gt($period->end_date->endOfDay())) {
                $validator->errors()->add('period_id', 'Periode pendaftaran sudah berakhir.');
            }
        });
    }
}
