<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\DestinationCategory;
use App\Services\CrowdPredictionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiServiceIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected Destination $destination;
    protected CrowdPredictionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        $category = DestinationCategory::factory()->create(['name' => 'Pantai']);
        $this->destination = Destination::factory()->create([
            'destination_category_id' => $category->id,
            'max_capacity' => 1000,
        ]);
        
        $this->service = app(CrowdPredictionService::class);
    }

    public function test_it_uses_ml_prediction_when_fastapi_succeeds()
    {
        Http::fake([
            '127.0.0.1:8001/predict-crowd-ml' => Http::response([
                'success' => true,
                'data' => [
                    'destination_id' => $this->destination->id,
                    'predicted_count' => 600,
                    'max_capacity' => 1000,
                    'crowd_score' => 0.60,
                    'crowd_level' => 'moderate',
                    'crowd_label' => 'Normal',
                    'method' => 'machine_learning',
                    'model_version' => 'ml-rf-v1',
                ]
            ], 200),
        ]);

        $result = $this->service->predict($this->destination);

        $this->assertEquals('machine_learning', $result['method']);
        $this->assertEquals(600, $result['predicted_count']);
        
        $this->assertDatabaseHas('crowd_predictions', [
            'destination_id' => $this->destination->id,
            'method' => 'machine_learning',
            'predicted_count' => 600,
        ]);
    }

    public function test_it_falls_back_to_fastapi_rule_based_when_ml_fails()
    {
        Http::fake([
            '127.0.0.1:8001/predict-crowd-ml' => Http::response(['success' => false], 404),
            '127.0.0.1:8001/predict-crowd' => Http::response([
                'success' => true,
                'data' => [
                    'destination_id' => $this->destination->id,
                    'prediction_date' => Carbon::today()->format('Y-m-d'),
                    'prediction_hour' => 12,
                    'visitor_count' => 500,
                    'max_capacity' => 1000,
                    'crowd_score' => 0.50,
                    'crowd_level' => 'moderate',
                    'crowd_label' => 'Normal',
                    'method' => 'rule_based_fastapi',
                ]
            ], 200),
        ]);

        $result = $this->service->predict($this->destination);

        $this->assertEquals('ai_service_rule_based', $result['method']);
        $this->assertEquals(0.50, $result['crowd_score']);
    }

    public function test_it_falls_back_to_internal_rule_based_when_fastapi_is_down()
    {
        // Simulate total failure (e.g., connection refused)
        Http::fake([
            '*' => Http::response(null, 500)
        ]);

        $result = $this->service->predict($this->destination);

        $this->assertEquals('rule_based', $result['method']);
        
        $this->assertDatabaseHas('crowd_predictions', [
            'destination_id' => $this->destination->id,
            'method' => 'rule_based',
        ]);
    }

    public function test_destination_crowd_status_api_endpoint()
    {
        Http::fake([
            '127.0.0.1:8001/predict-crowd-ml' => Http::response([
                'success' => true,
                'data' => [
                    'destination_id' => $this->destination->id,
                    'predicted_count' => 800,
                    'max_capacity' => 1000,
                    'crowd_score' => 0.80,
                    'crowd_level' => 'high',
                    'crowd_label' => 'Ramai',
                    'method' => 'machine_learning',
                    'model_version' => 'ml-rf-v1',
                ]
            ], 200),
        ]);

        $response = $this->getJson("/api/destinations/{$this->destination->id}/crowd-status");

        $response->assertStatus(200)
                 ->assertJsonPath('data.method', 'machine_learning')
                 ->assertJsonPath('data.crowd_score', 0.80);
    }

    public function test_ai_service_model_info_api_endpoint()
    {
        Http::fake([
            '127.0.0.1:8001/health' => Http::response(['success' => true], 200),
            '127.0.0.1:8001/model-info' => Http::response([
                'success' => true,
                'data' => [
                    'model_available' => true,
                    'model_version' => 'ml-rf-v1',
                    'metadata' => ['mae' => 50]
                ]
            ], 200),
        ]);

        $response = $this->getJson('/api/ai-service/model-info');

        $response->assertStatus(200)
                 ->assertJsonPath('data.available', true)
                 ->assertJsonPath('data.model_available', true);
    }
}
