<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'nama',
        'role_id',
        'karyawan_nik',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // ── Relations ──────────────────────────────────
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_nik', 'nik');
    }

    // ── Helpers ────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin';
    }

    public function isGuest(): bool
    {
        return $this->role?->name === 'guest';
    }

    // Override default auth field dari 'email' ke 'username'
    public function getAuthIdentifierName(): string
    {
        return 'username';
    }
}