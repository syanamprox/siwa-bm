<?php

namespace Database\Seeders;

use App\Models\Keluarga;
use App\Models\Warga;
use App\Models\Wilayah;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * SEEDER DATA DUMMY — RT 02 RW 03 Kelurahan Bendul Merisi.
 *
 * ⚠️ SEMUA NIK / no_kk / nama SINTETIS (bukan warga nyata):
 *   - NIK     = 357820 + YYMMDD (perempuan: tanggal +40, aturan SIAK) + serial 9xxx
 *   - no_kk   = 357820 + DDMM lahir kepala + blok 9xxxxx
 * Serial deterministik (9000 + idxKK*10 + idxAnggota) → idempotent & stabil antar-run.
 *
 * Placeholder sampai hasil ekstraksi 78 foto KK asli siap — struktur data
 * (array FAMILIES) tinggal ditimpa dengan data real, kode generator tidak berubah.
 *
 * Idempotent (updateOrCreate by no_kk / nik).
 */
class Rt02Rw03BendulMerisiSeeder extends Seeder
{
    use WithoutModelEvents;

    /** @var array<int, array{dob:string, alamat:string, anggota: list<array{0:string,1:string,2:string,3:string,4:?string,5:string,6:string,7:string,8:string,9:?string,10:?string}>}> */
    private const FAMILIES = [
        // ── 1. Keluarga lengkap + orang tua ikut KK
        ['dob' => '1206', 'alamat' => 'Jl. Bendul Merisi No. 12', 'anggota' => [
            ['Slamet Riyadi', 'Surabaya', '1963-06-12', 'L', 'B', 'Kawin', 'Wiraswasta', 'D4/S1', 'Kepala Keluarga', 'Muhammad Ali', 'Siti Fatimah'],
            ['Nurhayati', 'Surabaya', '1966-02-20', 'P', 'O', 'Kawin', 'Mengurus Rumah Tangga', 'SMA/sederajat', 'Istri', 'Abdul Rahman', 'Halimah'],
            ['Rizal Fadillah', 'Surabaya', '1998-09-14', 'L', 'B', 'Belum Kawin', 'Pelajar/Mahasiswa', 'S2', 'Anak', 'Slamet Riyadi', 'Nurhayati'],
            ['Kartono Riyadi', 'Surabaya', '1938-03-03', 'L', 'A', 'Cerai Mati', 'Pensiunan', 'Tidak Sekolah', 'Orang Tua', 'Unknown', 'Unknown'],
        ]],
        // ── 2. Keluarga buruh 3 anak
        ['dob' => '0402', 'alamat' => 'Jl. Bendul Merisi No. 14', 'anggota' => [
            ['Bambang Sutrisno', 'Sidoarjo', '1979-02-04', 'L', 'O', 'Kawin', 'Buruh Harian Lepas', 'SMP/sederajat', 'Kepala Keluarga', 'Sukarman', 'Partini'],
            ['Siti Aminah', 'Surabaya', '1982-07-19', 'P', 'A', 'Kawin', 'Mengurus Rumah Tangga', 'SMP/sederajat', 'Istri', 'Mahmud', 'Nur Aini'],
            ['Fajar Sutrisno', 'Surabaya', '2005-11-08', 'L', 'O', 'Belum Kawin', 'Pelajar/Mahasiswa', 'SMA/sederajat', 'Anak', 'Bambang Sutrisno', 'Siti Aminah'],
            ['Intan Permata', 'Surabaya', '2009-04-22', 'P', 'A', 'Belum Kawin', 'Pelajar/Mahasiswa', 'SMP/sederajat', 'Anak', 'Bambang Sutrisno', 'Siti Aminah'],
            ['Ardi Sutrisno', 'Surabaya', '2014-08-30', 'L', 'O', 'Belum Kawin', 'Pelajar/Mahasiswa', 'SD/sederajat', 'Anak', 'Bambang Sutrisno', 'Siti Aminah'],
        ]],
        // ── 3. Keluarga sopir 2 anak
        ['dob' => '2311', 'alamat' => 'Jl. Bendul Merisi No. 15', 'anggota' => [
            ['Joko Susilo', 'Surabaya', '1986-11-23', 'L', 'B', 'Kawin', 'Sopir', 'SMA/sederajat', 'Kepala Keluarga', 'Sutarto', 'Yatmi'],
            ['Dewi Susanti', 'Surabaya', '1989-01-05', 'P', 'AB', 'Kawin', 'Pedagang', 'SMA/sederajat', 'Istri', 'Hasan', 'Marsini'],
            ['Raka Susilo', 'Surabaya', '2013-05-17', 'L', 'B', 'Belum Kawin', 'Pelajar/Mahasiswa', 'SD/sederajat', 'Anak', 'Joko Susilo', 'Dewi Susanti'],
            ['Nadia Susilo', 'Surabaya', '2017-09-02', 'P', 'AB', 'Belum Kawin', 'Pelajar/Mahasiswa', 'SD/sederajat', 'Anak', 'Joko Susilo', 'Dewi Susanti'],
        ]],
        // ── 4. Janda/pria duda (cerai mati) kepala KK + 2 anak
        ['dob' => '1707', 'alamat' => 'Jl. Bendul Merisi Gg. II No. 3', 'anggota' => [
            ['Hadi Purnomo', 'Malang', '1972-07-17', 'L', 'A', 'Cerai Mati', 'Pensiunan', 'SMA/sederajat', 'Kepala Keluarga', 'Sutomo', 'Sulastri'],
            ['Wulan Purnomo', 'Malang', '2002-12-11', 'P', 'A', 'Belum Kawin', 'Pelajar/Mahasiswa', 'D4/S1', 'Anak', 'Hadi Purnomo', 'Ratna Dewi'],
            ['Gilang Purnomo', 'Malang', '2007-06-25', 'L', 'A', 'Belum Kawin', 'Pelajar/Mahasiswa', 'SMA/sederajat', 'Anak', 'Hadi Purnomo', 'Ratna Dewi'],
        ]],
        // ── 5. Keluarga muda + balita
        ['dob' => '0903', 'alamat' => 'Jl. Bendul Merisi Gg. II No. 5', 'anggota' => [
            ['Andi Prasetyo', 'Surabaya', '1996-03-09', 'L', 'O', 'Kawin', 'Karyawan Swasta', 'D4/S1', 'Kepala Keluarga', 'Bambang Prasetyo', 'Untung Sri'],
            ['Rina Marlina', 'Gresik', '1998-10-27', 'P', 'B', 'Kawin', 'Mengurus Rumah Tangga', 'D3', 'Istri', 'Zainuddin', 'Halimah'],
            ['Kayla Prasetyo', 'Surabaya', '2023-02-14', 'P', 'B', 'Belum Kawin', 'Belum/Tidak Bekerja', 'Tidak Sekolah', 'Anak', 'Andi Prasetyo', 'Rina Marlina'],
        ]],
        // ── 6. Lansia pasutri
        ['dob' => '2804', 'alamat' => 'Jl. Bendul Merisi No. 18', 'anggota' => [
            ['Moch. Saleh', 'Surabaya', '1954-04-28', 'L', 'A', 'Kawin', 'Pensiunan', 'SD/sederajat', 'Kepala Keluarga', 'Ibrahim', 'Fatimah'],
            ['Khodijah', 'Surabaya', '1957-08-15', 'P', 'O', 'Kawin', 'Mengurus Rumah Tangga', 'Tidak Sekolah', 'Istri', 'Saman', 'Aminah'],
        ]],
        // ── 7. Single person KK (pindahan)
        ['dob' => '3101', 'alamat' => 'Jl. Bendul Merisi Gg. III No. 8', 'anggota' => [
            ['Yusuf Maulana', 'Jember', '1991-01-31', 'L', 'B', 'Belum Kawin', 'Guru', 'S2', 'Kepala Keluarga', 'Abdul Malik', 'Siti Khadijah'],
        ]],
        // ── 8. Keluarga + mertua ikut KK
        ['dob' => '1505', 'alamat' => 'Jl. Bendul Merisi Gg. III No. 9', 'anggota' => [
            ['Budi Santoso', 'Surabaya', '1984-05-15', 'L', 'O', 'Kawin', 'Karyawan Swasta', 'D3', 'Kepala Keluarga', 'Anton Santoso', 'Ratna'],
            ['Endang Wahyuni', 'Surabaya', '1986-12-01', 'P', 'A', 'Kawin', 'Mengurus Rumah Tangga', 'SMA/sederajat', 'Istri', 'Suparman', 'Ngatirah'],
            ['Dimas Santoso', 'Surabaya', '2012-03-19', 'L', 'O', 'Belum Kawin', 'Pelajar/Mahasiswa', 'SD/sederajat', 'Anak', 'Budi Santoso', 'Endang Wahyuni'],
            ['Salsa Santoso', 'Surabaya', '2016-07-07', 'P', 'A', 'Belum Kawin', 'Pelajar/Mahasiswa', 'Tidak Sekolah', 'Anak', 'Budi Santoso', 'Endang Wahyuni'],
            ['Suparman', 'Surabaya', '1956-09-09', 'L', 'Tidak Tahu', 'Kawin', 'Pensiunan', 'SD/sederajat', 'Mertua', 'Unknown', 'Unknown'],
        ]],
        // ── 9. Keluarga + cucu tinggal bersama
        ['dob' => '0209', 'alamat' => 'Jl. Bendul Merisi No. 20', 'anggota' => [
            ['Karim', 'Surabaya', '1968-09-02', 'L', 'B', 'Kawin', 'Pedagang', 'SMP/sederajat', 'Kepala Keluarga', 'Mochtar', 'Nyai'],
            ['Maryam', 'Surabaya', '1970-04-13', 'P', 'AB', 'Kawin', 'Pedagang', 'SMP/sederajat', 'Istri', 'Rustam', 'Saenah'],
            ['Fahmi Karim', 'Surabaya', '1995-02-21', 'L', 'B', 'Belum Kawin', 'Karyawan Swasta', 'D4/S1', 'Anak', 'Karim', 'Maryam'],
            ['Aliya Karim', 'Surabaya', '2019-11-30', 'P', 'B', 'Belum Kawin', 'Belum/Tidak Bekerja', 'Tidak Sekolah', 'Cucu', 'Fahmi Karim', 'Unknown'],
        ]],
        // ── 10. Keluarga buruh 4 anak
        ['dob' => '1208', 'alamat' => 'Jl. Bendul Merisi Gg. IV No. 2', 'anggota' => [
            ['Sukirman', 'Nganjuk', '1976-08-12', 'L', 'O', 'Kawin', 'Buruh Harian Lepas', 'SD/sederajat', 'Kepala Keluarga', 'Wiryo', 'Suparni'],
            ['Sri Wahyuni', 'Nganjuk', '1978-06-06', 'P', 'B', 'Kawin', 'Mengurus Rumah Tangga', 'SD/sederajat', 'Istri', 'Darmo', 'Tumini'],
            ['Bagus Sukirman', 'Surabaya', '2002-10-10', 'L', 'O', 'Belum Kawin', 'Buruh Harian Lepas', 'SMA/sederajat', 'Anak', 'Sukirman', 'Sri Wahyuni'],
            ['Dwi Sukirman', 'Surabaya', '2006-01-18', 'L', 'O', 'Belum Kawin', 'Pelajar/Mahasiswa', 'SMP/sederajat', 'Anak', 'Sukirman', 'Sri Wahyuni'],
            ['Seka Sukirman', 'Surabaya', '2010-05-23', 'P', 'B', 'Belum Kawin', 'Pelajar/Mahasiswa', 'SD/sederajat', 'Anak', 'Sukirman', 'Sri Wahyuni'],
            ['Bayu Sukirman', 'Surabaya', '2015-12-05', 'L', 'O', 'Belum Kawin', 'Pelajar/Mahasiswa', 'SD/sederajat', 'Anak', 'Sukirman', 'Sri Wahyuni'],
        ]],
        // ── 11. Keluarga wiraswasta
        ['dob' => '0610', 'alamat' => 'Jl. Bendul Merisi Gg. IV No. 6', 'anggota' => [
            ['Agus Wibowo', 'Surabaya', '1988-10-06', 'L', 'A', 'Kawin', 'Wiraswasta', 'D4/S1', 'Kepala Keluarga', 'Hendra Wibowo', 'Sri Lestari'],
            ['Fitri Handayani', 'Surabaya', '1990-03-12', 'P', 'O', 'Kawin', 'Perawat', 'D3', 'Istri', 'Sugeng', 'Wahyuni'],
            ['Arka Wibowo', 'Surabaya', '2018-08-21', 'L', 'A', 'Belum Kawin', 'Belum/Tidak Bekerja', 'Tidak Sekolah', 'Anak', 'Agus Wibowo', 'Fitri Handayani'],
            ['SENja Wibowo', 'Surabaya', '2021-04-04', 'P', 'O', 'Belum Kawin', 'Belum/Tidak Bekerja', 'Tidak Sekolah', 'Anak', 'Agus Wibowo', 'Fitri Handayani'],
        ]],
        // ── 12. Keluarga Kristen (minoritas RT)
        ['dob' => '1902', 'alamat' => 'Jl. Bendul Merisi No. 22', 'anggota' => [
            ['Hendrik Gunawan', 'Surabaya', '1974-02-19', 'L', 'B', 'Kawin', 'Guru', 'S2', 'Kepala Keluarga', 'Yohanes Gunawan', 'Martha'],
            ['Ratna Gunawan', 'Surabaya', '1977-11-25', 'P', 'A', 'Kawin', 'Guru', 'D3', 'Istri', 'Simanjuntak', 'Berenice'],
            ['Stefanus Gunawan', 'Surabaya', '2003-06-16', 'L', 'B', 'Belum Kawin', 'Pelajar/Mahasiswa', 'D4/S1', 'Anak', 'Hendrik Gunawan', 'Ratna Gunawan'],
        ]],
    ];

