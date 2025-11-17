<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        User::create([
            'username' => 'admin',
            'name' => 'Administrator',
            'email' => 'admin@siwa.test',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'status_aktif' => true,
        ]);

        // Create default lurah user
        User::create([
            'username' => 'lurah',
            'name' => 'Budi Santoso, S.IP',
            'email' => 'lurah@siwa.test',
            'password' => bcrypt('lurah123'),
            'role' => 'lurah',
            'status_aktif' => true,
        ]);

        // Create default RW user
        User::create([
            'username' => 'rw01',
            'name' => 'Ahmad Fadli',
            'email' => 'rw01@siwa.test',
            'password' => bcrypt('rw123'),
            'role' => 'rw',
            'status_aktif' => true,
        ]);

        // Create default RT user
        User::create([
            'username' => 'rt01',
            'name' => 'Siti Nurhaliza',
            'email' => 'rt01@siwa.test',
            'password' => bcrypt('rt123'),
            'role' => 'rt',
            'status_aktif' => true,
        ]);
    }
}