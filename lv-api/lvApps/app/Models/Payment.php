<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $casts = [
        'user_id' => 'integer',
        'customer_id' => 'integer',
        'order_id' => 'integer',

        'bill_total' => 'double',
        'payment' => 'double',
        'due' => 'double',
        'items' => 'array',
    ];

    protected $fillable = [
        'payment_type',
        'payment_method',
        'customer_type',
        
        'bill_total',
        'payment',
        'due',
        'items',

        'order_id',
        'customer_id',
        'user_id'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
