@extends('layouts.ppdb')
@section('title', 'Status Pendaftaran')

@section('content')
<div class="row justify-content-center"><div class="col-md-8">
    <div class="card"><div class="card-body p-4">
        <h1 class="h4 fw-bold mb-3">Status Pendaftaran</h1>
        <table class="table">
            <tr><th style="width:220px">Nomor Pendaftaran</th><td class="fw-bold">{{ $application->registration_no }}</td></tr>
            <tr><th>Nama</th><td>{{ $application->applicant->full_name }}</td></tr>
            <tr><th>Periode</th><td>{{ $application->period->name }} ({{ $application->period->academicYear?->year }})</td></tr>
            <tr><th>Jalur</th><td>{{ $application->path->name }}</td></tr>
            <tr><th>Jurusan Pilihan</th><td>{{ $application->major?->name ?? '-' }}</td></tr>
            <tr><th>Status</th><td><x-status-badge :status="$application->status" /></td></tr>
            <tr><th>Tanggal Kirim</th><td>{{ $application->submitted_at?->format('d M Y H:i') ?? '-' }}</td></tr>
        </table>

        @if(in_array($application->status, ['accepted', 'registered']))
            <div class="alert alert-success mb-0">Selamat, pendaftaran dinyatakan <strong>diterima</strong>. Ikuti petunjuk daftar ulang dari sekolah.</div>
        @elseif($application->status === 'waitlisted')
            <div class="alert alert-warning mb-0">Status kamu <strong>cadangan</strong>. Pantau pengumuman berikutnya.</div>
        @elseif($application->status === 'rejected')
            <div class="alert alert-danger mb-0">Mohon maaf, pendaftaran belum diterima pada periode ini.</div>
        @endif
    </div></div>
</div></div>
@endsection
