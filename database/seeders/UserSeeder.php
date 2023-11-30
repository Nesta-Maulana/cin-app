<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            [
                'name' => 'Webdev',
                'username' => 'webdev',
                'email' => 'webdev@shofyan.my.id',
                'password' => '$2y$10$kpfYig8ZOtKlXnc6dmCR9O4HTifCsBT4ETHtgPWTcQKQWCn2I8p1C',
            ],
            [
                'name' => 'Admin',
                'username' => 'admin',
                'email' => 'admin@shofyan.my.id',
                'password' => '$2y$10$ofZbNRKN3e49KOEcIFwWm.eZbJb2Zp3ZCtGUFxNFg0W34oRQNlbYq',
            ],
            [
                'name' => 'Operator',
                'username' => 'operator',
                'email' => 'operator@shofyan.my.id',
                'password' => '$2y$10$ofZbNRKN3e49KOEcIFwWm.eZbJb2Zp3ZCtGUFxNFg0W34oRQNlbYq',
            ],
        ]);

        $superAdmin = User::find(1);
        $admin = User::find(2);
        $operator = User::find(3);

        $superAdmin->assignRole('Super Admin');
        $admin->assignRole('Admin');
        $operator->assignRole('Operator');
    }
}
