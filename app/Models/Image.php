<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    public function product()
    {
        return $this->belongsToMany('App\Models\Product', 'product_image', 'image_id', 'product_id');
    }
}
