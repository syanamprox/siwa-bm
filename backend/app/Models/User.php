<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status_aktif',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status_aktif' => 'boolean',
        ];
    }

    /**
     * Relasi ke wilayah (many-to-many)
     */
    public function wilayah()
    {
        return $this->belongsToMany(Wilayah::class, 'user_wilayahs');
    }

    /**
     * Relasi ke user_wilayah table
     */
    public function userWilayah()
    {
        return $this->hasMany(UserWilayah::class);
    }

    /**
     * Relasi ke warga yang dibuat
     */
    public function wargaDibuat()
    {
        return $this->hasMany(Warga::class, 'created_by');
    }

    /**
     * Relasi ke warga yang diupdate
     */
    public function wargaDiupdate()
    {
        return $this->hasMany(Warga::class, 'updated_by');
    }

    /**
     * Relasi ke pembayaran iuran sebagai petugas
     */
    public function pembayaranIuran()
    {
        return $this->hasMany(PembayaranIuran::class, 'petugas_id');
    }

    /**
     * Relasi ke aktivitas log
     */
    public function aktivitasLog()
    {
        return $this->hasMany(AktivitasLog::class);
    }

    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah lurah
     */
    public function isLurah(): bool
    {
        return $this->role === 'lurah';
    }

    /**
     * Cek apakah user adalah RW
     */
    public function isRw(): bool
    {
        return $this->role === 'rw';
    }

    /**
     * Cek apakah user adalah RT
     */
    public function isRt(): bool
    {
        return $this->role === 'rt';
    }

    /**
     * Check if user has specific role or multiple roles
     */
    public function hasRole($roles): bool
    {
        if (is_string($roles)) {
            return $this->role === $roles;
        }

        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }

        return false;
    }

    /**
     * Get role label
     */
    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin' => 'Administrator',
            'lurah' => 'Lurah',
            'rw' => 'RW',
            'rt' => 'RT',
            default => 'Unknown',
        };
    }

    /**
     * Scope untuk user aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }

    /**
     * Scope berdasarkan role
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Get URL foto profile
     */
    public function getFotoProfileUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset($this->avatar);
        }

        // Default avatar berdasarkan role
        return match($this->role) {
            'admin' => 'https://ui-avatars.com/api/?name=Admin&background=dc3545&color=fff',
            'lurah' => 'https://ui-avatars.com/api/?name=Lurah&background=28a745&color=fff',
            'rw' => 'https://ui-avatars.com/api/?name=RW&background=007bff&color=fff',
            'rt' => 'https://ui-avatars.com/api/?name=RT&background=6c757d&color=fff',
            default => 'https://ui-avatars.com/api/?name=User&background=6c757d&color=fff',
        };
    }
}
