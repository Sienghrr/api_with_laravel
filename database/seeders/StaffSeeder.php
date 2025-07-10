<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('staff')->truncate();
        DB::table('staff')->insert([
                [
                    'position_id' => '1',
                    'name' => 'admin',
                    'gender' => 'male',
                    'dob' => '2000-12-12',
                    'pob' => 'PP',
                    'address' => 'PP',
                    'phone_number' => '00000000',
                    'nation_id_card' => '12121312312',
                ],
                [
                    'position_id' => '2',
                    'name' => 'sale',
                    'gender' => 'male',
                    'dob' => '2000-12-12',
                    'pob' => 'PP',
                    'address' => 'PP',
                    'phone_number' => '00000000',
                    'nation_id_card' => '12121312312',
                ]
            ]
        );
        //
    }
}