<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Iuran extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'iurans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kk_id',
        'jenis_iuran_id',
        'nominal',
        'periode_bulan',
        'status',
        'jatuh_tempo',
        'denda_terlambatan',
        'reminder_sent_at',
        'created_by',
        'keterangan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'denda_terlambatan' => 'decimal:2',
            'jatuh_tempo' => 'date',
            'reminder_sent_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke keluarga (KK-based)
     */
    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class, 'kk_id');
    }

    /**
     * Relasi ke jenis iuran
     */
    public function jenisIuran()
    {
        return $this->belongsTo(JenisIuran::class, 'jenis_iuran_id');
    }

    /**
     * Relasi ke pembayaran
     */
    public function pembayaran()
    {
        return $this->hasMany(PembayaranIuran::class, 'iuran_id');
    }

    /**
     * Relasi ke user yang membuat
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope berdasarkan status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk yang belum bayar
     */
    public function scopeBelumBayar($query)
    {
        return $query->where('status', 'belum_bayar');
    }

    /**
     * Scope untuk yang tertunda
     */
    public function scopeTertunda($query)
    {
        return $query->where('status', 'tertunda');
    }

    /**
     * Scope untuk yang sudah lunas
     */
    public function scopeLunas($query)
    {
        return $query->where('status', 'lunas');
    }

    /**
     * Scope untuk yang jatuh tempo
     */
    public function scopeJatuhTempo($query)
    {
        return $query->whereDate('jatuh_tempo', '<=', now());
    }

    /**
     * Scope untuk yang overdue
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'belum_bayar')
                    ->whereDate('jatuh_tempo', '<', now());
    }

    /**
     * Scope berdasarkan periode
     */
    public function scopePeriode($query, $periode)
    {
        return $query->where('periode_bulan', $periode);
    }

    /**
     * Scope berdasarkan RT
     */
    public function scopeRt($query, $rt)
    {
        return $query->where('rt_id', $rt);
    }

    /**
     * Scope berdasarkan RW
     */
    public function scopeRw($query, $rw)
    {
        return $query->where('rw_id', $rw);
    }

    /**
     * Get total pembayaran
     */
    public function getTotalPembayaranAttribute(): float
    {
        return $this->pembayaran->sum('jumlah_bayar');
    }

    /**
     * Get sisa pembayaran
     */
    public function getSisaPembayaranAttribute(): float
    {
        return ($this->nominal + $this->denda_terlambatan) - $this->total_pembayaran;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'belum_bayar' => 'Belum Bayar',
            'tertunda' => 'Tertunda',
            'lunas' => 'Lunas',
            'batal' => 'Dibatalkan',
            default => $this->status,
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'belum_bayar' => 'warning',
            'tertunda' => 'danger',
            'lunas' => 'success',
            'batal' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Format nominal ke Rupiah
     */
    public function getNominalRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    /**
     * Format total pembayaran ke Rupiah
     */
    public function getTotalPembayaranRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->total_pembayaran, 0, ',', '.');
    }

    /**
     * Format sisa pembayaran ke Rupiah
     */
    public function getSisaPembayaranRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->sisa_pembayaran, 0, ',', '.');
    }

    /**
     * Format denda ke Rupiah
     */
    public function getDendaRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->denda_terlambatan, 0, ',', '.');
    }

    /**
     * Cek apakah sudah lunas
     */
    public function isLunas(): bool
    {
        return $this->status === 'lunas' || $this->sisa_pembayaran <= 0;
    }

    /**
     * Cek apakah overdue
     */
    public function isOverdue(): bool
    {
        return $this->status === 'belum_bayar' && $this->jatuh_tempo->isPast();
    }

    /**
     * Get jumlah hari terlambat
     */
    public function getHariTerlambatAttribute(): int
    {
        if ($this->jatuh_tempo->isPast()) {
            return $this->jatuh_tempo->diffInDays(now());
        }
        return 0;
    }

    /**
     * Generate otomatis iuran untuk warga
     */
    public static function generateIuran(Warga $warga, JenisIuran $jenisIuran, string $periode): self
    {
        return self::create([
            'warga_id' => $warga->id,
            'kk_id' => $warga->kk_id,
            'jenis_iuran_id' => $jenisIuran->id,
            'rt_id' => $warga->rt_domisili,
            'rw_id' => $warga->rw_domisili,
            'nominal' => $jenisIuran->nominal_default,
            'periode_bulan' => $periode,
            'status' => 'belum_bayar',
            'jatuh_tempo' => now()->addDays(30), // Jatuh tempo 30 hari
            'denda_terlambatan' => 0,
        ]);
    }

    /**
     * Update status ke lunas
     */
    public function updateToLunas(): void
    {
        if ($this->sisa_pembayaran <= 0) {
            $this->update(['status' => 'lunas']);
        }
    }

    /**
     * Hitung denda keterlambatan
     */
    public function hitungDenda(): float
    {
        if ($this->isOverdue()) {
            $hariTerlambat = $this->hari_terlambat;
            $denda = ($this->nominal * 0.02) * $hariTerlambat; // 2% per hari
            return min($denda, $this->nominal * 0.5); // Maks 50% dari nominal
        }
        return 0;
    }

    /**
     * Apply denda otomatis
     */
    public function applyDenda(): void
    {
        $denda = $this->hitungDenda();
        if ($denda > 0) {
            $this->update(['denda_terlambatan' => $denda]);
        }
    }

    /**
     * Generate tagihan bulanan untuk semua warga
     */
    public static function generateTagihanBulanan(string $periode = null): int
    {
        $periode = $periode ?: now()->format('Y-m');
        $count = 0;

        $jenisIuran = JenisIuran::bulanan()->aktif()->get();
        $warga = Warga::all();

        foreach ($warga as $w) {
            foreach ($jenisIuran as $ji) {
                // Cek apakah iuran untuk periode ini sudah ada
                $exists = self::where('warga_id', $w->id)
                            ->where('jenis_iuran_id', $ji->id)
                            ->where('periode_bulan', $periode)
                            ->exists();

                if (!$exists) {
                    self::generateIuran($w, $ji, $periode);
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Get statistik pembayaran per periode
     */
    public static function getStatistikPembayaran(string $periode = null): array
    {
        $periode = $periode ?: now()->format('Y-m');

        $total = self::where('periode_bulan', $periode)->count();
        $lunas = self::where('periode_bulan', $periode)->lunas()->count();
        $belum = self::where('periode_bulan', $periode)->belumBayar()->count();
        $overdue = self::where('periode_bulan', $periode)->overdue()->count();

        return [
            'total' => $total,
            'lunas' => $lunas,
            'belum' => $belum,
            'overdue' => $overdue,
            'persentase_lunas' => $total > 0 ? round(($lunas / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get daftar status
     */
    public static function getDaftarStatus(): array
    {
        return [
            'belum_bayar' => 'Belum Bayar',
            'tertunda' => 'Tertunda',
            'lunas' => 'Lunas',
            'batal' => 'Dibatalkan',
        ];
    }
}