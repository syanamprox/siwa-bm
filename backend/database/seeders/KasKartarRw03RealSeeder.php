<?php

namespace Database\Seeders;

use App\Models\KasTransaksi;
use App\Models\KasUnit;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;

/**
 * Buku kas ASLI Karang Taruna RW 03 Bendul Merisi — Februari 2023 s.d. Juli 2026.
 * Sumber: transkrip foto buku kas fisik (bendahara Kartar).
 *
 * Catatan transkripsi:
 *  - Baris tanpa tanggal tercatat (mis. "Pengeluaran Agustusan") diberi tanggal
 *    perkiraan berdasarkan posisi baris di buku — bukan tanggal pasti.
 *  - Nominal "Bakar-bakar (5 sosis)" 1 Jan 2026 dikoreksi dari Rp308.000 (tulisan
 *    buram) menjadi Rp408.000 — angka ini yang membuat saldo berjalan balance persis
 *    dengan seluruh saldo tercetak di halaman berikutnya (bukan hanya di baris itu).
 *  - Kategori "Revita"/"Wabila" dll tanpa keterangan rinci disimpan apa adanya
 *    (nama panitia/anggota yang menyetor-/mengeluarkan, bukan nama kategori baku).
 *
 * Idempotent: baris manual unit Kartar RW03 dihapus lalu ditanam ulang.
 */
class KasKartarRw03RealSeeder extends Seeder
{
    /** Saldo akhir sesuai buku asli — self-check, selisih 1 rupiah = gagal seed. */
    private const SALDO_AKHIR = 4440200;

