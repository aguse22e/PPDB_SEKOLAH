<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;
    protected $fillable = [
        'period_id',
        'title',
        'content',
        'publish_at',
        'is_published',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'publish_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    public function period()
    {
        return $this->belongsTo(AdmissionPeriod::class, 'period_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
