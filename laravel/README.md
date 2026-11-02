# PPDB Laravel — Migration, Model, Seeder, Controller, Route & Blade View

Paket ini adalah versi Laravel dari `ppdb_schema.sql` (19 tabel).
Ditulis dengan gaya Laravel 11/12 (anonymous class migration).

## Isi paket

- `database/migrations/` — 20 migration, nomor urut 000001–000020 agar foreign key aman saat `migrate` (000020 = `password_reset_tokens` + `sessions` untuk auth)
- `database/seeders/PpdbMasterSeeder.php` — role, jalur, jenis dokumen, syarat dokumen per jalur, settings, dan 1 akun admin awal
- `database/factories/` + `database/seeders/PpdbDummySeeder.php` — 14 factory dan data dummy testing (30 pendaftar semua status + akun uji per role, khusus local/testing — lihat `PANDUAN-INSTALL.md`)
- `app/Models/` — 19 model dengan `$fillable`, `casts()`, dan relasi antar model
- `app/Http/Controllers/` — 26 controller: `Public/`, `Applicant/`, `Admin/`, `Guru/`, dan `Auth/`
- `app/Http/Requests/` — 10 Form Request untuk validasi (termasuk register & login di `Requests/Auth/`)
- `app/Http/Middleware/EnsureUserHasRole.php` — middleware `role`
- `app/Http/Middleware/IdleTimeoutLogout.php` — middleware `idle.timeout`: **auto-logout setelah 5 menit tidak ada aktivitas**, dengan pesan penjelasan di halaman login
- `routes/ppdb.php` + `routes/ppdb-auth.php` — semua route PPDB & auth, terpisah dari `routes/web.php`
- `resources/views/` — 56 file Blade: 1 layout utama, 5 komponen (termasuk sidebar per peran), view untuk semua controller (publik, pendaftar, admin per peran, guru), 4 partial form, dan 4 view auth

## Cara pasang Blade View

1. Salin isi folder `resources/views/` ke `resources/views/` di proyekmu.
2. Layout memakai **Bootstrap 5 via CDN** (`layouts/ppdb.blade.php`), jadi langsung tampil tanpa perlu build Tailwind/Vite. Kalau proyekmu sudah memakai Tailwind, sesuaikan class di layout saja — isi view memakai class Bootstrap.
3. Komponen yang dipakai di banyak tempat:
   - `<x-status-badge :status="$application->status" />` — label warna untuk semua status pendaftaran/dokumen/pembayaran/seleksi
   - `@include('components.alert')` — pesan sukses & daftar error validasi (sudah dipakai layout)
   - `@include('components.applicant-sidebar')` / `@include('components.admin-sidebar')` — menu samping per peran (menu admin otomatis disesuaikan dengan role yang login)
4. Nama route `login`, `logout`, dan `register` di layout mengikuti auth PPDB di paket ini (lihat bagian Auth di bawah) atau starter kit Laravel (Breeze/Jetstream) — keduanya memakai nama route yang sama.

## Cara pasang Controller & Route

1. Salin folder `app/Http/Controllers/`, `app/Http/Requests/`, dan `app/Http/Middleware/` ke proyekmu (controller bawaan `app/Http/Controllers/Controller.php` jangan ditimpa — paket ini tidak menyertakannya).
2. Salin `routes/ppdb.php` ke folder `routes/`, lalu tambahkan 1 baris ini di `routes/web.php`:

   ```php
   require __DIR__ . '/ppdb.php';
   ```

3. Daftarkan middleware `role` di `bootstrap/app.php`:

   ```php
   use App\Http\Middleware\EnsureUserHasRole;
   use App\Http\Middleware\IdleTimeoutLogout;

   ->withMiddleware(function (Middleware $middleware) {
       $middleware->alias([
           'role' => EnsureUserHasRole::class,
           'idle.timeout' => IdleTimeoutLogout::class,
       ]);
   })
   ```

4. Untuk login/register, pakai auth PPDB bawaan paket ini (bagian Auth di bawah) ATAU starter kit Laravel (Breeze/Jetstream/Fortify) — pilih salah satu, jangan keduanya.
5. Cek route yang terdaftar:

   ```bash
   php artisan route:list --name=ppdb
   php artisan route:list --name=applicant
   php artisan route:list --name=admin
   ```

## Auth: Register, Login, Logout & Reset Password

Paket ini menyertakan auth sendiri di folder `app/Http/Controllers/Auth/`, `app/Http/Requests/Auth/`, view `resources/views/auth/`, dan route `routes/ppdb-auth.php`.

Cara pasang:

