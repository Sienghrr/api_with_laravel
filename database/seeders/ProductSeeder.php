<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('product')->truncate();
        DB::table('product')->insert([
                [
                    'product_name' => 'Coca',
                    'cost' => '0.25',
                    'price' => '0.5',
                    'category_id' => '1',
                ],
                [
                    'product_name' => 'មីខ្លាឃ្មុំ',
                    'cost' => '0.10',
                    'price' => '0.25',
                    'category_id' => '2',
                ]
            ]
        );
        //
    }
}