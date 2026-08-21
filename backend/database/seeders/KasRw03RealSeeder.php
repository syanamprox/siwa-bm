<?php

namespace Database\Seeders;

use App\Models\KasTransaksi;
use App\Models\KasUnit;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;

/**
 * Buku kas ASLI RW 03 Bendul Merisi — Juli 2026 (28 transaksi + opening balance).
 * Sumber: laporan keuangan bendahara RW (saldo akhir terverifikasi Rp5.644.500 per 29/07/2026).
 *
 * Histori buku sebelum Juli 2026 tidak tersedia → saldo pembuka 6.061.000 ditanam
 * sebagai baris 'Saldo Awal' per 01/07/2026 (pola opening balance modul kas).
 * Pemasukan dominan dana parkir (kategori 'Parkir'); pembagian dana parkir ke
 * Musholla/Rukem/Kartar/TPQ/Anak Yatim = 'Donasi'; setoran iuran makam RT = 'Iuran'.
 *
 * Idempotent: baris manual unit RW03 dihapus lalu ditanam ulang (auto-post iuran utuh).
 */
class KasRw03RealSeeder extends Seeder
{
    /** Saldo akhir sesuai buku asli — self-check, selisih 1 rupiah = gagal seed. */
    private const SALDO_AKHIR = 5644500;

    /** @var array<int, array{0: string, 1: string, 2: int, 3: string, 4: string}> [tanggal, tipe, jumlah, kategori, keterangan] */
    private const TRANSAKSI = [
        ['2026-07-01', 'masuk', 6061000, 'Saldo Awal', "Saldo Awal Buku Kas RW 03 (Juli 2026)"],
        ['2026-07-01', 'keluar', 156500, 'Operasional', 'Bayar listrik Balai RW03 bln Juli'],
        ['2026-07-01', 'masuk', 200000, 'Parkir', 'Dari Bp. Ari (Parkir mobil Utara)'],
        ['2026-07-01', 'masuk', 200000, 'Parkir', 'Dari Bp. Ari (Parkir mobil Utara)'],
        ['2026-07-01', 'keluar', 300000, 'Operasional', 'Bayar iuran Makam RW 03 bln Juli'],
        ['2026-07-02', 'masuk', 200000, 'Parkir', 'Dari Bp. Choirul (Parkir mobil Utara)'],
        ['2026-07-03', 'keluar', 300000, 'Perlengkapan', 'Beli 2 mic untuk Kartar'],
        ['2026-07-03', 'keluar', 1000000, 'Kegiatan', 'Sumbang untuk acara Agustusan'],
        ['2026-07-12', 'masuk', 200000, 'Parkir', 'Dari Bp. Ihsan (Parkir mobil Utara)'],
        ['2026-07-12', 'keluar', 200000, 'Operasional', 'Bayar insentif kebersihan P. Fadli'],
        ['2026-07-12', 'keluar', 240000, 'Donasi', 'Pembagian dana parkir untuk Musholla'],
        ['2026-07-13', 'masuk', 400000, 'Parkir', 'Dari Bp. Sambas (Parkir mobil Utara) Juni-Juli'],
        ['2026-07-14', 'masuk', 200000, 'Parkir', 'Dari Bp. Satrijo (Parkir mobil Utara)'],
        ['2026-07-14', 'masuk', 200000, 'Parkir', 'Dari Bp. Ryan (Parkir mobil Utara)'],
        ['2026-07-14', 'keluar', 240000, 'Donasi', 'Pembagian dana parkir untuk Rukem'],
        ['2026-07-14', 'keluar', 100000, 'Donasi', 'Pembagian dana parkir untuk Kartar'],
        ['2026-07-15', 'masuk', 200000, 'Parkir', 'Dari Bp. Fajar (Parkir mobil Utara)'],
        ['2026-07-15', 'masuk', 200000, 'Parkir', 'Dari Bp. Suhadi (Parkir mobil Utara)'],
        ['2026-07-16', 'masuk', 150000, 'Parkir', 'Dari Bp. Yono (Parkir mobil Selatan)'],
        ['2026-07-16', 'masuk', 200000, 'Parkir', 'Dari Bp. Nardi (Parkir mobil Utara)'],
        ['2026-07-17', 'keluar', 10000, 'Kesehatan', 'Bayar POSGA'],
        ['2026-07-22', 'keluar', 300000, 'Rapat', 'Konsumsi rapat sosialisasi Uditch dan paving'],
        ['2026-07-22', 'masuk', 75000, 'Iuran', "Dari RT 02 iuran makam bln Juni'26"],
        ['2026-07-24', 'masuk', 150000, 'Iuran', "Dari RT 04 iuran makam bln Juni-Juli'26"],
        ['2026-07-24', 'masuk', 400000, 'Parkir', "Dari Bp. H. Bambang (Parkir mobil Utara) Juni-Juli'26"],
        ['2026-07-28', 'keluar', 200000, 'Donasi', 'Pembagian dana parkir utk TPQ'],
        ['2026-07-28', 'keluar', 720000, 'Donasi', 'Pembagian dana parkir utk Anak Yatim-Duafa'],
        ['2026-07-28', 'masuk', 225000, 'Iuran', "Dari RT 01 iuran makam bln Feb - April'26"],
        ['2026-07-29', 'masuk', 150000, 'Parkir', 'Dari Bp. Hasan (Parkir mobil Selatan)'],
    ];

    public function run(): void
    {
        $rw = $this->resolveRw03();
        $unit = KasUnit::forWilayah($rw);
        $adminId = User::where('role', 'admin')->value('id');

        // Self-check: buku harus balance dengan saldo akhir buku asli
        $rows = collect(self::TRANSAKSI);
        $saldo = $rows->where(1, 'masuk')->sum(2) - $rows->where(1, 'keluar')->sum(2);
        if ($saldo !== self::SALDO_AKHIR) {
            throw new \RuntimeException(
                "Self-check gagal: saldo seeder {$saldo} ≠ buku asli ".self::SALDO_AKHIR.' — data tidak ditanam.'
            );
        }

        // Idempotent: ganti baris manual unit RW03 (auto-post iuran dibiarkan utuh)
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
            ['Saldo akhir (29/07/2026)', 'Rp'.number_format($saldo, 0, ',', '.')],
        ]);
    }

    private function resolveRw03(): Wilayah
    {
        $kel = Wilayah::where('tingkat', 'Kelurahan')->where('nama', 'like', '%Bendul Merisi%')->first();
        $rw = $kel?->children()->where('nama', 'like', '%RW 03%')->first();

        abort_unless($rw instanceof Wilayah, 500, 'Wilayah RW 03 Bendul Merisi tidak ditemukan — jalankan WilayahSeeder dulu.');

        return $rw;
    }
}
