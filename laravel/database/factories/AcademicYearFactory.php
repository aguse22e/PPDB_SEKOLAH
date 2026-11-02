<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AcademicYear> */
class AcademicYearFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->unique()->numberBetween(2025, 2032);

        return [
            'year' => $start . '/' . ($start + 1),
            'is_active' => false,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['is_active' => true]);
    }
}
