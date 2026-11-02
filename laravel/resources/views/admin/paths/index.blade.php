@extends('layouts.ppdb')
@section('title', 'Jalur Pendaftaran')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.admin-sidebar')</div>
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 fw-bold mb-0">Jalur Pendaftaran</h1>
            <a href="{{ route('admin.paths.create') }}" class="btn btn-primary">+ Tambah</a>
        </div>
        <div class="card"><div class="table-responsive"><table class="table table-hover mb-0 align-middle">
            <thead><tr><th>Kode</th><th>Nama</th><th>Kuota</th><th>Dokumen Wajib</th><th>Aktif</th><th></th></tr></thead>
            <tbody>
            @forelse($paths as $path)
                <tr><td>{{ $path->code }}</td><td class="fw-bold">{{ $path->name }}</td><td>{{ $path->quota }}</td>
                <td>{{ $path->documentTypes->filter(fn($d) => $d->pivot->is_required)->pluck('name')->join(', ') ?: '-' }}</td>
                <td>{{ $path->is_active ? 'Ya' : 'Tidak' }}</td>
                <td class="text-end text-nowrap">
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.paths.edit', $path) }}">Ubah</a>
                    <form method="POST" action="{{ route('admin.paths.destroy', $path) }}" class="d-inline" onsubmit="return confirm('Hapus jalur ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form>
                </td></tr>
            @empty <tr><td colspan="6" class="text-center text-muted py-4">Belum ada jalur.</td></tr> @endforelse
            </tbody>
        </table></div></div>
        <div class="mt-3">{{ $paths->links() }}</div>
    </div>
</div>
@endsection
