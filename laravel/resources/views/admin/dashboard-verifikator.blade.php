@extends('layouts.ppdb')
@section('title', 'Dashboard Verifikator')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.admin-sidebar')</div>
    <div class="col-md-9">
        <h1 class="h4 fw-bold mb-3">Dashboard Verifikator Berkas</h1>
        <p class="text-muted">Fokus halaman ini: antrean berkas yang harus kamu periksa. Statistik pembayaran dan master data tidak ditampilkan karena bukan tugas verifikator.</p>

        <div class="row g-3 mb-3">
            @foreach([
                ['Antrean Pendaftaran', $stats['antrean_pendaftaran']],
                ['Dokumen Menunggu', $stats['dokumen_menunggu']],
                ['Dokumen Terverifikasi', $stats['dokumen_terverifikasi']],
                ['Dokumen Ditolak', $stats['dokumen_ditolak']],
                ['Pendaftaran Lolos Berkas', $stats['pendaftaran_terverifikasi']],
            ] as [$label, $value])
                <div class="col-md-4 col-xl"><div class="card stat-card h-100"><div class="card-body">
                    <div class="text-muted small">{{ $label }}</div><h3>{{ number_format($value, 0, ',', '.') }}</h3>
                </div></div></div>
            @endforeach
        </div>

        <div class="card"><div class="card-body">
            <h2 class="h5 fw-bold">Antrean Verifikasi (Terlama Menunggu di Atas)</h2>
            @forelse($queue as $app)
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <div>
                        <div class="fw-bold">{{ $app->applicant->full_name }}</div>
                        <div class="small text-muted">{{ $app->registration_no }} • {{ $app->period->name }} • {{ $app->path->name }} • {{ $app->major?->name ?? '-' }} • {{ $app->dokumen_pending }} dari {{ $app->documents_count }} dokumen menunggu • sejak {{ $app->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <x-status-badge :status="$app->status" />
                        <a class="btn btn-sm btn-primary" href="{{ route('admin.applications.show', $app) }}">Periksa</a>
                    </div>
                </div>
            @empty
                <p class="text-muted mb-0">Antrean kosong — semua pendaftaran sudah diperiksa.</p>
            @endforelse
        </div></div>
    </div>
</div>
@endsection
