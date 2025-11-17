<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PengaturanSistem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pengaturan_sistem';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
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
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Disable timestamps for this model
     */
    public $timestamps = true;

    /**
     * Get nilai pengaturan dengan cache
     */
    public static function getValue(string $key, $default = null)
    {
        return Cache::remember("pengaturan_{$key}", 3600, function () use ($key, $default) {
            $pengaturan = self::where('key', $key)->first();
            return $pengaturan ? $pengaturan->value : $default;
        });
    }

    /**
     * Set nilai pengaturan
     */
    public static function setValue(string $key, $value, string $keterangan = null): self
    {
        $pengaturan = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'keterangan' => $keterangan,
            ]
        );

        // Clear cache
        Cache::forget("pengaturan_{$key}");

        return $pengaturan;
    }

    /**
     * Get identitas kelurahan
     */
    public static function getIdentitasKelurahan(): array
    {
        return [
            'nama' => self::getValue('kelurahan_nama', 'Kelurahan Contoh'),
            'alamat' => self::getValue('kelurahan_alamat', 'Jl. Contoh No. 123'),
            'telepon' => self::getValue('kelurahan_telepon', '021-1234567'),
            'email' => self::getValue('kelurahan_email', 'kelurahan@example.com'),
            'kepala_kelurahan' => self::getValue('kepala_kelurahan', 'Budi Santoso, S.IP'),
            'kepala_nip' => self::getValue('kepala_nip', '1234567890123456'),
            'sekretaris_kelurahan' => self::getValue('sekretaris_kelurahan', 'Ahmad Fadli, S.Sos'),
            'sekretaris_nip' => self::getValue('sekretaris_nip', '1234567890123457'),
        ];
    }

    /**
     * Set identitas kelurahan
     */
    public static function setIdentitasKelurahan(array $data): void
    {
        foreach ($data as $key => $value) {
            self::setValue($key, $value);
        }
    }

    /**
     * Get pengaturan iuran (denda, reminder, dll)
     */
    public static function getPengaturanIuran(): array
    {
        return [
            'denda_persen_harian' => (float) self::getValue('denda_persen_harian', 2), // 2% per hari
            'denda_maksimal_persen' => (float) self::getValue('denda_maksimal_persen', 50), // maks 50%
            'hari_jatuh_tempo' => (int) self::getValue('hari_jatuh_tempo', 30), // 30 hari
            'hari_reminder_1' => (int) self::getValue('hari_reminder_1', 7), // reminder 7 hari sebelum jatuh tempo
            'hari_reminder_2' => (int) self::getValue('hari_reminder_2', 3), // reminder 3 hari sebelum jatuh tempo
            'hari_reminder_3' => (int) self::getValue('hari_reminder_3', 1), // reminder 1 hari sebelum jatuh tempo
            'interval_overdue_reminder' => (int) self::getValue('interval_overdue_reminder', 7), // reminder overdue setiap 7 hari
        ];
    }

    /**
     * Set pengaturan iuran
     */
    public static function setPengaturanIuran(array $data): void
    {
        foreach ($data as $key => $value) {
            self::setValue($key, $value);
        }
    }

    /**
     * Get pengaturan backup
     */
    public static function getPengaturanBackup(): array
    {
        return [
            'auto_backup' => self::getValue('auto_backup', true),
            'backup_schedule' => self::getValue('backup_schedule', '0 2 * * *'), // setiap hari jam 2 pagi
            'backup_retention' => (int) self::getValue('backup_retention', 30), // 30 hari
            'backup_location' => self::getValue('backup_location', 'storage/app/backups'),
            'backup_include_files' => self::getValue('backup_include_files', true),
        ];
    }

    /**
     * Set pengaturan backup
     */
    public static function setPengaturanBackup(array $data): void
    {
        foreach ($data as $key => $value) {
            self::setValue($key, $value);
        }
    }

    /**
     * Get pengaturan notifikasi
     */
    public static function getPengaturanNotifikasi(): array
    {
        return [
            'whatsapp_enabled' => self::getValue('whatsapp_enabled', false),
            'whatsapp_api_url' => self::getValue('whatsapp_api_url', ''),
            'whatsapp_api_key' => self::getValue('whatsapp_api_key', ''),
            'sms_enabled' => self::getValue('sms_enabled', false),
            'sms_provider' => self::getValue('sms_provider', 'twilio'),
            'sms_api_key' => self::getValue('sms_api_key', ''),
            'sms_api_secret' => self::getValue('sms_api_secret', ''),
            'email_enabled' => self::getValue('email_enabled', true),
            'email_from' => self::getValue('email_from', 'no-reply@kelurahan.com'),
            'email_from_name' => self::getValue('email_from_name', 'SIWA Kelurahan'),
        ];
    }

    /**
     * Set pengaturan notifikasi
     */
    public static function setPengaturanNotifikasi(array $data): void
    {
        foreach ($data as $key => $value) {
            self::setValue($key, $value);
        }
    }

    /**
     * Get pengaturan sistem UI
     */
    public static function getPengaturanUI(): array
    {
        return [
            'app_name' => self::getValue('app_name', 'SIWA - Sistem Informasi Warga'),
            'app_version' => self::getValue('app_version', '1.0.0'),
            'theme_color' => self::getValue('theme_color', '#007bff'),
            'sidebar_collapse' => self::getValue('sidebar_collapse', false),
            'items_per_page' => (int) self::getValue('items_per_page', 20),
            'date_format' => self::getValue('date_format', 'd-m-Y'),
            'time_format' => self::getValue('time_format', 'H:i'),
            'currency_symbol' => self::getValue('currency_symbol', 'Rp'),
            'decimal_separator' => self::getValue('decimal_separator', ','),
            'thousands_separator' => self::getValue('thousands_separator', '.'),
        ];
    }

    /**
     * Set pengaturan sistem UI
     */
    public static function setPengaturanUI(array $data): void
    {
        foreach ($data as $key => $value) {
            self::setValue($key, $value);
        }
    }

    /**
     * Get template pesan notifikasi
     */
    public static function getTemplatePesan(): array
    {
        return [
            'reminder_iuran' => self::getValue('template_reminder_iuran', 'Yth. Bapak/Ibu {nama_warga},\n\nIni adalah pengingat bahwa iuran {jenis_iuran} periode {periode} sebesar {nominal} harus dibayar sebelum {jatuh_tempo}.\n\nTerima kasih.\n\nKelurahan {kelurahan}'),
            'overdue_iuran' => self::getValue('template_overdue_iuran', 'Yth. Bapak/Ibu {nama_warga},\n\nIuran {jenis_iuran} periode {periode} sebesar {nominal} sudah jatuh tempo pada {jatuh_tempo}. Mohon segera melakukan pembayaran untuk menghindari denda.\n\nTerima kasih.\n\nKelurahan {kelurahan}'),
            'konfirmasi_pembayaran' => self::getValue('template_konfirmasi_pembayaran', 'Yth. Bapak/Ibu {nama_warga},\n\nPembayaran iuran {jenis_iuran} sebesar {jumlah} dengan metode {metode} telah kami terima.\n\nTerima kasih atas pembayarannya.\n\nKelurahan {kelurahan}'),
        ];
    }

    /**
     * Set template pesan notifikasi
     */
    public static function setTemplatePesan(array $data): void
    {
        foreach ($data as $key => $value) {
            self::setValue("template_{$key}", $value);
        }
    }

    /**
     * Process template dengan data dinamis
     */
    public static function processTemplate(string $template, array $data): string
    {
        foreach ($data as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }

        return $template;
    }

    /**
     * Get semua pengaturan dalam bentuk array
     */
    public static function getAllSettings(): array
    {
        return self::pluck('value', 'key')->toArray();
    }

    /**
     * Batch update pengaturan
     */
    public static function batchUpdate(array $settings): void
    {
        foreach ($settings as $key => $value) {
            self::setValue($key, $value);
        }
    }

    /**
     * Clear semua cache pengaturan
     */
    public static function clearCache(): void
    {
        $keys = self::pluck('key')->toArray();
        foreach ($keys as $key) {
            Cache::forget("pengaturan_{$key}");
        }
    }

    /**
     * Export pengaturan ke JSON
     */
    public static function exportSettings(): string
    {
        return json_encode(self::getAllSettings(), JSON_PRETTY_PRINT);
    }

    /**
     * Import pengaturan dari JSON
     */
    public static function importSettings(string $json): void
    {
        $settings = json_decode($json, true);
        if ($settings) {
            self::batchUpdate($settings);
        }
    }

    /**
     * Reset pengaturan ke default
     */
    public static function resetToDefault(): void
    {
        // Hapus semua pengaturan
        self::query()->delete();

        // Set pengaturan default
        self::setIdentitasKelurahan([
            'kelurahan_nama' => 'Kelurahan Contoh',
            'kelurahan_alamat' => 'Jl. Contoh No. 123',
            'kelurahan_telepon' => '021-1234567',
            'kelurahan_email' => 'kelurahan@example.com',
        ]);

        self::setPengaturanIuran([
            'denda_persen_harian' => 2,
            'denda_maksimal_persen' => 50,
            'hari_jatuh_tempo' => 30,
        ]);

        self::clearCache();
    }

    /**
     * Check apakah pengaturan ada
     */
    public static function hasKey(string $key): bool
    {
        return self::where('key', $key)->exists();
    }

    /**
     * Delete pengaturan
     */
    public static function deleteKey(string $key): bool
    {
        $deleted = self::where('key', $key)->delete();
        if ($deleted) {
            Cache::forget("pengaturan_{$key}");
        }
        return $deleted;
    }
}