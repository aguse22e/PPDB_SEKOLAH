@extends('layouts.ppdb')
@section('title', 'Atur Ulang Password')

@section('content')
<div class="row justify-content-center"><div class="col-md-5">
    <div class="card"><div class="card-body p-4">
        <h1 class="h4 fw-bold mb-3">Atur Ulang Password</h1>
        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $email) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Ulangi Password Baru</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Simpan Password Baru</button>
        </form>
    </div></div>
</div></div>
@endsection
