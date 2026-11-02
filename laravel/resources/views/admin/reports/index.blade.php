@extends('layouts.ppdb')
@section('title', 'Laporan PPDB')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.admin-sidebar')</div>
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h1 class="h4 fw-bold mb-0">Laporan PPDB</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.reports.export-applications', request()->query()) }}" class="btn btn-outline-primary">Export Pendaftar (CSV)</a>
                <a href="{{ route('admin.reports.export-payments', request()->query()) }}" class="btn btn-outline-success">Export Pembayaran (CSV)</a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6"><div class="card h-100"><div class="card-body">
                <h2 class="h6 fw-bold">Pendaftar per Jalur</h2>
                @forelse($summary['per_jalur'] as $row)
                    <div class="d-flex justify-content-between border-bottom py-1"><span>{{ $row->path?->name ?? $row->path_id }}</span><strong>{{ $row->total }}</strong></div>
                @empty <p class="text-muted mb-0">Belum ada data.</p> @endforelse
            </div></div></div>
            <div class="col-md-6"><div class="card h-100"><div class="card-body">
                <h2 class="h6 fw-bold">Pendaftar per Jurusan</h2>
                @forelse($summary['per_jurusan'] as $row)
                    <div class="d-flex justify-content-between border-bottom py-1"><span>{{ $row->major?->name ?? '-' }}</span><strong>{{ $row->total }}</strong></div>
                @empty <p class="text-muted mb-0">Belum ada data.</p> @endforelse
            </div></div></div>
            <div class="col-md-6"><div class="card h-100"><div class="card-body">
                <h2 class="h6 fw-bold">Pendaftar per Status</h2>
                @forelse($summary['per_status'] as $row)
                    <div class="d-flex justify-content-between border-bottom py-1"><span><x-status-badge :status="$row->status" /></span><strong>{{ $row->total }}</strong></div>
                @empty <p class="text-muted mb-0">Belum ada data.</p> @endforelse
            </div></div></div>
            <div class="col-md-6"><div class="card h-100"><div class="card-body">
                <h2 class="h6 fw-bold">Keuangan & Kelengkapan</h2>
                <div class="d-flex justify-content-between border-bottom py-1"><span>Dokumen belum lengkap (draft / ditolak)</span><strong>{{ $summary['dokumen_belum_lengkap'] }}</strong></div>
                <div class="d-flex justify-content-between py-1"><span>Total pembayaran lunas</span><strong>Rp{{ number_format($summary['total_pembayaran'], 0, ',', '.') }}</strong></div>
            </div></div></div>
        </div>
        <p class="small text-muted mt-3">Filter per periode: tambahkan <code>?period_id=ID_PERIODE</code> pada URL halaman ini, export CSV akan mengikuti filter yang sama.</p>
    </div>
</div>
@endsection
