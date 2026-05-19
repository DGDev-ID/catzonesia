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
            ['package_id' => 3, 'product_id' => 1],
            ['package_id' => 3, 'product_id' => 4],
            ['package_id' => 4, 'product_id' => 1],
            ['package_id' => 4, 'product_id' => 5]
        ];

        foreach ($packageProducts as $packageProduct) {
            PackageProduct::create($packageProduct);
        }
    }
}