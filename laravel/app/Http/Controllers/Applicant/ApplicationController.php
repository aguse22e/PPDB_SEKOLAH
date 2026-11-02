<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApplicationRequest;
use App\Models\AdmissionPath;
use App\Models\AdmissionPeriod;
use App\Models\Application;
use App\Models\Major;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $applicant = $request->user()->applicant;

        // Tetap kembalikan paginator (view memanggil ->links()) walau profil belum ada.
        $applications = $applicant
            ? $applicant->applications()
                ->with(['period.academicYear', 'path', 'major'])
                ->latest()
                ->paginate(10)
            : Application::query()->whereRaw('1 = 0')->paginate(10);

        return view('applicant.applications.index', compact('applications'));
    }

    public function create()
    {
        $periods = AdmissionPeriod::where('status', 'open')->with('academicYear')->get();
        $paths = AdmissionPath::where('is_active', true)->get();
        $majors = Major::where('is_active', true)->get();

        return view('applicant.applications.create', compact('periods', 'paths', 'majors'));
    }

    public function store(StoreApplicationRequest $request)
    {
        $applicant = $request->user()->applicant;

        abort_if(! $applicant, 403, 'Isi biodata calon siswa terlebih dahulu.');

        $application = $applicant->applications()->create([
            ...$request->validated(),
            'registration_no' => 'TEMP', // diganti di bawah setelah ID terbentuk
            'status' => 'draft',
        ]);

        // Format nomor pendaftaran: PPDB-2027-0001
        $year = $application->period->academicYear?->year;
        $yearPart = $year ? substr($year, 0, 4) : now()->format('Y');
        $application->update([
            'registration_no' => sprintf('PPDB-%s-%04d', $yearPart, $application->id),
        ]);

        return redirect()
            ->route('applicant.applications.show', $application)
            ->with('success', 'Pendaftaran dibuat sebagai draft. Lengkapi dokumen lalu kirim.');
    }

    public function show(Request $request, Application $application)
    {
        $this->ensureOwnApplication($request, $application);

        $application->load([
            'period.academicYear', 'path', 'major', 'majorChoice2',
            'documents.documentType', 'payments', 'selectionResults',
        ]);

        $requiredDocuments = $application->path->documentTypes()->withPivot('is_required')->get();

        return view('applicant.applications.show', compact('application', 'requiredDocuments'));
    }

    /** Kirim pendaftaran: hanya bisa jika semua dokumen wajib sudah diunggah. */
    public function submit(Request $request, Application $application)
    {
        $this->ensureOwnApplication($request, $application);

        abort_unless($application->status === 'draft', 422, 'Pendaftaran ini sudah dikirim.');

        $period = $application->period;

        abort_unless($period->status === 'open', 422, 'Periode pendaftaran ini sudah ditutup.');

        abort_if(now()->lt($period->start_date) || now()->gt($period->end_date->endOfDay()), 422,
            'Periode pendaftaran tidak sedang berjalan.');

        $requiredIds = $application->path->documentTypes()
            ->wherePivot('is_required', true)
            ->pluck('document_types.id');

        $uploadedIds = $application->documents()->pluck('document_type_id');

        $missing = $requiredIds->diff($uploadedIds);

        if ($missing->isNotEmpty()) {
            return back()->withErrors([
                'documents' => 'Dokumen wajib belum lengkap. Unggah semua dokumen yang ditandai wajib.',
            ]);
        }

        $application->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return back()->with('success', 'Pendaftaran berhasil dikirim dan menunggu verifikasi.');
    }

    public function destroy(Request $request, Application $application)
    {
        $this->ensureOwnApplication($request, $application);

        abort_unless($application->status === 'draft', 422, 'Hanya pendaftaran draft yang bisa dihapus.');

        $application->delete();

        return redirect()
            ->route('applicant.applications.index')
            ->with('success', 'Draft pendaftaran berhasil dihapus.');
    }

    private function ensureOwnApplication(Request $request, Application $application): void
    {
        abort_unless($application->applicant_id === $request->user()->applicant?->id, 403);
    }
}
