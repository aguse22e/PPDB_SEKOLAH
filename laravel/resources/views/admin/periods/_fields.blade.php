<div class="mb-3"><label class="form-label">Tahun Ajaran *</label>
    <select name="academic_year_id" class="form-select" required>
        <option value="">- Pilih tahun ajaran -</option>
        @foreach($years as $year)
            <option value="{{ $year->id }}" @selected(old('academic_year_id', $period->academic_year_id ?? null) == $year->id)>{{ $year->year }}</option>
        @endforeach
    </select></div>
<div class="mb-3"><label class="form-label">Nama Periode *</label><input name="name" class="form-control" value="{{ old('name', $period->name ?? '') }}" placeholder="Gelombang 1" required></div>
<div class="row">
    <div class="col-md-6 mb-3"><label class="form-label">Tanggal Mulai *</label><input type="date" name="start_date" class="form-control" value="{{ old('start_date', isset($period) ? $period->start_date?->format('Y-m-d') : '') }}" required></div>
    <div class="col-md-6 mb-3"><label class="form-label">Tanggal Selesai *</label><input type="date" name="end_date" class="form-control" value="{{ old('end_date', isset($period) ? $period->end_date?->format('Y-m-d') : '') }}" required></div>
</div>
<div class="mb-3"><label class="form-label">Status *</label>
    <select name="status" class="form-select" required>
        @foreach(['draft' => 'Draft', 'open' => 'Dibuka', 'closed' => 'Ditutup'] as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $period->status ?? 'draft') === $value)>{{ $label }}</option>
        @endforeach
    </select></div>
<div class="mb-3">
    <label class="form-label fw-bold">Kuota per Jurusan pada Periode Ini</label>
    @php $quotaMap = isset($period) ? $period->majorQuotas->pluck('quota', 'major_id') : collect(); @endphp
    @foreach($majors as $major)
        <div class="input-group mb-2">
            <span class="input-group-text" style="min-width:220px">{{ $major->name }}</span>
            <input type="number" name="quotas[{{ $major->id }}]" class="form-control" min="0" value="{{ old('quotas.' . $major->id, $quotaMap[$major->id] ?? '') }}" placeholder="Kuota">
        </div>
    @endforeach
</div>
