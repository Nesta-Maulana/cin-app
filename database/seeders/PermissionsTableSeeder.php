<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('permissions')->delete();
        
        \DB::table('permissions')->insert(array (
            0 => 
            array (
                'created_at' => '2023-04-13 07:02:18',
                'group' => 'User',
                'guard_name' => 'web',
                'id' => 1,
                'name' => 'create-user-admin',
                'updated_at' => '2023-04-13 07:02:18',
            ),
            1 => 
            array (
                'created_at' => '2023-04-13 07:02:27',
                'group' => 'User',
                'guard_name' => 'web',
                'id' => 2,
                'name' => 'read-user-admin',
                'updated_at' => '2023-04-13 07:02:27',
            ),
            2 => 
            array (
                'created_at' => '2023-04-13 07:02:34',
                'group' => 'User',
                'guard_name' => 'web',
                'id' => 3,
                'name' => 'update-user-admin',
                'updated_at' => '2023-04-13 07:02:34',
            ),
            3 => 
            array (
                'created_at' => '2023-04-13 07:02:41',
                'group' => 'User',
                'guard_name' => 'web',
                'id' => 4,
                'name' => 'delete-user-admin',
                'updated_at' => '2023-04-13 07:02:41',
            ),
            4 => 
            array (
                'created_at' => '2023-04-13 07:02:50',
                'group' => 'Role',
                'guard_name' => 'web',
                'id' => 5,
                'name' => 'create-role',
                'updated_at' => '2023-04-13 07:02:50',
            ),
            5 => 
            array (
                'created_at' => '2023-04-13 07:02:55',
                'group' => 'Role',
                'guard_name' => 'web',
                'id' => 6,
                'name' => 'read-role',
                'updated_at' => '2023-04-13 07:02:55',
            ),
            6 => 
            array (
                'created_at' => '2023-04-13 07:03:28',
                'group' => 'Role',
                'guard_name' => 'web',
                'id' => 7,
                'name' => 'update-role',
                'updated_at' => '2023-04-13 07:03:28',
            ),
            7 => 
            array (
                'created_at' => '2023-04-13 07:03:34',
                'group' => 'Role',
                'guard_name' => 'web',
                'id' => 8,
                'name' => 'delete-role',
                'updated_at' => '2023-04-13 07:03:34',
            ),
            8 => 
            array (
                'created_at' => '2023-04-13 07:04:19',
                'group' => 'Permission',
                'guard_name' => 'web',
                'id' => 9,
                'name' => 'create-permission',
                'updated_at' => '2023-04-13 07:04:19',
            ),
            9 => 
            array (
                'created_at' => '2023-04-13 07:04:27',
                'group' => 'Permission',
                'guard_name' => 'web',
                'id' => 10,
                'name' => 'read-permission',
                'updated_at' => '2023-04-13 07:04:27',
            ),
            10 => 
            array (
                'created_at' => '2023-04-13 07:04:36',
                'group' => 'Permission',
                'guard_name' => 'web',
                'id' => 11,
                'name' => 'update-permission',
                'updated_at' => '2023-04-13 07:04:36',
            ),
            11 => 
            array (
                'created_at' => '2023-04-13 07:04:42',
                'group' => 'Permission',
                'guard_name' => 'web',
                'id' => 12,
                'name' => 'delete-permission',
                'updated_at' => '2023-04-13 07:04:42',
            ),
            12 => 
            array (
                'created_at' => '2023-04-13 08:15:00',
                'group' => 'Menu',
                'guard_name' => 'web',
                'id' => 13,
                'name' => 'create-menu',
                'updated_at' => '2023-04-13 08:15:00',
            ),
            13 => 
            array (
                'created_at' => '2023-04-13 08:15:00',
                'group' => 'Menu',
                'guard_name' => 'web',
                'id' => 14,
                'name' => 'read-menu',
                'updated_at' => '2023-04-13 08:15:00',
            ),
            14 => 
            array (
                'created_at' => '2023-04-13 08:15:00',
                'group' => 'Menu',
                'guard_name' => 'web',
                'id' => 15,
                'name' => 'update-menu',
                'updated_at' => '2023-04-13 08:15:00',
            ),
            15 => 
            array (
                'created_at' => '2023-04-13 08:15:00',
                'group' => 'Menu',
                'guard_name' => 'web',
                'id' => 16,
                'name' => 'delete-menu',
                'updated_at' => '2023-04-13 08:15:00',
            ),
            16 => 
            array (
                'created_at' => '2023-04-24 07:47:09',
                'group' => 'Setting',
                'guard_name' => 'web',
                'id' => 17,
                'name' => 'create-setting',
                'updated_at' => '2023-04-24 07:47:09',
            ),
            17 => 
            array (
                'created_at' => '2023-04-24 07:47:20',
                'group' => 'Setting',
                'guard_name' => 'web',
                'id' => 18,
                'name' => 'read-setting',
                'updated_at' => '2023-04-24 07:47:20',
            ),
            18 => 
            array (
                'created_at' => '2023-04-24 07:47:31',
                'group' => 'Setting',
                'guard_name' => 'web',
                'id' => 19,
                'name' => 'update-setting',
                'updated_at' => '2023-04-24 07:47:31',
            ),
            19 => 
            array (
                'created_at' => '2023-04-24 07:47:40',
                'group' => 'Setting',
                'guard_name' => 'web',
                'id' => 20,
                'name' => 'delete-setting',
                'updated_at' => '2023-04-24 07:47:40',
            ),
        ));
        
        
    }
}