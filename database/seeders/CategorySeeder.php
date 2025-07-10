<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('category')->truncate();
        DB::table('category')->insert([
                [
                    'name' => 'Drink',
                    'description' => 'Drink',
                ],
                [
                    'name' => 'FastFood',
                    'description' => 'FastFood',
                ]
            ]
        );
        //
    }
}