<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function product()
    {
//        return $this->hasMany(Product::class, 'id', 'product_id');
        return $this->hasOne(Product::class, 'id', 'product_id');

    }
}
