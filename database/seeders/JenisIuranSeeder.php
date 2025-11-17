<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisIuranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('jenis_iurans')->delete();

        $jenisIuran = [
            [
                'nama' => 'Iuran Kebersihan',
                'deskripsi' => 'Iuran rutin untuk kebersihan lingkungan RT/RW',
                'jumlah' => 25000,
                'periode' => 'bulanan',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Iuran Keamanan',
                'deskripsi' => 'Iuran rutin untuk keamanan lingkungan RT/RW',
                'jumlah' => 20000,
                'periode' => 'bulanan',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Iuran Pembangunan',
                'deskripsi' => 'Iuran khusus untuk pembangunan fasilitas umum',
                'jumlah' => 100000,
                'periode' => 'tahunan',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Iuran Sampah',
                'deskripsi' => 'Iuran untuk pengelolaan sampah lingkungan',
                'jumlah' => 15000,
                'periode' => 'bulanan',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Iuran Acara 17 Agustus',
                'deskripsi' => 'Iuran khusus untuk perayaan Hari Kemerdekaan RI',
                'jumlah' => 50000,
                'periode' => 'sekali',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Iuran Kerja Bakti',
                'deskripsi' => 'Iuran untuk kegiatan kerja bakti rutin',
                'jumlah' => 10000,
                'periode' => 'bulanan',
                'status' => 'aktif',
            ],
        ];

        // Insert all jenis iuran
        DB::table('jenis_iurans')->insert($jenisIuran);

        $this->command->info('✅ Jenis Iuran data seeded successfully!');
        $this->command->info('💰 Total: ' . count($jenisIuran) . ' jenis iuran');
    }
}