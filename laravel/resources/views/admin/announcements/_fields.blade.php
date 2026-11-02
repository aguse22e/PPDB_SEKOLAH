<div class="mb-3"><label class="form-label">Periode Terkait</label>
    <select name="period_id" class="form-select">
        <option value="">- Semua periode / umum -</option>
        @foreach($periods as $period)
            <option value="{{ $period->id }}" @selected(old('period_id', $announcement->period_id ?? null) == $period->id)>{{ $period->name }}</option>
        @endforeach
    </select></div>
<div class="mb-3"><label class="form-label">Judul *</label><input name="title" class="form-control" value="{{ old('title', $announcement->title ?? '') }}" required></div>
<div class="mb-3"><label class="form-label">Isi Pengumuman *</label><textarea name="content" class="form-control" rows="5" required>{{ old('content', $announcement->content ?? '') }}</textarea></div>
<div class="row">
    <div class="col-md-6 mb-3"><label class="form-label">Waktu Terbit</label><input type="datetime-local" name="publish_at" class="form-control" value="{{ old('publish_at', isset($announcement) && $announcement->publish_at ? $announcement->publish_at->format('Y-m-d\TH:i') : '') }}"></div>
    <div class="col-md-6 mb-3"><div class="form-check mt-4"><input type="checkbox" name="is_published" value="1" class="form-check-input" id="published" @checked(old('is_published', $announcement->is_published ?? false))><label class="form-check-label" for="published">Terbitkan</label></div></div>
</div>
