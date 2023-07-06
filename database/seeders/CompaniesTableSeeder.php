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
            'name' => 'Psychotherapy Health Center',
            'address' => '123 Wellness St, Health City',
            'phone' => '123-456-7890',
            'vat_number' => '999999999',
            'email' => 'contact@psychhealthcenter.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('companies')->insert([
            'name' => 'Counselors Health Center',
            'address' => '123 Wellness St, Health City',
            'phone' => '123-456-7890',
            'vat_number' => '999999998',
            'email' => 'contact@counselorshealthcenter.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('companies')->insert([
            'name' => 'Life Coaching & Health Center',
            'address' => '123 Wellness St, Health City',
            'phone' => '123-456-7890',
            'vat_number' => '999999997',
            'email' => 'contact@lifecoachinghealthcenter.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
