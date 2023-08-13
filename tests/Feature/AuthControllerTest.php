<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\SetsUpUsersRolesAndPermissions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

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
        dump($userData);

        $response = $this->postJson('/auth/register', $userData);

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

        $response = $this->postJson('/auth/login', $loginData);

        $response->assertStatus(200);
        $response->assertJsonStructure(['user', 'token']);
    }

    public function test_user_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson('/auth/logout');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Successfully logged out']);
    }

    public function test_user_profile()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson('/auth/user-profile');

        $response->assertStatus(200);
        $response->assertJsonStructure(['user']);
    }

    public function test_add_secretary()
    {
        $user = User::factory()->create(['profession' => 'psychologist']);
        $role = Role::factory()->create(['name' => 'secretary']);

        $secretaryData = [
            'firstName' => 'Secretary',
            'lastName' => 'User',
            'email' => 'secretary@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'profession' => 'secretary',
        ];

        $response = $this->actingAs($user, 'api')->postJson('/users/create-secretary', $secretaryData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'secretary@example.com']);
    }
}
