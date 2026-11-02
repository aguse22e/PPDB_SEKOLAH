<?php

namespace Database\Seeders;

use App\Models\AdmissionPath;
use App\Models\DocumentType;
use App\Models\PathDocumentRequirement;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PpdbMasterSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $roles = [
            ['code' => 'applicant', 'name' => 'Calon Siswa'],
            ['code' => 'admin', 'name' => 'Admin / Panitia'],
            ['code' => 'verifikator', 'name' => 'Verifikator Berkas'],
            ['code' => 'finance', 'name' => 'Keuangan'],
            ['code' => 'kepala_sekolah', 'name' => 'Kepala Sekolah'],
            ['code' => 'guru', 'name' => 'Guru / Penilai Seleksi'],
        ];
        foreach ($roles as $role) {
            Role::updateOrCreate(['code' => $role['code']], $role);
        }

        // Jalur pendaftaran
        $paths = [
            ['code' => 'reguler', 'name' => 'Reguler'],
            ['code' => 'prestasi', 'name' => 'Prestasi'],
            ['code' => 'afirmasi', 'name' => 'Afirmasi'],
            ['code' => 'zonasi', 'name' => 'Zonasi'],
            ['code' => 'mutasi', 'name' => 'Mutasi Orang Tua'],
        ];
        foreach ($paths as $path) {
            AdmissionPath::updateOrCreate(['code' => $path['code']], $path);
        }

        // Jenis dokumen
        $docs = [
            ['code' => 'kk', 'name' => 'Kartu Keluarga'],
            ['code' => 'akta', 'name' => 'Akta Kelahiran'],
            ['code' => 'ijazah', 'name' => 'Ijazah / SKL'],
            ['code' => 'rapor', 'name' => 'Rapor Terakhir'],
            ['code' => 'foto', 'name' => 'Pas Foto'],
            ['code' => 'kip', 'name' => 'Kartu Indonesia Pintar (jika ada)'],
            ['code' => 'sertifikat', 'name' => 'Sertifikat Prestasi (jalur prestasi)'],
        ];
        foreach ($docs as $doc) {
            DocumentType::updateOrCreate(['code' => $doc['code']], $doc);
        }

        // Syarat dokumen: semua jalur wajib KK, Akta, Ijazah, Rapor, Foto
        $wajibUmum = ['kk', 'akta', 'ijazah', 'rapor', 'foto'];
        $tambahan = [
            'prestasi' => ['sertifikat'],
            'afirmasi' => ['kip'],
        ];
        foreach (AdmissionPath::all() as $path) {
            $codes = array_merge($wajibUmum, $tambahan[$path->code] ?? []);
            foreach ($codes as $code) {
                $doc = DocumentType::where('code', $code)->first();
                if ($doc) {
                    PathDocumentRequirement::updateOrCreate(
                        ['path_id' => $path->id, 'document_type_id' => $doc->id],
                        ['is_required' => true]
                    );
                }
            }
        }

        // Pengaturan awal
        $settings = [
            'nama_sekolah' => '',
            'biaya_formulir' => '0',
            'biaya_daftar_ulang' => '0',
        ];
        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Akun admin awal - GANTI password ini setelah login pertama!
        $adminRole = Role::where('code', 'admin')->first();
        User::updateOrCreate(
            ['email' => 'admin@sekolah.sch.id'],
            [
                'role_id' => $adminRole->id,
                'name' => 'Admin PPDB',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
    }
}
