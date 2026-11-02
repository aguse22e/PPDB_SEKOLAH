@props(['status'])

@php
    $map = [
        'draft' => ['secondary', 'Draft'],
        'submitted' => ['info', 'Terkirim'],
        'document_review' => ['info', 'Dokumen Ditinjau'],
        'verified' => ['primary', 'Terverifikasi'],
        'rejected_document' => ['danger', 'Dokumen Ditolak'],
        'selection' => ['warning', 'Seleksi'],
        'accepted' => ['success', 'Diterima'],
        'waitlisted' => ['warning', 'Cadangan'],
        'rejected' => ['danger', 'Ditolak'],
        'registered' => ['success', 'Sudah Daftar Ulang'],
        'withdrawn' => ['dark', 'Mengundurkan Diri'],
        'pending' => ['secondary', 'Menunggu'],
        'waiting_verification' => ['warning', 'Menunggu Verifikasi'],
        'paid' => ['success', 'Lunas'],
        'failed' => ['danger', 'Gagal'],
        'expired' => ['dark', 'Kedaluwarsa'],
        'refunded' => ['dark', 'Dikembalikan'],
        'passed' => ['success', 'Lulus'],
        'open' => ['success', 'Dibuka'],
        'closed' => ['dark', 'Ditutup'],
    ];
    [$color, $label] = $map[$status] ?? ['secondary', ucfirst(str_replace('_', ' ', (string) $status))];
@endphp

<span class="badge text-bg-{{ $color }}">{{ $label }}</span>
