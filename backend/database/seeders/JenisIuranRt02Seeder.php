<?php

namespace Database\Seeders;

use App\Models\JenisIuran;
use App\Models\Keluarga;
use App\Models\KeluargaIuran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Jenis iuran MILIK RT 02 RW 03 Bendul Merisi (rt_id scope) — keputusan rapat RT:
 * Sosial Rp3.000 · Iuran RT Rp2.000 · Rukem Rp5.000 (semuanya bulanan).
 *
 * Otomatis menghubungkan semua KK domisili RT 02 RW 03 ke 3 jenis ini
 * (tanpa nominal custom — pakai default). Idempotent.
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
            ['nama' => 'Iuran Sosial', 'kode' => 'SOS-0302', 'jumlah' => 3000, 'periode' => 'bulanan', 'keterangan' => 'Dana sosial/kematian RT 02 RW 03'],
            ['nama' => 'Iuran RT', 'kode' => 'RT-0302', 'jumlah' => 2000, 'periode' => 'bulanan', 'keterangan' => 'Operasional RT 02 RW 03'],
            ['nama' => 'Iuran Rukem', 'kode' => 'RUK-0302', 'jumlah' => 5000, 'periode' => 'bulanan', 'keterangan' => 'Rukun kemasyarakatan RT 02 RW 03'],
        ];

        foreach ($jenisList as $j) {
            JenisIuran::updateOrCreate(
                ['kode' => $j['kode']],
                $j + ['rt_id' => $rt->id, 'is_aktif' => 1, 'sasaran' => 'kk']
            );
        }

        // Hubungkan semua KK RT 02 RW 03 ke 3 jenis ini (default nominal)
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
