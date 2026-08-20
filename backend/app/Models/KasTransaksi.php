<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasTransaksi extends Model
{
    use HasFactory;

    protected $table = 'kas_transaksis';

    protected $fillable = [
        'kas_unit_id',
        'tipe',
        'sumber',
        'pembayaran_iuran_id',
        'jumlah',
        'kategori',
        'keterangan',
        'tanggal',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'tanggal' => 'date',
        ];
    }

    public function kasUnit()
    {
        return $this->belongsTo(KasUnit::class);
    }

    public function pembayaran()
    {
        return $this->belongsTo(PembayaranIuran::class);
    }

    /**
     * Auto-post kas dari pembayaran iuran (idempotent by pembayaran_iuran_id).
     * Dipanggil setelah PembayaranIuran::create — jangan pernah biarkan gagal
     * membatalkan pembayaran (caller wajib wrap try-catch).
     */
    public static function postFromPembayaran(PembayaranIuran $pembayaran, Iuran $iuran): ?self
    {
        $unit = KasUnit::forPembayaran($iuran);
        if (! $unit) {
            return null;
        }

        $iuran->loadMissing('jenisIuran:id,nama', 'keluarga:id,no_kk,rt_id');
        $noKk = (string) ($iuran->keluarga?->no_kk ?? '');

        return self::firstOrCreate(
            ['pembayaran_iuran_id' => $pembayaran->id],
            [
                'kas_unit_id' => $unit->id,
                'tipe' => 'masuk',
                'sumber' => 'iuran',
                'kategori' => 'Iuran',
                'jumlah' => $pembayaran->jumlah_bayar,
                'tanggal' => $pembayaran->created_at->toDateString(),
                'keterangan' => 'Iuran '.$iuran->jenisIuran?->nama.' · '.$iuran->periode_bulan.' · KK …'.substr($noKk, -4),
                'created_by' => $pembayaran->created_by,
            ],
        );
    }
}
