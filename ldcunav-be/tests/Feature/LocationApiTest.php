<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocationApiTest extends TestCase
{
    public function test_can_list_locations(): void
    {
        $response = $this->getJson('/api/locations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'category',
                        'type',
                    ],
                ],
            ]);
    }

    public function test_can_filter_locations_by_category(): void
    {
        $response = $this->getJson('/api/locations?category=Academic');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $data = $response->json('data');
        $this->assertNotEmpty($data);
        foreach ($data as $location) {
            $this->assertSame('Academic', $location['category']);
        }
    }

    public function test_can_search_locations_by_keyword(): void
    {
        $response = $this->getJson('/api/locations/search?q=rodolsa');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertSame('Rodolsa Hall', $data[0]['name']);
    }

    public function test_search_requires_query_parameter(): void
    {
        $response = $this->getJson('/api/locations/search');

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['q']);
    }

    public function test_can_retrieve_map_building_placements(): void
    {
        $response = $this->getJson('/api/locations/map');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $data = $response->json('data');
        $this->assertCount(4, $data);
        foreach ($data as $location) {
            $this->assertNotNull($location['mapOrder']);
        }
    }

    public function test_can_retrieve_single_location_by_slug(): void
    {
        $response = $this->getJson('/api/locations/west-academic-cluster');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'West Academic Cluster');
    }

    public function test_returns_404_for_unknown_slug(): void
    {
        $response = $this->getJson('/api/locations/non-existent-cluster');

        $response->assertStatus(404)
            ->assertJsonPath('success', false);
    }
}
