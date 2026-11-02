<div class="list-group shadow-sm">
    <a href="{{ route('guru.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">Dashboard Guru</a>
    <a href="{{ route('guru.selection.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('guru.selection.*') ? 'active' : '' }}">Peserta Seleksi & Penilaian</a>
</div>
<p class="small text-muted mt-2 mb-0">Kamu masuk sebagai <strong>Guru / Penilai Seleksi</strong>. Menu master data, verifikasi berkas, dan pembayaran dikelola panitia (admin) sehingga tidak tampil di sini.</p>
