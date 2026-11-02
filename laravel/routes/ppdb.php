<?php

use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\AdmissionPathController;
use App\Http\Controllers\Admin\AdmissionPeriodController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\ApplicationVerificationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DocumentTypeController;
use App\Http\Controllers\Admin\DocumentVerificationController;
use App\Http\Controllers\Admin\MajorController;
use App\Http\Controllers\Admin\PaymentVerificationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SelectionResultController;
use App\Http\Controllers\Applicant\ApplicantProfileController;
use App\Http\Controllers\Applicant\ApplicationController;
use App\Http\Controllers\Applicant\ApplicationDocumentController;
use App\Http\Controllers\Applicant\DashboardController as ApplicantDashboardController;
use App\Http\Controllers\Applicant\GuardianController;
use App\Http\Controllers\Applicant\PaymentController;
use App\Http\Controllers\Applicant\ResultController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\SelectionController as GuruSelectionController;
use App\Http\Controllers\Public\PpdbPublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PPDB Routes
|--------------------------------------------------------------------------
| File ini tidak menimpa routes/web.php bawaan.
| Daftarkan di routes/web.php dengan satu baris:
|   require __DIR__ . '/ppdb.php';
|
| Auth (login/register) memakai paket auth Laravel yang kamu pakai
| (Breeze / Jetstream / Fortify) — route di bawah hanya memakai
| middleware 'auth' dan 'role' (lihat README untuk mendaftarkan 'role').
*/

// ---------------------------------------------------------------------
// PUBLIK
// ---------------------------------------------------------------------
Route::prefix('ppdb')->name('ppdb.')->group(function () {
    Route::get('/', [PpdbPublicController::class, 'index'])->name('index');
    Route::get('/cek-status', [PpdbPublicController::class, 'checkForm'])->name('check-form');
    Route::post('/cek-status', [PpdbPublicController::class, 'checkStatus'])
        ->middleware('throttle:10,1') // batasi percobaan cek status
        ->name('check-status');
});

