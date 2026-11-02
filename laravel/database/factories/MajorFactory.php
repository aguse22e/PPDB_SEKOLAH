<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Major> */
class MajorFactory extends Factory
{
    public function definition(): array
    {
        $major = fake()->unique()->randomElement([
            ['code' => 'UMUM', 'name' => 'Umum'],
            ['code' => 'IPA', 'name' => 'IPA / MIPA'],
            ['code' => 'IPS', 'name' => 'IPS / IIS'],
            ['code' => 'BHS', 'name' => 'Bahasa'],
            ['code' => 'TKJ', 'name' => 'Teknik Komputer dan Jaringan'],
            ['code' => 'RPL', 'name' => 'Rekayasa Perangkat Lunak'],
            ['code' => 'AKL', 'name' => 'Akuntansi dan Keuangan Lembaga'],
            ['code' => 'OTKP', 'name' => 'Otomatisasi Tata Kelola Perkantoran'],
        ]);

        return [
            ...$major,
            'quota' => fake()->numberBetween(30, 72),
            'is_active' => true,
        ];
    }
}
