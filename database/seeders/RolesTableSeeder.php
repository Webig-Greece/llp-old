<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            ['name' => 'admin', 'description' => 'Administrator'],
            ['name' => 'psychologist', 'description' => 'Psychologist'],
            ['name' => 'counselor', 'description' => 'Mental Health Counselor'],
            ['name' => 'coach', 'description' => 'Life Coach'],
            ['name' => 'secretary', 'description' => 'Secretary']
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
