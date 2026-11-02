<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\SelectionResult;

/**
 * Dashboard GURU / PENILAI SELEKSI.
 *
 * Berbeda dari dashboard admin: guru hanya berurusan dengan tahap
 * seleksi — siapa saja pesertanya, siapa yang belum dinilai, dan
 * nilai yang sudah masuk. Tidak ada master data, pembayaran, atau
 * verifikasi berkas di sini.
 */
class DashboardController extends Controller
{
    public function index()
    {
        // Peserta yang menjadi urusan guru: sudah terverifikasi atau sedang seleksi
        $participants = Application::whereIn('status', ['verified', 'selection'])
            ->with(['applicant', 'period', 'path', 'major', 'selectionResults'])
            ->latest();

        $stats = [
            'peserta_seleksi' => Application::whereIn('status', ['verified', 'selection'])->count(),
            'sedang_seleksi' => Application::where('status', 'selection')->count(),
            'belum_dinilai' => Application::whereIn('status', ['verified', 'selection'])
                ->doesntHave('selectionResults')->count(),
            'sudah_dinilai' => Application::whereIn('status', ['verified', 'selection'])
                ->has('selectionResults')->count(),
            'rata_rata_nilai' => round((float) SelectionResult::whereNotNull('score')->avg('score'), 1),
        ];

        $needScoring = (clone $participants)
            ->doesntHave('selectionResults')
            ->take(10)
            ->get();

        $recentlyScored = SelectionResult::with(['application.applicant', 'assessor'])
            ->latest('assessed_at')
            ->take(8)
            ->get();

        return view('guru.dashboard', compact('stats', 'needScoring', 'recentlyScored'));
    }
}
