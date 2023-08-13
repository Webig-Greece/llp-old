<?php

namespace Tests\Traits;

use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

trait SetsUpUsersRolesAndPermissions
{
    private function generateVatNumber()
    {
        return mt_rand(100000000, 999999999);
    }

    protected function setUpUsersRolesAndPermissions()
    {
        // Create companies from DB
        $psychotherapyHealthCenter = Company::factory()->create(['vat_number' => '999999999'])->firstOrFail();
        $counselorsHealthCenter = Company::factory()->create(['vat_number' => '999999998'])->firstOrFail();
        $lifeCoachingHealthCenter = Company::factory()->create(['vat_number' => '999999997'])->firstOrFail();

        // Create branch from DB
        $downtownClinic = Branch::factory()->create(['name' => 'Downtown Clinic'])->firstOrFail();

        // Define roles
        $adminRole = Role::create(['name' => 'admin']);
        $psychologistRole = Role::create(['name' => 'psychologist']);
        $counselorRole = Role::create(['name' => 'counselor']);
        $coachRole = Role::create(['name' => 'coach']);
        $psychiatristRole = Role::create(['name' => 'psychiatrist']);
        $secretaryRole = Role::create(['name' => 'secretary']);
        $trialUserRole = Role::create(['name' => 'trial_user']);

        // Define permissions
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

        // Create users and associate roles
        $user1 = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password' => Hash::make('123456'),
            'vat_number' => '000000000',
            'email_verified_at' => Carbon::now(),
            'role_id' => $adminRole->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $user1->roles()->attach($adminRole);

        // Other users
        $user2 = User::create([
            'first_name' => 'User1',
            'last_name' => 'Example',
            'email' => 'user1@example.com',
            'password' => Hash::make('123456'),
            'vat_number' => $this->generateVatNumber(),
            'trial_ends_at' => Carbon::now()->addDays(14),
            'email_verified_at' => Carbon::now(),
            'profession' => 'psychologist',
            'account_type' => 'main',
            'role_id' => $psychologistRole->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'company_id' => $psychotherapyHealthCenter->id,
            'branch_id' => $downtownClinic->id
        ]);
        $user2->roles()->attach($psychologistRole);

        $user3 = User::create([
            'first_name' => 'User2',
            'last_name' => 'Example',
            'email' => 'user2@example.com',
            'password' => Hash::make('123456'),
            'vat_number' => $this->generateVatNumber(),
            'trial_ends_at' => Carbon::now()->addDays(14),
            'email_verified_at' => Carbon::now(),
            'profession' => 'counselor',
            'account_type' => 'main',
            'role_id' => $counselorRole->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'company_id' => $counselorsHealthCenter->id
        ]);
        $user3->roles()->attach($counselorRole);

        $user4 = User::create([
            'first_name' => 'User3',
            'last_name' => 'Example',
            'email' => 'user3@example.com',
            'password' => Hash::make('123456'),
            'vat_number' => $this->generateVatNumber(),
            'trial_ends_at' => Carbon::now()->addDays(14),
            'email_verified_at' => Carbon::now(),
            'profession' => 'coach',
            'account_type' => 'main',
            'role_id' => $coachRole->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'company_id' => $lifeCoachingHealthCenter->id
        ]);
        $user4->roles()->attach($coachRole);

        $user5 = User::create([
            'first_name' => 'User5',
            'last_name' => 'Example',
            'email' => 'user5@example.com',
            'password' => Hash::make('123456'),
            'vat_number' => $this->generateVatNumber(),
            'trial_ends_at' => Carbon::now()->addDays(14),
            'email_verified_at' => Carbon::now(),
            'profession' => 'psychiatrist',
            'account_type' => 'main',
            'role_id' => $psychiatristRole->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'company_id' => $lifeCoachingHealthCenter->id
        ]);
        $user5->roles()->attach($psychiatristRole);

        $user6 = User::create([
            'first_name' => 'Secretary',
            'last_name' => 'User',
            'email' => 'secretary@example.com',
            'password' => Hash::make('123456'),
            'vat_number' => $this->generateVatNumber(),
            'trial_ends_at' => Carbon::now()->addDays(14),
            'email_verified_at' => Carbon::now(),
            'account_type' => 'secondary',
            'role_id' => $secretaryRole->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'company_id' => $psychotherapyHealthCenter->id,
            'branch_id' => $downtownClinic->id
        ]);
        $user6->roles()->attach($secretaryRole);

        $user7 = User::create([
            'first_name' => 'Trial',
            'last_name' => 'User',
            'email' => 'trial@example.com',
            'password' => Hash::make('123456'),
            'vat_number' => $this->generateVatNumber(),
            'email_verified_at' => Carbon::now(),
            'profession' => 'psychologist',
            'role_id' => $trialUserRole->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'company_id' => $psychotherapyHealthCenter->id,
            'branch_id' => $downtownClinic->id
        ]);
        $user7->roles()->attach($trialUserRole);
        // Additional logic if needed

    }
}
