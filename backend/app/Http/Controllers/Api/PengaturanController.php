<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\LogsActivity;
use App\Models\PengaturanSistem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PengaturanController extends Controller
{
    use LogsActivity;

    /** Key yang boleh diubah via API — di luar ini ditolak. */
    private const ALLOWED_KEYS = [
        'nama_aplikasi', 'versi_aplikasi', 'zona_waktu', 'format_tanggal', 'format_nomor', 'mata_uang',
        'nama_kelurahan', 'nama_kecamatan', 'nama_kabupaten', 'nama_provinsi',
        'telepon_kantor', 'email_kantor', 'alamat_kantor',
        'maks_login', 'timeout_sesi', 'log_semua_aktivitas',
        'auto_post_kas_iuran',
    ];

    /** Key canonical → [default, keterangan] */
    private function spec(): array
    {
        return [
            'app' => [
                ['nama_aplikasi', 'SIWA - Sistem Informasi Warga', 'Nama Aplikasi'],
                ['versi_aplikasi', '1.0.0', 'Versi Aplikasi'],
                ['zona_waktu', 'Asia/Jakarta', 'Zona Waktu'],
                ['format_tanggal', 'd/m/Y', 'Format Tanggal'],
                ['format_nomor', 'id_ID', 'Format Penulisan Nomor'],
                ['mata_uang', 'IDR', 'Mata Uang'],
            ],
            'kelurahan' => [
                ['nama_kelurahan', 'Bendul Merisi', 'Nama Kelurahan'],
                ['nama_kecamatan', 'Wonocolo', 'Nama Kecamatan'],
                ['nama_kabupaten', 'Kota Surabaya', 'Nama Kabupaten/Kota'],
                ['nama_provinsi', 'Jawa Timur', 'Nama Provinsi'],
                ['telepon_kantor', '', 'Telepon Kantor Kelurahan'],
                ['email_kantor', '', 'Email Kantor Kelurahan'],
                ['alamat_kantor', '', 'Alamat Kantor Kelurahan'],
            ],
            'keamanan' => [
                ['maks_login', '5', 'Batas Percobaan Login Gagal'],
                ['timeout_sesi', '120', 'Sesi Login Berakhir Setelah (menit)'],
                ['log_semua_aktivitas', '1', 'Catat Semua Aktivitas Petugas'],
            ],
            'keuangan' => [
                // Mati (0) by default: bendahara mencatat kas di buku fisik — pembayaran
                // iuran via app TIDAK otomatis masuk kas (hindari double-entry).
                // Nyalakan (1) saat kas app menjadi sumber tunggal (kepengurusan baru).
                ['auto_post_kas_iuran', '0', 'Otomatis Catat Pembayaran Iuran ke Kas'],
            ],
        ];
    }

    /**
     * GET /api/pengaturan — grouped settings (dengan default).
     */
    public function index(): JsonResponse
    {
        $groups = [];
        foreach ($this->spec() as $group => $items) {
            $groups[$group] = array_map(
                fn ($i) => ['key' => $i[0], 'value' => PengaturanSistem::getValue($i[0], $i[1]), 'keterangan' => $i[2]],
                $items
            );
        }

        return response()->json(['data' => $groups]);
    }

    /**
     * PUT /api/pengaturan — update banyak sekaligus [{key, value}].
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'settings' => ['required', 'array', 'min:1'],
            'settings.*.key' => ['required', 'string', 'max:255', Rule::in(self::ALLOWED_KEYS)],
            'settings.*.value' => ['required', 'string', 'max:1000'],
        ]);

        foreach ($validated['settings'] as $setting) {
            PengaturanSistem::setValue($setting['key'], $setting['value']);
        }
        $this->logActivity($request, 'update', 'pengaturan', 'Update pengaturan sistem', null, ['keys' => array_column($validated['settings'], 'key')]);

        return response()->json(['message' => 'Pengaturan tersimpan.']);
    }
}
