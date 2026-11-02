@extends('layouts.ppdb')
@section('title', 'Masuk')

@section('content')
<div class="row justify-content-center"><div class="col-md-5">
    <div class="card"><div class="card-body p-4">
        <h1 class="h4 fw-bold mb-1">Masuk</h1>
        <p class="text-muted">Untuk pendaftar, panitia, verifikator, dan keuangan — memakai form yang sama.</p>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input type="checkbox" name="remember" value="1" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Ingat saya</label>
                </div>
                <a href="{{ route('password.request') }}">Lupa password?</a>
            </div>
            <button class="btn btn-primary w-100" type="submit">Masuk</button>
        </form>
        <p class="text-center text-muted mt-3 mb-0">Belum punya akun pendaftar? <a href="{{ route('register') }}">Daftar di sini</a></p>
    </div></div>
</div></div>
@endsection
