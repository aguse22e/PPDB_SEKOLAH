<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'allowed_extensions',
        'max_size_mb',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function applicationDocuments()
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function paths()
    {
        return $this->belongsToMany(AdmissionPath::class, 'path_document_requirements', 'document_type_id', 'path_id')->withPivot('is_required')->withTimestamps();
    }

}
