<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'view_own_records',
            'edit_own_records',
            'create_records',
            'transfer_records',
            'view_all_records',
            'manage_users',
            'manage_subscriptions',
            'view_analytics',
            'send_communications',
            'manage_own_appointments',
            'manage_all_appointments',
            'export_patient_data',
            'import_patient_data',
            'create-branch',
            'create-additional-professional',
        ];

        foreach ($permissions as $permission) {
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }
    }
}
