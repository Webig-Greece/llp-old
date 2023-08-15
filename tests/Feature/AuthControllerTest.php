<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\SetsUpUsersRolesAndPermissions;
use Tests\TestCase;
use App\Models\User;
use App\Models\SubscriptionPlan;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, SetsUpUsersRolesAndPermissions;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up the roles and permissions
        $this->setUpUsersRolesAndPermissions();
    }

    public function test_user_registration()
    {
        $userData = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'vatNumber' => '123456789',
            'acceptTerms' => true,
            'profession' => 'psychologist',
            'roleIdentity' => 'freelancer',
            'language' => 'en',
        ];

        $response = $this->postJson('api/auth/register', $userData);
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    public function test_user_login()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $loginData = [
            'email' => 'john@example.com',
            'password' => 'password',
        ];

        $response = $this->postJson('api/auth/login', $loginData);

        $response->assertStatus(200);
        $response->assertJsonStructure(['user', 'token']);
    }

    public function test_user_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson('/api/auth/logout');
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Successfully logged out']);
    }

    public function test_user_profile()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson('api/auth/user-profile');
        $response->assertStatus(200);
        $response->assertJsonStructure(['user']);
    }

    public function test_add_secretary()
    {
        $user = User::factory()->create(['profession' => 'psychologist']);

        $secretaryData = [
            'firstName' => 'Secretary',
            'lastName' => 'User',
            'email' => 'secretary.example123@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'account_type' => 'secretary'
        ];

        $response = $this->actingAs($user, 'api')->postJson('api/users/' . $user->id . '/create-secretary', $secretaryData);
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['account_type' => 'secretary']);
        $this->assertDatabaseHas('users', ['email' => 'secretary.example123@example.com']);
    }

    public function test_add_professional()
    {
        $subscriptionPlan = SubscriptionPlan::factory()->create([
            'allows_additional_professional_accounts' => true,
        ]);

        $user = User::factory()->create([
            'profession' => 'psychologist',
            'account_type' => 'main',
            'subscription_plan_id' => $subscriptionPlan->id,
            'password' => 'password',
        ]);

        $professionalData = [
            'first_name' => 'Professional',
            'last_name' => 'User',
            'email' => 'professional.example123@example.com',
            'profession' => 'psychologist',
            'password' => 'password',
            'password_confirmation' => 'password',
            'address' => '123 Main St',
            'phone' => '123-456-7890',
            'language' => 'en',
        ];

        // Make a POST request to the endpoint
        $response = $this->actingAs($user, 'api')->postJson('api/users/create-secondary-professional', $professionalData);

        // Assert the response status
        $response->assertStatus(201);

        // Assert the response data
        $response->assertJsonStructure([
            'message',
            'data' => [
                'account_type',
                'first_name',
                'last_name',
                'email',
                'profession',
                'company_id',
                'branch_id',
                'updated_at',
                'created_at',
                'id'
            ],
        ]);

        // Assert the database has the new professional user
        $this->assertDatabaseHas('users', [
            'account_type' => 'professional',
            'first_name' => 'Professional',
            'last_name' => 'User',
            'email' => 'professional.example123@example.com',
            'profession' => 'psychologist',
            'company_id' => $user->company_id,
            'branch_id' => $user->branch_id,
            'status' => 'active',
        ]);
    }
}
