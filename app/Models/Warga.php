<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warga extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wargas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Data KTP
        'nik',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'golongan_darah',
        'alamat_ktp',
        'rt_ktp',
        'rw_ktp',
        'kelurahan_ktp',
        'kecamatan_ktp',
        'kabupaten_ktp',
        'provinsi_ktp',
        'agama',
        'status_perkawinan',
        'pekerjaan',
        'kewarganegaraan',
        'pendidikan_terakhir',
        'foto_ktp',

        // Data Keluarga
        'kk_id',
        'hubungan_keluarga',

        // Kontak Personal
        'no_telepon',
        'email',

        // Tracking
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
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
        return $this->belongsTo(Keluarga::class, 'kk_id');
    }

    /**
     * Relasi ke user yang membuat data
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke user yang mengupdate data
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relasi ke iuran
     */
    public function iuran()
    {
        return $this->hasMany(Iuran::class, 'warga_id');
    }

    /**
     * Scope berdasarkan NIK
     */
    public function scopeNik($query, $nik)
    {
        return $query->where('nik', $nik);
    }

    /**
     * Scope berdasarkan nama
     */
    public function scopeNama($query, $nama)
    {
        return $query->where('nama_lengkap', 'like', "%{$nama}%");
    }

    /**
     * Scope berdasarkan jenis kelamin
     */
    public function scopeJenisKelamin($query, $jenis_kelamin)
    {
        return $query->where('jenis_kelamin', $jenis_kelamin);
    }

    /**
     * Scope berdasarkan RT domisili
     */
    public function scopeRtDomisili($query, $rt)
    {
        return $query->where('rt_domisili', $rt);
    }

    /**
     * Scope berdasarkan RW domisili
     */
    public function scopeRwDomisili($query, $rw)
    {
        return $query->where('rw_domisili', $rw);
    }

    /**
     * Scope berdasarkan agama
     */
    public function scopeAgama($query, $agama)
    {
        return $query->where('agama', $agama);
    }

    /**
     * Scope berdasarkan pekerjaan
     */
    public function scopePekerjaan($query, $pekerjaan)
    {
        return $query->where('pekerjaan', $pekerjaan);
    }

    /**
     * Scope berdasarkan pendidikan
     */
    public function scopePendidikan($query, $pendidikan)
    {
        return $query->where('pendidikan_terakhir', $pendidikan);
    }

    /**
     * Scope untuk warga yang memiliki KK
     */
    public function scopeMemilikiKK($query)
    {
        return $query->whereNotNull('kk_id');
    }

    /**
     * Search warga berdasarkan NIK atau nama
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function($q) use ($keyword) {
            $q->where('nik', 'like', "%{$keyword}%")
              ->orWhere('nama_lengkap', 'like', "%{$keyword}%");
        });
    }

    /**
     * Get umur dalam tahun
     */
    public function getUmurAttribute(): int
    {
        return $this->tanggal_lahir->age;
    }

    /**
     * Get jenis kelamin label
     */
    public function getJenisKelaminLabelAttribute(): string
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    /**
     * Get status perkawinan label
     */
    public function getStatusPerkawinanLabelAttribute(): string
    {
        return match($this->status_perkawinan) {
            'Belum Kawin' => 'Belum Kawin',
            'Kawin' => 'Kawin',
            'Cerai Hidup' => 'Cerai Hidup',
            'Cerai Mati' => 'Cerai Mati',
            default => $this->status_perkawinan,
        };
    }

    
    /**
     * Get kewarganegaraan label
     */
    public function getKewarganegaraanLabelAttribute(): string
    {
        return match($this->kewarganegaraan) {
            'WNI' => 'Warga Negara Indonesia',
            'WNA' => 'Warga Negara Asing',
            default => $this->kewarganegaraan,
        };
    }

    /**
     * Get alamat KTP lengkap
     */
    public function getAlamatKtpLengkapAttribute(): string
    {
        return "{$this->alamat_ktp}, RT {$this->rt_ktp}/RW {$this->rw_kp}, {$this->kelurahan_ktp}";
    }

    
    /**
     * Get URL foto KTP
     */
    public function getFotoKtpUrlAttribute(): ?string
    {
        if ($this->foto_ktp) {
            return asset('storage/' . $this->foto_ktp);
        }
        return null;
    }

    /**
     * Cek apakah warga sudah menikah
     */
    public function isSudahMenikah(): bool
    {
        return in_array($this->status_perkawinan, ['Kawin', 'Cerai Hidup']);
    }

    /**
     * Cek apakah warga adalah kepala keluarga
     */
    public function isKepalaKeluarga(): bool
    {
        return $this->hubungan_keluarga === 'Kepala Keluarga';
    }

    
    /**
     * Get daftar agama
     */
    public static function getDaftarAgama(): array
    {
        return [
            'Islam',
            'Kristen',
            'Katolik',
            'Hindu',
            'Buddha',
            'Konghucu'
        ];
    }

    /**
     * Get daftar status perkawinan
     */
    public static function getDaftarStatusPerkawinan(): array
    {
        return [
            'Belum Kawin',
            'Kawin',
            'Cerai Hidup',
            'Cerai Mati'
        ];
    }

    /**
     * Get daftar pekerjaan
     */
    public static function getDaftarPekerjaan(): array
    {
        return [
            'Belum/Tidak Bekerja',
            'Mengurus Rumah Tangga',
            'Pelajar/Mahasiswa',
            'Pensiunan',
            'Pegawai Negeri Sipil',
            'Tentara Nasional Indonesia',
            'Kepolisian RI',
            'Perdagangan',
            'Petani/Pekebun',
            'Peternak',
            'Nelayan/Perikanan',
            'Industri',
            'Konstruksi',
            'Transportasi',
            'Karyawan Swasta',
            'Wirausaha',
            'Buruh Harian Lepas',
            'Buruh Tani/Perkebunan',
            'Buruh Nelayan/Perikanan',
            'Buruh Bangunan',
            'Pembantu Rumah Tangga',
            'Penambang',
            'Lainnya'
        ];
    }

    /**
     * Get daftar pendidikan terakhir
     */
    public static function getDaftarPendidikan(): array
    {
        return [
            'Tidak/Sekolah',
            'SD',
            'SMP',
            'SMA',
            'D1/D2/D3',
            'S1',
            'S2',
            'S3'
        ];
    }

    /**
     * Format NIK dengan tanda hubung
     */
    public function getNikFormatAttribute(): string
    {
        $nik = $this->nik;
        if (strlen($nik) === 16) {
            return substr($nik, 0, 8) . ' ' .
                   substr($nik, 8, 6) . ' ' .
                   substr($nik, 14, 2);
        }
        return $nik;
    }

    /**
     * Validasi format NIK
     */
    public static function validateNik($nik): bool
    {
        return preg_match('/^[0-9]{16}$/', $nik);
    }

    /**
     * Generate NIK unik (untuk testing)
     */
    public static function generateNik(): string
    {
        do {
            $nik = str_pad(mt_rand(1, 9999999999999999), 16, '0', STR_PAD_LEFT);
        } while (self::where('nik', $nik)->exists());

        return $nik;
    }
}