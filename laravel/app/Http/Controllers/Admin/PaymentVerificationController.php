<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Application;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentVerificationController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with(['application.applicant'])
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load('application.applicant');
        return view('admin.payments.show', compact('payment'));
    }

    /** Buat tagihan untuk sebuah pendaftaran, mis. biaya formulir / daftar ulang. */
    public function store(Request $request, Application $application)
    {
        $data = $request->validate([
            'payment_type' => ['required', 'in:formulir,tes,daftar_ulang,lainnya'],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        $payment = $application->payments()->create([
            ...$data,
            'invoice_no' => 'TEMP',
            'status' => 'pending',
        ]);

        $payment->update(['invoice_no' => sprintf('INV-%s-%04d', now()->format('Y'), $payment->id)]);

        return back()->with('success', "Tagihan {$payment->invoice_no} berhasil dibuat.");
    }

    public function verify(Request $request, Payment $payment)
    {
        abort_unless($payment->status === 'waiting_verification', 422, 'Pembayaran ini tidak menunggu verifikasi.');

        $payment->update([
            'status' => 'paid',
            'paid_at' => $payment->paid_at ?? now(),
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        // Pembayaran daftar ulang hanya menyelesaikan pendaftaran yang DITERIMA.
        // (Tanpa guard ini, verifikasi tagihan daftar ulang bisa menghidupkan
        //  lagi pendaftaran yang sudah ditolak menjadi 'registered'.)
        if ($payment->payment_type === 'daftar_ulang' && $payment->application->status === 'accepted') {
            $payment->application->update(['status' => 'registered']);
        }

        AppNotification::create([
            'user_id' => $payment->application->applicant->user_id,
            'title' => 'Pembayaran Terverifikasi',
            'message' => "Pembayaran {$payment->invoice_no} sebesar Rp" . number_format($payment->amount, 0, ',', '.') . ' sudah terverifikasi.',
            'channel' => 'app',
            'sent_at' => now(),
        ]);

        return back()->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    public function reject(Request $request, Payment $payment)
    {
        abort_unless($payment->status === 'waiting_verification', 422, 'Pembayaran ini tidak menunggu verifikasi.');

        $data = $request->validate(['notes' => ['required', 'string', 'max:500']]);

        $payment->update([
            'status' => 'failed',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
            'notes' => $data['notes'],
        ]);

        return back()->with('success', 'Pembayaran ditolak. Pendaftar bisa upload bukti ulang.');
    }
}
