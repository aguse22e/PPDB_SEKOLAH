@extends('layouts.ppdb')
@section('title', 'Periksa Pembayaran')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.admin-sidebar')</div>
    <div class="col-md-9"><div class="card"><div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="h4 fw-bold mb-1">{{ $payment->invoice_no }}</h1>
                <div class="text-muted">{{ $payment->application->applicant->full_name }} • {{ $payment->application->registration_no }} • {{ ucfirst($payment->payment_type) }}</div>
            </div>
            <x-status-badge :status="$payment->status" />
        </div>
        <p class="display-6 fw-bold mt-3">Rp{{ number_format($payment->amount, 0, ',', '.') }}</p>
        <table class="table">
            <tr><th style="width:220px">Metode</th><td>{{ $payment->method ?? '-' }}</td></tr>
            <tr><th>No. Referensi</th><td>{{ $payment->reference_no ?? '-' }}</td></tr>
            <tr><th>Bukti Pembayaran</th><td>{{ $payment->proof_path ?? 'Belum ada bukti diunggah' }}</td></tr>
            <tr><th>Tanggal Bayar</th><td>{{ $payment->paid_at?->format('d M Y H:i') ?? '-' }}</td></tr>
            <tr><th>Dibuat</th><td>{{ $payment->created_at->format('d M Y H:i') }}</td></tr>
        </table>
        @if($payment->notes)<div class="alert alert-warning"><strong>Catatan:</strong> {{ $payment->notes }}</div>@endif

        @if($payment->status === 'waiting_verification')
            <div class="d-flex gap-2 flex-wrap">
                <form method="POST" action="{{ route('admin.payments.verify', $payment) }}">@csrf
                    <button class="btn btn-success" type="submit">Verifikasi — Nyatakan Lunas</button>
                </form>
                <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" class="d-flex gap-2">
                    @csrf
                    <input name="notes" class="form-control" placeholder="Alasan ditolak (wajib)" required style="min-width:260px">
                    <button class="btn btn-outline-danger" type="submit">Tolak Pembayaran</button>
                </form>
            </div>
        @endif
        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary mt-3">Kembali ke Daftar</a>
    </div></div></div>
</div>
@endsection
