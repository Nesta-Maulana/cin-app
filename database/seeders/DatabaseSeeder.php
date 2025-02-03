<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(MenusTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(RoleHasPermissionsTableSeeder::class);
        $this->call(ItemTypeSeeder::class);
        $this->call(ItemCategoriesTableSeeder::class);
        $this->call(UnitOfMeasurementTableSeeder::class);
        // $this->call(ItemsRelatedSeeder::class);

    }
}
