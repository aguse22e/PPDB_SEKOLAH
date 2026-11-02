<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocumentType> */
class DocumentTypeFactory extends Factory
{
    public function definition(): array
    {
        $doc = fake()->unique()->randomElement([
            ['code' => 'kk', 'name' => 'Kartu Keluarga'],
            ['code' => 'akta', 'name' => 'Akta Kelahiran'],
            ['code' => 'ijazah', 'name' => 'Ijazah / SKL'],
            ['code' => 'rapor', 'name' => 'Rapor Terakhir'],
            ['code' => 'foto', 'name' => 'Pas Foto'],
            ['code' => 'kip', 'name' => 'Kartu Indonesia Pintar'],
            ['code' => 'sertifikat', 'name' => 'Sertifikat Prestasi'],
        ]);

        return [
            ...$doc,
            'allowed_extensions' => 'pdf,jpg,jpeg,png',
            'max_size_mb' => 2,
            'is_active' => true,
        ];
    }
}
