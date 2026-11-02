<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSelectionResultRequest;
use App\Models\Application;
use App\Models\SelectionResult;
use Illuminate\Http\Request;

/**
 * Penilaian seleksi oleh guru: daftar peserta tahap seleksi,
 * detail peserta, input / ubah / hapus nilai (tes tulis, wawancara, dll).
 *
 * Guru TIDAK menetapkan hasil akhir (diterima/ditolak) — itu wewenang
 * panitia lewat Admin\SelectionResultController::updateFinalStatus.
 */
class SelectionController extends Controller
{
    /** Daftar peserta yang boleh dinilai guru. */
    public function index(Request $request)
    {
        $applications = Application::whereIn('status', ['verified', 'selection', 'accepted', 'waitlisted', 'rejected'])
            ->with(['applicant', 'period', 'path', 'major', 'selectionResults'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->string('q');
                $query->where('registration_no', 'like', "%{$q}%")
                    ->orWhereHas('applicant', fn ($a) => $a->where('full_name', 'like', "%{$q}%"));
            })
            ->when($request->filled('penilaian'), function ($query) use ($request) {
                $request->string('penilaian') === 'belum'
                    ? $query->doesntHave('selectionResults')
                    : $query->has('selectionResults');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('guru.selection.index', compact('applications'));
    }

    /** Detail peserta + daftar nilai + form input nilai. */
    public function show(Application $application)
    {
        abort_unless(
            in_array($application->status, ['verified', 'selection', 'accepted', 'waitlisted', 'rejected'], true),
            404,
            'Peserta ini belum masuk tahap seleksi.'
        );

        $application->load(['applicant.guardians', 'period', 'path', 'major', 'selectionResults.assessor']);

        return view('guru.selection.show', compact('application'));
    }

    /** Simpan nilai baru (validasi & otorisasi sama dengan sisi admin). */
    public function store(StoreSelectionResultRequest $request, Application $application)
    {
        abort_unless(in_array($application->status, ['verified', 'selection'], true), 422,
            'Nilai hanya bisa diinput saat peserta berada pada tahap seleksi.');
        $application->selectionResults()->create([
            ...$request->validated(),
            'assessed_by' => $request->user()->id,
            'assessed_at' => now(),
        ]);

        if (in_array($application->status, ['verified', 'selection'], true)) {
            $application->update(['status' => 'selection']);
        }

        return back()->with('success', 'Nilai seleksi berhasil disimpan.');
    }

    /** Hapus nilai (mis. salah input). */
    public function destroy(SelectionResult $result)
    {
        abort_unless(in_array($result->application->status, ['verified', 'selection'], true), 422,
            'Nilai peserta yang hasil akhirnya sudah ditetapkan tidak bisa dihapus dari halaman guru.');

        $result->delete();

        return back()->with('success', 'Nilai seleksi berhasil dihapus.');
    }
}
