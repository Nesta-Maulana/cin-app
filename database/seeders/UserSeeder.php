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
                'name' => 'Super',
                'username' => 'super',
                'email' => 'super@shofyan.my.id',
                'password' => '$2y$10$$2y$10$.GKkvpnuCU9bci0//4P8/./yKIsFxDhxyZziA23QOHzRC6/gemSQ6',
            ],
            [
                'name' => 'Admin',
                'username' => 'admin',
                'email' => 'admin@shofyan.my.id',
                'password' => '$2y$10$$2y$10$.GKkvpnuCU9bci0//4P8/./yKIsFxDhxyZziA23QOHzRC6/gemSQ6',
            ],
            [
                'name' => 'Operator',
                'username' => 'operator',
                'email' => 'operator@shofyan.my.id',
                'password' => '$2y$10$$2y$10$.GKkvpnuCU9bci0//4P8/./yKIsFxDhxyZziA23QOHzRC6/gemSQ6',
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
