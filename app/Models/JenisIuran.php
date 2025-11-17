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
        'deskripsi',
        'nominal_default',
        'periode',
        'status_aktif',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nominal_default' => 'decimal:2',
            'status_aktif' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
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
     * Scope untuk iuran aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
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
        return $query->where('periode', 'Bulanan');
    }

    /**
     * Scope untuk iuran tahunan
     */
    public function scopeTahunan($query)
    {
        return $query->where('periode', 'Tahunan');
    }

    /**
     * Scope untuk iuran sekali bayar
     */
    public function scopeSekali($query)
    {
        return $query->where('periode', 'Sekali');
    }

    /**
     * Format nominal ke Rupiah
     */
    public function getNominalRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal_default, 0, ',', '.');
    }

    /**
     * Get periode label
     */
    public function getPeriodeLabelAttribute(): string
    {
        return match($this->periode) {
            'Bulanan' => 'Setiap Bulan',
            'Tahunan' => 'Setiap Tahun',
            'Sekali' => 'Sekali Bayar',
            default => $this->periode,
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status_aktif ? 'Aktif' : 'Tidak Aktif';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return $this->status_aktif ? 'success' : 'secondary';
    }

    /**
     * Toggle status aktif
     */
    public function toggleStatus(): void
    {
        $this->update(['status_aktif' => !$this->status_aktif]);
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
            'Bulanan' => 'Setiap Bulan',
            'Tahunan' => 'Setiap Tahun',
            'Sekali' => 'Sekali Bayar',
        ];
    }

    /**
     * Search jenis iuran
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function($q) use ($keyword) {
            $q->where('nama', 'like', "%{$keyword}%")
              ->orWhere('deskripsi', 'like', "%{$keyword}%");
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
                'deskripsi' => 'Iuran untuk kebersihan lingkungan RT/RW',
                'nominal_default' => 25000,
                'periode' => 'Bulanan',
                'status_aktif' => true,
            ],
            [
                'nama' => 'Iuran Keamanan',
                'deskripsi' => 'Iuran untuk satpam/keamanan lingkungan',
                'nominal_default' => 30000,
                'periode' => 'Bulanan',
                'status_aktif' => true,
            ],
            [
                'nama' => 'Iuran Sosial',
                'deskripsi' => 'Iuran untuk dana sosial/kematian',
                'nominal_default' => 20000,
                'periode' => 'Bulanan',
                'status_aktif' => true,
            ],
            [
                'nama' => 'Iuran Infrastruktur',
                'deskripsi' => 'Iuran untuk pembangunan/infrastruktur',
                'nominal_default' => 50000,
                'periode' => 'Sekali',
                'status_aktif' => true,
            ],
        ];
    }
}