<?php

namespace Database\Factories;

use App\Models\AdmissionPath;
use App\Models\AdmissionPeriod;
use App\Models\Applicant;
use App\Models\Major;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application> */
class ApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'applicant_id' => Applicant::factory(),
            'period_id' => AdmissionPeriod::factory(),
            'path_id' => AdmissionPath::factory(),
            'major_id' => Major::factory(),
            'major_choice_2_id' => null,
            'registration_no' => 'PPDB-' . fake()->year() . '-' . fake()->unique()->numerify('####'),
            'status' => 'draft',
            'notes' => null,
            'submitted_at' => null,
            'verified_at' => null,
            'verified_by' => null,
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn () => [
            'status' => 'submitted',
            'submitted_at' => now()->subDays(fake()->numberBetween(0, 5)),
        ]);
    }

    public function inSelection(): static
    {
        return $this->state(fn () => [
            'status' => 'selection',
            'submitted_at' => now()->subDays(fake()->numberBetween(5, 10)),
            'verified_at' => now()->subDays(fake()->numberBetween(1, 4)),
        ]);
    }

    public function accepted(): static
    {
        return $this->inSelection()->state(fn () => ['status' => 'accepted']);
    }

    public function waitlisted(): static
    {
        return $this->inSelection()->state(fn () => ['status' => 'waitlisted']);
    }

    public function rejected(): static
    {
        return $this->inSelection()->state(fn () => ['status' => 'rejected']);
    }

    public function registered(): static
    {
        return $this->inSelection()->state(fn () => ['status' => 'registered']);
    }
}
