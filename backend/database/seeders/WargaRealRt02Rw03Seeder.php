<?php

namespace Database\Seeders;

use App\Models\Keluarga;
use App\Models\Warga;
use App\Models\Wilayah;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * DATA WARGA REAL RT 02 RW 03 Kelurahan Bendul Merisi — hasil ekstraksi 74 berkas KK
 * (kk/DATA_WARGA.md, 2026-08-20). 67 KK valid · 214 warga · 67 foto KK.
 *
 * Foto KK disalin otomatis dari arsip kk/KK Warga/ (gitignored, PII) ke public/kk/
 * sesuai SRC_MAP — folder arsip tetap utuh, hanya dibaca.
 *
 * Kategori arsip RT → status domisili: A1 Tetap (53) · A2 Domisili (7) ·
 * A3 Non Domisili (5) · A4 Pendatang (2). Semua KK terdaftar domisili RT 02 RW 03.
 *
 * Dihapus dari seeder: A4 Supardi (No. KK tak terbaca) & duplikat folder Jufri —
 * lihat "Perlu Verifikasi Ulang" di DATA_WARGA.md. NIK dengan kualitas baca rendah
 * ([?]) sudah dibersihkan; beberapa tanggal lahir perlu verifikasi dokumen fisik.
 *
 * Idempotent (updateOrCreate by no_kk / nik).
 */
class WargaRealRt02Rw03Seeder extends Seeder
{
    use WithoutModelEvents;

