@extends('layouts.ppdb')
@section('title', 'Daftar Akun Pendaftar')

@section('content')
<div class="row justify-content-center"><div class="col-md-7">
    <div class="card"><div class="card-body p-4">
        <h1 class="h4 fw-bold mb-1">Daftar Akun Pendaftar</h1>
        <p class="text-muted">Satu akun untuk satu calon siswa. Biodata lengkap bisa dilengkapi setelah masuk.</p>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <h2 class="h6 fw-bold mt-3">Data Akun</h2>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Nama Pemilik Akun *</label><input name="name" class="form-control" value="{{ old('name') }}" required></div>
                <div class="col-md-6"><label class="form-label">No. HP</label><input name="phone" class="form-control" value="{{ old('phone') }}"></div>
                <div class="col-12"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" required></div>
                <div class="col-md-6"><label class="form-label">Password *</label><input type="password" name="password" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">Ulangi Password *</label><input type="password" name="password_confirmation" class="form-control" required></div>
            </div>

            <h2 class="h6 fw-bold mt-4">Data Awal Calon Siswa</h2>
            <div class="row g-3">
                <div class="col-md-8"><label class="form-label">Nama Lengkap Calon Siswa *</label><input name="full_name" class="form-control" value="{{ old('full_name') }}" required></div>
                <div class="col-md-4"><label class="form-label">Jenis Kelamin *</label>
                    <select name="gender" class="form-select" required>
                        <option value="L" @selected(old('gender') === 'L')>Laki-laki</option>
                        <option value="P" @selected(old('gender') === 'P')>Perempuan</option>
                    </select></div>
                <div class="col-md-6"><label class="form-label">Tanggal Lahir *</label><input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}" required></div>
                <div class="col-md-6"><label class="form-label">NISN</label><input name="nisn" class="form-control" value="{{ old('nisn') }}"></div>
                <div class="col-12"><label class="form-label">Asal Sekolah</label><input name="previous_school" class="form-control" value="{{ old('previous_school') }}"></div>
            </div>

            <button class="btn btn-primary w-100 mt-4" type="submit">Buat Akun & Masuk</button>
        </form>
        <p class="text-center text-muted mt-3 mb-0">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
    </div></div>
</div></div>
@endsection
