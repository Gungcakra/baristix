<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_category_id',
        'name',
        'price',
        'file_path',
    ];

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
