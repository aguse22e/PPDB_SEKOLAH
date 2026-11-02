@extends('layouts.ppdb')
@section('title', 'Periksa Pendaftaran')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.admin-sidebar')</div>
    <div class="col-md-9">
        <div class="card mb-3"><div class="card-body">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <h1 class="h4 fw-bold mb-1">{{ $application->registration_no }} — {{ $application->applicant->full_name }}</h1>
                    <div class="text-muted">{{ $application->period->name }} ({{ $application->period->academicYear?->year }}) • {{ $application->path->name }} • {{ $application->major?->name ?? '-' }}@if($application->majorChoice2) / {{ $application->majorChoice2->name }}@endif</div>
                </div>
                <x-status-badge :status="$application->status" />
            </div>
            <div class="row mt-3 small">
                <div class="col-md-6">
                    <div>NIK: {{ $application->applicant->nik ?? '-' }} • NISN: {{ $application->applicant->nisn ?? '-' }}</div>
                    <div>Tempat/Tgl Lahir: {{ $application->applicant->birth_place ?? '-' }}, {{ $application->applicant->birth_date?->format('d M Y') ?? '-' }}</div>
                    <div>HP: {{ $application->applicant->phone ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <div>Asal Sekolah: {{ $application->applicant->previous_school ?? '-' }}</div>
                    <div>Alamat: {{ $application->applicant->address ?? '-' }}</div>
                    <div>Dikirim: {{ $application->submitted_at?->format('d M Y H:i') ?? '-' }}</div>
                </div>
            </div>
            <div class="mt-2 small">
                <strong>Wali:</strong>
                @forelse($application->applicant->guardians as $guardian)
                    <span class="badge text-bg-light border text-capitalize">{{ $guardian->relationship }}: {{ $guardian->name }} ({{ $guardian->phone ?? '-' }})</span>
                @empty <span class="text-muted">tidak ada data wali</span> @endforelse
            </div>
        </div></div>

        <div class="card mb-3"><div class="card-body">
            <h2 class="h5 fw-bold mb-3">Verifikasi Dokumen</h2>
            @forelse($application->documents as $document)
                <div class="border rounded p-3 mb-2">
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <div>
                            <span class="fw-bold">{{ $document->documentType->name }}</span> — {{ $document->original_filename }}
                            <div class="small text-muted">Diunggah {{ $document->uploaded_at?->format('d M Y H:i') }} • {{ $document->file_size_kb }} KB • Path: {{ $document->file_path }}</div>
                            @if($document->notes)<div class="small text-danger">Catatan: {{ $document->notes }}</div>@endif
                        </div>
                        <x-status-badge :status="$document->verification_status" />
                    </div>
                    @if($document->verification_status === 'pending')
                        <div class="d-flex gap-2 mt-2 flex-wrap">
                            <form method="POST" action="{{ route('admin.documents.verify', $document) }}">@csrf
                                <button class="btn btn-sm btn-success" type="submit">Verifikasi</button>
                            </form>
                            <form method="POST" action="{{ route('admin.documents.reject', $document) }}" class="d-flex gap-2">
                                @csrf
                                <input name="notes" class="form-control form-control-sm" placeholder="Alasan ditolak (wajib)" required style="min-width:240px">
                                <button class="btn btn-sm btn-outline-danger" type="submit">Tolak</button>
                            </form>
                        </div>
                    @endif
                </div>
            @empty <p class="text-muted mb-0">Pendaftar belum mengunggah dokumen.</p> @endforelse

            <div class="d-flex gap-2 mt-3 flex-wrap">
                <form method="POST" action="{{ route('admin.applications.verify', $application) }}">@csrf
                    <button class="btn btn-success" type="submit">Setujui & Lanjut ke Seleksi</button>
                </form>
                <form method="POST" action="{{ route('admin.applications.reject', $application) }}" class="d-flex gap-2">
                    @csrf
                    <input name="notes" class="form-control" placeholder="Alasan pengembalian (wajib)" required style="min-width:260px">
                    <button class="btn btn-outline-danger" type="submit">Kembalikan ke Pendaftar</button>
                </form>
            </div>
        </div></div>

        <div class="row g-3">
            <div class="col-md-6"><div class="card h-100"><div class="card-body">
                <h2 class="h5 fw-bold">Nilai Seleksi</h2>
                @forelse($application->selectionResults as $result)
                    <div class="border-bottom py-2 d-flex justify-content-between gap-2">
                        <div>{{ ucfirst(str_replace('_', ' ', $result->stage)) }} {{ $result->subject ? '— ' . $result->subject : '' }}
                            <div class="small text-muted">Nilai {{ $result->score ?? '-' }}/{{ $result->max_score ?? 100 }} • Peringkat {{ $result->rank ?? '-' }}</div></div>
                        <div class="text-end"><x-status-badge :status="$result->result_status" />
                            <form method="POST" action="{{ route('admin.selection-results.destroy', $result) }}" class="mt-1">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form>
                        </div>
                    </div>
                @empty <p class="text-muted">Belum ada nilai.</p> @endforelse

                <form method="POST" action="{{ route('admin.selection-results.store', $application) }}" class="mt-3">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-6"><label class="form-label">Tahap</label><select name="stage" class="form-select"><option value="tes_tulis">Tes Tulis</option><option value="wawancara">Wawancara</option><option value="seleksi_berkas">Seleksi Berkas</option><option value="prestasi">Prestasi</option></select></div>
                        <div class="col-md-6"><label class="form-label">Mapel / Keterangan</label><input name="subject" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label">Nilai</label><input type="number" step="0.01" name="score" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label">Nilai Maks</label><input type="number" step="0.01" name="max_score" class="form-control" value="100"></div>
                        <div class="col-md-4"><label class="form-label">Peringkat</label><input type="number" name="rank" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Hasil Tahap</label><select name="result_status" class="form-select"><option value="pending">Menunggu</option><option value="passed">Lulus</option><option value="failed">Tidak Lulus</option><option value="waitlisted">Cadangan</option></select></div>
                        <div class="col-md-6"><label class="form-label">Catatan</label><input name="notes" class="form-control"></div>
                    </div>
                    <button class="btn btn-primary btn-sm mt-2" type="submit">Simpan Nilai</button>
                </form>

                <hr>
                <form method="POST" action="{{ route('admin.applications.final-status', $application) }}" class="d-flex gap-2">
                    @csrf
                    <select name="status" class="form-select"><option value="accepted">Diterima</option><option value="waitlisted">Cadangan</option><option value="rejected">Ditolak</option></select>
                    <button class="btn btn-warning" type="submit" onclick="return confirm('Tetapkan hasil akhir ini dan kirim notifikasi ke pendaftar?')">Tetapkan Hasil Akhir</button>
                </form>
            </div></div></div>

            <div class="col-md-6"><div class="card h-100"><div class="card-body">
                <h2 class="h5 fw-bold">Pembayaran</h2>
                @forelse($application->payments as $payment)
                    <div class="border-bottom py-2 d-flex justify-content-between">
                        <div>{{ $payment->invoice_no }}<div class="small text-muted">{{ ucfirst($payment->payment_type) }} • Rp{{ number_format($payment->amount, 0, ',', '.') }}</div></div>
                        <x-status-badge :status="$payment->status" />
                    </div>
                @empty <p class="text-muted">Belum ada tagihan.</p> @endforelse

                @if(in_array(auth()->user()->role?->code, ['admin', 'finance']))
                    <form method="POST" action="{{ route('admin.payments.store', $application) }}" class="mt-3">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-6"><label class="form-label">Jenis Tagihan</label><select name="payment_type" class="form-select"><option value="formulir">Formulir</option><option value="tes">Tes</option><option value="daftar_ulang">Daftar Ulang</option><option value="lainnya">Lainnya</option></select></div>
                            <div class="col-md-6"><label class="form-label">Nominal (Rp)</label><input type="number" name="amount" class="form-control" min="0" required></div>
                        </div>
                        <button class="btn btn-primary btn-sm mt-2" type="submit">Buat Tagihan</button>
                    </form>
                @endif
            </div></div></div>
        </div>
    </div>
</div>
@endsection
