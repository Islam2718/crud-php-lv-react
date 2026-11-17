<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('products')->upsert([
            [
                'id' => 4,
                'name' => 'Space OF Helmate',
                'description' => '--',
                'thumb' => null,
                'buy_price' => 0.000000000,
                'sell_price' => 0.000000000,
                'unit' => 'pcs',
                'alert_quantity' => '10',
                'created_at' => Carbon::create(2024, 11, 10, 12, 33, 14),
                'updated_at' => Carbon::create(2024, 11, 10, 12, 33, 14),
            ],
            [
                'id' => 5,
                'name' => 'Space FF Helmate',
                'description' => '--',
                'thumb' => null,
                'buy_price' => 0.000000000,
                'sell_price' => 0.000000000,
                'unit' => 'pcs',
                'alert_quantity' => '10',
                'created_at' => Carbon::create(2024, 11, 10, 12, 35, 11),
                'updated_at' => Carbon::create(2024, 11, 10, 12, 35, 11),
            ],
            [
                'id' => 6,
                'name' => 'FogLight - LED',
                'description' => '--',
                'thumb' => null,
                'buy_price' => 100.000000000,
                'sell_price' => 150.000000000,
                'unit' => 'pcs',
                'alert_quantity' => '10',
                'created_at' => Carbon::create(2024, 11, 14, 12, 54, 42),
                'updated_at' => Carbon::create(2024, 11, 14, 12, 54, 42),
            ],
        ], ['id'], [
            'name',
            'description',
            'thumb',
            'buy_price',
            'sell_price',
            'unit',
            'alert_quantity',
            'updated_at',
        ]);
    }
}
