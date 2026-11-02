<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use Illuminate\Http\Request;

class MajorController extends Controller
{
    public function index()
    {
        $majors = Major::latest()->paginate(10);
        return view('admin.majors.index', compact('majors'));
    }

    public function create()
    {
        return view('admin.majors.create');
    }

    public function store(Request $request)
    {
        Major::create($request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:majors,code'],
            'name' => ['required', 'string', 'max:100'],
            'quota' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]));

        return redirect()->route('admin.majors.index')->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function edit(Major $major)
    {
        return view('admin.majors.edit', compact('major'));
    }

    public function update(Request $request, Major $major)
    {
        $major->update($request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:majors,code,' . $major->id],
            'name' => ['required', 'string', 'max:100'],
            'quota' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]));

        return redirect()->route('admin.majors.index')->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy(Major $major)
    {
        try {
            $major->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withErrors(['delete' => 'Jurusan ini tidak bisa dihapus karena sudah dipilih oleh pendaftar. Nonaktifkan saja jika tidak lagi dipakai.']);
        }

        return back()->with('success', 'Jurusan berhasil dihapus.');
    }
}
