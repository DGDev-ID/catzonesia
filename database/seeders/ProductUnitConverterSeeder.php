<?php

namespace Database\Seeders;

use App\Models\ProductUnitConverter;
use Illuminate\Database\Seeder;

class ProductUnitConverterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $converters = [
            ['product_id' => 1, 'unit_from_id' => 1, 'unit_to_id' => 3, 'multiplier' => 0.001],
            ['product_id' => 2, 'unit_from_id' => 2, 'unit_to_id' => 3, 'multiplier' => 0.085],
            ['product_id' => 3, 'unit_from_id' => 1, 'unit_to_id' => 3, 'multiplier' => 10],
            ['product_id' => 3, 'unit_from_id' => 1, 'unit_to_id' => 2, 'multiplier' => 10000]
        ];

        foreach ($converters as $converter) {
            ProductUnitConverter::create($converter);
        }
    }
}
