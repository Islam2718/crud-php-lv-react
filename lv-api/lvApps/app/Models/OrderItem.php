<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $casts = [
        'buy_price' => 'double',
        'sell_price' => 'double',
        'discount' => 'double',
        'total' => 'double',
        'quantity' => 'integer',
        'order_id' => 'integer',
        'product_id' => 'integer',
        'user_id' => 'integer',
    ];

    protected $fillable = [                
        'buy_price',
        'sell_price',
        'quantity',
        'discount',
        'total',
        'order_id',
        'product_id',
        'user_id',
    ];

    // public function stocks()
    // {
    //     return $this->hasMany(Stock::class);
    // }
}
