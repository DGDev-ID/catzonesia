<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';
    protected $fillable = ['price', 'fee', 'total_price', 'status'];

    // Relationships
    public function productMovements()
    {
        return $this->hasMany(ProductMovement::class, 'transaction_id');
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_id');
    }

    public function transactionGrooming()
    {
        return $this->hasOne(TransactionGrooming::class, 'transaction_id');
    }

    public function transactionLogs()
    {
        return $this->hasMany(TransactionLog::class, 'transaction_id');
    }
}