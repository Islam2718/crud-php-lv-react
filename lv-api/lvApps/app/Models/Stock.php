<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $casts = [
        'quantity' => 'integer',
        'product_id'=> 'integer',
        'user_id' => 'integer',
        'purchase_price' => 'double',
        'status' => 'integer',
    ];

    protected $fillable = [
        'type',
        'product_id',
        'quantity',
        'purchase_price',   
        'purchase_invoice',
        'remarks',
        'user_id',
        'status'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}