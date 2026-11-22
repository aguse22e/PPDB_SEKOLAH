@extends('layouts.ppdb')
@section('title', 'Absensi Barcode')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.admin-sidebar')</div>
    <div class="col-md-9">

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h1 class="h4 fw-bold mb-0">Absensi Barcode</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.attendance.scan-form') }}" class="btn btn-primary">Scan Barcode</a>
                <a href="{{ route('admin.attendance.cards') }}" class="btn btn-outline-primary">Kartu Barcode</a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.attendance.index') }}" class="card mb-3"><div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Tanggal</label>
                    <input type="date" name="date" value="{{ $date }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Tipe</label>
                    <select name="type" class="form-select">
                        <option value="">Semua</option>
                        <option value="siswa" {{ $type === 'siswa' ? 'selected' : '' }}>Siswa</option>
                        <option value="guru" {{ $type === 'guru' ? 'selected' : '' }}>Guru</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        @foreach(\App\Models\Attendance::STATUSES as $s)
                            <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Cari nama</label>
                    <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Nama siswa / guru">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" type="submit">Tampilkan</button>
                </div>
            </div>
        </div></form>

        <div class="d-flex flex-wrap gap-2 mb-3">
            @foreach(\App\Models\Attendance::STATUSES as $s)
                <span class="badge text-bg-light border fs-6 fw-normal">{{ ucfirst($s) }}: <strong>{{ $summary[$s] ?? 0 }}</strong></span>
            @endforeach
            <span class="badge text-bg-light border fs-6 fw-normal">Total: <strong>{{ $attendances->total() }}</strong></span>
        </div>

        <div class="card mb-3"><div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead><tr><th>Nama</th><th>Tipe</th><th>Masuk</th><th>Pulang</th><th>Status</th><th>Cara</th><th>Dicatat Oleh</th><th></th></tr></thead>
                <tbody>
                @forelse($attendances as $a)
                    <tr>
                        <td>{{ $a->attendeeName() }}</td>
                        <td>{{ $a->attendeeLabel() }}</td>
                        <td>{{ $a->check_in_at?->format('H:i') ?? '-' }}</td>
                        <td>{{ $a->check_out_at?->format('H:i') ?? '-' }}</td>
                        <td><x-status-badge :status="$a->status" /></td>
                        <td>{{ $a->method === 'barcode' ? 'Barcode' : 'Manual' }}</td>
                        <td>{{ $a->scanner?->name ?? '-' }}</td>
                        <td class="text-end text-nowrap">
                            @if(in_array(auth()->user()->role?->code, ['admin', 'verifikator']))
                                <form method="POST" action="{{ route('admin.attendance.update', $a) }}" class="d-inline">
                                    @csrf @method('PUT')
                                    <select name="status" class="form-select form-select-sm d-inline w-auto">
                                        @foreach(\App\Models\Attendance::STATUSES as $s)
                                            <option value="{{ $s }}" {{ $a->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-outline-primary" type="submit">Ubah</button>
                                </form>
                                <form method="POST" action="{{ route('admin.attendance.destroy', $a) }}" class="d-inline" onsubmit="return confirm('Hapus data absensi ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada absensi pada tanggal ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div></div>
        <div class="mb-3">{{ $attendances->links() }}</div>

        @if(in_array(auth()->user()->role?->code, ['admin', 'verifikator']))
        <div class="card"><div class="card-body">
            <h2 class="h6 fw-bold">Catat Manual (Izin / Sakit / Alpa / Hadir tanpa scan)</h2>
            <form method="POST" action="{{ route('admin.attendance.manual') }}">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small mb-1">Siswa / Guru</label>
                        <select name="attendee" class="form-select" required>
                            <option value="">Pilih orang</option>
                            <optgroup label="Siswa (sudah daftar ulang)">
                                @foreach($students as $s)
                                    <option value="siswa:{{ $s->id }}">{{ $s->full_name }} ({{ $s->barcode_code ?? '-' }})</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Guru">
                                @foreach($teachers as $t)
                                    <option value="guru:{{ $t->id }}">{{ $t->name }} ({{ $t->barcode_code ?? '-' }})</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Tanggal</label>
                        <input type="date" name="date" value="{{ $date }}" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Status</label>
                        <select name="status" class="form-select" required>
                            @foreach(\App\Models\Attendance::STATUSES as $s)
                                <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Catatan</label>
                        <input type="text" name="notes" class="form-control" placeholder="Opsional">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" type="submit">Simpan</button>
                    </div>
                </div>
            </form>
            <p class="small text-muted mt-2 mb-0">Jam masuk sekolah: {{ \App\Models\Attendance::startTime() }} (Setting <code>attendance.start_time</code>). Scan masuk setelah jam itu otomatis berstatus Terlambat.</p>
        </div></div>
        @endif

    </div>
</div>
@endsection
