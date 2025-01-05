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

        \DB::table('permissions')->insert(array(
            array(
                'id' => 1,
                'name' => 'create-user',
                'guard_name' => 'web',
                'created_at' => '2023-04-13 07:02:18',
                'updated_at' => '2023-04-13 07:02:18',
                'group' => 'User',
            ),
            array(
                'id' => 2,
                'name' => 'read-user',
                'guard_name' => 'web',
                'created_at' => '2023-04-13 07:02:27',
                'updated_at' => '2023-04-13 07:02:27',
                'group' => 'User',
            ),
            array(
                'id' => 3,
                'name' => 'update-user',
                'guard_name' => 'web',
                'created_at' => '2023-04-13 07:02:34',
                'updated_at' => '2023-04-13 07:02:34',
                'group' => 'User',
            ),
            array(
                'id' => 4,
                'name' => 'delete-user',
                'guard_name' => 'web',
                'created_at' => '2023-04-13 07:02:41',
                'updated_at' => '2023-04-13 07:02:41',
                'group' => 'User',
            ),
            array(
                'id' => 5,
                'name' => 'create-role',
                'guard_name' => 'web',
                'created_at' => '2023-04-13 07:02:50',
                'updated_at' => '2023-04-13 07:02:50',
                'group' => 'Role',
            ),
            array(
                'id' => 6,
                'name' => 'read-role',
                'guard_name' => 'web',
                'created_at' => '2023-04-13 07:02:55',
                'updated_at' => '2023-04-13 07:02:55',
                'group' => 'Role',
            ),
            array(
                'id' => 7,
                'name' => 'update-role',
                'guard_name' => 'web',
                'created_at' => '2023-04-13 07:03:28',
                'updated_at' => '2023-04-13 07:03:28',
                'group' => 'Role',
            ),
            array(
                'id' => 8,
                'name' => 'delete-role',
                'guard_name' => 'web',
                'created_at' => '2023-04-13 07:03:34',
                'updated_at' => '2023-04-13 07:03:34',
                'group' => 'Role',
            ),
            array(
                'id' => 9,
                'name' => 'create-permission',
                'guard_name' => 'web',
                'created_at' => '2023-04-13 07:04:19',
                'updated_at' => '2023-04-13 07:04:19',
                'group' => 'Permission',
            ),
            array(
                'id' => 10,
                'name' => 'read-permission',
                'guard_name' => 'web',
                'created_at' => '2023-04-13 07:04:27',
                'updated_at' => '2023-04-13 07:04:27',
                'group' => 'Permission',
            ),
            array(
                'id' => 11,
                'name' => 'update-permission',
                'guard_name' => 'web',
                'created_at' => '2023-04-13 07:04:36',
                'updated_at' => '2023-04-13 07:04:36',
                'group' => 'Permission',
            ),
            array(
                'id' => 12,
                'name' => 'delete-permission',
                'guard_name' => 'web',
                'created_at' => '2023-04-13 07:04:42',
                'updated_at' => '2023-04-13 07:04:42',
                'group' => 'Permission',
            ),
            array(
                'id' => 13,
                'name' => 'create-menu',
                'guard_name' => 'web',
                'created_at' => '2023-04-13 08:15:00',
                'updated_at' => '2023-04-13 08:15:00',
                'group' => 'Menu',
            ),
            array(
                'id' => 14,
                'name' => 'read-menu',
                'guard_name' => 'web',
                'created_at' => '2023-04-13 08:15:00',
                'updated_at' => '2023-04-13 08:15:00',
                'group' => 'Menu',
            ),
            array(
                'id' => 15,
                'name' => 'update-menu',
                'guard_name' => 'web',
                'created_at' => '2023-04-13 08:15:00',
                'updated_at' => '2023-04-13 08:15:00',
                'group' => 'Menu',
            ),
            array(
                'id' => 16,
                'name' => 'delete-menu',
                'guard_name' => 'web',
                'created_at' => '2023-04-13 08:15:00',
                'updated_at' => '2023-04-13 08:15:00',
                'group' => 'Menu',
            ),
            array(
                'id' => 17,
                'name' => 'create-setting',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 07:47:09',
                'updated_at' => '2023-04-24 07:47:09',
                'group' => 'Setting',
            ),
            array(
                'id' => 18,
                'name' => 'read-setting',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 07:47:20',
                'updated_at' => '2023-04-24 07:47:20',
                'group' => 'Setting',
            ),
            array(
                'id' => 19,
                'name' => 'update-setting',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 07:47:31',
                'updated_at' => '2023-04-24 07:47:31',
                'group' => 'Setting',
            ),
            array(
                'id' => 20,
                'name' => 'delete-setting',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 07:47:40',
                'updated_at' => '2023-04-24 07:47:40',
                'group' => 'Setting',
            ),
            array(
                'id' => 45,
                'name' => 'create-customer',
                'guard_name' => 'web',
                'created_at' => '2025-01-06 01:20:48',
                'updated_at' => '2025-01-06 01:20:48',
                'group' => 'Customer',
            ),
            array(
                'id' => 46,
                'name' => 'read-customer',
                'guard_name' => 'web',
                'created_at' => '2025-01-06 01:20:51',
                'updated_at' => '2025-01-06 01:20:51',
                'group' => 'Customer',
            ),
            array(
                'id' => 47,
                'name' => 'update-customer',
                'guard_name' => 'web',
                'created_at' => '2025-01-06 01:20:51',
                'updated_at' => '2025-01-06 01:20:51',
                'group' => 'Customer',
            ),
            array(
                'id' => 48,
                'name' => 'delete-customer',
                'guard_name' => 'web',
                'created_at' => '2025-01-06 01:20:51',
                'updated_at' => '2025-01-06 01:20:51',
                'group' => 'Customer',
            ),
        ));




    }
}
