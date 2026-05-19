<?php

namespace Database\Seeders;

use App\Models\ProductMovement;
use Illuminate\Database\Seeder;

class ProductMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movements = [
            ['product_id' => 1, 'type' => 'inbound', 'quantity' => 10, 'unit_id' => 1, 'transaction_id' => null, 'note' => 'Initial stock', 'price' => 400000],
            ['product_id' => 2, 'type' => 'inbound', 'quantity' => 50, 'unit_id' => 2, 'transaction_id' => null, 'note' => 'Initial stock', 'price' => 2000],
            ['product_id' => 3, 'type' => 'inbound', 'quantity' => 20, 'unit_id' => 1, 'transaction_id' => null, 'note' => 'Initial stock', 'price' => 120000],
            ['product_id' => 4, 'type' => 'inbound', 'quantity' => 10, 'unit_id' => 8, 'transaction_id' => null, 'note' => 'Initial stock', 'price' => 40000],
            ['product_id' => 5, 'type' => 'inbound', 'quantity' => 15, 'unit_id' => 6, 'transaction_id' => null, 'note' => 'Initial stock', 'price' => 30000],
            ['product_id' => 1, 'type' => 'outbound', 'quantity' => 1, 'unit_id' => 1, 'transaction_id' => 1, 'note' => 'Sold to customer', 'price' => 450000],
            ['product_id' => 4, 'type' => 'outbound', 'quantity' => 2, 'unit_id' => 8, 'transaction_id' => 2, 'note' => 'Sold to customer', 'price' => 50000],
            ['product_id' => 3, 'type' => 'outbound', 'quantity' => 1, 'unit_id' => 1, 'transaction_id' => 5, 'note' => 'Sold to customer', 'price' => 150000]
        ];

        foreach ($movements as $movement) {
            ProductMovement::create($movement);
        }
    }
}