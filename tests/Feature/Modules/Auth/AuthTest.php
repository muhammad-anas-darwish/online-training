<?php 

namespace Tests\Feature\Modules\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Auth\Entities\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected $authenticatedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authenticatedUser = User::query()->first();
    }

    #[Test]
    public function login()
    {        
        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@admin.com',
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user', 
                    'token',
                ],
            ])
            ->assertJson([
                'success' => true
            ]);
    }

    #[Test]
    public function register()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'user',
                    'token',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);
    }

    #[Test]
    public function register_requires_valid_data()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'email',
                'password',
            ]);
    }

    #[Test]
    public function logout()
    {
        // First login to get a token
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'admin@admin.com',
            'password' => 'password',
        ]);
        
        $token = $loginResponse->json('data.token');
        
        // Then logout with the token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/auth/logout');
        
        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'data',
            ])   
            ->assertJson([
                'success' => true
            ]);
    }
}