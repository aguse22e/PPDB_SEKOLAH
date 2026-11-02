@extends('layouts.ppdb')
@section('title', 'Peserta Seleksi')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.guru-sidebar')</div>
    <div class="col-md-9">
        <h1 class="h4 fw-bold mb-3">Peserta Seleksi & Penilaian</h1>

        <form method="get" class="card mb-3"><div class="card-body d-flex gap-2 flex-wrap">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" style="max-width:280px" placeholder="Cari nama / no. pendaftaran">
            <select name="penilaian" class="form-select" style="max-width:200px">
                <option value="">Semua penilaian</option>
                <option value="belum" @selected(request('penilaian') === 'belum')>Belum dinilai</option>
                <option value="sudah" @selected(request('penilaian') === 'sudah')>Sudah dinilai</option>
            </select>
            <button class="btn btn-primary">Filter</button>
        </div></form>

        <div class="card"><div class="card-body">
            <div class="table-responsive"><table class="table align-middle mb-0">
                <thead><tr><th>No. Pendaftaran</th><th>Nama</th><th>Jalur</th><th>Jurusan</th><th>Status</th><th>Jumlah Nilai</th><th></th></tr></thead>
                <tbody>
                @forelse($applications as $app)
                    <tr>
                        <td>{{ $app->registration_no }}</td>
                        <td>{{ $app->applicant->full_name }}</td>
                        <td>{{ $app->path->name }}</td>
                        <td>{{ $app->major?->name ?? '-' }}</td>
                        <td><x-status-badge :status="$app->status" /></td>
                        <td>{{ $app->selectionResults->count() }}</td>
                        <td><a class="btn btn-sm btn-primary" href="{{ route('guru.selection.show', $app) }}">Nilai</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-muted">Belum ada peserta pada tahap seleksi.</td></tr>
                @endforelse
                </tbody>
            </table></div>
            <div class="mt-3">{{ $applications->links() }}</div>
        </div></div>
    </div>
</div>
@endsection