1. Salin file-file auth tersebut ke proyekmu, lalu tambahkan di `routes/web.php`:

   ```php
   require __DIR__ . '/ppdb-auth.php';
   ```

2. **Jangan gabungkan dengan route auth starter kit** (Breeze/Jetstream/Fortify) — nama route-nya sama (`login`, `register`, `logout`, `password.request`, `password.reset`) dan akan bentrok. Pilih salah satu.
3. Jalankan migration sampai `000020` — migration terakhir membuat tabel `password_reset_tokens` dan `sessions` (dengan guard `hasTable`, aman jika sudah ada), karena di Laravel 11/12 dua tabel itu biasanya dibuat oleh migration users bawaan yang diganti paket ini.
4. Atur mail di `.env` (`MAIL_*`) agar email lupa password terkirim. Untuk pengujian lokal bisa memakai `MAIL_MAILER=log`.

Perilaku auth:

- **Register khusus pendaftar:** satu transaksi membuat akun `users` dengan role `applicant` + profil `applicants` (nama, jenis kelamin, tanggal lahir, NISN, asal sekolah), lalu langsung login dan diarahkan ke dashboard pendaftar untuk melengkapi biodata.
- **Login satu pintu, dashboard berbeda per peran:** setelah masuk, setiap peran diarahkan ke dashboardnya sendiri — pendaftar ke `applicant.dashboard` (ada progress tracker tahap pendaftaran + langkah berikutnya), guru/penilai seleksi ke `guru.dashboard` (khusus antrean penilaian & input nilai, tanpa master data/pembayaran), dan admin/verifikator/finance/kepala sekolah ke `admin.dashboard` yang ISINYA dibedakan lagi per peran (admin: ringkasan lengkap; verifikator: antrean berkas; finance: pembayaran; kepala sekolah: ringkasan eksekutif + keterserapan kuota). `last_login_at` dicatat setiap login.
- **Proteksi login:** maksimal 5 percobaan gagal per email+IP (pola Breeze), dan akun dengan `is_active = false` ditolak masuk.
- **Reset password** memakai password broker bawaan Laravel; link dikirim ke email dan berlaku sesuai `config/auth.php` (default 60 menit).
- Akun panitia tidak bisa mendaftar sendiri — dibuat lewat `PpdbMasterSeeder` (admin awal) atau oleh admin.

## Pembagian akses per peran

| Area route | Peran yang boleh |
|---|---|
| `/ppdb/*` (publik + cek status) | Tanpa login, cek status dibatasi `throttle:10,1` |
| `/pendaftar/*` | `applicant` |
| `/admin/ppdb/dashboard` | `admin`, `verifikator`, `finance`, `kepala_sekolah` |
| Master data (tahun ajaran, periode, jalur, jurusan, jenis dokumen, pengumuman) | `admin` |
| Verifikasi pendaftaran/dokumen + nilai seleksi + hasil akhir | `admin`, `verifikator` |
| Pembayaran (buat tagihan, verifikasi, tolak) | `admin`, `finance` |
| Laporan + export CSV | `admin`, `finance`, `kepala_sekolah` |
| `/guru/ppdb/*` (dashboard guru + penilaian seleksi) | `guru` |

**RBAC — pemisahan ketat antarperan.** Setiap area hanya boleh dimasuki peran pemiliknya, dan pemeriksaan dilakukan middleware `role` di server pada *setiap* request (bukan hanya saat login):

| Peran akun | Area pendaftar | Area guru | Area admin |
|---|---|---|---|
| `applicant` (siswa) | Diizinkan | Ditolak | Ditolak |
| `guru` | Ditolak | Diizinkan | Ditolak |
| `admin` | Ditolak | Ditolak | Diizinkan |

- Register publik selalu menetapkan peran `applicant` — pendaftar tidak bisa memilih/menetapkan peran guru atau admin untuk dirinya.
- Akun guru/admin dan perubahan peran hanya boleh dibuat oleh pihak yang berwenang (admin), bukan lewat pendaftaran publik.
- Peran dibaca dari data akun di server, tidak pernah dipercaya dari pilihan di browser.
- Peran yang tidak dikenal / tidak berizin ditolak secara default (*deny by default*) — mengetik URL area lain secara langsung tetap ditolak.

## Nama view yang dirujuk controller

Controller mengembalikan view dengan nama berikut — semua file Blade-nya sudah termasuk di paket ini:

- Publik: `public.ppdb.index`, `public.ppdb.check-form`, `public.ppdb.status`
- Pendaftar: `applicant.dashboard`, `applicant.profile.edit`, `applicant.guardians.index`, `applicant.applications.*`, `applicant.payments.*`, `applicant.results.show`
- Admin: `admin.dashboard`, `admin.applications.*`, `admin.payments.*`, `admin.reports.index`, dan CRUD `admin.academic-years.*`, `admin.periods.*`, `admin.paths.*`, `admin.majors.*`, `admin.document-types.*`, `admin.announcements.*`

