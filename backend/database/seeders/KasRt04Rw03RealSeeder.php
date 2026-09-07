<?php

namespace Database\Seeders;

use App\Models\KasTransaksi;
use App\Models\KasUnit;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;

/**
 * Buku kas ASLI RT 04 RW 03 Bendul Merisi — Januari 2016 s.d. Agustus 2026.
 * Sumber: rt4.xlsx (buku kas fisik bendahara RT 04), 400 baris,
 * 139 masuk Rp73,559,650 / 261 keluar Rp65,519,150 — saldo akhir Rp8,040,500 per akhir Agustus 2026.
 * Rantai saldo terverifikasi 100% vs kolom "Jumlah" buku (nol selisih).
 *
 * Konvensi:
 *  - Buku hanya mencatat TAHUN + BULAN (tanpa tanggal) — tanggal disimpan tgl 1 bulan
 *    bersangkutan; urutan dalam bulan mengikuti urutan baris buku.
 *  - 2 baris "Iuran warga" Rp0 (April & Juni 2024, tanpa efek saldo) tidak ditanam.
 *  - Keterangan asli dipertahankan verbatim (termasuk "butuh keterangan" — 21 baris
 *    berlabel begitu adalah baris buku nyata, kategori Lain-lain; 1 baris "butuh
 *    keterangan" masuk Agt 2026 ikut ditanam).
 *  - Setoran ke RW dalam segala label ("Bayar RW", "Bayar iuran RW 6 bln",
 *    "Bayar Agustusan RW", "Bayar HUT Agts RW", dst) = kategori Iuran (keputusan user).
 *
 * Kategori map: iuran warga/saldo awal→Iuran/Saldo Awal · sumbangan/Asia Net/bagi hasil
 * RW→Donasi · P Gimin honor rutin→Operasional · kematian/makam/takziah/THR makam→Pemakaman ·
 * posga(+makam)/jentik/disinfektan→Kesehatan · kerbak/kerja bakti/tirakatan/HUT 17 Agt/
 * karnaval/isro'-maulud/halbi/KSH/rekreasi/trawas→Kegiatan · pilihan RT/snack/cangkrukan→Rapat ·
 * beli barang/lampu/benner/fotocopy/kartu iuran→Perlengkapan · pinjaman/sewa/lainnya→Lain-lain.
 *
 * Idempotent: baris manual unit RT 04 dihapus lalu ditanam ulang.
 * GENERATED oleh pipeline /tmp/opencode/kas_xlsx/pipeline.py — jangan edit manual.
 */
class KasRt04Rw03RealSeeder extends Seeder
{
    /** Saldo akhir sesuai buku asli per akhir Agustus 2026 — self-check. */
    private const SALDO_AKHIR = 8040500;

