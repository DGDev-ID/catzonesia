<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMovement extends Model
{
    use HasFactory;

    protected $table = 'product_movements';
    protected $fillable = [
        'product_id', 'type', 'quantity', 'unit_id', 
        'transaction_id', 'note', 'price'
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(MProduct::class, 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(MUnit::class, 'unit_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}