<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PPDB Online') — {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f6f7fb; }
        .navbar-ppdb { background: linear-gradient(90deg, #1d4ed8, #0ea5e9); }
        .card { border: 0; box-shadow: 0 1px 4px rgba(16,24,40,.08); }
        .sidebar .list-group-item { border: 0; border-radius: .5rem; margin-bottom: .25rem; }
        .sidebar .list-group-item.active { background: #1d4ed8; }
        .stat-card h3 { font-weight: 700; margin-bottom: 0; }
    </style>
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark navbar-ppdb">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('ppdb.index') }}">PPDB Online</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="{{ route('ppdb.index') }}">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('ppdb.check-form') }}">Cek Status</a></li>
                @auth
                    @if(auth()->user()->role?->code === 'applicant')
                        <li class="nav-item"><a class="nav-link" href="{{ route('applicant.dashboard') }}">Dashboard Saya</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                    @endif
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-light" type="submit">Keluar ({{ auth()->user()->name }})</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a class="btn btn-sm btn-light" href="{{ route('login') }}">Masuk</a></li>
                    @if(Route::has('register'))
                        <li class="nav-item"><a class="btn btn-sm btn-warning" href="{{ route('register') }}">Daftar Akun</a></li>
                    @endif
                @endauth
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4">
    @include('components.alert')
    @yield('content')
</main>

<footer class="container pb-4 text-center text-muted small">
    PPDB Online &copy; {{ date('Y') }}
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
