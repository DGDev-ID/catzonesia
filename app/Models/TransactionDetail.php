<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;

    protected $table = 'transaction_details';
    protected $fillable = [
        'transaction_id', 'package_id', 'product_id', 'amount', 'unit_id', 
        'price', 'notes'
    ];

    // Relationships
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function package()
    {
        return $this->belongsTo(MPackage::class, 'package_id');
    }

    public function product()
    {
        return $this->belongsTo(MProduct::class, 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(MUnit::class, 'unit_id');
    }
}