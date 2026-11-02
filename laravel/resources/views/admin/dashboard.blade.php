@extends('layouts.ppdb')
@section('title', 'Dashboard Admin')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.admin-sidebar')</div>
    <div class="col-md-9">
        <h1 class="h4 fw-bold mb-3">Dashboard Panitia PPDB</h1>
        <div class="row g-3 mb-3">
            @foreach([
                ['Total Pendaftar', $stats['total_pendaftar']],
                ['Menunggu Verifikasi', $stats['menunggu_verifikasi']],
                ['Diterima', $stats['diterima']],
                ['Sudah Daftar Ulang', $stats['sudah_daftar_ulang']],
                ['Pembayaran Menunggu', $stats['pembayaran_menunggu']],
            ] as [$label, $value])
                <div class="col-md-4 col-xl"><div class="card stat-card h-100"><div class="card-body">
                    <div class="text-muted small">{{ $label }}</div><h3>{{ number_format($value, 0, ',', '.') }}</h3>
                </div></div></div>
            @endforeach
            <div class="col-md-4 col-xl"><div class="card stat-card h-100"><div class="card-body">
                <div class="text-muted small">Pembayaran Masuk</div><h3>Rp{{ number_format($stats['pembayaran_masuk'], 0, ',', '.') }}</h3>
            </div></div></div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4"><div class="card h-100"><div class="card-body">
                <h2 class="h6 fw-bold">Per Status</h2>
                @forelse($perStatus as $status => $total)
                    <div class="d-flex justify-content-between border-bottom py-1"><span><x-status-badge :status="$status" /></span><strong>{{ $total }}</strong></div>
                @empty <p class="text-muted mb-0">Belum ada data.</p> @endforelse
            </div></div></div>
            <div class="col-md-4"><div class="card h-100"><div class="card-body">
                <h2 class="h6 fw-bold">Per Jalur</h2>
                @foreach($perPath as $row)
                    <div class="d-flex justify-content-between border-bottom py-1"><span>{{ $row->path?->name ?? $row->path_id }}</span><strong>{{ $row->total }}</strong></div>
                @endforeach
            </div></div></div>
            <div class="col-md-4"><div class="card h-100"><div class="card-body">
                <h2 class="h6 fw-bold">Per Jurusan</h2>
                @foreach($perMajor as $row)
                    <div class="d-flex justify-content-between border-bottom py-1"><span>{{ $row->major?->name ?? '-' }}</span><strong>{{ $row->total }}</strong></div>
                @endforeach
            </div></div></div>
        </div>

        <div class="card"><div class="card-body">
            <h2 class="h6 fw-bold">Pendaftar Terbaru</h2>
            <div class="table-responsive"><table class="table table-sm align-middle mb-0">
                <thead><tr><th>No. Pendaftaran</th><th>Nama</th><th>Jalur</th><th>Jurusan</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @forelse($latest as $app)
                    <tr><td>{{ $app->registration_no }}</td><td>{{ $app->applicant->full_name }}</td><td>{{ $app->path->name }}</td><td>{{ $app->major?->name ?? '-' }}</td>
                    <td><x-status-badge :status="$app->status" /></td>
                    <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.applications.show', $app) }}">Lihat</a></td></tr>
                @empty <tr><td colspan="6" class="text-center text-muted">Belum ada pendaftar.</td></tr> @endforelse
                </tbody>
            </table></div>
        </div></div>
    </div>
</div>
@endsection
