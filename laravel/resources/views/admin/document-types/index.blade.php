@extends('layouts.ppdb')
@section('title', 'Jenis Dokumen')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.admin-sidebar')</div>
    <div class="col-md-9">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 fw-bold mb-0">Jenis Dokumen</h1>
            <a href="{{ route('admin.document-types.create') }}" class="btn btn-primary">+ Tambah</a>
        </div>
        <div class="card"><div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead><tr><th>Kode</th><th>Nama Dokumen</th><th>Format</th><th>Maks (MB)</th><th>Aktif</th><th></th></tr></thead>
                <tbody>
                @forelse($types as $type)
                    <tr>
                        <td>{{ $type->code }}</td>
                        <td>{{ $type->name }}</td>
                        <td>{{ $type->allowed_extensions }}</td>
                        <td>{{ $type->max_size_mb }}</td>
                        <td>{{ $type->is_active ? 'Ya' : 'Tidak' }}</td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.document-types.edit', $type) }}">Ubah</a>
                            <form method="POST" action="{{ route('admin.document-types.destroy', $type) }}" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div></div>
        <div class="mt-3">{{ $types->links() }}</div>
    </div>
</div>
@endsection
