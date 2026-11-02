@extends('layouts.ppdb')
@section('title', 'Jurusan')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.admin-sidebar')</div>
    <div class="col-md-9">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 fw-bold mb-0">Jurusan</h1>
            <a href="{{ route('admin.majors.create') }}" class="btn btn-primary">+ Tambah</a>
        </div>
        <div class="card"><div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead><tr><th>Kode</th><th>Nama Jurusan</th><th>Kuota Default</th><th>Aktif</th><th></th></tr></thead>
                <tbody>
                @forelse($majors as $major)
                    <tr>
                        <td>{{ $major->code }}</td>
                        <td>{{ $major->name }}</td>
                        <td>{{ $major->quota }}</td>
                        <td>{{ $major->is_active ? 'Ya' : 'Tidak' }}</td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.majors.edit', $major) }}">Ubah</a>
                            <form method="POST" action="{{ route('admin.majors.destroy', $major) }}" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div></div>
        <div class="mt-3">{{ $majors->links() }}</div>
    </div>
</div>
@endsection
