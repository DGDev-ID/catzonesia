<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionGrooming extends Model
{
    use HasFactory;

    protected $table = 'transaction_groomings';
    protected $fillable = [
        'transaction_id', 'owner_name', 'owner_phone', 'cat_name', 'cat_age', 
        'cat_estimated_weight', 'notes'
    ];

    // Relationships
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}