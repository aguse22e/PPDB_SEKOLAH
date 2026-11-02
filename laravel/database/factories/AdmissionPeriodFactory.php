<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdmissionPeriod> */
class AdmissionPeriodFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-1 month', '+2 months');

        return [
            'academic_year_id' => AcademicYear::factory(),
            'name' => fake()->randomElement(['Gelombang 1', 'Gelombang 2', 'Gelombang 3']),
            'start_date' => $start->format('Y-m-d'),
            'end_date' => (clone $start)->modify('+30 days')->format('Y-m-d'),
            'status' => 'draft',
        ];
    }

    public function open(): static
    {
        return $this->state(fn () => [
            'status' => 'open',
            'start_date' => now()->subDays(7)->format('Y-m-d'),
            'end_date' => now()->addDays(30)->format('Y-m-d'),
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'status' => 'closed',
            'start_date' => now()->subDays(60)->format('Y-m-d'),
            'end_date' => now()->subDays(30)->format('Y-m-d'),
        ]);
    }
}
