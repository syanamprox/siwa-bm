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
                'kode' => 'IK',
                'jumlah' => 25000,
                'periode' => 'bulanan',
                'keterangan' => 'Iuran rutin untuk kebersihan lingkungan RT/RW',
                'is_aktif' => 1,
                'sasaran' => 'kk',
            ],
            [
                'nama' => 'Iuran Keamanan',
                'kode' => 'IKM',
                'jumlah' => 30000,
                'periode' => 'bulanan',
                'keterangan' => 'Iuran rutin untuk keamanan lingkungan RT/RW',
                'is_aktif' => 1,
                'sasaran' => 'kk',
            ],
            [
                'nama' => 'Iuran Sosial/Kematian',
                'kode' => 'IS',
                'jumlah' => 10000,
                'periode' => 'bulanan',
                'keterangan' => 'Iuran untuk dana sosial dan kematian warga',
                'is_aktif' => 1,
                'sasaran' => 'kk',
            ],
            [
                'nama' => 'Iuran Kampung',
                'kode' => 'IKMP',
                'jumlah' => 10000,
                'periode' => 'bulanan',
                'keterangan' => 'Iuran untuk kegiatan rutin kampung dan fasilitas umum',
                'is_aktif' => 1,
                'sasaran' => 'kk',
            ],
            [
                'nama' => 'Iuran Acara 17 Agustus',
                'kode' => 'IA17',
                'jumlah' => 50000,
                'periode' => 'tahunan',
                'keterangan' => 'Iuran khusus untuk perayaan Hari Kemerdekaan RI',
                'is_aktif' => 1,
                'sasaran' => 'kk',
            ],
        ];

        // Insert all jenis iuran
        DB::table('jenis_iurans')->insert($jenisIuran);

        $this->command->info('✅ Jenis Iuran data seeded successfully!');
        $this->command->info('💰 Total: ' . count($jenisIuran) . ' jenis iuran');
    }
}