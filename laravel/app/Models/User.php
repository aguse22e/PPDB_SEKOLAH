<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'phone',
        'barcode_code',
        'password',
        'is_active',
        'must_change_password',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    protected static function booted(): void
    {
        // Kode barcode guru dibuat otomatis saat akun guru dibuat: GRU + 6 digit id.
        static::created(function (User $user) {
            if (empty($user->barcode_code) && $user->role?->code === 'guru') {
                $user->updateQuietly([
                    'barcode_code' => 'GRU'.str_pad((string) $user->id, 6, '0', STR_PAD_LEFT),
                ]);
            }
        });
    }

    public function applicant()
    {
        return $this->hasOne(Applicant::class);
    }

    public function appNotifications()
    {
        return $this->hasMany(AppNotification::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function isAdmin(): bool
    {
        return $this->role?->code === 'admin';
    }
}
