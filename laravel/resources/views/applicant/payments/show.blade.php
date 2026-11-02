@extends('layouts.ppdb')
@section('title', 'Detail Pembayaran')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.applicant-sidebar')</div>
    <div class="col-md-9">
        <div class="card"><div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1 class="h4 fw-bold mb-1">{{ $payment->invoice_no }}</h1>
                    <div class="text-muted">{{ $payment->application->registration_no }} • {{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</div>
                </div>
                <x-status-badge :status="$payment->status" />
            </div>
            <p class="display-6 fw-bold mt-3">Rp{{ number_format($payment->amount, 0, ',', '.') }}</p>
            @if($payment->notes)<div class="alert alert-warning"><strong>Catatan keuangan:</strong> {{ $payment->notes }}</div>@endif

            @if(in_array($payment->status, ['pending', 'failed']))
                <hr>
                <h2 class="h5 fw-bold">Unggah Bukti Pembayaran</h2>
                <p class="text-muted">Transfer/QRIS ke rekening resmi sekolah, lalu unggah buktinya di sini (PDF/JPG/PNG, maks 2 MB).</p>
                <form method="POST" action="{{ route('applicant.payments.upload-proof', $payment) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Metode *</label>
                            <select name="method" class="form-select" required>
                                @foreach(['transfer' => 'Transfer Bank', 'qris' => 'QRIS', 'tunai' => 'Tunai', 'va' => 'Virtual Account', 'ewallet' => 'E-Wallet'] as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select></div>
                        <div class="col-md-4"><label class="form-label">No. Referensi</label><input name="reference_no" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label">Bukti Pembayaran *</label><input type="file" name="proof" class="form-control" required></div>
                    </div>
                    <button class="btn btn-primary mt-3" type="submit">Kirim Bukti Pembayaran</button>
                </form>
            @elseif($payment->status === 'waiting_verification')
                <div class="alert alert-info mb-0">Bukti pembayaran sudah terkirim dan sedang diverifikasi oleh bagian keuangan.</div>
            @elseif($payment->status === 'paid')
                <div class="alert alert-success mb-0">Pembayaran lunas{{ $payment->paid_at ? ' pada ' . $payment->paid_at->format('d M Y H:i') : '' }}. Simpan halaman ini sebagai kuitansi.</div>
            @endif
        </div></div>
    </div>
</div>
@endsection
