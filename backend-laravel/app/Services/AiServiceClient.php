<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiServiceClient
{
    protected string $baseUrl;
    protected int $timeout;
    protected bool $enabled;

    public function __construct()
    {
        $this->baseUrl = config('services.ai_service.base_url', 'http://127.0.0.1:8001');
        $this->timeout = config('services.ai_service.timeout', 5);
        $this->enabled = config('services.ai_service.enabled', true);
    }

    protected function client()
    {
        return Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->acceptJson();
    }

    public function isAvailable(): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $response = $this->client()->get('/health');
            return $response->successful() && $response->json('success') === true;
        } catch (\Exception $e) {
            Log::warning('AI Service health check failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getModelInfo(): ?array
    {
        if (!$this->enabled) {
            return null;
        }

        try {
            $response = $this->client()->get('/model-info');
            if ($response->successful()) {
                return $response->json('data');
            }
            return null;
        } catch (\Exception $e) {
            Log::warning('AI Service model-info failed: ' . $e->getMessage());
            return null;
        }
    }

    public function predictCrowdMl(array $payload): ?array
    {
        if (!$this->enabled) {
            return null;
        }

        try {
            $response = $this->client()->post('/predict-crowd-ml', $payload);
            if ($response->successful() && $response->json('success') === true) {
                return $response->json('data');
            }
            return null;
        } catch (\Exception $e) {
            Log::warning('AI Service ML prediction failed: ' . $e->getMessage());
            return null;
        }
    }

    public function predictCrowdRuleBased(array $payload): ?array
    {
        if (!$this->enabled) {
            return null;
        }

        try {
            $response = $this->client()->post('/predict-crowd', $payload);
            if ($response->successful() && $response->json('success') === true) {
                return $response->json('data');
            }
            return null;
        } catch (\Exception $e) {
            Log::warning('AI Service rule-based prediction failed: ' . $e->getMessage());
            return null;
        }
    }
}
