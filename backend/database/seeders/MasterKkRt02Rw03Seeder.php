<?php

namespace Database\Seeders;

use App\Models\Keluarga;
use App\Models\Warga;
use App\Models\Wilayah;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * MASTER KK RT 02 RW 03 Bendul Merisi dari daftar kelurahan (113 KK, 2026-08-23) —
 * 52 KK yang BELUM tercakup WargaRealRt02Rw03Seeder (arsip foto kk/ tidak punya
 * dokumennya). DATA FINAL per konfirmasi user 23 Agt: seluruh anggota keluarga
 * sudah masuk — KK yang beranggota 1 memang dihuni kepala keluarga sendirian.
 *
 * Entry minimal per keluarga: baris KK + kepala keluarga stub. Identitas kepala
 * (NIK/tgl lahir/dst) belum diverifikasi dokumen — NIK sintetis deterministik
 * ber-awalan 9 (bukan kode wilayah Kemendagri → mudah diaudit/dibersihkan saat
 * data asli masuk), placeholder lain dipilih yang lolos validasi UI supaya baris
 * tetap bisa disempurnakan lewat form tanpa error.
 *
 * KK Jamari 3578020101081516 TIDAK diseed terpisah — rumah tangga yang sama
 * dengan KK Misfa 3578020609220004 yang sudah ada (konfirmasi user 2026-08-23:
 * Jamari suami Misfa, kini tercatat kepala keluarga di KK kelurahan).
 *
 * GAKIN_MAP menandai status miskin 8 KK existing sesuai master kelurahan.
 * Status domisili: KK terdaftar RT 02 RW 03 = Tetap · KK luar wilayah = Domisili.
 * 2 KK master tanpa nama kepala (3578020901250003, 3578021811240005) TIDAK
 * diseed — dihapus per keputusan user 2026-08-23 (menunggu data lengkap).
 *
 * Idempotent (updateOrCreate by no_kk / nik).
 */
class MasterKkRt02Rw03Seeder extends Seeder
{
    use WithoutModelEvents;

    /** Status miskin (master kelurahan) untuk KK yang sudah di-seed WargaReal. */
    private const GAKIN_MAP = [
        '3578020101085977' => 'Miskin',
        '3578020101087412' => 'Miskin',
        '3578020101088801' => 'Miskin',
        '3578020203150011' => 'Miskin',
        '3578020908170003' => 'Miskin',
        '3578021701230004' => 'Miskin',
        '3578021712120003' => 'Pra-Miskin',
        '3578022205120012' => 'Miskin',
    ];

