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
        $users = [
            ['name' => 'Administrator', 'username' => 'admin', 'password' => 'admin123', 'role' => 'admin'],
            ['name' => 'RULLY PRASETYA NEGARA, S.STP.,M.Si', 'username' => 'lurah', 'password' => 'lurah123', 'role' => 'lurah'],
            ['name' => 'BAMBANG SETYAWAN', 'username' => 'rw03', 'password' => 'rw123', 'role' => 'rw'],
            ['name' => 'TRI BAGUS WAHYUDI', 'username' => 'rt01', 'password' => 'rt123', 'role' => 'rt'],
            ['name' => 'AKHMAD SURYADI', 'username' => 'rt02', 'password' => 'rt123', 'role' => 'rt'],
            ['name' => 'M. YASIN', 'username' => 'rt03', 'password' => 'rt123', 'role' => 'rt'],
            ['name' => 'SULICHAH', 'username' => 'rt04', 'password' => 'rt123', 'role' => 'rt'],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['username' => $u['username']],
                [
                    'name' => $u['name'],
                    'password' => bcrypt($u['password']),
                    'role' => $u['role'],
                    'status_aktif' => 1,
                ]
            );
        }
    }
}
