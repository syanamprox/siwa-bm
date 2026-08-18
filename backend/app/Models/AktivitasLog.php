<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AktivitasLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'aktivitas_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'module',
        'description',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_lama' => 'array',
            'data_baru' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Disable timestamps for this model
     */
    public $timestamps = true;

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Log aktivitas otomatis
     */
    public static function logActivity(
        int $userId,
        string $tabel,
        int $idReferensi,
        string $jenisAktivitas,
        string $deskripsi,
        array $dataLama = null,
        array $dataBaru = null,
        string $ipAddress = null,
        string $userAgent = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'tabel_referensi' => $tabel,
            'id_referensi' => $idReferensi,
            'jenis_aktivitas' => $jenisAktivitas,
            'deskripsi' => $deskripsi,
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
        ]);
    }

    /**
     * Log login
     */
    public static function logLogin(int $userId, string $ipAddress = null): self
    {
        $user = User::find($userId);
        return self::logActivity(
            $userId,
            'users',
            $userId,
            'login',
            "User {$user->username} berhasil login",
            null,
            null,
            $ipAddress
        );
    }

    /**
     * Log logout
     */
    public static function logLogout(int $userId, string $ipAddress = null): self
    {
        $user = User::find($userId);
        return self::logActivity(
            $userId,
            'users',
            $userId,
            'logout',
            "User {$user->username} berhasil logout",
            null,
            null,
            $ipAddress
        );
    }

    /**
     * Log CRUD Create
     */
    public static function logCreate(int $userId, string $tabel, int $id, $data, string $namaEntitas = null): self
    {
        $namaEntitas = $namaEntitas ?? ($data['nama_lengkap'] ?? $data['nama'] ?? 'Data');
        return self::logActivity(
            $userId,
            $tabel,
            $id,
            'create',
            "Menambah {$namaEntitas} di tabel {$tabel}",
            null,
            $data
        );
    }

    /**
     * Log CRUD Update
     */
    public static function logUpdate(int $userId, string $tabel, int $id, $dataLama, $dataBaru, string $namaEntitas = null): self
    {
        $namaEntitas = $namaEntitas ?? ($dataBaru['nama_lengkap'] ?? $dataBaru['nama'] ?? 'Data');
        return self::logActivity(
            $userId,
            $tabel,
            $id,
            'update',
            "Mengupdate {$namaEntitas} di tabel {$tabel}",
            $dataLama,
            $dataBaru
        );
    }

    /**
     * Log CRUD Delete
     */
    public static function logDelete(int $userId, string $tabel, int $id, $dataLama, string $namaEntitas = null): self
    {
        $namaEntitas = $namaEntitas ?? ($dataLama['nama_lengkap'] ?? $dataLama['nama'] ?? 'Data');
        return self::logActivity(
            $userId,
            $tabel,
            $id,
            'delete',
            "Menghapus {$namaEntitas} dari tabel {$tabel}",
            $dataLama,
            null
        );
    }

    /**
     * Log pembayaran iuran
     */
    public static function logPembayaranIuran(int $userId, int $iuranId, float $jumlah, string $metode): self
    {
        return self::logActivity(
            $userId,
            'pembayaran_iuran',
            $iuranId,
            'create',
            "Pembayaran iuran sebesar Rp " . number_format($jumlah, 0, ',', '.') . " dengan metode {$metode}",
            null,
            ['jumlah' => $jumlah, 'metode' => $metode]
        );
    }

    /**
     * Log export data
     */
    public static function logExport(int $userId, string $tabel, string $format): self
    {
        return self::logActivity(
            $userId,
            $tabel,
            0,
            'export',
            "Export data tabel {$tabel} ke format {$format}",
            null,
            ['format' => $format]
        );
    }

    /**
     * Log import data
     */
    public static function logImport(int $userId, string $tabel, int $jumlahData): self
    {
        return self::logActivity(
            $userId,
            $tabel,
            0,
            'import',
            "Import {$jumlahData} data ke tabel {$tabel}",
            null,
            ['jumlah_data' => $jumlahData]
        );
    }

    /**
     * Log backup database
     */
    public static function logBackup(int $userId, string $backupFile, string $tipe = 'manual'): self
    {
        return self::logActivity(
            $userId,
            'database',
            0,
            'backup',
            "Backup database ({$tipe}): {$backupFile}",
            null,
            ['backup_file' => $backupFile, 'tipe' => $tipe]
        );
    }

    /**
     * Log restore database
     */
    public static function logRestore(int $userId, string $backupFile): self
    {
        return self::logActivity(
            $userId,
            'database',
            0,
            'restore',
            "Restore database dari: {$backupFile}",
            null,
            ['backup_file' => $backupFile]
        );
    }

    /**
     * Scope berdasarkan user
     */
    public function scopeUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope berdasarkan tabel
     */
    public function scopeTabel($query, $tabel)
    {
        return $query->where('tabel_referensi', $tabel);
    }

    /**
     * Scope berdasarkan jenis aktivitas
     */
    public function scopeJenis($query, $jenis)
    {
        return $query->where('jenis_aktivitas', $jenis);
    }

    /**
     * Scope untuk hari ini
     */
    public function scopeHariIni($query)
    {
        return $query->whereDate('created_at', now());
    }

    /**
     * Scope untuk minggu ini
     */
    public function scopeMingguIni($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope untuk bulan ini
     */
    public function scopeBulanIni($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    /**
     * Get jenis aktivitas label
     */
    public function getJenisAktivitasLabelAttribute(): string
    {
        return match($this->jenis_aktivitas) {
            'create' => 'Create',
            'update' => 'Update',
            'delete' => 'Delete',
            'login' => 'Login',
            'logout' => 'Logout',
            'export' => 'Export',
            'import' => 'Import',
            'backup' => 'Backup',
            'restore' => 'Restore',
            'payment' => 'Payment',
            'view' => 'View',
            default => ucfirst($this->jenis_aktivitas),
        };
    }

    /**
     * Get jenis aktivitas icon
     */
    public function getJenisAktivitasIconAttribute(): string
    {
        return match($this->jenis_aktivitas) {
            'create' => 'plus-circle',
            'update' => 'edit',
            'delete' => 'trash',
            'login' => 'sign-in-alt',
            'logout' => 'sign-out-alt',
            'export' => 'download',
            'import' => 'upload',
            'backup' => 'save',
            'restore' => 'history',
            'payment' => 'credit-card',
            'view' => 'eye',
            default => 'circle',
        };
    }

    /**
     * Get jenis aktivitas color
     */
    public function getJenisAktivitasColorAttribute(): string
    {
        return match($this->jenis_aktivitas) {
            'create' => 'success',
            'update' => 'warning',
            'delete' => 'danger',
            'login', 'logout' => 'info',
            'export', 'import' => 'primary',
            'backup', 'restore' => 'secondary',
            'payment' => 'success',
            'view' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Get daftar jenis aktivitas
     */
    public static function getDaftarJenis(): array
    {
        return [
            'create' => 'Create/Tambah',
            'update' => 'Update/Ubah',
            'delete' => 'Delete/Hapus',
            'login' => 'Login',
            'logout' => 'Logout',
            'export' => 'Export Data',
            'import' => 'Import Data',
            'backup' => 'Backup Database',
            'restore' => 'Restore Database',
            'payment' => 'Pembayaran',
            'view' => 'View Data',
        ];
    }

    /**
     * Get ringkasan aktivitas per hari
     */
    public static function getRingkasanPerHari(int $days = 7): array
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $tanggal = now()->subDays($i)->toDateString();
            $total = self::whereDate('created_at', $tanggal)->count();

            $data[] = [
                'tanggal' => $tanggal,
                'hari' => now()->subDays($i)->format('D'),
                'total_aktivitas' => $total,
            ];
        }

        return $data;
    }

    /**
     * Get statistik aktivitas per jenis
     */
    public static function getStatistikPerJenis(string $startDate = null, string $endDate = null): array
    {
        $query = self::query();

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query->selectRaw('jenis_aktivitas, COUNT(*) as total')
                   ->groupBy('jenis_aktivitas')
                   ->orderBy('total', 'desc')
                   ->get()
                   ->keyBy('jenis_aktivitas')
                   ->toArray();
    }

    /**
     * Get aktivitas terakhir
     */
    public static function getAktivitasTerakhir(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return self::with('user')
                   ->latest()
                   ->limit($limit)
                   ->get();
    }

    /**
     * Clean old logs
     */
    public static function cleanOldLogs(int $days = 90): int
    {
        return self::where('created_at', '<', now()->subDays($days))->delete();
    }

    /**
     * Get user agent info
     */
    public function getUserAgentInfoAttribute(): array
    {
        if (!$this->user_agent) {
            return [];
        }

        // Simple parsing user agent (bisa gunakan library yang lebih advanced)
        $ua = $this->user_agent;

        return [
            'browser' => 'Unknown',
            'os' => 'Unknown',
            'device' => 'Unknown',
            'raw' => $ua,
        ];
    }

    /**
     * Get perbedaan data
     */
    public function getPerbedaanAttribute(): array
    {
        $perbedaan = [];

        if ($this->data_lama && $this->data_baru) {
            foreach ($this->data_baru as $key => $newValue) {
                $oldValue = $this->data_lama[$key] ?? null;

                if ($oldValue !== $newValue) {
                    $perbedaan[$key] = [
                        'lama' => $oldValue,
                        'baru' => $newValue,
                    ];
                }
            }
        }

        return $perbedaan;
    }

    /**
     * Format data untuk display
     */
    public function formatData($data): string
    {
        if (!is_array($data)) {
            return $data;
        }

        $formatted = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $formatted[] = "{$key}: {$value}";
        }

        return implode("\n", $formatted);
    }
}