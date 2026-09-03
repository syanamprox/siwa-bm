<?php

namespace Database\Seeders;

use App\Models\KasTransaksi;
use App\Models\KasUnit;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;

/**
 * Buku kas ASLI Rukem Sehati RW 03 Bendul Merisi — Februari 2017 s.d. 27 Agustus 2026.
 * Rukem Sehati = organisasi dana pembiayaan pemakaman warga RW03, TERPISAH dari kas RW03
 * (KasRw03RealSeeder). Pemasukan: iuran warga disetor per RT + bagi hasil parkir dari RW.
 * Sumber: transkrip Rukem.xlsx (buku kas fisik bendahara Rukem), 45 halaman/periode.
 *
 * Konvensi (disepakati dgn user, 25/08/2026):
 *  - Baris buku tanpa nominal (kolom Debet/Kredit kosong, atau dicoret/voided ~~...~~,
 *    atau 'Rp -') TIDAK diinput.
 *  - Selisih saldo akhir HALAMAN (bukan bulan — buku ini dibukukan per lembar, bukan per
 *    bulan) dgn hasil penjumlahan transaksi yang berhasil ditranskrip = baris 'Perlu
 *    Konfirmasi' (kategori Lain-lain) dicatat di tanggal transaksi TERAKHIR halaman
 *    tersebut, berisi nominal selisihnya persis — supaya bisa diusut kemudian hari.
 *    16 baris begitu di seluruh buku (1 di antaranya memang tertulis apa adanya di buku
 *    asli, tanggal 31/12/2017).
 *  - Rincian biaya pemakaman per kejadian (gali makam, opo/ubo rampe, papan/peti, modin,
 *    ambulance, dst) digabung jadi SATU baris 'keluar' kategori Pemakaman per kejadian,
 *    dgn rincian item di keterangan (dipisah koma) — bukan satu baris per item.
 *  - 2 nilai yang tertulis sbg teks non-numerik di buku (bukan dicoret) dipulihkan lewat
 *    validasi selisih saldo berjalan: 21/1/2023 'Rp 35.000.' (fotocopy laporan, kredit)
 *    dan 30/4/2023 'Rp 480.000 Rp 1.415.000' (iuran RT02, hanya Rp1.415.000 yg terbukti
 *    masuk dari delta saldo tercetak — Rp480.000 dianggap referensi/superseded).
 *  - Keterangan dirapikan (ejaan Iyuran→Iuran, Nopember→November, Pebruari→Februari, RT/RW
 *    dinormalisasi jadi 'RT 0N'/'RW 0N', sisa tanda transkrip '-'/'->'/'//' & titik nyasar
 *    dibersihkan) TANPA mengubah nominal/tanggal/kategori — makna & nama asli dipertahankan.
 *
 * Kategori map: parkir→Parkir · iyuran/iuran/setor+RT+rukem/bayar+rukem→Iuran ·
 * meninggal/wafat/gali makam/opo-ubo rampe/modin/papan/peti/ambulance/santunan→Pemakaman ·
 * kemung/sumbangan/shodaqoh/hamba allah/jimpitan→Donasi · rapat/sosialisasi→Rapat ·
 * fotocopy/poto kopi/perlengkapan/inventaris→Perlengkapan · transport/renovasi→Operasional ·
 * konfirmasi→Lain-lain.
 *
 * Idempotent: baris manual unit Rukem Sehati dihapus lalu ditanam ulang.
 * GENERATED oleh pipeline transkrip (parsing Rukem.xlsx per halaman + rekonsiliasi saldo
 * otomatis + normalisasi teks) — jangan edit manual, regenerate dari sumber bila ada koreksi.
 */
class KasRukemRw03RealSeeder extends Seeder
{
    /** Saldo akhir sesuai buku asli per 25/08/2026 — self-check, selisih 1 rupiah = gagal seed. */
    private const SALDO_AKHIR = 10572000;

