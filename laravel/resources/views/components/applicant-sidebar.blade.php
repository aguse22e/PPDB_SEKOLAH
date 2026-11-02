<div class="list-group sidebar">
    <a href="{{ route('applicant.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('applicant.dashboard') ? 'active' : '' }}">Dashboard</a>
    <a href="{{ route('applicant.profile.edit') }}" class="list-group-item list-group-item-action {{ request()->routeIs('applicant.profile.*') ? 'active' : '' }}">Biodata Calon Siswa</a>
    <a href="{{ route('applicant.guardians.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('applicant.guardians.*') ? 'active' : '' }}">Data Orang Tua / Wali</a>
    <a href="{{ route('applicant.applications.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('applicant.applications.*') ? 'active' : '' }}">Pendaftaran Saya</a>
    <a href="{{ route('applicant.applications.create') }}" class="list-group-item list-group-item-action">+ Daftar Baru</a>
    <a href="{{ route('applicant.payments.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('applicant.payments.*') ? 'active' : '' }}">Pembayaran</a>
</div>
