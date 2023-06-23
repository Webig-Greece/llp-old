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
            ['name' => 'view_records', 'description' => 'View Patient Records'],
            ['name' => 'create_records', 'description' => 'Create Patient Records'],
            ['name' => 'edit_records', 'description' => 'Edit Patient Records'],
            ['name' => 'delete_records', 'description' => 'Delete Patient Records'],
            ['name' => 'manage_users', 'description' => 'Manage Users']
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
