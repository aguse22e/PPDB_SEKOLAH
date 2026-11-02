@extends('layouts.ppdb')
@section('title', 'Dashboard Keuangan')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.admin-sidebar')</div>
    <div class="col-md-9">
        <h1 class="h4 fw-bold mb-3">Dashboard Keuangan</h1>
        <p class="text-muted">Fokus halaman ini: tagihan, bukti bayar yang menunggu verifikasi, dan uang yang sudah masuk. Data berkas & seleksi tidak ditampilkan di sini.</p>

        <div class="row g-3 mb-3">
            <div class="col-md-4 col-xl"><div class="card stat-card h-100"><div class="card-body">
                <div class="text-muted small">Pembayaran Masuk (Lunas)</div><h3>Rp{{ number_format($stats['pembayaran_masuk'], 0, ',', '.') }}</h3>
            </div></div></div>
            @foreach([
                ['Bukti Menunggu Verifikasi', $stats['menunggu_verifikasi']],
                ['Tagihan Belum Dibayar', $stats['belum_bayar']],
                ['Total Tagihan', $stats['total_tagihan']],
            ] as [$label, $value])
                <div class="col-md-4 col-xl"><div class="card stat-card h-100"><div class="card-body">
                    <div class="text-muted small">{{ $label }}</div><h3>{{ number_format($value, 0, ',', '.') }}</h3>
                </div></div></div>
            @endforeach
            <div class="col-md-4 col-xl"><div class="card stat-card h-100"><div class="card-body">
                <div class="text-muted small">Total Nominal Tagihan</div><h3>Rp{{ number_format($stats['total_nominal_tagihan'], 0, ',', '.') }}</h3>
            </div></div></div>
        </div>

        <div class="row g-3">
            <div class="col-md-5"><div class="card h-100"><div class="card-body">
                <h2 class="h6 fw-bold">Pemasukan per Jenis Tagihan</h2>
                @forelse($perType as $row)
                    <div class="d-flex justify-content-between border-bottom py-1">
                        <span>{{ ucfirst(str_replace('_', ' ', $row->payment_type)) }} ({{ $row->total }})</span>
                        <strong>Rp{{ number_format($row->nominal, 0, ',', '.') }}</strong>
                    </div>
                @empty <p class="text-muted mb-0">Belum ada pembayaran lunas.</p> @endforelse
            </div></div></div>
            <div class="col-md-7"><div class="card h-100"><div class="card-body">
                <h2 class="h6 fw-bold">Bukti Bayar Menunggu Verifikasi</h2>
                @forelse($waiting as $payment)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div>
                            <div class="fw-bold">{{ $payment->invoice_no }} — {{ $payment->application->applicant->full_name }}</div>
                            <div class="small text-muted">{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }} • Rp{{ number_format($payment->amount, 0, ',', '.') }}</div>
                        </div>
                        <a class="btn btn-sm btn-primary" href="{{ route('admin.payments.show', $payment) }}">Periksa</a>
                    </div>
                @empty <p class="text-muted mb-0">Tidak ada bukti bayar yang menunggu.</p> @endforelse
            </div></div></div>
        </div>
    </div>
</div>
@endsection
