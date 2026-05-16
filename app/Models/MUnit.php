<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MUnit extends Model
{
    use HasFactory;

    protected $table = 'm_units';
    protected $fillable = ['name'];

    // Relationships
    public function products()
    {
        return $this->hasMany(MProduct::class, 'base_unit_id');
    }

    public function productUnitConverters()
    {
        return $this->hasMany(ProductUnitConverter::class, 'unit_id');
    }

    public function productMovements()
    {
        return $this->hasMany(ProductMovement::class, 'unit_id');
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class, 'unit_id');
    }
}