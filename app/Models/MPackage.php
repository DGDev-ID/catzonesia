<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MPackage extends Model
{
    use HasFactory;

    protected $table = 'm_package';
    protected $fillable = ['name', 'price', 'is_grooming', 'description'];

    // Relationships
    public function packageProducts()
    {
        return $this->hasMany(PackageProduct::class, 'package_id');
    }

    public function products()
    {
        return $this->belongsToMany(MProduct::class, 'package_products', 'package_id', 'product_id');
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class, 'package_id');
    }
}