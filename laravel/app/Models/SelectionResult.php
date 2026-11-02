<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectionResult extends Model
{
    use HasFactory;
    protected $fillable = [
        'application_id',
        'stage',
        'subject',
        'score',
        'max_score',
        'rank',
        'result_status',
        'notes',
        'assessed_by',
        'assessed_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'max_score' => 'decimal:2',
            'assessed_at' => 'datetime',
        ];
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function assessor()
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

}
