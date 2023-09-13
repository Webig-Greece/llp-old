<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\SetsUpUsersRolesAndPermissions;
use Tests\TestCase;
use App\Models\User;
use App\Models\SubscriptionPlan;
use Laravel\Sanctum\Sanctum;
use App\Models\Role;
use App\Models\Permission;

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
            'vatNumber' => 'EL801807760',
            'acceptTerms' => true,
            'profession' => 'psychologist',
            'roleIdentity' => 'freelancer',
            'language' => 'en',
        ];


        // Mock the SOAP client to return a valid VAT number
        $mockSoapClient = $this->createMock(TestableSoapClient::class);
        $mockSoapClient->method('checkVat')->willReturn((object) ['valid' => true]);

        // Bind the mock SOAP client to the service container
        $this->app->bind(TestableSoapClient::class, function ($app) use ($mockSoapClient) {
            return $mockSoapClient;
        });

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

    public function test_user_registration_with_invalid_vat()
    {
        $userData = [
            'firstName' => 'TestFirstName',
            'lastName' => 'TestLastName',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'acceptTerms' => true,
            'profession' => 'psychologist',
            'roleIdentity' => 'freelancer',
            'language' => 'en',
            'vatNumber' => 'INVALIDVAT', // Should not pass
        ];

        // Mock the SOAP client to return a valid VAT number
        $mockSoapClient = $this->createMock(TestableSoapClient::class);
        $mockSoapClient->method('checkVat')->willReturn((object) ['valid' => true]);

        // Bind the mock SOAP client to the service container
        $this->app->bind(TestableSoapClient::class, function ($app) use ($mockSoapClient) {
            return $mockSoapClient;
        });

        $response = $this->postJson('api/auth/register', $userData);
        $response->assertStatus(400);
        $response->assertJson(['message' => 'Invalid VAT number']);
    }

    public function test_user_changeSubscription()
    {
        // Create the premiumPlanRole
        $premiumPlanRole = Role::firstOrCreate(['name' => 'premiumPlanRole']);

        // Try to get the trial user role or create it if it doesn't exist
        $trialUserRole = Role::firstOrCreate(['name' => 'trial_user']);

        // If you just created the role, then you can attach permissions to it
        if (!$trialUserRole->wasRecentlyCreated) {
            $trialUserPermissions = Permission::whereIn('name', [
                'view_own_records',
                'edit_own_records',
                'create_records',
                'manage_own_appointments',
                'import_patient_data'
            ])->pluck('id');
            $trialUserRole->permissions()->sync($trialUserPermissions);
        }

        $trialUser = User::factory()->create([
            'profession' => 'psychologist',
            'account_type' => 'main',
            'password' => 'password',
            'role_id' => $trialUserRole->id,
        ]);
        $trialUser->roles()->attach($trialUserRole);

        $subscriptionPlan = SubscriptionPlan::factory()->create(['name' => 'premium']);

        $upgradeData = [
            'subscription_plan' => 'premium',
        ];

        Sanctum::actingAs($trialUser);

        $response = $this->postJson('api/auth/change-subscription', $upgradeData);
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Successfully changed subscription!']);
    }
}

// Overwrite class
class TestableSoapClient extends SoapClient
{
    public function checkVat($parameters)
    {
        // This will be overridden in the test
    }
}
