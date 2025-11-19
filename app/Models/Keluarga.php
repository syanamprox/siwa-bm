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
    protected $table = 'keluargas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Data Kartu Keluarga
        'no_kk',
        'kepala_keluarga_id',
        'foto_kk',

        // Alamat KTP (Input Manual Lengkap)
        'alamat_kk',
        'rt_kk',
        'rw_kk',
        'kelurahan_kk',
        'kecamatan_kk',
        'kabupaten_kk',
        'provinsi_kk',

        // Alamat Domisili (Koneksi Sistem Wilayah)
        'alamat_domisili', // Alamat jalan saja
        'rt_id', // Foreign key ke wilayahs table

        // Status & Keterangan
        'status_domisili_keluarga',
        'tanggal_mulai_domisili_keluarga',
        'keterangan_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_mulai_domisili_keluarga' => 'date',
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
     * Relasi ke wilayah (RT)
     */
    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'rt_id');
    }

    // Note: Boot method tidak diperlukan lagi karena alamat domisili di-load dynamically via rt_id relationship

    /**
     * Relasi ke iuran
     */
    public function iuran()
    {
        return $this->hasMany(Iuran::class, 'keluarga_id');
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
            'hubungan_keluarga' => 'Lainnya' // Use default enum value instead of null
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
     * Get status domisili keluarga label
     */
    public function getStatusDomisiliKeluargaLabelAttribute(): string
    {
        return match($this->status_domisili_keluarga) {
            'Tetap' => 'Tetap (Alamat & Domisili Sama)',
            'Non Domisili' => 'Non Domisili (Alamat Sini, Domisili Luar)',
            'Luar' => 'Luar (Alamat Luar, Domisili Sini)',
            'Sementara' => 'Sementara (Kontrak/Ngontrak)',
            default => $this->status_domisili_keluarga,
        };
    }

    /**
     * Get alamat lengkap KK (sesuai KTP)
     */
    public function getAlamatLengkapKkAttribute(): string
    {
        return "{$this->alamat_kk}, RT {$this->rt_kk}/RW {$this->rw_kk}, {$this->kelurahan_kk}, {$this->kecamatan_kk}, {$this->kabupaten_kk}, {$this->provinsi_kk}";
    }

    /**
     * Get alamat lengkap domisili (dynamic load via rt_id relationship)
     */
    public function getAlamatLengkapDomisiliAttribute(): string
    {
        if (!$this->rt_id || !$this->wilayah) {
            return $this->alamat_domisili ?? '-';
        }

        $rt = $this->wilayah;
        $rw = $rt ? $rt->parent : null;
        $kelurahan = $rw ? $rw->parent : null;

        return "{$this->alamat_domisili}, RT {$rt->kode}/RW {$rw?->kode}, "
             . "{$kelurahan?->nama}, Wonocolo, Surabaya, Jawa Timur";
    }

    /**
     * Get RT domisili (dynamic via rt_id)
     */
    public function getRtDomisiliAttribute(): string
    {
        return $this->wilayah?->kode ?? '';
    }

    /**
     * Get RW domisili (dynamic via rt_id)
     */
    public function getRwDomisiliAttribute(): string
    {
        return $this->wilayah?->parent?->kode ?? '';
    }

    /**
     * Get Kelurahan domisili (dynamic via rt_id)
     */
    public function getKelurahanDomisiliAttribute(): string
    {
        return $this->wilayah?->parent?->parent?->nama ?? '';
    }

    /**
     * Get Kecamatan domisili (fixed based on data structure)
     */
    public function getKecamatanDomisiliAttribute(): string
    {
        return 'Wonocolo'; // Fixed based on actual data structure
    }

    /**
     * Get alamat lengkap keluarga (backward compatibility - uses KK address)
     */
    public function getAlamatLengkapAttribute(): string
    {
        return $this->getAlamatLengkapKkAttribute();
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

    /**
     * Get URL untuk foto KK
     */
    public function getFotoKkUrlAttribute(): ?string
    {
        if (!$this->foto_kk) {
            return null;
        }

        // Jika path sudah full URL, return as is
        if (filter_var($this->foto_kk, FILTER_VALIDATE_URL)) {
            return $this->foto_kk;
        }

        // Jika relative path, convert ke full URL
        return asset('storage/' . $this->foto_kk);
    }
}