    private const DATA = [
        [
            'no_kk' => '3578020101083195', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. 5/9', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 5/9',
            'anggota' => [
                ['nik' => '9488071080680284', 'nama' => 'Hairul Anwar', 'tgl' => '1969-04-28', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9396735346859329', 'nama' => 'Varia Indah Subekti', 'tgl' => '1970-02-12', 'jk' => 'P', 'hub' => 'Istri'],
                ['nik' => '9213416369417058', 'nama' => 'Luna Aura Ramadhan', 'tgl' => '2015-11-29', 'jk' => 'P', 'hub' => 'Anak'],
                ['nik' => '9538531190073807', 'nama' => 'Salsabilla Samudra Rinjani', 'tgl' => '2018-12-08', 'jk' => 'P', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578020101084736', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi 3/3-B', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi 3/3-B',
            'anggota' => [
                ['nik' => '9726176531091006', 'nama' => 'Nadjib Sutiono', 'tgl' => '1966-11-18', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9220707397924348', 'nama' => 'Elviana Soviaty,SE', 'tgl' => '1972-11-27', 'jk' => 'P', 'hub' => 'Istri'],
                ['nik' => '9976985112178686', 'nama' => 'Alfaiz Farhan Navinda', 'tgl' => '2003-02-11', 'jk' => 'L', 'hub' => 'Anak'],
                ['nik' => '9258694446658305', 'nama' => 'Davina Auriel Luthfiah Putri', 'tgl' => '2008-12-02', 'jk' => 'P', 'hub' => 'Anak'],
                ['nik' => '9968495669561991', 'nama' => 'Kanaya Salshafiqah Anvienna', 'tgl' => '2018-12-31', 'jk' => 'P', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578020101085798', 'status' => 'Non Domisili', 'ket' => 'Pindah luar kota (data kelurahan 04-05-2023)', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. 4/27', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 4/27',
            'anggota' => [
                ['nik' => '9285881099282871', 'nama' => 'Erik Fahmi', 'tgl' => '1973-10-16', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9697810115323065', 'nama' => 'Eny Yenita Suswanti', 'tgl' => '1971-01-20', 'jk' => 'P', 'hub' => 'Istri'],
                ['nik' => '9009284985796100', 'nama' => 'Yudistira Fahmi Saputra', 'tgl' => '1997-05-10', 'jk' => 'L', 'hub' => 'Anak'],
                ['nik' => '9705156103744924', 'nama' => 'Yudhavino Fahmi Faizan', 'tgl' => '2002-08-08', 'jk' => 'L', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578020101086731', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. 3/5-A', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 3/5-A',
            'anggota' => [
                ['nik' => '9208865693871239', 'nama' => 'Sri Indahwati', 'tgl' => '1963-01-12', 'jk' => 'P', 'hub' => 'Kepala Keluarga'],
            ],
        ],
        [
            'no_kk' => '3578020101086784', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. 3/5-D', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 3/5-D',
            'anggota' => [
                ['nik' => '9139968921241549', 'nama' => 'Djaenal Arifin', 'tgl' => '1972-02-03', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
            ],
        ],
        [
            'no_kk' => '3578020101088300', 'status' => 'Non Domisili', 'ket' => 'Pindah luar kota ke Malang (data kelurahan 11-09-2023)', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. IV/27', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. IV/27',
            'anggota' => [
                ['nik' => '9290722694403810', 'nama' => 'Mohamad Saufiq', 'tgl' => '1970-09-03', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9289298573761057', 'nama' => 'Ken Nia Maswaya', 'tgl' => '2007-10-30', 'jk' => 'P', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578020101089302', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. IV/27', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. IV/27',
            'anggota' => [
                ['nik' => '9714171811743527', 'nama' => 'Yuda Effani', 'tgl' => '1972-04-07', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
            ],
        ],
        [
            'no_kk' => '3578020101089381', 'status' => 'Non Domisili', 'ket' => 'Pindah ke Sidoarjo (data kelurahan 11-09-2023)', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi IV/27', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi IV/27',
            'anggota' => [
                ['nik' => '9383436898125228', 'nama' => 'Siari', 'tgl' => '1970-04-14', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9192094952740500', 'nama' => 'Masiatin', 'tgl' => '1966-09-30', 'jk' => 'P', 'hub' => 'Istri'],
            ],
        ],
        [
            'no_kk' => '3578020101089694', 'status' => 'Non Domisili', 'ket' => 'Kepala meninggal 13-09-2022, anak pindah luar kota (kelurahan 11-09-2023)', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. IV/41', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. IV/41',
            'anggota' => [
                ['nik' => '9139900539461405', 'nama' => 'Arsimah', 'tgl' => '1973-01-05', 'jk' => 'P', 'hub' => 'Kepala Keluarga', 'wafat' => '2022-09-13'],
                ['nik' => '9219762874070000', 'nama' => 'Siti Komariah', 'tgl' => '1977-08-02', 'jk' => 'P', 'hub' => 'Anak'],
                ['nik' => '9290068513165623', 'nama' => 'Supriyatin', 'tgl' => '1977-05-25', 'jk' => 'P', 'hub' => 'Anak'],
                ['nik' => '9814937995042562', 'nama' => 'Annisa Donti Putri', 'tgl' => '2009-05-10', 'jk' => 'P', 'hub' => 'Cucu'],
            ],
        ],
        [
            'no_kk' => '3578020104190001', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. 3/5-D', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 3/5-D',
            'anggota' => [
                ['nik' => '9888324181199483', 'nama' => 'Lilis Mulyowati', 'tgl' => '1973-02-08', 'jk' => 'P', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9853082082430105', 'nama' => 'Mochammad Ilham Arifin Pratama', 'tgl' => '1995-06-28', 'jk' => 'L', 'hub' => 'Anak'],
                ['nik' => '9215538372483922', 'nama' => 'Aliya Rahmawati', 'tgl' => '2004-09-29', 'jk' => 'P', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578020201080749', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Jl. Bendulmerisi IV/35', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Jl. Bendulmerisi IV/35',
            'anggota' => [
                ['nik' => '9509947294090580', 'nama' => 'Koniyem', 'tgl' => '1972-07-18', 'jk' => 'P', 'hub' => 'Kepala Keluarga'],
            ],
        ],
        [
            'no_kk' => '3578020201080799', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. 04/29', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 04/29',
            'anggota' => [
                ['nik' => '9855251788627125', 'nama' => 'Kiran Soejanto', 'tgl' => '1960-05-13', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
            ],
        ],
        [
            'no_kk' => '3578020201081242', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. 3/3', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 3/3',
            'anggota' => [
                ['nik' => '9991152739186201', 'nama' => 'Markam', 'tgl' => '1964-10-06', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
            ],
        ],
        [
            'no_kk' => '3578020201086469', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. 3/5-D', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 3/5-D',
            'anggota' => [
                ['nik' => '9006688317513234', 'nama' => 'Mahatma Djohan', 'tgl' => '1970-12-20', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
            ],
        ],
        [
            'no_kk' => '3578020201086472', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. 3/3-C', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 3/3-C',
            'anggota' => [
                ['nik' => '9920547608555518', 'nama' => 'Harris Teguh Santoso', 'tgl' => '1969-11-02', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9482969572813124', 'nama' => 'Nyomi Umiati', 'tgl' => '1983-03-06', 'jk' => 'P', 'hub' => 'Istri'],
            ],
        ],
        [
            'no_kk' => '3578020201088499', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. 3/5', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 3/5',
            'anggota' => [
                ['nik' => '9836338289089527', 'nama' => 'Biah', 'tgl' => '1959-04-14', 'jk' => 'P', 'hub' => 'Kepala Keluarga'],
            ],
        ],
        [
            'no_kk' => '3578020201089933', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi 4/43', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi 4/43',
            'anggota' => [
                ['nik' => '9980103167882553', 'nama' => 'Aminah', 'tgl' => '1960-01-15', 'jk' => 'P', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9010203300652785', 'nama' => 'Yuliatin', 'tgl' => '1985-07-05', 'jk' => 'P', 'hub' => 'Anak'],
                ['nik' => '9075040731878345', 'nama' => 'Setiawan', 'tgl' => '2007-08-13', 'jk' => 'L', 'hub' => 'Cucu'],
                ['nik' => '9326196691389594', 'nama' => 'Ayu Askanah', 'tgl' => '2008-03-03', 'jk' => 'P', 'hub' => 'Cucu'],
                ['nik' => '9747579332246584', 'nama' => 'Arifin', 'tgl' => '2011-03-04', 'jk' => 'L', 'hub' => 'Cucu'],
            ],
        ],
        [
            'no_kk' => '3578020205090003', 'status' => 'Non Domisili', 'ket' => 'Seluruh keluarga pindah luar kota (kelurahan 11-09-2023)', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. 3/3-C', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 3/3-C',
            'anggota' => [
                ['nik' => '9550627202966494', 'nama' => 'Johan Reinaldi', 'tgl' => '1963-01-05', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9828962497198274', 'nama' => 'Anisah Harris Suharini', 'tgl' => '1977-03-24', 'jk' => 'P', 'hub' => 'Istri'],
                ['nik' => '9728151843802476', 'nama' => 'Azhar Zhafir Reinaldi', 'tgl' => '2009-07-09', 'jk' => 'L', 'hub' => 'Anak'],
                ['nik' => '9332949119737052', 'nama' => 'Azhalea Numa Reinaldi', 'tgl' => '2008-03-08', 'jk' => 'P', 'hub' => 'Anak'],
                ['nik' => '9953695795564005', 'nama' => 'Azriel Maulana Reinaldi', 'tgl' => '1997-03-25', 'jk' => 'L', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578020301080470', 'status' => 'Non Domisili', 'ket' => 'Seluruh keluarga pindah luar kota (kelurahan 11-09-2023)', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. IV No. 27-B', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. IV No. 27-B',
            'anggota' => [
                ['nik' => '9590563625681322', 'nama' => 'Subijantoro', 'tgl' => '1962-03-22', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9250377946037375', 'nama' => 'Tjahyaningrum Lusia', 'tgl' => '1980-04-07', 'jk' => 'P', 'hub' => 'Istri'],
                ['nik' => '9665333650122229', 'nama' => 'Raditya Rafie Ramadhan', 'tgl' => '1995-01-26', 'jk' => 'L', 'hub' => 'Anak'],
                ['nik' => '9116960724081039', 'nama' => 'Rossi Rafael Ramadhan', 'tgl' => '2004-07-30', 'jk' => 'L', 'hub' => 'Anak'],
                ['nik' => '9027571875219281', 'nama' => 'Siswoko', 'tgl' => '1974-01-30', 'jk' => 'L', 'hub' => 'Famili Lain'],
            ],
        ],
        [
            'no_kk' => '3578020301080966', 'status' => 'Non Domisili', 'ket' => 'Seluruh keluarga pindah luar kota (kelurahan 11-09-2023)', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. 4/29', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 4/29',
            'anggota' => [
                ['nik' => '9610477553799855', 'nama' => 'Misdjar', 'tgl' => '1956-09-30', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9199482171059504', 'nama' => 'Sutia', 'tgl' => '1977-09-30', 'jk' => 'P', 'hub' => 'Istri'],
                ['nik' => '9673223879505180', 'nama' => 'Munir', 'tgl' => '1994-01-09', 'jk' => 'L', 'hub' => 'Anak'],
                ['nik' => '9252030399606300', 'nama' => 'Choliq', 'tgl' => '2005-03-19', 'jk' => 'L', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578020403130003', 'status' => 'Tetap', 'miskin' => 'Miskin',
            'alamat' => 'Bendul Merisi Gg. 3/5-D', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendul Merisi Gg. 3/5-D',
            'anggota' => [
                ['nik' => '9724924934832692', 'nama' => 'Wiji', 'tgl' => '1973-05-07', 'jk' => 'P', 'hub' => 'Kepala Keluarga'],
            ],
        ],
        [
            'no_kk' => '3578020510090010', 'status' => 'Domisili', 'miskin' => 'Non',
            'alamat' => 'Bendul Merisi Selatan Buntu 13', 'rt_kk' => '004', 'rw_kk' => '007',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendul Merisi Jaya Besar Timur 58',
            'anggota' => [
                ['nik' => '9456536192149557', 'nama' => 'Achmad Wahyudi', 'tgl' => '1962-04-12', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
            ],
        ],
        [
            'no_kk' => '3578020601150003', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. 4/27 Surabaya', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 4/27 Surabaya',
            'anggota' => [
                ['nik' => '9713229452381374', 'nama' => 'Sutrisno', 'tgl' => '1961-04-09', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9059830520751493', 'nama' => 'Halimah', 'tgl' => '1979-06-08', 'jk' => 'P', 'hub' => 'Istri'],
            ],
        ],
        [
            'no_kk' => '3578020610200008', 'status' => 'Non Domisili', 'ket' => 'Seluruh keluarga pindah luar kota (kelurahan 11-09-2023)', 'miskin' => 'Non',
            'alamat' => 'Bendul Merisi 3/5', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendul Merisi 3/5',
            'anggota' => [
                ['nik' => '9627965491512475', 'nama' => 'Abdul Musirin', 'tgl' => '1972-05-15', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9877045598037124', 'nama' => 'Siti Hotijah', 'tgl' => '1980-11-25', 'jk' => 'P', 'hub' => 'Istri'],
                ['nik' => '9667562123084455', 'nama' => 'Masdalina Safitri', 'tgl' => '1997-07-02', 'jk' => 'P', 'hub' => 'Anak'],
                ['nik' => '9237287010886904', 'nama' => 'M. Afandi', 'tgl' => '2005-07-06', 'jk' => 'L', 'hub' => 'Anak'],
                ['nik' => '9963498307150891', 'nama' => 'Rohman', 'tgl' => '1996-12-17', 'jk' => 'L', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578020706210012', 'status' => 'Non Domisili', 'ket' => 'Seluruh keluarga pindah ke Sidoarjo (kelurahan 11-09-2023)', 'miskin' => 'Non',
            'alamat' => 'Bendul Merisi 4 No. 22', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendul Merisi 4 No. 22',
            'anggota' => [
                ['nik' => '9828765151940899', 'nama' => 'Saiful Effendi', 'tgl' => '1972-05-02', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9415274641604137', 'nama' => 'Rizka Putri Purwanti', 'tgl' => '1997-06-04', 'jk' => 'P', 'hub' => 'Istri'],
                ['nik' => '9293071789836046', 'nama' => 'Mecca Manzil Habibi Fenriz', 'tgl' => '2010-08-09', 'jk' => 'L', 'hub' => 'Anak'],
                ['nik' => '9653430369896440', 'nama' => 'Melody Kyara Oretha Fenriz', 'tgl' => '2018-04-24', 'jk' => 'P', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578020808120006', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendul Merisi 4/35', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendul Merisi 4/35',
            'anggota' => [
                ['nik' => '9604063768679897', 'nama' => 'Karjono', 'tgl' => '1967-06-27', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
            ],
        ],
        [
            'no_kk' => '3578021003200002', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. 5/1', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 5/1',
            'anggota' => [
                ['nik' => '9647108143731944', 'nama' => 'Mardani Pristiyanto', 'tgl' => '1958-11-23', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9177053179596257', 'nama' => 'Riska Muntasiyah', 'tgl' => '1993-01-19', 'jk' => 'P', 'hub' => 'Istri'],
                ['nik' => '9637348569034014', 'nama' => 'Vano Juliansyah', 'tgl' => '2014-07-02', 'jk' => 'L', 'hub' => 'Anak'],
                ['nik' => '9545664924086083', 'nama' => 'Vino Novansyah', 'tgl' => '2011-10-19', 'jk' => 'L', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578021004150007', 'status' => 'Non Domisili', 'ket' => 'Seluruh keluarga pindah luar kota (kelurahan 11-09-2023)', 'miskin' => 'Non',
            'alamat' => 'Bendul Merisi IV/27', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendul Merisi IV/27',
            'anggota' => [
                ['nik' => '9759755828578046', 'nama' => 'Heri Darmawan', 'tgl' => '1960-12-16', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9267402274986585', 'nama' => 'Susi Irawati', 'tgl' => '1981-04-10', 'jk' => 'P', 'hub' => 'Istri'],
            ],
        ],
        [
            'no_kk' => '3578021202130003', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendul Merisi Gg. 3/5-D', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendul Merisi Gg. 3/5-D',
            'anggota' => [
                ['nik' => '9017368628008988', 'nama' => 'Edy Wahyudi', 'tgl' => '1956-04-24', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9558888691817549', 'nama' => 'Lia Widyastuti', 'tgl' => '1986-09-01', 'jk' => 'P', 'hub' => 'Istri'],
                ['nik' => '9828954979162213', 'nama' => 'Nisrina Quds Ivana', 'tgl' => '2016-05-09', 'jk' => 'L', 'hub' => 'Anak'],
                ['nik' => '9626846858842412', 'nama' => 'Yasmine Naila Fadhla', 'tgl' => '2019-10-04', 'jk' => 'P', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578021303150002', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. 4/27 Surabaya', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 4/27 Surabaya',
            'anggota' => [
                ['nik' => '9119054233969884', 'nama' => 'Nopieka Wahyuni', 'tgl' => '1963-03-10', 'jk' => 'P', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9926205396038131', 'nama' => 'Vino Raffa Alvaro', 'tgl' => '2013-03-14', 'jk' => 'L', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578021405100004', 'status' => 'Non Domisili', 'ket' => 'Seluruh keluarga pindah luar kota (kelurahan 11-09-2023)', 'miskin' => 'Non',
            'alamat' => 'Bendul Merisi Bsr. Timur I/22', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendul Merisi Bsr. Timur I/22',
            'anggota' => [
                ['nik' => '9597630557366517', 'nama' => 'Wakidi', 'tgl' => '1972-02-10', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9899611811884311', 'nama' => 'Yatemi', 'tgl' => '1973-04-19', 'jk' => 'P', 'hub' => 'Istri'],
            ],
        ],
        [
            'no_kk' => '3578021411120003', 'status' => 'Non Domisili', 'ket' => 'Seluruh keluarga pindah luar kota (kelurahan 11-09-2023)', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. 3/3-C', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 3/3-C',
            'anggota' => [
                ['nik' => '9601184893568933', 'nama' => 'Irma Harris Rahayu W.', 'tgl' => '1955-07-12', 'jk' => 'P', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9687667449235055', 'nama' => 'Inas Aidah Nur Afiyah Harris', 'tgl' => '2001-02-01', 'jk' => 'P', 'hub' => 'Anak'],
                ['nik' => '9530713981183430', 'nama' => 'Isnaeni Nur Afifah Harris', 'tgl' => '2012-08-16', 'jk' => 'P', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578021605180003', 'status' => 'Non Domisili', 'ket' => 'Keluarga pindah luar kota (kelurahan 11-09-2023)', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi 4/43', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi 4/43',
            'anggota' => [
                ['nik' => '9396210573904511', 'nama' => 'Supriyanto', 'tgl' => '1959-05-23', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9576008535079623', 'nama' => 'Ika Widiawati', 'tgl' => '1992-07-29', 'jk' => 'P', 'hub' => 'Istri'],
                ['nik' => '9149492619985754', 'nama' => 'Muhammad Alvarendra El Zaidan', 'tgl' => '2008-11-10', 'jk' => 'L', 'hub' => 'Anak'],
                ['nik' => '9002143204281361', 'nama' => 'Chezta Azka Supriyanto', 'tgl' => '2009-05-24', 'jk' => 'P', 'hub' => 'Anak'],
                ['nik' => '9643590835138099', 'nama' => 'Muhammad Fadlan Al Firdaus', 'tgl' => '2008-09-06', 'jk' => 'L', 'hub' => 'Anak'],
                ['nik' => '9600023402813472', 'nama' => 'Shiva Azka Supriyanto', 'tgl' => '2013-06-15', 'jk' => 'P', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578021607220003', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendul Merisi 3/5 B', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendul Merisi 3/5 B',
            'anggota' => [
                ['nik' => '9182245452674345', 'nama' => 'Mulimah', 'tgl' => '1962-09-10', 'jk' => 'P', 'hub' => 'Kepala Keluarga'],
            ],
        ],
        [
            'no_kk' => '3578021611160006', 'status' => 'Non Domisili', 'ket' => 'Seluruh keluarga pindah luar kota (kelurahan 11-09-2023)', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. 3/3-C', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 3/3-C',
            'anggota' => [
                ['nik' => '9354867005554805', 'nama' => 'Aan Haryono', 'tgl' => '1963-06-17', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9134416450057102', 'nama' => 'Emi Harris Widyati', 'tgl' => '1992-09-19', 'jk' => 'P', 'hub' => 'Istri'],
                ['nik' => '9442003066995234', 'nama' => 'Andalusia Pracidina Haryono', 'tgl' => '2015-11-23', 'jk' => 'P', 'hub' => 'Anak'],
                ['nik' => '9293796267522989', 'nama' => 'Lubna Ebadi Pracidina Haryono', 'tgl' => '2012-11-13', 'jk' => 'P', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578021802100011', 'status' => 'Non Domisili', 'ket' => 'Kepala & sebagian keluarga pindah ke Sidoarjo (kelurahan 11-09-2023)', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. IV/27', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. IV/27',
            'anggota' => [
                ['nik' => '9642143622936451', 'nama' => 'Nanang Prasetyo', 'tgl' => '1974-07-14', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9914041889222462', 'nama' => 'Sari Kusrini', 'tgl' => '1999-03-27', 'jk' => 'P', 'hub' => 'Istri'],
                ['nik' => '9047926375153754', 'nama' => 'Syabrina Novalia Puteri', 'tgl' => '2016-05-12', 'jk' => 'P', 'hub' => 'Anak'],
                ['nik' => '9543699495220444', 'nama' => 'Ceisya Syafania Ayundini', 'tgl' => '2012-03-25', 'jk' => 'P', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578021802200002', 'status' => 'Non Domisili', 'ket' => 'Seluruh keluarga pindah luar kota (kelurahan 11-09-2023)', 'miskin' => 'Non',
            'alamat' => 'Bendul Merisi 4/43', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendul Merisi 4/43',
            'anggota' => [
                ['nik' => '9963088889287238', 'nama' => 'Endang Suparmi', 'tgl' => '1966-12-25', 'jk' => 'P', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9641502695799775', 'nama' => 'Tirza Arya Pratama', 'tgl' => '2004-11-09', 'jk' => 'L', 'hub' => 'Anak'],
                ['nik' => '9479703654496246', 'nama' => 'Nabila Maulidyah Putri', 'tgl' => '2005-03-15', 'jk' => 'P', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578021806150007', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. 4/29', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 4/29',
            'anggota' => [
                ['nik' => '9707809716551104', 'nama' => 'Moh. Abd Muhyi', 'tgl' => '1959-02-01', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9044889395673659', 'nama' => 'Siti Fathilah', 'tgl' => '1978-05-05', 'jk' => 'P', 'hub' => 'Istri'],
                ['nik' => '9912836381129515', 'nama' => 'Muhammad Faizur Rohman', 'tgl' => '2005-05-16', 'jk' => 'L', 'hub' => 'Anak'],
                ['nik' => '9502051892051314', 'nama' => 'Kayla Adila Rashya', 'tgl' => '2020-05-24', 'jk' => 'P', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578021910160006', 'status' => 'Non Domisili', 'ket' => 'Seluruh keluarga pindah ke Bojonegoro (kelurahan 11-09-2023)', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi IV No. 31', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi IV No. 31',
            'anggota' => [
                ['nik' => '9436068393733058', 'nama' => 'Suhartono', 'tgl' => '1966-10-08', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9775686650162593', 'nama' => 'Yeni Muawanah', 'tgl' => '1982-06-23', 'jk' => 'P', 'hub' => 'Istri'],
                ['nik' => '9809394882633958', 'nama' => 'Razzan Arsen Putra Atharrayhan', 'tgl' => '2004-09-26', 'jk' => 'L', 'hub' => 'Anak'],
                ['nik' => '9543736677063666', 'nama' => 'Callea Poeteri May Zhafirra', 'tgl' => '2004-04-06', 'jk' => 'P', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578022007220004', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. 3/5-D', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 3/5-D',
            'anggota' => [
                ['nik' => '9618594295246192', 'nama' => 'Djuwariningsih', 'tgl' => '1973-01-08', 'jk' => 'P', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9909269323803731', 'nama' => 'Aurelya Febryna Maulidya Putri', 'tgl' => '2003-08-23', 'jk' => 'P', 'hub' => 'Anak'],
                ['nik' => '9749248123276564', 'nama' => 'Zeldi Nurhazaini Kasrizal Guci', 'tgl' => '2011-12-16', 'jk' => 'L', 'hub' => 'Anak'],
                ['nik' => '9050749612097815', 'nama' => 'Ardhynal Safrizal Guci', 'tgl' => '2002-02-18', 'jk' => 'L', 'hub' => 'Anak'],
                ['nik' => '9034057202539163', 'nama' => 'Aghrys Yasrizal Guci', 'tgl' => '2002-04-19', 'jk' => 'L', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578022009220001', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendul Merisi Besar Timur I No. 36', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendul Merisi Besar Timur I No. 36',
            'anggota' => [
                ['nik' => '9443771020447715', 'nama' => 'Yudi Dwi Zena Putra', 'tgl' => '1962-09-21', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9672677961108954', 'nama' => 'Alfina Rosita', 'tgl' => '1994-12-05', 'jk' => 'P', 'hub' => 'Istri'],
                ['nik' => '9343154173014639', 'nama' => 'Muhammad Islami Royana', 'tgl' => '2013-04-01', 'jk' => 'L', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578022101180004', 'status' => 'Non Domisili', 'ket' => 'Seluruh keluarga pindah ke Jakarta Pusat (kelurahan 11-09-2023)', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. 4/31', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 4/31',
            'anggota' => [
                ['nik' => '9545946697532671', 'nama' => 'Fuad Hasan', 'tgl' => '1960-05-30', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9436099611804201', 'nama' => 'Fajar Bilqis', 'tgl' => '1999-04-19', 'jk' => 'P', 'hub' => 'Istri'],
                ['nik' => '9801246107943844', 'nama' => 'Faqihul Aqli Zidane', 'tgl' => '2014-02-30', 'jk' => 'L', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578022104130006', 'status' => 'Non Domisili', 'ket' => 'Seluruh keluarga pindah luar kota (kelurahan 11-09-2023)', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. 04/29', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 04/29',
            'anggota' => [
                ['nik' => '9492009334164317', 'nama' => 'Mohammad Faris', 'tgl' => '1973-12-13', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9628309539611400', 'nama' => 'Sunarti', 'tgl' => '1990-06-08', 'jk' => 'P', 'hub' => 'Istri'],
                ['nik' => '9954607789323838', 'nama' => 'Moh. Rafael Al Farizi', 'tgl' => '2011-10-18', 'jk' => 'L', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578022302180006', 'status' => 'Domisili', 'miskin' => 'Non',
            'alamat' => 'Bendul Merisi Jaya Besar Timur I-A No. 11', 'rt_kk' => '005', 'rw_kk' => '012',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => null,
            'anggota' => [
                ['nik' => '9836370271669624', 'nama' => 'Maulud Finazar', 'tgl' => '1960-05-01', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9735437369603969', 'nama' => 'Latifah', 'tgl' => '1987-04-01', 'jk' => 'P', 'hub' => 'Istri'],
                ['nik' => '9452657587496305', 'nama' => 'Leon Syakir Pratama', 'tgl' => '2015-09-15', 'jk' => 'L', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578022302210003', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi Gg. 3/5', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 3/5',
            'anggota' => [
                ['nik' => '9488854022781847', 'nama' => 'Ribut Suhadi', 'tgl' => '1955-02-05', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
            ],
        ],
        [
            'no_kk' => '3578022308180012', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendul Merisi Gg. 3/5-C', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendul Merisi Gg. 3/5-C',
            'anggota' => [
                ['nik' => '9742764910049191', 'nama' => 'Syamsul Arief Wahyudi', 'tgl' => '1956-01-31', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9856911709135725', 'nama' => 'Nur Rahmawati Rahayu', 'tgl' => '1994-11-29', 'jk' => 'P', 'hub' => 'Istri'],
                ['nik' => '9011643759582591', 'nama' => 'Afsheena Khanza Syahira', 'tgl' => '2011-05-05', 'jk' => 'P', 'hub' => 'Anak'],
                ['nik' => '9824150328228457', 'nama' => 'Gavin Al-Hakim Shakeil', 'tgl' => '2011-10-03', 'jk' => 'L', 'hub' => 'Anak'],
            ],
        ],
        [
            'no_kk' => '3578022505210001', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendulmerisi 4/43', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi 4/43',
            'anggota' => [
                ['nik' => '9179544561237233', 'nama' => 'Mulyana', 'tgl' => '1971-01-11', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
            ],
        ],
        [
            'no_kk' => '3578022705220003', 'status' => 'Domisili', 'miskin' => 'Non',
            'alamat' => 'Bendul Merisi Jaya Besar Timur I No. 11-A', 'rt_kk' => '005', 'rw_kk' => '012',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendul Merisi Besar Timur I No. 11-A',
            'anggota' => [
                ['nik' => '9640261782804860', 'nama' => 'Yohana Eka Sri Wardani', 'tgl' => '1966-11-28', 'jk' => 'P', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9900545876462521', 'nama' => 'Nur Farida', 'tgl' => '1972-04-28', 'jk' => 'P', 'hub' => 'Istri'],
            ],
        ],
        [
            'no_kk' => '3578022708190001', 'status' => 'Tetap', 'miskin' => 'Non',
            'alamat' => 'Bendul Merisi 4 No. 22', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendul Merisi 4 No. 22',
            'anggota' => [
                ['nik' => '9658456571210163', 'nama' => 'Watiha', 'tgl' => '1970-04-17', 'jk' => 'P', 'hub' => 'Kepala Keluarga'],
            ],
        ],
        [
            'no_kk' => '3578022909140002', 'status' => 'Tetap', 'miskin' => 'Miskin',
            'alamat' => 'Bendulmerisi Gg. 4/31', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendulmerisi Gg. 4/31',
            'anggota' => [
                ['nik' => '9666858526434023', 'nama' => 'Siti Zulaikah', 'tgl' => '1958-03-19', 'jk' => 'P', 'hub' => 'Kepala Keluarga'],
            ],
        ],
        [
            'no_kk' => '3578023010120007', 'status' => 'Non Domisili', 'ket' => 'Sebagian keluarga pindah luar kota (kelurahan 11-09-2023)', 'miskin' => 'Non',
            'alamat' => 'Bendul Merisi 4/33', 'rt_kk' => '002', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Bendul Merisi 4/33',
            'anggota' => [
                ['nik' => '9823903252231812', 'nama' => 'Moch. Rasyid', 'tgl' => '1973-01-10', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                ['nik' => '9069291092831989', 'nama' => 'Hairiyah', 'tgl' => '1980-10-03', 'jk' => 'P', 'hub' => 'Istri'],
                ['nik' => '9273873519793240', 'nama' => 'Lista Imanunah', 'tgl' => '2008-02-09', 'jk' => 'P', 'hub' => 'Lainnya'],
                ['nik' => '9869220921922568', 'nama' => 'Nayla Firdauziah', 'tgl' => '2009-04-25', 'jk' => 'P', 'hub' => 'Lainnya'],
            ],
        ],
        [
            'no_kk' => '3578042511110015', 'status' => 'Domisili', 'miskin' => 'Non',
            'alamat' => 'Bendul Merisi Utara Kav. 16-17', 'rt_kk' => '001', 'rw_kk' => '003',
            'kelurahan' => 'Bendul Merisi', 'kecamatan' => 'Wonocolo', 'kabupaten' => 'Kota Surabaya',
            'dom' => 'Jagir Sidosermo 4 Gg. 09 No. 57',
            'anggota' => [
                ['nik' => '9112763500147827', 'nama' => 'Soiran', 'tgl' => '1966-09-10', 'jk' => 'L', 'hub' => 'Kepala Keluarga'],
                 ['nik' => '9682964236603566', 'nama' => 'Sulasmi', 'tgl' => '1971-11-04', 'jk' => 'P', 'hub' => 'Istri'],
             ],
         ],
     ];

    public function run(): void
    {
        $rt = Wilayah::where('nama', 'RT 02 RW 03 Bendul Merisi')->where('tingkat', 'RT')->first();
        if (! $rt) {
            $this->command->warn('Wilayah RT 02 RW 03 Bendul Merisi tidak ditemukan — seeder dilewati.');

            return;
        }

        $adminId = 1;

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
                    'provinsi_kk' => 'Jawa Timur',
                    'status_keluarga' => $fam['status'],
                    'status_miskin' => $fam['miskin'],
                    'keterangan_status' => $fam['ket'] ?? null,
                    'alamat_domisili' => $fam['dom'],
                    'rt_id' => $rt->id,
                    'foto_kk' => null,
                ]
            );

            // Anggota: kepala + keluarga — placeholder konsisten validasi UI, NIK
            // sintetis awalan 9 (bukan kode wilayah resmi) sampai NIK asli tersedia.
            foreach ($fam['anggota'] as $a) {
                $warga = Warga::updateOrCreate(
                    ['nik' => $a['nik']],
                    [
                        'nama_lengkap' => $a['nama'],
                        'tempat_lahir' => 'Tidak Diketahui',
                        'tanggal_lahir' => $a['tgl'],
                        'jenis_kelamin' => $a['jk'],
                        'golongan_darah' => 'Tidak Tahu',
                        'agama' => 'Islam',
                        'status_perkawinan' => in_array($a['hub'], ['Anak', 'Cucu']) ? 'Belum Kawin' : 'Kawin',
                        'pekerjaan' => $a['hub'] === 'Anak' ? 'Pelajar/mahasiswa' : 'Belum/tidak Bekerja',
                        'pendidikan_terakhir' => $a['hub'] === 'Anak' ? 'Tidak Sekolah' : 'SD/sederajat',
                        'kewarganegaraan' => 'WNI',
                        'kk_id' => $keluarga->id,
                        'hubungan_keluarga' => $a['hub'],
                        'meninggal' => isset($a['wafat']),
                        'tanggal_meninggal' => $a['wafat'] ?? null,
                        'created_by' => $adminId,
                    ]
                );
                if ($a['hub'] === 'Kepala Keluarga' && $keluarga->kepala_keluarga_id !== $warga->id) {
                    $keluarga->update(['kepala_keluarga_id' => $warga->id]);
                }
            }
        }

        // Sinkronkan status miskin KK existing (WargaReal) sesuai master kelurahan
        foreach (self::GAKIN_MAP as $noKk => $status) {
            Keluarga::where('no_kk', $noKk)->update(['status_miskin' => $status]);
        }

        $this->command->info('Master KK RT 02 RW 03: '.count(self::DATA).' KK kelurahan (stub, tanpa arsip dokumen) · gakin existing: '.count(self::GAKIN_MAP).'.');
    }
}
