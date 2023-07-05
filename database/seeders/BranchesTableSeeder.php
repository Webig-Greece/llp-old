<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('branches')->insert([
            'company_id' => 1,
            'name' => 'Downtown Clinic',
            'address' => '456 Health Ave, Health City',
            'phone' => '123-456-7891',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
