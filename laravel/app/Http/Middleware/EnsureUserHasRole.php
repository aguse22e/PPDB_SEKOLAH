<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Pakai di route sebagai: ->middleware('role:admin,verifikator')
     * Daftarkan alias 'role' di bootstrap/app.php (lihat README).
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! in_array($user->role?->code, $roles, true)) {
            abort(403, 'Kamu tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
