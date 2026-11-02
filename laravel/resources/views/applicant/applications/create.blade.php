@extends('layouts.ppdb')
@section('title', 'Buat Pendaftaran')

@section('content')
<div class="row g-3">
    <div class="col-md-3">@include('components.applicant-sidebar')</div>
    <div class="col-md-9">
        <div class="card"><div class="card-body p-4">
            <h1 class="h4 fw-bold mb-3">Buat Pendaftaran Baru</h1>
            <form method="POST" action="{{ route('applicant.applications.store') }}">
                @csrf
                <div class="mb-3"><label class="form-label">Periode / Gelombang *</label>
                    <select name="period_id" class="form-select" required>
                        <option value="">- Pilih periode -</option>
                        @foreach($periods as $period)
                            <option value="{{ $period->id }}" @selected(old('period_id') == $period->id)>{{ $period->name }} — {{ $period->academicYear?->year }} ({{ $period->start_date->format('d M Y') }} s/d {{ $period->end_date->format('d M Y') }})</option>
                        @endforeach
                    </select></div>
                <div class="mb-3"><label class="form-label">Jalur Pendaftaran *</label>
                    <select name="path_id" class="form-select" required>
                        <option value="">- Pilih jalur -</option>
                        @foreach($paths as $path)
                            <option value="{{ $path->id }}" @selected(old('path_id') == $path->id)>{{ $path->name }}</option>
                        @endforeach
                    </select></div>
                <div class="mb-3"><label class="form-label">Jurusan Pilihan 1</label>
                    <select name="major_id" class="form-select">
                        <option value="">- Pilih jurusan -</option>
                        @foreach($majors as $major)
                            <option value="{{ $major->id }}" @selected(old('major_id') == $major->id)>{{ $major->name }}</option>
                        @endforeach
                    </select></div>
                <div class="mb-3"><label class="form-label">Jurusan Pilihan 2</label>
                    <select name="major_choice_2_id" class="form-select">
                        <option value="">- Pilih jurusan -</option>
                        @foreach($majors as $major)
                            <option value="{{ $major->id }}" @selected(old('major_choice_2_id') == $major->id)>{{ $major->name }}</option>
                        @endforeach
                    </select></div>
                <button class="btn btn-primary" type="submit">Simpan sebagai Draft</button>
                <p class="small text-muted mt-2 mb-0">Setelah draft dibuat, unggah dokumen sesuai jalur yang dipilih, lalu klik Kirim Pendaftaran.</p>
            </form>
        </div></div>
    </div>
</div>
@endsection
