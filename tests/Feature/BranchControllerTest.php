<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\SetsUpUsersRolesAndPermissions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Role;

class BranchControllerTest extends TestCase
{
    use RefreshDatabase, SetsUpUsersRolesAndPermissions;


    protected function setUp(): void
    {
        parent::setUp();

        // Set up the roles and permissions
        $this->setUpUsersRolesAndPermissions();
    }

    public function test_store_branch()
    {
        // Fetch the admin role
        $adminRole = Role::where('name', 'admin')->first();
        // Create a user with the admin role
        $user = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);
        $user->roles()->attach($adminRole);
        $user->refresh();
        $this->assertTrue($user->hasPermission('create-branch'));


        // Create a company
        $company = Company::factory()->create();

        // Make the request as the admin user
        $response = $this->actingAs($user, 'api')->postJson('/api/branches', [
            'company_id' => $company->id,
            'name' => 'Test Branch',
            'address' => '123 Test Street',
            'phone' => '1234567890',
            'email' => 'test@example.com'
        ]);

        $response->assertStatus(201); // Check the response status
        $this->assertDatabaseHas('branches', ['name' => 'Test Branch']); // Check the database
    }


    public function test_show_branch()
    {
        // Fetch the admin role
        $adminRole = Role::where('name', 'admin')->first();
        // Create a user with the admin role
        $user = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);
        $user->roles()->attach($adminRole);
        $user->refresh();

        $branch = Branch::factory()->create();

        $response = $this->actingAs($user, 'api') // Authenticate the user
            ->getJson('/api/branches/' . $branch->id);

        $response->assertStatus(200);
        $response->assertJson(['data' => ['name' => $branch->name]]);
    }

    public function test_update_branch()
    {
        // Fetch the admin role
        $adminRole = Role::where('name', 'admin')->first();
        // Create a user with the admin role
        $user = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);
        $user->roles()->attach($adminRole);
        $user->refresh();

        $branch = Branch::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->putJson('/api/branches/' . $branch->id, [
                'company_id' => $branch->company_id, // Assuming this remains the same
                'name' => 'Updated Branch',
                'address' => 'Updated Address',
                'phone' => '1234567891',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('branches', [
            'name' => 'Updated Branch',
            'address' => 'Updated Address',
            'phone' => '1234567891',
        ]);
    }

    public function test_destroy_branch()
    {
        // Fetch the admin role
        $adminRole = Role::where('name', 'admin')->first();
        // Create a user with the admin role
        $user = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);
        $user->roles()->attach($adminRole);
        $user->refresh();

        $branch = Branch::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->deleteJson('/api/branches/' . $branch->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('branches', ['id' => $branch->id]);
    }
}
