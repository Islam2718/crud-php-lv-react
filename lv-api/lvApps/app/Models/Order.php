<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $casts = [
        'total' => 'double',
        'discount' => 'double',
        'sub_total' => 'double',
        'payment' => 'double',
        'user_id' => 'integer',
    ];

    protected $fillable = [
        'invoice_no',
        'customer_type',
        'total',
        'discount',
        'sub_total',
        'payment',
        'user_id',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relationship to get the user who created the order
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}