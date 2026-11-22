<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;
    protected $fillable = [
        'applicant_id',
        'period_id',
        'path_id',
        'major_id',
        'major_choice_2_id',
        'registration_no',
        'status',
        'notes',
        'submitted_at',
        'verified_at',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function period()
    {
        return $this->belongsTo(AdmissionPeriod::class, 'period_id');
    }

    public function path()
    {
        return $this->belongsTo(AdmissionPath::class, 'path_id');
    }

    public function major()
    {
        return $this->belongsTo(Major::class, 'major_id');
    }

    public function majorChoice2()
    {
        return $this->belongsTo(Major::class, 'major_choice_2_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function documents()
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function selectionResults()
    {
        return $this->hasMany(SelectionResult::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Alur pendaftaran (wizard calon siswa)
    |--------------------------------------------------------------------------
    | Urutan langkah yang harus dilewati sebelum pendaftaran bisa dikirim,
    | mengikuti alur PPDB yang umum: biodata -> data wali -> dokumen wajib
    | -> kirim. Dipakai ApplicationController@submit sebagai pengaman dan
    | bisa dipakai dashboard untuk menampilkan progres langkah.
    */

    public function isBiodataComplete(): bool
    {
        $applicant = $this->applicant;

        return $applicant !== null
            && filled($applicant->full_name)
            && filled($applicant->gender);
    }

    public function hasGuardian(): bool
    {
        return $this->applicant !== null
            && $this->applicant->guardians()->exists();
    }

    public function missingRequiredDocumentTypeIds()
    {
        $requiredIds = $this->path->documentTypes()
            ->wherePivot('is_required', true)
            ->pluck('document_types.id');

        $uploadedIds = $this->documents()->pluck('document_type_id');

        return $requiredIds->diff($uploadedIds);
    }

    public function flowSteps(): array
    {
        return [
            ['key' => 'biodata', 'label' => 'Biodata calon siswa', 'done' => $this->isBiodataComplete()],
            ['key' => 'guardian', 'label' => 'Data orang tua / wali', 'done' => $this->hasGuardian()],
            ['key' => 'documents', 'label' => 'Dokumen wajib', 'done' => $this->missingRequiredDocumentTypeIds()->isEmpty()],
            ['key' => 'submit', 'label' => 'Kirim pendaftaran', 'done' => $this->status !== 'draft'],
        ];
    }

}
