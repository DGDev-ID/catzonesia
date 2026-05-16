<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MProduct extends Model
{
    use HasFactory;

    protected $table = 'm_products';
    protected $fillable = [
        'base_unit_id', 'name', 'sku', 'price', 'avg_inbound_price', 'stock', 
        'stock_alert', 'description', 'img_url', 'is_display'
    ];

    // Relationships
    public function baseUnit()
    {
        return $this->belongsTo(MUnit::class, 'base_unit_id');
    }

    public function productCategories()
    {
        return $this->hasMany(ProductCategory::class, 'product_id');
    }

    public function categories()
    {
        return $this->belongsToMany(MCategory::class, 'product_categories', 'product_id', 'category_id');
    }

    public function productUnitConverters()
    {
        return $this->hasMany(ProductUnitConverter::class, 'product_id');
    }

    public function productMovements()
    {
        return $this->hasMany(ProductMovement::class, 'product_id');
    }

    public function packageProducts()
    {
        return $this->hasMany(PackageProduct::class, 'product_id');
    }

    public function packages()
    {
        return $this->belongsToMany(MPackage::class, 'package_products', 'product_id', 'package_id');
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class, 'product_id');
    }
}