<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productCategories = [
            ['product_id' => 1, 'category_id' => 1],
            ['product_id' => 1, 'category_id' => 7],
            ['product_id' => 2, 'category_id' => 2],
            ['product_id' => 2, 'category_id' => 7],
            ['product_id' => 3, 'category_id' => 4],
            ['product_id' => 4, 'category_id' => 5],
            ['product_id' => 5, 'category_id' => 6]
        ];

        foreach ($productCategories as $productCategory) {
            ProductCategory::create($productCategory);
        }
    }
}