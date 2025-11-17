<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wilayah extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wilayahs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode',
        'nama',
        'tingkat',
        'parent_id',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'kode_display',
        'tingkat_label',
        'nama_lengkap',
        'nama_hierarki',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke parent wilayah
     */
    public function parent()
    {
        return $this->belongsTo(Wilayah::class, 'parent_id');
    }

    /**
     * Relasi ke child wilayah
     */
    public function children()
    {
        return $this->hasMany(Wilayah::class, 'parent_id');
    }

    /**
     * Relasi ke users (many-to-many)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_wilayahs');
    }

    /**
     * Relasi ke warga (melalui domisili)
     */
    public function warga()
    {
        if ($this->tingkat === 'RT') {
            return $this->hasMany(Warga::class, 'rt_domisili', 'kode');
        } elseif ($this->tingkat === 'RW') {
            return Warga::where('rw_domisili', $this->kode);
        } elseif ($this->tingkat === 'Kelurahan') {
            return Warga::where('kelurahan_domisili', 'like', '%' . $this->nama . '%');
        }

        return collect(); // Return empty collection for safety
    }

    /**
     * Scope untuk tingkat wilayah
     */
    public function scopeTingkat($query, $tingkat)
    {
        return $query->where('tingkat', $tingkat);
    }

    /**
     * Scope untuk kelurahan
     */
    public function scopeKelurahan($query)
    {
        return $query->where('tingkat', 'Kelurahan');
    }

    /**
     * Scope untuk RW
     */
    public function scopeRw($query)
    {
        return $query->where('tingkat', 'RW');
    }

    /**
     * Scope untuk RT
     */
    public function scopeRt($query)
    {
        return $query->where('tingkat', 'RT');
    }

    /**
     * Get nama lengkap dengan kode
     */
    public function getNamaLengkapAttribute(): string
    {
        return "{$this->kode} - {$this->nama}";
    }

    /**
     * Get tingkat label
     */
    public function getTingkatLabelAttribute(): string
    {
        return match($this->tingkat) {
            'Kelurahan' => 'Kelurahan',
            'RW' => 'RW',
            'RT' => 'RT',
            default => 'Unknown',
        };
    }

    /**
     * Get nama lengkap hierarki
     */
    public function getNamaHierarkiAttribute(): string
    {
        $nama = $this->nama;

        if ($this->parent) {
            $nama .= ', ' . $this->parent->getNamaHierarkiAttribute();
        }

        return $nama;
    }

    /**
     * Cek apakah ini adalah kelurahan (root)
     */
    public function isKelurahan(): bool
    {
        return $this->tingkat === 'Kelurahan';
    }

    /**
     * Cek apakah ini adalah RW
     */
    public function isRw(): bool
    {
        return $this->tingkat === 'RW';
    }

    /**
     * Cek apakah ini adalah RT
     */
    public function isRt(): bool
    {
        return $this->tingkat === 'RT';
    }

    /**
     * Get semua RT dalam RW ini
     */
    public function getAllRtAttribute()
    {
        if ($this->tingkat === 'RW') {
            return $this->children()->where('tingkat', 'RT')->get();
        }

        return collect();
    }

    /**
     * Format kode display
     */
    public function getKodeDisplayAttribute(): string
    {
        return match($this->tingkat) {
            'Kelurahan' => $this->kode,
            'RW' => "RW {$this->kode}",
            'RT' => "RT {$this->kode}",
            default => $this->kode,
        };
    }
}