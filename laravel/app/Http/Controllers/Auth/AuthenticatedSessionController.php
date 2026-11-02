<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    /** Login dipakai semua role; tujuan akhir dibedakan berdasarkan role. */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();
        $user->forceFill(['last_login_at' => now()])->save();

        return redirect()->intended($this->homeFor($user->role?->code));
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('ppdb.index');
    }

    public static function homeFor(?string $roleCode): string
    {
        // Setiap peran punya dashboard sendiri sesuai kebutuhannya.
        return match ($roleCode) {
            'applicant' => route('applicant.dashboard'),
            'guru' => route('guru.dashboard'),
            default => route('admin.dashboard'), // admin, verifikator, finance, kepala_sekolah (isi dashboard dibedakan lagi per peran)
        };
    }
}
