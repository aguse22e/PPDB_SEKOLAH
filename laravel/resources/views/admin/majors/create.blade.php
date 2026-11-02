@extends('layouts.ppdb')
@section('title', 'Tambah Jurusan')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.admin-sidebar')</div>
    <div class="col-md-9">

        <div class="card"><div class="card-body p-4">
            <h1 class="h4 fw-bold mb-3">{{ 'Tambah ' }}Jurusan</h1>
            <form method="POST" action="{{ route('admin.majors.store') }}">
                @csrf 
                <div class="mb-3"><label class="form-label">Kode, contoh: RPL</label><input type="text" name="code" class="form-control" value="{{ old('code') }}" required></div>
                <div class="mb-3"><label class="form-label">Nama Jurusan</label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
                <div class="mb-3"><label class="form-label">Kuota Default</label><input type="number" name="quota" class="form-control" value="{{ old('quota') }}" ></div>
                <div class="form-check mb-3"><input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked(old('is_active'))><label class="form-check-label" for="is_active">Aktif</label></div>
                <button class="btn btn-primary" type="submit">Simpan</button>
                <a href="{{ route('admin.majors.index') }}" class="btn btn-outline-secondary">Batal</a>
            </form>
        </div></div>
    </div>
</div>
@endsection
