@extends('layouts.ppdb')
@section('title', 'Tambah Jenis Dokumen')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.admin-sidebar')</div>
    <div class="col-md-9">

        <div class="card"><div class="card-body p-4">
            <h1 class="h4 fw-bold mb-3">{{ 'Tambah ' }}Jenis Dokumen</h1>
            <form method="POST" action="{{ route('admin.document-types.store') }}">
                @csrf 
                <div class="mb-3"><label class="form-label">Kode, contoh: kk</label><input type="text" name="code" class="form-control" value="{{ old('code') }}" required></div>
                <div class="mb-3"><label class="form-label">Nama Dokumen</label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
                <div class="mb-3"><label class="form-label">Format yang diizinkan, contoh: pdf,jpg,png</label><input type="text" name="allowed_extensions" class="form-control" value="{{ old('allowed_extensions') }}" ></div>
                <div class="mb-3"><label class="form-label">Ukuran maks (MB)</label><input type="number" name="max_size_mb" class="form-control" value="{{ old('max_size_mb') }}" ></div>
                <div class="form-check mb-3"><input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked(old('is_active'))><label class="form-check-label" for="is_active">Aktif</label></div>
                <button class="btn btn-primary" type="submit">Simpan</button>
                <a href="{{ route('admin.document-types.index') }}" class="btn btn-outline-secondary">Batal</a>
            </form>
        </div></div>
    </div>
</div>
@endsection
