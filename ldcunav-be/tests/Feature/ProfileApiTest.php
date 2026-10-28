<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    private string $token;
    private string $email;
    private string $fullName = 'Original Profile Name';

    protected function setUp(): void
    {
        parent::setUp();
        $random = bin2hex(random_bytes(4));
        $this->email = "profile.test.{$random}@liceo.edu.ph";
        $studentId = "2026-{$random}";

        $res = $this->postJson('/api/register', [
            'fullName' => $this->fullName,
            'studentId' => $studentId,
            'email' => $this->email,
            'password' => 'ProfilePass123!',
        ]);

        $this->token = (string) $res->json('data.token');
    }

    public function test_me_returns_authenticated_user(): void
    {
        $response = $this->withToken($this->token)->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', $this->email)
            ->assertJsonPath('data.fullName', $this->fullName);
    }

    public function test_me_without_token_returns_401(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401);
    }

    public function test_me_with_invalid_token_returns_401(): void
    {
        $response = $this->withToken('completely-invalid-bearer-token')->getJson('/api/me');

        $response->assertStatus(401);
    }

    public function test_show_profile_returns_user_data(): void
    {
        $response = $this->withToken($this->token)->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', $this->email);
    }

    public function test_can_update_profile_full_name(): void
    {
        $updatedName = 'Updated Student Full Name';

        $response = $this->withToken($this->token)->putJson('/api/profile', [
            'fullName' => $updatedName,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.fullName', $updatedName);

        // Verify persistence via /me (reset in-memory guard cache to re-evaluate from DB)
        app('auth')->forgetGuards();
        $meRes = $this->withToken($this->token)->getJson('/api/me');
        $meRes->assertStatus(200)
            ->assertJsonPath('data.fullName', $updatedName);
    }

    public function test_update_profile_validates_required_name(): void
    {
        $response = $this->withToken($this->token)->putJson('/api/profile', [
            'fullName' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['fullName']);
    }
}
