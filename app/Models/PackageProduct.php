<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageProduct extends Model
{
    use HasFactory;

    protected $table = 'package_products';
    public $timestamps = false;
    protected $fillable = ['package_id', 'product_id'];

    // Relationships
    public function package()
    {
        return $this->belongsTo(MPackage::class, 'package_id');
    }

    public function product()
    {
        return $this->belongsTo(MProduct::class, 'product_id');
    }
}