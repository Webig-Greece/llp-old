<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\SetsUpUsersRolesAndPermissions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Role;

class CompanyControllerTest extends TestCase
{
    use RefreshDatabase, SetsUpUsersRolesAndPermissions;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up the roles and permissions
        $this->setUpUsersRolesAndPermissions();
    }

    public function test_store_company()
    {
        // Fetch the admin role
        $adminRole = Role::where('name', 'admin')->first();
        // Create a user with the admin role
        $user = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);
        $user->roles()->attach($adminRole);
        $user->refresh();

        // Make the request as the admin user
        $response = $this->actingAs($user, 'api')->postJson('/api/companies', [
            'name' => 'Test Company',
            'vat_number' => '123456789',
            // Add other company fields as needed
        ]);

        $response->assertStatus(201); // Check the response status
        $this->assertDatabaseHas('companies', ['name' => 'Test Company']); // Check the database
    }

    public function test_show_company()
    {
        // Fetch the admin role
        $adminRole = Role::where('name', 'admin')->first();
        // Create a user with the admin role
        $user = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);
        $user->roles()->attach($adminRole);
        $user->refresh();

        $company = Company::factory()->create();

        $response = $this->actingAs($user, 'api') // Authenticate the user
            ->getJson('/api/companies/' . $company->id);

        $response->assertStatus(200);
        $response->assertJson(['data' => ['name' => $company->name]]);
    }

    public function test_update_company()
    {
        // Fetch the admin role
        $adminRole = Role::where('name', 'admin')->first();
        // Create a user with the admin role
        $user = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);
        $user->roles()->attach($adminRole);
        $user->refresh();

        $company = Company::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->putJson('/api/companies/' . $company->id, [
                'name' => 'Updated Company',
                'vat_number' => '987654321',
                // Add other fields as needed
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('companies', [
            'name' => 'Updated Company',
            'vat_number' => '987654321',
        ]);
    }

    public function test_destroy_company()
    {
        // Fetch the admin role
        $adminRole = Role::where('name', 'admin')->first();
        // Create a user with the admin role
        $user = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);
        $user->roles()->attach($adminRole);
        $user->refresh();

        $company = Company::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->deleteJson('/api/companies/' . $company->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }
}
