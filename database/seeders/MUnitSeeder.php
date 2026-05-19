<?php

namespace Database\Seeders;

use App\Models\MUnit;
use Illuminate\Database\Seeder;

class MUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MUnit::create(['name' => 'Unit']);
        MUnit::create(['name' => 'Gram']);
        MUnit::create(['name' => 'Kilogram']);
        MUnit::create(['name' => 'Liter']);
        MUnit::create(['name' => 'Meter']);
        MUnit::create(['name' => 'Pack']);
        MUnit::create(['name' => 'Box']);
        MUnit::create(['name' => 'Pcs']);
    }
}