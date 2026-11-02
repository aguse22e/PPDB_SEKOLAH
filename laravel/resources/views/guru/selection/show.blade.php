@extends('layouts.ppdb')
@section('title', 'Penilaian — ' . $application->registration_no)

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.guru-sidebar')</div>
    <div class="col-md-9">
        <div class="card mb-3"><div class="card-body">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <h1 class="h4 fw-bold mb-1">{{ $application->applicant->full_name }}</h1>
                    <div class="text-muted">{{ $application->registration_no }} • {{ $application->period->name }} • {{ $application->path->name }} • {{ $application->major?->name ?? '-' }}</div>
                    <div class="small text-muted mt-1">Asal sekolah: {{ $application->applicant->previous_school ?? '-' }} • Wali: {{ $application->applicant->guardians->map(fn ($g) => $g->name . ' (' . $g->relationship . ')')->join(', ') ?: '-' }}</div>
                </div>
                <x-status-badge :status="$application->status" />
            </div>
        </div></div>

        <div class="card mb-3"><div class="card-body">
            <h2 class="h5 fw-bold">Nilai yang Sudah Masuk</h2>
            @forelse($application->selectionResults as $result)
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <div>
                        <div class="fw-bold">{{ $result->stage }}{{ $result->subject ? ' • ' . $result->subject : '' }}</div>
                        <div class="small text-muted">Penilai: {{ $result->assessor?->name ?? '-' }} • {{ $result->assessed_at?->format('d M Y H:i') }}{{ $result->notes ? ' • ' . $result->notes : '' }}</div>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <span class="fw-bold">{{ $result->score ?? '-' }} / {{ $result->max_score ?? 100 }}</span>
                        <x-status-badge :status="$result->result_status" />
                        <form method="post" action="{{ route('guru.selection.destroy', $result) }}" onsubmit="return confirm('Hapus nilai ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-muted mb-0">Belum ada nilai untuk peserta ini.</p>
            @endforelse
        </div></div>

        <div class="card"><div class="card-body">
            <h2 class="h5 fw-bold">Input Nilai Baru</h2>
            <form method="post" action="{{ route('guru.selection.store', $application) }}">
                @csrf
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">Tahap</label>
                        <select name="stage" class="form-select" required>
                            <option value="tes_tulis">Tes Tulis</option>
                            <option value="wawancara">Wawancara</option>
                            <option value="seleksi_berkas">Seleksi Berkas</option>
                            <option value="prestasi">Prestasi</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mata Pelajaran / Keterangan</label>
                        <input type="text" name="subject" class="form-control" placeholder="cth. Matematika">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Nilai</label>
                        <input type="number" step="0.01" min="0" name="score" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Nilai Maks</label>
                        <input type="number" step="0.01" min="1" name="max_score" class="form-control" value="100">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Hasil Tahap</label>
                        <select name="result_status" class="form-select" required>
                            <option value="pending">Menunggu</option>
                            <option value="passed">Lulus</option>
                            <option value="failed">Tidak Lulus</option>
                            <option value="waitlisted">Cadangan</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Catatan</label>
                        <input type="text" name="notes" class="form-control" placeholder="Catatan penilai (opsional)">
                    </div>
                </div>
                <button class="btn btn-success mt-3">Simpan Nilai</button>
                <p class="small text-muted mt-2 mb-0">Hasil akhir (diterima / cadangan / ditolak) ditetapkan oleh panitia, bukan dari halaman guru ini.</p>
            </form>
        </div></div>
    </div>
</div>
@endsection
