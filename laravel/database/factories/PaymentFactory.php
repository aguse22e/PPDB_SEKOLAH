<?php

namespace Database\Factories;

use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment> */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(['formulir', 'tes', 'daftar_ulang', 'lainnya']);

        return [
            'application_id' => Application::factory(),
            'payment_type' => $type,
            'invoice_no' => 'INV-' . fake()->year() . '-' . fake()->unique()->numerify('####'),
            'amount' => match ($type) {
                'formulir' => 150000,
                'tes' => 100000,
                'daftar_ulang' => fake()->randomElement([1000000, 1500000, 2000000]),
                default => fake()->numberBetween(50, 500) * 1000,
            },
            'method' => null,
            'reference_no' => null,
            'proof_path' => null,
            'status' => 'pending',
            'paid_at' => null,
            'verified_by' => null,
            'verified_at' => null,
            'notes' => null,
        ];
    }

    public function formulir(): static
    {
        return $this->state(fn () => ['payment_type' => 'formulir', 'amount' => 150000]);
    }

    public function daftarUlang(): static
    {
        return $this->state(fn () => ['payment_type' => 'daftar_ulang', 'amount' => 1500000]);
    }

    public function waitingVerification(): static
    {
        return $this->state(fn () => [
            'status' => 'waiting_verification',
            'method' => fake()->randomElement(['transfer', 'qris', 'va']),
            'reference_no' => fake()->numerify('REF##########'),
            'proof_path' => 'payment-proofs/dummy/bukti.jpg',
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'status' => 'paid',
            'method' => fake()->randomElement(['transfer', 'qris', 'va', 'tunai']),
            'reference_no' => fake()->numerify('REF##########'),
            'proof_path' => 'payment-proofs/dummy/bukti.jpg',
            'paid_at' => now()->subDays(fake()->numberBetween(0, 3)),
            'verified_at' => now(),
        ]);
    }
}
