<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PatientRecordsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('patient_records')->insert([
            'user_id' => 1,
            'branch_id' => 1,
            'patient_first_name' => 'John',
            'patient_last_name' => 'Doe',
            'date_of_birth' => '1980-01-01',
            'address' => '789 Patient St, Health City',
            'phone' => '123-456-7892',
            'email' => 'john.doe@example.com',
            'medical_history' => 'No known allergies. No prior surgeries.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