    private const DATA = [
        [
            'no_kk' => '3578020101086029', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020101086029.jpg',
            'alamat' => 'Bendulmerisi Gg.4/21', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Abas Akadara', 'nik' => '3578040802740010', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1974-02-08', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Wiraswasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Nur Mochamad', 'ibu' => 'Watini'],
            ['nama' => 'Tutik Tri Wahyuningsih', 'nik' => '3578026805790001', 'jk' => 'P', 'ttl' => 'Mojokerto', 'tgl' => '1979-05-28', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Kasnawi', 'ibu' => 'Tarmini'],
            ['nama' => 'Tiara Nur Alifia', 'nik' => '3578025112990001', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1999-12-11', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Abas Akadara', 'ibu' => 'Tutik Tri Wahyuningsih'],
            ['nama' => 'Fakhri Abas Al-rasyid', 'nik' => '3578020704090001', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2009-04-07', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Abas Akadara', 'ibu' => 'Tutik Tri Wahyuningsih'],
            ],
        ],
        [
            'no_kk' => '3578020206130001', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020206130001.jpg',
            'alamat' => 'Bendulmerisi Gg.4/29', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Abdul Rosid', 'nik' => '3578040905820010', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1982-05-09', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Misdjar', 'ibu' => 'Sutia'],
            ['nama' => 'Sulfia', 'nik' => '3527055109860005', 'jk' => 'P', 'ttl' => 'Sampang', 'tgl' => '1986-09-11', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Wiraswasta', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Marsuki (alm)', 'ibu' => 'Laspati'],
            ['nama' => 'Muhammad Farhan', 'nik' => '3578050309110004', 'jk' => 'L', 'ttl' => 'Sampang', 'tgl' => '2011-09-03', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Abdul Rosid', 'ibu' => 'Sulfia'],
            ['nama' => 'Ahmad Zidnil Ilmi', 'nik' => '3578020303180002', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2016-03-03', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Abdul Rosid', 'ibu' => 'Sulfia'],
            ],
        ],
        [
            'no_kk' => '3578020201081199', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020201081199.jpg',
            'alamat' => 'Bendulmerisi Iv No 31', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Achmad Ihsan', 'nik' => '3578021504690002', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1969-04-15', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Niti Subrata', 'ibu' => 'Salminingsih'],
            ['nama' => 'Pipit Satriyawatie', 'nik' => '3578025812700002', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1970-12-18', 'agama' => 'Islam', 'pend' => 'D4/S1', 'kerja' => 'Guru', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Dadang Sudirdja', 'ibu' => 'Sri Astuti Js'],
            ['nama' => 'Ikhlasul Nusa', 'nik' => '3578021810000002', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2000-10-18', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Akhmad Iksan', 'ibu' => 'Pipit Satriyawatie, S.pd'],
            ['nama' => 'Ikhlasul Is\'ad', 'nik' => '3578022109060001', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2006-09-21', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Achmad Ihsan', 'ibu' => 'Pipit Satriyawatie, S.pd'],
            ],
        ],
        [
            'no_kk' => '3578020101087412', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020101087412.jpg',
            'alamat' => 'Bendulmerisi Gg.4/39', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Ach Supai', 'nik' => '3578040107830010', 'jk' => 'L', 'ttl' => 'Sampang', 'tgl' => '1983-07-01', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Marsuni', 'ibu' => 'Jatimah'],
            ['nama' => 'Mardiyah', 'nik' => '3578024508850001', 'jk' => 'P', 'ttl' => 'Bangkalan', 'tgl' => '1985-08-05', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Esba', 'ibu' => 'Siti Parni'],
            ['nama' => 'M.firmansyah', 'nik' => '3578040910040010', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2004-10-09', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Ach.supai', 'ibu' => 'Mardiyah'],
            ['nama' => 'Iqbal Sabili', 'nik' => '3578020505140004', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2014-05-05', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Ach Supai', 'ibu' => 'Mardiyah'],
            ['nama' => 'Abdul Aziz Fathul Islam', 'nik' => '3578021912150002', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2015-12-19', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => null, 'ibu' => null],
            ],
        ],
        [
            'no_kk' => '3578020101086814', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020101086814.jpg',
            'alamat' => 'Bendulmerisi Gg.Iii No. 5 C', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Agus Setio Wandono', 'nik' => '3578021608760001', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1976-08-16', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Wakidjan', 'ibu' => 'Soekesi'],
            ['nama' => 'Siti Aliyah', 'nik' => '3578025801750001', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1975-01-18', 'agama' => 'Islam', 'pend' => 'D3', 'kerja' => 'Pegawai Negeri Sipil (pns)', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Achnan', 'ibu' => 'Umaiyah'],
            ['nama' => 'Muhammad Rafli Hatifi Adzhani', 'nik' => '3578020208020002', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2002-08-02', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Agus Setio Wandono', 'ibu' => 'Siti Aliyah'],
            ['nama' => 'Muhammad Dzaky Malik Al Ghoffary', 'nik' => '3578021901070002', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2007-01-19', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Agus Setio Wandono', 'ibu' => 'Siti Aliyah'],
            ['nama' => 'Alfrida Putri Tribuana Tungga Dewi', 'nik' => '3578026705110002', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2011-05-27', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Rachmad. Alm', 'ibu' => 'Kamirah. Alm'],
            ['nama' => 'Umaijah', 'nik' => '3578047006480022', 'jk' => 'P', 'ttl' => 'Jombang', 'tgl' => '1948-06-30', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Cerai Mati', 'hub' => 'Mertua', 'ayah' => 'Yoyok Prastiyo', 'ibu' => null],
            ['nama' => 'Reswara Apridila Cetta', 'nik' => '3515141104130006', 'jk' => 'P', 'ttl' => 'Sidoarjo', 'tgl' => '2013-04-11', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Famili Lain', 'ayah' => null, 'ibu' => null],
            ],
        ],
        [
            'no_kk' => '3578020201084878', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020201084878.jpg',
            'alamat' => 'Bendulmerisi Gg. 3/3-B', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Akhmad Suryadi, Se', 'nik' => '3578042405650010', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2024-05-25', 'agama' => 'Islam', 'pend' => 'D4/S1', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Makmur Alm', 'ibu' => 'Soeryaniek'],
            ['nama' => 'Kusrini', 'nik' => '3578026806710006', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1965-05-24', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Guru', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Ngatiman', 'ibu' => 'Parwi'],
            ['nama' => 'Firda Rizqy Amalia', 'nik' => '3578045812970010', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1971-08-28', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Achmad Suryadi, Se', 'ibu' => 'Kusrini'],
            ['nama' => 'Azzalina Alsavira', 'nik' => '3578027108990001', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1998-08-31', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Achmad Suryadi, Se', 'ibu' => 'Kusrini'],
            ],
        ],
        [
            'no_kk' => '3578022501170006', 'status' => 'Tetap', 'foto_kk' => 'kk/3578022501170006.jpg',
            'alamat' => 'Bendulmerisi Gg. 4/31', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Al Amin Chomami', 'nik' => '3578207510269805', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1975-10-26', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Honorer', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Djupri Efendi', 'ibu' => 'Siti Zulaikah'],
            ['nama' => 'Nining Safitri', 'nik' => '3578041005850010', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1985-05-10', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => '`` Tidak Terbaca Jelas', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Drs. Sochibul Hadi', 'ibu' => 'Dra. Nursaidah'],
            ['nama' => 'Bachtiar Alamsyah', 'nik' => '3578256509890004', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1989-09-25', 'agama' => 'Islam', 'pend' => 'D4/S1', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Al Amin Chomami', 'ibu' => 'Nining Safitri'],
            ['nama' => 'Maulana Adnan Syah', 'nik' => '3578020610160002', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2016-10-06', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Al Amin Chomami', 'ibu' => 'Nining Safitri'],
            ],
        ],
        [
            'no_kk' => '3578022710200005', 'status' => 'Tetap', 'foto_kk' => 'kk/3578022710200005.jpg',
            'alamat' => 'Bendulmerisi Gg 4 / 37', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Bedrudin', 'nik' => '3527050808960004', 'jk' => 'L', 'ttl' => 'Sampang', 'tgl' => '1996-08-08', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Wiraswasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Abusiri', 'ibu' => 'Hamidah'],
            ['nama' => 'Rani Tri Pamungkas', 'nik' => '3578025510990001', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1999-10-15', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Nanang Kosim', 'ibu' => 'Endang Sutrisno'],
            ['nama' => 'Ainul Yaqin', 'nik' => '3578021402210002', 'jk' => 'L', 'ttl' => 'Sampang', 'tgl' => '2021-02-14', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Bedrudin', 'ibu' => 'Rani Tri Pamungkas'],
            ],
        ],
        [
            'no_kk' => '3578022205120012', 'status' => 'Tetap', 'foto_kk' => 'kk/3578022205120012.jpg',
            'alamat' => 'Bendulmerisi Gg. 3/3', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Chairul Anwar', 'nik' => '3578021205720001', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1972-05-12', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Markam', 'ibu' => 'Kayatun'],
            ['nama' => 'Liswanah', 'nik' => '3578045107740002', 'jk' => 'P', 'ttl' => 'Lamongan', 'tgl' => '1974-07-11', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Nur Salim', 'ibu' => 'Kasiyem'],
            ['nama' => 'Alfina Choirunisa', 'nik' => '3578026606080005', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2008-06-26', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => '`` Tidak Terbaca', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Chairul Anwar', 'ibu' => 'Liswanah'],
            ['nama' => 'Achmad Mustofa Alkamal', 'nik' => '3578020302110004', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2011-02-03', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => '`` Tidak Terbaca', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Chairul Anwar', 'ibu' => 'Liswanah'],
            ],
        ],
        [
            'no_kk' => '3578020101085977', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020101085977.jpg',
            'alamat' => 'Bendulmerisi Gg.4/35', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Chrismanu Rudyanto', 'nik' => '3578041907670010', 'jk' => 'L', 'ttl' => 'Yogyakarta', 'tgl' => '1967-07-19', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Sukaryadi', 'ibu' => 'Siti Rukiyah'],
            ['nama' => 'Ma\'rufah', 'nik' => '3578025104670002', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1967-04-11', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Trimo', 'ibu' => 'Sumiyati'],
            ['nama' => 'Agus Arif Rahman', 'nik' => '3578020508010003', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2001-08-05', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Chrismanu Rudyanto', 'ibu' => 'Ma\'rufah'],
            ],
        ],
        [
            'no_kk' => '3578022809150010', 'status' => 'Tetap', 'foto_kk' => 'kk/3578022809150010.jpg',
            'alamat' => 'Bendulmerisi Gg.4/22', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Doni Eko Satrianto', 'nik' => '3578041706850010', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1985-06-17', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Satrijo', 'ibu' => 'Misni'],
            ['nama' => 'Siti Nur Jannah', 'nik' => '3515095708870002', 'jk' => 'P', 'ttl' => 'Sidoarjo', 'tgl' => '1987-08-17', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Supa\'at', 'ibu' => 'Kolipah'],
            ['nama' => 'Nasya Syifa Nur Az-zahra', 'nik' => '3515095812100003', 'jk' => 'P', 'ttl' => 'Sidoarjo', 'tgl' => '2010-12-18', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Doni Eko Satrianto', 'ibu' => 'Siti Nur Jannah'],
            ['nama' => 'Rameysa Aleea Zara', 'nik' => '3578025607170002', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2017-07-16', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Doni Eko Satrianto', 'ibu' => 'Siti Nur Jannah'],
            ],
        ],
        [
            'no_kk' => '3578020410210008', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020410210008.jpg',
            'alamat' => 'Bendul Merisi Iv No.31', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Dwi Yulianto', 'nik' => '3523173107910001', 'jk' => 'L', 'ttl' => 'Tuban', 'tgl' => '1991-07-31', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Suhardi', 'ibu' => 'Suparmi'],
            ['nama' => 'Novia Kusuma Dewi', 'nik' => '3578024411960002', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1996-11-04', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Henrikus Rukristiawan', 'ibu' => 'Mardiyaningsih'],
            ['nama' => 'Ukkasyah', 'nik' => '3578020503220001', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2022-03-05', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Dwi Yulianto', 'ibu' => 'Novia Kusuma Dewi'],
            ],
        ],
        [
            'no_kk' => '3578023110200003', 'status' => 'Tetap', 'foto_kk' => 'kk/3578023110200003.jpg',
            'alamat' => 'Bendulmerisi Gg.5/5-A', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Rr. Endang Soelistijowati', 'nik' => '3578026404500002', 'jk' => 'P', 'ttl' => 'Magetan', 'tgl' => '1950-04-24', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Cerai Mati', 'hub' => 'Kepala Keluarga', 'ayah' => 'Suharjo (alm)', 'ibu' => 'Kusni'],
            ],
        ],
        [
            'no_kk' => '3578021306110006', 'status' => 'Tetap', 'foto_kk' => 'kk/3578021306110006.jpg',
            'alamat' => 'Bendul Merisi Gg.Iv/29', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Erich Rachmad Saufi', 'nik' => '3578022702820004', 'jk' => 'L', 'ttl' => 'Madiun', 'tgl' => '1982-02-27', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Soleh Rachmad (alm)', 'ibu' => 'Sri Harini'],
            ['nama' => 'Rosy Eliana, Ss', 'nik' => '3578025609780005', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1978-09-16', 'agama' => 'Islam', 'pend' => 'D4/S1', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Ibrahim, Bba', 'ibu' => 'Suaidah Faqih'],
            ['nama' => 'Ragata Hemasnala Saufi', 'nik' => '3578026102120001', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2012-02-21', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Erich Rachmad Saufi', 'ibu' => 'Rosy Eliana, Ss'],
            ],
        ],
        [
            'no_kk' => '3578020201081200', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020201081200.jpg',
            'alamat' => 'Bendul Merisi Iv No.31', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Henrikus Ruskristiawan', 'nik' => '3578020111690003', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1969-11-01', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Pc Sumardi', 'ibu' => 'Rr. Suratmi Rusmiati'],
            ['nama' => 'Mardiyaningsih', 'nik' => '3578025403720006', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1972-03-14', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Perdagangan', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Niti Subrata (alm)', 'ibu' => 'Salmi Ningsih (alm)'],
            ['nama' => 'Bimantara Kusumadewa', 'nik' => '3578021305020002', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2002-05-13', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Henrikus Ruskristiawan', 'ibu' => 'Mardiyaningsih'],
            ['nama' => 'Raditya Kusuma Dewa', 'nik' => '3578020411070004', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2007-11-04', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Henrikus Ruskristiawan', 'ibu' => 'Mardiyaningsih'],
            ],
        ],
        [
            'no_kk' => '3578023012200010', 'status' => 'Tetap', 'foto_kk' => 'kk/3578023012200010.jpg',
            'alamat' => 'Bendul Merisi Gg 3/5-C', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Indah Hariningsih', 'nik' => '3578026904740001', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1974-04-29', 'agama' => 'Islam', 'pend' => 'D4/S1', 'kerja' => 'Wiraswasta', 'kawin' => 'Cerai Mati', 'hub' => 'Kepala Keluarga', 'ayah' => 'Slamet', 'ibu' => 'Tumi'],
            ['nama' => 'Muhammad Aziz Al Bilad', 'nik' => '3578020304990002', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1999-04-03', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Sugeng Nurdyanto Wahyudi', 'ibu' => 'Indah Hariningsih'],
            ['nama' => 'Sulthan Al Hadid', 'nik' => '3578021412000001', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2000-12-14', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Sugeng Nurdyanto Wahyudi', 'ibu' => 'Indah Hariningsih'],
            ],
        ],
        [
            'no_kk' => '3578021503190003', 'status' => 'Tetap', 'foto_kk' => 'kk/3578021503190003.jpg',
            'alamat' => 'Bendul Merisi Gg 4 No. 37', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Jufri', 'nik' => '3527052604810002', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1981-04-26', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Wiraswasta', 'kawin' => 'Belum Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Mat Tamin (alm)', 'ibu' => 'Mamnu\'ah (alm)'],
            ],
        ],
        [
            'no_kk' => '3578021701230004', 'status' => 'Tetap', 'foto_kk' => 'kk/3578021701230004.jpg',
            'alamat' => 'Bendulmerisi Gg. 3/3', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Kayatun', 'nik' => '3578025303530002', 'jk' => 'P', 'ttl' => 'Jombang', 'tgl' => '1953-03-13', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Cerai Mati', 'hub' => 'Kepala Keluarga', 'ayah' => 'Mardjuki (alm)', 'ibu' => 'Siti'],
            ['nama' => 'Chairul Umam', 'nik' => '3578020105740003', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1974-05-01', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Anak', 'ayah' => 'Markam', 'ibu' => 'Kayatun'],
            ['nama' => 'Chairul Saifudin', 'nik' => '3578021409760001', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1976-09-14', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Markam', 'ibu' => 'Kayatun'],
            ['nama' => 'Alfina Nurul Anisa', 'nik' => '3578026106080004', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2008-06-21', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Cucu', 'ayah' => 'Chairul Anwar', 'ibu' => 'Liswana'],
            ],
        ],
        [
            'no_kk' => '3578020803220015', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020803220015.jpg',
            'alamat' => 'Bendulmerisi Iv No 31', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Ki Dwi Waluyo Jati', 'nik' => '3578022602950004', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1995-02-26', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Langgeng Prayitno', 'ibu' => 'Piani'],
            ['nama' => 'Ichvita Rachma Unengan', 'nik' => '3578044602970010', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1997-02-06', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Akhmad Iksan', 'ibu' => 'Pipit Satriyawatie, S.pd'],
            ],
        ],
        [
            'no_kk' => '3578021501200046', 'status' => 'Tetap', 'foto_kk' => 'kk/3578021501200046.jpg',
            'alamat' => 'Bendulmerisi Gg. 3/5', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Mariyati', 'nik' => '3578024609780004', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1978-09-06', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Sulam', 'ibu' => 'Misnah, Alm'],
            ['nama' => 'Wulan Sari', 'nik' => '3578025906010004', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2001-06-19', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => null, 'ibu' => 'Mariyati'],
            ['nama' => 'Cantika', 'nik' => '3578024304070004', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2007-04-03', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => null, 'ibu' => 'Mariyati'],
            ['nama' => 'Falen', 'nik' => '3578024102100001', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2010-02-01', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => null, 'ibu' => 'Mariyati'],
            ],
        ],
        [
            'no_kk' => '3578020101084364', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020101084364.jpg',
            'alamat' => 'Bendulmerisi 4/17', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Mat Dewi', 'nik' => '3578020909690002', 'jk' => 'L', 'ttl' => 'Sampang', 'tgl' => '1969-09-09', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Cerai Mati', 'hub' => 'Kepala Keluarga', 'ayah' => 'Matasan', 'ibu' => 'Asma [alm]'],
            ['nama' => 'Kiki Wahyudi', 'nik' => '3578040905940010', 'jk' => 'L', 'ttl' => 'Sampang', 'tgl' => '1994-05-09', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Mat Dewi', 'ibu' => 'Musrifah, Alm'],
            ],
        ],
        [
            'no_kk' => '3578020101087242', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020101087242.jpg',
            'alamat' => 'Bendulmerisi Gg.4/29', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Matnali', 'nik' => '3578021208770002', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1977-08-12', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Wiraswasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Misdjar', 'ibu' => 'Sutt\'ah'],
            ['nama' => 'Holimah', 'nik' => '3578045305830010', 'jk' => 'P', 'ttl' => 'Bangkalan', 'tgl' => '1983-05-13', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Puser', 'ibu' => 'Sutiya'],
            ['nama' => 'Salman Al Varisi', 'nik' => '3578021712030002', 'jk' => 'L', 'ttl' => 'Bangkalan', 'tgl' => '2003-12-17', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Matnali', 'ibu' => 'Cholimah'],
            ['nama' => 'Zaskiatul Alifia', 'nik' => '3578026205150003', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2015-05-22', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Matnali', 'ibu' => 'Holimah'],
            ['nama' => 'Ach. Noufal Maulana', 'nik' => '3578020802180003', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2018-02-08', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Matnali', 'ibu' => 'Holimah'],
            ],
        ],
        [
            'no_kk' => '3578020609220004', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020609220004.pdf',
            'alamat' => 'Bendulmerisi 4/37', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Misfa', 'nik' => '3578027006710001', 'jk' => 'P', 'ttl' => 'Bangkalan', 'tgl' => '1971-06-30', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Cerai Mati', 'hub' => 'Kepala Keluarga', 'ayah' => 'Asmu\'i', 'ibu' => 'Buride'],
            ['nama' => 'Hoirul Umam', 'nik' => '3578021206890003', 'jk' => 'L', 'ttl' => 'Bangkalan', 'tgl' => '1989-06-12', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Jamari', 'ibu' => 'Misfa'],
            ['nama' => 'Syaiful Anam', 'nik' => '3578042806980010', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1998-06-28', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Jamari', 'ibu' => 'Misfa'],
            ['nama' => 'Halimatus Sakdiyah', 'nik' => '3578026905030002', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2003-05-29', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Jamari', 'ibu' => 'Misfa'],
            ],
        ],
        [
            'no_kk' => '3578021712120003', 'status' => 'Tetap', 'foto_kk' => 'kk/3578021712120003.jpg',
            'alamat' => 'Bendulmerisi Gg 3/7', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Moch Nurawan', 'nik' => '3578020505740002', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1974-05-05', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Imam Masjid', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Munawi Alm', 'ibu' => 'Mistijah'],
            ['nama' => 'Puji Lestari', 'nik' => '3506134508870002', 'jk' => 'P', 'ttl' => 'Kediri', 'tgl' => '1987-08-05', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Jasmadi', 'ibu' => 'Nurul Yatin'],
            ['nama' => 'M. Raihan Hidayatulloh', 'nik' => '3578021406080001', 'jk' => 'L', 'ttl' => 'Kediri', 'tgl' => '2008-06-04', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Moch. Nurawan', 'ibu' => 'Puji Lestari'],
            ['nama' => 'Rafardhan Athalla Nur Azami', 'nik' => '3578020805180001', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2018-05-08', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Moch Nurawan', 'ibu' => 'Puji Lestari'],
            ['nama' => 'Almahdi Sharique Zhafran', 'nik' => '3578022310190001', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2019-10-23', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Moch Nurawan', 'ibu' => 'Puji Lestari'],
            ],
        ],
        [
            'no_kk' => '3578020201086566', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020201086566.jpg',
            'alamat' => 'Bendul Merisi 4/33', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Moh. Ali', 'nik' => '3578020402650002', 'jk' => 'L', 'ttl' => 'Pamekasan', 'tgl' => '1965-02-04', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pedagang', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Samuri P. Mustaji', 'ibu' => 'Buryani'],
            ['nama' => 'Rukati', 'nik' => '3578024501710003', 'jk' => 'P', 'ttl' => 'Mojokerto', 'tgl' => '1971-01-05', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Sarinoto', 'ibu' => 'Suyat'],
            ['nama' => 'Ahmad Subhan Purnomo', 'nik' => '3578021606920004', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1992-06-16', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Moh. Ali', 'ibu' => 'Rukati'],
            ['nama' => 'Adam Maulana Ibrahim Ali', 'nik' => '3578021710990002', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1999-10-17', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Moh. Ali', 'ibu' => 'Rukati'],
            ],
        ],
        [
            'no_kk' => '3578022511210002', 'status' => 'Tetap', 'foto_kk' => 'kk/3578022511210002.jpg',
            'alamat' => 'Bendulmerisi 4/17', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Moh. Ali', 'nik' => '3513170107900109', 'jk' => 'L', 'ttl' => 'Probolinggo', 'tgl' => '1990-04-04', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Pedagang', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Sahid', 'ibu' => 'Sawiti'],
            ['nama' => 'Yulianti', 'nik' => '3578024707910011', 'jk' => 'P', 'ttl' => 'Sampang', 'tgl' => '1991-07-07', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Mat Dewi', 'ibu' => 'Musrifah, Alm'],
            ['nama' => 'Ila Najatul Auliya', 'nik' => '3528084702130001', 'jk' => 'P', 'ttl' => 'Pamekasan', 'tgl' => '2013-02-07', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Ediyanto', 'ibu' => 'Yulianti'],
            ['nama' => 'Muhammad Husaen', 'nik' => '3578022209210002', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2021-09-22', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Moh. Ali', 'ibu' => 'Yulianti'],
            ],
        ],
        [
            'no_kk' => '3578020201080747', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020201080747.jpg',
            'alamat' => 'Bendulmerisi Gg.4/27 Sby', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Mohamad Hadi', 'nik' => '3578023006640016', 'jk' => 'L', 'ttl' => 'Jombang', 'tgl' => '1964-06-30', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Ketang (alm)', 'ibu' => 'Surini'],
            ['nama' => 'Sri Widati', 'nik' => '3578027006680028', 'jk' => 'P', 'ttl' => 'Tulungagung', 'tgl' => '1968-06-30', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Kadarsi (alm)', 'ibu' => 'Rusmini'],
            ['nama' => 'Anis Maulidiya', 'nik' => '3578026005040004', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2004-05-20', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Mohamad Hadi', 'ibu' => 'Sri Widati'],
            ],
        ],
        [
            'no_kk' => '3578022605170004', 'status' => 'Tetap', 'foto_kk' => 'kk/3578022605170004.jpg',
            'alamat' => 'Bendul Merisi Gg 04/29', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Moh. Sarofik', 'nik' => '3515092509840002', 'jk' => 'L', 'ttl' => 'Jombang', 'tgl' => '1994-09-23', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Suratno', 'ibu' => 'Badiah'],
            ['nama' => 'Susi Fenti Rahayu', 'nik' => '3515096211820003', 'jk' => 'P', 'ttl' => 'Sidoarjo', 'tgl' => '1992-11-23', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Suaman', 'ibu' => 'Tri Ningsih'],
            ['nama' => 'Zetta Ocba Agustina', 'nik' => '3515091808100003', 'jk' => 'P', 'ttl' => 'Sidoarjo', 'tgl' => '2013-08-19', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Moh. Sarofik', 'ibu' => 'Susi Fenti Rahayu'],
            ['nama' => 'Alvaro Zello Agustino', 'nik' => '3515092408130003', 'jk' => 'L', 'ttl' => 'Sidoarjo', 'tgl' => '2013-08-24', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Moh. Sarofik', 'ibu' => 'Susi Fenti Rahayu'],
            ],
        ],
        [
            'no_kk' => '3578020908170003', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020908170003.jpg',
            'alamat' => 'Bendul Merisi Gg 3/7', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'M. Slamet Riadi', 'nik' => '3509050704820005', 'jk' => 'L', 'ttl' => 'Jember', 'tgl' => '1982-01-07', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Mistar', 'ibu' => 'Tilah'],
            ['nama' => 'Lusiana', 'nik' => '3578026101850002', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1985-01-21', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Buruh Harian Lepas', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Munawi. Alm', 'ibu' => 'Mistijah'],
            ['nama' => 'Aminatus Sakdiyah', 'nik' => '3578024803100001', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2010-03-08', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'M. Slamet Riadi', 'ibu' => 'Lusiana'],
            ['nama' => 'Nurul Aini', 'nik' => '3578026006140003', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2014-06-20', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'M. Slamet Riadi', 'ibu' => 'Lusiana'],
            ],
        ],
        [
            'no_kk' => '3578020201086559', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020201086559.jpg',
            'alamat' => 'Bendulmerisi Gg 4/27', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Muchamad Achiyatyayak', 'nik' => '3578021702540002', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1954-02-17', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Wiraswasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Samin', 'ibu' => 'Mariyati'],
            ['nama' => 'Suwarni', 'nik' => '3578024302620004', 'jk' => 'P', 'ttl' => 'Gresik', 'tgl' => '1962-02-03', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Sumarno', 'ibu' => 'Satri'],
            ['nama' => 'Syaiful Anas', 'nik' => '3578021711800002', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1980-11-17', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Muchamad Achiyat/yayak', 'ibu' => 'Suwarni'],
            ],
        ],
        [
            'no_kk' => '3578020101088801', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020101088801.jpg',
            'alamat' => 'Bendulmerisi Gg 4/37', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Nanang Kosim', 'nik' => '3578020711600005', 'jk' => 'L', 'ttl' => 'Nganjuk', 'tgl' => '1980-11-07', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Cerai Mati', 'hub' => 'Kepala Keluarga', 'ayah' => 'Warlan Alm', 'ibu' => 'Parni'],
            ],
        ],
        [
            'no_kk' => '3578020203150011', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020203150011.jpg',
            'alamat' => 'Bendul Merisi Gg 3/7', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Nurfadilah', 'nik' => '3578025202780003', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1978-02-12', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pembantu Rumah Tangga', 'kawin' => 'Cerai Hidup', 'hub' => 'Kepala Keluarga', 'ayah' => 'Munawi (alm)', 'ibu' => 'Mistijah'],
            ['nama' => 'Wahid Abdul Rochman', 'nik' => '3578020708010002', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2001-09-07', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Selamet Poniman', 'ibu' => 'Nurfadilah'],
            ['nama' => 'Nurfaizah', 'nik' => '3578027011130002', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2013-11-30', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => null, 'ibu' => 'Nurfadilah'],
            ],
        ],
        [
            'no_kk' => '3578022603200001', 'status' => 'Tetap', 'foto_kk' => 'kk/3578022603200001.jpg',
            'alamat' => 'Bendulmerisi Gg 3/7', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Nur Faridah', 'nik' => '3578026306820002', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1982-06-23', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pembantu Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Munawi. Alm', 'ibu' => 'Mistijah'],
            ],
        ],
        [
            'no_kk' => '3578020807210013', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020807210013.jpg',
            'alamat' => 'Bendul Merisi Besar Timur I/37', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Nurul Helmawati', 'nik' => '3578026303710001', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1971-03-23', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Cerai Mati', 'hub' => 'Kepala Keluarga', 'ayah' => 'Abdoel Sjoekoer', 'ibu' => 'Masdjidah'],
            ['nama' => 'Mutiara Chandra Sae Mahardika', 'nik' => '3578024906990006', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1999-06-09', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Suprijadi', 'ibu' => 'Nurul Helmawati'],
            ['nama' => 'Dimas Rangga Rajawali.p.p', 'nik' => '3578020304010004', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2001-04-03', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Suprijadi', 'ibu' => 'Nurul Helmawati'],
            ['nama' => 'Bintang Rakha Elang Perkasa.p', 'nik' => '3578041111020010', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2002-11-11', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Suprijadi', 'ibu' => 'Nurul Helmawati'],
            ],
        ],
        [
            'no_kk' => '3578022605160012', 'status' => 'Tetap', 'foto_kk' => 'kk/3578022605160012.jpg',
            'alamat' => 'Bendulmerisi Gg 4/37', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Putri Inang Kurniawati', 'nik' => '3578207509095680', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1975-09-09', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Belum Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Nanang Kosim', 'ibu' => 'Endang Sutrisno'],
            ['nama' => 'Gilang Pratama Putra', 'nik' => '3578025007940005', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1994-07-10', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Umar', 'ibu' => 'Putri Inang Kurniawati'],
            ['nama' => 'Kenzo Elvaro Alfarizhi', 'nik' => '3578022209120001', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2012-09-22', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => null, 'ibu' => 'Putri Inang Kurniawati'],
            ['nama' => 'Gielsyah Earlitha Putri', 'nik' => '3578026009170004', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2017-09-20', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => null, 'ibu' => 'Putri Inang Kurniawati'],
            ],
        ],
        [
            'no_kk' => '3578022207190003', 'status' => 'Tetap', 'foto_kk' => 'kk/3578022207190003.jpg',
            'alamat' => 'Bendulmerisi Gg.3/5.C', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Rajif Al Ahmad Ramadhani', 'nik' => '3520111704910001', 'jk' => 'L', 'ttl' => 'Madiun', 'tgl' => '1991-04-17', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Sugianto', 'ibu' => 'Barlia Insani'],
            ['nama' => 'Friska Retno Wahyu Kusmalasari', 'nik' => '3578045502930010', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1993-02-15', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Kusmiaji', 'ibu' => 'Wahyuningsih, Se'],
            ],
        ],
        [
            'no_kk' => '3578020306220002', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020306220002.jpg',
            'alamat' => 'Bendulmerisi Gg.4/27 Sby', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Robi Handoyo', 'nik' => '3575010408920001', 'jk' => 'L', 'ttl' => 'Pasuruan', 'tgl' => '1992-08-04', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Kamsuni', 'ibu' => 'Muslichah'],
            ['nama' => 'Venny Febriantika', 'nik' => '3578026402990001', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1999-02-24', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Mohamad Hadi', 'ibu' => 'Sri Widati'],
            ['nama' => 'Rizhan Alfarizqi', 'nik' => '3578020603230001', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2023-03-06', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Robi Handoyo', 'ibu' => 'Venny Febriantika'],
            ],
        ],
        [
            'no_kk' => '3578020101086759', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020101086759.jpg',
            'alamat' => 'Bendulmerisi Gg.3/5', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Samsuri', 'nik' => '3578021503690003', 'jk' => 'L', 'ttl' => 'Sampang', 'tgl' => '1989-03-15', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Manan [alm]', 'ibu' => 'Nawati [alm]'],
            ['nama' => 'Mariyam', 'nik' => '3578026406760004', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1976-08-24', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Sulam', 'ibu' => null],
            ],
        ],
        [
            'no_kk' => '3578020201084874', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020201084874.jpg',
            'alamat' => 'Bendulmerisi Gg 5/1', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Santoso Bin Kiman', 'nik' => '3578021305650003', 'jk' => 'L', 'ttl' => 'Demak', 'tgl' => '1965-05-13', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Kiman', 'ibu' => 'Sulima'],
            ['nama' => 'Yatimah S.pd', 'nik' => '3578025204660003', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1966-04-12', 'agama' => 'Islam', 'pend' => 'D4/S1', 'kerja' => 'Guru', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Niti', 'ibu' => 'Salmi'],
            ['nama' => 'Oktavia Rachmawati, A.md.gz', 'nik' => '3578026910890002', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1989-10-29', 'agama' => 'Islam', 'pend' => 'D3', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Santoso Bin Kiman', 'ibu' => 'Yatimah'],
            ],
        ],
        [
            'no_kk' => '3578020101088007', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020101088007.jpg',
            'alamat' => 'Bendulmerisi Gg.4/22', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Satrijo', 'nik' => '3578020810630002', 'jk' => 'L', 'ttl' => 'Bondowoso', 'tgl' => '1963-10-08', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Djumain (alm)', 'ibu' => 'Sanimah'],
            ['nama' => 'Misni', 'nik' => '3578026411660002', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1966-11-24', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Rais (alm)', 'ibu' => 'Karti'],
            ['nama' => 'Dedy Satriyono', 'nik' => '3578022906950006', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1995-06-29', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Satrijo', 'ibu' => 'Misni'],
            ],
        ],
        [
            'no_kk' => '3578020101088773', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020101088773.jpg',
            'alamat' => 'Bendulmerisi Gg 3/3 A', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Soekadji', 'nik' => '3578020102430002', 'jk' => 'L', 'ttl' => 'Nganjuk', 'tgl' => '1943-02-01', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Pensiunan', 'kawin' => 'Cerai Mati', 'hub' => 'Kepala Keluarga', 'ayah' => 'Soemohardjo (alm)', 'ibu' => 'Yatmi'],
            ['nama' => 'Eny Sumaryati', 'nik' => '3578025305670002', 'jk' => 'P', 'ttl' => 'Nganjuk', 'tgl' => '1967-05-13', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Kawin', 'hub' => 'Famili Lain', 'ayah' => 'Soetjipto', 'ibu' => null],
            ],
        ],
        [
            'no_kk' => '3578022901130020', 'status' => 'Tetap', 'foto_kk' => 'kk/3578022901130020.jpg',
            'alamat' => 'Bendulmerisi Iv / 27', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Sunarto', 'nik' => '3518110604780006', 'jk' => 'L', 'ttl' => 'Nganjuk', 'tgl' => '1978-04-06', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Panimin', 'ibu' => 'Sulasiyah'],
            ['nama' => 'Pipit Widyastiwi', 'nik' => '3578025703860002', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1966-03-17', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Buruh Harian Lepas', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Siari', 'ibu' => 'Masiatin'],
            ['nama' => 'Putri Aissah', 'nik' => '3578026409100001', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2010-09-24', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Sunarto', 'ibu' => 'Pipit Widyastiwi'],
            ['nama' => 'Much. Maulana Saputra', 'nik' => '3578020210160001', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2016-10-02', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Sunarto', 'ibu' => 'Pipit Widyastiwi'],
            ],
        ],
        [
            'no_kk' => '3578020101089610', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020101089610.jpg',
            'alamat' => 'Bendulmerisi Gg 04/29', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Suratno', 'nik' => '3578020603560002', 'jk' => 'L', 'ttl' => 'Malang', 'tgl' => '1956-03-06', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Perdagangan', 'kawin' => 'Cerai Mati', 'hub' => 'Kepala Keluarga', 'ayah' => 'Karno Sekak', 'ibu' => 'Siwuh'],
            ],
        ],
        [
            'no_kk' => '3578020108220002', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020108220002.jpg',
            'alamat' => 'Bendulmerisi Gg 4/27', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Syaiful Anis', 'nik' => '3578021104770002', 'jk' => 'L', 'ttl' => 'Gresik', 'tgl' => '1977-04-11', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Muchamad Achiyatyayak ``', 'ibu' => 'Suwarni'],
            ['nama' => 'Kusuma Fatwaningsih, Se', 'nik' => '3578045408760007', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1976-08-14', 'agama' => 'Islam', 'pend' => 'D4/S1', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Koesno (alm)', 'ibu' => 'Sudarwati'],
            ],
        ],
        [
            'no_kk' => '3578042211110020', 'status' => 'Tetap', 'foto_kk' => 'kk/3578042211110020.jpg',
            'alamat' => 'Bendul Merisi 4 No 22 B', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Syaiful Romli', 'nik' => '3578052701830003', 'jk' => 'L', 'ttl' => 'Bangkalan', 'tgl' => '1983-01-27', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Mat Hapi', 'ibu' => 'Bunisma'],
            ['nama' => 'Ida Royani', 'nik' => '3578045305860004', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1986-05-13', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Mat Hamid', 'ibu' => 'Rokayah (alm)'],
            ['nama' => 'Farah Riska Fadhilah', 'nik' => '3578045104040005', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2004-04-11', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Syabul Romli', 'ibu' => 'Ida Royani'],
            ['nama' => 'Qonita Abadia', 'nik' => '3578047004090008', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2009-04-30', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Syabul Romli', 'ibu' => 'Ida Royani'],
            ['nama' => 'Fitria Maulidia', 'nik' => '3578056712140002', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2014-12-27', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Syaiful Romli', 'ibu' => 'Ida Royani'],
            ['nama' => 'Elsa Arcelia Ruqayah', 'nik' => '3578026302220001', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2022-02-23', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Syaiful Romli', 'ibu' => 'Ida Royani'],
            ],
        ],
        [
            'no_kk' => '3578020101089707', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020101089707.jpg',
            'alamat' => 'Bendulmerisi Gg Iv/27', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Tadju Subekti', 'nik' => '3578020104530003', 'jk' => 'L', 'ttl' => 'Tulungagung', 'tgl' => '1953-04-01', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Wiraswasta', 'kawin' => 'Belum Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Romli (alm)', 'ibu' => 'Djumirah (alm)'],
            ['nama' => 'Djumitun', 'nik' => '3578044505600010', 'jk' => 'P', 'ttl' => 'Blitar', 'tgl' => '1960-05-05', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Wiraswasta', 'kawin' => 'Belum Kawin', 'hub' => 'Istri', 'ayah' => 'Suradi', 'ibu' => 'Kamisah'],
            ['nama' => 'Achmad Toni', 'nik' => '3578022804790003', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1979-04-28', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Tadju Subekti', 'ibu' => 'Djumitun'],
            ['nama' => 'Anisa Eka Bela', 'nik' => '3578025806080003', 'jk' => 'P', 'ttl' => 'Jombang', 'tgl' => '2008-06-18', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Cucu', 'ayah' => 'Ahmad Tumi', 'ibu' => 'Yestikawiduri'],
            ],
        ],
        [
            'no_kk' => '3578020204140004', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020204140004.jpg',
            'alamat' => 'Bendulmerisi Gg.3/5-C Sby', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Toemi', 'nik' => '3578027112460003', 'jk' => 'P', 'ttl' => 'Nganjuk', 'tgl' => '1946-12-31', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Pensiunan', 'kawin' => 'Belum Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Hardjo Sentono (alm)', 'ibu' => 'Dariyem (alm)'],
            ],
        ],
        [
            'no_kk' => '3578020311180011', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020311180011.jpg',
            'alamat' => 'Bendulmerisi Gg.4/27 Sby', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Tri Budicahyono', 'nik' => '3578040406860010', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1986-06-04', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Belum Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Mohamad Hadi', 'ibu' => 'Sri Widati'],
            ['nama' => 'Sujiatun', 'nik' => '3523174511870003', 'jk' => 'P', 'ttl' => 'Tuban', 'tgl' => '1987-11-05', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Belum Kawin', 'hub' => 'Istri', 'ayah' => 'Ngajiran', 'ibu' => 'Suwarsi'],
            ['nama' => 'Nabila Salsabila Zahra', 'nik' => '3523174402090001', 'jk' => 'P', 'ttl' => 'Tuban', 'tgl' => '2009-02-04', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Tri Budi Cahyono', 'ibu' => 'Sujiatun'],
            ],
        ],
        [
            'no_kk' => '3578022707210011', 'status' => 'Tetap', 'foto_kk' => 'kk/3578022707210011.jpg',
            'alamat' => 'Bendulmerisi Gg.3/5.C', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Wahyuningsih, Se', 'nik' => '3578026704690002', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1969-04-27', 'agama' => 'Islam', 'pend' => 'D4/S1', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Cerai Mati', 'hub' => 'Kepala Keluarga', 'ayah' => 'Slamet', 'ibu' => 'Tumi'],
            ['nama' => 'Satrio Aji Nugroho', 'nik' => '3578021511970002', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1997-11-15', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Kusmiaji', 'ibu' => 'Wahyuningsih, Se'],
            ],
        ],
        [
            'no_kk' => '3578022507220002', 'status' => 'Tetap', 'foto_kk' => 'kk/3578022507220002.jpg',
            'alamat' => 'Bendulmerisi Gg.Iii No. 5 C', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Wakidjan', 'nik' => '3515141607450003', 'jk' => 'L', 'ttl' => 'Kediri', 'tgl' => '1945-07-16', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Pensiunan', 'kawin' => 'Cerai Mati', 'hub' => 'Kepala Keluarga', 'ayah' => 'Joyongulomo (alm)', 'ibu' => 'Rusilah'],
            ],
        ],
        [
            'no_kk' => '3578240101087171', 'status' => 'Tetap', 'foto_kk' => 'kk/3578240101087171.jpg',
            'alamat' => 'Bendul Merisi 3/5 B', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Wiyono', 'nik' => '3578240707650004', 'jk' => 'L', 'ttl' => 'Kediri', 'tgl' => '1965-07-07', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Belum Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Solidin', 'ibu' => 'Widji'],
            ],
        ],
        [
            'no_kk' => '3578020201230005', 'status' => 'Tetap', 'foto_kk' => 'kk/3578020201230005.jpg',
            'alamat' => 'Bendul Merisi 4 No. 22', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Yesi Rasita', 'nik' => '3578026704920001', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1992-04-27', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Belum Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Asnawi', 'ibu' => 'Watiha'],
            ['nama' => 'Moch. Zein Al Qodri', 'nik' => '3578022703180001', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2018-03-27', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Moch. Husen', 'ibu' => 'Yesi Rasita'],
            ],
        ],
        [
            'no_kk' => '3578021911150008', 'status' => 'Tetap', 'foto_kk' => 'kk/3578021911150008.pdf',
            'alamat' => 'Bendulmerisi 4/37', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Yoyok Sumarsono', 'nik' => '3578161410880005', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1988-10-14', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Belum Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Darmanto Sujito', 'ibu' => 'Kanah'],
            ['nama' => 'Siti Romlah', 'nik' => '3578027112910004', 'jk' => 'P', 'ttl' => 'Bangkalan', 'tgl' => '1991-12-31', 'agama' => 'Islam', 'pend' => 'D4/S1', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Belum Kawin', 'hub' => 'Istri', 'ayah' => 'Jamari', 'ibu' => 'Misfa'],
            ['nama' => 'Naufal Rizki Ramadhan', 'nik' => '3578023006160002', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2016-06-30', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Yoyok Sumarsono', 'ibu' => 'Siti Romlah'],
            ['nama' => 'Almahyra Yumna Azzahra', 'nik' => '3578027112200002', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2020-12-31', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Yoyok Sumarsono', 'ibu' => 'Siti Romlah'],
            ['nama' => 'Farzana Anahita Az-zaida', 'nik' => '3578025102230001', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2023-02-11', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Yoyok Sumarsono', 'ibu' => 'Siti Romlah'],
            ],
        ],
        [
            'no_kk' => '3578230101082677', 'status' => 'Domisili', 'foto_kk' => 'kk/3578230101082677.pdf',
            'alamat' => 'Jambangan Iii-Sd/17', 'rt_kk' => '003', 'rw_kk' => '001',
            'kelurahan' => 'Jambangan', 'kecamatan' => 'Jambangan', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Ahmad Hidayat', 'nik' => '3578232812770003', 'jk' => 'L', 'ttl' => 'Jombang', 'tgl' => '1977-12-28', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Wiraswasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Buari', 'ibu' => 'Paila'],
            ['nama' => 'Marsih', 'nik' => '3578235211740001', 'jk' => 'P', 'ttl' => 'Madiun', 'tgl' => '1974-11-12', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Kasan Dahud', 'ibu' => 'Marinem'],
            ['nama' => 'Febryan Wahyudi Putra', 'nik' => '3578232606980001', 'jk' => 'L', 'ttl' => 'Magetan', 'tgl' => '1998-06-26', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Ahmad Hidayat', 'ibu' => 'Marsih'],
            ['nama' => 'Marsell Adi Nugroho', 'nik' => '3578230712030002', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2003-12-07', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Ahmad Hidayat', 'ibu' => 'Marsih'],
            ],
        ],
        [
            'no_kk' => '3578162505210001', 'status' => 'Domisili', 'foto_kk' => 'kk/3578162505210001.jpg',
            'alamat' => 'Tenggumung Baru Selatan 5/53', 'rt_kk' => '008', 'rw_kk' => '010',
            'kelurahan' => 'Pegirian', 'kecamatan' => 'Semampir', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Angga Pratama Setya Satria', 'nik' => '3578162607960001', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1996-07-26', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Bambang Trianto Nursatrio', 'ibu' => 'Wiwik Sulistiyani'],
            ['nama' => 'Ni\'matus Sholikah', 'nik' => '3578045108990004', 'jk' => 'P', 'ttl' => 'Lamongan', 'tgl' => '1999-08-11', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Choirul Anwar', 'ibu' => 'Liswanah'],
            ['nama' => 'Damar Rasyid Ihsan Satrio', 'nik' => '3578162607210003', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2021-07-26', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Angga Pratama Setya Satria', 'ibu' => 'Ni\'matus Sholikah'],
            ],
        ],
        [
            'no_kk' => '3578022712180002', 'status' => 'Domisili', 'foto_kk' => 'kk/3578022712180002.jpg',
            'alamat' => 'Bendul Merisi Jaya Gg. Makam 6/18-A', 'rt_kk' => '004', 'rw_kk' => '012',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Rohamit', 'nik' => '3527040107832850', 'jk' => 'L', 'ttl' => 'Sampang', 'tgl' => '1983-07-01', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Perdagangan', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'P Miryah', 'ibu' => 'B Miryah'],
            ['nama' => 'Hosidah', 'nik' => '3527045002800008', 'jk' => 'P', 'ttl' => 'Sampang', 'tgl' => '1980-02-10', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Jakop', 'ibu' => 'Dampek'],
            ['nama' => 'Lastri', 'nik' => '3527044107074540', 'jk' => 'P', 'ttl' => 'Sampang', 'tgl' => '2007-07-01', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Rohamit', 'ibu' => 'Hosidah'],
            ['nama' => 'Muhammad Maulana', 'nik' => '3527027051500003', 'jk' => 'L', 'ttl' => 'Sampang', 'tgl' => '2016-05-27', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Rohamit', 'ibu' => 'Hosidah'],
            ],
        ],
        [
            'no_kk' => '3527051110110020', 'status' => 'Domisili', 'foto_kk' => 'kk/3527051110110020.jpg',
            'alamat' => 'Bendulmerisi Gg. 5/5-A', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Samhari', 'nik' => '3527051207740004', 'jk' => 'L', 'ttl' => 'Sampang', 'tgl' => '1974-07-12', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Wiraswasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Nudin P. Mu\'arip', 'ibu' => 'Maryati B. Mu\'arip'],
            ['nama' => 'Maslahah', 'nik' => '3527055005860005', 'jk' => 'P', 'ttl' => 'Sampang', 'tgl' => '1986-05-10', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Sagimin', 'ibu' => 'Surasma'],
            ['nama' => 'Fauzi', 'nik' => '3527051309000003', 'jk' => 'L', 'ttl' => 'Sampang', 'tgl' => '2000-09-13', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Samhari', 'ibu' => 'Maslahah'],
            ['nama' => 'Nur Khofifatul Fauziyah', 'nik' => '3527056309110001', 'jk' => 'P', 'ttl' => 'Sampang', 'tgl' => '2011-09-23', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Samhari', 'ibu' => 'Maslahah'],
            ],
        ],
        [
            'no_kk' => '3578040201080406', 'status' => 'Domisili', 'foto_kk' => 'kk/3578040201080406.jpg',
            'alamat' => 'Pulo Wonokromo Wetan 4/9', 'rt_kk' => '003', 'rw_kk' => '004',
            'kelurahan' => 'Jagir', 'kecamatan' => 'Wonokromo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Siswanto', 'nik' => '3578041610690004', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1969-10-16', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Wiraswasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Slamet', 'ibu' => 'Muchamah'],
            ['nama' => 'Santi Viatiningrum', 'nik' => '3578046505740004', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1974-05-25', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Tjarum Bin Slamet', 'ibu' => 'Satumi'],
            ['nama' => 'Andi Bachtiar', 'nik' => '3578042209000004', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2000-09-22', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Belum/Tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Siswanto', 'ibu' => 'Santi Viatiningrum'],
            ],
        ],
        [
            'no_kk' => '3578022003120030', 'status' => 'Domisili', 'foto_kk' => 'kk/3578022003120030.jpg',
            'alamat' => 'Bendulmerisi 2 Dalam 20', 'rt_kk' => '002', 'rw_kk' => '002',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Slamet', 'nik' => '3578021709820011', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1982-09-17', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Buruh Harian Lepas', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Matnakip Alm', 'ibu' => 'Mariyam'],
            ['nama' => 'Musrifah', 'nik' => '3578025201820003', 'jk' => 'P', 'ttl' => 'Bangkalan', 'tgl' => '1982-01-12', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Mattari', 'ibu' => 'Fatima'],
            ['nama' => 'Nurhasanah', 'nik' => '3578024712110002', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2011-12-07', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Slamet', 'ibu' => 'Musrifah'],
            ['nama' => 'Raysa Putri Humairah', 'nik' => '3578025504190001', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2019-04-15', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Slamet', 'ibu' => 'Musrifah'],
            ],
        ],
        [
            'no_kk' => '3578072203110003', 'status' => 'Domisili', 'foto_kk' => 'kk/3578072203110003.jpg',
            'alamat' => 'Dinoyo 9/9-B', 'rt_kk' => '002', 'rw_kk' => '005',
            'kelurahan' => 'Keputran', 'kecamatan' => 'Tegalsari', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Tan Djun Bouw-welly Setiawan', 'nik' => '3578071007740001', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1974-07-10', 'agama' => 'Kristen', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Tan Giok Tjheng / Gunadi S.', 'ibu' => 'Sie Lee Na Nio / Lena Ekawati'],
            ['nama' => 'Anna Silvana Tedjanegara', 'nik' => '3578066206740003', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1974-05-12', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Arya Peranadi T.n', 'ibu' => 'Irawati (tjio Hok Niok)'],
            ['nama' => 'Vanesa Setiawan', 'nik' => '3578055504110001', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2011-04-15', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Tan Djun Bouw-welly Setiawan', 'ibu' => 'Anna Silvana Tedjanegara'],
            ['nama' => 'Nathan Alexander', 'nik' => '3578052505060005', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2006-05-25', 'agama' => 'Islam', 'pend' => 'SD/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Belum Kawin', 'hub' => 'Famili Lain', 'ayah' => 'Juli Priyonugra Sunarjo', 'ibu' => 'Anna Silvana Tedjanegara'],
            ],
        ],
        [
            'no_kk' => '3578020101081330', 'status' => 'Non Domisili', 'foto_kk' => 'kk/3578020101081330.pdf',
            'alamat' => 'Bendulmerisi 5/5-A', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Endra Sukmana', 'nik' => '3578020107730006', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1973-07-01', 'agama' => 'Islam', 'pend' => 'D3', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Roestamadji, Sh', 'ibu' => 'Endang Sulistyawati'],
            ['nama' => 'Irana Soedarwatiningsih', 'nik' => '3578134702780001', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1978-02-07', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Soeratin Dianto', 'ibu' => 'Sukarsih'],
            ],
        ],
        [
            'no_kk' => '3578021603220001', 'status' => 'Non Domisili', 'foto_kk' => 'kk/3578021603220001.jpg',
            'alamat' => 'Bendul Merisi 3/5 B', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Mia Puji Astuti', 'nik' => '3578246509890001', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1989-09-25', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Belum Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Wiyono', 'ibu' => 'Mulimah'],
            ['nama' => 'Athaya Arziky Faeyza Tegar', 'nik' => '3578021906140003', 'jk' => 'L', 'ttl' => 'Pasuruan', 'tgl' => '2014-06-19', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Tegar Siswanto', 'ibu' => 'Mia Puji Astuti'],
            ['nama' => 'Abiyan Fajar Ibrahim', 'nik' => '3578021712150004', 'jk' => 'L', 'ttl' => 'Pasuruan', 'tgl' => '2015-12-17', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Tegar Siswanto', 'ibu' => 'Mia Puji Astuti'],
            ],
        ],
        [
            'no_kk' => '3578022702180010', 'status' => 'Non Domisili', 'foto_kk' => 'kk/3578022702180010.jpg',
            'alamat' => 'Bendulmerisi 4/35', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Nanang Sumantri', 'nik' => '3522090104720001', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1972-04-01', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Wiraswasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Trimo', 'ibu' => 'Sumiati'],
            ],
        ],
        [
            'no_kk' => '3578022208110006', 'status' => 'Non Domisili', 'foto_kk' => 'kk/3578022208110006.jpg',
            'alamat' => 'Bendulmerisi Gg 04/29', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Slamet Kuswanto', 'nik' => '3578022905830001', 'jk' => 'L', 'ttl' => 'Jombang', 'tgl' => '1983-05-29', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Suratno', 'ibu' => 'Badiah'],
            ['nama' => 'Dewi Kartika Sari', 'nik' => '3578084212850003', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1985-12-02', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Samudji (alm)', 'ibu' => 'Sri Wigatiningsih'],
            ['nama' => 'Zahra Rashika Sari', 'nik' => '3578026103090001', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2009-03-21', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Slamet Kuswanto', 'ibu' => 'Dewi Kartika Sari'],
            ['nama' => 'Rosyta Dwi Puspita Sari', 'nik' => '3578025905100001', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2010-05-19', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Slamet Kuswanto', 'ibu' => 'Dewi Kartika Sari'],
            ['nama' => 'Muhammad Yusdan', 'nik' => '3578021406190001', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2019-06-14', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Slamet Kuswanto', 'ibu' => 'Dewi Kartika Sari'],
            ['nama' => 'Muhammad Abyaz Basuki', 'nik' => '3578022605220003', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '2022-05-26', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'Slamet Kuswanto', 'ibu' => 'Dewi Kartika Sari'],
            ],
        ],
        [
            'no_kk' => '3578020311110001', 'status' => 'Non Domisili', 'foto_kk' => 'kk/3578020311110001.jpg',
            'alamat' => 'Bendul Merisi Gg.3/3', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Suhariyanto', 'nik' => '3578020405820007', 'jk' => 'L', 'ttl' => 'Mojokerto', 'tgl' => '1982-05-04', 'agama' => 'Islam', 'pend' => 'SMP/sederajat', 'kerja' => 'Karyawan Swasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Paito / Markam', 'ibu' => 'Sarti / Kayatun'],
            ['nama' => 'Nurul Istiqomah', 'nik' => '3578025307840001', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '1984-07-13', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Mengurus Rumah Tangga', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'Suhariyanto', 'ibu' => 'Nurul Istiqomah'],
            ['nama' => 'Reski Amaliyah Hariyanto', 'nik' => '3578025411070002', 'jk' => 'P', 'ttl' => 'Surabaya', 'tgl' => '2007-11-14', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => null, 'ibu' => null],
            ],
        ],
        [
            'no_kk' => '3526031512210003', 'status' => 'Pendatang', 'foto_kk' => 'kk/3526031512210003.jpg',
            'alamat' => 'Dsn. Duko', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Banangkah', 'kecamatan' => 'Burneh', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'M. Arifin', 'nik' => '3526032301890005', 'jk' => 'L', 'ttl' => 'Bangkalan', 'tgl' => '1990-01-23', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Wiraswasta', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'M. Hesba', 'ibu' => 'B. Tija Als Hayem'],
            ['nama' => 'Alvi Nurdiana', 'nik' => '3526036704020004', 'jk' => 'P', 'ttl' => 'Bangkalan', 'tgl' => '2002-04-27', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Wiraswasta', 'kawin' => 'Kawin', 'hub' => 'Istri', 'ayah' => 'M. Deli', 'ibu' => 'Maryam'],
            ['nama' => 'Afifah Nafisa', 'nik' => '3526034308210001', 'jk' => 'P', 'ttl' => 'Bangkalan', 'tgl' => '2021-08-03', 'agama' => 'Islam', 'pend' => 'Tidak Sekolah', 'kerja' => 'Belum/tidak Bekerja', 'kawin' => 'Belum Kawin', 'hub' => 'Anak', 'ayah' => 'M. Arifin', 'ibu' => 'Alvi Nurdiana'],
            ],
        ],
        [
            'no_kk' => '3578022812220006', 'status' => 'Pendatang', 'foto_kk' => 'kk/3578022812220006.jpg',
            'alamat' => 'Bendul Merisi Jaya Sel. 6 Gg.Buntu Ii/1', 'rt_kk' => '006', 'rw_kk' => '012',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya', 'provinsi' => 'Jawa Timur',
            'anggota' => [
            ['nama' => 'Mohammad Komaruddin', 'nik' => '3578020702990004', 'jk' => 'L', 'ttl' => 'Surabaya', 'tgl' => '1996-12-16', 'agama' => 'Islam', 'pend' => 'SMA/sederajat', 'kerja' => 'Pelajar/mahasiswa', 'kawin' => 'Kawin', 'hub' => 'Kepala Keluarga', 'ayah' => 'Abd.rahman', 'ibu' => 'Sa\'diyah'],
            ],
        ],
    ];

    /**
     * Map no_kk => nama berkas sumber di kk/KK Warga/ (arsip PII, gitignored).
     * Dipakai run() untuk MENYALIN foto KK ke public/kk/ saat seed. Folder kk/
     * sengaja tidak masuk repo — penyalinan hanya terjadi di mesin yang punya
     * arsip; di mesin lain seeder skip dengan peringatan (bukan error).
     */
    private const SRC_MAP = [
        '3526031512210003' => 'A4_M. Arifin.jpg',
        '3527051110110020' => 'A2_Samhari.jpg',
        '3578020101081330' => 'A3_Endra Sukmana.pdf',
        '3578020101084364' => 'A1_Mat Dewi.jpg',
        '3578020101085977' => 'A1_Chrismanu Rudyanto.jpg',
        '3578020101086029' => 'A1_Abas Akadara.jpg',
        '3578020101086759' => 'A1_Samsuri.jpg',
        '3578020101086814' => 'A1_Agus Setio Wandono.jpg',
        '3578020101087242' => 'A1_Matnali.jpg',
        '3578020101087412' => 'A1_Ach Supai.jpg',
        '3578020101088007' => 'A1_Satrijo.jpg',
        '3578020101088773' => 'A1_Soekadji.jpg',
        '3578020101088801' => 'A1_Nanang Kosim.jpg',
        '3578020101089610' => 'A1_Suratno.jpg',
        '3578020101089707' => 'A1_Tadju Subekti.jpg',
        '3578020108220002' => 'A1_Syaiful Anis.jpg',
        '3578020201080747' => 'A1_Mohamad Hadi.jpg',
        '3578020201081199' => 'A1_Achmad Ihsan.jpg',
        '3578020201081200' => 'A1_Henrikus Ruskristiawan.jpg',
        '3578020201084874' => 'A1_Santoso Bin Kiman.jpg',
        '3578020201084878' => 'A1_Akhmad Suryadi.jpg',
        '3578020201086559' => 'A1_Muchamad Achiyat Yayak.jpg',
        '3578020201086566' => 'A1_Moh Ali (Aba).jpg',
        '3578020201230005' => 'A1_Yesi Rasita.jpg',
        '3578020203150011' => 'A1_Nurfadilah.jpg',
        '3578020204140004' => 'A1_Toemi.jpg',
        '3578020206130001' => 'A1_Abdul Rosid.jpg',
        '3578020306220002' => 'A1_Robi Handoyo.jpg',
        '3578020311110001' => 'A3_Suhariyanto.jpg',
        '3578020311180011' => 'A1_Tri Budi Cahyono.jpg',
        '3578020410210008' => 'A1_Dwi Yulianto.jpg',
        '3578020609220004' => 'A1_Misfa.pdf',
        '3578020803220015' => 'A1_Ki Dwi Waluyo Jati.jpg',
        '3578020807210013' => 'A1_Nurul Helmawati.jpg',
        '3578020908170003' => 'A1_M Slamet Riadi.jpg',
        '3578021306110006' => 'A1_Erich Rachmad Saufi.jpg',
        '3578021501200046' => 'A1_Mariyati.jpg',
        '3578021503190003' => 'A1_Jufri.jpg',
        '3578021603220001' => 'A3_Mia Puji Astuti.jpg',
        '3578021701230004' => 'A1_Kayatun.jpg',
        '3578021712120003' => 'A1_Moch Nurawan.jpg',
        '3578021911150008' => 'A1_Yoyok Sumarsono.pdf',
        '3578022003120030' => 'A2_Slamet.jpg',
        '3578022205120012' => 'A1_Chairul Anwar.jpg',
        '3578022207190003' => 'A1_Rajif Al Ahmad Ramadhani.jpg',
        '3578022208110006' => 'A3_Slamet Kuswanto.jpg',
        '3578022501170006' => 'A1_Al Amin Chomami.jpg',
        '3578022507220002' => 'A1_Wakidjan.jpg',
        '3578022511210002' => 'A1_Moh Ali (Yanti).jpg',
        '3578022603200001' => 'A1_Nur Faridah.jpg',
        '3578022605160012' => 'A1_Putri Inang Kurniawati.jpg',
        '3578022605170004' => 'A1_Moh Sarofik (BLURR).jpg',
        '3578022702180010' => 'A3_Nanang Sumantri.jpg',
        '3578022707210011' => 'A1_Wahyuningsih.jpg',
        '3578022710200005' => 'A1_Bedrudin.jpg',
        '3578022712180002' => 'A2_Rohamit.jpg',
        '3578022809150010' => 'A1_Doni Eko Satrianto.jpg',
        '3578022812220006' => 'A4_Mohammad Komaruddin.jpg',
        '3578022901130020' => 'A1_Sunarto.jpg',
        '3578023012200010' => 'A1_Indah Hariningsih.jpg',
        '3578023110200003' => 'A1_Endang Soelistijowati.jpg',
        '3578040201080406' => 'A2_Siswanto.jpg',
        '3578042211110020' => 'A1_Syaiful Romli.jpg',
        '3578072203110003' => 'A2_Tan Djun Bouw-Welly Setiawan.jpg',
        '3578162505210001' => 'A2_Angga Pratama Setya Satria.jpg',
        '3578230101082677' => 'A2_Ahmad Hidayat.pdf',
        '3578240101087171' => 'A1_Wiyono.jpg',
    ];

    public function run(): void
    {
        $rt = Wilayah::where('nama', 'RT 02 RW 03 Bendul Merisi')->where('tingkat', 'RT')->first();
        if (! $rt) {
            $this->command->warn('Wilayah RT 02 RW 03 Bendul Merisi tidak ditemukan — seeder dilewati.');

            return;
        }

        $adminId = 1;
        $totalWarga = 0;

        // Foto KK: arsip kk/KK Warga/ ada di ROOT monorepo (bukan backend/) —
        // seeder jalan dari backend/, public/kk yang di-serve Next.js juga di root.
        $root = dirname(base_path());
        $srcDir = $root.'/kk/KK Warga';
        $pubDir = $root.'/public/kk';
        $arsipAda = is_dir($srcDir);
        if (! $arsipAda) {
            $this->command->warn('Arsip kk/KK Warga/ tidak ditemukan — penyalinan foto KK dilewati (arsip PII memang tidak ikut repo).');
        } elseif (! is_dir($pubDir)) {
            mkdir($pubDir, 0775, true);
        }
        $fotoTerpasang = 0;
        $fotoGagal = [];

        foreach (self::DATA as $fam) {
            $keluarga = Keluarga::updateOrCreate(
                ['no_kk' => $fam['no_kk']],
                [
                    'alamat_kk' => $fam['alamat'],
                    'rt_kk' => $fam['rt_kk'],
                    'rw_kk' => $fam['rw_kk'],
                    'kelurahan_kk' => $fam['kelurahan'],
                    'kecamatan_kk' => $fam['kecamatan'],
                    'kabupaten_kk' => $fam['kabupaten'],
                    'provinsi_kk' => $fam['provinsi'],
                    'status_keluarga' => $fam['status'],
                    'alamat_domisili' => $fam['alamat'],
                    'rt_id' => $rt->id,
                    'foto_kk' => $fam['foto_kk'],
                    // Verifikasi sengaja TIDAK di-set — semua data mulai belum terverifikasi,
                    // admin memverifikasi manual per KK/warga lewat aplikasi.
                ]
            );

            $kepala = null;
            foreach ($fam['anggota'] as $a) {
                $warga = Warga::updateOrCreate(
                    ['nik' => $a['nik']],
                    [
                        'nama_lengkap' => $a['nama'],
                        'tempat_lahir' => $a['ttl'],
                        'tanggal_lahir' => $a['tgl'],
                        'jenis_kelamin' => $a['jk'],
                        'agama' => $a['agama'],
                        'status_perkawinan' => $a['kawin'],
                        'pekerjaan' => $a['kerja'],
                        'pendidikan_terakhir' => $a['pend'],
                        'kewarganegaraan' => 'WNI',
                        'kk_id' => $keluarga->id,
                        'hubungan_keluarga' => $a['hub'],
                        'nama_ayah' => $a['ayah'],
                        'nama_ibu' => $a['ibu'],
                        'created_by' => $adminId,
                        // Verifikasi sengaja tidak di-set (default false) — menyusul via aplikasi.
                    ]
                );
                if ($a['hub'] === 'Kepala Keluarga') {
                    $kepala = $warga;
                }
                $totalWarga++;
            }

            if ($kepala && $keluarga->kepala_keluarga_id !== $kepala->id) {
                $keluarga->update(['kepala_keluarga_id' => $kepala->id]);
            }

            // Salin foto KK terverifikasi dari arsip → public/kk/ bila belum ada
            // (idempotent). Arsip hanya dibaca, tidak pernah dimodifikasi.
            if ($arsipAda && $fam['foto_kk'] && ($src = self::SRC_MAP[$fam['no_kk']] ?? null)) {
                $target = $pubDir.'/'.basename($fam['foto_kk']);
                if (file_exists($target) || @copy($srcDir.'/'.$src, $target)) {
                    $fotoTerpasang++;
                } else {
                    $fotoGagal[] = $src;
                }
            }
        }

        if ($fotoGagal) {
            $this->command->warn('Foto KK gagal disalin ('.count($fotoGagal).'): '.implode(', ', $fotoGagal));
        }

        $this->command->info('Warga RT 02 RW 03 (real): '.count(self::DATA).' KK + '.$totalWarga.' warga · foto KK terpasang: '.$fotoTerpasang.'/'.count(self::SRC_MAP).'.');
    }
}
