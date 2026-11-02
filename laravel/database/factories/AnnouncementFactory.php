<?php

namespace Database\Factories;

use App\Models\AdmissionPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement> */
class AnnouncementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'period_id' => AdmissionPeriod::factory(),
            'title' => fake()->randomElement([
                'PPDB Gelombang 1 Resmi Dibuka',
                'Jadwal Tes Seleksi Masuk',
                'Pengumuman Hasil Seleksi',
                'Batas Akhir Pengumpulan Dokumen',
                'Informasi Biaya Daftar Ulang',
            ]),
            'content' => fake()->paragraphs(2, true),
            'publish_at' => now()->subDays(fake()->numberBetween(0, 10)),
            'is_published' => true,
            'created_by' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['is_published' => false, 'publish_at' => null]);
    }
}
