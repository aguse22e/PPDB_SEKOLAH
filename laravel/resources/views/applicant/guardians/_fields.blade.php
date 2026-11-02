@php $g = fn($key) => old($key, $guardian->$key ?? ''); @endphp
<div class="col-md-4"><label class="form-label">Hubungan *</label>
    <select name="relationship" class="form-select" required>
        @foreach(['ayah' => 'Ayah', 'ibu' => 'Ibu', 'wali' => 'Wali'] as $value => $label)
            <option value="{{ $value }}" @selected($g('relationship') === $value)>{{ $label }}</option>
        @endforeach
    </select></div>
<div class="col-md-8"><label class="form-label">Nama Lengkap *</label><input name="name" class="form-control" value="{{ $g('name') }}" required></div>
<div class="col-md-6"><label class="form-label">NIK</label><input name="nik" class="form-control" value="{{ $g('nik') }}"></div>
<div class="col-md-6"><label class="form-label">Tanggal Lahir</label><input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', $guardian?->birth_date?->format('Y-m-d')) }}"></div>
<div class="col-md-6"><label class="form-label">Pendidikan Terakhir</label><input name="education" class="form-control" value="{{ $g('education') }}"></div>
<div class="col-md-6"><label class="form-label">Pekerjaan</label><input name="occupation" class="form-control" value="{{ $g('occupation') }}"></div>
<div class="col-md-6"><label class="form-label">Kisaran Penghasilan</label>
    <select name="income_range" class="form-select">
        <option value="">- Pilih -</option>
        @foreach(['<1jt', '1-3jt', '3-5jt', '>5jt'] as $range)
            <option value="{{ $range }}" @selected($g('income_range') === $range)>{{ $range }}</option>
        @endforeach
    </select></div>
<div class="col-md-6"><label class="form-label">No. HP</label><input name="phone" class="form-control" value="{{ $g('phone') }}"></div>
<div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ $g('email') }}"></div>
<div class="col-md-6"><label class="form-label">Alamat (kosongkan jika sama dengan siswa)</label><input name="address" class="form-control" value="{{ $g('address') }}"></div>
<div class="col-12 form-check ms-2"><input type="checkbox" name="is_emergency_contact" value="1" class="form-check-input" id="emergency-{{ $guardian?->id ?? 'new' }}" @checked(old('is_emergency_contact', $guardian->is_emergency_contact ?? false))><label class="form-check-label" for="emergency-{{ $guardian?->id ?? 'new' }}">Jadikan kontak darurat</label></div>
