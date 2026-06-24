<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\DestinationCategory;
use App\Models\Event;
use App\Models\VisitorLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestinationApiTest extends TestCase
{
    use RefreshDatabase;

    protected Destination $destination;

    protected function setUp(): void
    {
        parent::setUp();

        $category = DestinationCategory::create([
            'name' => 'Pantai',
            'slug' => 'pantai',
            'is_active' => true,
        ]);

        $this->destination = Destination::create([
            'destination_category_id' => $category->id,
            'name' => 'Pantai Test',
            'slug' => 'pantai-test',
            'description' => 'Destinasi test.',
            'address' => 'Bali, Indonesia',
            'max_capacity' => 500,
            'ticket_price' => 25000,
            'is_active' => true,
        ]);

        // Add some visitor logs
        VisitorLog::create([
            'destination_id' => $this->destination->id,
            'visit_date' => now()->format('Y-m-d'),
            'visit_hour' => 10,
            'visitor_count' => 120,
            'source' => 'admin_input',
        ]);

        // Add an event
        Event::create([
            'destination_id' => $this->destination->id,
            'name' => 'Festival Pantai',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(2),
            'expected_impact' => 'medium',
        ]);

        // Add another destination for recommendation tests
        Destination::create([
            'destination_category_id' => $category->id,
            'name' => 'Pantai Alternatif',
            'slug' => 'pantai-alternatif',
            'description' => 'Pantai lain untuk rekomendasi.',
            'address' => 'Lombok, Indonesia',
            'max_capacity' => 300,
            'ticket_price' => 15000,
            'is_active' => true,
        ]);
    }

    public function test_can_list_destinations(): void
    {
        $response = $this->getJson('/api/destinations');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Destinations retrieved successfully',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => ['id', 'name', 'slug', 'category', 'is_active'],
                ],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);

        $this->assertCount(2, $response->json('data'));
    }

    public function test_can_search_destinations(): void
    {
        $response = $this->getJson('/api/destinations?search=Alternatif');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Pantai Alternatif', $response->json('data.0.name'));
    }

    public function test_can_show_destination_detail(): void
    {
        $response = $this->getJson("/api/destinations/{$this->destination->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Destination detail retrieved successfully',
            ])
            ->assertJsonStructure([
                'data' => [
                    'id', 'name', 'slug', 'category', 'events',
                    'latest_reviews', 'crowd_status',
                ],
            ]);

        // Check crowd_status structure
        $response->assertJsonStructure([
            'data' => [
                'crowd_status' => [
                    'destination_id', 'prediction_date', 'crowd_score',
                    'crowd_level', 'crowd_label', 'method', 'factors',
                ],
            ],
        ]);
    }

    public function test_can_get_crowd_status(): void
    {
        $today = now()->format('Y-m-d');

        $response = $this->getJson(
            "/api/destinations/{$this->destination->id}/crowd-status?date={$today}&hour=10"
        );

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'destination_id' => $this->destination->id,
                    'prediction_date' => $today,
                    'prediction_hour' => 10,
                    'visitor_count' => 120,
                    'max_capacity' => 500,
                    'method' => 'rule_based',
                ],
            ]);

        // crowd_score should be visitor_count/max_capacity + adjustments
        $this->assertArrayHasKey('crowd_score', $response->json('data'));
        $this->assertArrayHasKey('crowd_level', $response->json('data'));
        $this->assertArrayHasKey('crowd_label', $response->json('data'));
    }

    public function test_crowd_status_validates_hour(): void
    {
        $response = $this->getJson(
            "/api/destinations/{$this->destination->id}/crowd-status?hour=25"
        );

        $response->assertUnprocessable();
    }

    public function test_can_get_recommendations(): void
    {
        $response = $this->getJson(
            "/api/destinations/{$this->destination->id}/recommendations"
        );

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Alternative destinations retrieved successfully',
            ]);

        // Should not include the source destination
        $ids = collect($response->json('data'))->pluck('destination.id');
        $this->assertNotContains($this->destination->id, $ids);
    }

    public function test_crowd_status_handles_zero_capacity(): void
    {
        $zeroCapDest = Destination::create([
            'destination_category_id' => $this->destination->destination_category_id,
            'name' => 'Zero Cap Dest',
            'slug' => 'zero-cap',
            'max_capacity' => 0,
            'is_active' => true,
        ]);

        $response = $this->getJson("/api/destinations/{$zeroCapDest->id}/crowd-status");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'crowd_score' => 0,
                    'max_capacity' => 0,
                ],
            ]);
    }
}
