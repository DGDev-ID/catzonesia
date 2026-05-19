<?php

namespace Database\Seeders;

use App\Models\TransactionLog;
use Illuminate\Database\Seeder;

class TransactionLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $logs = [
            ['transaction_id' => 1, 'status' => 'pending', 'notes' => 'Transaction created'],
            ['transaction_id' => 1, 'status' => 'processing', 'notes' => 'Payment verified'],
            ['transaction_id' => 1, 'status' => 'completed', 'notes' => 'Product shipped'],
            ['transaction_id' => 3, 'status' => 'pending', 'notes' => 'Grooming appointment scheduled'],
            ['transaction_id' => 3, 'status' => 'completed', 'notes' => 'Grooming service completed'],
            ['transaction_id' => 4, 'status' => 'pending', 'notes' => 'Transaction created'],
            ['transaction_id' => 4, 'status' => 'cancelled', 'notes' => 'Customer cancelled'],
            ['transaction_id' => 5, 'status' => 'pending', 'notes' => 'Transaction created'],
            ['transaction_id' => 5, 'status' => 'completed', 'notes' => 'Product picked up']
        ];

        foreach ($logs as $log) {
            TransactionLog::create($log);
        }
    }
}