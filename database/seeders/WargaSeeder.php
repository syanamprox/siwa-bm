<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Warga data cleared temporarily - will be populated through keluarga input system
        $this->command->info('✅ WargaSeeder cleared - ready for manual input through keluarga system');
    }
}