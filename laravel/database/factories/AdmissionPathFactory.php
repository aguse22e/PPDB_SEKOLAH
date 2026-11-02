<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdmissionPath> */
class AdmissionPathFactory extends Factory
{
    public function definition(): array
    {
        $path = fake()->unique()->randomElement([
            ['code' => 'reguler', 'name' => 'Reguler'],
            ['code' => 'prestasi', 'name' => 'Prestasi'],
            ['code' => 'afirmasi', 'name' => 'Afirmasi'],
            ['code' => 'zonasi', 'name' => 'Zonasi'],
            ['code' => 'mutasi', 'name' => 'Mutasi Orang Tua'],
        ]);

        return [
            ...$path,
            'description' => fake()->sentence(),
            'quota' => fake()->numberBetween(20, 100),
            'is_active' => true,
        ];
    }
}
