<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PembayaranIuran extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pembayaran_iuran';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'iuran_id',
        'jumlah_bayar',
        'metode_pembayaran',
        'bukti_pembayaran',
        'tanggal_bayar',
        'denda_dibayar',
        'petugas_id',
        'catatan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jumlah_bayar' => 'decimal:2',
            'denda_dibayar' => 'decimal:2',
            'tanggal_bayar' => 'date',
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
        return $this->belongsTo(Iuran::class, 'iuran_id');
    }

    /**
     * Relasi ke petugas (user yang memproses)
     */
    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    /**
     * Scope berdasarkan metode pembayaran
     */
    public function scopeMetode($query, $metode)
    {
        return $query->where('metode_pembayaran', $metode);
    }

    /**
     * Scope berdasarkan tanggal
     */
    public function scopeTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal_bayar', $tanggal);
    }

    /**
     * Scope untuk pembayaran hari ini
     */
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal_bayar', now());
    }

    /**
     * Scope untuk pembayaran bulan ini
     */
    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal_bayar', now()->month)
                    ->whereYear('tanggal_bayar', now()->year);
    }

    /**
     * Scope untuk pembayaran tahun ini
     */
    public function scopeTahunIni($query)
    {
        return $query->whereYear('tanggal_bayar', now()->year);
    }

    /**
     * Get format jumlah bayar
     */
    public function getJumlahBayarRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->jumlah_bayar, 0, ',', '.');
    }

    /**
     * Get format denda dibayar
     */
    public function getDendaDibayarRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->denda_dibayar, 0, ',', '.');
    }

    /**
     * Get total pembayaran (jumlah + denda)
     */
    public function getTotalPembayaranAttribute(): float
    {
        return $this->jumlah_bayar + $this->denda_dibayar;
    }

    /**
     * Get format total pembayaran
     */
    public function getTotalPembayaranRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->total_pembayaran, 0, ',', '.');
    }

    /**
     * Get metode pembayaran label
     */
    public function getMetodePembayaranLabelAttribute(): string
    {
        return match($this->metode_pembayaran) {
            'tunai' => 'Tunai',
            'transfer' => 'Transfer Bank',
            'qris' => 'QRIS',
            'ewallet' => 'E-Wallet',
            default => $this->metode_pembayaran,
        };
    }

    /**
     * Get metode pembayaran icon
     */
    public function getMetodePembayaranIconAttribute(): string
    {
        return match($this->metode_pembayaran) {
            'tunai' => 'cash',
            'transfer' => 'bank',
            'qris' => 'qrcode',
            'ewallet' => 'phone',
            default => 'credit-card',
        };
    }

    /**
     * Get URL bukti pembayaran
     */
    public function getBuktiPembayaranUrlAttribute(): ?string
    {
        if ($this->bukti_pembayaran) {
            return asset('storage/' . $this->bukti_pembayaran);
        }
        return null;
    }

    /**
     * Cek apakah pembayaran mencukupi
     */
    public function isMencukupi(): bool
    {
        if (!$this->iuran) return false;

        $totalYangHarusDibayar = $this->iuran->nominal + $this->iuran->denda_terlambatan;
        return $this->total_pembayaran >= $totalYangHarusDibayar;
    }

    /**
     * Cek apakah ada kelebihan pembayaran
     */
    public function getKelebihanPembayaranAttribute(): float
    {
        if (!$this->iuran) return 0;

        $totalYangHarusDibayar = $this->iuran->nominal + $this->iuran->denda_terlambatan;
        $kelebihan = $this->total_pembayaran - $totalYangHarusDibayar;

        return max(0, $kelebihan);
    }

    /**
     * Get format kelebihan pembayaran
     */
    public function getKelebihanPembayaranRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->kelebihan_pembayaran, 0, ',', '.');
    }

    /**
     * Process pembayaran dan update status iuran
     */
    public function processPembayaran(): void
    {
        if (!$this->iuran) return;

        // Update status iuran jika lunas
        if ($this->isMencukupi()) {
            $this->iuran->updateToLunas();
        } else {
            // Update status jadi tertunda jika belum lunas
            $this->iuran->update(['status' => 'tertunda']);
        }
    }

    /**
     * Create pembayaran otomatis
     */
    public static function createPembayaran(Iuran $iuran, float $jumlah, string $metode, User $petugas, string $catatan = null): self
    {
        $pembayaran = self::create([
            'iuran_id' => $iuran->id,
            'jumlah_bayar' => $jumlah,
            'metode_pembayaran' => $metode,
            'tanggal_bayar' => now()->toDateString(),
            'denda_dibayar' => $iuran->denda_terlambatan,
            'petugas_id' => $petugas->id,
            'catatan' => $catatan,
        ]);

        // Process pembayaran
        $pembayaran->processPembayaran();

        return $pembayaran;
    }

    /**
     * Get statistik pembayaran per metode
     */
    public static function getStatistikPerMetode(string $startDate = null, string $endDate = null): array
    {
        $query = self::query();

        if ($startDate) {
            $query->whereDate('tanggal_bayar', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('tanggal_bayar', '<=', $endDate);
        }

        $statistik = $query->selectRaw('metode_pembayaran, COUNT(*) as total, SUM(jumlah_bayar + denda_dibayar) as nominal')
                           ->groupBy('metode_pembayaran')
                           ->get();

        $result = [];
        foreach ($statistik as $stat) {
            $result[$stat->metode_pembayaran] = [
                'metode' => $stat->metode_pembayaran,
                'label' => self::getMetodeLabel($stat->metode_pembayaran),
                'total_transaksi' => $stat->total,
                'total_nominal' => $stat->nominal,
                'total_nominal_rupiah' => 'Rp ' . number_format($stat->nominal, 0, ',', '.'),
            ];
        }

        return $result;
    }

    /**
     * Get metode label
     */
    public static function getMetodeLabel(string $metode): string
    {
        return match($metode) {
            'tunai' => 'Tunai',
            'transfer' => 'Transfer Bank',
            'qris' => 'QRIS',
            'ewallet' => 'E-Wallet',
            default => $metode,
        };
    }

    /**
     * Get daftar metode pembayaran
     */
    public static function getDaftarMetode(): array
    {
        return [
            'tunai' => 'Tunai',
            'transfer' => 'Transfer Bank',
            'qris' => 'QRIS',
            'ewallet' => 'E-Wallet',
        ];
    }

    /**
     * Generate nomor referensi pembayaran
     */
    public static function generateNomorReferensi(): string
    {
        do {
            $nomor = 'PAY-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('nomor_referensi', $nomor)->exists());

        return $nomor;
    }

    /**
     * Get ringkasan pembayaran per hari
     */
    public static function getRingkasanPerHari(int $days = 7): array
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $tanggal = now()->subDays($i)->toDateString();
            $total = self::whereDate('tanggal_bayar', $tanggal)->sum('jumlah_bayar + denda_dibayar');
            $transaksi = self::whereDate('tanggal_bayar', $tanggal)->count();

            $data[] = [
                'tanggal' => $tanggal,
                'hari' => now()->subDays($i)->format('D'),
                'total_nominal' => $total,
                'total_nominal_rupiah' => 'Rp ' . number_format($total, 0, ',', '.'),
                'total_transaksi' => $transaksi,
            ];
        }

        return $data;
    }

    /**
     * Cek pembayaran yang perlu verifikasi
     */
    public static function getPerluVerifikasi(): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('metode_pembayaran', '!=', 'tunai')
                   ->whereNull('verified_at')
                   ->with(['iuran.warga', 'petugas'])
                   ->get();
    }

    /**
     * Verifikasi pembayaran (untuk non-tunai)
     */
    public function verifikasi(User $verifikator): void
    {
        $this->update([
            'verified_at' => now(),
            'verified_by' => $verifikator->id,
        ]);

        // Process pembayaran setelah verifikasi
        $this->processPembayaran();
    }
}