    public function run(): void
    {
        $rt = Wilayah::where('nama', 'RT 02 RW 03 Bendul Merisi')->where('tingkat', 'RT')->first();
        if (! $rt) {
            $this->command->warn('⚠️  Wilayah RT 02 RW 03 Bendul Merisi tidak ditemukan — seeder dilewati.');

            return;
        }

        $adminId = 1;
        $total = 0;

        // Variasi status domisili biar semua badge teruji (sisanya Tetap)
        $statusByIndex = [2 => 'Domisili', 6 => 'Non Domisili', 10 => 'Pendatang'];

        foreach (self::FAMILIES as $fi => $fam) {
            $noKk = '357820'.$fam['dob'].'9'.str_pad((string) ($fi + 1), 5, '0', STR_PAD_LEFT);

            $keluarga = Keluarga::updateOrCreate(
                ['no_kk' => $noKk],
                [
                    'alamat_kk' => $fam['alamat'],
                    'rt_kk' => '002',
                    'rw_kk' => '003',
                    'kelurahan_kk' => 'Bendul Merisi',
                    'kecamatan_kk' => 'Wonocolo',
                    'kabupaten_kk' => 'Kota Surabaya',
                    'provinsi_kk' => 'Jawa Timur',
                    'status_keluarga' => $statusByIndex[$fi] ?? 'Tetap',
                    'alamat_domisili' => $fam['alamat'],
                    'rt_id' => $rt->id,
                    'tanggal_mulai_domisili_keluarga' => '2015-01-01',
                ]
            );

            $kepala = null;
            foreach ($fam['anggota'] as $mi => [$nama, $tempat, $tgl, $jk, $goldar, $kawin, $kerja, $pendidikan, $hubungan, $ayah, $ibu]) {
                $warga = Warga::updateOrCreate(
                    ['nik' => $this->nik($tgl, $jk, $fi, $mi)],
                    [
                        'nama_lengkap' => $nama,
                        'tempat_lahir' => $tempat,
                        'tanggal_lahir' => $tgl,
                        'jenis_kelamin' => $jk,
                        'golongan_darah' => $goldar,
                        'agama' => str_contains($nama, 'Gunawan') ? 'Kristen' : 'Islam',
                        'status_perkawinan' => $kawin,
                        'pekerjaan' => $kerja,
                        'pendidikan_terakhir' => $pendidikan,
                        'kewarganegaraan' => 'WNI',
                        'kk_id' => $keluarga->id,
                        'hubungan_keluarga' => $hubungan,
                        'nama_ayah' => $ayah,
                        'nama_ibu' => $ibu,
                        'no_telepon' => $hubungan === 'Kepala Keluarga' ? '08'.str_pad((string) (5000000000 + $fi * 11111111), 10, '0') : null,
                        'created_by' => $adminId,
                    ]
                );

                if ($hubungan === 'Kepala Keluarga') {
                    $kepala = $warga;
                }
                $total++;
            }

            if ($kepala && $keluarga->kepala_keluarga_id !== $kepala->id) {
                $keluarga->update(['kepala_keluarga_id' => $kepala->id]);
            }
        }

        $this->command->info('✅ RT 02 RW 03 Bendul Merisi: '.count(self::FAMILIES)." KK dummy + {$total} warga (data sintetis, siap ditimpa hasil ekstraksi KK asli).");
    }

    /**
     * NIK 16 digit format SIAK: 357820 + YYMMDD (P: tanggal +40) + serial deterministik.
     */
    private function nik(string $tgl, string $jk, int $fi, int $mi): string
    {
        [$y, $m, $d] = explode('-', $tgl);
        if ($jk === 'P') {
            $d = (int) $d + 40;
        }

        return '357820'.substr($y, 2).$m.str_pad((string) $d, 2, '0', STR_PAD_LEFT).(string) (9000 + $fi * 10 + $mi);
    }
}
