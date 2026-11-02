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
