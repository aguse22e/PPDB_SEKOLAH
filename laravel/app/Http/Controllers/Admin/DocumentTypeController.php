<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    public function index()
    {
        $types = DocumentType::latest()->paginate(10);
        return view('admin.document-types.index', compact('types'));
    }

    public function create()
    {
        return view('admin.document-types.create');
    }

    public function store(Request $request)
    {
        DocumentType::create($this->rules($request));

        return redirect()->route('admin.document-types.index')->with('success', 'Jenis dokumen berhasil ditambahkan.');
    }

    public function edit(DocumentType $documentType)
    {
        return view('admin.document-types.edit', compact('documentType'));
    }

    public function update(Request $request, DocumentType $documentType)
    {
        $documentType->update($this->rules($request, $documentType));

        return redirect()->route('admin.document-types.index')->with('success', 'Jenis dokumen berhasil diperbarui.');
    }

    public function destroy(DocumentType $documentType)
    {
        try {
            $documentType->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withErrors(['delete' => 'Jenis dokumen ini tidak bisa dihapus karena sudah dipakai pada persyaratan jalur / berkas pendaftar. Nonaktifkan saja jika tidak lagi dipakai.']);
        }

        return back()->with('success', 'Jenis dokumen berhasil dihapus.');
    }

    private function rules(Request $request, ?DocumentType $type = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:document_types,code' . ($type ? ',' . $type->id : '')],
            'name' => ['required', 'string', 'max:100'],
            'allowed_extensions' => ['nullable', 'string', 'max:100'],
            'max_size_mb' => ['nullable', 'integer', 'min:1', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
