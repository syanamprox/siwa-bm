<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KasUnit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kas_units';

    /**
     * Kategori transaksi kas (dipakai validasi KasController + FE).
     */
    public const KATEGORI = [
        'Iuran', 'Parkir', 'Infaq', 'Donasi', 'Saldo Awal', 'Lain-lain',
        'Operasional', 'Rapat', 'Perlengkapan', 'Kesehatan', 'Kegiatan',
    ];

    protected $fillable = [
        'nama',
        'jenis',
        'wilayah_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function transaksis()
    {
        return $this->hasMany(KasTransaksi::class)
            ->orderByDesc('tanggal')
            ->orderByDesc('id');
    }

    /**
     * Unit kas untuk sebuah wilayah (RT/RW/Kelurahan) — find-or-create.
     */
    public static function forWilayah(Wilayah $wilayah): self
    {
        return self::firstOrCreate(
            ['jenis' => strtolower($wilayah->tingkat), 'wilayah_id' => $wilayah->id],
            ['nama' => $wilayah->nama],
        );
    }

    /**
     * Unit RT dari keluarga iuran (domisili KK) — null jika keluarga tanpa rt_id.
     */
    public static function forPembayaran(Iuran $iuran): ?self
    {
        $iuran->loadMissing('keluarga:id,no_kk,rt_id');

        $rtId = $iuran->keluarga?->rt_id;
        if (! $rtId) {
            return null;
        }

        $rt = Wilayah::find($rtId);

        return $rt ? self::forWilayah($rt) : null;
    }
}
