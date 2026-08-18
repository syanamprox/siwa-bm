<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KeluargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Keluarga data cleared temporarily - will be populated through keluarga input system
        $this->command->info('✅ KeluargaSeeder cleared - ready for manual input through keluarga system');
    }
}