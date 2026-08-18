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
     * Get kepala keluarga attribute
     */
    public function getKepalaKeluargaAttribute()
    {
        return $this->keluarga->kepalaKeluarga?->nama_lengkap ?? '-';
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
     * Scope berdasarkan RT (melalui keluarga)
     */
    public function scopeRt($query, $rt)
    {
        return $query->whereHas('keluarga', function($q) use ($rt) {
            $q->where('rt_id', $rt);
        });
    }

    /**
     * Scope berdasarkan RW (melalui keluarga)
     */
    public function scopeRw($query, $rw)
    {
        return $query->whereHas('keluarga', function($q) use ($rw) {
            $q->where('rw_id', $rw);
        });
    }

    /**
     * Get total tagihan (nominal + denda)
     */
    public function getTotalTagihanAttribute(): float
    {
        return $this->nominal + ($this->denda_terlambatan ?? 0);
    }

    /**
     * Get total pembayaran
     */
    public function getTotalPembayaranAttribute(): float
    {
        return $this->pembayaran->sum('jumlah');
    }

    /**
     * Get sisa pembayaran
     */
    public function getSisaPembayaranAttribute(): float
    {
        return $this->total_tagihan - $this->total_pembayaran;
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
     * Generate otomatis iuran untuk keluarga (KK-based)
     */
    public static function generateIuran(Keluarga $keluarga, JenisIuran $jenisIuran, string $periode): self
    {
        return self::create([
            'kk_id' => $keluarga->id,
            'jenis_iuran_id' => $jenisIuran->id,
            'nominal' => $jenisIuran->jumlah,
            'periode_bulan' => $periode,
            'status' => 'belum_bayar',
            'jatuh_tempo' => Carbon::parse($periode . '-01')->endOfMonth(),
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
     * Generate tagihan bulanan untuk semua keluarga aktif (KK-based)
     */
    public static function generateTagihanBulanan(string $periode = null): int
    {
        $periode = $periode ?: now()->format('Y-m');
        $count = 0;

        $jenisIuran = JenisIuran::where('is_aktif', true)->get();
        $keluargas = Keluarga::where('status_keluarga', 'Aktif')->get();

        foreach ($keluargas as $keluarga) {
            foreach ($jenisIuran as $ji) {
                // Check if keluarga is connected to this jenis iuran
                $connected = $keluarga->keluargaIuran()
                    ->where('jenis_iuran_id', $ji->id)
                    ->where('status_aktif', true)
                    ->exists();

                if ($connected) {
                    // Cek apakah iuran untuk periode ini sudah ada
                    $exists = self::where('kk_id', $keluarga->id)
                                ->where('jenis_iuran_id', $ji->id)
                                ->where('periode_bulan', $periode)
                                ->exists();

                    if (!$exists) {
                        // Get nominal from keluarga_iuran or jenis_iuran
                        $keluargaIuran = $keluarga->keluargaIuran()
                            ->where('jenis_iuran_id', $ji->id)
                            ->where('status_aktif', true)
                            ->first();

                        self::create([
                            'kk_id' => $keluarga->id,
                            'jenis_iuran_id' => $ji->id,
                            'nominal' => $keluargaIuran?->nominal_custom ?? $ji->jumlah,
                            'periode_bulan' => $periode,
                            'status' => 'belum_bayar',
                            'jatuh_tempo' => Carbon::parse($periode . '-01')->endOfMonth(),
                            'denda_terlambatan' => 0,
                            'keterangan' => "Generate otomatis periode {$periode}",
                        ]);
                        $count++;
                    }
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