<?php

namespace Database\Seeders;

use App\Models\KasUnit;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;

/**
 * Materialize unit kas WILAYAH saja (RT/RW/Kelurahan/Kecamatan) — idempotent.
 * Tanpa data transaksi & tanpa organisasi contoh: kas mulai kosong (data real),
 * petugas mencatat Saldo Awal manual per unit; iuran RT ter-post otomatis saat dibayar.
 */
class KasUnitWilayahSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::where('role', 'admin')->value('id');

        $count = 0;
        Wilayah::whereIn('tingkat', ['RT', 'RW', 'Kelurahan'])->each(function (Wilayah $w) use ($adminId, &$count) {
            KasUnit::firstOrCreate(
                ['jenis' => strtolower($w->tingkat), 'wilayah_id' => $w->id],
                ['nama' => $w->nama, 'created_by' => $adminId]
            );
            $count++;
        });

        KasUnit::firstOrCreate(
            ['jenis' => 'kecamatan', 'wilayah_id' => null],
            ['nama' => 'Kecamatan Wonocolo', 'created_by' => $adminId]
        );

        $this->command->info("✅ Unit kas wilayah: {$count} + 1 kecamatan (tanpa transaksi — data real dimulai bersih).");
    }
}
