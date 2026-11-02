<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Applicant> */
class ApplicantFactory extends Factory
{
    public function definition(): array
    {
        $gender = fake()->randomElement(['L', 'P']);

        return [
            'user_id' => User::factory(),
            'full_name' => fake()->name($gender === 'L' ? 'male' : 'female'),
            'nik' => fake()->unique()->numerify('################'),
            'nisn' => fake()->unique()->numerify('##########'),
            'gender' => $gender,
            'birth_place' => fake()->city(),
            // Calon siswa umumnya berumur 11-16 tahun
            'birth_date' => fake()->dateTimeBetween('-16 years', '-11 years')->format('Y-m-d'),
            'religion' => fake()->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']),
            'address' => fake()->streetAddress(),
            'rt_rw' => fake()->numerify('00#/00#'),
            'village' => fake()->word() . ' ' . fake()->word(),
            'district' => fake()->citySuffix() . ' ' . fake()->word(),
            'city' => fake()->city(),
            'province' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'phone' => '08' . fake()->numerify('##########'),
            'previous_school' => fake()->randomElement(['SMPN', 'SMP', 'MTsN', 'MTs']) . ' ' .
                fake()->numberBetween(1, 30) . ' ' . fake()->city(),
            'previous_school_address' => fake()->streetAddress(),
            'photo_path' => null,
        ];
    }
}
