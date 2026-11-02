<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Memakai password broker bawaan Laravel (tabel password_reset_tokens)
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Link atur ulang password sudah dikirim ke email kamu.')
            : back()->withErrors(['email' => 'Email tidak ditemukan atau coba lagi sebentar lagi.']);
    }
}
