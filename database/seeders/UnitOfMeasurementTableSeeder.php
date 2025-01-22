<?php



namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UnitOfMeasurementTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('unit_of_measurements')->delete();
        \DB::table('unit_of_measurements')->insert(array(
            array(
                'id' => 1,
                'name' => 'Kilogram',
                'code' => 'kg',
                'description' => 'Unit of weight measurement.',
                'type' => 'weight',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            array(
                'id' => 2,
                'name' => 'Liter',
                'code' => 'l',
                'description' => 'Unit of volume measurement.',
                'type' => 'volume',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            array(
                'id' => 3,
                'name' => 'Meter',
                'code' => 'm',
                'description' => 'Unit of length measurement.',
                'type' => 'length',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            array(
                'id' => 4,
                'name' => 'Square Meter',
                'code' => 'm2',
                'description' => 'Unit of area measurement.',
                'type' => 'area',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            array(
                'id' => 5,
                'name' => 'Hour',
                'code' => 'hr',
                'description' => 'Unit of time measurement.',
                'type' => 'time',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            array(
                'id' => 6,
                'name' => 'Piece',
                'code' => 'pcs',
                'description' => 'Unit of quantity measurement.',
                'type' => 'quantity',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            array(
                'id' => 7,
                'name' => 'Service',
                'code' => 'svc',
                'description' => 'Unit for service measurement.',
                'type' => 'service',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ),
        ));
    }
}
