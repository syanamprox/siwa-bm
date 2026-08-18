<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisIuran extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jenis_iurans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'kode',
        'jumlah',
        'keterangan',
        'is_aktif',
        'periode'
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'is_aktif' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke iuran
     */
    public function iuran()
    {
        return $this->hasMany(Iuran::class, 'jenis_iuran_id');
    }

    /**
     * Relasi many-to-many ke keluarga (koneksi iuran)
     */
    public function keluarga()
    {
        return $this->belongsToMany(Keluarga::class, 'keluarga_iuran')
            ->withPivot(['nominal_custom', 'status_aktif', 'alasan_custom', 'created_by'])
            ->withTimestamps();
    }

    /**
     * Scope untuk iuran aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    /**
     * Scope berdasarkan periode
     */
    public function scopePeriode($query, $periode)
    {
        return $query->where('periode', $periode);
    }

    /**
     * Scope untuk iuran bulanan
     */
    public function scopeBulanan($query)
    {
        return $query->where('periode', 'bulanan');
    }

    /**
     * Scope untuk iuran tahunan
     */
    public function scopeTahunan($query)
    {
        return $query->where('periode', 'tahunan');
    }

    /**
     * Scope untuk iuran sekali bayar
     */
    public function scopeSekali($query)
    {
        return $query->where('periode', 'sekali');
    }

    /**
     * Format nominal ke Rupiah
     */
    public function getNominalRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }

    /**
     * Get formatted periode label
     */
    public function getPeriodeLabelAttribute(): string
    {
        return match($this->periode) {
            'bulanan' => 'Setiap Bulan',
            'tahunan' => 'Setiap Tahun',
            'sekali' => 'Sekali Bayar',
            default => ucfirst($this->periode),
        };
    }

    /**
     * Get nominal default attribute (alias for jumlah)
     */
    public function getNominalDefaultAttribute()
    {
        return $this->jumlah;
    }

    
    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_aktif ? 'Aktif' : 'Tidak Aktif';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return $this->is_aktif ? 'success' : 'secondary';
    }

    /**
     * Toggle status aktif
     */
    public function toggleStatus(): void
    {
        $this->update(['is_aktif' => !$this->is_aktif]);
    }

    /**
     * Cek apakah bisa dihapus (belum ada iuran terkait)
     */
    public function bisaDihapus(): bool
    {
        return $this->iuran()->count() === 0;
    }

    /**
     * Get daftar periode
     */
    public static function getDaftarPeriode(): array
    {
        return [
            'bulanan' => 'Setiap Bulan',
            'tahunan' => 'Setiap Tahun',
            'sekali' => 'Sekali Bayar',
        ];
    }

    /**
     * Search jenis iuran
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function($q) use ($keyword) {
            $q->where('nama', 'like', "%{$keyword}%")
              ->orWhere('keterangan', 'like', "%{$keyword}%");
        });
    }

    /**
     * Get total pemasukan dari jenis iuran ini
     */
    public function getTotalPemasukanAttribute(): float
    {
        return $this->iuran()
            ->whereHas('pembayaran')
            ->with('pembayaran')
            ->get()
            ->sum(function($iuran) {
                return $iuran->pembayaran->sum('jumlah_bayar');
            });
    }

    /**
     * Generate default iuran types
     */
    public static function generateDefaultTypes(): array
    {
        return [
            [
                'nama' => 'Iuran Kebersihan',
                'keterangan' => 'Iuran untuk kebersihan lingkungan RT/RW',
                'jumlah' => 25000,
                'periode' => 'bulanan',
                'is_aktif' => true,
            ],
            [
                'nama' => 'Iuran Keamanan',
                'keterangan' => 'Iuran untuk satpam/keamanan lingkungan',
                'jumlah' => 30000,
                'periode' => 'bulanan',
                'is_aktif' => true,
            ],
            [
                'nama' => 'Iuran Sosial',
                'keterangan' => 'Iuran untuk dana sosial/kematian',
                'jumlah' => 20000,
                'periode' => 'bulanan',
                'is_aktif' => true,
            ],
            [
                'nama' => 'Iuran Infrastruktur',
                'keterangan' => 'Iuran untuk pembangunan/infrastruktur',
                'jumlah' => 50000,
                'periode' => 'sekali',
                'is_aktif' => true,
            ],
        ];
    }
}