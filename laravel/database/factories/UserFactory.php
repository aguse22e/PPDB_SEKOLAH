<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Menggantikan UserFactory bawaan Laravel: user PPDB wajib punya role.
 * Password semua user factory adalah "password" (khusus lokal/testing).
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'role_id' => fn () => Role::firstOrCreate(
                ['code' => 'applicant'],
                ['name' => 'Calon Siswa']
            )->id,
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '08' . fake()->numerify('##########'),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function role(string $code, string $name): static
    {
        return $this->state(fn () => [
            'role_id' => Role::firstOrCreate(['code' => $code], ['name' => $name])->id,
        ]);
    }

    public function admin(): static
    {
        return $this->role('admin', 'Admin / Panitia');
    }

    public function verifikator(): static
    {
        return $this->role('verifikator', 'Verifikator Berkas');
    }

    public function finance(): static
    {
        return $this->role('finance', 'Keuangan');
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}
