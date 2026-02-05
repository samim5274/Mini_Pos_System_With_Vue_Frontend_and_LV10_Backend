<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'sku', 'price', 'stock_quantity', 'min_stock', 'image'];

    public function cart() {
        return $this->hasMany(Cart::class,'product_id');
    }
}