    /** @var array<int, array{0: string, 1: string, 2: int, 3: string, 4: string}> [tanggal, tipe, jumlah, kategori, keterangan] */
    private const TRANSAKSI = [
        // ── Januari 2016 ────────────────────
        ['2016-01-01', 'masuk', 3706450, 'Saldo Awal', 'Saldo Desember 2015'],
        ['2016-01-01', 'masuk', 814000, 'Iuran', 'Iuran warga'],
        ['2016-01-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        ['2016-01-01', 'keluar', 18000, 'Perlengkapan', 'Fotocopi Lap. + buku'],
        ['2016-01-01', 'keluar', 100000, 'Perlengkapan', 'Pesan Kartu Iuran'],
        // ── Februari 2016 ────────────────────
        ['2016-02-01', 'masuk', 470500, 'Iuran', 'Iuran warga'],
        ['2016-02-01', 'masuk', 200000, 'Donasi', 'Sumbangan P. Joko (kas)'],
        ['2016-02-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        // ── Maret 2016 ────────────────────
        ['2016-03-01', 'masuk', 1478500, 'Iuran', 'Iuran warga'],
        ['2016-03-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        ['2016-03-01', 'keluar', 500000, 'Lain-lain', 'Di pinjam P. Gimin'],
        ['2016-03-01', 'keluar', 100000, 'Pemakaman', 'Diambil kematian (B. Ana)'],
        ['2016-03-01', 'keluar', 114000, 'Perlengkapan', 'Beli lampu 2 -> kampung 3'],
        // ── April 2016 ────────────────────
        ['2016-04-01', 'masuk', 500500, 'Iuran', 'Iuran warga'],
        ['2016-04-01', 'keluar', 10300, 'Kegiatan', 'Foto kk + kerja bakti'],
        // ── Mei 2016 ────────────────────
        ['2016-05-01', 'masuk', 447500, 'Iuran', 'Iuran warga'],
        ['2016-05-01', 'keluar', 400000, 'Operasional', 'Bayar P. Gimin (byr utang 50.000)'],
        // ── Juni 2016 ────────────────────
        ['2016-06-01', 'masuk', 664500, 'Iuran', 'Iuran warga'],
        ['2016-06-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        ['2016-06-01', 'keluar', 115000, 'Perlengkapan', 'Beli lampu gapura dpn'],
        ['2016-06-01', 'keluar', 500000, 'Operasional', 'THR P. Gimin + makam'],
        // ── Juli 2016 ────────────────────
        ['2016-07-01', 'masuk', 654500, 'Iuran', 'Iuran warga'],
        ['2016-07-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        // ── Agustus 2016 ────────────────────
        ['2016-08-01', 'masuk', 838000, 'Iuran', 'Iuran warga'],
        ['2016-08-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        ['2016-08-01', 'keluar', 19500, 'Perlengkapan', 'Fotocopy krt Okt + Pemberitahuan'],
        ['2016-08-01', 'keluar', 459000, 'Kegiatan', 'Kerja Bakti + umbul + benner'],
        ['2016-08-01', 'keluar', 168000, 'Lain-lain', 'Pengeluaran lainnya (tanpa keterangan)'],
        // ── September 2016 ────────────────────
        ['2016-09-01', 'masuk', 478000, 'Iuran', 'Iuran warga'],
        ['2016-09-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        ['2016-09-01', 'keluar', 800000, 'Iuran', 'Bayar Iuran RW Agts s/d Des \'16'],
        // ── Oktober 2016 ────────────────────
        ['2016-10-01', 'masuk', 470500, 'Iuran', 'Iuran warga'],
        ['2016-10-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        // ── November 2016 ────────────────────
        ['2016-11-01', 'masuk', 663500, 'Iuran', 'Iuran warga'],
        ['2016-11-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        ['2016-11-01', 'keluar', 200000, 'Rapat', 'Konsumsi Pilihan RT'],
        ['2016-11-01', 'keluar', 200000, 'Pemakaman', 'Kematian putra Didik + Lukman'],
        // ── Desember 2016 ────────────────────
        ['2016-12-01', 'masuk', 1580500, 'Iuran', 'Iuran warga'],
        ['2016-12-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        ['2016-12-01', 'keluar', 112000, 'Kegiatan', 'Beli mika maulut'],
        ['2016-12-01', 'keluar', 100000, 'Pemakaman', 'Kematian H. Iksan'],
        ['2016-12-01', 'keluar', 116700, 'Lain-lain', 'butuh keterangan'],
        // ── Januari 2017 ────────────────────
        ['2017-01-01', 'masuk', 548500, 'Iuran', 'Iuran warga'],
        ['2017-01-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        // ── Februari 2017 ────────────────────
        ['2017-02-01', 'masuk', 592500, 'Iuran', 'Iuran warga'],
        ['2017-02-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        ['2017-02-01', 'keluar', 100000, 'Perlengkapan', 'Beli lampu dpn Hasip + Juki'],
        // ── Maret 2017 ────────────────────
        ['2017-03-01', 'masuk', 733500, 'Iuran', 'Iuran warga'],
        ['2017-03-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        ['2017-03-01', 'keluar', 100000, 'Perlengkapan', 'Beli Lampu 2 (mama + Hj. Mimah)'],
        // ── April 2017 ────────────────────
        ['2017-04-01', 'masuk', 420000, 'Iuran', 'Iuran warga'],
        ['2017-04-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        ['2017-04-01', 'keluar', 150000, 'Rapat', 'Utk cangkrukan RW'],
        ['2017-04-01', 'keluar', 50000, 'Lain-lain', 'Tanam Cabe'],
        // ── Mei 2017 ────────────────────
        ['2017-05-01', 'masuk', 1649000, 'Iuran', 'Iuran warga'],
        ['2017-05-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        // ── Juni 2017 ────────────────────
        ['2017-06-01', 'masuk', 799000, 'Iuran', 'Iuran warga'],
        ['2017-06-01', 'keluar', 900000, 'Operasional', 'Bayar P. Gimin + THR'],
        ['2017-06-01', 'keluar', 59000, 'Perlengkapan', 'Beli Lampu dpn Hj. Mimah'],
        ['2017-06-01', 'keluar', 100000, 'Pemakaman', 'Bayar THR makam'],
        // ── Juli 2017 ────────────────────
        ['2017-07-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        ['2017-07-01', 'keluar', 100000, 'Pemakaman', 'Kematian B. Beni'],
        // ── Agustus 2017 ────────────────────
        ['2017-08-01', 'masuk', 871000, 'Iuran', 'Iuran warga'],
        ['2017-08-01', 'keluar', 600000, 'Iuran', 'Bayar Iuran RW Jan\'17 s/d Des\'17'],
        ['2017-08-01', 'keluar', 381000, 'Kegiatan', 'Beli cat utk kerja bakti'],
        ['2017-08-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        ['2017-08-01', 'keluar', 125000, 'Perlengkapan', 'Beli kupon kecamatan 50 lbr'],
        // ── September 2017 ────────────────────
        ['2017-09-01', 'masuk', 675500, 'Iuran', 'Iuran warga'],
        ['2017-09-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        // ── Oktober 2017 ────────────────────
        ['2017-10-01', 'masuk', 394000, 'Iuran', 'Iuran warga'],
        ['2017-10-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        // ── November 2017 ────────────────────
        ['2017-11-01', 'masuk', 331000, 'Iuran', 'Iuran warga'],
        ['2017-11-01', 'keluar', 30000, 'Perlengkapan', 'Beli bolam 2 a 15.000'],
        ['2017-11-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        // ── Desember 2017 ────────────────────
        ['2017-12-01', 'masuk', 389000, 'Iuran', 'Iuran warga'],
        ['2017-12-01', 'keluar', 30000, 'Perlengkapan', 'Beli bolam 2 a 15.000'],
        ['2017-12-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        // ── Januari 2018 ────────────────────
        ['2018-01-01', 'masuk', 683500, 'Iuran', 'Iuran warga'],
        ['2018-01-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        ['2018-01-01', 'keluar', 100000, 'Pemakaman', 'Kematian P. Arif'],
        // ── Februari 2018 ────────────────────
        ['2018-02-01', 'masuk', 419500, 'Iuran', 'Iuran warga'],
        ['2018-02-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        // ── Maret 2018 ────────────────────
        ['2018-03-01', 'masuk', 382500, 'Iuran', 'Iuran warga'],
        ['2018-03-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        // ── April 2018 ────────────────────
        ['2018-04-01', 'masuk', 516000, 'Iuran', 'Iuran warga'],
        ['2018-04-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        // ── Mei 2018 ────────────────────
        ['2018-05-01', 'masuk', 577500, 'Iuran', 'Iuran warga'],
        ['2018-05-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        ['2018-05-01', 'keluar', 100000, 'Perlengkapan', 'Beli Lampu 2'],
        ['2018-05-01', 'keluar', 450, 'Lain-lain', 'butuh keterangan'],
        // ── Juni 2018 ────────────────────
        ['2018-06-01', 'masuk', 558000, 'Iuran', 'Iuran warga'],
        ['2018-06-01', 'keluar', 900000, 'Operasional', 'Bayar P. Gimin & HR'],
        ['2018-06-01', 'keluar', 100000, 'Pemakaman', 'THR Makam'],
        // ── Juli 2018 ────────────────────
        ['2018-07-01', 'masuk', 2092500, 'Iuran', 'Iuran warga'],
        ['2018-07-01', 'keluar', 450000, 'Operasional', 'Bayar P. Gimin'],
        // ── Agustus 2018 ────────────────────
        ['2018-08-01', 'masuk', 918000, 'Iuran', 'Iuran warga'],
        ['2018-08-01', 'keluar', 500000, 'Operasional', 'Bayar P. Gimin'],
        ['2018-08-01', 'keluar', 475000, 'Kegiatan', 'Utk kerja bakti Gapura (beli cat)'],
        ['2018-08-01', 'keluar', 150000, 'Perlengkapan', 'Cat uv'],
        // ── September 2018 ────────────────────
        ['2018-09-01', 'masuk', 344500, 'Iuran', 'Iuran warga'],
        ['2018-09-01', 'keluar', 500000, 'Operasional', 'Bayar P. Gimin'],
        ['2018-09-01', 'keluar', 600000, 'Iuran', 'Bayar RW'],
        ['2018-09-01', 'keluar', 70000, 'Perlengkapan', 'Cetak buku iuran'],
        // ── Oktober 2018 ────────────────────
        ['2018-10-01', 'masuk', 474000, 'Iuran', 'Iuran warga'],
        ['2018-10-01', 'keluar', 500000, 'Operasional', 'Bayar P. Gimin'],
        ['2018-10-01', 'keluar', 100000, 'Pemakaman', 'Kematian B. Sugiyah Kasih'],
        // ── November 2018 ────────────────────
        ['2018-11-01', 'masuk', 400500, 'Iuran', 'Iuran warga'],
        ['2018-11-01', 'keluar', 500000, 'Operasional', 'Bayar P. Gimin'],
        ['2018-11-01', 'keluar', 100000, 'Pemakaman', 'Kematian B. Teguh'],
        ['2018-11-01', 'keluar', 58000, 'Perlengkapan', 'Beli lampu (dpn umik)'],
        // ── Desember 2018 ────────────────────
        ['2018-12-01', 'masuk', 473500, 'Iuran', 'Iuran warga'],
        ['2018-12-01', 'keluar', 500000, 'Operasional', 'Bayar P. Gimin'],
        ['2018-12-01', 'masuk', 300000, 'Donasi', 'Dpt dari RW pemanfaatan lahan'],
        ['2018-12-01', 'keluar', 500, 'Lain-lain', 'butuh keterangan'],
        // ── Januari 2019 ────────────────────
        ['2019-01-01', 'masuk', 1000, 'Lain-lain', 'butuh keterangan'],
        ['2019-01-01', 'masuk', 662000, 'Iuran', 'Iuran warga'],
        ['2019-01-01', 'keluar', 500000, 'Operasional', 'Bayar P. Gimin'],
        // ── Februari 2019 ────────────────────
        ['2019-02-01', 'masuk', 890500, 'Iuran', 'Iuran warga'],
        ['2019-02-01', 'keluar', 500000, 'Operasional', 'Bayar P. Gimin'],
        // ── Maret 2019 ────────────────────
        ['2019-03-01', 'masuk', 381000, 'Iuran', 'Iuran warga'],
        ['2019-03-01', 'keluar', 500000, 'Operasional', 'Bayar P. Gimin'],
        // ── April 2019 ────────────────────
        ['2019-04-01', 'masuk', 406500, 'Iuran', 'Iuran warga'],
        ['2019-04-01', 'keluar', 100000, 'Operasional', 'Kematian P. Gimin'],
        ['2019-04-01', 'keluar', 100000, 'Pemakaman', 'Kematian B. Rusdi'],
        // ── Mei 2019 ────────────────────
        ['2019-05-01', 'masuk', 125000, 'Iuran', 'Iuran warga'],
        ['2019-05-01', 'keluar', 40000, 'Perlengkapan', 'Beli Lampu Gapura'],
        ['2019-05-01', 'keluar', 100000, 'Pemakaman', 'HR Makam'],
        ['2019-05-01', 'keluar', 100000, 'Lain-lain', 'butuh keterangan'],
        // ── Juni 2019 ────────────────────
        ['2019-06-01', 'masuk', 60000, 'Iuran', 'Iuran warga'],
        // ── Juli 2019 ────────────────────
        ['2019-07-01', 'masuk', 262000, 'Iuran', 'Iuran warga'],
        ['2019-07-01', 'keluar', 3000000, 'Kegiatan', 'Bayar Bis ke Malang'],
        // ── Agustus 2019 ────────────────────
        ['2019-08-01', 'masuk', 1084500, 'Iuran', 'Iuran warga'],
        ['2019-08-01', 'masuk', 48500, 'Lain-lain', 'butuh keterangan'],
        ['2019-08-01', 'keluar', 485500, 'Kegiatan', 'Beli Cat utk 17 Agt'],
        // ── September 2019 ────────────────────
        ['2019-09-01', 'masuk', 157000, 'Iuran', 'Iuran warga'],
        ['2019-09-01', 'keluar', 85000, 'Operasional', 'Diambil Sampah B. Kasiyat'],
        // ── Oktober 2019 ────────────────────
        ['2019-10-01', 'masuk', 161500, 'Iuran', 'Iuran warga'],
        ['2019-10-01', 'keluar', 100000, 'Pemakaman', 'Kematian P. Teguh'],
        ['2019-10-01', 'keluar', 8800, 'Perlengkapan', 'Foto Copi undangan RT'],
        ['2019-10-01', 'keluar', 200000, 'Rapat', 'Snack Pilihan RT'],
        // ── November 2019 ────────────────────
        ['2019-11-01', 'masuk', 303500, 'Iuran', 'Iuran warga'],
        ['2019-11-01', 'masuk', 100000, 'Lain-lain', 'butuh keterangan'],
        ['2019-11-01', 'keluar', 100000, 'Pemakaman', 'Kematian P. No/Suryono'],
        ['2019-11-01', 'keluar', 100000, 'Pemakaman', 'Kematian B. Kantun'],
        ['2019-11-01', 'keluar', 53000, 'Perlengkapan', 'Beli Lampu dpn Hj. Mimah'],
        ['2019-11-01', 'keluar', 600000, 'Iuran', 'Bayar iuran RW jan s/d Des'],
        // ── Desember 2019 ────────────────────
        ['2019-12-01', 'masuk', 282000, 'Iuran', 'Iuran warga'],
        // ── Januari 2020 ────────────────────
        ['2020-01-01', 'masuk', 515500, 'Iuran', 'Iuran warga'],
        ['2020-01-01', 'keluar', 64000, 'Perlengkapan', 'Beli lampu dpn umik'],
        // ── Februari 2020 ────────────────────
        ['2020-02-01', 'masuk', 178000, 'Iuran', 'Iuran warga'],
        // ── Maret 2020 ────────────────────
        ['2020-03-01', 'masuk', 226500, 'Iuran', 'Iuran warga'],
        ['2020-03-01', 'keluar', 200000, 'Perlengkapan', 'Beli alat semprot'],
        ['2020-03-01', 'keluar', 100000, 'Pemakaman', 'Kematian Selamin'],
        ['2020-03-01', 'keluar', 20000, 'Perlengkapan', 'Beli Lampu dpn rumah'],
        // ── April 2020 ────────────────────
        ['2020-04-01', 'masuk', 322500, 'Iuran', 'Iuran warga'],
        ['2020-04-01', 'keluar', 55000, 'Perlengkapan', 'Foto Copy jilid 1 & II'],
        // ── Mei 2020 ────────────────────
        ['2020-05-01', 'masuk', 183000, 'Iuran', 'Iuran warga'],
        ['2020-05-01', 'keluar', 12000, 'Perlengkapan', 'Foto copy TASA III'],
        ['2020-05-01', 'keluar', 50000, 'Kesehatan', 'Urun disinfektan'],
        // ── Juni 2020 ────────────────────
        ['2020-06-01', 'masuk', 663000, 'Iuran', 'Iuran warga'],
        ['2020-06-01', 'keluar', 100000, 'Pemakaman', 'Kematian B. Kastamah'],
        ['2020-06-01', 'keluar', 90000, 'Perlengkapan', 'Beli Bolam 2'],
        ['2020-06-01', 'keluar', 150000, 'Perlengkapan', 'Beli HT'],
        ['2020-06-01', 'keluar', 12000, 'Perlengkapan', 'Foto Copy Jaga Lu'],
        ['2020-06-01', 'keluar', 250000, 'Perlengkapan', 'Beli Lampu rainbow 10 bj'],
        // ── Juli 2020 ────────────────────
        ['2020-07-01', 'masuk', 207000, 'Iuran', 'Iuran warga'],
        ['2020-07-01', 'keluar', 12000, 'Perlengkapan', 'Foto Copy jilid V'],
        ['2020-07-01', 'keluar', 250000, 'Perlengkapan', 'Beli Lampu rainbow co'],
        ['2020-07-01', 'keluar', 105000, 'Perlengkapan', 'Kawat 15 bj a 7rb'],
        // ── Agustus 2020 ────────────────────
        ['2020-08-01', 'masuk', 202500, 'Iuran', 'Iuran warga'],
        ['2020-08-01', 'keluar', 225000, 'Perlengkapan', 'Beli pring 15 bj a 15rb'],
        // ── September 2020 ────────────────────
        ['2020-09-01', 'masuk', 308000, 'Iuran', 'Iuran warga'],
        // ── Oktober 2020 ────────────────────
        ['2020-10-01', 'keluar', 100000, 'Pemakaman', 'Kematian P. Kasiyat'],
        // ── November 2020 ────────────────────
        ['2020-11-01', 'masuk', 382500, 'Iuran', 'Iuran warga'],
        // ── Desember 2020 ────────────────────
        ['2020-12-01', 'masuk', 249500, 'Iuran', 'Iuran warga'],
        ['2020-12-01', 'keluar', 600000, 'Iuran', 'Bayar iuran RW 1 th'],
        ['2020-12-01', 'keluar', 1000, 'Lain-lain', 'butuh keterangan'],
        // ── Januari 2021 ────────────────────
        ['2021-01-01', 'masuk', 1500, 'Lain-lain', 'butuh keterangan'],
        ['2021-01-01', 'masuk', 1299500, 'Iuran', 'Iuran warga'],
        ['2021-01-01', 'keluar', 70000, 'Perlengkapan', 'Beli Bolam 2'],
        // ── Februari 2021 ────────────────────
        ['2021-02-01', 'masuk', 334500, 'Iuran', 'Iuran warga'],
        ['2021-02-01', 'keluar', 35000, 'Perlengkapan', 'Beli lampu dpp/mas Andik'],
        // ── Maret 2021 ────────────────────
        ['2021-03-01', 'masuk', 392500, 'Iuran', 'Iuran warga'],
        // ── April 2021 ────────────────────
        ['2021-04-01', 'masuk', 335000, 'Iuran', 'Iuran warga'],
        // ── Mei 2021 ────────────────────
        ['2021-05-01', 'masuk', 494000, 'Iuran', 'Iuran warga'],
        // ── Juni 2021 ────────────────────
        ['2021-06-01', 'masuk', 349000, 'Iuran', 'Iuran warga'],
        // ── Juli 2021 ────────────────────
        ['2021-07-01', 'keluar', 150000, 'Pemakaman', 'Kematian B. Subari'],
        ['2021-07-01', 'keluar', 150000, 'Pemakaman', 'Kematian B. Sochartatis'],
        ['2021-07-01', 'keluar', 150000, 'Pemakaman', 'Kematian Rudik W.'],
        ['2021-07-01', 'keluar', 150000, 'Pemakaman', 'Kematian Didik P.S.'],
        // ── Agustus 2021 ────────────────────
        ['2021-08-01', 'masuk', 546500, 'Iuran', 'Iuran warga'],
        ['2021-08-01', 'keluar', 420000, 'Perlengkapan', 'Beli Bendera 30 bj @ 14rb'],
        ['2021-08-01', 'keluar', 35000, 'Perlengkapan', 'Lampu Sari'],
        ['2021-08-01', 'keluar', 367500, 'Perlengkapan', 'Lampu RT 3 bh'],
        // ── September 2021 ────────────────────
        ['2021-09-01', 'masuk', 342000, 'Iuran', 'Iuran warga'],
        ['2021-09-01', 'keluar', 150000, 'Pemakaman', 'Kematian B. Cholifah'],
        // ── Oktober 2021 ────────────────────
        ['2021-10-01', 'masuk', 322000, 'Iuran', 'Iuran warga'],
        ['2021-10-01', 'keluar', 200000, 'Kegiatan', 'Konsumsi perantingan'],
        ['2021-10-01', 'keluar', 200, 'Lain-lain', 'butuh keterangan'],
        // ── November 2021 ────────────────────
        ['2021-11-01', 'masuk', 631000, 'Iuran', 'Iuran warga'],
        // ── Desember 2021 ────────────────────
        ['2021-12-01', 'masuk', 270000, 'Iuran', 'Iuran warga'],
        ['2021-12-01', 'keluar', 45000, 'Perlengkapan', 'Beli Lampu dpn bakso'],
        ['2021-12-01', 'keluar', 600000, 'Iuran', 'Bayar iuran RW jan s/d des \'21'],
        ['2021-12-01', 'keluar', 33600, 'Perlengkapan', 'Foto Copy'],
        ['2021-12-01', 'keluar', 90000, 'Perlengkapan', 'Benner CT Agt'],
        ['2021-12-01', 'masuk', 700, 'Lain-lain', 'butuh keterangan'],
        // ── Januari 2022 ────────────────────
        ['2022-01-01', 'masuk', 486000, 'Iuran', 'Iuran warga'],
        ['2022-01-01', 'keluar', 150000, 'Pemakaman', 'Kematian Firman (Pimun)'],
        ['2022-01-01', 'keluar', 18000, 'Perlengkapan', 'Foto copy Laporan iuran'],
        ['2022-01-01', 'keluar', 100000, 'Perlengkapan', 'Cetak Kartu Iuran'],
        ['2022-01-01', 'keluar', 31000, 'Perlengkapan', 'Foto Copy Data Warga'],
        ['2022-01-01', 'keluar', 400000, 'Kegiatan', 'Konsumsi kerja bakti massal'],
        // ── Februari 2022 ────────────────────
        ['2022-02-01', 'masuk', 1576000, 'Iuran', 'Iuran warga'],
        ['2022-02-01', 'keluar', 150000, 'Pemakaman', 'Kematian B. Dina'],
        // ── Maret 2022 ────────────────────
        ['2022-03-01', 'masuk', 597000, 'Iuran', 'Iuran warga'],
        // ── April 2022 ────────────────────
        ['2022-04-01', 'masuk', 534000, 'Iuran', 'Iuran warga'],
        // ── Mei 2022 ────────────────────
        ['2022-05-01', 'masuk', 325000, 'Iuran', 'Iuran warga'],
        ['2022-05-01', 'keluar', 600000, 'Kegiatan', 'Diambil utk Halbi PKK RT'],
        // ── Juni 2022 ────────────────────
        ['2022-06-01', 'masuk', 219000, 'Iuran', 'Iuran warga'],
        // ── Juli 2022 ────────────────────
        ['2022-07-01', 'masuk', 676000, 'Iuran', 'Iuran warga'],
        ['2022-07-01', 'keluar', 150000, 'Pemakaman', 'Kematian B. Sutris'],
        // ── Agustus 2022 ────────────────────
        ['2022-08-01', 'masuk', 253000, 'Iuran', 'Iuran warga'],
        ['2022-08-01', 'keluar', 300000, 'Kegiatan', 'Bayar HUT 17 Agustus kantar'],
        ['2022-08-01', 'keluar', 300000, 'Perlengkapan', 'Beli lampu dicco + pitingan 6 bj'],
        ['2022-08-01', 'keluar', 90000, 'Perlengkapan', 'Bendrat + paku + konsumsi'],
        ['2022-08-01', 'keluar', 140000, 'Perlengkapan', 'Benner + kabel'],
        ['2022-08-01', 'keluar', 298763, 'Perlengkapan', 'Semen + lem + cat mowlakk'],
        ['2022-08-01', 'keluar', 230000, 'Kegiatan', 'Beli kardus utk mlm tirakatan'],
        // ── September 2022 ────────────────────
        ['2022-09-01', 'masuk', 554000, 'Iuran', 'Iuran warga'],
        ['2022-09-01', 'keluar', 150000, 'Pemakaman', 'Kematian Bpk S. Sutrisno'],
        // ── Oktober 2022 ────────────────────
        ['2022-10-01', 'masuk', 334000, 'Iuran', 'Iuran warga'],
        ['2022-10-01', 'keluar', 40000, 'Kegiatan', 'Beli mika maulud'],
        ['2022-10-01', 'keluar', 48000, 'Perlengkapan', 'Beli bolam dpn P. Ilyas'],
        // ── November 2022 ────────────────────
        ['2022-11-01', 'masuk', 454000, 'Iuran', 'Iuran warga'],
        ['2022-11-01', 'keluar', 300000, 'Rapat', 'Konsumsi pemilihan RT + APK'],
        ['2022-11-01', 'keluar', 200000, 'Lain-lain', 'Sewa kursi 50 + meja'],
        ['2022-11-01', 'keluar', 250000, 'Kegiatan', 'Yang lelah panitia'],
        // ── Desember 2022 ────────────────────
        ['2022-12-01', 'masuk', 351000, 'Iuran', 'Iuran warga'],
        ['2022-12-01', 'keluar', 300000, 'Kegiatan', 'Diambil utk RW ke Trawas'],
        ['2022-12-01', 'keluar', 48000, 'Perlengkapan', 'Beli Lampu dpn Atik'],
        ['2022-12-01', 'keluar', 600000, 'Iuran', 'Bayar RW 1 th'],
        ['2022-12-01', 'keluar', 150000, 'Pemakaman', 'Kematian pringgo'],
        // ── Januari 2023 ────────────────────
        ['2023-01-01', 'masuk', 836000, 'Iuran', 'Iuran warga'],
        ['2023-01-01', 'masuk', 200000, 'Donasi', 'Dari RW (Hajatan Warga)'],
        // ── Februari 2023 ────────────────────
        ['2023-02-01', 'masuk', 686000, 'Iuran', 'Iuran warga'],
        ['2023-02-01', 'keluar', 4715000, 'Operasional', 'Sumber maron'],
        ['2023-02-01', 'keluar', 150000, 'Iuran', 'Bayar iuran RW jan + feb'],
        ['2023-02-01', 'keluar', 300000, 'Kegiatan', 'Bayar Isro\' Miroj'],
        ['2023-02-01', 'keluar', 300000, 'Pemakaman', 'Kematian B. Muntiani + Drg Andrp'],
        ['2023-02-01', 'keluar', 500000, 'Perlengkapan', 'Beli meja'],
        // ── Maret 2023 ────────────────────
        ['2023-03-01', 'masuk', 386000, 'Iuran', 'Iuran warga'],
        ['2023-03-01', 'keluar', 145000, 'Perlengkapan', 'Beli bolam lengkap 66S'],
        ['2023-03-01', 'keluar', 75000, 'Iuran', 'Bayar iuran RW 6 bln Mart'],
        // ── April 2023 ────────────────────
        ['2023-04-01', 'masuk', 631000, 'Iuran', 'Iuran warga'],
        ['2023-04-01', 'keluar', 75000, 'Iuran', 'Bayar iuran RW 6 bln April'],
        ['2023-04-01', 'keluar', 150000, 'Pemakaman', 'Kematian P. Marino'],
        ['2023-04-01', 'keluar', 200000, 'Operasional', 'Beli bensin ke Caruban'],
        // ── Mei 2023 ────────────────────
        ['2023-05-01', 'masuk', 370000, 'Iuran', 'Iuran warga'],
        ['2023-05-01', 'keluar', 1000000, 'Kegiatan', 'Utk Halbi'],
        ['2023-05-01', 'keluar', 150000, 'Pemakaman', 'Kematian M. Naim'],
        ['2023-05-01', 'keluar', 8000, 'Kegiatan', 'Undangan utk Halbi'],
        // ── Juni 2023 ────────────────────
        ['2023-06-01', 'masuk', 751000, 'Iuran', 'Iuran warga'],
        ['2023-06-01', 'keluar', 80000, 'Perlengkapan', 'Beli lampu dpn juki + andik'],
        ['2023-06-01', 'keluar', 150000, 'Iuran', 'Bayar iuran RW bln Mei'],
        // ── Juli 2023 ────────────────────
        ['2023-07-01', 'masuk', 1036000, 'Iuran', 'Iuran warga'],
        ['2023-07-01', 'keluar', 150000, 'Pemakaman', 'Kematian B. Sugiowati'],
        ['2023-07-01', 'keluar', 130000, 'Perlengkapan', 'Pesen Bener'],
        ['2023-07-01', 'keluar', 75000, 'Pemakaman', 'Bayar makam 1'],
        ['2023-07-01', 'keluar', 200000, 'Kegiatan', 'Buat makan kerbak I'],
        ['2023-07-01', 'keluar', 669000, 'Perlengkapan', 'Beli cat (5 warna)'],
        ['2023-07-01', 'keluar', 400000, 'Iuran', 'Bayar Agustus / kantar RW'],
        ['2023-07-01', 'keluar', 355000, 'Perlengkapan', 'Beli bendera & lampu'],
        ['2023-07-01', 'keluar', 337, 'Lain-lain', 'butuh keterangan'],
        // ── Agustus 2023 ────────────────────
        ['2023-08-01', 'masuk', 581000, 'Iuran', 'Iuran warga'],
        ['2023-08-01', 'keluar', 150000, 'Pemakaman', 'Kematian B. Jasim'],
        ['2023-08-01', 'keluar', 50000, 'Perlengkapan', 'Beli lampu apn B. Dipo'],
        ['2023-08-01', 'keluar', 183000, 'Kegiatan', 'Kerbak II'],
        ['2023-08-01', 'keluar', 165000, 'Kegiatan', 'Kerbak III'],
        ['2023-08-01', 'keluar', 185000, 'Kegiatan', 'Kerbak IV'],
        // ── September 2023 ────────────────────
        ['2023-09-01', 'masuk', 335000, 'Iuran', 'Iuran warga'],
        ['2023-09-01', 'keluar', 130000, 'Operasional', 'Pengeluaran gorong-gorong'],
        ['2023-09-01', 'keluar', 150000, 'Pemakaman', 'Bayar makam Agts + sep'],
        // ── Oktober 2023 ────────────────────
        ['2023-10-01', 'masuk', 430000, 'Iuran', 'Iuran warga'],
        ['2023-10-01', 'keluar', 150000, 'Pemakaman', 'Kematian Rumiyanto'],
        ['2023-10-01', 'keluar', 75000, 'Pemakaman', 'Bayar makam'],
        // ── November 2023 ────────────────────
        ['2023-11-01', 'masuk', 360000, 'Iuran', 'Iuran warga'],
        ['2023-11-01', 'masuk', 2500000, 'Donasi', 'Terima dari Asia Net'],
        ['2023-11-01', 'keluar', 75000, 'Pemakaman', 'Bayar makam'],
        // ── Desember 2023 ────────────────────
        ['2023-12-01', 'masuk', 435000, 'Iuran', 'Iuran warga'],
        ['2023-12-01', 'keluar', 140000, 'Kegiatan', 'Utk KSH'],
        ['2023-12-01', 'keluar', 100000, 'Operasional', 'Utk PDAM (Udh)'],
        ['2023-12-01', 'keluar', 150000, 'Lain-lain', 'Utk Darfas'],
        ['2023-12-01', 'keluar', 75000, 'Pemakaman', 'Bayar makam'],
        // ── Januari 2024 ────────────────────
        ['2024-01-01', 'masuk', 290000, 'Iuran', 'Iuran warga'],
        ['2024-01-01', 'keluar', 28500, 'Perlengkapan', 'Foto copy laporan'],
        ['2024-01-01', 'keluar', 75000, 'Iuran', 'Bayar RW'],
        ['2024-01-01', 'keluar', 50000, 'Perlengkapan', 'Beli Bolam dpn sari'],
        ['2024-01-01', 'masuk', 3000, 'Lain-lain', 'butuh keterangan'],
        // ── Februari 2024 ────────────────────
        ['2024-02-01', 'masuk', 483000, 'Iuran', 'Iuran warga'],
        ['2024-02-01', 'keluar', 75000, 'Iuran', 'Bayar RW'],
        ['2024-02-01', 'keluar', 300000, 'Kegiatan', 'Isro\' miroj Mushola'],
        // ── Maret 2024 ────────────────────
        ['2024-03-01', 'masuk', 448000, 'Iuran', 'Iuran warga'],
        ['2024-03-01', 'keluar', 150000, 'Iuran', 'Bayar RW Mart & April'],
        // ── Mei 2024 ────────────────────
        ['2024-05-01', 'masuk', 873000, 'Iuran', 'Iuran warga'],
        ['2024-05-01', 'keluar', 150000, 'Iuran', 'Bayar RW Mei & Juni'],
        // ── Juli 2024 ────────────────────
        ['2024-07-01', 'masuk', 425000, 'Iuran', 'Iuran warga'],
        // ── Agustus 2024 ────────────────────
        ['2024-08-01', 'masuk', 550000, 'Iuran', 'Iuran warga'],
        ['2024-08-01', 'keluar', 500000, 'Iuran', 'Bayar Agustusan RW'],
        ['2024-08-01', 'keluar', 180000, 'Perlengkapan', 'Benner + lampu merah putih'],
        ['2024-08-01', 'keluar', 60000, 'Kegiatan', 'ft copy kerbak + lampu dpn P. Yos'],
        ['2024-08-01', 'keluar', 1355000, 'Kegiatan', 'Baju karnaval'],
        // ── September 2024 ────────────────────
        ['2024-09-01', 'masuk', 475000, 'Iuran', 'Iuran warga'],
        ['2024-09-01', 'keluar', 200000, 'Kegiatan', 'Bayar maulud mushola'],
        ['2024-09-01', 'keluar', 225000, 'Iuran', 'Bayar RW juli, Agt + Sep'],
        // ── Oktober 2024 ────────────────────
        ['2024-10-01', 'masuk', 240000, 'Iuran', 'Iuran warga'],
        ['2024-10-01', 'keluar', 125000, 'Iuran', 'Bayar RW utk perpisahan B. Sukesih + RW'],
        // ── November 2024 ────────────────────
        ['2024-11-01', 'masuk', 280000, 'Iuran', 'Iuran warga'],
        ['2024-11-01', 'keluar', 75000, 'Iuran', 'Bayar RW'],
        // ── Desember 2024 ────────────────────
        ['2024-12-01', 'masuk', 645000, 'Iuran', 'Iuran warga'],
        ['2024-12-01', 'keluar', 75000, 'Iuran', 'Bayar RW'],
        ['2024-12-01', 'keluar', 50000, 'Perlengkapan', 'Beli bolam dpn P. Munif'],
        // ── Januari 2025 ────────────────────
        ['2025-01-01', 'keluar', 3000, 'Lain-lain', 'butuh keterangan'],
        ['2025-01-01', 'masuk', 255000, 'Iuran', 'Iuran warga'],
        ['2025-01-01', 'keluar', 300000, 'Kegiatan', 'Diambil utk Isro\' miroj'],
        ['2025-01-01', 'keluar', 75000, 'Iuran', 'Bayar RW'],
        // ── Februari 2025 ────────────────────
        ['2025-02-01', 'masuk', 660000, 'Iuran', 'Iuran warga'],
        ['2025-02-01', 'keluar', 64000, 'Perlengkapan', 'Foto copy Lap. keuangan'],
        ['2025-02-01', 'keluar', 75000, 'Iuran', 'Bayar RW'],
        // ── Maret 2025 ────────────────────
        ['2025-03-01', 'masuk', 670000, 'Iuran', 'Iuran warga'],
        ['2025-03-01', 'keluar', 50000, 'Perlengkapan', 'Bolam dpn rohmat'],
        ['2025-03-01', 'keluar', 75000, 'Iuran', 'Bayar RW'],
        ['2025-03-01', 'keluar', 150000, 'Pemakaman', 'Kematian/Santunan Sugeng'],
        // ── April 2025 ────────────────────
        ['2025-04-01', 'masuk', 389000, 'Iuran', 'Iuran warga'],
        ['2025-04-01', 'keluar', 200000, 'Pemakaman', 'Kematian P. Sumarno'],
        ['2025-04-01', 'keluar', 20000, 'Kesehatan', 'Bayar posga mart+April'],
        ['2025-04-01', 'keluar', 75000, 'Iuran', 'Bayar RW'],
        // ── Mei 2025 ────────────────────
        ['2025-05-01', 'masuk', 485000, 'Iuran', 'Iuran warga'],
        ['2025-05-01', 'keluar', 10000, 'Kesehatan', 'Bayar posga'],
        ['2025-05-01', 'keluar', 75000, 'Iuran', 'Bayar RW'],
        ['2025-05-01', 'keluar', 500, 'Lain-lain', 'butuh keterangan'],
        // ── Juni 2025 ────────────────────
        ['2025-06-01', 'masuk', 475000, 'Iuran', 'Iuran warga'],
        ['2025-06-01', 'keluar', 500000, 'Iuran', 'Bayar HUT Agts RW'],
        ['2025-06-01', 'keluar', 50000, 'Perlengkapan', 'Bolam P Yos + Hasib'],
        ['2025-06-01', 'keluar', 800000, 'Kegiatan', 'Utk Rekreasi pengurus'],
        ['2025-06-01', 'keluar', 85000, 'Kesehatan', 'Posga juni + makam'],
        ['2025-06-01', 'masuk', 500, 'Lain-lain', 'butuh keterangan'],
        // ── Juli 2025 ────────────────────
        ['2025-07-01', 'masuk', 390000, 'Iuran', 'Iuran warga'],
        ['2025-07-01', 'keluar', 85000, 'Kesehatan', 'Bayar posga + makam'],
        ['2025-07-01', 'keluar', 6000, 'Kegiatan', 'foto copy kerbak + print'],
        ['2025-07-01', 'keluar', 500, 'Lain-lain', 'butuh keterangan'],
        // ── Agustus 2025 ────────────────────
        ['2025-08-01', 'masuk', 870000, 'Iuran', 'Iuran warga'],
        ['2025-08-01', 'keluar', 997000, 'Kegiatan', 'Mlm tirakatan (konsumsi + alat) + kekurangan hadiah'],
        ['2025-08-01', 'keluar', 200000, 'Pemakaman', 'Kematian Endra Septyono'],
        ['2025-08-01', 'keluar', 130000, 'Perlengkapan', 'Benner + foto copy'],
        ['2025-08-01', 'keluar', 85000, 'Kesehatan', 'Bayar posga + makam'],
        ['2025-08-01', 'masuk', 500, 'Lain-lain', 'butuh keterangan'],
        // ── September 2025 ────────────────────
        ['2025-09-01', 'masuk', 575000, 'Iuran', 'Iuran warga'],
        ['2025-09-01', 'keluar', 85000, 'Kesehatan', 'Bayar posga & makam'],
        // ── Oktober 2025 ────────────────────
        ['2025-10-01', 'masuk', 355000, 'Iuran', 'Iuran warga'],
        ['2025-10-01', 'keluar', 85000, 'Kesehatan', 'Bayar posga + makam'],
        // ── November 2025 ────────────────────
        ['2025-11-01', 'masuk', 520000, 'Iuran', 'Iuran warga'],
        ['2025-11-01', 'masuk', 300000, 'Donasi', 'Dari Balai RW'],
        ['2025-11-01', 'keluar', 85000, 'Kesehatan', 'Bayar posga + makam'],
        ['2025-11-01', 'keluar', 200000, 'Pemakaman', 'Santunan B. Mamik'],
        // ── Desember 2025 ────────────────────
        ['2025-12-01', 'masuk', 505000, 'Iuran', 'Iuran warga'],
        ['2025-12-01', 'keluar', 70000, 'Kesehatan', 'Bayar wisata jentik'],
        ['2025-12-01', 'keluar', 85000, 'Kesehatan', 'posga + makam'],
        ['2025-12-01', 'keluar', 50000, 'Perlengkapan', 'Bolam dpn P. Yos'],
        // ── Januari 2026 ────────────────────
        ['2026-01-01', 'masuk', 450000, 'Iuran', 'Iuran warga'],
        ['2026-01-01', 'keluar', 200000, 'Pemakaman', 'Santunan Kematian P. Gholib'],
        ['2026-01-01', 'keluar', 400000, 'Lain-lain', 'Utk Bayar ke Villa'],
        ['2026-01-01', 'keluar', 150000, 'Pemakaman', 'Transpor ke P. Gholib (Takziah)'],
        ['2026-01-01', 'keluar', 85000, 'Kesehatan', 'Mkm + Posga'],
        ['2026-01-01', 'keluar', 10000, 'Lain-lain', 'butuh keterangan'],
        // ── Februari 2026 ────────────────────
        ['2026-02-01', 'masuk', 490000, 'Iuran', 'Iuran warga'],
        ['2026-02-01', 'keluar', 85000, 'Kesehatan', 'Mkm + posga'],
        ['2026-02-01', 'keluar', 500, 'Lain-lain', 'butuh keterangan'],
        // ── Maret 2026 ────────────────────
        ['2026-03-01', 'masuk', 500, 'Lain-lain', 'butuh keterangan'],
        ['2026-03-01', 'masuk', 345000, 'Iuran', 'Iuran warga'],
        ['2026-03-01', 'keluar', 85000, 'Kesehatan', 'Makam & Posga'],
        ['2026-03-01', 'keluar', 210000, 'Perlengkapan', 'Beli konektor + ongkos'],
        // ── April 2026 ────────────────────
        ['2026-04-01', 'masuk', 310000, 'Iuran', 'Iuran warga'],
        ['2026-04-01', 'keluar', 85000, 'Kesehatan', 'Makam & Posga'],
        ['2026-04-01', 'keluar', 200000, 'Pemakaman', 'Kematian B. Kasiyat'],
        // ── Mei 2026 ────────────────────
        ['2026-05-01', 'masuk', 875000, 'Iuran', 'Iuran warga'],
        ['2026-05-01', 'keluar', 85000, 'Kesehatan', 'Makam + posga'],
        // ── Juni 2026 ────────────────────
        ['2026-06-01', 'masuk', 710000, 'Iuran', 'Iuran warga'],
        ['2026-06-01', 'keluar', 200000, 'Pemakaman', 'Kematian B. Rusiah'],
        ['2026-06-01', 'keluar', 85000, 'Kesehatan', 'Posga + makam'],
        // ── Juli 2026 ────────────────────
        ['2026-07-01', 'masuk', 515000, 'Iuran', 'Iuran warga'],
        ['2026-07-01', 'keluar', 500000, 'Iuran', 'Bayar Agustus RW'],
        ['2026-07-01', 'keluar', 85000, 'Kesehatan', 'Posga + makam'],
        // ── Agustus 2026 ────────────────────
        ['2026-08-01', 'masuk', 515000, 'Iuran', 'Iuran warga'],
        ['2026-08-01', 'keluar', 240000, 'Kegiatan', 'Maksi kerbak'],
        ['2026-08-01', 'keluar', 100000, 'Operasional', 'Perbaiki lampu'],
        ['2026-08-01', 'keluar', 805000, 'Kegiatan', 'Makan mlm tirakatan'],
        ['2026-08-01', 'keluar', 100000, 'Kegiatan', 'Ambil rombong Atik'],
        ['2026-08-01', 'keluar', 489000, 'Perlengkapan', 'Benner + foto copy'],
        ['2026-08-01', 'keluar', 200000, 'Perlengkapan', 'Beli lampu 50w dll'],
        ['2026-08-01', 'masuk', 100000, 'Lain-lain', 'butuh keterangan'],
    ];

    public function run(): void
    {
        $unit = $this->resolveUnit();
        $adminId = User::where('role', 'admin')->value('id');

        $rows = collect(self::TRANSAKSI);
        $saldo = $rows->where(1, 'masuk')->sum(2) - $rows->where(1, 'keluar')->sum(2);
        if ($saldo !== self::SALDO_AKHIR) {
            throw new \RuntimeException(
                "Self-check gagal: saldo seeder {$saldo} \u2260 buku asli ".self::SALDO_AKHIR.' \u2014 data tidak ditanam.'
            );
        }

        $deleted = KasTransaksi::where('kas_unit_id', $unit->id)->where('sumber', 'manual')->delete();

        foreach (self::TRANSAKSI as [$tanggal, $tipe, $jumlah, $kategori, $keterangan]) {
            KasTransaksi::create([
                'kas_unit_id' => $unit->id,
                'tipe' => $tipe,
                'sumber' => 'manual',
                'jumlah' => $jumlah,
                'kategori' => $kategori,
                'keterangan' => $keterangan,
                'tanggal' => $tanggal,
                'created_by' => $adminId,
            ]);
        }

        $masuk = $rows->where(1, 'masuk');
        $keluar = $rows->where(1, 'keluar');

        $this->command->table(['Metric', 'Nilai'], [
            ['Unit', $unit->nama.' (id '.$unit->id.')'],
            ['Baris manual lama dihapus', (string) $deleted],
            ['Transaksi ditanam', count(self::TRANSAKSI).' ('.$masuk->count().' masuk / '.$keluar->count().' keluar)'],
            ['Total masuk', 'Rp'.number_format($masuk->sum(2), 0, ',', '.')],
            ['Total keluar', 'Rp'.number_format($keluar->sum(2), 0, ',', '.')],
            ['Saldo akhir (Agt 2026)', 'Rp'.number_format($saldo, 0, ',', '.')],
        ]);
    }

    private function resolveUnit(): KasUnit
    {
        $kel = Wilayah::where('tingkat', 'Kelurahan')->where('nama', 'like', '%Bendul Merisi%')->first();
        $rw = $kel?->children()->where('nama', 'like', '%RW 03%')->first();
        $rt = $rw?->children()->where('nama', 'like', '%RT 04%')->first();

        abort_unless($rt instanceof Wilayah, 500, 'Wilayah RT 04 RW 03 Bendul Merisi tidak ditemukan — jalankan WilayahSeeder dulu.');

        return KasUnit::firstOrCreate(
            ['jenis' => 'rt', 'wilayah_id' => $rt->id, 'nama' => $rt->nama],
            ['created_by' => User::where('role', 'admin')->value('id')],
        );
    }
}
