@extends('layouts.ppdb')
@section('title', 'Pembayaran Admin')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.admin-sidebar')</div>
    <div class="col-md-9">
        <h1 class="h4 fw-bold mb-3">Pembayaran</h1>
        <form method="GET" class="card mb-3"><div class="card-body d-flex gap-2">
            <select name="status" class="form-select">
                <option value="">- Semua status -</option>
                @foreach(['pending','waiting_verification','paid','failed','expired','refunded'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
            <button class="btn btn-primary" type="submit">Filter</button>
        </div></form>

        <div class="card"><div class="table-responsive"><table class="table table-hover mb-0 align-middle">
            <thead><tr><th>Invoice</th><th>Pendaftar</th><th>No. Pendaftaran</th><th>Jenis</th><th>Nominal</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($payments as $payment)
                <tr><td class="fw-bold">{{ $payment->invoice_no }}</td><td>{{ $payment->application->applicant->full_name }}</td>
                <td>{{ $payment->application->registration_no }}</td><td>{{ ucfirst($payment->payment_type) }}</td>
                <td>Rp{{ number_format($payment->amount, 0, ',', '.') }}</td><td><x-status-badge :status="$payment->status" /></td>
                <td class="text-end"><a class="btn btn-sm btn-primary" href="{{ route('admin.payments.show', $payment) }}">Periksa</a></td></tr>
            @empty <tr><td colspan="7" class="text-center text-muted py-4">Belum ada pembayaran.</td></tr> @endforelse
            </tbody>
        </table></div></div>
        <div class="mt-3">{{ $payments->links() }}</div>
    </div>
</div>
@endsection
