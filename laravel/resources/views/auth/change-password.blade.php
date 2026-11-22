@extends('layouts.ppdb')
@section('title', 'Ganti Kata Sandi')

@section('content')
<div class="row justify-content-center"><div class="col-md-5">
    <div class="card"><div class="card-body p-4">
        <h1 class="h4 fw-bold mb-1">Ganti Kata Sandi</h1>
        <p class="text-muted">Akun ini masih memakai kata sandi awal. Buat kata sandi baru yang hanya kamu yang tahu, lalu kamu bisa lanjut memakai aplikasi.</p>
        <form method="POST" action="{{ route('password.change.update') }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Kata Sandi Saat Ini</label>
                <input type="password" name="current_password" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Kata Sandi Baru</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Ulangi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Simpan Kata Sandi Baru</button>
        </form>
    </div></div>
</div></div>
@endsection
