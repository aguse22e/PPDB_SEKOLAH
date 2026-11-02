@extends('layouts.ppdb')
@section('title', 'Pembayaran')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.applicant-sidebar')</div>
    <div class="col-md-9">
        <h1 class="h4 fw-bold mb-3">Pembayaran Saya</h1>
        <div class="card"><div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead><tr><th>Invoice</th><th>No. Pendaftaran</th><th>Jenis</th><th>Nominal</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td class="fw-bold">{{ $payment->invoice_no }}</td>
                        <td>{{ $payment->application->registration_no }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</td>
                        <td>Rp{{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td><x-status-badge :status="$payment->status" /></td>
                        <td class="text-end"><a class="btn btn-sm btn-primary" href="{{ route('applicant.payments.show', $payment) }}">Detail / Bayar</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada tagihan pembayaran.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div></div>
        <div class="mt-3">{{ $payments->links() }}</div>
    </div>
</div>
@endsection
