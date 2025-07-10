<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('branch')->truncate();
        DB::table('branch')->insert([
            'branch_name' => 'FakeMart',
            'location' => 'PhnomPenh',
            'contact_number' => '099 77 49 67',
        ]);
    }
}
