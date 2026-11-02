<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadDocumentRequest;
use App\Models\Application;
use App\Models\ApplicationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicationDocumentController extends Controller
{
    public function store(UploadDocumentRequest $request, Application $application)
    {
        $this->ensureOwnApplication($request, $application);

        abort_unless(in_array($application->status, ['draft', 'rejected_document'], true), 422,
            'Dokumen hanya bisa diunggah saat pendaftaran berstatus draft atau dokumen ditolak.');

        abort_unless(
            $application->path->documentTypes()->whereKey($request->validated('document_type_id'))->exists(),
            422,
            'Jenis dokumen ini tidak termasuk persyaratan jalur yang dipilih.'
        );

        $file = $request->file('document');

        // Simpan di storage private, database hanya menyimpan path
        $path = $file->store("documents/{$application->id}", 'local');

        // Upload ulang jenis yang sama = ganti berkas lama
        $existing = $application->documents()
            ->where('document_type_id', $request->validated('document_type_id'))
            ->first();

        if ($existing) {
            Storage::disk('local')->delete($existing->file_path);
            $existing->delete();
        }

        $application->documents()->create([
            'document_type_id' => $request->validated('document_type_id'),
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'file_size_kb' => (int) ceil($file->getSize() / 1024),
            'verification_status' => 'pending',
            'uploaded_at' => now(),
        ]);

        // Jika sebelumnya ditolak, kembalikan ke draft agar bisa dikirim ulang
        if ($application->status === 'rejected_document') {
            $application->update(['status' => 'draft']);
        }

        return back()->with('success', 'Dokumen berhasil diunggah.');
    }

    public function destroy(Request $request, Application $application, ApplicationDocument $document)
    {
        $this->ensureOwnApplication($request, $application);
        abort_unless($document->application_id === $application->id, 404);

        abort_unless(in_array($application->status, ['draft', 'rejected_document'], true), 422,
            'Dokumen tidak bisa dihapus setelah pendaftaran dikirim.');

        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }

    private function ensureOwnApplication(Request $request, Application $application): void
    {
        abort_unless($application->applicant_id === $request->user()->applicant?->id, 403);
    }
}
