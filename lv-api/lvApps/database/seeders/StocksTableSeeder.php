<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class StocksTableSeeder extends Seeder
{
    public function run()
    {
        $userId = DB::table('users')
            ->where('email', 'system@mail.com')
            ->value('id');

        DB::table('stocks')->upsert([
            [
                'id' => 29,
                'type' => 'IN',
                'quantity' => 1965,
                'purchase_price' => 0.000000000,
                'purchase_invoice' => null,
                'remarks' => null,
                'status' => 0,
                'product_id' => 4,
                'user_id' => $userId,
                'created_at' => Carbon::create(2024, 11, 10, 12, 40, 15),
                'updated_at' => Carbon::create(2024, 11, 10, 12, 40, 15),
            ],
            [
                'id' => 30,
                'type' => 'IN',
                'quantity' => 896,
                'purchase_price' => 0.000000000,
                'purchase_invoice' => null,
                'remarks' => null,
                'status' => 0,
                'product_id' => 5,
                'user_id' => $userId,
                'created_at' => Carbon::create(2024, 11, 10, 12, 43, 44),
                'updated_at' => Carbon::create(2024, 11, 10, 12, 43, 44),
            ],
            [
                'id' => 31,
                'type' => 'IN',
                'quantity' => 300,
                'purchase_price' => 0.000000000,
                'purchase_invoice' => '0',
                'remarks' => '--',
                'status' => 0,
                'product_id' => 6,
                'user_id' => $userId,
                'created_at' => Carbon::create(2024, 11, 14, 12, 58, 23),
                'updated_at' => Carbon::create(2024, 11, 14, 12, 58, 23),
            ],
        ], ['id'], [
            'type',
            'quantity',
            'purchase_price',
            'purchase_invoice',
            'remarks',
            'status',
            'product_id',
            'user_id',
            'updated_at',
        ]);
    }
}
