<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUnitConverter extends Model
{
    use HasFactory;

    protected $table = 'product_unit_converters';
    public $timestamps = false;
    protected $fillable = ['product_id', 'unit_id', 'multiplier'];

    // Relationships
    public function product()
    {
        return $this->belongsTo(MProduct::class, 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(MUnit::class, 'unit_id');
    }
}