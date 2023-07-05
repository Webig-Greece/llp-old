<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('companies')->insert([
            'name' => 'LifeLift Health Center',
            'address' => '123 Wellness St, Health City',
            'phone' => '123-456-7890',
            'vat_number' => '999999999',
            'email' => 'contact@lifelifthealthcenter.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
