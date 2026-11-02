<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * AUTO-LOGOUT karena tidak aktif (idle timeout).
 *
 * Setiap request dari user yang login memperbarui 'last_activity_time'
 * di session. Jika selisih waktu sejak aktivitas terakhir MELEBIHI
 * batas (default 5 menit), user dikeluarkan paksa dan diarahkan ke
 * halaman login dengan pesan penjelasan.
 *
 * Pakai di route sebagai: ->middleware('idle.timeout')
 * Daftarkan alias 'idle.timeout' di bootstrap/app.php (lihat README).
 *
 * Catatan: ini melengkapi SESSION_LIFETIME — middleware ini yang
 * memberi pesan ramah; SESSION_LIFETIME=5 di .env membuat session
 * benar-benar kedaluwarsa di sisi server pada batas yang sama.
 */
class IdleTimeoutLogout
{
    /** Batas tidak aktif sebelum auto-logout: 5 menit. */
    public const TIMEOUT_SECONDS = 5 * 60;

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivity = (int) $request->session()->get('last_activity_time', 0);

            if ($lastActivity > 0 && (time() - $lastActivity) > self::TIMEOUT_SECONDS) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with(
                    'success',
                    'Sesi kamu berakhir karena tidak ada aktivitas selama 5 menit. Silakan masuk lagi.'
                );
            }

            $request->session()->put('last_activity_time', time());
        }

        return $next($request);
    }
}
