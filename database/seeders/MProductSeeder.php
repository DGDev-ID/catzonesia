<?php

namespace Database\Seeders;

use App\Models\MProduct;
use Illuminate\Database\Seeder;

class MProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'base_unit_id' => 1,
                'name' => 'Royal Canin Persian 4kg',
                'sku' => 'RC-PERSIAN-4KG',
                'price' => 450000,
                'avg_inbound_price' => 400000,
                'stock' => 20,
                'stock_alert' => 5,
                'description' => 'Premium dry food for Persian cats',
                'img_url' => null,
                'is_display' => true
            ],
            [
                'base_unit_id' => 2,
                'name' => 'Whiskas Tuna Wet Food',
                'sku' => 'WHISKAS-TUNA-85G',
                'price' => 2500,
                'avg_inbound_price' => 2000,
                'stock' => 100,
                'stock_alert' => 20,
                'description' => 'Tuna flavor wet food for adult cats',
                'img_url' => null,
                'is_display' => true
            ],
            [
                'base_unit_id' => 1,
                'name' => 'Cat Litter Clumping 10kg',
                'sku' => 'LITTER-CLUMP-10KG',
                'price' => 150000,
                'avg_inbound_price' => 120000,
                'stock' => 30,
                'stock_alert' => 10,
                'description' => 'High-quality clumping cat litter',
                'img_url' => null,
                'is_display' => true
            ],
            [
                'base_unit_id' => 8,
                'name' => 'Interactive Cat Toy',
                'sku' => 'TOY-INTERACTIVE',
                'price' => 50000,
                'avg_inbound_price' => 40000,
                'stock' => 15,
                'stock_alert' => 5,
                'description' => 'Interactive laser pointer toy for cats',
                'img_url' => null,
                'is_display' => true
            ],
            [
                'base_unit_id' => 6,
                'name' => 'Cat Shampoo',
                'sku' => 'SHAMPOO-CAT',
                'price' => 35000,
                'avg_inbound_price' => 30000,
                'stock' => 25,
                'stock_alert' => 8,
                'description' => 'Gentle shampoo for cats',
                'img_url' => null,
                'is_display' => true
            ]
        ];

        foreach ($products as $product) {
            MProduct::create($product);
        }
    }
}