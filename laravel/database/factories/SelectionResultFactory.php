<?php

namespace Database\Factories;

use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SelectionResult> */
class SelectionResultFactory extends Factory
{
    public function definition(): array
    {
        $stage = fake()->randomElement(['tes_tulis', 'wawancara', 'seleksi_berkas', 'prestasi']);
        $score = fake()->numberBetween(45, 98);

        return [
            'application_id' => Application::factory(),
            'stage' => $stage,
            'subject' => $stage === 'tes_tulis'
                ? fake()->randomElement(['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'IPA'])
                : null,
            'score' => $score,
            'max_score' => 100,
            'rank' => null,
            'result_status' => $score >= 70 ? 'passed' : 'failed',
            'notes' => null,
            'assessed_by' => null,
            'assessed_at' => now()->subDays(fake()->numberBetween(0, 2)),
        ];
    }

    public function passed(): static
    {
        return $this->state(fn () => [
            'score' => fake()->numberBetween(70, 98),
            'result_status' => 'passed',
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'score' => fake()->numberBetween(30, 64),
            'result_status' => 'failed',
        ]);
    }
}
