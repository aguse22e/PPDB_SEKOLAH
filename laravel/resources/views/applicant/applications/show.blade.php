@extends('layouts.ppdb')
@section('title', 'Detail Pendaftaran')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.applicant-sidebar')</div>
    <div class="col-md-9">
        <div class="card mb-3"><div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1 class="h4 fw-bold mb-1">{{ $application->registration_no }}</h1>
                    <div class="text-muted">{{ $application->period->name }} ({{ $application->period->academicYear?->year }}) • {{ $application->path->name }} • {{ $application->major?->name ?? '-' }}@if($application->majorChoice2) / {{ $application->majorChoice2->name }}@endif</div>
                </div>
                <x-status-badge :status="$application->status" />
            </div>
            @if($application->notes)
                <div class="alert alert-warning mt-3 mb-0"><strong>Catatan panitia:</strong> {{ $application->notes }}</div>
            @endif
        </div></div>

        <div class="card mb-3"><div class="card-body">
            <h2 class="h5 fw-bold mb-3">Dokumen Persyaratan</h2>
            @foreach($requiredDocuments as $docType)
                @php $uploaded = $application->documents->firstWhere('document_type_id', $docType->id); @endphp
                <div class="border rounded p-3 mb-2">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="fw-bold">{{ $docType->name }}</span>
                            @if($docType->pivot->is_required)<span class="badge text-bg-danger">Wajib</span>@else<span class="badge text-bg-secondary">Opsional</span>@endif
                            <div class="small text-muted">Format: {{ $docType->allowed_extensions }} • Maks {{ $docType->max_size_mb }} MB
                                @if($uploaded) • Sudah diunggah: {{ $uploaded->original_filename }} — <x-status-badge :status="$uploaded->verification_status" />@endif
                            </div>
                            @if($uploaded?->verification_status === 'rejected' && $uploaded->notes)
                                <div class="small text-danger">Alasan ditolak: {{ $uploaded->notes }}</div>
                            @endif
                        </div>
                        @if(in_array($application->status, ['draft', 'rejected_document']))
                            <form method="POST" action="{{ route('applicant.applications.documents.store', $application) }}" enctype="multipart/form-data" class="d-flex gap-2">
                                @csrf
                                <input type="hidden" name="document_type_id" value="{{ $docType->id }}">
                                <input type="file" name="document" class="form-control form-control-sm" required>
                                <button class="btn btn-sm btn-outline-primary" type="submit">{{ $uploaded ? 'Ganti' : 'Unggah' }}</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div></div>

        <div class="row g-3">
            <div class="col-md-6"><div class="card h-100"><div class="card-body">
                <h2 class="h5 fw-bold">Pembayaran</h2>
                @forelse($application->payments as $payment)
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <div>{{ $payment->invoice_no }}<div class="small text-muted">{{ ucfirst($payment->payment_type) }} • Rp{{ number_format($payment->amount, 0, ',', '.') }}</div></div>
                        <x-status-badge :status="$payment->status" />
                    </div>
                @empty <p class="text-muted mb-0">Belum ada tagihan.</p> @endforelse
                <a href="{{ route('applicant.payments.index') }}" class="btn btn-sm btn-outline-primary mt-2">Lihat Semua Pembayaran</a>
            </div></div></div>
            <div class="col-md-6"><div class="card h-100"><div class="card-body">
                <h2 class="h5 fw-bold">Hasil Seleksi</h2>
                @forelse($application->selectionResults as $result)
                    <div class="border-bottom py-2">{{ ucfirst(str_replace('_', ' ', $result->stage)) }} {{ $result->subject ? '— ' . $result->subject : '' }}
                        <div class="small text-muted">Nilai: {{ $result->score ?? '-' }} / {{ $result->max_score ?? 100 }} • <x-status-badge :status="$result->result_status" /></div>
                    </div>
                @empty <p class="text-muted mb-0">Belum ada hasil seleksi.</p> @endforelse
                <a href="{{ route('applicant.applications.result', $application) }}" class="btn btn-sm btn-outline-primary mt-2">Lihat Hasil Lengkap</a>
            </div></div></div>
        </div>

        @if($application->status === 'draft')
            <div class="card mt-3"><div class="card-body d-flex gap-2 flex-wrap">
                <form method="POST" action="{{ route('applicant.applications.submit', $application) }}">
                    @csrf
                    <button class="btn btn-success" type="submit" onclick="return confirm('Kirim pendaftaran? Pastikan dokumen wajib sudah lengkap.')">Kirim Pendaftaran</button>
                </form>
                <form method="POST" action="{{ route('applicant.applications.destroy', $application) }}">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger" type="submit" onclick="return confirm('Hapus draft ini?')">Hapus Draft</button>
                </form>
            </div></div>
        @endif
    </div>
</div>
@endsection
