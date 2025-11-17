<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Keluarga extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'keluarga';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'no_kk',
        'kepala_keluarga_id',
        'alamat_kk',
        'rt_kk',
        'rw_kk',
        'kelurahan_kk',
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
     * Relasi ke kepala keluarga
     */
    public function kepalaKeluarga()
    {
        return $this->belongsTo(Warga::class, 'kepala_keluarga_id');
    }

    /**
     * Relasi ke semua anggota keluarga
     */
    public function anggotaKeluarga()
    {
        return $this->hasMany(Warga::class, 'kk_id');
    }

    /**
     * Relasi ke iuran
     */
    public function iuran()
    {
        return $this->hasMany(Iuran::class, 'kk_id');
    }

    /**
     * Scope untuk KK dengan kepala keluarga
     */
    public function scopeWithKepalaKeluarga($query)
    {
        return $query->with('kepalaKeluarga');
    }

    /**
     * Scope berdasarkan RT
     */
    public function scopeRt($query, $rt)
    {
        return $query->where('rt_kk', $rt);
    }

    /**
     * Scope berdasarkan RW
     */
    public function scopeRw($query, $rw)
    {
        return $query->where('rw_kk', $rw);
    }

    /**
     * Scope berdasarkan kelurahan
     */
    public function scopeKelurahan($query, $kelurahan)
    {
        return $query->where('kelurahan_kk', $kelurahan);
    }

    /**
     * Get jumlah anggota keluarga
     */
    public function getJumlahAnggotaAttribute(): int
    {
        return $this->anggotaKeluarga()->count();
    }

    /**
     * Get alamat lengkap
     */
    public function getAlamatLengkapAttribute(): string
    {
        return "{$this->alamat_kk}, RT {$this->rt_kk}/RW {$this->rw_kk}, {$this->kelurahan_kk}";
    }

    /**
     * Get nama kepala keluarga
     */
    public function getNamaKepalaKeluargaAttribute(): string
    {
        return $this->kepalaKeluarga ? $this->kepalaKeluarga->nama_lengkap : 'Belum ditentukan';
    }

    /**
     * Set kepala keluarga
     */
    public function setKepalaKeluarga(Warga $warga)
    {
        $this->kepala_keluarga_id = $warga->id;
        $this->save();

        // Update warga sebagai kepala keluarga
        $warga->update([
            'kk_id' => $this->id,
            'hubungan_keluarga' => 'Kepala Keluarga'
        ]);
    }

    /**
     * Tambah anggota keluarga
     */
    public function tambahAnggota(Warga $warga, string $hubungan = 'Anggota Keluarga')
    {
        $warga->update([
            'kk_id' => $this->id,
            'hubungan_keluarga' => $hubungan
        ]);
    }

    /**
     * Hapus anggota keluarga
     */
    public function hapusAnggota(Warga $warga)
    {
        $warga->update([
            'kk_id' => null,
            'hubungan_keluarga' => null
        ]);
    }

    /**
     * Cek apakah warga adalah anggota keluarga
     */
    public function isAnggota(Warga $warga): bool
    {
        return $this->anggotaKeluarga()->where('id', $warga->id)->exists();
    }

    /**
     * Get daftar hubungan keluarga yang tersedia
     */
    public static function getDaftarHubungan(): array
    {
        return [
            'Kepala Keluarga',
            'Istri',
            'Suami',
            'Anak',
            'Orang Tua',
            'Mertua',
            'Menantu',
            'Cucu',
            'Saudara',
            'Lainnya'
        ];
    }

    /**
     * Search KK berdasarkan nomor atau nama kepala keluarga
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function($q) use ($keyword) {
            $q->where('no_kk', 'like', "%{$keyword}%")
              ->orWhereHas('kepalaKeluarga', function($subQuery) use ($keyword) {
                  $subQuery->where('nama_lengkap', 'like', "%{$keyword}%");
              });
        });
    }

    /**
     * Format nomor KK dengan tanda hubung
     */
    public function getNoKkFormatAttribute(): string
    {
        $no_kk = $this->no_kk;
        if (strlen($no_kk) === 16) {
            return substr($no_kk, 0, 4) . '-' .
                   substr($no_kk, 4, 4) . '-' .
                   substr($no_kk, 8, 4) . '-' .
                   substr($no_kk, 12, 4);
        }
        return $no_kk;
    }
}