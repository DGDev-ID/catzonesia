<?php

namespace Database\Seeders;

use App\Models\TransactionGrooming;
use Illuminate\Database\Seeder;

class TransactionGroomingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groomings = [
            [
                'transaction_id' => 3,
                'owner_name' => 'John Doe',
                'owner_phone' => '081234567890',
                'cat_name' => 'Milo',
                'cat_age' => 3,
                'cat_estimated_weight' => 4.5,
                'notes' => 'Milo has sensitive skin, please use hypoallergenic shampoo'
            ],
            [
                'transaction_id' => 4,
                'owner_name' => 'Jane Smith',
                'owner_phone' => '089876543210',
                'cat_name' => 'Luna',
                'cat_age' => 2,
                'cat_estimated_weight' => 3.2,
                'notes' => 'Luna is very shy, please handle with care'
            ]
        ];

        foreach ($groomings as $grooming) {
            TransactionGrooming::create($grooming);
        }
    }
}