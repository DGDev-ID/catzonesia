<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MCategory extends Model
{
    use HasFactory;

    protected $table = 'm_categories';
    protected $fillable = ['name'];

    // Relationships
    public function productCategories()
    {
        return $this->hasMany(ProductCategory::class, 'category_id');
    }

    public function products()
    {
        return $this->belongsToMany(MProduct::class, 'product_categories', 'category_id', 'product_id');
    }
}