<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    use HasFactory;
    protected $fillable = [
        'applicant_id',
        'relationship',
        'name',
        'nik',
        'birth_date',
        'education',
        'occupation',
        'income_range',
        'phone',
        'email',
        'address',
        'is_emergency_contact',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'is_emergency_contact' => 'boolean',
        ];
    }

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

}
