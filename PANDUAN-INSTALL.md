# Panduan Install PPDB Laravel — dari Nol sampai Jalan

Panduan ini memasang paket **PPDB Laravel Lengkap** (20 migration, 19 model, 26 controller, 10 form request, 56 view Blade, auth bawaan) ke proyek Laravel baru yang masih kosong.
Waktu tempuh: sekitar 20–30 menit jika semua syarat sudah terpasang.

> **Aturan emas sebelum mulai:** jangan install starter kit auth apa pun (Breeze/Jetstream/Fortify) di proyek ini. Paket PPDB sudah punya register/login/reset password sendiri, dan nama route-nya akan bentrok jika digabung.

---

## Ringkasan Alur (Checklist)

- [ ] 1. PHP, Composer, dan MySQL siap
- [ ] 2. Proyek Laravel baru dibuat
- [ ] 3. Database MySQL dibuat dan `.env` diatur
- [ ] 4. File paket disalin, migration `users` bawaan dihapus
- [ ] 5. Route, middleware `role`, dan seeder didaftarkan
- [ ] 6. `php artisan migrate:fresh --seed` berhasil
- [ ] 7. Server jalan, login admin berhasil
- [ ] 8. Skenario uji end-to-end dilewati (Bagian 9)

---

## 1. Syarat

| Kebutuhan | Versi | Cek dengan |
|---|---|---|
| PHP | 8.2 atau lebih baru | `php -v` |
| Ekstensi PHP | BCMath, Ctype, Fileinfo, JSON, MbString, OpenSSL, PDO + pdo_mysql, Tokenizer, XML | `php -m` |
| Composer | 2.x | `composer -V` |
| MySQL / MariaDB | MySQL 8 atau MariaDB 10.4+ | `mysql --version` |

Catatan:

- Di Windows, cara termudah adalah **Laragon** atau XAMPP (PHP 8.2+) + Composer terpisah.
- **Node.js/NPM tidak dibutuhkan** — tampilan memakai Bootstrap 5 via CDN, tidak ada proses build Vite.
- Database harus MySQL/MariaDB. Jangan memakai SQLite: skema PPDB memakai kolom `ENUM` MySQL.

## 2. Buat Proyek Laravel Baru

```bash
composer create-project laravel/laravel ppdb-sekolah
cd ppdb-sekolah
```

Setelah selesai, proyek ini masih polos dan belum ada auth apa pun — memang itu yang kita mau.

## 3. Buat Database & Atur `.env`

Buat database kosong (pilih salah satu cara):

```sql
-- lewat MySQL CLI / phpMyAdmin
CREATE DATABASE ppdb_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Lalu ubah `.env`:

```env
APP_NAME="PPDB Sekolah"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ppdb_db
DB_USERNAME=root
DB_PASSWORD=

