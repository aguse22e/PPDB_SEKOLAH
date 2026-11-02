<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationVerificationController extends Controller
{
    public function index(Request $request)
    {
        $applications = Application::with(['applicant', 'period', 'path', 'major'])
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->period_id, fn ($q, $id) => $q->where('period_id', $id))
            ->when($request->path_id, fn ($q, $id) => $q->where('path_id', $id))
            ->when($request->search, fn ($q, $s) => $q->where('registration_no', 'like', "%{$s}%")
                ->orWhereHas('applicant', fn ($a) => $a->where('full_name', 'like', "%{$s}%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.applications.index', compact('applications'));
    }

    public function show(Application $application)
    {
        $application->load([
            'applicant.guardians', 'period.academicYear', 'path', 'major', 'majorChoice2',
            'documents.documentType', 'payments', 'selectionResults',
        ]);

        // Masuk antrean peninjauan dokumen
        if ($application->status === 'submitted') {
            $application->update(['status' => 'document_review']);
        }

        return view('admin.applications.show', compact('application'));
    }

    /** Setujui pendaftaran setelah semua dokumen terverifikasi. */
    public function verify(Request $request, Application $application)
    {
        abort_unless(in_array($application->status, ['submitted', 'document_review'], true), 422,
            'Pendaftaran ini tidak sedang menunggu verifikasi.');

        // Semua dokumen WAJIB sesuai jalur harus sudah diunggah DAN terverifikasi.
        // (Pengecekan lama hanya menghitung dokumen yang belum terverifikasi,
        //  sehingga pendaftaran tanpa dokumen sama sekali bisa lolos.)
        $requiredIds = $application->path->documentTypes()
            ->wherePivot('is_required', true)
            ->pluck('document_types.id');

        $verifiedRequired = $application->documents()
            ->whereIn('document_type_id', $requiredIds)
            ->where('verification_status', 'verified')
            ->pluck('document_type_id');

        if ($requiredIds->diff($verifiedRequired)->isNotEmpty()) {
            return back()->withErrors(['documents' => 'Dokumen wajib belum lengkap atau belum semuanya terverifikasi.']);
        }

        $unverified = $application->documents()
            ->where('verification_status', '!=', 'verified')
            ->count();

        if ($unverified > 0) {
            return back()->withErrors(['documents' => 'Masih ada dokumen yang belum terverifikasi.']);
        }

        $application->update([
            'status' => 'selection',
            'verified_at' => now(),
            'verified_by' => $request->user()->id,
        ]);

        $this->notify($application, 'Dokumen Terverifikasi', 'Dokumen kamu sudah terverifikasi. Pendaftaran lanjut ke tahap seleksi.');

        return back()->with('success', 'Pendaftaran berhasil diverifikasi dan lanjut ke seleksi.');
    }

    public function reject(Request $request, Application $application)
    {
        abort_unless(in_array($application->status, ['submitted', 'document_review'], true), 422,
            'Pendaftaran ini tidak sedang menunggu verifikasi.');

        $data = $request->validate([
            'notes' => ['required', 'string', 'max:1000'],
        ]);

        $application->update([
            'status' => 'rejected_document',
            'notes' => $data['notes'],
            'verified_by' => $request->user()->id,
        ]);

        $this->notify($application, 'Dokumen Perlu Perbaikan', $data['notes']);

        return back()->with('success', 'Pendaftaran dikembalikan ke pendaftar untuk diperbaiki.');
    }

    private function notify(Application $application, string $title, string $message): void
    {
        AppNotification::create([
            'user_id' => $application->applicant->user_id,
            'title' => $title,
            'message' => $message,
            'channel' => 'app',
            'sent_at' => now(),
        ]);
    }
}
