<?php

namespace Database\Seeders;

use App\Models\MPackage;
use Illuminate\Database\Seeder;

class MPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Basic Grooming Package',
                'price' => 300000,
                'is_grooming' => true,
                'description' => 'Basic grooming service including bath and brushing'
            ],
            [
                'name' => 'Premium Grooming Package',
                'price' => 500000,
                'is_grooming' => true,
                'description' => 'Premium grooming service including bath, brushing, and nail trimming'
            ],
            [
                'name' => 'Kitten Starter Pack',
                'price' => 200000,
                'is_grooming' => false,
                'description' => 'Starter pack for kittens including food and toys'
            ],
            [
                'name' => 'Health Maintenance Package',
                'price' => 400000,
                'is_grooming' => false,
                'description' => 'Health maintenance package including vitamins and supplements'
            ]
        ];

        foreach ($packages as $package) {
            MPackage::create($package);
        }
    }
}