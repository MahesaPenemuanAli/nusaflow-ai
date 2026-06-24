<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AiServiceClient;
use Illuminate\Http\JsonResponse;

class AiServiceController extends Controller
{
    protected AiServiceClient $aiService;

    public function __construct(AiServiceClient $aiService)
    {
        $this->aiService = $aiService;
    }

    public function health(): JsonResponse
    {
        $available = $this->aiService->isAvailable();

        return response()->json([
            'success' => true,
            'message' => 'AI service status retrieved successfully',
            'data' => [
                'available' => $available,
                'base_url' => config('services.ai_service.base_url'),
            ]
        ]);
    }

    public function modelInfo(): JsonResponse
    {
        $info = $this->aiService->getModelInfo();

        return response()->json([
            'success' => true,
            'message' => 'AI service model info retrieved successfully',
            'data' => [
                'available' => $this->aiService->isAvailable(),
                'model_available' => $info['model_available'] ?? false,
                'model_version' => $info['model_version'] ?? null,
                'metadata' => $info['metadata'] ?? [],
            ]
        ]);
    }
}
