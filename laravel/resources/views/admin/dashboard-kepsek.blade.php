@extends('layouts.ppdb')
@section('title', 'Dashboard Kepala Sekolah')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.admin-sidebar')</div>
    <div class="col-md-9">
        <h1 class="h4 fw-bold mb-3">Dashboard Kepala Sekolah</h1>
        <p class="text-muted">Ringkasan eksekutif PPDB{{ $openPeriod ? ' — ' . $openPeriod->name . ' (' . ($openPeriod->academicYear?->year ?? '') . ')' : '' }}: jumlah pendaftar, hasil akhir, dan keterserapan kuota per jurusan.</p>

        <div class="row g-3 mb-3">
            @foreach([
                ['Total Pendaftar', $stats['total_pendaftar']],
                ['Diterima', $stats['diterima']],
                ['Sudah Daftar Ulang', $stats['sudah_daftar_ulang']],
            ] as [$label, $value])
                <div class="col-md-4"><div class="card stat-card h-100"><div class="card-body">
                    <div class="text-muted small">{{ $label }}</div><h3>{{ number_format($value, 0, ',', '.') }}</h3>
                </div></div></div>
            @endforeach
            <div class="col-md-4"><div class="card stat-card h-100"><div class="card-body">
                <div class="text-muted small">Pembayaran Masuk</div><h3>Rp{{ number_format($stats['pembayaran_masuk'], 0, ',', '.') }}</h3>
            </div></div></div>
        </div>

        <div class="card mb-3"><div class="card-body">
            <h2 class="h6 fw-bold">Keterserapan Kuota per Jurusan{{ $openPeriod ? ' — ' . $openPeriod->name : '' }}</h2>
            @forelse($quotas as $quota)
                @php $persen = $quota->quota > 0 ? min(100, round($quota->terisi / $quota->quota * 100)) : 0; @endphp
                <div class="mb-2">
                    <div class="d-flex justify-content-between"><span>{{ $quota->major?->name ?? '-' }}</span><strong>{{ $quota->terisi }} / {{ $quota->quota }} ({{ $persen }}%)</strong></div>
                    <div class="progress" role="progressbar" aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar" style="width: {{ $persen }}%"></div></div>
                </div>
            @empty <p class="text-muted mb-0">Belum ada periode yang dibuka / kuota belum diatur.</p> @endforelse
        </div></div>

        <div class="row g-3">
            <div class="col-md-6"><div class="card h-100"><div class="card-body">
                <h2 class="h6 fw-bold">Pendaftar per Status</h2>
                @forelse($perStatus as $status => $total)
                    <div class="d-flex justify-content-between border-bottom py-1"><span><x-status-badge :status="$status" /></span><strong>{{ $total }}</strong></div>
                @empty <p class="text-muted mb-0">Belum ada data.</p> @endforelse
            </div></div></div>
            <div class="col-md-6"><div class="card h-100"><div class="card-body">
                <h2 class="h6 fw-bold">Pendaftar per Jalur</h2>
                @forelse($perPath as $row)
                    <div class="d-flex justify-content-between border-bottom py-1"><span>{{ $row->path?->name ?? $row->path_id }}</span><strong>{{ $row->total }}</strong></div>
                @empty <p class="text-muted mb-0">Belum ada data.</p> @endforelse
            </div></div></div>
        </div>
    </div>
</div>
@endsection
