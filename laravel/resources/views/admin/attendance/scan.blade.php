@extends('layouts.ppdb')
@section('title', 'Scan Absensi')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.admin-sidebar')</div>
    <div class="col-md-9">

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h1 class="h4 fw-bold mb-0">Scan Absensi</h1>
            <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline-primary">Rekap Hari Ini</a>
        </div>

        <div class="card mb-3"><div class="card-body">
            <form method="POST" action="{{ route('admin.attendance.scan') }}">
                @csrf
                <label class="form-label fw-bold" for="code">Kode Barcode / Kartu</label>
                <input type="text" name="code" id="code" class="form-control form-control-lg" placeholder="Scan kartu atau ketik kode, lalu Enter" autocomplete="off" autofocus required>
                <p class="small text-muted mt-2 mb-0">Scan pertama = absen masuk, scan kedua di hari yang sama = absen pulang. Pemindai barcode USB biasa langsung mengetik kodenya ke kolom ini.</p>
                <button class="btn btn-primary mt-2" type="submit">Catat Absensi</button>
            </form>
        </div></div>

        <div class="card"><div class="card-body">
            <h2 class="h6 fw-bold">10 Scan Terakhir Hari Ini</h2>
            <div class="table-responsive"><table class="table table-hover mb-0 align-middle">
                <thead><tr><th>Nama</th><th>Tipe</th><th>Masuk</th><th>Pulang</th><th>Status</th></tr></thead>
                <tbody>
                @forelse($today as $a)
                    <tr>
                        <td>{{ $a->attendeeName() }}</td>
                        <td>{{ $a->attendeeLabel() }}</td>
                        <td>{{ $a->check_in_at?->format('H:i') ?? '-' }}</td>
                        <td>{{ $a->check_out_at?->format('H:i') ?? '-' }}</td>
                        <td><x-status-badge :status="$a->status" /></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada yang absen hari ini.</td></tr>
                @endforelse
                </tbody>
            </table></div>
        </div></div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // Setelah halaman dimuat ulang (habis scan), kursor kembali ke kolom kode.
    document.getElementById('code').focus();
</script>
@endpush
