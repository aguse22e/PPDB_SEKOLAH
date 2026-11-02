<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Payment;
use Illuminate\Http\Request;

/**
 * Dashboard SISWA / CALON SISWA.
 *
 * Berbeda dari dashboard panitia: siswa hanya melihat datanya sendiri
 * plus PANDUAN LANGKAH — posisi tahap pendaftarannya (progress tracker)
 * dan satu aksi berikutnya yang harus dilakukan, supaya tidak bingung.
 */
class DashboardController extends Controller
{
    /** Urutan tahap yang dilalui setiap pendaftaran. */
    private const STEPS = ['Pendaftaran', 'Kirim & Dokumen', 'Verifikasi', 'Seleksi', 'Hasil', 'Daftar Ulang'];

    /** Indeks tahap aktif (0-5) untuk setiap status pendaftaran. */
    private const STEP_BY_STATUS = [
        'draft' => 0,
        'submitted' => 1,
        'document_review' => 2,
        'rejected_document' => 1, // kembali melengkapi dokumen
        'verified' => 3,
        'selection' => 3,
        'accepted' => 4,
        'waitlisted' => 4,
        'rejected' => 4,
        'registered' => 5,
        'withdrawn' => 0,
    ];

    /** Satu kalimat aksi berikutnya untuk setiap status. */
    private const NEXT_ACTION = [
        'draft' => 'Lengkapi dokumen persyaratan, lalu kirim pendaftaranmu dari halaman detail.',
        'submitted' => 'Pendaftaranmu sudah terkirim. Tunggu verifikasi dokumen oleh panitia — tidak ada yang perlu kamu lakukan sekarang.',
        'document_review' => 'Dokumenmu sedang diperiksa panitia. Pantau notifikasi untuk hasilnya.',
        'rejected_document' => 'Ada dokumen yang ditolak. Buka detail pendaftaran, baca alasannya, lalu unggah ulang dokumen yang benar.',
        'verified' => 'Dokumenmu lolos verifikasi. Tahap berikutnya adalah seleksi — siapkan diri dan pantau pengumuman jadwalnya.',
        'selection' => 'Kamu sedang dalam tahap seleksi. Hasil akan diumumkan di halaman Hasil Seleksi.',
        'accepted' => 'Selamat, kamu DITERIMA! Segera selesaikan pembayaran daftar ulang sebelum batas waktunya.',
        'waitlisted' => 'Kamu berada di daftar cadangan. Kami akan mengabari bila ada kuota yang kosong.',
        'rejected' => 'Mohon maaf, kamu belum diterima pada periode ini. Terima kasih sudah mendaftar.',
        'registered' => 'Daftar ulang selesai — kamu resmi tercatat sebagai siswa. Selamat bergabung!',
        'withdrawn' => 'Pendaftaran ini sudah kamu batalkan.',
    ];

    public function index(Request $request)
    {
        $applicant = $request->user()->applicant;

        $applications = $applicant
            ? $applicant->applications()->with(['period', 'path', 'major'])->latest()->get()
            : collect();

        $notifications = AppNotification::where('user_id', $request->user()->id)
            ->latest('sent_at')
            ->take(5)
            ->get();

        $latest = $applications->first();

        $progress = $latest ? [
            'steps' => self::STEPS,
            'current' => self::STEP_BY_STATUS[$latest->status] ?? 0,
            'status' => $latest->status,
            'next_action' => self::NEXT_ACTION[$latest->status] ?? null,
        ] : null;

        $unpaidBills = $applicant
            ? Payment::whereHas('application', fn ($q) => $q->where('applicant_id', $applicant->id))
                ->whereIn('status', ['pending', 'waiting_verification'])
                ->count()
            : 0;

        return view('applicant.dashboard', compact('applicant', 'applications', 'notifications', 'progress', 'unpaidBills'));
    }
}
