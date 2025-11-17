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
            'name' => 'Administrator',
            'email' => 'admin@siwa.test',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        // Create default lurah user
        User::create([
            'name' => 'RULLY PRASETYA NEGARA, S.STP.,M.Si',
            'email' => 'lurah@siwa.test',
            'password' => bcrypt('lurah123'),
            'role' => 'lurah',
        ]);

        // Create default RW III user
        User::create([
            'name' => 'BAMBANG SETYAWAN',
            'email' => 'rw03@siwa.test',
            'password' => bcrypt('rw123'),
            'role' => 'rw',
        ]);

        // Create default RT users
        User::create([
            'name' => 'TRI BAGUS WAHYUDI',
            'email' => 'rt01@siwa.test',
            'password' => bcrypt('rt123'),
            'role' => 'rt',
        ]);

        User::create([
            'name' => 'AKHMAD SURYADI',
            'email' => 'rt02@siwa.test',
            'password' => bcrypt('rt123'),
            'role' => 'rt',
        ]);

        User::create([
            'name' => 'M. YASIN',
            'email' => 'rt03@siwa.test',
            'password' => bcrypt('rt123'),
            'role' => 'rt',
        ]);

        User::create([
            'name' => 'SULICHAH',
            'email' => 'rt04@siwa.test',
            'password' => bcrypt('rt123'),
            'role' => 'rt',
        ]);
    }
}