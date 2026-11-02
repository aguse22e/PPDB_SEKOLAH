<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmissionPeriod;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Payment;
use App\Models\PeriodMajorQuota;
use Illuminate\Http\Request;

/**
 * Dashboard PANITIA — isinya DIBEDAKAN per peran:
 *  - admin           : ringkasan lengkap + master data (dashboard.blade)
 *  - verifikator     : antrean verifikasi berkas (dashboard-verifikator)
 *  - finance         : fokus pembayaran (dashboard-finance)
 *  - kepala_sekolah  : ringkasan eksekutif + keterserapan kuota (dashboard-kepsek)
 * Guru punya dashboard sendiri di App\Http\Controllers\Guru.
 */
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return match ($request->user()->role?->code) {
            'verifikator' => $this->verifikator(),
            'finance' => $this->finance(),
            'kepala_sekolah' => $this->kepalaSekolah(),
            default => $this->admin(),
        };
    }

    /** ADMIN: ringkasan lengkap semua modul. */
    private function admin()
    {
        $stats = [
            'total_pendaftar' => Application::count(),
            'menunggu_verifikasi' => Application::whereIn('status', ['submitted', 'document_review'])->count(),
            'diterima' => Application::where('status', 'accepted')->count(),
            'sudah_daftar_ulang' => Application::where('status', 'registered')->count(),
            'pembayaran_masuk' => Payment::where('status', 'paid')->sum('amount'),
            'pembayaran_menunggu' => Payment::where('status', 'waiting_verification')->count(),
        ];

        $perStatus = Application::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')->pluck('total', 'status');

        $perPath = Application::with('path')
            ->selectRaw('path_id, COUNT(*) as total')
            ->groupBy('path_id')->get();

        $perMajor = Application::with('major')
            ->selectRaw('major_id, COUNT(*) as total')
            ->whereNotNull('major_id')
            ->groupBy('major_id')->get();

        $latest = Application::with(['applicant', 'period', 'path', 'major'])
            ->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'perStatus', 'perPath', 'perMajor', 'latest'));
    }

    /** VERIFIKATOR: yang penting antrean berkas, bukan uang / master data. */
    private function verifikator()
    {
        $stats = [
            'antrean_pendaftaran' => Application::whereIn('status', ['submitted', 'document_review'])->count(),
            'dokumen_menunggu' => ApplicationDocument::where('verification_status', 'pending')->count(),
            'dokumen_terverifikasi' => ApplicationDocument::where('verification_status', 'verified')->count(),
            'dokumen_ditolak' => ApplicationDocument::where('verification_status', 'rejected')->count(),
            'pendaftaran_terverifikasi' => Application::whereIn('status', ['verified', 'selection', 'accepted', 'waitlisted', 'rejected', 'registered'])->count(),
        ];

        $queue = Application::whereIn('status', ['submitted', 'document_review'])
            ->with(['applicant', 'period', 'path', 'major'])
            ->withCount(['documents', 'documents as dokumen_pending' => fn ($q) => $q->where('verification_status', 'pending')])
            ->oldest()
            ->take(10)
            ->get();

        return view('admin.dashboard-verifikator', compact('stats', 'queue'));
    }

    /** KEUANGAN: yang penting tagihan & verifikasi pembayaran. */
    private function finance()
    {
        $stats = [
            'pembayaran_masuk' => Payment::where('status', 'paid')->sum('amount'),
            'menunggu_verifikasi' => Payment::where('status', 'waiting_verification')->count(),
            'belum_bayar' => Payment::where('status', 'pending')->count(),
            'total_tagihan' => Payment::count(),
            'total_nominal_tagihan' => Payment::sum('amount'),
        ];

        $perType = Payment::where('status', 'paid')
            ->selectRaw('payment_type, COUNT(*) as total, SUM(amount) as nominal')
            ->groupBy('payment_type')->get();

        $waiting = Payment::where('status', 'waiting_verification')
            ->with(['application.applicant'])
            ->oldest()
            ->take(10)
            ->get();

        return view('admin.dashboard-finance', compact('stats', 'perType', 'waiting'));
    }

    /** KEPALA SEKOLAH: ringkasan eksekutif + keterserapan kuota per jurusan. */
    private function kepalaSekolah()
    {
        $stats = [
            'total_pendaftar' => Application::count(),
            'diterima' => Application::where('status', 'accepted')->count(),
            'sudah_daftar_ulang' => Application::where('status', 'registered')->count(),
            'pembayaran_masuk' => Payment::where('status', 'paid')->sum('amount'),
        ];

        $perStatus = Application::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')->pluck('total', 'status');

        $perPath = Application::with('path')
            ->selectRaw('path_id, COUNT(*) as total')
            ->groupBy('path_id')->get();

        $openPeriod = AdmissionPeriod::where('status', 'open')->latest()->first();

        $quotas = $openPeriod
            ? PeriodMajorQuota::where('period_id', $openPeriod->id)
                ->with('major')
                ->get()
                ->map(function ($quota) use ($openPeriod) {
                    $quota->terisi = Application::where('period_id', $openPeriod->id)
                        ->where('major_id', $quota->major_id)
                        ->whereIn('status', ['accepted', 'registered'])
                        ->count();
                    return $quota;
                })
            : collect();

        return view('admin.dashboard-kepsek', compact('stats', 'perStatus', 'perPath', 'openPeriod', 'quotas'));
    }
}
