<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KeluargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $keluargas = [
            [
                'no_kk' => '3578110101010001',
                'alamat_kk' => 'Jl. Bendul Merisi Tengas No. 12',
                'rt_kk' => '01',
                'rw_kk' => '03',
                'kelurahan_kk' => 'Bendul Merisi',
                'kecamatan_kk' => 'Wonocolo',
                'kabupaten_kk' => 'Surabaya',
                'provinsi_kk' => 'Jawa Timur',
                'status_domisili_keluarga' => 'Tetap',
                'tanggal_mulai_domisili_keluarga' => '2020-01-15',
                'keterangan_status' => 'Keluarga asli Bendul Merisi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_kk' => '3578110101010002',
                'alamat_kk' => 'Jl. Bendul Merisi Tengas No. 15',
                'rt_kk' => '01',
                'rw_kk' => '03',
                'kelurahan_kk' => 'Bendul Merisi',
                'kecamatan_kk' => 'Wonocolo',
                'kabupaten_kk' => 'Surabaya',
                'provinsi_kk' => 'Jawa Timur',
                'status_domisili_keluarga' => 'Tetap',
                'tanggal_mulai_domisili_keluarga' => '2018-06-20',
                'keterangan_status' => 'Keluarga lama Bendul Merisi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_kk' => '3578110101010003',
                'alamat_kk' => 'Jl. Bendul Merisi Barat No. 8',
                'rt_kk' => '02',
                'rw_kk' => '03',
                'kelurahan_kk' => 'Bendul Merisi',
                'kecamatan_kk' => 'Wonocolo',
                'kabupaten_kk' => 'Surabaya',
                'provinsi_kk' => 'Jawa Timur',
                'status_domisili_keluarga' => 'Non Domisili',
                'tanggal_mulai_domisili_keluarga' => '2021-03-10',
                'keterangan_status' => 'Alamat di sini, domisili di luar kota',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_kk' => '3578110101010004',
                'alamat_kk' => 'Jl. Bendul Merisi Selatan No. 25',
                'rt_kk' => '03',
                'rw_kk' => '03',
                'kelurahan_kk' => 'Bendul Merisi',
                'kecamatan_kk' => 'Wonocolo',
                'kabupaten_kk' => 'Surabaya',
                'provinsi_kk' => 'Jawa Timur',
                'status_domisili_keluarga' => 'Sementara',
                'tanggal_mulai_domisili_keluarga' => '2023-01-01',
                'keterangan_status' => 'Kontrak, akan pindah dalam 1 tahun',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_kk' => '3578110101010005',
                'alamat_kk' => 'Jl. Bendul Merisi Utara No. 7',
                'rt_kk' => '04',
                'rw_kk' => '03',
                'kelurahan_kk' => 'Bendul Merisi',
                'kecamatan_kk' => 'Wonocolo',
                'kabupaten_kk' => 'Surabaya',
                'provinsi_kk' => 'Jawa Timur',
                'status_domisili_keluarga' => 'Luar',
                'tanggal_mulai_domisili_keluarga' => '2022-09-15',
                'keterangan_status' => 'Alamat KTP di luar, tinggal di sini',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($keluargas as $keluarga) {
            DB::table('keluargas')->insert($keluarga);
        }

        $this->command->info('✅ Keluarga data seeded successfully!');
        $this->command->info('👨‍👩‍👧‍👦 Total: ' . count($keluargas) . ' keluarga');
    }
}
