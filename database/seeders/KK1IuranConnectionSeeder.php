<?php

namespace Database\Seeders;

use App\Models\Keluarga;
use App\Models\JenisIuran;
use App\Models\KeluargaIuran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KK1IuranConnectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing connections for KK ID 1 only
        DB::table('keluarga_iuran')->where('keluarga_id', 1)->delete();

        // Get KK ID 1
        $keluarga = Keluarga::find(1);
        if (!$keluarga) {
            $this->command->error('❌ Keluarga dengan ID 1 tidak ditemukan!');
            return;
        }

        echo "📋 Found KK ID 1: " . $keluarga->no_kk . " - " . ($keluarga->kepalaKeluarga->nama_lengkap ?? '-') . PHP_EOL;

        // Get Iuran Kampung and Kebersihan
        $iuranKampung = JenisIuran::where('nama', 'like', '%kampung%')->first();
        $iuranKebersihan = JenisIuran::where('nama', 'like', '%kebersihan%')->first();

        if (!$iuranKampung) {
            $this->command->error('❌ Iuran Kampung tidak ditemukan!');
        } else {
            echo "💰 Found Iuran Kampung: ID " . $iuranKampung->id . " (Rp " . number_format($iuranKampung->jumlah, 0, ',', '.') . ")" . PHP_EOL;
        }

        if (!$iuranKebersihan) {
            $this->command->error('❌ Iuran Kebersihan tidak ditemukan!');
        } else {
            echo "💰 Found Iuran Kebersihan: ID " . $iuranKebersihan->id . " (Rp " . number_format($iuranKebersihan->jumlah, 0, ',', '.') . ")" . PHP_EOL;
        }

        $connectionsCreated = 0;

        // Create connection for Iuran Kampung
        if ($iuranKampung) {
            KeluargaIuran::create([
                'keluarga_id' => 1,
                'jenis_iuran_id' => $iuranKampung->id,
                'nominal_custom' => 10000, // Custom nominal for Kampung
                'status_aktif' => true,
                'alasan_custom' => null,
                'created_by' => 1,
            ]);
            $connectionsCreated++;
            echo "✅ Connected KK ID 1 with Iuran Kampung (Rp 10.000)" . PHP_EOL;
        }

        // Create connection for Iuran Kebersihan
        if ($iuranKebersihan) {
            KeluargaIuran::create([
                'keluarga_id' => 1,
                'jenis_iuran_id' => $iuranKebersihan->id,
                'nominal_custom' => 25000, // Custom nominal for Kebersihan
                'status_aktif' => true,
                'alasan_custom' => null,
                'created_by' => 1,
            ]);
            $connectionsCreated++;
            echo "✅ Connected KK ID 1 with Iuran Kebersihan (Rp 25.000)" . PHP_EOL;
        }

        $this->command->info('🎉 KK ID 1 Iuran connections created successfully!');
        $this->command->info("📊 Total connections created: {$connectionsCreated}");
    }
}
