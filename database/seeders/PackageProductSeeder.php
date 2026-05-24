<?php

namespace Database\Seeders;

use App\Models\PackageProduct;
use Illuminate\Database\Seeder;

class PackageProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packageProducts = [
            ['package_id' => 3, 'product_id' => 1, 'quantity' => 1, 'unit_id' => 1], // Kitten Starter Pack - Royal Canin Persian 4kg (1 unit)
            ['package_id' => 3, 'product_id' => 4, 'quantity' => 2, 'unit_id' => 8], // Kitten Starter Pack - Interactive Cat Toy (2 pcs)
            ['package_id' => 4, 'product_id' => 1, 'quantity' => 1, 'unit_id' => 1], // Health Maintenance Package - Royal Canin Persian 4kg (1 unit)
            ['package_id' => 4, 'product_id' => 5, 'quantity' => 1, 'unit_id' => 6]  // Health Maintenance Package - Cat Shampoo (1 pack)
        ];

        foreach ($packageProducts as $packageProduct) {
            PackageProduct::create($packageProduct);
        }
    }
}