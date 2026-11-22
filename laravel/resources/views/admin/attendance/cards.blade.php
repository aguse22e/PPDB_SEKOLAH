@extends('layouts.ppdb')
@section('title', 'Kartu Barcode')

@push('styles')
<style>
    .barcode-card { border: 2px solid #1d4ed8; border-radius: .75rem; padding: 1rem; text-align: center; margin-bottom: 1rem; }
    .barcode-card .barcode { max-width: 100%; }
    @media print {
        nav, footer, .no-print { display: none !important; }
        main { padding: 0 !important; }
        .col-md-3 { display: none !important; }
        .col-md-9 { width: 100% !important; }
    }
</style>
@endpush

@section('content')
<div class="row g-3">
    <div class="col-md-3 no-print">@include('components.admin-sidebar')</div>
    <div class="col-md-9">

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 no-print">
            <h1 class="h4 fw-bold mb-0">Kartu Barcode</h1>
            <div class="d-flex gap-2">
                <form method="POST" action="{{ route('admin.attendance.generate-barcode') }}" class="d-inline">
                    @csrf
                    <button class="btn btn-outline-primary" type="submit">Buat Kode yang Belum Ada</button>
                </form>
                <button class="btn btn-primary" type="button" onclick="window.print()">Cetak Kartu</button>
            </div>
        </div>
        <p class="small text-muted no-print">Kode siswa: <code>SIS + nomor</code> (contoh SIS000001). Kode guru: <code>GRU + nomor</code>. Barcode digambar otomatis dari kodenya dan bisa dipindai alat barcode apa pun.</p>

        <h2 class="h5 fw-bold">Siswa (sudah daftar ulang)</h2>
        <div class="row">
            @forelse($students as $s)
                <div class="col-md-4">
                    <div class="barcode-card">
                        <div class="fw-bold">{{ $s->full_name }}</div>
                        <div class="small text-muted mb-2">Siswa &middot; {{ $s->barcode_code ?? 'belum ada kode' }}</div>
                        @if($s->barcode_code)
                            <svg class="barcode" data-code="{{ $s->barcode_code }}"></svg>
                            <div class="small mt-1">{{ $s->barcode_code }}</div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-muted">Belum ada siswa yang sudah daftar ulang.</p>
            @endforelse
        </div>

        <h2 class="h5 fw-bold mt-2">Guru</h2>
        <div class="row">
            @forelse($teachers as $t)
                <div class="col-md-4">
                    <div class="barcode-card">
                        <div class="fw-bold">{{ $t->name }}</div>
                        <div class="small text-muted mb-2">Guru &middot; {{ $t->barcode_code ?? 'belum ada kode' }}</div>
                        @if($t->barcode_code)
                            <svg class="barcode" data-code="{{ $t->barcode_code }}"></svg>
                            <div class="small mt-1">{{ $t->barcode_code }}</div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-muted">Belum ada akun guru.</p>
            @endforelse
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.0.20/dist/JsBarcode.all.min.js"></script>
<script>
    document.querySelectorAll('svg.barcode').forEach(function (el) {
        JsBarcode(el, el.dataset.code, { format: 'CODE128', width: 2, height: 50, displayValue: false, margin: 4 });
    });
</script>
@endpush