    /** @var array<int, array{0: string, 1: string, 2: int, 3: string, 4: string}> [tanggal, tipe, jumlah, kategori, keterangan] */
    private const TRANSAKSI = [
        // ── 2023 ────────────────────────────────────────
        ['2023-02-28', 'masuk', 200000, 'Parkir', 'Uang parkir (26 lembar) — awal pencatatan'],
        ['2023-03-23', 'masuk', 235000, 'Saldo Awal', 'Sisa uang kas (serah terima)'],
        ['2023-03-23', 'masuk', 1051700, 'Saldo Awal', 'Uang kas (serah terima)'],
        ['2023-03-30', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2023-04-30', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2023-05-29', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2023-07-09', 'keluar', 55000, 'Perlengkapan', 'Pembelian map & air (Gian)'],
        ['2023-07-11', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2023-07-13', 'keluar', 85000, 'Kegiatan', 'Pengeluaran bakar-bakar'],
        ['2023-07-15', 'keluar', 20000, 'Perlengkapan', 'Pembelian map & fotocopy (Radit)'],
        ['2023-07-15', 'keluar', 33000, 'Operasional', 'Pengeluaran fotocopy (Cnessa)'],
        ['2023-08-01', 'keluar', 500000, 'Kegiatan', 'Pengeluaran Agustusan (Revita) — tanggal perkiraan'],
        ['2023-10-01', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2023-11-01', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2023-12-01', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2023-12-17', 'keluar', 50000, 'Kegiatan', 'Pengeluaran konsumsi (Revita)'],
        ['2023-12-24', 'keluar', 50000, 'Kegiatan', 'Konsumsi (Revita)'],
        ['2023-12-29', 'keluar', 33500, 'Operasional', 'Fotocopy + kekurangan konsumsi (Wabila)'],
        ['2023-12-25', 'keluar', 4000, 'Operasional', 'Print (Revita)'],
        // ── 2024 ────────────────────────────────────────
        ['2024-01-10', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2024-02-15', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2024-03-01', 'masuk', 1619500, 'Lain-lain', 'Sisa uang Agustusan'],
        ['2024-03-13', 'keluar', 500000, 'Kegiatan', 'Bukber'],
        ['2024-03-28', 'masuk', 224500, 'Lain-lain', 'Sisa uang Bukber'],
        ['2024-03-30', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2024-04-17', 'keluar', 58000, 'Lain-lain', 'Revita — tanggal perkiraan'],
        ['2024-04-25', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2024-04-28', 'masuk', 83000, 'Lain-lain', 'Sisa uang (Wabila)'],
        ['2024-05-10', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2024-06-20', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2024-07-04', 'keluar', 500000, 'Kegiatan', 'Dana Agustusan'],
        ['2024-07-07', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2024-08-13', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2024-09-11', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2024-10-15', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2024-11-05', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2024-12-11', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2024-12-30', 'keluar', 198500, 'Kegiatan', 'Dana Tahun Baru'],
        // ── 2025 ────────────────────────────────────────
        ['2025-01-01', 'keluar', 50000, 'Kegiatan', 'Konsumsi tahun baru'],
        ['2025-01-17', 'masuk', 5000, 'Lain-lain', 'Sisa tahun baru'],
        ['2025-01-17', 'masuk', 2000000, 'Donasi', 'Uang sisa hasil PHBN'],
        ['2025-02-20', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2025-03-09', 'keluar', 32500, 'Lain-lain', 'Revita'],
        ['2025-05-13', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2025-06-13', 'keluar', 100000, 'Kegiatan', 'Bakar-bakar'],
        ['2025-06-15', 'keluar', 105000, 'Kegiatan', 'Kekurangan bakar-bakar'],
        ['2025-07-29', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2025-08-03', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2025-08-23', 'keluar', 17000, 'Perlengkapan', 'Print sertifikat'],
        ['2025-08-29', 'keluar', 100000, 'Lain-lain', 'Uang hadiah panitia'],
        ['2025-09-13', 'keluar', 100000, 'Lain-lain', 'Revita'],
        ['2025-10-10', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2025-11-01', 'keluar', 72000, 'Kegiatan', 'Konsumsi cireng'],
        ['2025-11-29', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        // ── 2026 ────────────────────────────────────────
        ['2026-01-01', 'keluar', 408000, 'Kegiatan', 'Bakar-bakar (5 sosis)'],
        ['2026-01-02', 'keluar', 57500, 'Kegiatan', 'Jagung, kecap'],
        ['2026-01-09', 'keluar', 49500, 'Kegiatan', 'Air + arang'],
        ['2026-01-21', 'keluar', 500000, 'Kegiatan', 'Sewa Villa'],
        ['2026-01-30', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2026-02-17', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2026-03-20', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2026-04-02', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2026-05-30', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2026-06-11', 'masuk', 100000, 'Parkir', 'Uang parkir'],
        ['2026-07-01', 'keluar', 500000, 'Kegiatan', 'Uang Agustusan'],
        ['2026-07-20', 'masuk', 100000, 'Parkir', 'Uang parkir'],
    ];

    public function run(): void
    {
        $unit = $this->resolveUnit();
        $adminId = User::where('role', 'admin')->value('id');

        // Self-check: buku harus balance dengan saldo akhir buku asli (20/07/2026)
        $rows = collect(self::TRANSAKSI);
        $saldo = $rows->where(1, 'masuk')->sum(2) - $rows->where(1, 'keluar')->sum(2);
        if ($saldo !== self::SALDO_AKHIR) {
            throw new \RuntimeException(
                "Self-check gagal: saldo seeder {$saldo} ≠ buku asli ".self::SALDO_AKHIR.' — data tidak ditanam.'
            );
        }

        // Idempotent: ganti baris manual unit Kartar RW03
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
            ['Saldo akhir (20/07/2026)', 'Rp'.number_format($saldo, 0, ',', '.')],
        ]);
    }

    private function resolveUnit(): KasUnit
    {
        $kel = Wilayah::where('tingkat', 'Kelurahan')->where('nama', 'like', '%Bendul Merisi%')->first();
        $rw = $kel?->children()->where('nama', 'like', '%RW 03%')->first();

        abort_unless($rw instanceof Wilayah, 500, 'Wilayah RW 03 Bendul Merisi tidak ditemukan — jalankan WilayahSeeder dulu.');

        return KasUnit::firstOrCreate(
            ['jenis' => 'organisasi', 'wilayah_id' => $rw->id, 'nama' => 'Karang Taruna'],
            ['created_by' => User::where('role', 'admin')->value('id')],
        );
    }
}
