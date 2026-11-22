<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * WAJIB GANTI KATA SANDI AWAL.
 *
 * Akun yang dibuat seeder memakai kata sandi awal yang sudah diketahui
 * ('password'). Selama flag `must_change_password` masih aktif, user
 * diarahkan ke halaman ganti kata sandi dan tidak bisa membuka halaman
 * lain (kecuali halaman ganti kata sandi itu sendiri dan logout).
 *
 * Pakai di route sebagai: ->middleware(\App\Http\Middleware\EnsurePasswordChanged::class)
 * atau daftarkan alias 'password.changed' di bootstrap/app.php (lihat README).
 */
class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password
            && ! $request->routeIs('password.change', 'password.change.update', 'logout')) {
            return redirect()->route('password.change')->with(
                'success',
                'Kata sandi awal wajib diganti dulu sebelum memakai aplikasi.'
            );
        }

        return $next($request);
    }
}
