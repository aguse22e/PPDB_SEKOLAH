<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordChangeController extends Controller
{
    /** Form ganti kata sandi (dipakai saat wajib ganti kata sandi awal). */
    public function edit()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();
        $user->update([
            'password' => Hash::make($data['password']),
            'must_change_password' => false,
        ]);

        $role = $user->role?->code;
        $dashboard = $role === 'applicant'
            ? 'applicant.dashboard'
            : ($role === 'guru' ? 'guru.dashboard' : 'admin.dashboard');

        return redirect()->route($dashboard)->with('success', 'Kata sandi berhasil diganti. Silakan lanjutkan.');
    }
}
