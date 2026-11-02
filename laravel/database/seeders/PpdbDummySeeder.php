<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\AdmissionPath;
use App\Models\AdmissionPeriod;
use App\Models\Announcement;
use App\Models\AppNotification;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Guardian;
use App\Models\Major;
use App\Models\Payment;
use App\Models\PeriodMajorQuota;
use App\Models\Role;
use App\Models\SelectionResult;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Data dummy REALISTIS untuk testing lokal.
 *
 * Isi: tahun ajaran + 2 periode, 4 jurusan, akun panitia per role,
 * 1 akun siswa tetap untuk login cepat, dan 30 pendaftar acak yang
 * statusnya tersebar ke SEMUA tahap alur (draft s/d daftar ulang),
 * lengkap dengan wali, dokumen, pembayaran, dan nilai seleksi.
 *
 * Jalankan:  php artisan db:seed --class=PpdbDummySeeder
 *
 * PERINGATAN: seeder ini menolak berjalan di luar environment
 * local/testing, dan jangan pernah memakainya di production.
 */
class PpdbDummySeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            $this->command?->warn('PpdbDummySeeder hanya untuk local/testing. Dibatalkan.');
            return;
        }

        // Pastikan master data (role, jalur, dokumen, syarat, admin) sudah ada
        $this->call(PpdbMasterSeeder::class);

        $this->seedStaffAccounts();
        [$periodOpen, $periodNext] = $this->seedPeriodsAndMajors();
        $this->seedFixedStudent($periodOpen);
        $this->seedRandomApplicants($periodOpen, 30);
        $this->seedAnnouncements($periodOpen);

        $this->command?->info('Data dummy PPDB selesai dibuat. Akun uji (password semua: "password"):');
        $this->command?->info('  admin@sekolah.sch.id | verifikator@sekolah.sch.id | keuangan@sekolah.sch.id | kepsek@sekolah.sch.id | guru@sekolah.sch.id | siswa@contoh.id');
    }

    private function seedStaffAccounts(): void
    {
        $staff = [
            ['code' => 'verifikator', 'name' => 'Vera Verifikator', 'email' => 'verifikator@sekolah.sch.id', 'role_name' => 'Verifikator Berkas'],
            ['code' => 'finance', 'name' => 'Fina Keuangan', 'email' => 'keuangan@sekolah.sch.id', 'role_name' => 'Keuangan'],
            ['code' => 'kepala_sekolah', 'name' => 'Kepala Sekolah', 'email' => 'kepsek@sekolah.sch.id', 'role_name' => 'Kepala Sekolah'],
            ['code' => 'guru', 'name' => 'Guru Penilai', 'email' => 'guru@sekolah.sch.id', 'role_name' => 'Guru / Penilai Seleksi'],
        ];

        foreach ($staff as $person) {
            $role = Role::firstOrCreate(['code' => $person['code']], ['name' => $person['role_name']]);

            User::updateOrCreate(
                ['email' => $person['email']],
                [
                    'role_id' => $role->id,
                    'name' => $person['name'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );
        }
    }

    /** @return array{0: AdmissionPeriod, 1: AdmissionPeriod} */
    private function seedPeriodsAndMajors(): array
    {
        AcademicYear::query()->update(['is_active' => false]);
        $year = AcademicYear::updateOrCreate(['year' => '2027/2028'], ['is_active' => true]);

        $majors = collect([
            ['code' => 'UMUM', 'name' => 'Umum', 'quota' => 72],
            ['code' => 'IPA', 'name' => 'IPA / MIPA', 'quota' => 72],
            ['code' => 'IPS', 'name' => 'IPS / IIS', 'quota' => 72],
            ['code' => 'BHS', 'name' => 'Bahasa', 'quota' => 36],
        ])->map(fn ($m) => Major::updateOrCreate(['code' => $m['code']], $m));

        $open = AdmissionPeriod::updateOrCreate(
            ['academic_year_id' => $year->id, 'name' => 'Gelombang 1'],
            ['start_date' => now()->subDays(7)->format('Y-m-d'), 'end_date' => now()->addDays(30)->format('Y-m-d'), 'status' => 'open']
        );

        $next = AdmissionPeriod::updateOrCreate(
            ['academic_year_id' => $year->id, 'name' => 'Gelombang 2'],
            ['start_date' => now()->addDays(45)->format('Y-m-d'), 'end_date' => now()->addDays(75)->format('Y-m-d'), 'status' => 'draft']
        );

        foreach ($majors as $major) {
            PeriodMajorQuota::updateOrCreate(
                ['period_id' => $open->id, 'major_id' => $major->id],
                ['quota' => 36]
            );
        }

        return [$open, $next];
    }

    /** Akun siswa tetap agar bisa login & cek status tanpa menebak data acak. */
    private function seedFixedStudent(AdmissionPeriod $period): void
    {
        $role = Role::where('code', 'applicant')->firstOrFail();

        $user = User::updateOrCreate(
            ['email' => 'siswa@contoh.id'],
            [
                'role_id' => $role->id,
                'name' => 'Budi Pratama',
                'phone' => '081200000001',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $applicant = Applicant::updateOrCreate(
            ['user_id' => $user->id],
            [
                'full_name' => 'Budi Pratama',
                'nik' => '3201000000000001',
                'nisn' => '0012345678',
                'gender' => 'L',
                'birth_place' => 'Tangerang',
                'birth_date' => '2012-05-17',
                'religion' => 'Islam',
                'address' => 'Jl. Contoh No. 1',
                'city' => 'Tangerang Selatan',
                'province' => 'Banten',
                'phone' => '081200000001',
                'previous_school' => 'SMPN 1 Tangerang Selatan',
            ]
        );

        Guardian::updateOrCreate(
            ['applicant_id' => $applicant->id, 'relationship' => 'ayah'],
            ['name' => 'Andi Pratama', 'occupation' => 'Wiraswasta', 'phone' => '081200000002', 'is_emergency_contact' => true]
        );

        $path = AdmissionPath::where('code', 'reguler')->firstOrFail();
        $major = Major::where('code', 'IPA')->firstOrFail();

        // Status submitted + dokumen pending: siap dipakai menguji verifikasi admin
        $application = Application::updateOrCreate(
            ['registration_no' => 'PPDB-2027-0001'],
            [
                'applicant_id' => $applicant->id,
                'period_id' => $period->id,
                'path_id' => $path->id,
                'major_id' => $major->id,
                'status' => 'submitted',
                'submitted_at' => now()->subDay(),
            ]
        );

        foreach ($path->documentTypes()->wherePivot('is_required', true)->get() as $docType) {
            ApplicationDocument::factory()->create([
                'application_id' => $application->id,
                'document_type_id' => $docType->id,
                'original_filename' => strtolower($docType->code) . '-budi.pdf',
            ]);
        }
    }

    private function seedRandomApplicants(AdmissionPeriod $period, int $count): void
    {
        // Pola status diulang agar semua tahap alur terwakili merata
        $pattern = [
            'draft', 'submitted', 'submitted', 'document_review', 'document_review',
            'rejected_document', 'verified', 'selection', 'selection',
            'accepted', 'accepted', 'accepted', 'waitlisted', 'rejected', 'registered',
        ];

        $paths = AdmissionPath::all();
        $majors = Major::all();
        $verifikator = User::where('email', 'verifikator@sekolah.sch.id')->first();

        for ($i = 0; $i < $count; $i++) {
            $status = $pattern[$i % count($pattern)];
            $path = $paths->random();
            $major = $majors->random();

            $applicant = Applicant::factory()->create();
            Guardian::factory()->father()->create(['applicant_id' => $applicant->id]);
            Guardian::factory()->mother()->create(['applicant_id' => $applicant->id]);

            $application = Application::create([
                'applicant_id' => $applicant->id,
                'period_id' => $period->id,
                'path_id' => $path->id,
                'major_id' => $major->id,
                'major_choice_2_id' => $majors->where('id', '!=', $major->id)->random()->id,
                'registration_no' => sprintf('PPDB-2027-%04d', 100 + $i),
                'status' => $status,
                'submitted_at' => $status === 'draft' ? null : now()->subDays(rand(0, 6)),
                'verified_at' => in_array($status, ['verified', 'selection', 'accepted', 'waitlisted', 'rejected', 'registered'], true)
                    ? now()->subDays(rand(0, 3)) : null,
                'verified_by' => in_array($status, ['verified', 'selection', 'accepted', 'waitlisted', 'rejected', 'registered'], true)
                    ? $verifikator?->id : null,
                'notes' => $status === 'rejected_document' ? 'Ada dokumen yang ditolak, mohon perbaiki dan kirim ulang.' : null,
            ]);

            $this->seedDocuments($application, $path, $status, $verifikator?->id);
            $this->seedPayments($application, $status);
            $this->seedSelection($application, $status, $verifikator?->id);
        }
    }

    private function seedDocuments(Application $application, AdmissionPath $path, string $status, ?int $verifierId): void
    {
        $required = $path->documentTypes()->wherePivot('is_required', true)->get();

        foreach ($required as $index => $docType) {
            // Draft: baru sebagian dokumen yang diunggah
            if ($status === 'draft' && $index >= 2) {
                break;
            }

            $factory = ApplicationDocument::factory();

            if (in_array($status, ['verified', 'selection', 'accepted', 'waitlisted', 'rejected', 'registered'], true)) {
                $factory = $factory->verified();
            } elseif ($status === 'rejected_document' && $index === 0) {
                $factory = $factory->rejected();
            }

            $sudahDinilai = in_array($status, ['verified', 'selection', 'accepted', 'waitlisted', 'rejected', 'registered', 'rejected_document'], true);

            $factory->create([
                'application_id' => $application->id,
                'document_type_id' => $docType->id,
                'verified_by' => $sudahDinilai ? $verifierId : null,
            ]);
        }
    }

    private function seedPayments(Application $application, string $status): void
    {
        if ($status === 'draft') {
            return;
        }

        // Biaya formulir: yang sudah jauh alurnya dianggap lunas,
        // yang baru terkirim masih menunggu verifikasi
        if (in_array($status, ['verified', 'selection', 'accepted', 'waitlisted', 'rejected', 'registered'], true)) {
            Payment::factory()->formulir()->paid()->create(['application_id' => $application->id]);
        } else {
            Payment::factory()->formulir()->waitingVerification()->create(['application_id' => $application->id]);
        }

        if ($status === 'registered') {
            Payment::factory()->daftarUlang()->paid()->create(['application_id' => $application->id]);
        }

        if ($status === 'accepted') {
            Payment::factory()->daftarUlang()->create(['application_id' => $application->id]); // menunggu dibayar
        }
    }

    private function seedSelection(Application $application, string $status, ?int $assessorId): void
    {
        if (! in_array($status, ['selection', 'accepted', 'waitlisted', 'rejected', 'registered'], true)) {
            return;
        }

        $factory = match ($status) {
            'rejected' => SelectionResult::factory()->failed(),
            'selection' => SelectionResult::factory(),
            default => SelectionResult::factory()->passed(),
        };

        $factory->count(1)->create([
            'application_id' => $application->id,
            'stage' => 'tes_tulis',
            'assessed_by' => $assessorId,
        ]);

        SelectionResult::factory()->create([
            'application_id' => $application->id,
            'stage' => 'wawancara',
            'subject' => null,
            'result_status' => $status === 'rejected' ? 'failed' : ($status === 'selection' ? 'pending' : 'passed'),
            'assessed_by' => $assessorId,
        ]);

        if (in_array($status, ['accepted', 'waitlisted', 'rejected', 'registered'], true)) {
            AppNotification::create([
                'user_id' => $application->applicant->user_id,
                'title' => 'Pengumuman Hasil Seleksi',
                'message' => match ($status) {
                    'accepted', 'registered' => 'Selamat, kamu dinyatakan DITERIMA.',
                    'waitlisted' => 'Kamu masuk daftar CADANGAN.',
                    default => 'Mohon maaf, kamu belum diterima pada periode ini.',
                },
                'channel' => 'app',
                'sent_at' => now()->subDay(),
            ]);
        }
    }

    private function seedAnnouncements(AdmissionPeriod $period): void
    {
        $items = [
            ['title' => 'PPDB Gelombang 1 Resmi Dibuka', 'content' => "Pendaftaran Gelombang 1 sudah dibuka. Silakan membuat akun, melengkapi biodata, dan mengunggah dokumen sesuai jalur yang dipilih."],
            ['title' => 'Jadwal Tes Seleksi', 'content' => 'Tes tulis dan wawancara dilaksanakan sesuai jadwal yang tertera pada kartu peserta. Peserta wajib membawa bukti pendaftaran.'],
            ['title' => 'Informasi Daftar Ulang', 'content' => 'Peserta yang dinyatakan diterima wajib melakukan daftar ulang dan menyelesaikan pembayaran sebelum batas waktu yang ditentukan.'],
        ];

        foreach ($items as $item) {
            Announcement::updateOrCreate(
                ['title' => $item['title']],
                [...$item, 'period_id' => $period->id, 'publish_at' => now()->subDays(2), 'is_published' => true]
            );
        }
    }
}
