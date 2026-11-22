<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendee_type',
        'attendee_id',
        'date',
        'check_in_at',
        'check_out_at',
        'status',
        'method',
        'scanned_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
        ];
    }

    public const STATUSES = ['hadir', 'terlambat', 'izin', 'sakit', 'alpa'];

    public function attendee()
    {
        return $this->morphTo();
    }

    public function scanner()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    public function attendeeName(): string
    {
        return $this->attendee?->full_name ?? $this->attendee?->name ?? '-';
    }

    public function attendeeLabel(): string
    {
        return $this->attendee_type === Applicant::class ? 'Siswa' : 'Guru';
    }

    /** Jam masuk sekolah (HH:MM). Diatur lewat Setting key `attendance.start_time`, default 07:00. */
    public static function startTime(): string
    {
        return Setting::where('key', 'attendance.start_time')->value('value') ?: '07:00';
    }

    /** Cari pemilik kode barcode: siswa dulu, lalu guru. */
    public static function findByBarcode(string $code): ?Model
    {
        $code = trim($code);

        return Applicant::where('barcode_code', $code)->first()
            ?? User::where('barcode_code', $code)->first();
    }

    /**
     * Catat satu scan barcode.
     * Scan pertama hari itu = absen masuk, scan kedua = absen pulang,
     * scan ketiga dan seterusnya = sudah lengkap hari itu.
     */
    public static function recordScan(string $code, ?User $scanner, ?Carbon $now = null): array
    {
        $now ??= now();
        $person = static::findByBarcode($code);

        if (! $person) {
            return ['ok' => false, 'message' => 'Kode barcode tidak ditemukan. Periksa kartu atau ketik ulang kodenya.'];
        }

        $attendance = static::firstOrCreate([
            'attendee_type' => $person::class,
            'attendee_id' => $person->id,
            'date' => $now->toDateString(),
        ], [
            'status' => 'hadir',
            'method' => 'barcode',
            'scanned_by' => $scanner?->id,
        ]);

        $name = $person->full_name ?? $person->name;
        $label = $person instanceof Applicant ? 'Siswa' : 'Guru';

        // Sudah dicatat manual sebagai izin/sakit/alpa hari ini: jangan ditimpa scan.
        if (! $attendance->wasRecentlyCreated && $attendance->method === 'manual'
            && in_array($attendance->status, ['izin', 'sakit', 'alpa'], true)) {
            return [
                'ok' => false,
                'kind' => 'manual',
                'message' => $label.' '.$name.' sudah dicatat manual hari ini ('.$attendance->status.').',
                'attendance' => $attendance,
                'person' => $person,
            ];
        }

        if (! $attendance->check_in_at) {
            $late = $now->format('H:i') > static::startTime();
            $attendance->update([
                'check_in_at' => $now,
                'status' => $late ? 'terlambat' : 'hadir',
                'scanned_by' => $scanner?->id,
            ]);

            return [
                'ok' => true,
                'kind' => 'check_in',
                'message' => $label.' '.$name.' berhasil absen MASUK pukul '.$now->format('H:i').($late ? ' (terlambat)' : '').'.',
                'attendance' => $attendance->fresh(),
                'person' => $person,
            ];
        }

        if (! $attendance->check_out_at) {
            $attendance->update(['check_out_at' => $now]);

            return [
                'ok' => true,
                'kind' => 'check_out',
                'message' => $label.' '.$name.' berhasil absen PULANG pukul '.$now->format('H:i').'.',
                'attendance' => $attendance->fresh(),
                'person' => $person,
            ];
        }

        return [
            'ok' => false,
            'kind' => 'done',
            'message' => $label.' '.$name.' sudah absen masuk dan pulang hari ini.',
            'attendance' => $attendance,
            'person' => $person,
        ];
    }

    /** Siswa yang sudah daftar ulang (siap absen). */
    public static function registeredStudents()
    {
        return Applicant::whereHas('applications', fn ($q) => $q->where('status', 'registered'))
            ->orderBy('full_name');
    }

    /** Semua guru. */
    public static function teachers()
    {
        return User::whereHas('role', fn ($q) => $q->where('code', 'guru'))
            ->orderBy('name');
    }
}
