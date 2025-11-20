<?php

namespace Database\Seeders;

use App\Models\Keluarga;
use App\Models\JenisIuran;
use App\Models\KeluargaIuran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KeluargaIuranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('keluarga_iuran')->delete();

        // Get existing data
        $keluargas = Keluarga::where('status_keluarga', 'Aktif')->get();
        $jenisIurans = JenisIuran::where('is_aktif', 1)->get();

        if ($keluargas->isEmpty() || $jenisIurans->isEmpty()) {
            $this->command->warn('⚠️ No keluarga or jenis iuran data found. Skipping keluarga_iuran seeding.');
            return;
        }

        $connectionsCreated = 0;

        // Create koneksi iuran untuk setiap keluarga aktif
        foreach ($keluargas as $keluarga) {
            foreach ($jenisIurans as $jenisIuran) {
                // Skip iuran sekali (17 Agustus) jika sudah lewat tahun ini
                if ($jenisIuran->periode === 'tahunan' && $jenisIuran->nama === 'Iuran Acara 17 Agustus') {
                    $currentYear = now()->year;
                    if ($currentYear > 2025) {
                        continue; // Skip untuk tahun berikutnya
                    }
                }

                // Cek apakah koneksi sudah ada
                $exists = KeluargaIuran::where('keluarga_id', $keluarga->id)
                    ->where('jenis_iuran_id', $jenisIuran->id)
                    ->exists();

                if (!$exists) {
                    // Create default connection
                    KeluargaIuran::create([
                        'keluarga_id' => $keluarga->id,
                        'jenis_iuran_id' => $jenisIuran->id,
                        'nominal_custom' => null, // Use default nominal
                        'status_aktif' => true,
                        'alasan_custom' => null,
                        'created_by' => 1, // Default admin user
                    ]);

                    $connectionsCreated++;
                }
            }
        }

        $this->command->info('✅ Keluarga Iuran connections seeded successfully!');
        $this->command->info('👨 Total Keluarga Aktif: ' . $keluargas->count());
        $this->command->info('💰 Total Jenis Iuran: ' . $jenisIurans->count());
        $this->command->info('🔗 Total Koneksi Dibuat: ' . $connectionsCreated);
    }
}
