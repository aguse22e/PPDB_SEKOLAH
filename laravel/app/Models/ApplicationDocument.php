<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationDocument extends Model
{
    use HasFactory;
    protected $fillable = [
        'application_id',
        'document_type_id',
        'file_path',
        'original_filename',
        'file_size_kb',
        'verification_status',
        'verified_by',
        'verified_at',
        'notes',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'uploaded_at' => 'datetime',
        ];
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

}
