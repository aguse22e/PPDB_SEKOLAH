@extends('layouts.ppdb')
@section('title', 'Biodata Calon Siswa')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.applicant-sidebar')</div>
    <div class="col-md-9">
        <div class="card"><div class="card-body p-4">
            <h1 class="h4 fw-bold mb-3">Biodata Calon Siswa</h1>
            <form method="POST" action="{{ route('applicant.profile.update') }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="row g-3">
                    @php $a = fn($key, $default = '') => old($key, $applicant->$key ?? $default); @endphp
                    <div class="col-md-8"><label class="form-label">Nama Lengkap *</label><input name="full_name" class="form-control" value="{{ $a('full_name') }}" required></div>
                    <div class="col-md-4"><label class="form-label">Jenis Kelamin *</label>
                        <select name="gender" class="form-select" required>
                            <option value="L" @selected($a('gender') === 'L')>Laki-laki</option>
                            <option value="P" @selected($a('gender') === 'P')>Perempuan</option>
                        </select></div>
                    <div class="col-md-6"><label class="form-label">NIK</label><input name="nik" class="form-control" value="{{ $a('nik') }}"></div>
                    <div class="col-md-6"><label class="form-label">NISN</label><input name="nisn" class="form-control" value="{{ $a('nisn') }}"></div>
                    <div class="col-md-6"><label class="form-label">Tempat Lahir</label><input name="birth_place" class="form-control" value="{{ $a('birth_place') }}"></div>
                    <div class="col-md-6"><label class="form-label">Tanggal Lahir</label><input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', $applicant?->birth_date?->format('Y-m-d')) }}"></div>
                    <div class="col-md-6"><label class="form-label">Agama</label><input name="religion" class="form-control" value="{{ $a('religion') }}"></div>
                    <div class="col-md-6"><label class="form-label">No. HP</label><input name="phone" class="form-control" value="{{ $a('phone') }}"></div>
                    <div class="col-12"><label class="form-label">Alamat</label><textarea name="address" class="form-control" rows="2">{{ $a('address') }}</textarea></div>
                    <div class="col-md-3"><label class="form-label">RT/RW</label><input name="rt_rw" class="form-control" value="{{ $a('rt_rw') }}"></div>
                    <div class="col-md-3"><label class="form-label">Kelurahan / Desa</label><input name="village" class="form-control" value="{{ $a('village') }}"></div>
                    <div class="col-md-3"><label class="form-label">Kecamatan</label><input name="district" class="form-control" value="{{ $a('district') }}"></div>
                    <div class="col-md-3"><label class="form-label">Kode Pos</label><input name="postal_code" class="form-control" value="{{ $a('postal_code') }}"></div>
                    <div class="col-md-6"><label class="form-label">Kota / Kabupaten</label><input name="city" class="form-control" value="{{ $a('city') }}"></div>
                    <div class="col-md-6"><label class="form-label">Provinsi</label><input name="province" class="form-control" value="{{ $a('province') }}"></div>
                    <div class="col-md-6"><label class="form-label">Asal Sekolah</label><input name="previous_school" class="form-control" value="{{ $a('previous_school') }}"></div>
                    <div class="col-md-6"><label class="form-label">Alamat Asal Sekolah</label><input name="previous_school_address" class="form-control" value="{{ $a('previous_school_address') }}"></div>
                    <div class="col-md-6"><label class="form-label">Pas Foto (JPG/PNG, maks 2 MB)</label><input type="file" name="photo" class="form-control" accept=".jpg,.jpeg,.png"></div>
                </div>
                <button class="btn btn-primary mt-3" type="submit">Simpan Biodata</button>
            </form>
        </div></div>
    </div>
</div>
@endsection
