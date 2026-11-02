<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionPath extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'description',
        'quota',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'path_id');
    }

    public function documentTypes()
    {
        return $this->belongsToMany(DocumentType::class, 'path_document_requirements', 'path_id', 'document_type_id')->withPivot('is_required')->withTimestamps();
    }

}
