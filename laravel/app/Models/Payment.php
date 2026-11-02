<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'application_id',
        'payment_type',
        'invoice_no',
        'amount',
        'method',
        'reference_no',
        'proof_path',
        'status',
        'paid_at',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

}
