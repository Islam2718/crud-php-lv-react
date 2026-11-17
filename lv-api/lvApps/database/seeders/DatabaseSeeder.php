<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::firstOrCreate([
            'email' => 'system@mail.com',
        ], [
            'name' => 'Super Admin',
            'phone' => '+8801710001337',
            'type' => 'SUPERADMIN',
            'password' => Hash::make('123456'),
        ]);      

        $this->call([
            ProductsTableSeeder::class,
            StocksTableSeeder::class,
        ]);
    }
}
