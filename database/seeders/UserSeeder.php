<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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
        $password = Hash::make('password');
        User::insert([
            [
                'name' => 'Super',
                'username' => 'super',
                'email' => 'super@kreasisawalanusantara.com',
                'password' => $password,
            ],
            [
                'name' => 'Admin',
                'username' => 'admin',
                'email' => 'admin@kreasisawalanusantara.com',
                'password' => $password,
            ],
            [
                'name' => 'Operator',
                'username' => 'operator',
                'email' => 'operator@kreasisawalanusantara.com',
                'password' => $password,
            ],
            [
                'name' => 'Engineer Staff',
                'username' => 'engineer.staff',
                'email' => 'engineer.staff@gmail.com',
                'password' => $password,
            ],
            [
                'name' => 'Marketing Staff',
                'username' => 'marketing.staff',
                'email' => 'marketing.staff@gmail.com',
                'password' => $password,
            ],
            [
                'name' => 'Warehouse Staff',
                'username' => 'warehouse.staff',
                'email' => 'warehouse.staff@gmail.com',
                'password' => $password,
            ],
        ]);

        $superAdmin = User::find(1);
        $admin = User::find(2);
        $operator = User::find(3);

        $superAdmin->assignRole('Super Admin');
        $admin->assignRole('Admin');
    }
}
