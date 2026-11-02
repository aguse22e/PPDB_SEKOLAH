<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\DocumentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Catatan: file_path menunjuk berkas contoh "documents/dummy/contoh.pdf"
 * yang tidak benar-benar ada di storage. Cukup untuk menguji tampilan,
 * daftar dokumen, dan alur verifikasi — bukan untuk menguji unduhan file.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApplicationDocument>
 */
class ApplicationDocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            'document_type_id' => DocumentType::factory(),
            'file_path' => 'documents/dummy/contoh.pdf',
            'original_filename' => 'dokumen-' . fake()->word() . '.pdf',
            'file_size_kb' => fake()->numberBetween(80, 1900),
            'verification_status' => 'pending',
            'verified_by' => null,
            'verified_at' => null,
            'notes' => null,
            'uploaded_at' => now()->subDays(fake()->numberBetween(0, 5)),
        ];
    }

    public function verified(): static
    {
        return $this->state(fn () => [
            'verification_status' => 'verified',
            'verified_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'verification_status' => 'rejected',
            'verified_at' => now(),
            'notes' => fake()->randomElement([
                'Hasil pindai buram, mohon unggah ulang.',
                'Dokumen tidak terbaca, gunakan foto yang lebih terang.',
                'Dokumen yang diunggah tidak sesuai.',
            ]),
        ]);
    }
}
