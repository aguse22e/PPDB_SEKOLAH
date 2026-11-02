<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuardianRequest;
use App\Models\Guardian;
use Illuminate\Http\Request;

class GuardianController extends Controller
{
    public function index(Request $request)
    {
        $applicant = $request->user()->applicant;
        $guardians = $applicant?->guardians()->get() ?? collect();

        return view('applicant.guardians.index', compact('applicant', 'guardians'));
    }

    public function store(StoreGuardianRequest $request)
    {
        $applicant = $request->user()->applicant;

        abort_if(! $applicant, 403, 'Isi biodata calon siswa terlebih dahulu.');

        $applicant->guardians()->create($request->validated());

        return back()->with('success', 'Data orang tua/wali berhasil ditambahkan.');
    }

    public function update(StoreGuardianRequest $request, Guardian $guardian)
    {
        $this->ensureOwnGuardian($request, $guardian);

        $guardian->update($request->validated());

        return back()->with('success', 'Data orang tua/wali berhasil diperbarui.');
    }

    public function destroy(Request $request, Guardian $guardian)
    {
        $this->ensureOwnGuardian($request, $guardian);

        $guardian->delete();

        return back()->with('success', 'Data orang tua/wali berhasil dihapus.');
    }

    private function ensureOwnGuardian(Request $request, Guardian $guardian): void
    {
        abort_unless($guardian->applicant_id === $request->user()->applicant?->id, 403);
    }
}
