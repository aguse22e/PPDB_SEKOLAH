<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdmissionPeriod;
use App\Models\Announcement;
use App\Models\Application;
use Illuminate\Http\Request;

class PpdbPublicController extends Controller
{
    /** Halaman informasi PPDB untuk publik. */
    public function index()
    {
        $openPeriods = AdmissionPeriod::with('academicYear')
            ->where('status', 'open')
            ->orderBy('start_date')
            ->get();

        $announcements = Announcement::where('is_published', true)
            ->where(fn ($q) => $q->whereNull('publish_at')->orWhere('publish_at', '<=', now()))
            ->latest('publish_at')
            ->take(5)
            ->get();

        return view('public.ppdb.index', compact('openPeriods', 'announcements'));
    }

    public function checkForm()
    {
        return view('public.ppdb.check-form');
    }

    /** Cek status pendaftaran: wajib cocok nomor pendaftaran + tanggal lahir. */
    public function checkStatus(Request $request)
    {
        $data = $request->validate([
            'registration_no' => ['required', 'string', 'max:30'],
            'birth_date' => ['required', 'date'],
        ]);

        $application = Application::with(['applicant', 'period', 'path', 'major', 'payments', 'selectionResults'])
            ->where('registration_no', $data['registration_no'])
            ->whereHas('applicant', fn ($q) => $q->whereDate('birth_date', $data['birth_date']))
            ->first();

        if (! $application) {
            return back()->withErrors([
                'registration_no' => 'Data tidak ditemukan. Periksa nomor pendaftaran dan tanggal lahir.',
            ])->withInput();
        }

        return view('public.ppdb.status', compact('application'));
    }
}
