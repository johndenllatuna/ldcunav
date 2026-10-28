<?php

namespace Tests\Feature;

use App\Prisma\PrismaClient;
use Tests\TestCase;

class SavedLocationApiTest extends TestCase
{
    private string $token;
    private int $locationId;

    protected function setUp(): void
    {
        parent::setUp();
        $random = bin2hex(random_bytes(4));
        $res = $this->postJson('/api/register', [
            'fullName' => 'Saved Location Tester',
            'studentId' => "2026-{$random}",
            'email' => "saved.test.{$random}@liceo.edu.ph",
            'password' => 'SavedPass123!',
        ]);

        $this->token = (string) $res->json('data.token');

        // Look up an existing location ID (e.g. West Academic Cluster)
        $prisma = app(PrismaClient::class);
        $location = $prisma->location->findFirst();
        $this->locationId = (int) $location['id'];
    }

    public function test_saved_locations_requires_authentication(): void
    {
        $response = $this->getJson('/api/saved');

        $response->assertStatus(401);
    }

    public function test_can_list_saved_locations(): void
    {
        $response = $this->withToken($this->token)->getJson('/api/saved');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ]);
    }

    public function test_can_save_location(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/saved', [
            'locationId' => $this->locationId,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.locationId', $this->locationId)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'userId',
                    'locationId',
                    'location' => ['id', 'name', 'slug'],
                ],
            ]);
    }

    public function test_saving_duplicate_location_is_idempotent(): void
    {
        // First save
        $this->withToken($this->token)->postJson('/api/saved', [
            'locationId' => $this->locationId,
        ])->assertStatus(201);

        // Second save of identical location
        $repeatRes = $this->withToken($this->token)->postJson('/api/saved', [
            'locationId' => $this->locationId,
        ]);

        // Must succeed without throwing database unique violation
        $this->assertTrue(in_array($repeatRes->status(), [200, 201]));

        // List must still contain exactly one entry for this location
        $listRes = $this->withToken($this->token)->getJson('/api/saved');
        $listRes->assertStatus(200);
        $data = $listRes->json('data') ?? [];
        $matching = array_filter(
            $data,
            fn ($item) => (int) $item['locationId'] === $this->locationId
        );
        $this->assertCount(1, $matching);
    }

    public function test_saving_non_existent_location_returns_422(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/saved', [
            'locationId' => 9999999,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['locationId']);
    }

    public function test_can_remove_saved_location(): void
    {
        // First save it
        $this->withToken($this->token)->postJson('/api/saved', [
            'locationId' => $this->locationId,
        ]);

        // Delete it
        $delRes = $this->withToken($this->token)->deleteJson("/api/saved/{$this->locationId}");
        $delRes->assertStatus(200)
            ->assertJsonPath('success', true);

        // Deleting again returns 404
        $delAgain = $this->withToken($this->token)->deleteJson("/api/saved/{$this->locationId}");
        $delAgain->assertStatus(404)
            ->assertJsonPath('success', false);
    }
}
