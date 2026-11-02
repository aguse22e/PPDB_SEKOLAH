<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() === null; // hanya tamu yang boleh mendaftar
    }

    public function rules(): array
    {
        return [
            // Data akun
            'name' => ['required', 'string', 'max:100'], // nama akun (boleh nama orang tua / siswa)
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::defaults()],

            // Data awal calon siswa (biodata lengkap dilengkapi setelah login)
            'full_name' => ['required', 'string', 'max:150'],
            'gender' => ['required', 'in:L,P'],
            'birth_date' => ['required', 'date', 'before:today'],
            'nisn' => ['nullable', 'string', 'max:20', 'unique:applicants,nisn'],
            'previous_school' => ['nullable', 'string', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email ini sudah terdaftar. Silakan masuk dengan akun tersebut.',
            'nisn.unique' => 'NISN ini sudah terdaftar pada akun lain.',
        ];
    }
}
