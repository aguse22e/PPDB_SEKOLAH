@extends('layouts.ppdb')
@section('title', 'Pengumuman')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.admin-sidebar')</div>
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 fw-bold mb-0">Pengumuman</h1>
            <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">+ Tambah</a>
        </div>
        <div class="card"><div class="table-responsive"><table class="table table-hover mb-0 align-middle">
            <thead><tr><th>Judul</th><th>Periode</th><th>Terbit</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($announcements as $announcement)
                <tr><td class="fw-bold">{{ $announcement->title }}</td><td>{{ $announcement->period?->name ?? 'Umum' }}</td>
                <td>{{ $announcement->publish_at?->format('d M Y H:i') ?? '-' }}</td>
                <td>@if($announcement->is_published)<span class="badge text-bg-success">Terbit</span>@else<span class="badge text-bg-secondary">Draft</span>@endif</td>
                <td class="text-end text-nowrap">
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.announcements.edit', $announcement) }}">Ubah</a>
                    <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" class="d-inline" onsubmit="return confirm('Hapus pengumuman ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form>
                </td></tr>
            @empty <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pengumuman.</td></tr> @endforelse
            </tbody>
        </table></div></div>
        <div class="mt-3">{{ $announcements->links() }}</div>
    </div>
</div>
@endsection
