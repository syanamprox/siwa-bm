<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KeluargaIuran extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'keluarga_iuran';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'keluarga_id',
        'jenis_iuran_id',
        'nominal_custom',
        'status_aktif',
        'alasan_custom',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nominal_custom' => 'decimal:2',
            'status_aktif' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke keluarga
     */
    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class);
    }

    /**
     * Relasi ke jenis iuran
     */
    public function jenisIuran()
    {
        return $this->belongsTo(JenisIuran::class);
    }

    /**
     * Relasi ke user yang membuat koneksi
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get nominal efektif (custom atau default)
     */
    public function getEffectiveNominalAttribute(): float
    {
        return $this->nominal_custom ?? ($this->jenisIuran->jumlah ?? 0);
    }

    /**
     * Get nominal efektif format Rupiah
     */
    public function getEffectiveNominalRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->effective_nominal, 0, ',', '.');
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status_aktif ? 'Aktif' : 'Non-Aktif';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return $this->status_aktif ? 'success' : 'secondary';
    }

    /**
     * Check apakah menggunakan nominal custom
     */
    public function isCustomNominal(): bool
    {
        return !is_null($this->nominal_custom);
    }

    /**
     * Toggle status aktif
     */
    public function toggleStatus(): void
    {
        $this->update(['status_aktif' => !$this->status_aktif]);
    }

    /**
     * Scope untuk koneksi aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }

    /**
     * Scope untuk keluarga tertentu
     */
    public function scopeKeluarga($query, $keluargaId)
    {
        return $query->where('keluarga_id', $keluargaId);
    }

    /**
     * Scope untuk jenis iuran tertentu
     */
    public function scopeJenisIuran($query, $jenisIuranId)
    {
        return $query->where('jenis_iuran_id', $jenisIuranId);
    }

    /**
     * Scope untuk koneksi dengan nominal custom
     */
    public function scopeCustomNominal($query)
    {
        return $query->whereNotNull('nominal_custom');
    }
}
