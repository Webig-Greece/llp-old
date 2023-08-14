<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

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
            'trial_ends_at' => Carbon::now()->addDays(14),
            'email_verified_at' => Carbon::now(),
            'profession' => 'psychologist',
            'account_type' => 'main',
            'role_id' => 2, // Assigning role_id for Psychologist
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'company_id' => 1
        ]);

        // Counselor User
        $user3 = User::create([
            'first_name' => 'User2',
            'last_name' => 'Example',
            'email' => 'user2@example.com',
            'password' => Hash::make('123456'),
            'trial_ends_at' => Carbon::now()->addDays(14),
            'email_verified_at' => Carbon::now(),
            'profession' => 'counselor',
            'account_type' => 'main',
            'role_id' => 3, // Assigning role_id for Counselor
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'company_id' => 2
        ]);

        // Coach User
        $user4 = User::create([
            'first_name' => 'User3',
            'last_name' => 'Example',
            'email' => 'user3@example.com',
            'password' => Hash::make('123456'),
            'trial_ends_at' => Carbon::now()->addDays(14),
            'email_verified_at' => Carbon::now(),
            'profession' => 'coach',
            'account_type' => 'main',
            'role_id' => 4, // Assigning role_id for Coach
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'company_id' => 3
        ]);

        // Psychiatrist User
        $user5 = User::create([
            'first_name' => 'User5',
            'last_name' => 'Example',
            'email' => 'user5@example.com',
            'password' => Hash::make('123456'),
            'trial_ends_at' => Carbon::now()->addDays(14),
            'email_verified_at' => Carbon::now(),
            'profession' => 'psychiatrist',
            'account_type' => 'main',
            'role_id' => 4, // Assigning role_id for Psychiatrist
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'company_id' => 3
        ]);

        // Secretary User
        $user6 = User::create([
            'first_name' => 'Secretary',
            'last_name' => 'User',
            'email' => 'secretary@example.com',
            'password' => Hash::make('123456'),
            'trial_ends_at' => Carbon::now()->addDays(14),
            'email_verified_at' => Carbon::now(),
            'account_type' => 'secretary',
            'role_id' => 5, // Assigning role_id for Secretary
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'company_id' => 1
        ]);

        // Trial User
        $user7 = User::create([
            'first_name' => 'Trial',
            'last_name' => 'User',
            'email' => 'trial@example.com',
            'password' => Hash::make('123456'),
            'vat_number' => '000000010',
            // 'trial_ends_at' => Carbon::now()->addDays(14),
            'email_verified_at' => Carbon::now(),
            'profession' => 'psychologist',
            'role_id' => 6, // Assuming 1 is the role_id for admin
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'company_id' => 1
        ]);


        // Fetch roles
        $adminRole = Role::where('name', 'admin')->first();
        $psychologistRole = Role::where('name', 'psychologist')->first();
        $counselorRole = Role::where('name', 'counselor')->first();
        $coachRole = Role::where('name', 'coach')->first();
        $psychiatristRole = Role::where('name', 'psychiatrist')->first();
        $secretaryRole = Role::where('name', 'secretary')->first();
        $trialUserRole = Role::where('name', 'trial_user')->first();

        // Fetch permissions
        $permissions = [
            'create-branch',
            'update-branch',
            'delete-branch',
            'create-company',
            'update-company',
            'delete-company',
            'view_own_records',
            'edit_own_records',
            'create_records',
            'manage_own_appointments',
            'export_patient_data',
            'import_patient_data',
            'manage_all_appointments',
            'send_communications'
        ];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        $allPermissions = Permission::all()->pluck('id');
        $therapistPermissions = Permission::whereIn('name', [
            'view_own_records',
            'edit_own_records',
            'create_records',
            'manage_own_appointments',
            'export_patient_data',
            'import_patient_data'
        ])->pluck('id');
        $secretaryPermissions = Permission::whereIn('name', [
            'manage_all_appointments',
            'send_communications'
        ])->pluck('id');
        $trialUserPermissions = Permission::whereIn('name', [
            'view_own_records',
            'edit_own_records',
            'create_records',
            'manage_own_appointments',
            // Trial Users cannot export patient
            'import_patient_data'
        ])->pluck('id');

        // Associate permissions with roles
        $adminRole->permissions()->sync($allPermissions);
        $psychologistRole->permissions()->sync($therapistPermissions);
        $counselorRole->permissions()->sync($therapistPermissions);
        $coachRole->permissions()->sync($therapistPermissions);
        $psychiatristRole->permissions()->sync($therapistPermissions);
        $secretaryRole->permissions()->sync($secretaryPermissions);
        $trialUserRole->permissions()->sync($trialUserPermissions);

        // Associate roles with users
        $user1->roles()->attach($adminRole);
        $user2->roles()->attach($psychologistRole);
        $user3->roles()->attach($counselorRole);
        $user4->roles()->attach($coachRole);
        $user5->roles()->attach($psychiatristRole);
        $user6->roles()->attach($secretaryRole);
        $user7->roles()->attach($trialUserRole);
    }
}
