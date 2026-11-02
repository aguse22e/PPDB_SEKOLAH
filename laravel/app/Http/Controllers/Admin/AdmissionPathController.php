<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmissionPath;
use App\Models\DocumentType;
use Illuminate\Http\Request;

class AdmissionPathController extends Controller
{
    public function index()
    {
        $paths = AdmissionPath::with('documentTypes')->latest()->paginate(10);
        return view('admin.paths.index', compact('paths'));
    }

    public function create()
    {
        $documentTypes = DocumentType::where('is_active', true)->get();
        return view('admin.paths.create', compact('documentTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:admission_paths,code'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'quota' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'required_documents' => ['nullable', 'array'],
            'required_documents.*' => ['exists:document_types,id'],
            'optional_documents' => ['nullable', 'array'],
            'optional_documents.*' => ['exists:document_types,id'],
        ]);

        $path = AdmissionPath::create(collect($data)->only(['code', 'name', 'description', 'quota', 'is_active'])->all());

        $this->syncDocuments($path, $data);

        return redirect()->route('admin.paths.index')->with('success', 'Jalur pendaftaran berhasil ditambahkan.');
    }

    public function edit(AdmissionPath $path)
    {
        $documentTypes = DocumentType::where('is_active', true)->get();
        $path->load('documentTypes');
        return view('admin.paths.edit', compact('path', 'documentTypes'));
    }

    public function update(Request $request, AdmissionPath $path)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:admission_paths,code,' . $path->id],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'quota' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'required_documents' => ['nullable', 'array'],
            'required_documents.*' => ['exists:document_types,id'],
            'optional_documents' => ['nullable', 'array'],
            'optional_documents.*' => ['exists:document_types,id'],
        ]);

        $path->update(collect($data)->only(['code', 'name', 'description', 'quota', 'is_active'])->all());

        $this->syncDocuments($path, $data);

        return redirect()->route('admin.paths.index')->with('success', 'Jalur pendaftaran berhasil diperbarui.');
    }

    public function destroy(AdmissionPath $path)
    {
        try {
            $path->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withErrors(['delete' => 'Jalur ini tidak bisa dihapus karena sudah dipakai oleh pendaftar. Nonaktifkan saja jika tidak lagi dipakai.']);
        }

        return back()->with('success', 'Jalur pendaftaran berhasil dihapus.');
    }

    /** Syarat dokumen per jalur disimpan sebagai pivot dengan flag is_required. */
    private function syncDocuments(AdmissionPath $path, array $data): void
    {
        $sync = [];
        foreach ($data['required_documents'] ?? [] as $id) {
            $sync[$id] = ['is_required' => true];
        }
        foreach ($data['optional_documents'] ?? [] as $id) {
            $sync[$id] = ['is_required' => false];
        }
        $path->documentTypes()->sync($sync);
    }
}
