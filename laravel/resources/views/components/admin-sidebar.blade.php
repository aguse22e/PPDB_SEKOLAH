<div class="list-group sidebar">
    <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
    @if(auth()->user()->role?->code === 'admin')
        <a href="{{ route('admin.academic-years.index') }}" class="list-group-item list-group-item-action">Tahun Ajaran</a>
        <a href="{{ route('admin.periods.index') }}" class="list-group-item list-group-item-action">Periode / Gelombang</a>
        <a href="{{ route('admin.paths.index') }}" class="list-group-item list-group-item-action">Jalur Pendaftaran</a>
        <a href="{{ route('admin.majors.index') }}" class="list-group-item list-group-item-action">Jurusan</a>
        <a href="{{ route('admin.document-types.index') }}" class="list-group-item list-group-item-action">Jenis Dokumen</a>
        <a href="{{ route('admin.announcements.index') }}" class="list-group-item list-group-item-action">Pengumuman</a>
    @endif
    @if(in_array(auth()->user()->role?->code, ['admin', 'verifikator']))
        <a href="{{ route('admin.applications.index') }}" class="list-group-item list-group-item-action">Verifikasi Pendaftaran</a>
    @endif
    @if(in_array(auth()->user()->role?->code, ['admin', 'finance']))
        <a href="{{ route('admin.payments.index') }}" class="list-group-item list-group-item-action">Pembayaran</a>
    @endif
    @if(in_array(auth()->user()->role?->code, ['admin', 'finance', 'kepala_sekolah']))
        <a href="{{ route('admin.reports.index') }}" class="list-group-item list-group-item-action">Laporan</a>
    @endif
</div>
