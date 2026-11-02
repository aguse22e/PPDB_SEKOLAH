<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdmissionPeriodRequest;
use App\Models\AcademicYear;
use App\Models\AdmissionPeriod;
use App\Models\Major;

class AdmissionPeriodController extends Controller
{
    public function index()
    {
        $periods = AdmissionPeriod::with('academicYear')->latest()->paginate(10);
        return view('admin.periods.index', compact('periods'));
    }

    public function create()
    {
        $years = AcademicYear::all();
        $majors = Major::where('is_active', true)->get();
        return view('admin.periods.create', compact('years', 'majors'));
    }

    public function store(StoreAdmissionPeriodRequest $request)
    {
        $data = $request->validated();
        $quotas = $data['quotas'] ?? [];
        unset($data['quotas']);

        $period = AdmissionPeriod::create($data);

        // Kuota jurusan per periode: quotas[major_id] = jumlah
        foreach ($quotas as $majorId => $quota) {
            if ($quota !== null && $quota !== '') {
                $period->majorQuotas()->create(['major_id' => $majorId, 'quota' => (int) $quota]);
            }
        }

        return redirect()->route('admin.periods.index')->with('success', 'Periode PPDB berhasil ditambahkan.');
    }

    public function edit(AdmissionPeriod $period)
    {
        $years = AcademicYear::all();
        $majors = Major::where('is_active', true)->get();
        $period->load('majorQuotas');
        return view('admin.periods.edit', compact('period', 'years', 'majors'));
    }

    public function update(StoreAdmissionPeriodRequest $request, AdmissionPeriod $period)
    {
        $data = $request->validated();
        $quotas = $data['quotas'] ?? null;
        unset($data['quotas']);

        $period->update($data);

        if ($quotas !== null) {
            $period->majorQuotas()->delete();
            foreach ($quotas as $majorId => $quota) {
                if ($quota !== null && $quota !== '') {
                    $period->majorQuotas()->create(['major_id' => $majorId, 'quota' => (int) $quota]);
                }
            }
        }

        return redirect()->route('admin.periods.index')->with('success', 'Periode PPDB berhasil diperbarui.');
    }

    public function destroy(AdmissionPeriod $period)
    {
        try {
            $period->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withErrors(['delete' => 'Periode ini tidak bisa dihapus karena sudah memiliki pendaftar. Tutup saja periodenya jika pendaftaran sudah selesai.']);
        }

        return back()->with('success', 'Periode PPDB berhasil dihapus.');
    }
}