    /** @var array<int, array{0: string, 1: string, 2: int, 3: string, 4: string}> [tanggal, tipe, jumlah, kategori, keterangan] */
    private const TRANSAKSI = [
        // ── Februari 2017 ────────────────────
        ['2017-02-03', 'masuk', 436000, 'Donasi', 'Dari Bpk Heri, petugas Kemung RW 03'],
        ['2017-02-03', 'masuk', 1000000, 'Donasi', 'Terima Dari Bpk Atik Ketua RW 03'],
        ['2017-02-03', 'keluar', 500000, 'Pemakaman', 'Untuk Sohibul Musibah P. Timbul'],
        ['2017-02-03', 'keluar', 100000, 'Pemakaman', 'Untuk Sopir Ambulance'],
        ['2017-02-03', 'keluar', 300000, 'Pemakaman', 'Untuk pengantar 2. mobil: WAFATNYA P. Timbul 1. Cak Mad, Bpk. Usi, Untuk P. Modin Laki 2'],
        ['2017-02-09', 'masuk', 1000000, 'Donasi', 'Terima Dari Bpk Atik K. RW 03'],
        ['2017-02-09', 'keluar', 1525000, 'Pemakaman', 'Pengeluaran Wafatnya. B. Supini RT 01: Gali Makam, Ubu Rampe, Kayu buat peti, P. Modin Lk, Modin pr, Modin Pr'],
        ['2017-02-10', 'masuk', 500000, 'Donasi', 'Sumbangan dari B. pipit .RT 02'],
        ['2017-02-10', 'masuk', 350000, 'Donasi', 'Dari pk. Heri petugas Kemung'],
        ['2017-02-28', 'masuk', 513000, 'Iuran', 'Iuran Wajib + Sodaqoh RT 02'],
        // ── Maret 2017 ────────────────────
        ['2017-03-10', 'masuk', 750000, 'Iuran', 'Iuran Wajib + Sodaqoh RT 01'],
        ['2017-03-10', 'masuk', 440000, 'Parkir', 'Dari parkir mobil Balai RW 03'],
        ['2017-03-24', 'masuk', 974000, 'Iuran', 'Dari Iuran Wajib Rukem: RT. IV'],
        ['2017-03-28', 'masuk', 81000, 'Iuran', 'Iuran Wajib, dari RT 01'],
        ['2017-03-28', 'masuk', 382000, 'Iuran', 'Iuran Wajib, dari RT 03'],
        // ── April 2017 ────────────────────
        ['2017-04-13', 'masuk', 440000, 'Parkir', 'Dari parkir Mobil, Tetap: Balai RW 03 40%'],
        ['2017-04-13', 'masuk', 200000, 'Iuran', 'Iuran Wajib. Dari RT 01'],
        ['2017-04-27', 'masuk', 500000, 'Iuran', 'Iuran Wajib Dari RT 03'],
        ['2017-04-29', 'masuk', 440000, 'Parkir', 'Dari parkir mobil Balai RW 03'],
        // ── Mei 2017 ────────────────────
        ['2017-05-20', 'keluar', 1355000, 'Pemakaman', 'Diambil Biaya pemakaman Ibu. Sumarti Ningsih / P. Sayid, warga RT 03 RW 03'],
        // ── Juni 2017 ────────────────────
        ['2017-06-05', 'masuk', 328000, 'Iuran', 'Iuran Wajib Dari RT 03'],
        ['2017-06-06', 'masuk', 589000, 'Iuran', 'Iuran Wajib Dari RT 02'],
        ['2017-06-12', 'masuk', 550000, 'Iuran', 'Iuran Wajib Dari RT 04 / 04'],
        ['2017-06-12', 'masuk', 440000, 'Parkir', 'Dari parkir mobil Balai RW 03'],
        ['2017-06-27', 'keluar', 1460000, 'Pemakaman', 'Diambil Meninggalnya Ibu Suparti RT 03 RW 03: Beli papan 3. Biji, Ubu Rampe / Kembang komplit, Juru kunci Makam, Ambulance, Aqua / Rokok untuk Makam, P. Mudin Laki, Bu Mudin / Perempuan 2 orang'],
        // ── Juli 2017 ────────────────────
        ['2017-07-10', 'keluar', 1290000, 'Pemakaman', 'Diambil Meninggalnya Ibu Karsiman RT 02 / RW 03: Beli papan 3. Biji / lonjor, Gali makam / Juru kunci, Bpk Mudin Laki2, Ibu Mudin perempuan 2, Ubu Rampe / Kembang komplit'],
        ['2017-07-11', 'masuk', 440000, 'Parkir', 'Dari iuran parkir Balai RW 03. 40%'],
        // ── Agustus 2017 ────────────────────
        ['2017-08-01', 'masuk', 640000, 'Iuran', 'Iuran Wajib, RT 01'],
        ['2017-08-01', 'masuk', 405000, 'Iuran', 'Iuran Wajib, Dari RT 02'],
        ['2017-08-01', 'masuk', 225000, 'Iuran', 'Iuran Wajib Dari RT 04'],
        ['2017-08-12', 'masuk', 440000, 'Parkir', 'Dari Parkir mobil Balai RW 03'],
        ['2017-08-10', 'keluar', 1500000, 'Pemakaman', 'Diambil Untuk Santunan Meninggalnya Istri P. Timbul'],
        ['2017-08-17', 'keluar', 1500000, 'Pemakaman', 'Diambil Untuk Santunan Meninggalnya Ibu Chofiyaton Di MADURA. RT 03 RW 03'],
        // ── September 2017 ────────────────────
        ['2017-09-14', 'masuk', 440000, 'Parkir', 'Dari parkir mobil Di Area Balai RW 03, Tetap'],
        ['2017-09-17', 'masuk', 314000, 'Iuran', 'Iuran Wajib RT 04'],
        ['2017-09-17', 'masuk', 150000, 'Donasi', 'Sumbangan Dari PKK RT 04'],
        // ── November 2017 ────────────────────
        ['2017-11-11', 'masuk', 440000, 'Parkir', 'Dari parkir mobil, Balai RW 03'],
        ['2017-11-19', 'masuk', 274000, 'Iuran', 'Iuran Wajib Dari RT 02'],
        ['2017-11-03', 'masuk', 329000, 'Iuran', 'Iuran Wajib Dari RT 01'],
        ['2017-11-20', 'masuk', 483000, 'Iuran', 'Iuran Wajib Dari RT 02'],
        ['2017-11-20', 'masuk', 440000, 'Parkir', 'Dari parkir mobil area RW 03'],
        // ── Desember 2017 ────────────────────
        ['2017-12-19', 'masuk', 440000, 'Parkir', 'Dari parkir mobil area Balai RW 03'],
        ['2017-12-31', 'masuk', 100000, 'Lain-lain', 'Perlu Konfirmasi'],
        // ── Januari 2018 ────────────────────
        ['2018-01-01', 'masuk', 625000, 'Donasi', 'Shodaqoh dari keluarga B. MARYANA'],
        ['2018-01-01', 'masuk', 628000, 'Iuran', 'Dari iuran Wajib RT 03 JULI + AGUSTUS 2017'],
        ['2018-01-01', 'masuk', 611000, 'Iuran', 'Sep + OKT + NOP, Des 2017'],
        ['2018-01-01', 'keluar', 180000, 'Operasional', 'TRANPOT KE B. NOVA MADURA'],
        ['2018-01-01', 'masuk', 37000, 'Donasi', 'TAMBAHAN Dari P. ARYONO'],
        ['2018-01-16', 'keluar', 1433000, 'Pemakaman', 'Biaya pemakaman Bpk. Bunali RT 01: Gali Makam, Ubo Rampe, Papan untuk peti, Aqua, Rokok, Gali Makam, Bpk Modin Laki-laki'],
        ['2018-01-16', 'keluar', 150000, 'Rapat', 'Diambil Untuk Konsumsi 1 Rapat, / peraturan makam B. MARYA, 2 Informasi keuangan Rukem, 3 Lain-lain / Fotocopy'],
        ['2018-01-18', 'masuk', 440000, 'Parkir', 'Dari Iuran parkir mobil area Balai RW 03'],
        // ── Februari 2018 ────────────────────
        ['2018-02-05', 'masuk', 440000, 'Parkir', 'Dari Iuran parkir mobil Di Area B. RW 03'],
        ['2018-02-05', 'masuk', 870000, 'Iuran', 'Dari iuran Wajib RT 04'],
        // ── Maret 2018 ────────────────────
        ['2018-03-11', 'masuk', 440000, 'Parkir', 'Dari parkir mobil area Balai RW 03 40%'],
        ['2018-03-23', 'keluar', 1133000, 'Pemakaman', 'Biaya Meninggalnya Bpk Edi, RT 01 RW 03: Gali Makam, Papan untuk peti/ paku, Beli Batrei / Spiker, Aqua / Rokok Makam, Bpk Modin Laki2'],
        // ── April 2018 ────────────────────
        ['2018-04-07', 'keluar', 1155000, 'Pemakaman', 'Biaya Meninggalnya Bpk Hari, RT 02 RW 03: UBU Rampe / komplit, Gali Makam, Papan 3 bj, Bpk Modin Laki2'],
        ['2018-04-09', 'keluar', 1465000, 'Pemakaman', 'Biaya Meninggalnya Bpk Kadek RT 02 RW 03'],
        ['2018-04-09', 'keluar', 450000, 'Lain-lain', 'Perlu Konfirmasi'],
        ['2018-04-12', 'masuk', 440000, 'Parkir', 'Dari iuran parkir Mobil Di Area Balai RW 03'],
        ['2018-04-17', 'masuk', 670000, 'Iuran', 'Dari Iuran Wajib RT 04 Februari, Maret, April 2018'],
        // ── Mei 2018 ────────────────────
        ['2018-05-10', 'keluar', 1500000, 'Pemakaman', 'Biaya Meninggalnya Bpk Sibit RT 03 RW 03 / Beri Santunan'],
        ['2018-05-16', 'masuk', 440000, 'Parkir', 'Dari Iuran parkir Mobil Di Area Balai RW 03'],
        ['2018-05-29', 'keluar', 1125000, 'Pemakaman', 'Diambil Meninggalnya Bpk Sulistiono RT 01 RW 03: Ubo Rampe, Gali Makam pk pardi, Bpk Modin RW 03, Papan + Gula Teh Geduk Makam'],
        ['2018-05-29', 'masuk', 1000000, 'Donasi', 'Disumbang Bpk RW 03 untuk KAS RUKEM RW 03'],
        // ── Juni 2018 ────────────────────
        ['2018-06-07', 'masuk', 440000, 'Parkir', 'Dari Iuran parkir Mobil Di Area Balai RW 03'],
        ['2018-06-13', 'masuk', 948000, 'Iuran', 'Dari Iuran Wajib RT 02 Bulan Juni 2018'],
        ['2018-06-21', 'keluar', 1400000, 'Pemakaman', 'Diambil Meninggalnya Anak pk No\'im RT 03, RW 03 B. MANIS Biaya pemakaman'],
        ['2018-06-28', 'masuk', 1015000, 'Iuran', 'Dari Iuran Wajib, RT 01 Bulan Juni 2018'],
        // ── Juli 2018 ────────────────────
        ['2018-07-12', 'masuk', 219000, 'Iuran', 'Dari Iuran Wajib RT 04 Juli 2018'],
        ['2018-07-13', 'masuk', 440000, 'Parkir', 'Dari Iuran parkir mobil Di Balai RW 03'],
        // ── Agustus 2018 ────────────────────
        ['2018-08-11', 'masuk', 440000, 'Parkir', 'Dari Iuran parkir Mobil Di area B. RW 03'],
        // ── September 2018 ────────────────────
        ['2018-09-09', 'masuk', 440000, 'Parkir', 'Dari parkir Mobil Di area Balai RW 03'],
        ['2018-09-09', 'masuk', 40000, 'Iuran', 'Dari RT 04 Iuran Rutin'],
        ['2018-09-12', 'masuk', 166000, 'Iuran', 'Dari RT 04 Iuran Rutin'],
        ['2018-09-12', 'keluar', 275000, 'Lain-lain', 'Perlu Konfirmasi'],
        // ── Oktober 2018 ────────────────────
        ['2018-10-09', 'masuk', 440000, 'Parkir', 'Dari Iuran parkir Mobil Di Balai RW 03'],
        // ── September 2018 ────────────────────
        ['2018-09-08', 'keluar', 1500000, 'Pemakaman', 'Diambil Biaya Meninggalnya Ibu Kasih / Istri Pk SUYONO RT 04 / RW 03: Ubu rampe, Makam, papan 3 lonjor, Rokok / Aqua Gelas, Modin'],
        // ── Oktober 2018 ────────────────────
        ['2018-10-22', 'masuk', 345000, 'Iuran', 'Dari Iuran Wajib RT 01 RW 03 Bendul Merisi'],
        // ── November 2018 ────────────────────
        ['2018-11-13', 'masuk', 440000, 'Parkir', 'Dari Iuran parkir Mobil. Di area B. RW 03'],
        ['2018-11-23', 'keluar', 1305000, 'Pemakaman', 'Diambil Biaya Meninggalnya, Istri Bpk Tegu, RT 04 RW 03: Ubu Rampe, Gali Makam, papan 3 Lonjor, Bpk Mudin, Ibu Mudin 2 orang'],
        ['2018-11-23', 'masuk', 1000000, 'Donasi', 'Dari Hamba Allah p. Atik'],
        ['2018-11-25', 'keluar', 1155000, 'Pemakaman', 'Diambil biaya Meninggalnya, bpk Kambali RT 03 RW 03: Ubu Rampe, Gali makam, Bpk Modin Laki2, papan 3 Lonjor'],
        ['2018-11-22', 'masuk', 1080000, 'Iuran', 'Dari Bpk Margono, Setor iuran Rukem RT 03 RW 03'],
        ['2018-11-22', 'masuk', 118000, 'Iuran', 'Dari Iuran rutin RT 01 RW 03'],
        // ── Desember 2018 ────────────────────
        ['2018-12-08', 'keluar', 260000, 'Perlengkapan', 'Diambil beli Tong/Kran Untuk pemandian Mayit Inventaris Warga RW 03'],
        ['2018-12-08', 'keluar', 100000, 'Lain-lain', 'Perlu Konfirmasi'],
        ['2018-12-09', 'masuk', 177000, 'Iuran', 'Iuran Dari RT 04 RW 03'],
        ['2018-12-09', 'masuk', 286000, 'Iuran', 'Iuran Dari RT 01 RW 03'],
        ['2018-12-10', 'masuk', 2000000, 'Iuran', 'Dari Iuran RT 02 RW 03 bpk MARGONO. Setor/sodakoh'],
        ['2018-12-11', 'masuk', 440000, 'Parkir', 'Dari parkir Mobil di Area Balai RW 03'],
        ['2018-12-12', 'keluar', 400000, 'Pemakaman', 'Diambil Untuk pemakaman Meninggalnya A.n. Anak. Tri aji Utomo bin Sukaji RT 02 RW 03'],
        ['2018-12-15', 'masuk', 440000, 'Parkir', 'Dari iuran parkir Mobil di Area B. RW 03'],
        ['2018-12-28', 'masuk', 286000, 'Iuran', 'Iuran Dari RT 02 RW 03'],
        ['2018-12-31', 'masuk', 411000, 'Iuran', 'Iuran Dari RT 03 RW 03'],
        // ── Februari 2019 ────────────────────
        ['2019-02-07', 'masuk', 632000, 'Iuran', 'Iuran dari RT 04 RW 03'],
        ['2019-02-10', 'masuk', 440000, 'Parkir', 'Dari parkir Mobil di Area B. RW 03'],
        // ── Maret 2019 ────────────────────
        ['2019-03-10', 'masuk', 440000, 'Parkir', 'Dari iuran parkir mobil Di Area B. RW 03'],
        // ── April 2019 ────────────────────
        ['2019-04-03', 'keluar', 450000, 'Pemakaman', 'Diambil Meninggalnya Bpk AGUS / Satipa RT 01: Upo Rampe / Selanjutnya tanggung sendiri'],
        ['2019-04-10', 'masuk', 440000, 'Parkir', 'Dari Iuran parkir Mobil di area B. RW 03'],
        // ── Mei 2019 ────────────────────
        ['2019-05-02', 'keluar', 550000, 'Pemakaman', 'Diambil biaya Meninggalnya Bu Lan. RT 02 / Biaya selanjutnya. Di-tanggung Sohibul Musibah'],
        ['2019-05-11', 'masuk', 440000, 'Parkir', 'Dari parkir mobil Di area B. RW 03'],
        ['2019-05-23', 'masuk', 467000, 'Iuran', 'Iuran dari RT 01'],
        ['2019-05-23', 'masuk', 442000, 'Iuran', 'Iuran dari RT 02'],
        ['2019-05-23', 'masuk', 93000, 'Iuran', 'Iuran dari RT 03'],
        ['2019-05-23', 'masuk', 593000, 'Iuran', 'Iuran dari RT 04'],
        // ── Juni 2019 ────────────────────
        ['2019-06-03', 'keluar', 1316000, 'Pemakaman', 'Diambil Meninggalnya P. Gimin: Kayu papan, Upo Rampe, Nasi bungkus, P. Modin'],
        ['2019-06-12', 'masuk', 753000, 'Iuran', 'Iuran Dari RT 01'],
        ['2019-06-12', 'masuk', 168000, 'Iuran', 'Iuran Dari RT 04'],
        ['2019-06-13', 'masuk', 440000, 'Parkir', 'Dari parkir Mobil Di- area B. RW 03'],
        // ── Juli 2019 ────────────────────
        ['2019-07-12', 'masuk', 440000, 'Parkir', 'Dari parkir mobil Di Area B. RW 03. Yg Tetap'],
        // ── Oktober 2019 ────────────────────
        ['2019-10-13', 'keluar', 1105000, 'Pemakaman', 'Diambil biaya meninggalnya. P. TEGUH RT 04 RW 03: Upo rampe, Kayu papan, P. Modin Laki 2, Gali makam Juru kunci'],
        ['2019-10-29', 'keluar', 1105000, 'Pemakaman', 'Diambil biaya Meninggalnya P. Sutaji RT 02 RW 03: Upo rampe, Kayu papan, Gali makam, P. Modin'],
        ['2019-10-29', 'keluar', 195000, 'Lain-lain', 'Perlu Konfirmasi'],
        // ── November 2019 ────────────────────
        ['2019-11-08', 'keluar', 1255000, 'Pemakaman', 'Diambil Meninggalnya Bpk Suyono RT 04 RW 03: Upo rampe, papan, Gali Makam, P. Modin'],
        ['2019-11-23', 'keluar', 1355000, 'Pemakaman', 'Diambil Meninggalnya IBU SUD, Istri Bpk Sukaji RT 02 RW 03: Upo Rampe, Papan, Gali Makam, P. Modin, 1. Modin perempuan'],
        // ── Desember 2019 ────────────────────
        ['2019-12-19', 'keluar', 1255000, 'Pemakaman', 'Diambil Meninggalnya Bpk SULAM RT 03 RW 03: Upo Rampe, papan, Gali makam, Bpk Modin'],
        ['2019-12-22', 'keluar', 1230000, 'Pemakaman', 'Diambil Meninggalnya Ibu Naim RT 03 RW 03: Upo Rampe, papan, Gali makam, Bpk Modin Laki, Ibu Modin, Ibu Modin'],
        // ── Januari 2020 ────────────────────
        ['2020-01-18', 'keluar', 1255000, 'Pemakaman', 'Diambil Meninggalnya Bpk SUMARNO RT 01: Gali Makam, Upo Rampe, bpk Modin Laki2, Kayu / papan'],
        ['2020-01-18', 'masuk', 555000, 'Donasi', 'Dari kemung Warga'],
        ['2020-01-18', 'masuk', 440000, 'Parkir', 'Dari parkir mobil RW 03 B. Merisi'],
        // ── Februari 2020 ────────────────────
        ['2020-02-13', 'masuk', 440000, 'Parkir', 'Dari parkir mobil area B RW 03. B. Merisi'],
        ['2020-02-13', 'masuk', 2764000, 'Iuran', 'Dari bpk Rofik Setor DARI: RT 01, RT 02, RT 03, RT 04'],
        ['2020-02-13', 'keluar', 300000, 'Pemakaman', 'Diambil biaya anak Tomi, meninggal dunia Gali makam p. Pardi'],
        // ── Maret 2020 ────────────────────
        ['2020-03-11', 'keluar', 1340000, 'Pemakaman', 'Diambil Meninggalnya Bpk. Salimin RT 04 / RW 03: Gali makam, Opo Rampe, bpk Modin Laki2, Kayu papan + Meteran, Nasi Bungkus + Rokok Minum yg Gali makam'],
        ['2020-03-11', 'masuk', 520000, 'Donasi', 'Dari kemung Warga Sekitar RW 03. B. Merisi'],
        ['2020-03-14', 'masuk', 400000, 'Parkir', 'Dari parkir Mobil RW 03 B. Merisi'],
        ['2020-03-14', 'keluar', 180000, 'Lain-lain', 'Perlu Konfirmasi'],
        // ── April 2020 ────────────────────
        ['2020-04-11', 'masuk', 440000, 'Parkir', 'Dari parkir Mobil Di Area. B. RW 03'],
        ['2020-04-14', 'keluar', 1250000, 'Pemakaman', 'Diambil Meninggalnya Ibu Turi / istri Almarhum bpk Kadi RW 03. B. Merisi RT 02: Gali makam, Upo Rampe, Sisa biaya Ke Sohibul'],
        ['2020-04-14', 'masuk', 620000, 'Donasi', 'Dari kemung wilayah Warga RW 03. Bendul Merisi'],
        // ── Mei 2020 ────────────────────
        ['2020-05-10', 'keluar', 1305000, 'Pemakaman', 'Diambil Meninggalnya Ibu Endang / Istri Pk Nanang Kasim RT 02 RW 03: Gali Makam, Opo Rampe, Bpk Modin Lk, Papan 3 bj., Modin, 1. Perempuan, 2 Perempuan'],
        ['2020-05-10', 'masuk', 676000, 'Donasi', 'Dari hasil kemung Warga RW 03'],
        ['2020-05-18', 'masuk', 440000, 'Parkir', 'Dari parkir Mobil Di Area B. RW 03'],
        // ── Juni 2020 ────────────────────
        ['2020-06-04', 'keluar', 575000, 'Pemakaman', 'Diambil Meninggalnya Bpk ANOM RT 03 RW 03: Maesan, Gali Makam, Bpk Modin'],
        ['2020-06-04', 'keluar', 40000, 'Lain-lain', 'Perlu Konfirmasi'],
        ['2020-06-05', 'keluar', 1305000, 'Pemakaman', 'Diambil Meninggalnya PK. KASTAMA RT 04 RW 03: Upo Rampe, Gali Makam, Papan, Bpk Modin Lk, Ibu Modin pr, 6 Ibu Modin pr'],
        ['2020-06-05', 'masuk', 700000, 'Donasi', 'Dari Kemung Warga RW 03'],
        ['2020-06-14', 'masuk', 440000, 'Parkir', 'Dari parkir Mobil Area B. RW 03'],
        ['2020-06-15', 'masuk', 282000, 'Iuran', 'Dari iuran RT 01 RW 03'],
        ['2020-06-15', 'masuk', 936000, 'Iuran', 'Dari iuran RT 02 RW 03'],
        ['2020-06-15', 'masuk', 417000, 'Iuran', 'Dari iuran RT 04 RW 03'],
        ['2020-06-20', 'masuk', 2246000, 'Iuran', 'Dari Iuran RT 03 RW 03'],
        ['2020-06-20', 'masuk', 999000, 'Iuran', 'Dari iuran RT 04 RW 03'],
        // ── Juli 2020 ────────────────────
        ['2020-07-12', 'keluar', 1305000, 'Pemakaman', 'Diambil Meninggalnya Bu, Insaro RT 02 RW 03: Upo rampe, Gali makam, Papan, bpk Modin Lk, Ibu Modin pr, 6 Ibu Modin pr'],
        ['2020-07-12', 'masuk', 500000, 'Donasi', 'Hasil kemung Wilayah RW 03, Sekitarnya'],
        ['2020-07-12', 'masuk', 1000, 'Lain-lain', 'Perlu Konfirmasi'],
        ['2020-07-17', 'masuk', 440000, 'Parkir', 'Dari iuran parkir mobil Area B. RW 03'],
        ['2020-07-23', 'keluar', 400000, 'Operasional', 'Diambil Bayar, Renovasi, Tempat peralatan Makam RW 03. Ke bendahara, Bpk Supangkat'],
        // ── Agustus 2020 ────────────────────
        ['2020-08-10', 'keluar', 1075000, 'Pemakaman', 'Diambil Meninggalnya IBU TUTIK / Saudaranya IBU TUMI. RT 02 RW 03: Upo Rampe, Papan, Gali makam, Bpk Modin Laki2'],
        ['2020-08-10', 'masuk', 500000, 'Donasi', 'Dari Hasil kemung Wilayah RW 03'],
        ['2020-08-12', 'masuk', 440000, 'Parkir', 'Dari iuran parkir Mobil area B. RW 03'],
        ['2020-08-29', 'keluar', 1305000, 'Pemakaman', 'Diambil Meninggalnya BU TUMI / Istri pk Girani. RT 02 / RW 03: Opo Rampe, Papan, Gali Makam, Bpk Modin Laki2, Ibu Modin WANITA, Ibu Modin WANITA'],
        ['2020-08-29', 'masuk', 500000, 'Donasi', 'Dari Hasil Kemung Warga RW 03'],
        // ── September 2020 ────────────────────
        ['2020-09-17', 'masuk', 440000, 'Parkir', 'Dari iuran parkir Mobil B. RW 03'],
        ['2020-09-17', 'keluar', 1000, 'Lain-lain', 'Perlu Konfirmasi'],
        // ── Oktober 2020 ────────────────────
        ['2020-10-08', 'keluar', 1500000, 'Pemakaman', 'Diambil Meninggalnya Orang tua H. Yasin RT-03, Kontribusi Anggota RUKEM. RW 03'],
        ['2020-10-12', 'masuk', 440000, 'Parkir', 'Dari iuran parkir mbl Di Area B. RW 03'],
        ['2020-10-17', 'keluar', 1155000, 'Pemakaman', 'Diambil Meninggalnya Bpk Dastomaji RT 02 RW 03. B. Merisi: Upo rampe, papan, Gali makam, Bpk Modin'],
        ['2020-10-17', 'masuk', 410000, 'Donasi', 'Dari kemung keliling Area RW 03 Sekitarnya'],
        ['2020-10-24', 'keluar', 1155000, 'Pemakaman', 'Diambil Meninggalnya Bpk Kasiyat RT 04 RW 03: Upo Rampe, papan., Gali Makam, Bpk Modin'],
        ['2020-10-24', 'masuk', 400000, 'Donasi', 'Dari kemung keliling Warga RW 03'],
        // ── November 2020 ────────────────────
        ['2020-11-15', 'masuk', 440000, 'Parkir', 'Dari parkir mobil Area B. RW 03'],
        ['2020-11-20', 'masuk', 65000, 'Pemakaman', 'Dari Keluarga Sohibul Musibah, Al Hidayah Sodakoh'],
        ['2020-11-20', 'masuk', 5000, 'Lain-lain', 'Perlu Konfirmasi'],
        // ── Desember 2020 ────────────────────
        ['2020-12-15', 'masuk', 440000, 'Parkir', 'Dari iuran parkir Mobil Area B. RW 03'],
        ['2020-12-18', 'masuk', 500000, 'Donasi', 'Dari kemung RW 05'],
        ['2020-12-18', 'keluar', 1435000, 'Pemakaman', 'Diambil biayanya Meninggalnya Bpk Rahmad RT 01 RW 03: papan/peti 6 lonjor, Gali makam, Upo Rampe, Bpk Modin'],
        // ── Januari 2021 ────────────────────
        ['2021-01-07', 'keluar', 1604000, 'Pemakaman', 'Diambil biayanya Me- ninggalnya B. BATIRAH Istri Pk Surat RT 03 RW 03: papan/usuk, peti., Gali makam, Upo rampe, Bpk Modin, Ibu Modin, Bayklen 1. Botol, Dari kemung Keliling RW 03'],
        ['2021-01-07', 'masuk', 1800000, 'Iuran', 'Dari RT 03. Bayar iuran Rutin Warga'],
        // ── Februari 2021 ────────────────────
        ['2021-02-08', 'keluar', 1520000, 'Pemakaman', 'Diambil Biayanya meninggalnya Bpk Naim RW 03 sblh Barat nya pk Kambali Almarhum: Opo rampe, papan peti, Gali Makam, Modin'],
        ['2021-02-08', 'keluar', 55000, 'Lain-lain', 'Perlu Konfirmasi'],
        ['2021-02-08', 'masuk', 450000, 'Donasi', 'Saldo Saat ini: Hasil Kemung Di Wilayah RW 03'],
        ['2021-02-19', 'masuk', 440000, 'Parkir', 'Dari parkir Mobil Area B. RW 03'],
        // ── Maret 2021 ────────────────────
        ['2021-03-06', 'keluar', 1422000, 'Pemakaman', 'Diambil Biaya meninggalnya. PAK. MODIN / P. MADI RT 03 RW 03: Gali Makam, Opo Rampe, Bpk Mudin, Baru. Laki2, 4 Papan + paku'],
        ['2021-03-06', 'masuk', 600000, 'Donasi', 'Hasil Dari Kemung Wilayah RW 03'],
        ['2021-03-15', 'masuk', 440000, 'Parkir', 'Dari parkir Mobil area B. RW 03'],
        ['2021-03-25', 'masuk', 395000, 'Iuran', 'Dari pk Rofik RT 01 Bayar Iuran Rukem'],
        ['2021-03-25', 'masuk', 997000, 'Iuran', 'Dari pk Rofik RT 03 Setor Iuran Rukem'],
        ['2021-03-25', 'masuk', 920000, 'Iuran', 'Dari pk Rofik RT 04 Setor Iuran rukem'],
        ['2021-03-25', 'keluar', 1500000, 'Pemakaman', 'Diambil Meninggalnya Bpk ARI Wibisono RT 03 Nyantoni'],
        // ── April 2021 ────────────────────
        ['2021-04-03', 'masuk', 666000, 'Iuran', 'Dari Pk Rofik. RT 04 Bayar Iuran'],
        ['2021-04-03', 'keluar', 34000, 'Rapat', 'Diambil beli Minum AQUA Untuk Rapat Rukem'],
        ['2021-04-03', 'masuk', 3404000, 'Iuran', 'Dari Bpk Rofik. RT 01 Bayar iuran'],
        ['2021-04-15', 'masuk', 440000, 'Parkir', 'Dari iuran parkir Mobil Area B RW 03'],
        ['2021-04-22', 'keluar', 1700000, 'Pemakaman', 'Diambil Meninggalnya Ibu Manis RT 03 RW 03: Opo Rampe, Gali Makam, papan buat peti/paku, Ibu Modin 2 perempuan, Bpk Modin Laki2'],
        ['2021-04-22', 'masuk', 250000, 'Pemakaman', 'Hasil Dari kemung Bpk Erpan 1/2 ke Sohibul Musibah'],
        ['2021-04-14', 'masuk', 440000, 'Parkir', 'Dari iuran parkir Mobil Di Area B. RW 03'],
        // ── Mei 2021 ────────────────────
        ['2021-05-03', 'masuk', 1219000, 'Iuran', 'Dari Bpk Choirul RT 02 Bayar Rukem RW 03 Sampai 2020'],
        ['2021-05-15', 'masuk', 440000, 'Parkir', 'Dari parkir Mobil area B. RW 03'],
        ['2021-05-20', 'keluar', 550000, 'Pemakaman', 'Diambil Meninggalnya Bpk Giran RT 02 RW 03: Gali Makam, Bpk Modin'],
        // ── Juni 2021 ────────────────────
        ['2021-06-18', 'masuk', 440000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2021-06-22', 'keluar', 1500000, 'Pemakaman', 'Diambil Meninggalnya IBU IKA / istri Bpk hari RT 04 B. Mns'],
        // ── Juli 2021 ────────────────────
        ['2021-07-02', 'keluar', 1300000, 'Pemakaman', 'Diambil Meninggalnya Bpk Sumarto RT 03 Makam nyantuni'],
        ['2021-07-05', 'keluar', 1400000, 'Pemakaman', 'Diambil Buat Santunan Meninggalnya IBU Widodo RT 04 RW 03'],
        ['2021-07-05', 'masuk', 260000, 'Donasi', 'Dari Separuh kemung Wilayah RW 03 Sekitarnya'],
        ['2021-07-12', 'masuk', 440000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2021-07-13', 'keluar', 1055000, 'Pemakaman', 'Diambil Meninggalnya IBU. Satunik RT 01 RW 03: Gali Makam, Papan, Bpk Modin Laki, Upo Rampe'],
        ['2021-07-13', 'masuk', 725000, 'Donasi', 'Dari Hasil Kemung Wilayah RW 03, Sekitarnya'],
        ['2021-07-14', 'keluar', 1400000, 'Pemakaman', 'Diambil Meninggalnya Bpk. Kusmiaji, RT 02 RW 03. Nyantuni'],
        ['2021-07-15', 'keluar', 1405000, 'Pemakaman', 'Diambil Meninggalnya IBU Julekha (istri) Pk Ridwan RT 03 RW 03: Gali Makam, Opo Rampe / Beklen., Papan, Ibu Modin perempuan, Bpk Modin Laki2, Ibu Modin + / Rewang 1'],
        ['2021-07-15', 'masuk', 615000, 'Donasi', 'Dari Hasil kemung di Wilayah RW 03 sekitarnya'],
        ['2021-07-22', 'keluar', 1400000, 'Pemakaman', 'Diambil Meninggalnya Ibu Siti Kotijah (istri Alm, Pk Sulistiano). Nyantuni'],
        ['2021-07-22', 'masuk', 566000, 'Donasi', 'Hasil Dari Kemung Wilayah RW 03. Sekitarnya'],
        ['2021-07-30', 'keluar', 1255000, 'Pemakaman', 'Diambil Meninggalnya Bpk Farid RT 01 RW 03: Gali Makam., Opo Rampe, papan, Bpk Modin, Laki2, H Yasin'],
        ['2021-07-30', 'masuk', 357000, 'Donasi', 'Dari kemung Wilayah RW 03 dan Sekitarnya'],
        // ── Agustus 2021 ────────────────────
        ['2021-08-02', 'masuk', 928000, 'Iuran', 'Dari RT 04 Setor iuran Rukem'],
        ['2021-08-02', 'masuk', 136000, 'Iuran', 'Dari Pk Ariyono + Siti Kotijah RT 01'],
        ['2021-08-02', 'keluar', 20000, 'Lain-lain', 'Perlu Konfirmasi'],
        ['2021-08-15', 'masuk', 440000, 'Parkir', 'Dari parkir Mobil area B. RW 03'],
        ['2021-08-18', 'keluar', 1300000, 'Pemakaman', 'Diambil Meninggalnya orang tua Bpk H. Yasin / Mentahan'],
        // ── September 2021 ────────────────────
        ['2021-09-14', 'masuk', 440000, 'Parkir', 'Dari parkir Mobil area B. RW 03'],
        ['2021-09-17', 'masuk', 1000000, 'Iuran', 'Dari RT 03 Setor kas Rukem, / Pk Rofik'],
        ['2021-09-17', 'keluar', 1255000, 'Pemakaman', 'Diambil Meninggalnya SDR ERFAN / P. Tarjo: Upo Rampe, Papan, Gali makam., Bpk Modin'],
        ['2021-09-17', 'masuk', 540000, 'Donasi', 'Dari Hasil kemung Keliling Wilayah RW 03'],
        ['2021-09-18', 'keluar', 1185000, 'Pemakaman', 'Diambil Meninggalnya IBU Cholifah RT 04 B. mns: Gali makam, Opo Rampe, Ibu Modin 1, Ibu Modin 2, Papan'],
        ['2021-09-20', 'masuk', 305000, 'Donasi', 'Hasil kemung Di Wilayah RW 03. Sekitarnya'],
        ['2021-09-20', 'masuk', 400000, 'Lain-lain', 'Perlu Konfirmasi'],
        ['2021-09-20', 'keluar', 1255000, 'Pemakaman', 'Diambil biaya Meninggalnya Bpk Dadang. RT 02: Gali makam, Opo Rampe, Papan, Bpk. Modin'],
        // ── Oktober 2021 ────────────────────
        ['2021-10-09', 'masuk', 2882000, 'Iuran', 'Dari RT 03 Setor Rukem / Dari pk Rofik'],
        ['2021-10-15', 'masuk', 440000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        // ── November 2021 ────────────────────
        ['2021-11-15', 'masuk', 440000, 'Parkir', 'Dari parkir Mobil area B. RW 03'],
        // ── Desember 2021 ────────────────────
        ['2021-12-13', 'keluar', 1200000, 'Pemakaman', 'Diambil meninggalnya anak dari Tri Manto RT 03'],
        ['2021-12-15', 'keluar', 1637000, 'Pemakaman', 'Diambil Meninggalnya Bpk Choirul (LOVER) RT 03 RW 03: Upo Rampe, papan + 6 Bj + paku, Bpk Modin, Gali Makam'],
        ['2021-12-15', 'masuk', 282000, 'Iuran', 'Dari RT 03 Bayar Iuran Bpk Margono'],
        ['2021-12-15', 'masuk', 670000, 'Donasi', 'Hasil kemung Wilayah RW 03 Sekitarnya'],
        ['2021-12-15', 'masuk', 440000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2021-12-18', 'keluar', 1226000, 'Pemakaman', 'Diambil Meninggalnya Bpk Johan RT 02 RW 03: Opo Rampe, Juru kunci / Gali Makam., Bpk Modin, papan 3. Lonjor'],
        ['2021-12-18', 'masuk', 450000, 'Donasi', 'Dari kemung Wilayah RW 03 dan sekitarnya'],
        // ── Januari 2022 ────────────────────
        ['2022-01-02', 'keluar', 1200000, 'Pemakaman', 'Diambil meninggalnya SDR. SIMON Anak Dari Ibu Sari RT 04 RW 03: Opo Rampe, Papan, Bpk Modin., Gali Makam'],
        ['2022-01-05', 'masuk', 531000, 'Donasi', 'Hasil kemung Di Wilayah RW 03. Sekitarnya'],
        ['2022-01-09', 'masuk', 478000, 'Iuran', 'Dari RT 03. S/D Des 21'],
        ['2022-01-09', 'masuk', 353000, 'Iuran', 'Dari RT 04. S/D Okt 21'],
        ['2022-01-09', 'keluar', 52000, 'Rapat', 'Diambil Konsumsi Rapat Kordinasi, Rukem Air Cup + Gorengan'],
        ['2022-01-11', 'masuk', 1800000, 'Iuran', 'Dari Bpk Khusnul RT 02 RW 03 Bayar iuran. Rukem RW 03'],
        ['2022-01-17', 'masuk', 440000, 'Parkir', 'Dari parkir mobil di area B. RW 03'],
        ['2022-01-27', 'masuk', 2000000, 'Iuran', 'Dari Bpk: Choirul RT 02 Bayar Rukem RW 03 Untuk Kekurangan th. Kemarin 2017-2020'],
        // ── Februari 2022 ────────────────────
        ['2022-02-12', 'masuk', 440000, 'Parkir', 'Dari parkir Mobil area B. RW 03'],
        // ── Maret 2022 ────────────────────
        ['2022-03-07', 'keluar', 1200000, 'Pemakaman', 'Diambil meninggalnya. Istri Bpk ARUJI RT 03 RW 03. / Santunan'],
        ['2022-03-16', 'masuk', 440000, 'Parkir', 'Dari parkir Mobil area B. RW 03'],
        ['2022-03-28', 'keluar', 1316000, 'Pemakaman', 'Diambil Meninggalnya Mbak Margiyati RT 03 RW 03: Upo Rampe, Papan, Gali Makam, Bpk Modin Laki, Ibu Modin p., Ibu Modin p'],
        // ── April 2022 ────────────────────
        ['2022-04-01', 'keluar', 1276000, 'Pemakaman', 'Diambil Meninggalnya Bpk Sayid RT 03 RW 03: Opo Rampe, Gali makam, Papan, Bpk Modin Laki2'],
        ['2022-04-01', 'keluar', 411000, 'Pemakaman', 'Diambil beli papan + Buat peti, Almarhum Bpk Sayid RT 03 RW 03 + paku+reng'],
        ['2022-04-13', 'masuk', 1800000, 'Iuran', 'Dari Bpk Choirul RT 02 Bayar iuran Rukem RW 03 Januari S/d April 2022'],
        ['2022-04-13', 'masuk', 440000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2022-04-18', 'masuk', 1400000, 'Donasi', 'Dari Bantuan Dana Jimpitan RW 03'],
        // ── Mei 2022 ────────────────────
        ['2022-05-11', 'keluar', 1200000, 'Pemakaman', 'Diambil untuk Santunan H. Mansyur RT 01 RW 03'],
        ['2022-05-20', 'masuk', 440000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        // ── Juni 2022 ────────────────────
        ['2022-06-18', 'masuk', 450000, 'Iuran', 'Dari Bpk Khoirul RT 02 Bayar Iuran Rukem untuk Bulan Mei. 2022'],
        ['2022-06-18', 'masuk', 440000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        // ── Juli 2022 ────────────────────
        ['2022-07-16', 'masuk', 440000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2022-07-29', 'keluar', 1225000, 'Pemakaman', 'Diambil Meninggalnya Bu. Yatimurni RT 04: Opo rampe, Gali Makam, papan, Ibu. Modin. 2. Lk. 1'],
        // ── Agustus 2022 ────────────────────
        ['2022-08-17', 'masuk', 440000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2022-08-21', 'keluar', 200000, 'Donasi', 'Diambil Beli Kemung 1 Buah, Untuk Rukem'],
        ['2022-08-20', 'keluar', 1235000, 'Pemakaman', 'Diambil meninggal Bpk Djamari RW 03 RT 02: Opo Rampe, Gali makam., papan., Bpk. Modin'],
        // ── September 2022 ────────────────────
        ['2022-09-06', 'keluar', 1235000, 'Pemakaman', 'Diambil Meninggalnya Bpk MARKAM RT 02 RW 03: Opo Rompe, 2 Gali Makam, 3 Papan., 4 Bpk Modin'],
        ['2022-09-06', 'keluar', 1135000, 'Pemakaman', 'Diambil meninggalnya Bpk SUTRISNO RT 04 RW 03: Opo rampe, 2 Gali Makam, 3 Papan., 4 Bpk Modin'],
        ['2022-09-18', 'masuk', 1350000, 'Iuran', 'Dari Bpk Khoirul RT 02 RW 03 Bayar Iuran Rukem JUNI s/d Agust 2022'],
        ['2022-09-19', 'masuk', 440000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        // ── Oktober 2022 ────────────────────
        ['2022-10-01', 'keluar', 1325000, 'Pemakaman', 'Diambil meninggalnya IBU. WATIHA RT 02 RW 03: Upo Rampe, Gali makam, papan, Ibu. Modin, Ibu. Modin, Bpk. Modin'],
        ['2022-10-14', 'masuk', 440000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        // ── November 2022 ────────────────────
        ['2022-11-15', 'masuk', 440000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2022-11-21', 'keluar', 695000, 'Pemakaman', 'Diambil meninggalnya Anak nya Bpk Feri Susanto RT 03 RW 03 B. MRS. 66 A / 11 A, Beri fasilitas: Upo Rampe, papan 2. Lonjor'],
        ['2022-11-22', 'masuk', 1000000, 'Iuran', 'Dari Bpk Rofik Setor RT 03 Bpk Pi\'i Akir Setor Maret 2022'],
        // ── Desember 2022 ────────────────────
        ['2022-12-12', 'keluar', 1375000, 'Pemakaman', 'Diambil Meninggalnya IBU. DARMI RT 03 RW 03: Opo rampe, Gali Makam., papan 3. Ljr, Ibu. Modin p, Modin p, Bpk. Modin'],
        ['2022-12-12', 'masuk', 1025000, 'Iuran', 'Dari RT 04. Bayar iuran Rukem 1 Bu Julika'],
        ['2022-12-12', 'masuk', 5000, 'Lain-lain', 'Perlu Konfirmasi'],
        ['2022-12-15', 'masuk', 440000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        // ── Januari 2023 ────────────────────
        ['2023-01-09', 'masuk', 240000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2023-01-18', 'masuk', 1955000, 'Iuran', 'Dari RT 04. Bayar Iuran November s/d Desember 2022. (DARI. P. ROFIK)'],
        ['2023-01-18', 'masuk', 50000, 'Iuran', 'Dari Pk Sumaryono RT 02.) Oktober 2022 (akhir)'],
        ['2023-01-21', 'keluar', 35000, 'Perlengkapan', 'Diambil poto kopi untuk Laporan keluar masuk, Kas Rukem 130 LBR'],
        // ── Februari 2023 ────────────────────
        ['2023-02-08', 'keluar', 35000, 'Donasi', 'Diambil perbaiki kemung Inventaris Rukem / di Laskan'],
        ['2023-02-13', 'keluar', 1135000, 'Pemakaman', 'Diambil meninggalnya IBU. Muntiani RT 04, RW 03: Upo rampe, papan 3. Lonjor, Gali makam, Bpk. Modin'],
        ['2023-02-15', 'masuk', 240000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2023-02-20', 'keluar', 1285000, 'Pemakaman', 'Diambil Meninggalnya Bpk Didik Roskandi / Ipar Dari Bpk Kamid, RT 03: 1 Opo Rampe, Gali Makam, papan 3. LJR, Bpk Modin, RW 12'],
        ['2023-02-21', 'masuk', 900000, 'Iuran', 'Dari Adik Nusa Bendahara RT 02. Bayar Iuran Rukem Bulan September + Oktober. 2022'],
        // ── Maret 2023 ────────────────────
        ['2023-03-09', 'masuk', 240000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2023-03-20', 'keluar', 1200000, 'Pemakaman', 'Diambil meninggalnya. IBU. Bibit Warga RT 03 RW 03 / nyantuni'],
        // ── April 2023 ────────────────────
        ['2023-04-09', 'masuk', 240000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2023-04-30', 'masuk', 1415000, 'Iuran', 'Dari Adik Nusa RT 02 RW 03. Bayar Iuran Rukem dari. November S/d Desember 2022 = Januari + April. 2023'],
        ['2023-04-30', 'keluar', 1200000, 'Pemakaman', 'Diambil meninggalnya Bpk Sumarno RT 04 RW 03. / Nyantuni'],
        // ── Mei 2023 ────────────────────
        ['2023-05-01', 'keluar', 1200000, 'Pemakaman', 'Diambil meninggalnya Bpk. Maddari RT 02 RW 03 / Nyantuni'],
        ['2023-05-10', 'masuk', 240000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2023-05-21', 'masuk', 515000, 'Iuran', 'Dari Adik Nusa RT 02 RW 03. Bayar Iuran Rukem, MEI 2023'],
        // ── Juni 2023 ────────────────────
        ['2023-06-09', 'masuk', 240000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2023-06-21', 'keluar', 1135000, 'Pemakaman', 'Diambil meninggalnya Ibu Kartika, RT 03, RW 03: Opo rampe, papan 3. Lonjor, Gali Makam, Bpk. Modin'],
        // ── Juli 2023 ────────────────────
        ['2023-07-10', 'masuk', 240000, 'Parkir', 'Dari parkir Mobil area B. RW 03'],
        ['2023-07-12', 'masuk', 345000, 'Iuran', 'Dari Adik NUSA. RT 02 RW 03. Setor iuran Rukem. RW 03. Untuk Bulan JUNI 2023'],
        ['2023-07-12', 'keluar', 1375000, 'Pemakaman', 'Diambil meninggal IBU Susiswati GG III/1A RT 04 RW 03: Opo rampe, Gali makam (MALAM), papan 3. Ljr, IBU. MODIN, IBU. MODIN, Bpk MODIN'],
        ['2023-07-16', 'masuk', 1880000, 'Iuran', 'Dari Bpk Rofik, Setor iuran Rukem Dari RT 03 RW 03. Untuk Bulan Januari s/d April 2023'],
        ['2023-07-16', 'masuk', 2130000, 'Iuran', 'Dari Bpk Rofik, Setor iuran Rukem, Dari RT 01 RW 03. Untuk Bulan Januari JUNI 2023'],
        ['2023-07-16', 'keluar', 1200000, 'Pemakaman', 'Diambil meninggalnya Istri H. AS ARI RT 01 RW 03 / Nyantuni Di Makamkan Di Madura'],
        // ── Agustus 2023 ────────────────────
        ['2023-08-01', 'keluar', 1375000, 'Pemakaman', 'Diambil Meninggalnya IBU SUMIATI / IBU. Robikoh RT 04: Opo Rampe, Gali Makam, PAPAN 3. LJR, IBU MODIN 2'],
        ['2023-08-01', 'keluar', 50000, 'Lain-lain', 'Perlu Konfirmasi'],
        ['2023-08-01', 'masuk', 2050000, 'Iuran', 'Dari Bpk Rofik, Setor iuran Rukem Dari RT 04 Untuk Bulan. Maret JUNI 2023'],
        // ── September 2023 ────────────────────
        ['2023-09-08', 'keluar', 1425000, 'Pemakaman', 'Diambil Meninggalnya IBU SOYATI RT 02 RW 03: Opo Rampe, Gali Makam, IBU. MODIN, IBU MODIN, Bpk MODIN, papan 3. Lonjor'],
        ['2023-09-09', 'keluar', 1335000, 'Pemakaman', 'Diambil Meninggalnya Bpk H. Hari RT 03 RW 03: Opo Rampe, Gali makam, papan 3. Lonjor, Bpk. Modin'],
        ['2023-09-28', 'keluar', 1205000, 'Pemakaman', 'Diambil Meninggalnya Bpk Ariyono RT 02 RW 03: Opo rampe, Gali makam, papan 3. Lonjor, Bpk. Modin. Lk, Aplop 1 Dos'],
        // ── Oktober 2023 ────────────────────
        ['2023-10-03', 'keluar', 990000, 'Pemakaman', 'Diambil meninggal nya IBU Yudesan. RT 01 RW 03: 1 Opo Rampe., Gali Makam, IBU. Modin = (DUA) petugas., Bpk. Modin'],
        ['2023-10-03', 'masuk', 300000, 'Pemakaman', 'Dari Sohibul Musibah keluarga IBu yudesan RT 01 RW 03. beri Sodaqoh, Ke kas, Rukem RW 03'],
        ['2023-10-11', 'masuk', 240000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2023-10-12', 'keluar', 900000, 'Pemakaman', 'Diambil meninggalnya Bpk Rumianto RT 04 RW 03: 1 Opo Rampe, 2 Gali makam (Malam), 3 Bpk Modin'],
        ['2023-10-14', 'masuk', 1245000, 'Iuran', 'Dari Bendahara RT 02 Setor Rukem Adik Nusa. Juni + Oktober 2023'],
        ['2023-10-31', 'keluar', 1290000, 'Pemakaman', 'Diambil meninggalnya IBU KUSNAH RT 03 RW 03. B. MENSI: Opo Rampe, Gali makam., IBU. Modin, 4 IBU. Modin, Bpk. Modin, 6 papan'],
        // ── November 2023 ────────────────────
        ['2023-11-04', 'masuk', 950000, 'Iuran', 'Dari RT 03 RW 03 Bayar iuran Rukem UNTUK Bulan (MEI JUNI) 2023'],
        ['2023-11-10', 'masuk', 240000, 'Parkir', 'Dari parkir Mobil area B. RW 03'],
        ['2023-11-10', 'masuk', 100000, 'Donasi', 'Sumbangan dari Achmad suryadi'],
        ['2023-11-21', 'keluar', 1200000, 'Pemakaman', 'Diambil meninggalnya IBU Sumarmi RT 03 RW 03. Muntahan'],
        // ── Desember 2023 ────────────────────
        ['2023-12-03', 'masuk', 520000, 'Iuran', 'Dari RT 04. Bayar Iuran Rukem s/d Oktober 2023'],
        ['2023-12-03', 'masuk', 1300000, 'Iuran', 'Dari RT 01. Bayar iuran Rukem S/d November 2023'],
        ['2023-12-11', 'masuk', 240000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2023-12-17', 'masuk', 685000, 'Iuran', 'Dari Adik Nusa Bendahara RT 02 RW 03. Setor Iuran Rukem Untuk Bulan November 2023 s/d Desem-ber 2023'],
        ['2023-12-30', 'keluar', 1698000, 'Pemakaman', 'Diambil meninggalnya IBU Munawarah, IBU dari (IBU. Romlah (Bpk Rofik) RT 01 RW 03: Opo Rampe, papan, (Buat peti) komplit., Gali makam., 4 Bpk. Modin Laki2, 5 Ibu. Modin, 6 Ibu. Modin'],
        // ── Januari 2024 ────────────────────
        ['2024-01-10', 'masuk', 240000, 'Parkir', 'Dari parkir mobil. area B. RW 03'],
        ['2024-01-25', 'masuk', 360000, 'Iuran', 'Dari RT 04. Bayar Iuran Rukem s/d 2023'],
        // ── Februari 2024 ────────────────────
        ['2024-02-03', 'masuk', 250000, 'Iuran', 'Dari RT 01 Bayar Iuran Rukem. Thn 2023/belum, kekuranga 2023 S/d November 2023'],
        ['2024-02-03', 'masuk', 100000, 'Lain-lain', 'Bpk Margono ngangsur pinjaman Rukem'],
        ['2024-02-03', 'masuk', 580000, 'Iuran', 'Dari RT 03 Bayar Rukem Tahun 2023 Ada yang belum'],
        ['2024-02-09', 'keluar', 1185000, 'Pemakaman', 'Diambil Meninggalnya Bpk Wiyono RT 02 RW 03 Anak (IBU Wiji): Opo Rampe, papan 3 Lonjor, Gali makam, Bpk Modin (Laki2)'],
        ['2024-02-12', 'masuk', 240000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        // ── Maret 2024 ────────────────────
        ['2024-03-10', 'masuk', 240000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        // ── April 2024 ────────────────────
        ['2024-04-08', 'masuk', 785000, 'Iuran', 'Dari RT 02 RW 03. SDR. Adik Nusa Setor- iuran Rukem bulan Desember S/d Maret 2024'],
        ['2024-04-10', 'masuk', 240000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        // ── Mei 2024 ────────────────────
        ['2024-05-13', 'masuk', 240000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2024-05-13', 'masuk', 1000, 'Lain-lain', 'Perlu Konfirmasi'],
        // ── Juni 2024 ────────────────────
        ['2024-06-10', 'masuk', 240000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2024-06-12', 'masuk', 1880000, 'Iuran', 'Dari RT 03 Setor Rukem Untuk Tahun 2023, masih kurang (- 400.000)'],
        // ── Juli 2024 ────────────────────
        ['2024-07-10', 'masuk', 240000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2024-07-14', 'masuk', 910000, 'Iuran', 'Dari RT 02 Bayar iuran Rukem Adik nusa, untuk Bulan Sampai Juli 2024'],
        // ── Agustus 2024 ────────────────────
        ['2024-08-09', 'masuk', 240000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2024-08-28', 'keluar', 885000, 'Pemakaman', 'Diambil meninggalnya Anak IBU yanti / P. Ali RT 02 RW 03 Bendul Merisi: Opo rampe, MODIN perempuan 1. BU SIS, BU YUL, Gali makam Bpk Pardi, papan 1. Lonjor'],
        // ── September 2024 ────────────────────
        ['2024-09-11', 'masuk', 240000, 'Parkir', 'Dari parkir Mobil area B. RW 03'],
        // ── Oktober 2024 ────────────────────
        ['2024-10-10', 'masuk', 240000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2024-10-21', 'keluar', 1275000, 'Pemakaman', 'Diambil meninggalnya. Rahmatulloh, Nama Halimah / Ratih Kumala Sari RT 04 RW 03: Opo Rampe, Gali makam, Papan 3. Lonjor., 4 MODIN. perempuan, MODIN perempuan, Modin Laki2'],
        // ── November 2024 ────────────────────
        ['2024-11-11', 'masuk', 240000, 'Parkir', 'Dari parkir mobil area. B. RW 03'],
        ['2024-11-30', 'keluar', 1185000, 'Pemakaman', 'Diambil meninggalnya Bpk Wagirin RT 01 RW 03 B. Merisi: 1 Opo Rampe, Gali Makam, papan 3 Lonjor, Bpk MODIN, Laki2'],
        // ── Desember 2024 ────────────────────
        ['2024-12-10', 'masuk', 240000, 'Parkir', 'Dari parkir Mobil B. RW 03'],
        // ── Januari 2025 ────────────────────
        ['2025-01-05', 'keluar', 1185000, 'Pemakaman', 'Diambil meninggalnya Bpk Mardjuki RT 03 RW 03: Opo Rampe, Gali Makam., Papan 3. Lonjor, Bpk Modin'],
        ['2025-01-05', 'masuk', 1310000, 'Iuran', 'Dari RT 02 Setor Iuran Rukem, adik Nusa. bag. Bendahara Untuk Bulan Agustus S/d De-sember 2024'],
        ['2025-01-11', 'masuk', 240000, 'Parkir', 'Dari parkir Mobil area B. RW 03'],
        // ── Februari 2025 ────────────────────
        ['2025-02-10', 'masuk', 400000, 'Iuran', 'Dari RT 03 Setor kekurangan nya yang Tahun 2023. Des'],
        ['2025-02-10', 'masuk', 2640000, 'Iuran', 'Dari RT 04 Setor Untuk/ S/d Des. 2024'],
        ['2025-02-11', 'masuk', 240000, 'Parkir', 'Dari parkir Mobil area B. RW 03'],
        // ── Maret 2025 ────────────────────
        ['2025-03-13', 'masuk', 240000, 'Parkir', 'Dari parkir Mobil area B. RW 03'],
        // ── April 2025 ────────────────────
        ['2025-04-09', 'keluar', 1385000, 'Pemakaman', 'Diambil meninggalnya Bpk Sumarno, Warga RT 04 RW 03: Opo Rampe, Gali Makam, Bpk. Modin Laki2, papan 3. Lonjor'],
        ['2025-04-30', 'keluar', 350000, 'Pemakaman', 'Sumbangan Gali Makam IBU, dari Bpk Edi RT 03 RW 03 / meninggalnya'],
        ['2025-04-10', 'masuk', 240000, 'Parkir', 'Dari parkir Mobil, area B. RW 03'],
        ['2025-04-23', 'keluar', 1255000, 'Pemakaman', 'Diambil Meninggalnya IBU. Maktum RT 02 RW 03. Bendul Merisi Indah: Opo Rampe, Gali makam, Modin Laki, Modin perempuan, papan 3. pcs / lonjor'],
        // ── Mei 2025 ────────────────────
        ['2025-05-02', 'masuk', 1395000, 'Iuran', 'Dari RT 02 Setor Iuran Rukem. Untuk Bulan Des. 2024 S/d April 2025'],
        ['2025-05-13', 'masuk', 240000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2025-05-23', 'keluar', 1035000, 'Pemakaman', 'Diambil meninggalnya. IBU. Maisaroh/IBU Dul RT 03 RW 03: Upo Rampe, papan 3 Lonjor, Bpk Modin., Gali Makam'],
        ['2025-05-28', 'masuk', 600000, 'Iuran', 'Dari RT 03 Setor Iuran Rukem, Untuk Bulan Januari 2024'],
        // ── Juni 2025 ────────────────────
        ['2025-06-11', 'masuk', 240000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        // ── Juli 2025 ────────────────────
        ['2025-07-11', 'masuk', 240000, 'Parkir', 'Dari parkir Mobil area B. RW 03'],
        // ── Agustus 2025 ────────────────────
        ['2025-08-15', 'masuk', 240000, 'Parkir', 'Dari parkir Mobil area B. RW 03'],
        ['2025-08-15', 'masuk', 700000, 'Iuran', 'Dari RT 02. Setor Iuran Rukem, untuk Bulan Mei S/d Juli 2025'],
        ['2025-08-15', 'masuk', 830000, 'Iuran', 'Dari RT 03 RW 03 Setor iuran Rukem yang tung gakan Bulan februari S/d Bulan Maret 2024'],
        ['2025-08-13', 'keluar', 1400000, 'Pemakaman', 'Diambil Meninggalnya Satria bintang P. putra Bpk Akir RT 01 RW 03 Bendul Merisi: Gali Makam, Opo Rampe komplit, Bpk Modin Laki-Laki'],
        ['2025-08-13', 'masuk', 1500000, 'Iuran', 'Dari RT 01, RW 03 Setor Iuran Rukem, Untuk Yang, Tunggakan, Bulan Desember, Januari, februari 2024'],
        // ── September 2025 ────────────────────
        ['2025-09-12', 'masuk', 240000, 'Parkir', 'Dari parkir Mobil area B. RW 03'],
        // ── Oktober 2025 ────────────────────
        ['2025-10-11', 'masuk', 240000, 'Parkir', 'Dari parkir Mobil area B. RW 03'],
        // ── November 2025 ────────────────────
        ['2025-11-12', 'masuk', 240000, 'Parkir', 'Dari parkir Mobil RW 03'],
        ['2025-11-25', 'keluar', 1613000, 'Pemakaman', 'Diambil meninggalnya IBU Mamik/Marmi/Giran RT 04, RW 03: Opo Rampe, Gali Makam, papan + Ring + paku., IBU. Modin, Bpk. Modin'],
        // ── Desember 2025 ────────────────────
        ['2025-12-13', 'masuk', 240000, 'Parkir', 'Dari parkir Mobil area B. RW 03'],
        ['2025-12-31', 'keluar', 1350000, 'Pemakaman', 'Diambil meninggalnya Bpk MOH Achiyak (Yayak) RT 02 RW 03: Opo rampe., Gali makam (Malam), papan., Bpk Modin'],
        // ── Januari 2026 ────────────────────
        ['2026-01-01', 'masuk', 500000, 'Iuran', 'Dari RT 03 Bayar Hutang Rukem. April Juni 2024'],
        ['2026-01-03', 'keluar', 1200000, 'Pemakaman', 'Diambil meninggalnya Bpk. Dukud, RT 01 RW 03: Opo rampe, Gali makam, Bpk MODIN, papan'],
        ['2026-01-03', 'masuk', 500000, 'Iuran', 'Dari RT 01 RW 03 Bayar iuran (Bulan 11 s/d 12. 2025)'],
        ['2026-01-07', 'masuk', 700000, 'Iuran', 'Dari RT 02, RW 03 Setor Iuran Rukem untuk Bulan Agustus S/d Desember 2025'],
        ['2026-01-10', 'keluar', 1130000, 'Pemakaman', 'Diambil Meninggalnya IBU. Djalakah Sodara Bpk Kamid. RT 03 RW 03. B. MRS: Gali Makam, Opo Rampe, Bpk Modin Laki, perempuan, papan'],
        ['2026-01-11', 'masuk', 240000, 'Parkir', 'Dari parkir Mobil area B. RW 03'],
        // ── Februari 2026 ────────────────────
        ['2026-02-03', 'masuk', 415000, 'Iuran', 'Dari RT 03 Bayar iuran NOV. 2025'],
        ['2026-02-14', 'masuk', 1350000, 'Iuran', 'Dari RT 04 RW 03 Bayar Iuran Rukem JUNI S/d Oktober 2025'],
        ['2026-02-14', 'keluar', 1200000, 'Pemakaman', 'Diambil Meninggal Bpk Kholib RT 04 RT 03 Bendul Merisi, Mentahan'],
        ['2026-02-12', 'masuk', 240000, 'Parkir', 'Dari parkir Mobil area B. RW 03'],
        ['2026-02-17', 'keluar', 85000, 'Rapat', 'Diambil konsumsi Rapat pengurus Rukem, RW 03 / RT 03'],
        ['2026-02-17', 'masuk', 1000000, 'Iuran', 'Dari RT 01 RW 03 Bayar Iuran Rukem Januari + februari 2026'],
        // ── Maret 2026 ────────────────────
        ['2026-03-14', 'masuk', 240000, 'Parkir', 'Dari parkir Mobil area B. RW 03'],
        ['2026-03-17', 'masuk', 500000, 'Iuran', 'Dari RT 01 Bayar Iuran Rukem, Untuk bulan, Maret + April 2026'],
        ['2026-03-28', 'keluar', 1090000, 'Pemakaman', 'Diambil Meninggalnya IBU. Wirayu RT 03 / RW 03: Opo Rampe/komplit, Gali Makam, IBU. Modin, Bpk. Modin'],
        // ── April 2026 ────────────────────
        ['2026-04-05', 'keluar', 1090000, 'Pemakaman', 'Diambil meninggalnya IBU. Kasiati RT 04 RW 03: Opo Rampe koplit, Gali Makam, IBU. Modin, Bpk Modin Laki2'],
        ['2026-04-09', 'keluar', 1650000, 'Pemakaman', 'Diambil Meninggalnya SDR. Dimas (Putra dari IBU Tiara, RT 02 RW 03: Opo Rampe, Gali Makam, papan 6 Lonjor/peti, Bpk Modin'],
        ['2026-04-14', 'masuk', 545000, 'Iuran', 'Dari RT 03 Bayar Iuran Rukem untuk bulan Maret 2026'],
        ['2026-04-15', 'masuk', 240000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2026-04-17', 'masuk', 835000, 'Iuran', 'Dari RT 02 Setor Iuran Rukem Untuk bulan. Januari s/d februari 2026'],
        ['2026-04-28', 'keluar', 1300000, 'Pemakaman', 'Diambil meninggalnya Bpk AGUNG D. W (suami) Dari IBU. IKA. RT 04 RW 03 B. Merisi: Opo Rampe, Gali makam (MLm), papan 3. Lonjor, Bpk Modin Laki2'],
        // ── Mei 2026 ────────────────────
        ['2026-05-05', 'keluar', 1150000, 'Pemakaman', 'Diambil Meninggalnya SDR. Imran j. Anak dari Bpk Almarhum P. MAD Modin RT 03 RW 03. B. Merisi: Opo Rampe, Gali Makam, papan 3. Lonjor, Bpk Modin Laki2'],
        ['2026-05-12', 'masuk', 240000, 'Parkir', 'Dari parkir Mobil area B. RW 03'],
        ['2026-05-21', 'masuk', 780000, 'Iuran', 'Dari RT 01 RW 03 Setor Iuran Rukem MEI S/d Bulan JUNI 2026'],
        // ── Juni 2026 ────────────────────
        ['2026-06-13', 'masuk', 240000, 'Parkir', 'Dari parkir mobil Area B. RW 03'],
        ['2026-06-30', 'keluar', 640000, 'Pemakaman', 'Diambil Meninggalnya. IBU RUSIAH Warga RT 04 RW 03: Opo Rampe, Bpk. MODIN L, IBU. MODIN'],
        // ── Juli 2026 ────────────────────
        ['2026-07-01', 'masuk', 760000, 'Iuran', 'Dari RT 02 SDR. Nusa Setor iuran Rukem. Maret s/d Mei 2026'],
        ['2026-07-18', 'masuk', 400000, 'Iuran', 'Dari RT 01 RW 03 Setor Kas Rukem Juni S/d Juli 2026'],
        ['2026-07-21', 'masuk', 1515000, 'Iuran', 'Dari RT 04 RW 03 Setor, Rukem. RT 03 S/D Bulan April 2026'],
        // ── Agustus 2026 ────────────────────
        ['2026-08-10', 'keluar', 1200000, 'Pemakaman', 'Diambil Meninggalnya. Bpk HOIRI RT 01 RW 03: Opo Rampe, Gali Makam (Malam), Modin Laki2, papan 3. Lonjor'],
        ['2026-08-13', 'masuk', 240000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2026-08-13', 'masuk', 240000, 'Parkir', 'Dari parkir mobil area B. RW 03'],
        ['2026-08-18', 'masuk', 400000, 'Iuran', 'Dari RT 01 RW 03 Setor Kas Rukem untuk bulan Agustus 2026'],
        ['2026-08-22', 'keluar', 1250000, 'Pemakaman', 'Diambil meninggalnya Bpk Ahmat Tohe Warga RT 02 RW 03: Opo Rampe, Gali Makam, Modin penggan ti Bpk Sumairi, papan 3 Ljr'],
        ['2026-08-25', 'masuk', 1260000, 'Iuran', 'Dari RT 03 RW 03 Setor Kas Rukem untuk bulan. April S/d Juni 2026'],
        ['2026-08-26', 'masuk', 410000, 'Iuran', 'Dari RT 02 RW 03 Setor Kas Rukem untuk bulan Juni & Agustus 2026'],
        ['2026-08-27', 'keluar', 1050000, 'Pemakaman', 'Diambil meninggalnya Bpk IIN RT 01 RW 03 B. Merisi: Opo Rampe, Gali Makam, papan, Bpk Modin'],
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
        $konfirmasi = $rows->filter(fn ($r) => $r[4] === 'Perlu Konfirmasi');

        $this->command->table(['Metric', 'Nilai'], [
            ['Unit', $unit->nama.' (id '.$unit->id.')'],
            ['Baris manual lama dihapus', (string) $deleted],
            ['Transaksi ditanam', count(self::TRANSAKSI).' ('.$masuk->count().' masuk / '.$keluar->count().' keluar)'],
            ['Total masuk', 'Rp'.number_format($masuk->sum(2), 0, ',', '.')],
            ['Total keluar', 'Rp'.number_format($keluar->sum(2), 0, ',', '.')],
            ['Baris Perlu Konfirmasi', (string) $konfirmasi->count().' (total Rp'.number_format(
                $konfirmasi->sum(fn ($r) => $r[1] === 'masuk' ? $r[2] : -$r[2]), 0, ',', '.'
            ).', net)'],
            ['Saldo akhir (27/08/2026)', 'Rp'.number_format($saldo, 0, ',', '.')],
        ]);
    }

    private function resolveUnit(): KasUnit
    {
        $kel = Wilayah::where('tingkat', 'Kelurahan')->where('nama', 'like', '%Bendul Merisi%')->first();
        $rw = $kel?->children()->where('nama', 'like', '%RW 03%')->first();

        abort_unless($rw instanceof Wilayah, 500, 'Wilayah RW 03 Bendul Merisi tidak ditemukan — jalankan WilayahSeeder dulu.');

        return KasUnit::firstOrCreate(
            ['jenis' => 'organisasi', 'wilayah_id' => $rw->id, 'nama' => 'Rukem Sehati'],
            ['created_by' => User::where('role', 'admin')->value('id')],
        );
    }
}
