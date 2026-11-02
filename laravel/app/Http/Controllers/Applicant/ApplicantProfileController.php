<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateApplicantProfileRequest;
use Illuminate\Http\Request;

class ApplicantProfileController extends Controller
{
    public function edit(Request $request)
    {
        $applicant = $request->user()->applicant;

        return view('applicant.profile.edit', compact('applicant'));
    }

    public function update(UpdateApplicantProfileRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('applicant-photos', 'local');
        }
        unset($data['photo']);

        // 1 akun = 1 profil calon siswa: buat jika belum ada, update jika sudah ada
        $request->user()->applicant()->updateOrCreate([], $data);

        return redirect()
            ->route('applicant.profile.edit')
            ->with('success', 'Biodata berhasil disimpan.');
    }
}
