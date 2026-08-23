<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengaturanSistemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('pengaturan_sistems')->delete();

        $pengaturan = [
            // Umum
            [
                'key' => 'nama_kelurahan',
                'value' => 'Bendul Merisi',
                'tipe' => 'text',
                'kategori' => 'umum',
                'deskripsi' => 'Nama Kelurahan',
            ],
            [
                'key' => 'nama_kecamatan',
                'value' => 'Wonocolo',
                'tipe' => 'text',
                'kategori' => 'umum',
                'deskripsi' => 'Nama Kecamatan',
            ],
            [
                'key' => 'nama_kabupaten',
                'value' => 'Surabaya',
                'tipe' => 'text',
                'kategori' => 'umum',
                'deskripsi' => 'Nama Kabupaten/Kota',
            ],
            [
                'key' => 'nama_provinsi',
                'value' => 'Jawa Timur',
                'tipe' => 'text',
                'kategori' => 'umum',
                'deskripsi' => 'Nama Provinsi',
            ],

            // Aplikasi
            [
                'key' => 'nama_aplikasi',
                'value' => 'SIWA - Sistem Informasi Warga',
                'tipe' => 'text',
                'kategori' => 'aplikasi',
                'deskripsi' => 'Nama Aplikasi',
            ],
            [
                'key' => 'versi_aplikasi',
                'value' => '1.0.0',
                'tipe' => 'text',
                'kategori' => 'aplikasi',
                'deskripsi' => 'Versi Aplikasi',
            ],
            [
                'key' => 'zona_waktu',
                'value' => 'Asia/Jakarta',
                'tipe' => 'select',
                'kategori' => 'aplikasi',
                'deskripsi' => 'Zona Waktu',
            ],
            [
                'key' => 'format_tanggal',
                'value' => 'd/m/Y',
                'tipe' => 'select',
                'kategori' => 'aplikasi',
                'deskripsi' => 'Format Tanggal',
            ],
            [
                'key' => 'format_nomor',
                'value' => 'id_ID',
                'tipe' => 'select',
                'kategori' => 'aplikasi',
                'deskripsi' => 'Format Penulisan Nomor',
            ],
            [
                'key' => 'mata_uang',
                'value' => 'IDR',
                'tipe' => 'select',
                'kategori' => 'aplikasi',
                'deskripsi' => 'Mata Uang',
            ],
            [
                'key' => 'logo_aplikasi',
                'value' => 'images/logo.png',
                'tipe' => 'file',
                'kategori' => 'aplikasi',
                'deskripsi' => 'Logo Aplikasi',
            ],

            // Kontak
            [
                'key' => 'alamat_kantor',
                'value' => 'Jl. Bendul Merisi Tengas No.123, Surabaya',
                'tipe' => 'textarea',
                'kategori' => 'kontak',
                'deskripsi' => 'Alamat Kantor Kelurahan',
            ],
            [
                'key' => 'telepon_kantor',
                'value' => '(031) 8414251',
                'tipe' => 'text',
                'kategori' => 'kontak',
                'deskripsi' => 'Telepon Kantor Kelurahan',
            ],
            [
                'key' => 'email_kantor',
                'value' => 'bendulmerisi@surabaya.go.id',
                'tipe' => 'email',
                'kategori' => 'kontak',
                'deskripsi' => 'Email Kantor Kelurahan',
            ],

            // Keamanan
            [
                'key' => 'maks_login',
                'value' => '5',
                'tipe' => 'select',
                'kategori' => 'keamanan',
                'deskripsi' => 'Batas Percobaan Login Gagal',
            ],
            [
                'key' => 'timeout_sesi',
                'value' => '120',
                'tipe' => 'select',
                'kategori' => 'keamanan',
                'deskripsi' => 'Sesi Login Berakhir Setelah',
            ],
            [
                'key' => 'log_semua_aktivitas',
                'value' => '1',
                'tipe' => 'select',
                'kategori' => 'keamanan',
                'deskripsi' => 'Catat Semua Aktivitas Petugas',
            ],
            [
                // 0 = bendahara mencatat kas di buku fisik (default, hindari double-entry).
                // 1 = pembayaran iuran via app otomatis masuk kas (kas app sumber tunggal).
                'key' => 'auto_post_kas_iuran',
                'value' => '0',
                'tipe' => 'select',
                'kategori' => 'keuangan',
                'deskripsi' => 'Otomatis Catat Pembayaran Iuran ke Kas',
            ],
        ];

        // Insert all settings
        DB::table('pengaturan_sistems')->insert($pengaturan);

        $this->command->info('✅ Pengaturan Sistem data seeded successfully!');
        $this->command->info('⚙️ Total: ' . count($pengaturan) . ' pengaturan sistem');
    }
}