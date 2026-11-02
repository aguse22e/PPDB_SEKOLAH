<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadPaymentProofRequest;
use App\Models\Application;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $applicantId = $request->user()->applicant?->id;

        $payments = Payment::with('application')
            ->whereHas('application', fn ($q) => $q->where('applicant_id', $applicantId))
            ->latest()
            ->paginate(10);

        return view('applicant.payments.index', compact('payments'));
    }

    public function show(Request $request, Payment $payment)
    {
        abort_unless($payment->application->applicant_id === $request->user()->applicant?->id, 403);

        return view('applicant.payments.show', compact('payment'));
    }

    /** Pendaftar mengunggah bukti transfer/QRIS untuk pembayaran manual. */
    public function uploadProof(UploadPaymentProofRequest $request, Payment $payment)
    {
        abort_unless($payment->application->applicant_id === $request->user()->applicant?->id, 403);

        abort_unless(in_array($payment->status, ['pending', 'failed'], true), 422,
            'Pembayaran ini tidak menunggu bukti pembayaran.');

        $path = $request->file('proof')->store("payment-proofs/{$payment->application_id}", 'local');

        $payment->update([
            'proof_path' => $path,
            'method' => $request->validated('method'),
            'reference_no' => $request->validated('reference_no'),
            'status' => 'waiting_verification',
        ]);

        return back()->with('success', 'Bukti pembayaran terkirim dan menunggu verifikasi keuangan.');
    }
}
