@extends('layouts.ppdb')
@section('title', 'Ubah Tahun Ajaran')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.admin-sidebar')</div>
    <div class="col-md-9">

        <div class="card"><div class="card-body p-4">
            <h1 class="h4 fw-bold mb-3">{{ 'Ubah ' }}Tahun Ajaran</h1>
            <form method="POST" action="{{ route('admin.academic-years.update', $academicYear) }}">
                @csrf @method('PUT')
                <div class="mb-3"><label class="form-label">Tahun Ajaran, contoh: 2027/2028</label><input type="text" name="year" class="form-control" value="{{ old('year', $academicYear->year ?? '') }}" required></div>
                <div class="form-check mb-3"><input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked(old('is_active', $academicYear->is_active ?? false))><label class="form-check-label" for="is_active">Jadikan tahun ajaran aktif</label></div>
                <button class="btn btn-primary" type="submit">Simpan</button>
                <a href="{{ route('admin.academic-years.index') }}" class="btn btn-outline-secondary">Batal</a>
            </form>
        </div></div>
    </div>
</div>
@endsection
