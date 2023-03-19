<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsToMany('App\Models\Category', 'category_product', 'product_id', 'category_id');
    }

    public function images()
    {
        return $this->belongsToMany('App\Models\Image', 'product_image', 'product_id', 'image_id');
    }
}