## Logika bisnis yang sudah ditanam di controller

- Nomor pendaftaran otomatis: `PPDB-{tahun}-{id 4 digit}`, invoice otomatis: `INV-{tahun}-{id 4 digit}`
- Pendaftaran hanya bisa dikirim (`submit`) jika semua dokumen wajib sesuai jalur sudah diunggah, dan hanya pada periode berstatus `open` di rentang tanggalnya
- Upload dokumen mengikuti `allowed_extensions` + `max_size_mb` dari tabel `document_types`, disimpan di disk `local` (private); upload ulang mengganti file lama
- Verifikasi pendaftaran ditolak sistem jika masih ada dokumen belum `verified`
- Pembayaran `daftar_ulang` yang terverifikasi otomatis mengubah status pendaftaran menjadi `registered`
- Setiap perubahan penting (dokumen terverifikasi, perlu perbaikan, pembayaran lunas, hasil seleksi) membuat baris `AppNotification` untuk pendaftar
- Laporan: ringkasan per jalur/jurusan/status + export CSV pendaftar dan pembayaran (siap dibuka di Excel)

## Cara pasang di proyek Laravel

1. Salin isi folder `database/migrations/` ke `database/migrations/` di proyekmu.
2. **Penting — tabel `users`:**
   - Hapus/ganti migration `users` bawaan Laravel (`0001_01_01_000000_create_users_table.php`), karena paket ini sudah menyediakan `create_users_table` sendiri.
   - Migration bawaan lain seperti `password_reset_tokens`, `sessions`, `cache`, dan `jobs` boleh tetap dipakai.
   - Salin `app/Models/User.php` menimpa model bawaan, atau gabungkan manual: yang wajib ada adalah kolom `role_id` di `$fillable` dan relasi `role()` + `applicant()`.
3. Salin model lain di `app/Models/` dan seeder di `database/seeders/` ke proyekmu.
4. Daftarkan seeder di `database/seeders/DatabaseSeeder.php`:

   ```php
   public function run(): void
   {
       $this->call(PpdbMasterSeeder::class);
   }
   ```

5. Jalankan:

   ```bash
   php artisan migrate:fresh --seed
   ```

   Akun admin awal: `admin@sekolah.sch.id` / `password`
   **Segera ganti password ini setelah login pertama.**

## Penyesuaian dari versi SQL mentah

| Versi SQL | Versi Laravel | Alasan |
|---|---|---|
| `password_hash` | `password` | Laravel Auth membaca kolom `password` dan otomatis meng-hash (`'password' => 'hashed'` cast) |
| `TINYINT(1)` | `boolean()` | Idiom Laravel |
| `BIGINT UNSIGNED` + FK manual | `foreignId()->constrained()` | Lebih singkat, index & FK otomatis |
| Timestamp manual | `timestamps()` | `created_at` + `updated_at` otomatis |
| Tabel `notifications` | Model `AppNotification` (tabel tetap `notifications`) | Menghindari bentrok nama dengan class `Illuminate\Notifications\Notification`. Jika proyekmu sudah memakai tabel notifikasi bawaan Laravel, ganti nama tabel di migration `000017` menjadi `app_notifications` |

## Relasi utama di Model

```php
// 1 akun = 1 calon siswa, 1 siswa bisa daftar berkali-kali
$user->applicant;                 // HasOne
$applicant->guardians;            // HasMany: ayah, ibu, wali
$applicant->applications;         // HasMany

// Pendaftaran adalah entitas pusat
$application->applicant;
$application->period;             // AdmissionPeriod
$application->path;               // AdmissionPath
$application->major;              // pilihan 1
$application->majorChoice2;       // pilihan 2
$application->documents;          // HasMany ApplicationDocument
$application->payments;           // HasMany Payment
$application->selectionResults;   // HasMany SelectionResult

// Syarat dokumen per jalur (Many-to-Many)
$path->documentTypes;             // lewat path_document_requirements, pivot: is_required
$period->majors;                  // lewat period_major_quotas, pivot: quota
```

## Catatan

- Status tetap dipisah 3: `applications.status`, `application_documents.verification_status`, `payments.status`.
- File dokumen tidak disimpan di database — simpan di disk `local`/private storage, database hanya menyimpan `file_path`.
- Kolom `rank` di `selection_results` adalah kata yang perlu hati-hati di SQL mentah, tapi aman dipakai lewat Eloquent/Query Builder Laravel.
