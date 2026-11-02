<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Payment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $periodId = $request->period_id;

        $summary = [
            'per_jalur' => Application::with('path')
                ->when($periodId, fn ($q) => $q->where('period_id', $periodId))
                ->selectRaw('path_id, COUNT(*) as total')->groupBy('path_id')->get(),
            'per_jurusan' => Application::with('major')
                ->when($periodId, fn ($q) => $q->where('period_id', $periodId))
                ->whereNotNull('major_id')
                ->selectRaw('major_id, COUNT(*) as total')->groupBy('major_id')->get(),
            'per_status' => Application::query()
                ->when($periodId, fn ($q) => $q->where('period_id', $periodId))
                ->selectRaw('status, COUNT(*) as total')->groupBy('status')->get(),
            'dokumen_belum_lengkap' => Application::whereIn('status', ['draft', 'rejected_document'])
                ->when($periodId, fn ($q) => $q->where('period_id', $periodId))->count(),
            'total_pembayaran' => Payment::where('status', 'paid')
                ->whereHas('application', fn ($q) => $q->when($periodId, fn ($x) => $x->where('period_id', $periodId)))
                ->sum('amount'),
        ];

        return view('admin.reports.index', compact('summary'));
    }

    /** Export daftar pendaftar ke CSV (bisa dibuka di Excel). */
    public function exportApplications(Request $request): StreamedResponse
    {
        $query = Application::with(['applicant', 'period', 'path', 'major'])
            ->when($request->period_id, fn ($q, $id) => $q->where('period_id', $id))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest();

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['No Pendaftaran', 'Nama', 'NIK', 'Asal Sekolah', 'Periode', 'Jalur', 'Jurusan', 'Status', 'Tanggal Kirim']);

            $query->chunk(200, function ($rows) use ($out) {
                foreach ($rows as $app) {
                    fputcsv($out, [
                        $app->registration_no,
                        $this->csvSafe($app->applicant->full_name),
                        $app->applicant->nik,
                        $this->csvSafe($app->applicant->previous_school),
                        $app->period->name,
                        $app->path->name,
                        $app->major?->name,
                        $app->status,
                        $app->submitted_at?->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($out);
        }, 'laporan-pendaftar-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv']);
    }

    /** Export rekap pembayaran ke CSV. */
    public function exportPayments(Request $request): StreamedResponse
    {
        $query = Payment::with(['application.applicant'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest();

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Invoice', 'No Pendaftaran', 'Nama', 'Jenis', 'Nominal', 'Metode', 'Status', 'Tanggal Bayar']);

            $query->chunk(200, function ($rows) use ($out) {
                foreach ($rows as $payment) {
                    fputcsv($out, [
                        $payment->invoice_no,
                        $payment->application->registration_no,
                        $this->csvSafe($payment->application->applicant->full_name),
                        $payment->payment_type,
                        $payment->amount,
                        $payment->method,
                        $payment->status,
                        $payment->paid_at?->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($out);
        }, 'rekap-pembayaran-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv']);
    }

    /**
     * Cegah CSV/Formula Injection: sel teks yang diawali =, +, -, atau @
     * bisa dieksekusi Excel sebagai formula. Awali dengan tanda petik
     * agar diperlakukan sebagai teks biasa.
     */
    private function csvSafe(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return in_array($value[0], ['=', '+', '-', '@'], true) ? "'" . $value : $value;
    }
}
