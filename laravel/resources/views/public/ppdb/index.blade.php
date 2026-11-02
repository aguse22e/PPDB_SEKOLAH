@extends('layouts.ppdb')
@section('title', 'Informasi PPDB')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .ppdb-elegant { font-family:'Inter','Segoe UI',Arial,sans-serif; }
    .ppdb-elegant h1, .ppdb-elegant h2 { font-family:'Playfair Display',Georgia,serif; }
    .ppdb-elegant .hero-elegant { position:relative; overflow:hidden; color:#fff; background:linear-gradient(120deg,#0b1e3f 0%,#123a7a 55%,#0e5aa8 100%); border:none; box-shadow:0 24px 48px rgba(9,28,66,.28); }
    .ppdb-elegant .hero-elegant .lead { color:rgba(255,255,255,.86) !important; }
    .ppdb-elegant .hero-elegant .btn-primary { background:linear-gradient(135deg,#e6c65c,#c39a2e); border:none; color:#221a04; font-weight:700; box-shadow:0 12px 26px rgba(212,175,55,.35); }
    .ppdb-elegant .hero-elegant .btn-outline-primary { background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.65); color:#fff; }
    .ppdb-elegant .hero-elegant .hero-bg { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; z-index:0; }
    .ppdb-elegant .hero-elegant::before { content:''; position:absolute; inset:0; z-index:1; background:linear-gradient(120deg, rgba(8,24,52,.94) 0%, rgba(14,49,99,.88) 55%, rgba(11,70,130,.82) 100%); }
    .ppdb-elegant .hero-elegant > *:not(.hero-bg) { position:relative; z-index:2; }
    .ppdb-elegant .hero-eyebrow { display:inline-block; color:#f5d67b; font-size:.78rem; font-weight:700; letter-spacing:.22em; text-transform:uppercase; margin-bottom:.8rem; }
    .ppdb-elegant .card { border-radius:1rem; }
</style>
<div class="ppdb-elegant">
<div class="p-4 p-md-5 mb-4 rounded-3 card hero-elegant">
    <img class="hero-bg" src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1600&q=70" alt="" onerror="this.onerror=null;this.src='https://picsum.photos/seed/ppdb-sekolah/1600/900'">
    <span class="hero-eyebrow">Selamat Datang &bull; Tahun Ajaran {{ $openPeriods->first()?->academicYear?->year ?? '2027/2028' }}</span>
    <h1 class="fw-bold">Penerimaan Peserta Didik Baru</h1>
    <p class="lead text-muted mb-3">Daftar secara online, unggah dokumen, pantau verifikasi, pembayaran, dan hasil seleksi dalam satu tempat.</p>
    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Daftar Sekarang</a>
    <a href="{{ route('ppdb.check-form') }}" class="btn btn-outline-primary btn-lg">Cek Status Pendaftaran</a>
</div>

<h2 class="h4 fw-bold mb-3">Visi & Misi Sekolah</h2>
<div class="card mb-4"><div class="card-body p-4">
    <div class="row g-4 align-items-stretch">
        <div class="col-md-5">
            <img src="https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?auto=format&fit=crop&w=900&q=70" alt="Siswa-siswi berangkat sekolah" loading="lazy" class="w-100 h-100 rounded-3 shadow-sm" style="object-fit:cover; min-height:240px"
                 onerror="this.onerror=null;this.src='https://picsum.photos/seed/ppdb-visi/900/600'">
        </div>
        <div class="col-md-7">
            <h3 class="h6 fw-bold text-primary text-uppercase" style="letter-spacing:.12em">Visi</h3>
            <p>Terwujudnya lulusan yang berkarakter, berprestasi, dan berwawasan global, berlandaskan iman, ilmu, dan akhlak mulia.</p>
            <h3 class="h6 fw-bold text-primary text-uppercase mt-3" style="letter-spacing:.12em">Misi</h3>
            <ul class="mb-0 lh-lg">
                <li>Menyelenggarakan pembelajaran yang aktif, kreatif, dan berbasis teknologi.</li>
                <li>Membina karakter religius, disiplin, jujur, dan bertanggung jawab.</li>
                <li>Mengembangkan bakat dan minat siswa melalui kegiatan ekstrakurikuler yang beragam.</li>
                <li>Membangun kemitraan yang harmonis dengan orang tua dan masyarakat.</li>
            </ul>
        </div>
    </div>
</div></div>

<h2 class="h4 fw-bold mb-3">Periode yang Sedang Dibuka</h2>
<div class="row g-3 mb-4">
    @forelse($openPeriods as $period)
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body">
                <h3 class="h5 fw-bold">{{ $period->name }}</h3>
                <p class="text-muted mb-1">Tahun ajaran {{ $period->academicYear?->year }}</p>
                <p class="mb-0">{{ $period->start_date->format('d M Y') }} — {{ $period->end_date->format('d M Y') }}</p>
                <x-status-badge :status="$period->status" />
            </div></div>
        </div>
    @empty
        <div class="col-12"><div class="alert alert-warning mb-0">Saat ini belum ada periode pendaftaran yang dibuka.</div></div>
    @endforelse
</div>

<h2 class="h4 fw-bold mb-3">Berita & Kegiatan Sekolah</h2>
<div class="row g-3 mb-4">
    @foreach([
        ['photo-1509062522246-3755977927d7', 'ppdb-berita1', '28 Oktober 2026', 'PPDB Gelombang 1 Resmi Dibuka, Kuota 288 Siswa Baru', 'Pendaftaran peserta didik baru Gelombang 1 dibuka secara online. Calon siswa dapat memilih empat jurusan dan mengunggah seluruh dokumen dari rumah.'],
        ['photo-1523050854058-8df90110c9f1', 'ppdb-berita2', '24 Oktober 2026', 'Tim Olimpiade Sains Raih Juara Umum Tingkat Kota', 'Sebanyak 12 medali dibawa pulang tim olimpiade sains sekolah pada ajang tingkat kota, termasuk 5 medali emas dari bidang matematika dan IPA.'],
        ['photo-1577896851231-70ef18881754', 'ppdb-berita3', '18 Oktober 2026', 'Laboratorium Komputer & Perpustakaan Digital Diresmikan', 'Fasilitas pembelajaran baru resmi digunakan tahun ajaran ini: laboratorium komputer modern dan perpustakaan digital dengan ribuan koleksi e-book.'],
    ] as [$foto, $seed, $tgl, $judul, $ringkas])
        <div class="col-md-4">
            <article class="card h-100 overflow-hidden">
                <div style="aspect-ratio:16/9; overflow:hidden">
                    <img src="https://images.unsplash.com/{{ $foto }}?auto=format&fit=crop&w=900&q=70" alt="{{ $judul }}" loading="lazy" class="w-100 h-100" style="object-fit:cover"
                         onerror="this.onerror=null;this.src='https://picsum.photos/seed/{{ $seed }}/900/600'">
                </div>
                <div class="card-body">
                    <div class="small fw-bold text-uppercase" style="color:#b8912f; letter-spacing:.06em">{{ $tgl }}</div>
                    <h3 class="h5 fw-bold mt-1">{{ $judul }}</h3>
                    <p class="text-muted mb-0">{{ $ringkas }}</p>
                </div>
            </article>
        </div>
    @endforeach
</div>

<h2 class="h4 fw-bold mb-3">Pengumuman</h2>
<div class="card"><div class="list-group list-group-flush">
    @forelse($announcements as $announcement)
        <button type="button" class="list-group-item list-group-item-action text-start" onclick="showPengumuman({{ $announcement->id }})">
            <div class="fw-bold">{{ $announcement->title }}</div>
            <div class="small text-muted">{{ $announcement->publish_at?->format('d M Y H:i') }}</div>
            <div class="mt-1">{!! nl2br(e($announcement->content)) !!}</div>
            <div class="small fw-semibold text-primary mt-1">Baca Selengkapnya →</div>
        </button>
    @empty
        <div class="list-group-item text-muted">Belum ada pengumuman.</div>
    @endforelse
</div></div>

<div class="modal fade" id="pengumumanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:1rem; overflow:hidden">
            <div class="modal-body p-4">
                <div class="small fw-bold text-uppercase" id="pengumumanModalTgl" style="color:#b8912f; letter-spacing:.08em"></div>
                <h3 class="fw-bold mt-1" id="pengumumanModalJudul" style="font-family:'Playfair Display',Georgia,serif"></h3>
                <div id="pengumumanModalIsi" class="lh-lg"></div>
                <div class="d-flex align-items-center justify-content-between mt-4 pt-3 border-top">
                    <button type="button" id="pengumumanPrev" class="btn btn-outline-primary rounded-circle d-flex align-items-center justify-content-center" style="width:48px; height:48px; font-size:21px; font-weight:700" aria-label="Sebelumnya">&#8592;</button>
                    <span id="pengumumanPos" class="small fw-semibold text-muted"></span>
                    <button type="button" id="pengumumanNext" class="btn btn-outline-primary rounded-circle d-flex align-items-center justify-content-center" style="width:48px; height:48px; font-size:21px; font-weight:700" aria-label="Berikutnya">&#8594;</button>
                </div>
            </div>
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
    </div>
</div>
<script>
    window.PENGUMUMAN = {!! json_encode($announcements->mapWithKeys(fn ($a) => [$a->id => ['judul' => $a->title, 'tgl' => $a->publish_at?->format('d M Y'), 'isi' => $a->content]])) !!};
    var pengIds = Object.keys(window.PENGUMUMAN), pengIdx = 0;
    function renderPengumuman() {
        var p = window.PENGUMUMAN[pengIds[pengIdx]]; if (!p) return;
        document.getElementById('pengumumanModalTgl').textContent = p.tgl || '';
        document.getElementById('pengumumanModalJudul').textContent = p.judul;
        document.getElementById('pengumumanModalIsi').innerHTML = String(p.isi || '').split(/\n+/).filter(Boolean).map(function (par) {
            var d = document.createElement('p'); d.textContent = par; return d.outerHTML;
        }).join('');
        document.getElementById('pengumumanPrev').disabled = pengIdx <= 0;
        document.getElementById('pengumumanNext').disabled = pengIdx >= pengIds.length - 1;
        document.getElementById('pengumumanPos').textContent = (pengIdx + 1) + ' dari ' + pengIds.length;
    }
    function showPengumuman(id) {
        pengIdx = pengIds.indexOf(String(id));
        if (pengIdx < 0) return;
        renderPengumuman();
        bootstrap.Modal.getOrCreateInstance(document.getElementById('pengumumanModal')).show();
    }
    document.getElementById('pengumumanPrev').addEventListener('click', function () { if (pengIdx > 0) { pengIdx--; renderPengumuman(); } });
    document.getElementById('pengumumanNext').addEventListener('click', function () { if (pengIdx < pengIds.length - 1) { pengIdx++; renderPengumuman(); } });
</script>
</div>
@endsection
