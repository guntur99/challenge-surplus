<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public function getEnableAttribute($value)
    {
        switch ($value) {
            case 1:
                return true;
                break;

            default:
                return false;
                break;
        }
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'category_product', 'category_id', 'product_id');
    }
}
