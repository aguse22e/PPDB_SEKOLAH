# PPDB Sekolah

Aplikasi Penerimaan Peserta Didik Baru (PPDB) untuk sekolah — mencakup desain database, paket Laravel siap pasang, dan preview web interaktif.

**Demo langsung:** https://aguse22e.github.io/PPDB_SEKOLAH/ — masuk dengan akun demo `admin@sekolah.sch.id` / `password` (peran Admin), atau daftar sendiri sebagai siswa.

## Isi Repo

- `laravel/` — Paket Laravel lengkap: 24 migration, 20 model, 29 controller, 10 form request, 61 view Blade, seeder, factory, dan auth bawaan (register, login, reset password) dengan peran applicant, guru, admin, verifikator, finance, dan kepala sekolah.
- `preview/preview-web-ppdb.html` — Preview web satu berkas yang dapat dibuka langsung di browser: Beranda Publik, register/login per peran, dashboard siswa/guru/admin, CRUD data master, unggah berkas, verifikasi, pembayaran, dan laporan (simulasi dengan penyimpanan browser).
- `ppdb_schema.sql` — Skema MySQL 19 tabel.
- `ERD.md` dan `erd/` — Entity Relationship Diagram (teks, Mermaid, SVG, dan HTML).
- `PANDUAN-INSTALL.md` — Panduan memasang paket Laravel ke proyek baru, langkah demi langkah.

## Mulai Cepat

1. Buat proyek Laravel baru, lalu salin isi folder `laravel/` mengikuti `PANDUAN-INSTALL.md`.
2. Jalankan `php artisan migrate:fresh --seed`.
3. Untuk melihat tampilannya tanpa memasang apa pun, buka `preview/preview-web-ppdb.html` di browser.

## Alur PPDB

Alur pendaftaran calon siswa (wizard, berurutan — tombol kirim hanya jalan
bila langkah 2–5 sudah lengkap, dijaga `ApplicationController@submit`):

1. Beranda Publik → Register → Login
2. Lengkapi **biodata** calon siswa (nama lengkap dan jenis kelamin wajib)
3. Tambahkan minimal satu **orang tua / wali**
4. Buat **pendaftaran** (pilih periode, jalur, dan jurusan)
5. Unggah **dokumen wajib** sesuai jalur (KTP/Akte, KK, Ijazah)
6. **Kirim** pendaftaran
7. Panitia **memverifikasi berkas** → guru **menilai seleksi** → panitia
   menetapkan **hasil akhir**
8. **Pengumuman** hasil (diterima / cadangan / ditolak), bisa dicek publik
   lewat Cek Status
9. **Tagihan & daftar ulang** (pembayaran diverifikasi panitia)

Status pendaftaran bergerak: `draft` → `submitted` → `document_review` →
`verified` → `selection` → `accepted` / `waitlisted` / `rejected` →
`registered`, dengan `rejected_document` bila berkas ditolak dan `withdrawn`
bila pendaftaran dibatalkan.

## Absensi Barcode (Siswa & Guru)

- Setiap siswa yang sudah daftar ulang punya kode barcode `SIS + nomor`
  (contoh `SIS000001`), setiap guru `GRU + nomor`. Kode dibuat otomatis
  saat akun dibuat; kartu lama yang belum punya kode bisa dibuat lewat
  tombol "Buat Kode yang Belum Ada" di halaman Kartu Barcode.
- Scan di halaman **Absensi → Scan Barcode** (panitia) atau **Scan Absensi**
  (guru): scan pertama hari itu = absen masuk, scan kedua = absen pulang.
  Pemindai barcode USB biasa langsung mengetik kodenya ke kolom scan.
- Masuk setelah jam masuk sekolah berstatus **Terlambat** (jam masuk dari
  Setting `attendance.start_time`, default `07:00`).
- Izin / sakit / alpa dicatat manual dari halaman rekap absensi, lengkap
  dengan ubah status dan hapus. Rekap bisa difilter per tanggal, tipe
  (siswa/guru), status, dan nama; kepala sekolah bisa melihat rekap.
- Kartu barcode bisa dicetak dari halaman **Kartu Barcode** (barcode
  digambar otomatis dari kodenya, format CODE128).
