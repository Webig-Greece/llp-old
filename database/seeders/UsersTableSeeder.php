<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin User
        $user1 = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password' => Hash::make('123456'),
            'vat_number' => '000000000',
            // 'trial_ends_at' => Carbon::now()->addDays(14),
            'email_verified_at' => Carbon::now(),
            'role_id' => 1, // Assuming 1 is the role_id for admin
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Psychologist User
        $user2 =  User::create([
            'first_name' => 'User1',
            'last_name' => 'Example',
            'email' => 'user1@example.com',
            'password' => Hash::make('123456'),
            'vat_number' => '111111111',
            'trial_ends_at' => Carbon::now()->addDays(14),
            'email_verified_at' => Carbon::now(),
            'role_id' => 2, // Assigning role_id for psychologist
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Counselor User
        $user3 = User::create([
            'first_name' => 'User2',
            'last_name' => 'Example',
            'email' => 'user2@example.com',
            'password' => Hash::make('123456'),
            'vat_number' => '222222222',
            'trial_ends_at' => Carbon::now()->addDays(14),
            'email_verified_at' => Carbon::now(),
            'role_id' => 3, // Assigning role_id for psychologist
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Coach User
        $user4 = User::create([
            'first_name' => 'User3',
            'last_name' => 'Example',
            'email' => 'user3@example.com',
            'password' => Hash::make('123456'),
            'vat_number' => '333333333',
            'trial_ends_at' => Carbon::now()->addDays(14),
            'email_verified_at' => Carbon::now(),
            'role_id' => 4, // Assigning role_id for psychologist
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);


        // Fetch roles
        $adminRole = Role::where('name', 'admin')->first();
        $psychologistRole = Role::where('name', 'psychologist')->first();
        $counselorRole = Role::where('name', 'counselor')->first();
        $coachRole = Role::where('name', 'coach')->first();

        // Associate roles with users
        $user1->roles()->attach($adminRole);
        $user2->roles()->attach($psychologistRole);
        $user3->roles()->attach($counselorRole);
        $user4->roles()->attach($coachRole);
    }
}
