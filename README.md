# PPDB Sekolah

Aplikasi Penerimaan Peserta Didik Baru (PPDB) untuk sekolah — mencakup desain database, paket Laravel siap pasang, dan preview web interaktif.

## Isi Repo

- `laravel/` — Paket Laravel lengkap: 20 migration, 19 model, 26 controller, 10 form request, 56 view Blade, seeder, factory, dan auth bawaan (register, login, reset password) dengan peran applicant, guru, admin, verifikator, finance, dan kepala sekolah.
- `preview/preview-web-ppdb.html` — Preview web satu berkas yang dapat dibuka langsung di browser: Beranda Publik, register/login per peran, dashboard siswa/guru/admin, CRUD data master, unggah berkas, verifikasi, pembayaran, dan laporan (simulasi dengan penyimpanan browser).
- `ppdb_schema.sql` — Skema MySQL 19 tabel.
- `ERD.md` dan `erd/` — Entity Relationship Diagram (teks, Mermaid, SVG, dan HTML).
- `PANDUAN-INSTALL.md` — Panduan memasang paket Laravel ke proyek baru, langkah demi langkah.

## Mulai Cepat

1. Buat proyek Laravel baru, lalu salin isi folder `laravel/` mengikuti `PANDUAN-INSTALL.md`.
2. Jalankan `php artisan migrate:fresh --seed`.
3. Untuk melihat tampilannya tanpa memasang apa pun, buka `preview/preview-web-ppdb.html` di browser.

## Alur PPDB

Beranda Publik → Register → Login → Dashboard per peran → siswa melengkapi biodata & pendaftaran → unggah berkas (KTP/Akte, KK, Ijazah) → kirim pendaftaran → panitia memverifikasi berkas → guru menilai seleksi → panitia menetapkan hasil akhir → tagihan & daftar ulang.
