<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('wilayah')->delete();

        // Create Kelurahan Bendul Merisi
        $kelurahan = [
            'kode' => 'BENDULMER',
            'nama' => 'Kelurahan Bendul Merisi',
            'tingkat' => 'kelurahan',
            'parent_id' => null,
        ];

        $kelurahanId = DB::table('wilayah')->insertGetId($kelurahan);

        // Create RW 1-12
        $rwData = [];
        for ($i = 1; $i <= 12; $i++) {
            $rwNumber = str_pad($i, 2, '0', STR_PAD_LEFT);
            $rwData[] = [
                'kode' => $rwNumber,
                'nama' => "RW $rwNumber Bendul Merisi",
                'tingkat' => 'rw',
                'parent_id' => $kelurahanId,
            ];
        }

        // Insert all RWs
        DB::table('wilayah')->insert($rwData);

        // Get RW 3 ID for creating RTs
        $rw3Id = DB::table('wilayah')
            ->where('kode', '03')
            ->where('tingkat', 'rw')
            ->value('id');

        if ($rw3Id) {
            // Create 4 RTs under RW 03
            $rtData = [
                [
                    'kode' => '0301',
                    'nama' => 'RT 01 RW 03 Bendul Merisi',
                    'tingkat' => 'rt',
                    'parent_id' => $rw3Id,
                ],
                [
                    'kode' => '0302',
                    'nama' => 'RT 02 RW 03 Bendul Merisi',
                    'tingkat' => 'rt',
                    'parent_id' => $rw3Id,
                ],
                [
                    'kode' => '0303',
                    'nama' => 'RT 03 RW 03 Bendul Merisi',
                    'tingkat' => 'rt',
                    'parent_id' => $rw3Id,
                ],
                [
                    'kode' => '0304',
                    'nama' => 'RT 04 RW 03 Bendul Merisi',
                    'tingkat' => 'rt',
                    'parent_id' => $rw3Id,
                ],
            ];

            // Insert RTs
            DB::table('wilayah')->insert($rtData);
        }

        $this->command->info('✅ Wilayah data seeded successfully!');
        $this->command->info('📍 Kelurahan: Bendul Merisi');
        $this->command->info('🏘️ RW: 1-12');
        $this->command->info('🏠 RT: RW 03 memiliki 4 RT');
        $this->command->info('📊 Total: 1 Kelurahan + 12 RW + 4 RT = 17 wilayah');
    }
}
