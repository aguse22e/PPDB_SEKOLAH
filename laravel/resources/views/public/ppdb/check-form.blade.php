@extends('layouts.ppdb')
@section('title', 'Cek Status Pendaftaran')

@section('content')
<div class="row justify-content-center"><div class="col-md-6">
    <div class="card"><div class="card-body p-4">
        <h1 class="h4 fw-bold mb-3">Cek Status Pendaftaran</h1>
        <p class="text-muted">Masukkan nomor pendaftaran dan tanggal lahir calon siswa.</p>
        <form method="POST" action="{{ route('ppdb.check-status') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Nomor Pendaftaran</label>
                <input type="text" name="registration_no" class="form-control" placeholder="PPDB-2027-0001" value="{{ old('registration_no') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tanggal Lahir</label>
                <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Lihat Status</button>
        </form>
    </div></div>
</div></div>
@endsection
