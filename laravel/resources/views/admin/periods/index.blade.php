@extends('layouts.ppdb')
@section('title', 'Periode PPDB')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.admin-sidebar')</div>
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 fw-bold mb-0">Periode / Gelombang</h1>
            <a href="{{ route('admin.periods.create') }}" class="btn btn-primary">+ Tambah</a>
        </div>
        <div class="card"><div class="table-responsive"><table class="table table-hover mb-0 align-middle">
            <thead><tr><th>Periode</th><th>Tahun Ajaran</th><th>Jadwal</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($periods as $period)
                <tr><td class="fw-bold">{{ $period->name }}</td><td>{{ $period->academicYear?->year }}</td>
                <td>{{ $period->start_date->format('d M Y') }} — {{ $period->end_date->format('d M Y') }}</td>
                <td><x-status-badge :status="$period->status" /></td>
                <td class="text-end text-nowrap">
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.periods.edit', $period) }}">Ubah</a>
                    <form method="POST" action="{{ route('admin.periods.destroy', $period) }}" class="d-inline" onsubmit="return confirm('Hapus periode ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form>
                </td></tr>
            @empty <tr><td colspan="5" class="text-center text-muted py-4">Belum ada periode.</td></tr> @endforelse
            </tbody>
        </table></div></div>
        <div class="mt-3">{{ $periods->links() }}</div>
    </div>
</div>
@endsection
