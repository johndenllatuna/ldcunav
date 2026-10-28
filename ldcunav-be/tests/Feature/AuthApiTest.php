<?php

namespace Tests\Feature;

use App\Prisma\PrismaClient;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    private string $testEmail;
    private string $testStudentId;
    private string $testPassword = 'TestPassword123!';

    protected function setUp(): void
    {
        parent::setUp();
        $random = bin2hex(random_bytes(4));
        $this->testEmail = "auth.test.{$random}@liceo.edu.ph";
        $this->testStudentId = "2026-{$random}";
    }

    public function test_can_register_user_with_valid_liceo_email(): void
    {
        $response = $this->postJson('/api/register', [
            'fullName' => 'Test Student User',
            'studentId' => $this->testStudentId,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'user' => [
                        'id',
                        'fullName',
                        'studentId',
                        'email',
                    ],
                ],
            ]);

        // Security check: passwordHash must never be exposed
        $this->assertArrayNotHasKey('passwordHash', $response->json('data.user'));
    }

    public function test_registration_rejects_non_liceo_email(): void
    {
        $response = $this->postJson('/api/register', [
            'fullName' => 'Invalid Domain User',
            'studentId' => '2026-99999',
            'email' => 'student@gmail.com',
            'password' => $this->testPassword,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_rejects_weak_password(): void
    {
        $response = $this->postJson('/api/register', [
            'fullName' => 'Weak Password User',
            'studentId' => '2026-88888',
            'email' => "weak.{$this->testEmail}",
            'password' => 'lowercaseonly',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        // First registration
        $this->postJson('/api/register', [
            'fullName' => 'First User',
            'studentId' => $this->testStudentId,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
        ])->assertStatus(201);

        // Second registration with same email
        $response = $this->postJson('/api/register', [
            'fullName' => 'Second User',
            'studentId' => "diff-{$this->testStudentId}",
            'email' => $this->testEmail,
            'password' => $this->testPassword,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_rejects_duplicate_student_id(): void
    {
        // First registration
        $this->postJson('/api/register', [
            'fullName' => 'First User',
            'studentId' => $this->testStudentId,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
        ])->assertStatus(201);

        // Second registration with same studentId but different email
        $response = $this->postJson('/api/register', [
            'fullName' => 'Second User',
            'studentId' => $this->testStudentId,
            'email' => "diff.{$this->testEmail}",
            'password' => $this->testPassword,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['studentId']);
    }

    public function test_can_login_with_valid_credentials(): void
    {
        // Ensure user is registered
        $this->postJson('/api/register', [
            'fullName' => 'Login Test User',
            'studentId' => $this->testStudentId,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
        ])->assertStatus(201);

        $response = $this->postJson('/api/login', [
            'email' => $this->testEmail,
            'password' => $this->testPassword,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'user' => ['id', 'fullName', 'email'],
                ],
            ]);
    }

    public function test_login_fails_with_invalid_password(): void
    {
        $this->postJson('/api/register', [
            'fullName' => 'Bad Pass User',
            'studentId' => $this->testStudentId,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
        ])->assertStatus(201);

        $response = $this->postJson('/api/login', [
            'email' => $this->testEmail,
            'password' => 'WrongPassword123!',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    public function test_login_fails_with_non_existent_user(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent.user.2026@liceo.edu.ph',
            'password' => 'AnyPassword123!',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    public function test_logout_revokes_token(): void
    {
        $registerRes = $this->postJson('/api/register', [
            'fullName' => 'Logout Test User',
            'studentId' => $this->testStudentId,
            'email' => $this->testEmail,
            'password' => $this->testPassword,
        ]);
        $token = $registerRes->json('data.token');

        // Confirm authenticated access
        $this->withToken($token)->getJson('/api/me')->assertStatus(200);

        // Perform logout
        $logoutRes = $this->withToken($token)->postJson('/api/logout');
        $logoutRes->assertStatus(200)->assertJsonPath('success', true);

        // Reset in-memory guard cache so subsequent request evaluates DB state
        app('auth')->forgetGuards();

        // Token is now revoked
        $this->withToken($token)->getJson('/api/me')->assertStatus(401);
    }

    public function test_forgot_password_returns_generic_success(): void
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'student@liceo.edu.ph',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }
}
