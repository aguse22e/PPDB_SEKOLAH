@extends('layouts.ppdb')
@section('title', 'Dashboard Guru')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.guru-sidebar')</div>
    <div class="col-md-9">
        <h1 class="h4 fw-bold mb-3">Dashboard Guru — Penilaian Seleksi</h1>

        <div class="row g-3 mb-3">
            @foreach([
                ['Peserta Tahap Seleksi', $stats['peserta_seleksi']],
                ['Sedang Seleksi', $stats['sedang_seleksi']],
                ['Belum Dinilai', $stats['belum_dinilai']],
                ['Sudah Dinilai', $stats['sudah_dinilai']],
                ['Rata-rata Nilai', $stats['rata_rata_nilai']],
            ] as [$label, $value])
                <div class="col-md-4 col-xl"><div class="card stat-card h-100"><div class="card-body">
                    <div class="text-muted small">{{ $label }}</div><h3>{{ is_numeric($value) ? number_format($value, 0, ',', '.') : $value }}</h3>
                </div></div></div>
            @endforeach
        </div>

        <div class="card mb-3"><div class="card-body">
            <h2 class="h5 fw-bold">Antrean Belum Dinilai</h2>
            @forelse($needScoring as $app)
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <div>
                        <div class="fw-bold">{{ $app->applicant->full_name }}</div>
                        <div class="small text-muted">{{ $app->registration_no }} • {{ $app->period->name }} • {{ $app->path->name }} • {{ $app->major?->name ?? '-' }}</div>
                    </div>
                    <a class="btn btn-sm btn-primary" href="{{ route('guru.selection.show', $app) }}">Input Nilai</a>
                </div>
            @empty
                <p class="text-muted mb-0">Semua peserta tahap seleksi sudah memiliki nilai. Kerja bagus.</p>
            @endforelse
        </div></div>

        <div class="card"><div class="card-body">
            <h2 class="h5 fw-bold">Nilai Terakhir Diinput</h2>
            @forelse($recentlyScored as $result)
                <div class="d-flex justify-content-between border-bottom py-2">
                    <div>
                        <div class="fw-bold">{{ $result->application->applicant->full_name }} — {{ $result->stage }}{{ $result->subject ? ' • ' . $result->subject : '' }}</div>
                        <div class="small text-muted">oleh {{ $result->assessor?->name ?? '-' }} • {{ $result->assessed_at?->format('d M Y H:i') }}</div>
                    </div>
                    <span class="fw-bold">{{ $result->score ?? '-' }} / {{ $result->max_score ?? 100 }}</span>
                </div>
            @empty
                <p class="text-muted mb-0">Belum ada nilai yang diinput.</p>
            @endforelse
        </div></div>
    </div>
</div>
@endsection
