@extends('layouts.ppdb')
@section('title', 'Dashboard Pendaftar')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.applicant-sidebar')</div>
    <div class="col-md-9">
        <h1 class="h4 fw-bold mb-3">Halo, {{ auth()->user()->name }}</h1>

        @if(! $applicant)
            <div class="alert alert-warning">Biodata calon siswa belum diisi. <a href="{{ route('applicant.profile.edit') }}">Isi biodata sekarang</a> agar bisa membuat pendaftaran.</div>
        @endif

        @if($progress)
            <div class="card mb-3"><div class="card-body">
                <h2 class="h5 fw-bold">Perjalanan Pendaftaranmu</h2>
                <div class="d-flex flex-wrap gap-2 my-2">
                    @foreach($progress['steps'] as $i => $step)
                        @if($progress['status'] === 'registered' || $i < $progress['current'])
                            <span class="badge text-bg-success">{{ $i + 1 }}. {{ $step }} ✓</span>
                        @elseif($i === $progress['current'])
                            <span class="badge text-bg-primary">{{ $i + 1 }}. {{ $step }} — sedang berlangsung</span>
                        @else
                            <span class="badge text-bg-secondary">{{ $i + 1 }}. {{ $step }}</span>
                        @endif
                    @endforeach
                </div>
                @if($progress['next_action'])
                    <div class="alert alert-info mb-0"><strong>Langkah berikutnya:</strong> {{ $progress['next_action'] }}</div>
                @endif
                @if($unpaidBills > 0)
                    <div class="alert alert-warning mt-2 mb-0">Kamu punya <strong>{{ $unpaidBills }} tagihan</strong> yang belum lunas / menunggu verifikasi. <a href="{{ route('applicant.payments.index') }}">Lihat pembayaran</a></div>
                @endif
            </div></div>
        @endif

        <div class="card mb-3"><div class="card-body">
            <h2 class="h5 fw-bold">Pendaftaran Terakhir</h2>
            @forelse($applications as $app)
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <div>
                        <div class="fw-bold">{{ $app->registration_no }}</div>
                        <div class="small text-muted">{{ $app->period->name }} • {{ $app->path->name }} • {{ $app->major?->name ?? '-' }}</div>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <x-status-badge :status="$app->status" />
                        <a class="btn btn-sm btn-primary" href="{{ route('applicant.applications.show', $app) }}">Detail</a>
                    </div>
                </div>
            @empty
                <p class="text-muted mb-0">Belum ada pendaftaran. <a href="{{ route('applicant.applications.create') }}">Buat pendaftaran pertama</a>.</p>
            @endforelse
        </div></div>

        <div class="card"><div class="card-body">
            <h2 class="h5 fw-bold">Notifikasi Terbaru</h2>
            @forelse($notifications as $notification)
                <div class="border-bottom py-2">
                    <div class="fw-bold">{{ $notification->title }}</div>
                    <div>{{ $notification->message }}</div>
                    <div class="small text-muted">{{ $notification->sent_at?->format('d M Y H:i') }}</div>
                </div>
            @empty
                <p class="text-muted mb-0">Belum ada notifikasi.</p>
            @endforelse
        </div></div>
    </div>
</div>
@endsection
