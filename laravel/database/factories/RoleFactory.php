<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role> */
class RoleFactory extends Factory
{
    public function definition(): array
    {
        $role = fake()->unique()->randomElement([
            ['code' => 'applicant', 'name' => 'Calon Siswa'],
            ['code' => 'admin', 'name' => 'Admin / Panitia'],
            ['code' => 'verifikator', 'name' => 'Verifikator Berkas'],
            ['code' => 'finance', 'name' => 'Keuangan'],
            ['code' => 'kepala_sekolah', 'name' => 'Kepala Sekolah'],
        ]);

        return $role;
    }

    public function code(string $code, string $name): static
    {
        return $this->state(fn () => ['code' => $code, 'name' => $name]);
    }
}
