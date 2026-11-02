<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodMajorQuota extends Model
{
    use HasFactory;
    protected $fillable = [
        'period_id',
        'major_id',
        'quota',
    ];

    public function period()
    {
        return $this->belongsTo(AdmissionPeriod::class, 'period_id');
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

}
