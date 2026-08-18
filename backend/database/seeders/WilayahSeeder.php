<?php

namespace Database\Seeders;

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
        DB::table('wilayahs')->delete();

        // Statistics counters
        $totalKelurahan = 0;
        $totalRw = 0;
        $totalRt = 0;

        // ===========================================
        // KECAMATAN WONOCOLO SURABAYA
        // ===========================================

        // 1. Kelurahan Bendul Merisi (12 RW, 58 RT - Special: RW 03 only has RT 1-4)
        $bendulMerisiId = DB::table('wilayahs')->insertGetId([
            'kode' => 'BM',
            'nama' => 'Kelurahan Bendul Merisi',
            'tingkat' => 'Kelurahan',
            'parent_id' => null,
        ]);
        $totalKelurahan++;

        // RW Bendul Merisi
        $rwBendulMerisi = [
            '01' => 'RW 01 Bendul Merisi',
            '02' => 'RW 02 Bendul Merisi',
            '03' => 'RW 03 Bendul Merisi',
            '04' => 'RW 04 Bendul Merisi',
            '05' => 'RW 05 Bendul Merisi',
            '06' => 'RW 06 Bendul Merisi',
            '07' => 'RW 07 Bendul Merisi',
            '08' => 'RW 08 Bendul Merisi',
            '09' => 'RW 09 Bendul Merisi',
            '10' => 'RW 10 Bendul Merisi',
            '11' => 'RW 11 Bendul Merisi',
            '12' => 'RW 12 Bendul Merisi'
        ];

        // RT distribution for Bendul Merisi (58 RTs total)
        $rtBendulMerisi = [
            '01' => 5, // RW 01 has 5 RTs
            '02' => 5, // RW 02 has 5 RTs
            '03' => 4, // RW 03 has 4 RTs (as specified by user)
            '04' => 4, // RW 04 has 4 RTs
            '05' => 5, // RW 05 has 5 RTs
            '06' => 5, // RW 06 has 5 RTs
            '07' => 4, // RW 07 has 4 RTs
            '08' => 5, // RW 08 has 5 RTs
            '09' => 5, // RW 09 has 5 RTs
            '10' => 4, // RW 10 has 4 RTs
            '11' => 5, // RW 11 has 5 RTs
            '12' => 4  // RW 12 has 4 RTs
        ];

        foreach ($rwBendulMerisi as $rwNumber => $rwName) {
            $rwId = DB::table('wilayahs')->insertGetId([
                'kode' => $rwNumber,
                'nama' => $rwName,
                'tingkat' => 'RW',
                'parent_id' => $bendulMerisiId,
            ]);
            $totalRw++;

            // Create RTs for this RW
            for ($j = 1; $j <= $rtBendulMerisi[$rwNumber]; $j++) {
                $rtNumber = str_pad($j, 2, '0', STR_PAD_LEFT);
                DB::table('wilayahs')->insert([
                    'kode' => "{$rwNumber}{$rtNumber}",
                    'nama' => "RT $rtNumber $rwName",
                    'tingkat' => 'RT',
                    'parent_id' => $rwId,
                ]);
                $totalRt++;
            }
        }

        // 2. Kelurahan Jemur Wonosari (10 RW, 63 RT)
        $jemurWonosariId = DB::table('wilayahs')->insertGetId([
            'kode' => 'JW',
            'nama' => 'Kelurahan Jemur Wonosari',
            'tingkat' => 'Kelurahan',
            'parent_id' => null,
        ]);
        $totalKelurahan++;

        // RW Jemur Wonosari
        $rwJemurWonosari = [
            '01' => 'RW 01 Jemur Wonosari',
            '02' => 'RW 02 Jemur Wonosari',
            '03' => 'RW 03 Jemur Wonosari',
            '04' => 'RW 04 Jemur Wonosari',
            '05' => 'RW 05 Jemur Wonosari',
            '06' => 'RW 06 Jemur Wonosari',
            '07' => 'RW 07 Jemur Wonosari',
            '08' => 'RW 08 Jemur Wonosari',
            '09' => 'RW 09 Jemur Wonosari',
            '10' => 'RW 10 Jemur Wonosari'
        ];

        // RT distribution for Jemur Wonosari (63 RTs total)
        $rtJemurWonosari = [
            '01' => 6, // RW 01 has 6 RTs
            '02' => 6, // RW 02 has 6 RTs
            '03' => 6, // RW 03 has 6 RTs
            '04' => 6, // RW 04 has 6 RTs
            '05' => 6, // RW 05 has 6 RTs
            '06' => 7, // RW 06 has 7 RTs
            '07' => 7, // RW 07 has 7 RTs
            '08' => 6, // RW 08 has 6 RTs
            '09' => 7, // RW 09 has 7 RTs
            '10' => 6  // RW 10 has 6 RTs
        ];

        foreach ($rwJemurWonosari as $rwNumber => $rwName) {
            $rwId = DB::table('wilayahs')->insertGetId([
                'kode' => $rwNumber,
                'nama' => $rwName,
                'tingkat' => 'RW',
                'parent_id' => $jemurWonosariId,
            ]);
            $totalRw++;

            // Create RTs for this RW
            for ($j = 1; $j <= $rtJemurWonosari[$rwNumber]; $j++) {
                $rtNumber = str_pad($j, 2, '0', STR_PAD_LEFT);
                DB::table('wilayahs')->insert([
                    'kode' => "{$rwNumber}{$rtNumber}",
                    'nama' => "RT $rtNumber $rwName",
                    'tingkat' => 'RT',
                    'parent_id' => $rwId,
                ]);
                $totalRt++;
            }
        }

        // 3. Kelurahan Margorejo (8 RW, 36 RT)
        $margorejoId = DB::table('wilayahs')->insertGetId([
            'kode' => 'MR',
            'nama' => 'Kelurahan Margorejo',
            'tingkat' => 'Kelurahan',
            'parent_id' => null,
        ]);
        $totalKelurahan++;

        // RW Margorejo
        $rwMargorejo = [
            '01' => 'RW 01 Margorejo',
            '02' => 'RW 02 Margorejo',
            '03' => 'RW 03 Margorejo',
            '04' => 'RW 04 Margorejo',
            '05' => 'RW 05 Margorejo',
            '06' => 'RW 06 Margorejo',
            '07' => 'RW 07 Margorejo',
            '08' => 'RW 08 Margorejo'
        ];

        // RT distribution for Margorejo (36 RTs total)
        $rtMargorejo = [
            '01' => 5, // RW 01 has 5 RTs
            '02' => 4, // RW 02 has 4 RTs
            '03' => 4, // RW 03 has 4 RTs
            '04' => 5, // RW 04 has 5 RTs
            '05' => 4, // RW 05 has 4 RTs
            '06' => 5, // RW 06 has 5 RTs
            '07' => 5, // RW 07 has 5 RTs
            '08' => 4  // RW 08 has 4 RTs
        ];

        foreach ($rwMargorejo as $rwNumber => $rwName) {
            $rwId = DB::table('wilayahs')->insertGetId([
                'kode' => $rwNumber,
                'nama' => $rwName,
                'tingkat' => 'RW',
                'parent_id' => $margorejoId,
            ]);
            $totalRw++;

            // Create RTs for this RW
            for ($j = 1; $j <= $rtMargorejo[$rwNumber]; $j++) {
                $rtNumber = str_pad($j, 2, '0', STR_PAD_LEFT);
                DB::table('wilayahs')->insert([
                    'kode' => "{$rwNumber}{$rtNumber}",
                    'nama' => "RT $rtNumber $rwName",
                    'tingkat' => 'RT',
                    'parent_id' => $rwId,
                ]);
                $totalRt++;
            }
        }

        // 4. Kelurahan Sidosermo (8 RW, 35 RT)
        $sidosermoId = DB::table('wilayahs')->insertGetId([
            'kode' => 'SD',
            'nama' => 'Kelurahan Sidosermo',
            'tingkat' => 'Kelurahan',
            'parent_id' => null,
        ]);
        $totalKelurahan++;

        // RW Sidosermo
        $rwSidosermo = [
            '01' => 'RW 01 Sidosermo',
            '02' => 'RW 02 Sidosermo',
            '03' => 'RW 03 Sidosermo',
            '04' => 'RW 04 Sidosermo',
            '05' => 'RW 05 Sidosermo',
            '06' => 'RW 06 Sidosermo',
            '07' => 'RW 07 Sidosermo',
            '08' => 'RW 08 Sidosermo'
        ];

        // RT distribution for Sidosermo (35 RTs total)
        $rtSidosermo = [
            '01' => 4, // RW 01 has 4 RTs
            '02' => 4, // RW 02 has 4 RTs
            '03' => 5, // RW 03 has 5 RTs
            '04' => 4, // RW 04 has 4 RTs
            '05' => 4, // RW 05 has 4 RTs
            '06' => 5, // RW 06 has 5 RTs
            '07' => 5, // RW 07 has 5 RTs
            '08' => 4  // RW 08 has 4 RTs
        ];

        foreach ($rwSidosermo as $rwNumber => $rwName) {
            $rwId = DB::table('wilayahs')->insertGetId([
                'kode' => $rwNumber,
                'nama' => $rwName,
                'tingkat' => 'RW',
                'parent_id' => $sidosermoId,
            ]);
            $totalRw++;

            // Create RTs for this RW
            for ($j = 1; $j <= $rtSidosermo[$rwNumber]; $j++) {
                $rtNumber = str_pad($j, 2, '0', STR_PAD_LEFT);
                DB::table('wilayahs')->insert([
                    'kode' => "{$rwNumber}{$rtNumber}",
                    'nama' => "RT $rtNumber $rwName",
                    'tingkat' => 'RT',
                    'parent_id' => $rwId,
                ]);
                $totalRt++;
            }
        }

        // 5. Kelurahan Siwalankerto (6 RW, 40 RT)
        $siwalankertoId = DB::table('wilayahs')->insertGetId([
            'kode' => 'SW',
            'nama' => 'Kelurahan Siwalankerto',
            'tingkat' => 'Kelurahan',
            'parent_id' => null,
        ]);
        $totalKelurahan++;

        // RW Siwalankerto
        $rwSiwalankerto = [
            '01' => 'RW 01 Siwalankerto',
            '02' => 'RW 02 Siwalankerto',
            '03' => 'RW 03 Siwalankerto',
            '04' => 'RW 04 Siwalankerto',
            '05' => 'RW 05 Siwalankerto',
            '06' => 'RW 06 Siwalankerto'
        ];

        // RT distribution for Siwalankerto (40 RTs total)
        $rtSiwalankerto = [
            '01' => 7, // RW 01 has 7 RTs
            '02' => 6, // RW 02 has 6 RTs
            '03' => 7, // RW 03 has 7 RTs
            '04' => 6, // RW 04 has 6 RTs
            '05' => 7, // RW 05 has 7 RTs
            '06' => 7  // RW 06 has 7 RTs
        ];

        foreach ($rwSiwalankerto as $rwNumber => $rwName) {
            $rwId = DB::table('wilayahs')->insertGetId([
                'kode' => $rwNumber,
                'nama' => $rwName,
                'tingkat' => 'RW',
                'parent_id' => $siwalankertoId,
            ]);
            $totalRw++;

            // Create RTs for this RW
            for ($j = 1; $j <= $rtSiwalankerto[$rwNumber]; $j++) {
                $rtNumber = str_pad($j, 2, '0', STR_PAD_LEFT);
                DB::table('wilayahs')->insert([
                    'kode' => "{$rwNumber}{$rtNumber}",
                    'nama' => "RT $rtNumber $rwName",
                    'tingkat' => 'RT',
                    'parent_id' => $rwId,
                ]);
                $totalRt++;
            }
        }

        // ===========================================
        // OUTPUT SUMMARY
        // ===========================================
        $this->command->info('✅ Wilayah data seeded successfully!');
        $this->command->info('');
        $this->command->info('📍 KECAMATAN WONOCOLO SURABAYA:');
        $this->command->info('  - Kelurahan Bendul Merisi (12 RW, 58 RT - RW 03 hanya RT 1-4)');
        $this->command->info('  - Kelurahan Jemur Wonosari (10 RW, 63 RT)');
        $this->command->info('  - Kelurahan Margorejo (8 RW, 36 RT)');
        $this->command->info('  - Kelurahan Sidosermo (8 RW, 35 RT)');
        $this->command->info('  - Kelurahan Siwalankerto (6 RW, 40 RT)');
        $this->command->info('');
        $this->command->info('📊 STATISTICS:');
        $this->command->info('  - Total Kelurahan: ' . $totalKelurahan);
        $this->command->info('  - Total RW: ' . $totalRw);
        $this->command->info('  - Total RT: ' . $totalRt);
        $this->command->info('  - Total Wilayah: ' . ($totalKelurahan + $totalRw + $totalRt));
    }
}