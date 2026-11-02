@extends('layouts.ppdb')
@section('title', 'Verifikasi Pendaftaran')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.admin-sidebar')</div>
    <div class="col-md-9">
        <h1 class="h4 fw-bold mb-3">Daftar Pendaftaran</h1>
        <form method="GET" class="card mb-3"><div class="card-body row g-2">
            <div class="col-md-5"><input name="search" class="form-control" placeholder="Cari nomor pendaftaran / nama..." value="{{ request('search') }}"></div>
            <div class="col-md-4"><select name="status" class="form-select">
                <option value="">- Semua status -</option>
                @foreach(['draft','submitted','document_review','verified','rejected_document','selection','accepted','waitlisted','rejected','registered','withdrawn'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select></div>
            <div class="col-md-3"><button class="btn btn-primary w-100" type="submit">Filter</button></div>
        </div></form>

        <div class="card"><div class="table-responsive"><table class="table table-hover mb-0 align-middle">
            <thead><tr><th>No. Pendaftaran</th><th>Nama</th><th>Periode</th><th>Jalur</th><th>Jurusan</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($applications as $app)
                <tr><td class="fw-bold">{{ $app->registration_no }}</td><td>{{ $app->applicant->full_name }}</td>
                <td>{{ $app->period->name }}</td><td>{{ $app->path->name }}</td><td>{{ $app->major?->name ?? '-' }}</td>
                <td><x-status-badge :status="$app->status" /></td>
                <td class="text-end"><a class="btn btn-sm btn-primary" href="{{ route('admin.applications.show', $app) }}">Periksa</a></td></tr>
            @empty <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada pendaftaran yang cocok.</td></tr> @endforelse
            </tbody>
        </table></div></div>
        <div class="mt-3">{{ $applications->links() }}</div>
    </div>
</div>
@endsection
