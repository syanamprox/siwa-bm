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
 * Tagihan iuran RT 02 RW 03 Bendul Merisi — HANYA jenis iuran milik RT 02
 * (Sosial/RT/Rukem via JenisIuranRt02Seeder). Koneksi & tagihan ke jenis
 * global (lama, dummy) diputus supaya tidak campur antar RT.
 *
 * 6 bulan (2026-03..08): ≤05 disiplin · 06 mulai menunggak · 07 campur · 08 mayoritas belum.
 * Pembayaran backdate ke jatuh tempo → tren bulanan akurat. Idempotent.
 */
class Rt02Rw03IuranSeeder extends Seeder
{
    use WithoutModelEvents;

    private const BULAN = ['2026-03', '2026-04', '2026-05', '2026-06', '2026-07', '2026-08'];

    public function run(): void
    {
        $rt = \App\Models\Wilayah::where('nama', 'RT 02 RW 03 Bendul Merisi')->where('tingkat', 'RT')->first();
        if (! $rt) {
            $this->command->warn('RT 02 RW 03 tidak ditemukan — dilewati.');

            return;
        }

        $keluargas = Keluarga::where('rt_id', $rt->id)->orderBy('no_kk')->get();
        $jenisRt = JenisIuran::where('rt_id', $rt->id)->where('is_aktif', 1)->get();
        if ($keluargas->isEmpty() || $jenisRt->isEmpty()) {
            $this->command->warn('Kosong (keluarga/jenis RT) — jalankan WargaRealRt02Rw03Seeder + JenisIuranRt02Seeder dulu.');

            return;
        }

        // ── Bersihkan warisan dummy: koneksi & tagihan jenis GLOBAL utk KK RT 02 ──
        $globalJenisIds = JenisIuran::whereNull('rt_id')->pluck('id');
        $kkIds = $keluargas->pluck('id');
        DB::transaction(function () use ($globalJenisIds, $kkIds) {
            $oldIuranIds = Iuran::whereIn('kk_id', $kkIds)->whereIn('jenis_iuran_id', $globalJenisIds)->pluck('id');
            PembayaranIuran::whereIn('iuran_id', $oldIuranIds)->delete();
            Iuran::whereIn('id', $oldIuranIds)->delete();
            KeluargaIuran::whereIn('keluarga_id', $kkIds)->whereIn('jenis_iuran_id', $globalJenisIds)->delete();
        });

        // ── Tagihan 6 bulan, hanya jenis milik RT ──
        $tagihan = 0;
        foreach ($keluargas as $idx => $kel) {
            foreach ($jenisRt as $jenis) {
                $conn = KeluargaIuran::where('keluarga_id', $kel->id)->where('jenis_iuran_id', $jenis->id)->first();
                $nominal = ($conn && $conn->nominal_custom) ? $conn->nominal_custom : $jenis->jumlah;

                foreach (self::BULAN as $bulan) {
                    $status = $this->statusFor($bulan, $idx);

                    $iuran = Iuran::updateOrCreate(
                        ['kk_id' => $kel->id, 'jenis_iuran_id' => $jenis->id, 'periode_bulan' => $bulan],
                        [
                            'nominal' => $nominal,
                            'status' => $status,
                            'jatuh_tempo' => $bulan.'-25',
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

        $this->command->info("✅ Tagihan RT 02 RW 03 (jenis RT-scope): {$tagihan} · ".count($keluargas).' KK × '.count($jenisRt).' jenis × 6 bulan.');
    }

    private function statusFor(string $bulan, int $idx): string
    {
        return match ($bulan) {
            '2026-03', '2026-04', '2026-05' => 'lunas',
            '2026-06' => $idx % 4 === 0 ? 'belum_bayar' : 'lunas',
            '2026-07' => $idx % 3 === 0 ? 'belum_bayar' : 'lunas',
            default => $idx % 3 === 0 ? 'lunas' : 'belum_bayar',
        };
    }

    private function bayar(Iuran $iuran, float $jumlah, string $bulan): void
    {
        $metode = $iuran->id % 2 ? 'cash' : 'transfer';
        $bayar = PembayaranIuran::create([
            'iuran_id' => $iuran->id,
            'jumlah_bayar' => $jumlah,
            'metode_pembayaran' => $metode,
            'nomor_referensi' => $metode === 'transfer' ? 'TRF-'.$bulan.str_pad((string) $iuran->id, 5, '0', STR_PAD_LEFT) : null,
            'created_by' => 1,
        ]);
        $tgl = $bulan.'-'.str_pad((string) (11 + $iuran->id % 5), 2, '0', STR_PAD_LEFT);
        DB::table('pembayaran_iurans')->where('id', $bayar->id)->update(['created_at' => $tgl.' 09:00:00']);
    }
}
