<?php

namespace Database\Factories;

use App\Models\Applicant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guardian> */
class GuardianFactory extends Factory
{
    public function definition(): array
    {
        $relationship = fake()->randomElement(['ayah', 'ibu', 'wali']);

        return [
            'applicant_id' => Applicant::factory(),
            'relationship' => $relationship,
            'name' => fake()->name($relationship === 'ibu' ? 'female' : 'male'),
            'nik' => fake()->numerify('################'),
            'birth_date' => fake()->dateTimeBetween('-55 years', '-30 years')->format('Y-m-d'),
            'education' => fake()->randomElement(['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2']),
            'occupation' => fake()->jobTitle(),
            'income_range' => fake()->randomElement(['<1jt', '1-3jt', '3-5jt', '>5jt']),
            'phone' => '08' . fake()->numerify('##########'),
            'email' => fake()->safeEmail(),
            'address' => null, // sama dengan alamat siswa
            'is_emergency_contact' => $relationship !== 'wali',
        ];
    }

    public function father(): static
    {
        return $this->state(fn () => ['relationship' => 'ayah', 'name' => fake()->name('male')]);
    }

    public function mother(): static
    {
        return $this->state(fn () => ['relationship' => 'ibu', 'name' => fake()->name('female')]);
    }
}
