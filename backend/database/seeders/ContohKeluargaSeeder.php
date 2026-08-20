<?php

namespace Database\Seeders;

use App\Models\Keluarga;
use App\Models\Warga;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seeder 1 keluarga contoh: ayah, ibu, 4 anak (2 L, 2 P).
 * Idempotent (updateOrCreate) — aman dijalankan berulang.
 * Jalur DB: keluarga → warga → link kepala_keluarga (wargas.kk_id constraint-nya delayed).
 */
class ContohKeluargaSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = 1;

        // Ambil RT pertama sebagai domisili (RT 01 RW 01 Bendul Merisi)
        $rt = \App\Models\Wilayah::where('tingkat', 'RT')->orderBy('id')->first();

        $keluarga = Keluarga::updateOrCreate(
            ['no_kk' => '3578201234560001'],
            [
                'alamat_kk' => 'Jl. Bendul Merisi No. 25',
                'rt_kk' => '001',
                'rw_kk' => '001',
                'kelurahan_kk' => 'Bendul Merisi',
                'kecamatan_kk' => 'Wonocolo',
                'kabupaten_kk' => 'Kota Surabaya',
                'provinsi_kk' => 'Jawa Timur',
                'status_keluarga' => 'Tetap',
                'alamat_domisili' => 'Jl. Bendul Merisi No. 25',
                'rt_id' => $rt?->id,
                'tanggal_mulai_domisili_keluarga' => '2010-01-01',
            ]
        );

        $anggota = [
            // [nik, nama, tempat/tgl lahir, JK, goldar, kawin, pekerjaan, pendidikan, hubungan, ayah, ibu]
            ['3578201001780001', 'Ahmad Fauzi', 'Surabaya', '1978-01-10', 'L', 'O', 'Kawin', 'Karyawan Swasta', 'S1', 'Kepala Keluarga', 'Muhammad Yusuf', 'Halimah'],
            ['3578205110820002', 'Siti Rahayu', 'Surabaya', '1982-11-10', 'P', 'A', 'Kawin', 'Ibu Rumah Tangga', 'SMA', 'Istri', 'Abdul Karim', 'Fatimah'],
            ['3578200503050003', 'Budi Pratama', 'Surabaya', '2005-03-05', 'L', 'B', 'Belum Kawin', 'Pelajar', 'SMA', 'Anak', 'Ahmad Fauzi', 'Siti Rahayu'],
            ['3578202407070004', 'Rizky Saputra', 'Surabaya', '2007-07-24', 'L', 'O', 'Belum Kawin', 'Pelajar', 'SMA', 'Anak', 'Ahmad Fauzi', 'Siti Rahayu'],
            ['3578206003100005', 'Dewi Lestari', 'Surabaya', '2010-03-20', 'P', 'AB', 'Belum Kawin', 'Pelajar', 'SMA', 'Anak', 'Ahmad Fauzi', 'Siti Rahayu'],
            ['3578204812130006', 'Putri Maharani', 'Surabaya', '2013-12-08', 'P', 'A', 'Belum Kawin', 'Pelajar', 'SMP', 'Anak', 'Ahmad Fauzi', 'Siti Rahayu'],
        ];

        $kepala = null;
        foreach ($anggota as [$nik, $nama, $tempat, $tgl, $jk, $goldar, $kawin, $kerja, $pendidikan, $hubungan, $namaAyah, $namaIbu]) {
            $warga = Warga::updateOrCreate(
                ['nik' => $nik],
                [
                    'nama_lengkap' => $nama,
                    'tempat_lahir' => $tempat,
                    'tanggal_lahir' => $tgl,
                    'jenis_kelamin' => $jk,
                    'golongan_darah' => $goldar,
                    'agama' => 'Islam',
                    'status_perkawinan' => $kawin,
                    'pekerjaan' => $kerja,
                    'pendidikan_terakhir' => $pendidikan,
                    'kewarganegaraan' => 'WNI',
                    'kk_id' => $keluarga->id,
                    'hubungan_keluarga' => $hubungan,
                    'nama_ayah' => $namaAyah,
                    'nama_ibu' => $namaIbu,
                    'no_telepon' => $hubungan === 'Kepala Keluarga' ? '081234567890' : null,
                    'created_by' => $adminId,
                ]
            );

            if ($hubungan === 'Kepala Keluarga') {
                $kepala = $warga;
            }
        }

        // Link kepala keluarga (delayed — setelah semua warga ada)
        if ($kepala && $keluarga->kepala_keluarga_id !== $kepala->id) {
            $keluarga->update(['kepala_keluarga_id' => $kepala->id]);
        }

        $this->command->info("✅ Keluarga contoh {$keluarga->no_kk} ({$kepala?->nama_lengkap}) + 6 anggota — RT: {$rt?->nama}");
    }
}
