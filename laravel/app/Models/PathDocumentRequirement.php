<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PathDocumentRequirement extends Model
{
    use HasFactory;
    protected $fillable = [
        'path_id',
        'document_type_id',
        'is_required',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
        ];
    }

    public function path()
    {
        return $this->belongsTo(AdmissionPath::class, 'path_id');
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

}
