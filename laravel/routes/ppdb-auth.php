<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredApplicantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PPDB Auth Routes
|--------------------------------------------------------------------------
| Daftarkan di routes/web.php (cukup salah satu cara):
|   require __DIR__ . '/ppdb-auth.php';
|
| PENTING: Jangan pakai file ini bersamaan dengan route auth dari
| starter kit (Breeze/Jetstream/Fortify), karena nama route-nya sama:
| login, register, logout, password.request, password.reset, dst.
| Pilih satu: auth bawaan starter kit, ATAU auth PPDB ini.
|
| Register di sini KHUSUS pendaftar (role applicant dibuat otomatis).
| Akun admin/verifikator/finance dibuat lewat seeder atau oleh admin,
| tapi masuk lewat form login yang sama.
*/

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredApplicantController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredApplicantController::class, 'store']);

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/lupa-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/lupa-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
