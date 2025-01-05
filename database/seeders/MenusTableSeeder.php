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
        \DB::table('menus')->insert(array(
            array(
                'id' => 1,
                'name' => 'Users',
                'url' => 'user',
                'permission_id' => 2,
                'icon' => 'fa-users',
                'main_menu' => 6,
                'sort' => 1,
                'created_at' => '2023-04-13 10:45:28',
                'updated_at' => '2023-06-08 06:17:37',
            ),
            array(
                'id' => 2,
                'name' => 'Role',
                'url' => 'role',
                'permission_id' => 6,
                'icon' => 'fa-users-gear',
                'main_menu' => 6,
                'sort' => 2,
                'created_at' => '2023-04-13 11:12:44',
                'updated_at' => '2023-06-08 06:17:42',
            ),
            array(
                'id' => 3,
                'name' => 'Permission',
                'url' => 'permission',
                'permission_id' => 10,
                'icon' => 'fa-lock',
                'main_menu' => 5,
                'sort' => 2,
                'created_at' => '2023-04-13 11:45:17',
                'updated_at' => '2024-07-11 22:41:24',
            ),
            array(
                'id' => 4,
                'name' => 'Menu',
                'url' => 'menu',
                'permission_id' => 14,
                'icon' => 'fa-list',
                'main_menu' => 5,
                'sort' => 1,
                'created_at' => '2023-04-13 11:43:45',
                'updated_at' => '2024-07-11 22:41:23',
            ),
            array(
                'id' => 5,
                'name' => 'General Setting',
                'url' => NULL,
                'permission_id' => NULL,
                'icon' => 'fa-gears',
                'main_menu' => NULL,
                'sort' => 4,
                'created_at' => '2023-04-24 07:49:08',
                'updated_at' => '2024-07-18 16:09:10',
            ),
            array(
                'id' => 6,
                'name' => 'Access Control',
                'url' => NULL,
                'permission_id' => NULL,
                'icon' => 'fa-universal-access',
                'main_menu' => NULL,
                'sort' => 1,
                'created_at' => '2023-04-25 09:45:47',
                'updated_at' => '2023-06-08 06:17:40',
            ),
            array(
                'id' => 7,
                'name' => 'App Setting',
                'url' => 'setting',
                'permission_id' => 18,
                'icon' => 'fa-cogs',
                'main_menu' => 5,
                'sort' => 3,
                'created_at' => '2023-05-23 10:08:35',
                'updated_at' => '2024-07-11 22:40:34',
            ),
            array(
                'id' => 8,
                'name' => 'Transaction',
                'url' => NULL,
                'permission_id' => NULL,
                'icon' => 'fa-comments-dollar',
                'main_menu' => NULL,
                'sort' => 2,
                'created_at' => '2024-07-15 01:44:02',
                'updated_at' => '2024-07-15 01:44:02',
            ),
            array(
                'id' => 9,
                'name' => 'Master Data',
                'url' => NULL,
                'permission_id' => NULL,
                'icon' => 'fa-table',
                'main_menu' => NULL,
                'sort' => 3,
                'created_at' => '2024-11-24 11:12:40',
                'updated_at' => '2024-11-24 11:12:40',
            ),
            array(
                'id' => 10,
                'name' => 'Item Type',
                'url' => 'item-type',
                'permission_id' => 22,
                'icon' => 'fa-tags',
                'main_menu' => 9,
                'sort' => 1,
                'created_at' => '2025-01-05 14:32:13',
                'updated_at' => '2025-01-05 14:32:13',
            ),
            array(
                'id' => 11,
                'name' => 'Item Category',
                'url' => 'item-category',
                'permission_id' => 26,
                'icon' => 'fa-layer-group',
                'main_menu' => 9,
                'sort' => 2,
                'created_at' => '2025-01-05 15:41:04',
                'updated_at' => '2025-01-05 15:41:04',
            ),
            array(
                'id' => 12,
                'name' => 'Unit of Measurement',
                'url' => 'unit-of-measurement',
                'permission_id' => 30,
                'icon' => 'fa-table',
                'main_menu' => 9,
                'sort' => 3,
                'created_at' => '2025-01-05 16:49:52',
                'updated_at' => '2025-01-05 16:49:52',
            ),
            array(
                'id' => 13,
                'name' => 'Items',
                'url' => 'item',
                'permission_id' => 34,
                'icon' => 'fa-database',
                'main_menu' => 9,
                'sort' => 4,
                'created_at' => '2025-01-05 19:15:09',
                'updated_at' => '2025-01-05 19:15:09',
            ),
        ));


    }
}
