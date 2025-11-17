<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wargas = [
            // KK 3578110101010001
            [
                'nik' => '3578111501850001',
                'nama_lengkap' => 'Budi Santoso',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1985-01-15',
                'jenis_kelamin' => 'L',
                'golongan_darah' => 'A',
                'alamat_ktp' => 'Jl. Bendul Merisi Tengas No. 12',
                'rt_ktp' => '01',
                'rw_ktp' => '03',
                'kelurahan_ktp' => 'Bendul Merisi',
                'kecamatan_ktp' => 'Wonocolo',
                'kabupaten_ktp' => 'Surabaya',
                'provinsi_ktp' => 'Jawa Timur',
                'agama' => 'Islam',
                'status_perkawinan' => 'Kawin',
                'pekerjaan' => 'Pegawai Swasta',
                'kewarganegaraan' => 'WNI',
                'pendidikan_terakhir' => 'S1',
                'kk_id' => 1, // Will be set after keluarga seeding
                'hubungan_keluarga' => 'Kepala Keluarga',
                'no_telepon' => '08123456789',
                'email' => 'budi.santoso@email.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nik' => '3578114505850002',
                'nama_lengkap' => 'Siti Aminah',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1985-05-15',
                'jenis_kelamin' => 'P',
                'golongan_darah' => 'O',
                'alamat_ktp' => 'Jl. Bendul Merisi Tengas No. 12',
                'rt_ktp' => '01',
                'rw_ktp' => '03',
                'kelurahan_ktp' => 'Bendul Merisi',
                'kecamatan_ktp' => 'Wonocolo',
                'kabupaten_ktp' => 'Surabaya',
                'provinsi_ktp' => 'Jawa Timur',
                'agama' => 'Islam',
                'status_perkawinan' => 'Kawin',
                'pekerjaan' => 'Ibu Rumah Tangga',
                'kewarganegaraan' => 'WNI',
                'pendidikan_terakhir' => 'SMA',
                'kk_id' => 1,
                'hubungan_keluarga' => 'Istri',
                'no_telepon' => '08123456790',
                'email' => 'siti.aminah@email.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nik' => '3578110101100001',
                'nama_lengkap' => 'Ahmad Rizki',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '2010-01-01',
                'jenis_kelamin' => 'L',
                'golongan_darah' => 'A',
                'alamat_ktp' => 'Jl. Bendul Merisi Tengas No. 12',
                'rt_ktp' => '01',
                'rw_ktp' => '03',
                'kelurahan_ktp' => 'Bendul Merisi',
                'kecamatan_ktp' => 'Wonocolo',
                'kabupaten_ktp' => 'Surabaya',
                'provinsi_ktp' => 'Jawa Timur',
                'agama' => 'Islam',
                'status_perkawinan' => 'Belum Kawin',
                'pekerjaan' => 'Pelajar',
                'kewarganegaraan' => 'WNI',
                'pendidikan_terakhir' => 'SMP',
                'kk_id' => 1,
                'hubungan_keluarga' => 'Anak',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // KK 3578110101010002
            [
                'nik' => '3578112007750001',
                'nama_lengkap' => 'Ahmad Fadli',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1975-07-20',
                'jenis_kelamin' => 'L',
                'golongan_darah' => 'B',
                'alamat_ktp' => 'Jl. Bendul Merisi Tengas No. 15',
                'rt_ktp' => '01',
                'rw_ktp' => '03',
                'kelurahan_ktp' => 'Bendul Merisi',
                'kecamatan_ktp' => 'Wonocolo',
                'kabupaten_ktp' => 'Surabaya',
                'provinsi_ktp' => 'Jawa Timur',
                'agama' => 'Islam',
                'status_perkawinan' => 'Kawin',
                'pekerjaan' => 'Wiraswasta',
                'kewarganegaraan' => 'WNI',
                'pendidikan_terakhir' => 'D1/D2/D3',
                'kk_id' => 2,
                'hubungan_keluarga' => 'Kepala Keluarga',
                'no_telepon' => '08123456791',
                'email' => 'ahmad.fadli@email.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // KK 3578110101010003
            [
                'nik' => '3578111003850001',
                'nama_lengkap' => 'Dewi Ratna Sari',
                'tempat_lahir' => 'Malang',
                'tanggal_lahir' => '1985-03-10',
                'jenis_kelamin' => 'P',
                'golongan_darah' => 'AB',
                'alamat_ktp' => 'Jl. Bendul Merisi Barat No. 8',
                'rt_ktp' => '02',
                'rw_ktp' => '03',
                'kelurahan_ktp' => 'Bendul Merisi',
                'kecamatan_ktp' => 'Wonocolo',
                'kabupaten_ktp' => 'Surabaya',
                'provinsi_ktp' => 'Jawa Timur',
                'agama' => 'Islam',
                'status_perkawinan' => 'Cerai Hidup',
                'pekerjaan' => 'Karyawan Swasta',
                'kewarganegaraan' => 'WNI',
                'pendidikan_terakhir' => 'S1',
                'kk_id' => 3,
                'hubungan_keluarga' => 'Kepala Keluarga',
                'no_telepon' => '08123456792',
                'email' => 'dewi.ratna@email.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($wargas as $warga) {
            DB::table('wargas')->insert($warga);
        }

        // Update kepala_keluarga_id in keluargas table
        DB::table('keluargas')->where('id', 1)->update(['kepala_keluarga_id' => 1]); // Budi Santoso
        DB::table('keluargas')->where('id', 2)->update(['kepala_keluarga_id' => 4]); // Ahmad Fadli
        DB::table('keluargas')->where('id', 3)->update(['kepala_keluarga_id' => 5]); // Dewi Ratna Sari

        $this->command->info('✅ Warga data seeded successfully!');
        $this->command->info('👥 Total: ' . count($wargas) . ' warga');
    }
}
