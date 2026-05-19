<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transactions = [
            ['cust_email' => 'customer1@example.com', 'price' => 450000, 'fee' => 20000, 'total_price' => 470000, 'status' => 'completed', 'profit_margin' => 0.15],
            ['cust_email' => 'customer2@example.com', 'price' => 50000, 'fee' => 2000, 'total_price' => 52000, 'status' => 'pending', 'profit_margin' => 0.2],
            ['cust_email' => 'customer3@example.com', 'price' => 300000, 'fee' => 15000, 'total_price' => 315000, 'status' => 'completed', 'profit_margin' => 0.3],
            ['cust_email' => 'customer4@example.com', 'price' => 200000, 'fee' => 10000, 'total_price' => 210000, 'status' => 'cancelled', 'profit_margin' => 0.25],
            ['cust_email' => 'customer5@example.com', 'price' => 150000, 'fee' => 7500, 'total_price' => 157500, 'status' => 'completed', 'profit_margin' => 0.2]
        ];

        foreach ($transactions as $transaction) {
            Transaction::create($transaction);
        }
    }
}