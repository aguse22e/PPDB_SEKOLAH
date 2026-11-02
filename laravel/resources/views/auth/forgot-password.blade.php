@extends('layouts.ppdb')
@section('title', 'Lupa Password')

@section('content')
<div class="row justify-content-center"><div class="col-md-5">
    <div class="card"><div class="card-body p-4">
        <h1 class="h4 fw-bold mb-1">Lupa Password</h1>
        <p class="text-muted">Masukkan email akun kamu, kami kirimkan link untuk mengatur ulang password.</p>
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
            </div>
            <button class="btn btn-primary w-100" type="submit">Kirim Link Atur Ulang</button>
        </form>
        <p class="text-center mt-3 mb-0"><a href="{{ route('login') }}">Kembali ke halaman masuk</a></p>
    </div></div>
</div></div>
@endsection