# Untuk awal, email (termasuk link reset password) cukup ditulis ke log.
# Ganti ke smtp production jika sudah punya server email.
MAIL_MAILER=log
```

> Paket ini memakai session database (`SESSION_DRIVER=database` adalah default Laravel) — tabelnya dibuat oleh migration `000020` dari paket, jadi tidak perlu diubah.

## 4. Salin File Paket ke Proyek

Ekstrak `ppdb-laravel-lengkap.zip`, lalu salin isinya mengikuti tabel ini:

| Dari paket (`laravel/`) | Ke proyek | Keterangan |
|---|---|---|
| `database/migrations/*.php` | `database/migrations/` | 20 file, nomor 000001–000020 |
| `database/seeders/PpdbMasterSeeder.php` | `database/seeders/` | |
| `app/Models/*.php` | `app/Models/` | `User.php` menimpa model bawaan — memang harus |
| `app/Http/Controllers/*` | `app/Http/Controllers/` | Termasuk folder `Auth`, `Applicant`, `Admin`, `Public`. File `Controller.php` bawaan **jangan ditimpa** (paket tidak menyertakannya) |
| `app/Http/Requests/*` | `app/Http/Requests/` | Termasuk folder `Auth` |
| `app/Http/Middleware/EnsureUserHasRole.php` | `app/Http/Middleware/` | |
| `routes/ppdb.php` dan `routes/ppdb-auth.php` | `routes/` | File baru, tidak menimpa apa pun |
| `resources/views/*` | `resources/views/` | 56 file Blade |

### Langkah wajib: hapus migration users bawaan

Di proyek Laravel 11/12 baru ada file:

```text
database/migrations/0001_01_01_000000_create_users_table.php
```

**Hapus file itu.** File tersebut membuat tabel `users`, `password_reset_tokens`, dan `sessions` sekaligus — paket PPDB sudah menggantikannya lewat migration `000002` (users versi PPDB, ada `role_id`) dan `000020` (password_reset_tokens + sessions).

Dua file bawaan lain **biarkan saja, jangan dihapus**:

```text
0001_01_01_000001_create_cache_table.php
0001_01_01_000002_create_jobs_table.php
```

### Contoh perintah salin (Linux/macOS)

Jalankan dari folder hasil ekstrak ZIP (yang berisi folder `laravel/`):

```bash
cp laravel/database/migrations/*.php        /path/ke/ppdb-sekolah/database/migrations/
cp laravel/database/seeders/*.php           /path/ke/ppdb-sekolah/database/seeders/
cp laravel/app/Models/*.php                 /path/ke/ppdb-sekolah/app/Models/
cp -r laravel/app/Http/Controllers/*        /path/ke/ppdb-sekolah/app/Http/Controllers/
cp -r laravel/app/Http/Requests/*           /path/ke/ppdb-sekolah/app/Http/Requests/
cp laravel/app/Http/Middleware/*.php        /path/ke/ppdb-sekolah/app/Http/Middleware/
cp laravel/routes/*.php                     /path/ke/ppdb-sekolah/routes/
cp -r laravel/resources/views/*             /path/ke/ppdb-sekolah/resources/views/
rm /path/ke/ppdb-sekolah/database/migrations/0001_01_01_000000_create_users_table.php
```

Di Windows (PowerShell), gunakan `Copy-Item -Recurse` dengan pola yang sama, atau salin lewat File Explorer mengikuti tabel di atas.

Setelah menyalin, segarkan autoload:

```bash
composer dump-autoload
```

## 5. Daftarkan Route, Middleware, dan Seeder

### 5.1 Route — `routes/web.php`

Tambahkan dua baris ini di bagian bawah `routes/web.php`:

```php
require __DIR__ . '/ppdb.php';
require __DIR__ . '/ppdb-auth.php';
```

### 5.2 Middleware `role` — `bootstrap/app.php`

Di dalam blok `withMiddleware` yang sudah ada, tambahkan alias berikut:

```php
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\IdleTimeoutLogout;

// di dalam ->withMiddleware(function (Middleware $middleware) { ... })
$middleware->alias([
    'role' => EnsureUserHasRole::class,
    'idle.timeout' => IdleTimeoutLogout::class,
]);
```

Middleware `idle.timeout` membuat akun **otomatis keluar setelah 5 menit tidak ada aktivitas** (berlaku untuk pendaftar, guru, dan semua panitia), dengan pesan penjelasan di halaman login. Sebagai pengaman tambahan di sisi server, set juga di `.env`:

```env
SESSION_LIFETIME=5
```

### 5.3 Seeder — `database/seeders/DatabaseSeeder.php`

Isi method `run()` menjadi:

```php
public function run(): void
{
    $this->call(PpdbMasterSeeder::class);
}
```

Seeder ini mengisi: 5 role, 5 jalur pendaftaran, 7 jenis dokumen, syarat dokumen per jalur, settings awal, dan akun admin pertama.

## 6. Jalankan Migration + Seeder

```bash
php artisan migrate:fresh --seed
```

> Peringatan: `migrate:fresh` **menghapus semua tabel** lalu membuatnya ulang. Aman di proyek baru; jangan jalankan di database yang sudah berisi data produksi. Untuk pemasangan ulang tanpa menghapus data pendaftar, gunakan `php artisan migrate` + `php artisan db:seed --class=PpdbMasterSeeder` (seeder paket ini aman diulang karena memakai `updateOrCreate`).

Hasil yang benar: tidak ada error, dan jumlah tabel bertambah sesuai paket (19 tabel PPDB + tabel bawaan Laravel seperti cache/jobs/sessions).

Perintah verifikasi cepat:

```bash
php artisan route:list --name=applicant
php artisan route:list --name=admin
php artisan route:list --name=ppdb
```

## 7. Jalankan Server & Login Admin

```bash
php artisan serve
```

Buka alamat berikut:

| URL | Untuk |
|---|---|
| `http://127.0.0.1:8000/ppdb` | Halaman publik PPDB + pengumuman |
| `http://127.0.0.1:8000/register` | Pendaftaran akun calon siswa |
| `http://127.0.0.1:8000/login` | Login semua role |
| `http://127.0.0.1:8000/ppdb/cek-status` | Cek status tanpa login |

Akun admin dari seeder:

```text
Email    : admin@sekolah.sch.id
Password : password
```

Setelah login, admin masuk ke `/admin/ppdb/dashboard`.

### Segera ganti password admin

Belum ada halaman ganti password di paket ini, jadi ganti lewat Tinker:

```bash
php artisan tinker
```

```php
\App\Models\User::where('email', 'admin@sekolah.sch.id')->first()
    ->update(['password' => \Illuminate\Support\Facades\Hash::make('password-baru-yang-kuat')]);
```

## 8. Siapkan Data Master (Sekali Saja, sebagai Admin)

Login sebagai admin, lalu isi berurutan:

1. **Tahun Ajaran** → tambah, misalnya `2027/2028`, centang **Aktif**.
2. **Jurusan** → tambah semua jurusan/kelas yang dibuka.
3. **Jalur Pendaftaran** → sudah terisi 5 jalur dari seeder; periksa dan atur dokumen wajib/opsional per jalur bila perlu.
4. **Jenis Dokumen** → sudah terisi 7 jenis dari seeder; sesuaikan format dan ukuran maksimal bila perlu.
5. **Periode / Gelombang** → tambah periode: pilih tahun ajaran, isi tanggal, atur **kuota per jurusan**, lalu ubah status menjadi **Dibuka (open)**. Pendaftar hanya bisa mendaftar pada periode berstatus open dan di dalam rentang tanggalnya.
6. **Pengumuman** → buat pengumuman pembukaan PPDB dan centang **Terbitkan** agar tampil di halaman publik.

Pengaturan nama sekolah dan biaya ada di tabel `settings` (belum ada halaman UI-nya) — ubah lewat phpMyAdmin atau Tinker:

```php
\App\Models\Setting::updateOrCreate(['key' => 'nama_sekolah'], ['value' => 'SMA Contoh']);
\App\Models\Setting::updateOrCreate(['key' => 'biaya_formulir'], ['value' => '150000']);
\App\Models\Setting::updateOrCreate(['key' => 'biaya_daftar_ulang'], ['value' => '1500000']);
```

## 9. Skenario Uji End-to-End (Wajib Dicoba Sekali)

Lewati alur ini untuk memastikan semua modul tersambung:

**Sebagai pendaftar (logout dulu dari admin, atau pakai jendela incognito):**

1. Buka `/register` → daftar dengan data calon siswa → otomatis masuk ke dashboard pendaftar.
2. **Biodata** → lengkapi dan simpan.
3. **Data Orang Tua/Wali** → tambah minimal satu (ayah/ibu/wali).
4. **Daftar Baru** → pilih periode yang open, jalur, dan jurusan → tersimpan sebagai draft dengan nomor seperti `PPDB-2027-0001`.
5. Di halaman detail pendaftaran, **unggah semua dokumen wajib** sesuai jalur.
6. Klik **Kirim Pendaftaran** → status menjadi Terkirim. Coba juga `/ppdb/cek-status` dengan nomor pendaftaran + tanggal lahir.

**Sebagai admin:**

7. Di detail pendaftaran yang sama: **verifikasi setiap dokumen**, lalu klik **Setujui & Lanjut ke Seleksi**.
8. **Buat tagihan** biaya formulir dari panel Pembayaran di halaman yang sama.
9. Masukkan **nilai seleksi**, lalu **Tetapkan Hasil Akhir = Diterima**.
10. Buat tagihan **daftar ulang**.

**Sebagai pendaftar lagi:**

11. Buka menu **Pembayaran** → buka tagihan → unggah bukti bayar (status menjadi Menunggu Verifikasi).

**Sebagai admin/keuangan:**

12. Buka menu **Pembayaran** → periksa tagihan daftar ulang → **Verifikasi**. Status pembayaran menjadi Lunas dan status pendaftaran otomatis menjadi **Sudah Daftar Ulang**.
13. Buka **Laporan** → angka per jalur/jurusan/status terisi, dan export CSV bisa diunduh.

Jika 13 langkah ini mulus, aplikasi sudah jalan penuh.

## Data Dummy untuk Testing (Opsional, Lokal Saja)

Paket menyertakan 14 factory di `database/factories/` dan seeder `PpdbDummySeeder`. Untuk tampilan yang penuh data realistis sejak pertama jalan:

```bash
# opsional: nama & alamat bernuansa Indonesia
# tambahkan di .env: FAKER_LOCALE=id_ID

php artisan migrate:fresh --seed
php artisan db:seed --class=PpdbDummySeeder
```

Isi data dummy: tahun ajaran 2027/2028 aktif, Gelombang 1 berstatus open (+ kuota jurusan), 4 jurusan, 3 pengumuman, **30 pendaftar** yang statusnya tersebar ke semua tahap (draft, terkirim, ditinjau, dokumen ditolak, terverifikasi, seleksi, diterima, cadangan, ditolak, daftar ulang) lengkap dengan wali, dokumen, pembayaran, dan nilai seleksi.

Akun uji — semua berpassword `password`:

| Email | Peran | Catatan |
|---|---|---|
| `admin@sekolah.sch.id` | Admin | Dari PpdbMasterSeeder |
| `verifikator@sekolah.sch.id` | Verifikator | Untuk uji verifikasi dokumen |
| `keuangan@sekolah.sch.id` | Keuangan | Untuk uji verifikasi pembayaran |
| `kepsek@sekolah.sch.id` | Kepala Sekolah | Untuk uji laporan |
| `guru@sekolah.sch.id` | Guru / Penilai Seleksi | Untuk uji dashboard guru & input nilai seleksi |
| `siswa@contoh.id` | Pendaftar | Budi Pratama, pendaftaran `PPDB-2027-0001` berstatus terkirim; tanggal lahir `2012-05-17` untuk uji Cek Status |

Catatan jujur soal batasannya:

- `file_path` dokumen dan bukti bayar dummy menunjuk berkas contoh (`documents/dummy/contoh.pdf`) yang **tidak benar-benar ada** di storage — cukup untuk menguji daftar, status, dan verifikasi, bukan unduhan file.
- Seeder dummy **menolak berjalan** di luar environment `local`/`testing`. Jangan pernah memaksanya di production.
- Factory juga bisa dipakai di PHPUnit, contoh: `Application::factory()->accepted()->create()` atau `Payment::factory()->paid()->create()`.

## 10. Troubleshooting

| Gejala | Penyebab paling umum | Solusi |
|---|---|---|
| Error `Table 'users' already exists` saat migrate | Migration users bawaan belum dihapus | Hapus `0001_01_01_000000_create_users_table.php`, lalu `php artisan migrate:fresh --seed` |
| Error route `login` / `register` sudah terdaftar | Starter kit auth terinstall bersamaan, atau `ppdb-auth.php` di-require dua kali | Pilih satu sumber auth; cek `routes/web.php` tidak meng-require dua kali |
| Error `Class EnsureUserHasRole not found` | File middleware belum tersalin / alias belum didaftarkan | Cek Bagian 4 dan 5.2, lalu `composer dump-autoload` |
| Error `PpdbMasterSeeder not found` | Seeder belum tersalin atau belum didaftarkan | Cek Bagian 4 dan 5.3 |
| `SQLSTATE[HY000] [1045/1049]` | `.env` salah atau database belum dibuat | Cek Bagian 3; buat database `ppdb_db` dulu |
| Halaman pendaftar/admin balas **403** | Role user tidak sesuai, atau seeder belum jalan | Pastikan `migrate:fresh --seed` sudah jalan; user pendaftar harus ber-role `applicant` (otomatis dari halaman register) |
| Email reset password tidak masuk | `MAIL_MAILER=log` (mode awal) atau SMTP belum diatur | Dengan `log`, link reset tertulis di `storage/logs/laravel.log`. Untuk produksi atur `MAIL_MAILER=smtp` + kredensial SMTP di `.env` |
| Upload dokumen/bukti gagal | Batas ukuran di `document_types`, atau batas PHP (`upload_max_filesize`, `post_max_size`) lebih kecil | Sesuaikan jenis dokumen di menu admin dan/atau naikkan batas di `php.ini`, lalu restart server |
| Error `View [...] not found` | Folder `resources/views` belum tersalin utuh | Salin ulang Bagian 4, lalu `php artisan view:clear` |
| Error tabel `sessions` tidak ditemukan | Migration `000020` belum ikut tersalin/terjalan | Pastikan 20 file migration ada, jalankan `php artisan migrate` |
| Halaman balas **419 Page Expired** | Session bermasalah / token CSRF kedaluwarsa | Muat ulang halaman; pastikan tabel `sessions` ada dan `APP_URL` benar |
| Perubahan kode tidak terasa | Cache lama | `php artisan optimize:clear` |

## 11. Sebelum Dipakai Beneran (Produksi)

- [ ] Ganti password admin awal (Bagian 7)
- [ ] `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` = domain asli (https)
- [ ] `php artisan key:generate` hanya jika key masih kosong — jangan regenerasi di sistem yang sudah berjalan tanpa alasan
- [ ] Atur SMTP sungguhan untuk email reset password dan notifikasi
- [ ] Pastikan folder `storage/` dan `bootstrap/cache/` bisa ditulis web server
- [ ] Aktifkan HTTPS — formulir berisi NIK dan dokumen identitas anak
- [ ] Jadwalkan backup database + folder `storage/app` (di situlah dokumen dan bukti bayar tersimpan)
- [ ] Batasi siapa yang tahu akun berperan `finance` dan `verifikator` — keduanya menyentuh uang dan kelulusan

## 12. Ringkasan Perintah (Copy-Paste)

```bash
composer create-project laravel/laravel ppdb-sekolah
cd ppdb-sekolah
# buat database ppdb_db di MySQL, atur .env (Bagian 3)
# salin file paket + hapus migration users bawaan (Bagian 4)
# daftarkan route + middleware + seeder (Bagian 5)
composer dump-autoload
php artisan migrate:fresh --seed
php artisan serve
# buka http://127.0.0.1:8000/ppdb
# login admin: admin@sekolah.sch.id / password  -> lalu GANTI password-nya
```
