<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ItemTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('item_types')->delete();
        \DB::table('item_types')->insert(array(
            array(
                'id' => 1,
                'name' => 'Cement',
                'description' => 'Various types of cement for construction purposes.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            array(
                'id' => 2,
                'name' => 'Bricks',
                'description' => 'Different types of bricks including red bricks and concrete blocks.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            array(
                'id' => 3,
                'name' => 'Roofing Materials',
                'description' => 'Materials for roofing such as tiles, sheets, and insulation.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            array(
                'id' => 4,
                'name' => 'Pipes and Fittings',
                'description' => 'PVC pipes, metal pipes, and various fittings for plumbing.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            array(
                'id' => 5,
                'name' => 'Paints and Coatings',
                'description' => 'Interior and exterior paints, primers, and protective coatings.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            array(
                'id' => 6,
                'name' => 'Electrical Materials',
                'description' => 'Wires, switches, and other electrical installation materials.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            array(
                'id' => 7,
                'name' => 'Tools',
                'description' => 'Hand tools and power tools for construction and repair work.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            array(
                'id' => 8,
                'name' => 'Flooring Materials',
                'description' => 'Tiles, wood planks, and other flooring solutions.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
        ));
    }
}
