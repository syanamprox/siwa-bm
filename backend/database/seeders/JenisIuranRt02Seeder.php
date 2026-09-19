<?php

namespace Database\Seeders;

use App\Models\JenisIuran;
use App\Models\Keluarga;
use App\Models\KeluargaIuran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Jenis iuran MILIK RT 02 RW 03 Bendul Merisi (rt_id scope) — keputusan user 19 Sep 2026:
 * Iuran Kampung Rp5.000 · Iuran Rukem Rp5.000 (bulanan). Iuran Sosial DIHAPUS,
 * Iuran RT lama (Rp2.000) diganti Iuran Kampung.
 *
 * Otomatis menghubungkan semua KK domisili RT 02 RW 03 ke 2 jenis ini
 * (tanpa nominal custom — pakai default). Idempotent — jenis lama di luar daftar
 * ikut dibersihkan bersama koneksi keluarga_iuran-nya.
 */
class JenisIuranRt02Seeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $rt = \App\Models\Wilayah::where('nama', 'RT 02 RW 03 Bendul Merisi')->where('tingkat', 'RT')->first();
        if (! $rt) {
            $this->command->warn('RT 02 RW 03 Bendul Merisi tidak ditemukan — seeder dilewati.');

            return;
        }

        $jenisList = [
            ['nama' => 'Iuran Kampung', 'kode' => 'KPG-0302', 'jumlah' => 5000, 'periode' => 'bulanan', 'keterangan' => 'Iuran kampung RT 02 RW 03'],
            ['nama' => 'Iuran Rukem', 'kode' => 'RUK-0302', 'jumlah' => 5000, 'periode' => 'bulanan', 'keterangan' => 'Rukun kemasyarakatan RT 02 RW 03'],
        ];

        foreach ($jenisList as $j) {
            JenisIuran::updateOrCreate(
                ['kode' => $j['kode']],
                $j + ['rt_id' => $rt->id, 'is_aktif' => 1, 'sasaran' => 'kk']
            );
        }

        // Bersihkan jenis lama di luar daftar (Sosial, Iuran RT kode lama) + koneksi KK-nya
        $stale = JenisIuran::where('rt_id', $rt->id)->whereNotIn('kode', array_column($jenisList, 'kode'))->pluck('id');
        if ($stale->isNotEmpty()) {
            KeluargaIuran::whereIn('jenis_iuran_id', $stale)->delete();
            JenisIuran::whereIn('id', $stale)->delete();
            $this->command->warn('🧹 Dihapus '.count($stale).' jenis iuran lama RT 02 (beserta koneksi KK).');
        }

        // Hubungkan semua KK RT 02 RW 03 ke 2 jenis ini (default nominal)
        $jenisIds = JenisIuran::whereIn('kode', array_column($jenisList, 'kode'))->pluck('id');
        $kkIds = Keluarga::where('rt_id', $rt->id)->pluck('id');
        $conns = 0;
        foreach ($kkIds as $kkId) {
            foreach ($jenisIds as $jenisId) {
                KeluargaIuran::firstOrCreate(
                    ['keluarga_id' => $kkId, 'jenis_iuran_id' => $jenisId],
                    ['status_aktif' => true, 'created_by' => 1]
                );
                $conns++;
            }
        }

        $this->command->info('✅ Jenis iuran RT 02 RW 03: '.count($jenisList).' jenis ('.number_format(array_sum(array_column($jenisList, 'jumlah'))).'/bln per KK) · '.$conns.' koneksi KK.');
    }
}
