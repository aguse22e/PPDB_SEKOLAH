<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'full_name',
        'nik',
        'nisn',
        'gender',
        'birth_place',
        'birth_date',
        'religion',
        'address',
        'rt_rw',
        'village',
        'district',
        'city',
        'province',
        'postal_code',
        'phone',
        'barcode_code',
        'previous_school',
        'previous_school_address',
        'photo_path',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        // Kode barcode siswa dibuat otomatis saat akun siswa dibuat: SIS + 6 digit id.
        static::created(function (Applicant $applicant) {
            if (empty($applicant->barcode_code)) {
                $applicant->updateQuietly([
                    'barcode_code' => 'SIS'.str_pad((string) $applicant->id, 6, '0', STR_PAD_LEFT),
                ]);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function guardians()
    {
        return $this->hasMany(Guardian::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

}
