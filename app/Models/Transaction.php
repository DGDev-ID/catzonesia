<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';
    protected $fillable = ['unique_id', 'cust_email', 'price', 'fee', 'total_price', 'status', 'profit_margin'];

    protected static function booted()
    {
        static::creating(function ($transaction) {

            if ($transaction->unique_id) {
                return;
            }

            do {
                $uniqueId =
                    'TRX-' .
                    now()->format('Ymd') .
                    '-' .
                    strtoupper(substr(uniqid(), -6));

            } while (
                self::where('unique_id', $uniqueId)->exists()
            );

            $transaction->unique_id = $uniqueId;
        });
    }

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