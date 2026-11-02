<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDocument;
use Illuminate\Http\Request;

class DocumentVerificationController extends Controller
{
    public function verify(Request $request, ApplicationDocument $document)
    {
        $this->ensureReviewable($document);

        $document->update([
            'verification_status' => 'verified',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
            'notes' => null,
        ]);

        return back()->with('success', "Dokumen {$document->documentType->name} berhasil diverifikasi.");
    }

    public function reject(Request $request, ApplicationDocument $document)
    {
        $this->ensureReviewable($document);

        $data = $request->validate([
            'notes' => ['required', 'string', 'max:500'],
        ]);

        $document->update([
            'verification_status' => 'rejected',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
            'notes' => $data['notes'],
        ]);

        return back()->with('success', "Dokumen {$document->documentType->name} ditolak. Pendaftar diminta upload ulang.");
    }

    /** Dokumen hanya boleh diproses bila pendaftarannya sudah dikirim dan tidak dibatalkan. */
    private function ensureReviewable(ApplicationDocument $document): void
    {
        abort_if(in_array($document->application->status, ['draft', 'withdrawn'], true), 422,
            'Dokumen ini milik pendaftaran yang belum dikirim atau sudah dibatalkan.');
    }
}
