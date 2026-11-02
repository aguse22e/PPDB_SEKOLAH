@extends('layouts.ppdb')
@section('title', 'Tahun Ajaran')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.admin-sidebar')</div>
    <div class="col-md-9">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 fw-bold mb-0">Tahun Ajaran</h1>
            <a href="{{ route('admin.academic-years.create') }}" class="btn btn-primary">+ Tambah</a>
        </div>
        <div class="card"><div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead><tr><th>Tahun</th><th>Aktif</th><th></th></tr></thead>
                <tbody>
                @forelse($years as $academicYear)
                    <tr>
                        <td>{{ $academicYear->year }}</td>
                        <td>{{ $academicYear->is_active ? 'Ya' : 'Tidak' }}</td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.academic-years.edit', $academicYear) }}">Ubah</a>
                            <form method="POST" action="{{ route('admin.academic-years.destroy', $academicYear) }}" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted py-4">Belum ada data.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div></div>
        <div class="mt-3">{{ $years->links() }}</div>
    </div>
</div>
@endsection
