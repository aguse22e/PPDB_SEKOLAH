<div class="row">
    <div class="col-md-4 mb-3"><label class="form-label">Kode *</label><input name="code" class="form-control" value="{{ old('code', $path->code ?? '') }}" required></div>
    <div class="col-md-8 mb-3"><label class="form-label">Nama Jalur *</label><input name="name" class="form-control" value="{{ old('name', $path->name ?? '') }}" required></div>
</div>
<div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="2">{{ old('description', $path->description ?? '') }}</textarea></div>
<div class="row">
    <div class="col-md-6 mb-3"><label class="form-label">Kuota Jalur</label><input type="number" name="quota" class="form-control" min="0" value="{{ old('quota', $path->quota ?? 0) }}"></div>
    <div class="col-md-6 mb-3"><div class="form-check mt-4"><input type="checkbox" name="is_active" value="1" class="form-check-input" id="path-active" @checked(old('is_active', $path->is_active ?? true))><label class="form-check-label" for="path-active">Aktif</label></div></div>
</div>
@php
    $requiredIds = isset($path) ? $path->documentTypes->filter(fn($d) => $d->pivot->is_required)->pluck('id')->all() : [];
    $optionalIds = isset($path) ? $path->documentTypes->filter(fn($d) => ! $d->pivot->is_required)->pluck('id')->all() : [];
@endphp
<div class="row">
    <div class="col-md-6 mb-3"><label class="form-label fw-bold">Dokumen Wajib</label>
        @foreach($documentTypes as $doc)
            <div class="form-check"><input type="checkbox" name="required_documents[]" value="{{ $doc->id }}" class="form-check-input" id="req-{{ $doc->id }}" @checked(in_array($doc->id, old('required_documents', $requiredIds)))><label class="form-check-label" for="req-{{ $doc->id }}">{{ $doc->name }}</label></div>
        @endforeach
    </div>
    <div class="col-md-6 mb-3"><label class="form-label fw-bold">Dokumen Opsional</label>
        @foreach($documentTypes as $doc)
            <div class="form-check"><input type="checkbox" name="optional_documents[]" value="{{ $doc->id }}" class="form-check-input" id="opt-{{ $doc->id }}" @checked(in_array($doc->id, old('optional_documents', $optionalIds)))><label class="form-check-label" for="opt-{{ $doc->id }}">{{ $doc->name }}</label></div>
        @endforeach
    </div>
</div>
