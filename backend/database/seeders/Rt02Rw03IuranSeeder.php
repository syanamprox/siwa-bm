<?php

namespace Database\Seeders;

use App\Models\Iuran;
use App\Models\JenisIuran;
use App\Models\Keluarga;
use App\Models\KeluargaIuran;
use App\Models\PembayaranIuran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * SEEDER IURAN RT 02 RW 03 Bendul Merisi (data dummy).
 *
 * Scope: semua KK di rt_id 0302 (RT 02 RW 03) KECUALI KK id 1 (dilayani KK1 seeders).
 * - Koneksi keluarga_iuran: semua KK → Iuran Kampung; mayoritas → Iuran Kebersihan.
 *   1 KK sengaja TANPA koneksi (untuk test flow "iuran tersedia" di UI).
 * - Tagihan 6 bulan (2026-03 s.d. 2026-08) dengan status per bulan:
 *   ≤05 lunas semua · 06 mulai ada menunggak · 07 campur lunas/belum/sebagian · 08 mayoritas belum.
 * - Pembayaran di-backdate mengikuti jatuh tempo → chart tren 6 bulan hidup.
 *
 * Idempotent: Iuran updateOrCreate by (kk, jenis, periode); pembayaran hanya dibuat
 * jika iuran berstatus lunas/sebagian dan belum punya pembayaran.
 */
class Rt02Rw03IuranSeeder extends Seeder
{
    use WithoutModelEvents;

    private const BULAN = ['2026-03', '2026-04', '2026-05', '2026-06', '2026-07', '2026-08'];

    public function run(): void
    {
        $keluargas = Keluarga::where('rt_id', 16)->where('id', '!=', 1)->orderBy('no_kk')->get();
        if ($keluargas->isEmpty()) {
            $this->command->warn('⚠️  Tidak ada keluarga di RT 02 RW 03 (rt_id=16) — jalankan Rt02Rw03BendulMerisiSeeder dulu.');

            return;
        }

        $kampung = JenisIuran::where('nama', 'like', '%Kampung%')->first();
        $kebersihan = JenisIuran::where('nama', 'like', '%Kebersihan%')->first();
        if (! $kampung || ! $kebersihan) {
            $this->command->warn('⚠️  JenisIuran Kampung/Kebersihan tidak ditemukan — jalankan JenisIuranSeeder dulu.');

            return;
        }

        /* ── 1. Koneksi keluarga ↔ jenis iuran ── */
        foreach ($keluargas as $idx => $kel) {
            if ($idx === $keluargas->count() - 1) {
                continue; // 1 KK terakhir tanpa koneksi — utk test flow "iuran tersedia"
            }

            KeluargaIuran::updateOrCreate(
                ['keluarga_id' => $kel->id, 'jenis_iuran_id' => $kampung->id],
                ['nominal_custom' => 10000, 'status_aktif' => true, 'created_by' => 1]
            );

            if ($idx !== 6) { // 1 KK skip kebersihan
                KeluargaIuran::updateOrCreate(
                    ['keluarga_id' => $kel->id, 'jenis_iuran_id' => $kebersihan->id],
                    ['nominal_custom' => 25000, 'status_aktif' => true, 'created_by' => 1]
                );
            }
        }

        /* ── 2. Tagihan + pembayaran ── */
        $tagihan = 0;
        foreach ($keluargas as $idx => $kel) {
            $conns = KeluargaIuran::where('keluarga_id', $kel->id)->where('status_aktif', 1)->get();

            foreach ($conns as $conn) {
                $jenis = JenisIuran::find($conn->jenis_iuran_id);
                $nominal = $conn->nominal_custom ?: $jenis->jumlah;

                foreach (self::BULAN as $bulan) {
                    $status = $this->statusFor($bulan, $idx);

                    $iuran = Iuran::updateOrCreate(
                        ['kk_id' => $kel->id, 'jenis_iuran_id' => $jenis->id, 'periode_bulan' => $bulan],
                        [
                            'nominal' => $nominal,
                            'status' => $status,
                            'jatuh_tempo' => $bulan.'-10',
                            'created_by' => 1,
                        ]
                    );
                    $tagihan++;

                    if ($status === 'lunas' && $iuran->pembayaran()->count() === 0) {
                        $this->bayar($iuran, $nominal, $bulan);
                    }
                }
            }
        }

        $this->command->info("✅ Iuran RT 02 RW 03: {$tagihan} tagihan (6 bulan × ".count($keluargas)." KK) — lunas/belum/sebagian + pembayaran backdated.");
    }

    /**
     * Deterministik by (bulan, idx KK) — hanya lunas/belum_bayar (tanpa bayar sebagian):
     * ≤05: disiplin · 06: mulai menunggak · 07: campur · 08 (berjalan): mayoritas belum.
     */
    private function statusFor(string $bulan, int $idx): string
    {
        return match ($bulan) {
            '2026-03', '2026-04', '2026-05' => 'lunas',
            '2026-06' => $idx % 4 === 0 ? 'belum_bayar' : 'lunas',
            '2026-07' => $idx % 3 === 0 ? 'belum_bayar' : 'lunas',
            default => $idx % 3 === 0 ? 'lunas' : 'belum_bayar', // 2026-08
        };
    }

    private function bayar(Iuran $iuran, float $jumlah, string $bulan): void
    {
        $metode = $iuran->id % 2 ? 'cash' : 'transfer';
        $ref = $metode === 'transfer' ? 'TRF-'.substr($bulan, 0, 4).str_pad((string) $iuran->id, 5, '0', STR_PAD_LEFT) : null;

        $bayar = PembayaranIuran::create([
            'iuran_id' => $iuran->id,
            'jumlah_bayar' => $jumlah,
            'metode_pembayaran' => $metode,
            'nomor_referensi' => $ref,
            'created_by' => 1,
        ]);

        // Backdate: jatuh tempo (tgl 10) + 1-5 hari, agar tren bulanan akurat
        $tgl = $bulan.'-'.str_pad((string) (11 + $iuran->id % 5), 2, '0', STR_PAD_LEFT);
        DB::table('pembayaran_iurans')->where('id', $bayar->id)->update(['created_at' => $tgl.' 09:00:00']);
    }
}
