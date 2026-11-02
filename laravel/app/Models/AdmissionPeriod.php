<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionPeriod extends Model
{
    use HasFactory;
    protected $fillable = [
        'academic_year_id',
        'name',
        'start_date',
        'end_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'period_id');
    }

    public function majorQuotas()
    {
        return $this->hasMany(PeriodMajorQuota::class, 'period_id');
    }

    public function majors()
    {
        return $this->belongsToMany(Major::class, 'period_major_quotas', 'period_id', 'major_id')->withPivot('quota')->withTimestamps();
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'period_id');
    }

}
