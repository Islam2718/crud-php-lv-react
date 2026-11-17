<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $casts = [
        'buy_price' => 'float',
        'sell_price' => 'float',
    ];

    protected $fillable = [        
        'name',
        'description',
        'thumb',
        
        'buy_price',
        'sell_price',
        'unit',
        'alert_quantity',
    ];  

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

}
