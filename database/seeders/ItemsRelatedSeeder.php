<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ItemsRelatedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed `items` table
        \DB::table('items')->delete();
        \DB::table('items')->insert(array_merge(
            $this->getItemsForType(1, [
                'Concrete Mixers',
                'White Cement',
                'Rapid Hardening Cement',
                'Low Heat Cement',
                'Blast Furnace Slag Cement',
                'Sulphate Resisting Cement',
                'High Alumina Cement',
                'Hydraulic Cement'
            ], 'High-quality cement for construction.', 'Packed in 50kg bags.'),

            $this->getItemsForType(3, [
                'Clay Roof Tiles',
                'Concrete Roof Tile',
                'Asphalt Shingle',
                'Metal Roofing Sheet',
                'Plastic Roofing Sheet',
                'Slate Roof Tile',
                'Green Roof System',
                'Solar Roofing Tile'
            ], 'Durable roofing materials.', 'High-quality clay tiles.')
        ));

        // Seed `item_uoms` table
        \DB::table('item_uoms')->delete();
        \DB::table('item_uoms')->insert(array_merge(
            $this->getItemUOMs(1, 8, 3),
            $this->getItemUOMs(9, 8, 3)
        ));

        // Seed `item_price_histories` table
        \DB::table('item_price_histories')->delete();
        \DB::table('item_price_histories')->insert(array_merge(
            $this->getItemPriceHistories(1, 24, 50000, 40000),
            $this->getItemPriceHistories(25, 24, 10000, 8000)
        ));
    }

    private function getItemsForType($typeId, $names, $description, $specification)
    {
        $items = [];
        foreach ($names as $index => $name) {
            $items[] = [
                'id' => ($typeId - 1) * 8 + $index + 1, // ID tetap unik
                'name' => $name,
                'sku' => "$name-$typeId",
                'barcode' => str_pad((($typeId - 1) * 8 + $index + 1), 13, '0', STR_PAD_LEFT),
                'item_type_id' => $typeId, // typeId valid (1-8)
                'item_category_id' => $index + 1, // Pastikan id kategori sesuai (1-8)
                'unit_of_measurement_id' => ($typeId === 1) ? 1 : 6, // Pilih UOM berdasarkan tipe
                'description' => $description,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'spesification' => $specification,
            ];
        }
        return $items;
    }

    private function getItemUOMs($itemIdStart, $itemCount, $uomCount)
    {
        $uoms = [];
        for ($i = $itemIdStart; $i < $itemIdStart + $itemCount; $i++) {
            for ($j = 1; $j <= $uomCount; $j++) {
                $uoms[] = [
                    'id' => (($i - $itemIdStart) * $uomCount) + $j,
                    'item_id' => $i,
                    'unit_of_measurement_id' => ($j === 1) ? 1 : (($j === 2) ? 6 : 7),
                    'conversion' => $j * 0.5,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        return $uoms;
    }

    private function getItemPriceHistories($itemIdStart, $historyCount, $price, $cost)
    {
        $histories = [];
        for ($i = $itemIdStart; $i < $itemIdStart + $historyCount; $i++) {
            for ($j = 1; $j <= 3; $j++) {
                $histories[] = [
                    'id' => (($i - $itemIdStart) * 3) + $j,
                    'item_uom_id' => (($i - $itemIdStart) * 3) + $j,
                    'price' => $price * $j,
                    'cost' => $cost * $j,
                    'currency' => 'IDR',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        return $histories;
    }
}
