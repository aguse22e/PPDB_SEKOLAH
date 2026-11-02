@extends('layouts.ppdb')
@section('title', 'Hasil Seleksi')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.applicant-sidebar')</div>
    <div class="col-md-9">
        <div class="card"><div class="card-body p-4">
            <h1 class="h4 fw-bold mb-1">Hasil Seleksi — {{ $application->registration_no }}</h1>
            <p class="text-muted">{{ $application->period->name }} • {{ $application->path->name }} • {{ $application->major?->name ?? '-' }}</p>
            <p>Status pendaftaran: <x-status-badge :status="$application->status" /></p>

            @if(in_array($application->status, ['accepted', 'registered']))
                <div class="alert alert-success">Selamat! Kamu dinyatakan <strong>diterima</strong>. Segera lakukan daftar ulang sesuai jadwal sekolah.</div>
            @elseif($application->status === 'waitlisted')
                <div class="alert alert-warning">Kamu masuk daftar <strong>cadangan</strong>.</div>
            @elseif($application->status === 'rejected')
                <div class="alert alert-danger">Mohon maaf, kamu belum diterima pada periode ini.</div>
            @endif

            <div class="table-responsive"><table class="table align-middle">
                <thead><tr><th>Tahap</th><th>Mata Pelajaran / Keterangan</th><th>Nilai</th><th>Peringkat</th><th>Hasil</th></tr></thead>
                <tbody>
                @forelse($application->selectionResults as $result)
                    <tr>
                        <td>{{ ucfirst(str_replace('_', ' ', $result->stage)) }}</td>
                        <td>{{ $result->subject ?? '-' }}</td>
                        <td>{{ $result->score ?? '-' }} / {{ $result->max_score ?? 100 }}</td>
                        <td>{{ $result->rank ?? '-' }}</td>
                        <td><x-status-badge :status="$result->result_status" /></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Hasil seleksi belum diumumkan untuk pendaftaran ini.</td></tr>
                @endforelse
                </tbody>
            </table></div>
        </div></div>
    </div>
</div>
@endsection
