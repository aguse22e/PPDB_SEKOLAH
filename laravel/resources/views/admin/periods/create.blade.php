@extends('layouts.ppdb')
@section('title', 'Tambah Periode')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.admin-sidebar')</div>
    <div class="col-md-9"><div class="card"><div class="card-body p-4">
        <h1 class="h4 fw-bold mb-3">Tambah Periode</h1>
        <form method="POST" action="{{ route('admin.periods.store') }}">
            @csrf
            @include('admin.periods._fields', ['period' => null])
            <button class="btn btn-primary" type="submit">Simpan</button>
            <a href="{{ route('admin.periods.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div></div></div>
</div>
@endsection
