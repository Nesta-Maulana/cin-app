<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MenusTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('menus')->delete();

        \DB::table('menus')->insert(array (
            0 =>
            array (
                'created_at' => '2023-04-13 10:45:28',
                'icon' => NULL,
                'id' => 1,
                'main_menu' => 6,
                'name' => 'Administrator',
                'permission_id' => 2,
                'sort' => 1,
                'updated_at' => '2023-06-08 06:17:37',
                'url' => 'admin/user',
            ),
            1 =>
            array (
                'created_at' => '2023-04-13 11:12:44',
                'icon' => NULL,
                'id' => 2,
                'main_menu' => 5,
                'name' => 'Role',
                'permission_id' => 6,
                'sort' => 2,
                'updated_at' => '2023-06-08 06:17:42',
                'url' => 'admin/role',
            ),
            2 =>
            array (
                'created_at' => '2023-04-13 11:45:17',
                'icon' => NULL,
                'id' => 3,
                'main_menu' => 5,
                'name' => 'Permission',
                'permission_id' => 10,
                'sort' => 3,
                'updated_at' => '2023-06-08 06:17:06',
                'url' => 'admin/permission',
            ),
            3 =>
            array (
                'created_at' => '2023-04-13 11:43:45',
                'icon' => NULL,
                'id' => 4,
                'main_menu' => 5,
                'name' => 'Menu',
                'permission_id' => 14,
                'sort' => 4,
                'updated_at' => '2023-06-08 06:17:13',
                'url' => 'admin/menu',
            ),
            4 =>
            array (
                'created_at' => '2023-04-24 07:49:08',
                'icon' => 'adjustments-horizontal',
                'id' => 5,
                'main_menu' => NULL,
                'name' => 'Setting',
                'permission_id' => NULL,
                'sort' => 2,
                'updated_at' => '2023-06-08 06:17:40',
                'url' => NULL,
            ),
            5 =>
            array (
                'created_at' => '2023-04-25 09:45:47',
                'icon' => 'users',
                'id' => 6,
                'main_menu' => NULL,
                'name' => 'Users',
                'permission_id' => NULL,
                'sort' => 1,
                'updated_at' => '2023-06-08 06:17:40',
                'url' => NULL,
            ),
            6 =>
            array (
                'created_at' => '2023-05-23 10:08:35',
                'icon' => NULL,
                'id' => 7,
                'main_menu' => 5,
                'name' => 'App',
                'permission_id' => 18,
                'sort' => 1,
                'updated_at' => '2023-06-08 06:17:42',
                'url' => 'admin/setting',
            ),
        ));


    }
}
