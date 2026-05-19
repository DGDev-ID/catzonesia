<?php

namespace Database\Seeders;

use App\Models\TransactionDetail;
use Illuminate\Database\Seeder;

class TransactionDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $details = [
            ['transaction_id' => 1, 'package_id' => null, 'product_id' => 1, 'amount' => 1, 'unit_id' => 1, 'price' => 450000, 'notes' => ''],
            ['transaction_id' => 2, 'package_id' => null, 'product_id' => 4, 'amount' => 2, 'unit_id' => 8, 'price' => 50000, 'notes' => ''],
            ['transaction_id' => 3, 'package_id' => 1, 'product_id' => null, 'amount' => 1, 'unit_id' => 1, 'price' => 300000, 'notes' => ''],
            ['transaction_id' => 4, 'package_id' => 3, 'product_id' => null, 'amount' => 1, 'unit_id' => 1, 'price' => 200000, 'notes' => ''],
            ['transaction_id' => 5, 'package_id' => null, 'product_id' => 3, 'amount' => 1, 'unit_id' => 1, 'price' => 150000, 'notes' => '']
        ];

        foreach ($details as $detail) {
            TransactionDetail::create($detail);
        }
    }
}