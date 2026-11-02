@extends('layouts.ppdb')
@section('title', 'Data Orang Tua / Wali')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.applicant-sidebar')</div>
    <div class="col-md-9">
        <div class="card mb-3"><div class="card-body">
            <h1 class="h4 fw-bold mb-3">Orang Tua / Wali yang Tersimpan</h1>
            @forelse($guardians as $guardian)
                <div class="border rounded p-3 mb-2">
                    <div class="d-flex justify-content-between">
                        <div><span class="badge text-bg-primary text-capitalize">{{ $guardian->relationship }}</span>
                        <span class="fw-bold ms-1">{{ $guardian->name }}</span></div>
                        <form method="POST" action="{{ route('applicant.guardians.destroy', $guardian) }}" onsubmit="return confirm('Hapus data ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                        </form>
                    </div>
                    <div class="small text-muted mt-1">
                        Pekerjaan: {{ $guardian->occupation ?? '-' }} • HP: {{ $guardian->phone ?? '-' }} • Email: {{ $guardian->email ?? '-' }}
                    </div>
                    {{-- Edit cepat: form update menimpa data yang sama --}}
                    <details class="mt-2"><summary class="small">Ubah data</summary>
                        <form method="POST" action="{{ route('applicant.guardians.update', $guardian) }}" class="row g-2 mt-1">
                            @csrf @method('PUT')
                            @include('applicant.guardians._fields', ['guardian' => $guardian])
                            <div class="col-12"><button class="btn btn-sm btn-primary" type="submit">Simpan Perubahan</button></div>
                        </form>
                    </details>
                </div>
            @empty
                <p class="text-muted mb-0">Belum ada data orang tua/wali. Tambahkan minimal data ayah atau ibu atau wali di bawah ini.</p>
            @endforelse
        </div></div>

        <div class="card"><div class="card-body">
            <h2 class="h5 fw-bold mb-3">Tambah Orang Tua / Wali</h2>
            @if($applicant)
                <form method="POST" action="{{ route('applicant.guardians.store') }}" class="row g-2">
                    @csrf
                    @include('applicant.guardians._fields', ['guardian' => null])
                    <div class="col-12"><button class="btn btn-primary" type="submit">Tambah Data</button></div>
                </form>
            @else
                <div class="alert alert-warning mb-0">Isi biodata calon siswa terlebih dahulu.</div>
            @endif
        </div></div>
    </div>
</div>
@endsection