// ---------------------------------------------------------------------
// CALON SISWA / PENDAFTAR
// ---------------------------------------------------------------------
Route::middleware(['auth', 'idle.timeout', 'role:applicant'])
    ->prefix('pendaftar')
    ->name('applicant.')
    ->group(function () {
        Route::get('/dashboard', [ApplicantDashboardController::class, 'index'])->name('dashboard');

        // Biodata calon siswa
        Route::get('/biodata', [ApplicantProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/biodata', [ApplicantProfileController::class, 'update'])->name('profile.update');

        // Orang tua / wali
        Route::resource('wali', GuardianController::class)
            ->parameters(['wali' => 'guardian'])
            ->except(['create', 'edit', 'show'])
            ->names('guardians');

        // Pendaftaran
        Route::resource('pendaftaran', ApplicationController::class)
            ->parameters(['pendaftaran' => 'application'])
            ->only(['index', 'create', 'store', 'show', 'destroy'])
            ->names('applications');
        Route::post('/pendaftaran/{application}/kirim', [ApplicationController::class, 'submit'])
            ->name('applications.submit');

        // Dokumen
        Route::post('/pendaftaran/{application}/dokumen', [ApplicationDocumentController::class, 'store'])
            ->name('applications.documents.store');
        Route::delete('/pendaftaran/{application}/dokumen/{document}', [ApplicationDocumentController::class, 'destroy'])
            ->name('applications.documents.destroy');

        // Pembayaran
        Route::get('/pembayaran', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/pembayaran/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::post('/pembayaran/{payment}/bukti', [PaymentController::class, 'uploadProof'])
            ->name('payments.upload-proof');

        // Hasil seleksi
        Route::get('/pendaftaran/{application}/hasil', [ResultController::class, 'show'])
            ->name('applications.result');
    });

// ---------------------------------------------------------------------
// ADMIN / PANITIA
// ---------------------------------------------------------------------
Route::middleware(['auth', 'idle.timeout'])
    ->prefix('admin/ppdb')
    ->name('admin.')
    ->group(function () {

        // Dashboard: semua peran panitia boleh melihat ringkasan
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->middleware('role:admin,verifikator,finance,kepala_sekolah')
            ->name('dashboard');

        // Master data: khusus admin
        Route::middleware('role:admin')->group(function () {
            Route::resource('tahun-ajaran', AcademicYearController::class)
                ->parameters(['tahun-ajaran' => 'academicYear'])
                ->except(['show'])
                ->names('academic-years');
            Route::resource('periode', AdmissionPeriodController::class)
                ->parameters(['periode' => 'period'])
                ->except(['show'])
                ->names('periods');
            Route::resource('jalur', AdmissionPathController::class)
                ->parameters(['jalur' => 'path'])
                ->except(['show'])
                ->names('paths');
            Route::resource('jurusan', MajorController::class)
                ->parameters(['jurusan' => 'major'])
                ->except(['show'])
                ->names('majors');
            Route::resource('jenis-dokumen', DocumentTypeController::class)
                ->parameters(['jenis-dokumen' => 'documentType'])
                ->except(['show'])
                ->names('document-types');
            Route::resource('pengumuman', AnnouncementController::class)
                ->parameters(['pengumuman' => 'announcement'])
                ->except(['show'])
                ->names('announcements');
        });

        // Verifikasi berkas & pendaftaran: admin + verifikator
        Route::middleware('role:admin,verifikator')->group(function () {
            Route::get('/pendaftaran', [ApplicationVerificationController::class, 'index'])->name('applications.index');
            Route::get('/pendaftaran/{application}', [ApplicationVerificationController::class, 'show'])->name('applications.show');
            Route::post('/pendaftaran/{application}/verifikasi', [ApplicationVerificationController::class, 'verify'])->name('applications.verify');
            Route::post('/pendaftaran/{application}/tolak', [ApplicationVerificationController::class, 'reject'])->name('applications.reject');

            Route::post('/dokumen/{document}/verifikasi', [DocumentVerificationController::class, 'verify'])->name('documents.verify');
            Route::post('/dokumen/{document}/tolak', [DocumentVerificationController::class, 'reject'])->name('documents.reject');

            // Nilai seleksi & hasil akhir
            Route::post('/pendaftaran/{application}/nilai', [SelectionResultController::class, 'store'])->name('selection-results.store');
            Route::put('/nilai/{result}', [SelectionResultController::class, 'update'])->name('selection-results.update');
            Route::delete('/nilai/{result}', [SelectionResultController::class, 'destroy'])->name('selection-results.destroy');
            Route::post('/pendaftaran/{application}/hasil-akhir', [SelectionResultController::class, 'updateFinalStatus'])->name('applications.final-status');
        });

        // Keuangan: admin + finance
        Route::middleware('role:admin,finance')->group(function () {
            Route::get('/pembayaran', [PaymentVerificationController::class, 'index'])->name('payments.index');
            Route::get('/pembayaran/{payment}', [PaymentVerificationController::class, 'show'])->name('payments.show');
            Route::post('/pendaftaran/{application}/tagihan', [PaymentVerificationController::class, 'store'])->name('payments.store');
            Route::post('/pembayaran/{payment}/verifikasi', [PaymentVerificationController::class, 'verify'])->name('payments.verify');
            Route::post('/pembayaran/{payment}/tolak', [PaymentVerificationController::class, 'reject'])->name('payments.reject');
        });

        // Laporan: admin, keuangan, kepala sekolah
        Route::middleware('role:admin,finance,kepala_sekolah')->group(function () {
            Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
            Route::get('/laporan/export-pendaftar', [ReportController::class, 'exportApplications'])->name('reports.export-applications');
            Route::get('/laporan/export-pembayaran', [ReportController::class, 'exportPayments'])->name('reports.export-payments');
        });
    });

// ---------------------------------------------------------------------
// GURU / PENILAI SELEKSI
// Dashboard & penilaian khusus guru — tanpa master data, tanpa
// verifikasi berkas, tanpa pembayaran. Pemisahan ketat (RBAC):
// area guru hanya untuk peran guru — admin TIDAK ikut membuka,
// begitu pula guru tidak bisa membuka area admin / pendaftar.
// ---------------------------------------------------------------------
Route::middleware(['auth', 'idle.timeout', 'role:guru'])
    ->prefix('guru/ppdb')
    ->name('guru.')
    ->group(function () {
        Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');
        Route::get('/seleksi', [GuruSelectionController::class, 'index'])->name('selection.index');
        Route::get('/seleksi/{application}', [GuruSelectionController::class, 'show'])->name('selection.show');
        Route::post('/seleksi/{application}/nilai', [GuruSelectionController::class, 'store'])->name('selection.store');
        Route::delete('/nilai/{result}', [GuruSelectionController::class, 'destroy'])->name('selection.destroy');
    });
