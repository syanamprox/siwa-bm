<?php

namespace Database\Seeders;

use App\Models\KasUnit;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;

/**
 * Unit kas ORGANISASI real sekitar RW 03 Bendul Merisi — idempotent (firstOrCreate).
 * Satu wilayah boleh punya BANYAK organisasi (unique: jenis+wilayah+nama).
 * Kas organisasi mulai kosong; petugas mencatat Saldo Awal manual.
 */
class KasOrganisasiRw03Seeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::where('role', 'admin')->value('id');

        $organisasi = [
            'RW 03 Bendul Merisi' => ['Musholla Roudhatul Jannah', 'Rukem Sehati', 'PKK', 'Karang Taruna'],
            'RT 02 RW 03 Bendul Merisi' => ['Sosial', 'PKK'],
        ];

        $count = 0;
        foreach ($organisasi as $wilayahNama => $daftarNama) {
            $wilayah = Wilayah::where('nama', $wilayahNama)->first();
            if (! $wilayah) {
                $this->command->warn("⚠️ Wilayah {$wilayahNama} tidak ditemukan — skip.");
                continue;
            }

            foreach ($daftarNama as $nama) {
                KasUnit::firstOrCreate(
                    ['jenis' => 'organisasi', 'wilayah_id' => $wilayah->id, 'nama' => $nama],
                    ['created_by' => $adminId]
                );
                $count++;
            }

            $this->command->info("  Organisasi under {$wilayahNama}: ".implode(' · ', $daftarNama));
        }

        $this->command->info("✅ Unit kas organisasi: {$count} terdaftar.");
    }
}
