<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ItemCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('item_categories')->delete();
        \DB::table('item_categories')->insert(array(
            array(
                'id' => 1,
                'name' => 'Concrete Mixers',
                'description' => 'Equipment used for mixing concrete efficiently.',
                'item_type_id' => 1, // Assuming item_type_id 1 refers to Cement
                'parent_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            array(
                'id' => 2,
                'name' => 'Red Bricks',
                'description' => 'Traditional red clay bricks.',
                'item_type_id' => 2, // Assuming item_type_id 2 refers to Bricks
                'parent_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            array(
                'id' => 3,
                'name' => 'Concrete Blocks',
                'description' => 'Large concrete blocks for heavy construction.',
                'item_type_id' => 2, // Bricks category
                'parent_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            array(
                'id' => 4,
                'name' => 'Clay Roof Tiles',
                'description' => 'Durable roof tiles made of clay.',
                'item_type_id' => 3, // Assuming item_type_id 3 refers to Roofing Materials
                'parent_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            array(
                'id' => 5,
                'name' => 'PVC Pipes',
                'description' => 'Lightweight and durable pipes for plumbing.',
                'item_type_id' => 4, // Assuming item_type_id 4 refers to Pipes and Fittings
                'parent_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            array(
                'id' => 6,
                'name' => 'Metal Fittings',
                'description' => 'Durable metal fittings for industrial use.',
                'item_type_id' => 4, // Pipes and Fittings
                'parent_id' => 5, // Child of PVC Pipes
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            array(
                'id' => 7,
                'name' => 'Interior Paint',
                'description' => 'Paints specifically for interior walls and surfaces.',
                'item_type_id' => 5, // Assuming item_type_id 5 refers to Paints and Coatings
                'parent_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            array(
                'id' => 8,
                'name' => 'Exterior Paint',
                'description' => 'Weather-resistant paints for exterior use.',
                'item_type_id' => 5, // Paints and Coatings
                'parent_id' => 7, // Child of Interior Paint
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
        ));
    }
}
