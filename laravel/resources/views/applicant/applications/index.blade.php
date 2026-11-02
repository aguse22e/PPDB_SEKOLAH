@extends('layouts.ppdb')
@section('title', 'Pendaftaran Saya')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.applicant-sidebar')</div>
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 fw-bold mb-0">Pendaftaran Saya</h1>
            <a href="{{ route('applicant.applications.create') }}" class="btn btn-primary">+ Daftar Baru</a>
        </div>
        <div class="card"><div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead><tr><th>No. Pendaftaran</th><th>Periode</th><th>Jalur</th><th>Jurusan</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @forelse($applications as $app)
                    <tr>
                        <td class="fw-bold">{{ $app->registration_no }}</td>
                        <td>{{ $app->period->name }}</td>
                        <td>{{ $app->path->name }}</td>
                        <td>{{ $app->major?->name ?? '-' }}</td>
                        <td><x-status-badge :status="$app->status" /></td>
                        <td class="text-end"><a class="btn btn-sm btn-primary" href="{{ route('applicant.applications.show', $app) }}">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pendaftaran.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div></div>
        <div class="mt-3">{{ $applications->links() }}</div>
    </div>
</div>
@endsection
