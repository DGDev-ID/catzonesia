<?php

namespace Database\Seeders;

use App\Models\MCategory;
use Illuminate\Database\Seeder;

class MCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MCategory::create(['name' => 'Dry Food']);
        MCategory::create(['name' => 'Wet Food']);
        MCategory::create(['name' => 'Snacks']);
        MCategory::create(['name' => 'Litter']);
        MCategory::create(['name' => 'Toys']);
        MCategory::create(['name' => 'Grooming']);
        MCategory::create(['name' => 'Health']);
        MCategory::create(['name' => 'Accessories']);
    }
}