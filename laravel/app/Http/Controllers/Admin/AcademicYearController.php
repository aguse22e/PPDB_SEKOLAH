<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $years = AcademicYear::latest()->paginate(10);
        return view('admin.academic-years.index', compact('years'));
    }

    public function create()
    {
        return view('admin.academic-years.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'year' => ['required', 'string', 'max:20', 'unique:academic_years,year'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (! empty($data['is_active'])) {
            AcademicYear::query()->update(['is_active' => false]);
        }

        AcademicYear::create($data);

        return redirect()->route('admin.academic-years.index')->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $data = $request->validate([
            'year' => ['required', 'string', 'max:20', 'unique:academic_years,year,' . $academicYear->id],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (! empty($data['is_active'])) {
            AcademicYear::where('id', '!=', $academicYear->id)->update(['is_active' => false]);
        }

        $academicYear->update($data);

        return redirect()->route('admin.academic-years.index')->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        try {
            $academicYear->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withErrors(['delete' => 'Tahun ajaran ini tidak bisa dihapus karena sudah dipakai oleh periode PPDB. Nonaktifkan saja jika tidak lagi dipakai.']);
        }

        return back()->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}
