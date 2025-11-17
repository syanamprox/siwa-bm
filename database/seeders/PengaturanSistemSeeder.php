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

            // Keuangan
            [
                'key' => 'mata_uang',
                'value' => 'IDR',
                'tipe' => 'text',
                'kategori' => 'keuangan',
                'deskripsi' => 'Mata Uang Default',
            ],
            [
                'key' => 'format_nomor',
                'value' => 'id_ID',
                'tipe' => 'text',
                'kategori' => 'keuangan',
                'deskripsi' => 'Format Penulisan Nomor',
            ],
        ];

        // Insert all settings
        DB::table('pengaturan_sistems')->insert($pengaturan);

        $this->command->info('✅ Pengaturan Sistem data seeded successfully!');
        $this->command->info('⚙️ Total: ' . count($pengaturan) . ' pengaturan sistem');
    }
}