<?php

namespace Database\Seeders;

use App\Models\KasTransaksi;
use App\Models\KasUnit;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;

/**
 * Buku kas ASLI RT 02 RW 03 Bendul Merisi — Januari 2023 s.d. Agustus 2026 (144 transaksi).
 * Sumber: laporan keuangan bendahara RT (saldo akhir terverifikasi Rp8.747.160 per 18/08/2026).
 *
 * Semua transaksi sumber 'manual' (historis, pra-sistem). Auto-post iuran tetap hidup
 * untuk pembayaran lewat sistem ke depannya — tidak dobel karena iuran sistem mulai 2026
 * dicatat sebagai tagihan/pembayaran module iuran, kas historis ini adalah buku bendahara.
 *
 * Idempotent: baris manual unit RT02 dihapus lalu ditanam ulang (auto-post iuran utuh).
 */
class KasRt02Rw03RealSeeder extends Seeder
{
    /** Saldo akhir sesuai buku asli — self-check, selisih 1 rupiah = gagal seed. */
    private const SALDO_AKHIR = 8747160;

    /** @var array<int, array{0: string, 1: string, 2: int, 3: string, 4: string}> [tanggal, tipe, jumlah, kategori, keterangan] */
    private const TRANSAKSI = [
        // ── 2023 ────────────────────────────────────────────────────────────
        ['2023-01-01', 'masuk', 1720660, 'Saldo Awal', 'Penyerahan Dari RT Lama'],
        ['2023-02-11', 'keluar', 300000, 'Kegiatan', "Iuran Is'ro Mi'roj"],
        ['2023-02-19', 'masuk', 500000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2023-02-21', 'keluar', 900000, 'Operasional', 'Bayar Iuran Rukem (Bulan September & Oktober 2022)'],
        ['2023-03-12', 'masuk', 990000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2023-03-12', 'keluar', 20000, 'Perlengkapan', 'Pembuatan Kartu Iuran'],
        ['2023-03-15', 'masuk', 250000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2023-03-19', 'keluar', 19500, 'Perlengkapan', 'Pembuatan Kartu Iuran'],
        ['2023-03-19', 'masuk', 500000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2023-03-19', 'masuk', 190000, 'Iuran', 'Pemasukan Iuran Kampung PKK bulan Maret 2023 (Bu Agus)'],
        ['2023-03-19', 'masuk', 497000, 'Iuran', 'Pemasukan Iuran Kampung Ibu PKK yang lalu, sudah termasuk sosial (Bu Rini)'],
        ['2023-04-30', 'masuk', 1000000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2023-04-30', 'keluar', 1415000, 'Operasional', 'Bayar Iuran Rukem (Bulan November s.d. April 2023)'],
        ['2023-05-01', 'masuk', 420000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2023-05-07', 'masuk', 120000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2023-05-11', 'keluar', 1191000, 'Kegiatan', 'Pembayaran Uang Sosial (Bln Januari - Mei)'],
        ['2023-05-21', 'masuk', 900000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2023-05-21', 'keluar', 515000, 'Operasional', 'Bayar Iuran Rukem (Bln Mei 2023)'],
        ['2023-06-04', 'masuk', 120000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2023-06-28', 'masuk', 795000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2023-06-29', 'masuk', 180000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2023-07-12', 'keluar', 345000, 'Operasional', 'Bayar Iuran Rukem (Bln Juni 2023)'],
        ['2023-07-15', 'keluar', 400000, 'Kegiatan', 'Pembayaran Iuran Acara Agustusan'],
        ['2023-07-22', 'masuk', 550000, 'Iuran', 'Pemasukan Iuran Kampung Ibu PKK Juni - Juli (Bu Agus)'],
        ['2023-07-30', 'keluar', 70000, 'Perlengkapan', 'Beli Umbul-Umbul'],
        ['2023-07-30', 'keluar', 430000, 'Perlengkapan', 'Beli Cat untuk jalan Agustusan'],
        ['2023-08-06', 'masuk', 1050000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2023-08-13', 'masuk', 120000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2023-08-13', 'keluar', 45000, 'Kegiatan', 'Rokok Pengecatan Gapura Merah Putih'],
        ['2023-08-14', 'masuk', 20000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2023-08-17', 'masuk', 130000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2023-10-02', 'masuk', 370000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2023-10-02', 'keluar', 52000, 'Operasional', 'Konsumsi Tukang Gorong-Gorong'],
        ['2023-10-04', 'keluar', 52000, 'Operasional', 'Konsumsi Tukang Gorong-Gorong'],
        ['2023-10-05', 'keluar', 57000, 'Operasional', 'Konsumsi Tukang Gorong-Gorong'],
        ['2023-10-06', 'keluar', 40000, 'Operasional', 'Konsumsi Tukang Gorong-Gorong'],
        ['2023-10-07', 'keluar', 52000, 'Operasional', 'Konsumsi Tukang Gorong-Gorong'],
        ['2023-10-08', 'keluar', 52000, 'Operasional', 'Konsumsi Tukang Gorong-Gorong'],
        ['2023-10-10', 'keluar', 52000, 'Operasional', 'Konsumsi Tukang Gorong-Gorong'],
        ['2023-10-14', 'keluar', 1245000, 'Operasional', 'Bayar Iuran Rukem (Bln Juli - Oktober 2023)'],
        ['2023-10-15', 'masuk', 470000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2023-11-19', 'masuk', 1500000, 'Donasi', 'Dana dari Asianet'],
        ['2023-11-20', 'masuk', 60000, 'Iuran', 'Pemasukan Iuran Kampung (Pak Sumargono) Dari Pak Rofiq'],
        ['2023-11-21', 'masuk', 280000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2023-11-30', 'masuk', 100000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2023-12-09', 'masuk', 60000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2023-12-09', 'keluar', 1029000, 'Kegiatan', 'Pembayaran Uang Sosial (Bulan Juni s.d. Desember)'],
        ['2023-12-12', 'masuk', 60000, 'Lain-lain', 'Dana Pinjam Kursi Putra (Bu Sima)'],
        ['2023-12-16', 'masuk', 100000, 'Iuran', 'Pemasukan Iuran Kampung (Pak Sumargono) Dari Pak Rofiq'],
        ['2023-12-17', 'masuk', 570000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2023-12-17', 'masuk', 200000, 'Iuran', 'Pemasukan Iuran Kampung PKK (koreksi ke Bu Agus)'],
        ['2023-12-17', 'keluar', 685000, 'Operasional', 'Bayar Iuran Rukem'],
        ['2023-12-19', 'keluar', 140000, 'Kegiatan', 'Pembayaran Kegiatan KSH'],
        ['2023-12-25', 'keluar', 250000, 'Kegiatan', 'Pembayaran Acara Penutupan Agustusan'],
        ['2023-12-31', 'masuk', 660000, 'Iuran', 'Pemasukan Iuran Kampung'],
        // ── 2024 ────────────────────────────────────────────────────────────
        ['2024-01-06', 'masuk', 140000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2024-01-14', 'masuk', 100000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2024-01-17', 'keluar', 300000, 'Kegiatan', "Iuran PHBI Isro' Mi'roj (P. Sarjito)"],
        ['2024-03-06', 'masuk', 420000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2024-03-08', 'masuk', 120000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2024-03-10', 'masuk', 450000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2024-04-08', 'keluar', 785000, 'Operasional', 'Bayar Pemasukan Iuran Kampung'],
        ['2024-04-21', 'masuk', 860000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2024-06-16', 'masuk', 60000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2024-06-19', 'masuk', 150000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2024-06-21', 'keluar', 50000, 'Kegiatan', 'Pembayaran Acara Anjangsana (Bu Upa)'],
        ['2024-06-30', 'masuk', 390000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2024-07-14', 'masuk', 495000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2024-07-14', 'keluar', 910000, 'Operasional', 'Pembayaran Rukem'],
        ['2024-07-16', 'keluar', 500000, 'Kegiatan', 'Pembayaran Agustusan'],
        ['2024-07-28', 'keluar', 250000, 'Perlengkapan', 'Pembelian 2 Cat Jalan Kerja Bakti'],
        ['2024-07-28', 'keluar', 20000, 'Kegiatan', 'Pembelian Konsumsi Kerja Bakti'],
        ['2024-07-28', 'keluar', 140000, 'Perlengkapan', 'Pembelian 10 Umbul Umbul'],
        ['2024-07-28', 'masuk', 30000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2024-08-01', 'masuk', 450000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2024-09-04', 'masuk', 50000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2024-09-05', 'keluar', 200000, 'Kegiatan', 'Pembayaran PHBI Maulid Nabi'],
        ['2024-09-16', 'masuk', 1485000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2024-10-12', 'masuk', 250000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2024-11-13', 'keluar', 70000, 'Kegiatan', 'Pembayaran KSH'],
        ['2024-12-08', 'masuk', 445000, 'Iuran', 'Pemasukan Iuran Kampung'],
        // ── 2025 ────────────────────────────────────────────────────────────
        ['2025-01-02', 'keluar', 300000, 'Kegiatan', 'Pembayaran Isro Miroj'],
        ['2025-01-05', 'masuk', 200000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2025-01-05', 'keluar', 1310000, 'Operasional', 'Pembayaran Rukem'],
        ['2025-01-08', 'masuk', 240000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2025-02-02', 'masuk', 1535000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2025-02-04', 'masuk', 190000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2025-03-14', 'keluar', 10000, 'Kegiatan', 'Kegiatan Puspaga'],
        ['2025-04-16', 'keluar', 10000, 'Kegiatan', 'Kegiatan Puspaga'],
        ['2025-04-27', 'masuk', 1100000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2025-05-02', 'keluar', 1395000, 'Operasional', 'Pembayaran Rukem'],
        ['2025-05-05', 'masuk', 120000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2025-05-11', 'masuk', 160000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2025-06-15', 'keluar', 10000, 'Kegiatan', 'Kegiatan Puspaga'],
        ['2025-06-15', 'masuk', 960000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2025-06-17', 'keluar', 500000, 'Kegiatan', 'Iuran Untuk Agustusan'],
        ['2025-06-26', 'masuk', 60000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2025-07-27', 'masuk', 540000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2025-08-03', 'keluar', 44000, 'Kegiatan', 'Konsumsi Pemasangan Umbul Agustusan'],
        ['2025-08-13', 'keluar', 58000, 'Perlengkapan', 'Banner Malam Tirakatan 2025'],
        ['2025-08-15', 'keluar', 700000, 'Operasional', 'Pembayaran Rukem'],
        ['2025-08-16', 'keluar', 496000, 'Kegiatan', 'Pengeluaran Malam Tirakatan'],
        ['2025-08-17', 'masuk', 200000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2025-09-03', 'masuk', 80000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2025-10-05', 'masuk', 280000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2025-10-05', 'keluar', 50000, 'Kegiatan', 'Kegiatan Puspaga (7-11)'],
        ['2025-10-26', 'masuk', 570000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2025-11-16', 'masuk', 80000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2025-11-30', 'masuk', 90000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2025-12-17', 'masuk', 240000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2025-12-19', 'keluar', 70000, 'Kegiatan', 'Kegiatan Jentik'],
        // ── 2026 ────────────────────────────────────────────────────────────
        ['2026-01-04', 'masuk', 300000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2026-01-07', 'keluar', 700000, 'Operasional', 'Pembayaran Rukem'],
        ['2026-01-12', 'keluar', 400000, 'Kegiatan', 'Pembayaran Villa'],
        ['2026-01-15', 'masuk', 120000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2026-01-15', 'masuk', 110000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2026-02-01', 'masuk', 630000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2026-02-07', 'masuk', 150000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2026-02-07', 'masuk', 160000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2026-02-12', 'masuk', 120000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2026-02-15', 'masuk', 120000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2026-04-09', 'masuk', 100000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2026-04-17', 'keluar', 835000, 'Operasional', 'Pembayaran Rukem'],
        ['2026-04-17', 'keluar', 60000, 'Kegiatan', 'Banner Halal Bihalal'],
        ['2026-05-03', 'masuk', 1105000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2026-06-16', 'masuk', 625000, 'Iuran', 'Pemasukan Iuran Kampung'],
        ['2026-07-01', 'keluar', 760000, 'Operasional', 'Pembayaran Rukem (Maret - Mei)'],
        ['2026-07-06', 'keluar', 500000, 'Kegiatan', 'Iuran Untuk Agustusan HUT RI 2026'],
        ['2026-08-02', 'keluar', 110000, 'Perlengkapan', 'Pembelian 2 Umbul-Umbul & Pring'],
        ['2026-08-02', 'keluar', 270000, 'Perlengkapan', 'Pembelian 2 Cat 5 KG'],
        ['2026-08-02', 'keluar', 12000, 'Perlengkapan', 'Pembelian 2 Kuas Cat'],
        ['2026-08-02', 'keluar', 20000, 'Kegiatan', 'Pembelian Konsumsi Kerja Bakti'],
        ['2026-08-02', 'keluar', 95000, 'Perlengkapan', 'Pembelian Kain Backdoor'],
        ['2026-08-02', 'keluar', 40000, 'Kegiatan', 'Rokok Pengecatan Gapura Merah Putih'],
        ['2026-08-09', 'masuk', 880000, 'Iuran', 'Iuran Rukem'],
        ['2026-08-16', 'keluar', 88000, 'Kegiatan', 'Soklin 1 dos + Tepung Bumbu 1 Renteng (Malam Tirakatan)'],
        ['2026-08-16', 'keluar', 89000, 'Perlengkapan', 'Banner 3x1,8 (Malam Tirakatan)'],
        ['2026-08-16', 'keluar', 350000, 'Kegiatan', 'Nasi Tumpeng'],
        ['2026-08-16', 'keluar', 14000, 'Kegiatan', 'Sampul Cokelat'],
        ['2026-08-16', 'keluar', 96000, 'Kegiatan', 'Air Mineral'],
        ['2026-08-16', 'keluar', 202000, 'Kegiatan', 'Konsumsi (Pisang + Kentang + Kletikan)'],
        ['2026-08-16', 'keluar', 120000, 'Kegiatan', 'Dorprise Mie Goreng'],
        ['2026-08-16', 'keluar', 200000, 'Kegiatan', 'Kardus dan Tisue'],
        ['2026-08-18', 'keluar', 200000, 'Kegiatan', 'Sound Tirakatan (Pak Samhari)'],
    ];

    public function run(): void
    {
        $rt = $this->resolveRt02Rw03();
        $unit = KasUnit::forWilayah($rt);
        $adminId = User::where('role', 'admin')->value('id');

        // Self-check: buku harus balance dengan saldo akhir buku asli
        $rows = collect(self::TRANSAKSI);
        $saldo = $rows->where(1, 'masuk')->sum(2) - $rows->where(1, 'keluar')->sum(2);
        if ($saldo !== self::SALDO_AKHIR) {
            throw new \RuntimeException(
                "Self-check gagal: saldo seeder {$saldo} ≠ buku asli ".self::SALDO_AKHIR.' — data tidak ditanam.'
            );
        }

        // Idempotent: ganti baris manual unit RT02 (auto-post iuran dibiarkan utuh)
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
            ['Saldo akhir (18/08/2026)', 'Rp'.number_format($saldo, 0, ',', '.')],
        ]);
    }

    private function resolveRt02Rw03(): Wilayah
    {
        $kel = Wilayah::where('tingkat', 'Kelurahan')->where('nama', 'like', '%Bendul Merisi%')->first();
        $rw = $kel?->children()->where('nama', 'like', '%RW 03%')->first();
        $rt = $rw?->children()->where('nama', 'like', '%RT 02%')->first()
            ?? Wilayah::where('tingkat', 'RT')->where('nama', 'like', '%RT 02%RW 03%')->first();

        abort_unless($rt instanceof Wilayah, 500, 'Wilayah RT 02 RW 03 Bendul Merisi tidak ditemukan — jalankan WilayahSeeder dulu.');

        return $rt;
    }
}
