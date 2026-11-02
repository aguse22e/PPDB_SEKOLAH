<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSelectionResultRequest;
use App\Http\Requests\UpdateApplicationFinalStatusRequest;
use App\Models\AppNotification;
use App\Models\Application;
use App\Models\SelectionResult;
use Illuminate\Http\Request;

class SelectionResultController extends Controller
{
    public function store(StoreSelectionResultRequest $request, Application $application)
    {
        $application->selectionResults()->create([
            ...$request->validated(),
            'assessed_by' => $request->user()->id,
            'assessed_at' => now(),
        ]);

        if ($application->status === 'verified' || $application->status === 'selection') {
            $application->update(['status' => 'selection']);
        }

        return back()->with('success', 'Nilai seleksi berhasil disimpan.');
    }

    public function update(StoreSelectionResultRequest $request, SelectionResult $result)
    {
        $result->update([
            ...$request->validated(),
            'assessed_by' => $request->user()->id,
            'assessed_at' => now(),
        ]);

        return back()->with('success', 'Nilai seleksi berhasil diperbarui.');
    }

    public function destroy(SelectionResult $result)
    {
        $result->delete();
        return back()->with('success', 'Nilai seleksi berhasil dihapus.');
    }

    /** Tetapkan hasil akhir: diterima / cadangan / ditolak. */
    public function updateFinalStatus(UpdateApplicationFinalStatusRequest $request, Application $application)
    {
        $status = $request->validated('status');

        $application->update(['status' => $status]);

        $labels = [
            'accepted' => 'Selamat, kamu dinyatakan DITERIMA.',
            'waitlisted' => 'Kamu masuk daftar CADANGAN. Kami akan mengabari jika ada kuota yang kosong.',
            'rejected' => 'Mohon maaf, kamu belum diterima pada periode ini.',
        ];

        AppNotification::create([
            'user_id' => $application->applicant->user_id,
            'title' => 'Pengumuman Hasil Seleksi',
            'message' => $labels[$status] ?? 'Status pendaftaran kamu diperbarui.',
            'channel' => 'app',
            'sent_at' => now(),
        ]);

        return back()->with('success', 'Hasil akhir pendaftaran berhasil ditetapkan.');
    }
}
