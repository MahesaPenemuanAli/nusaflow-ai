<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DestinationDetailResource;
use App\Http\Resources\DestinationResource;
use App\Models\Destination;
use App\Services\CrowdPredictionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DestinationController extends Controller
{
    public function __construct(
        protected CrowdPredictionService $crowdService,
    ) {}

    /**
     * GET /api/destinations
     *
     * List active destinations with optional filters and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Destination::with('category')
            ->where('is_active', true);

        // Search by name, slug, or address
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Filter by category ID
        if ($categoryId = $request->query('category_id')) {
            $query->where('destination_category_id', $categoryId);
        }

        // Filter by category slug
        if ($categorySlug = $request->query('category_slug')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        // Filter by crowd level (requires calculating prediction for each)
        if ($crowdLevel = $request->query('crowd_level')) {
            $query->whereHas('crowdPredictions', function ($q) use ($crowdLevel) {
                $q->where('crowd_level', $crowdLevel)
                  ->where('method', 'rule_based')
                  ->whereDate('prediction_date', now()->format('Y-m-d'));
            });
        }

        $limit = min((int) ($request->query('limit', 15)), 50);

        $destinations = $query->orderBy('name')->paginate($limit);

        return response()->json([
            'success' => true,
            'message' => 'Destinations retrieved successfully',
            'data' => DestinationResource::collection($destinations),
            'meta' => [
                'current_page' => $destinations->currentPage(),
                'last_page' => $destinations->lastPage(),
                'per_page' => $destinations->perPage(),
                'total' => $destinations->total(),
            ],
        ]);
    }

    /**
     * GET /api/destinations/{destination}
     *
     * Show destination detail with events, reviews, and crowd status.
     */
    public function show(Destination $destination): JsonResponse
    {
        $destination->load(['category', 'events', 'reviews']);

        $crowdStatus = $this->crowdService->predict($destination);

        $resource = (new DestinationDetailResource($destination))
            ->additional(['crowd_status' => $crowdStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Destination detail retrieved successfully',
            'data' => $resource,
        ]);
    }

    /**
     * GET /api/destinations/{destination}/crowd-status
     *
     * Get rule-based crowd prediction for a specific destination.
     */
    public function crowdStatus(Request $request, Destination $destination): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'nullable|date',
            'hour' => 'nullable|integer|min:0|max:23',
        ]);

        $prediction = $this->crowdService->predict(
            destination: $destination,
            date: $validated['date'] ?? null,
            hour: isset($validated['hour']) ? (int) $validated['hour'] : null,
        );

        return response()->json([
            'success' => true,
            'message' => 'Crowd status generated successfully',
            'data' => $prediction,
        ]);
    }

    /**
     * GET /api/destinations/{destination}/recommendations
     *
     * Get alternative destination recommendations based on crowd level.
     */
    public function recommendations(Request $request, Destination $destination): JsonResponse
    {
        $date = $request->query('date');
        $hour = $request->query('hour') !== null ? (int) $request->query('hour') : null;
        $limit = min((int) ($request->query('limit', 5)), 20);

        // Get all other active destinations
        $candidates = Destination::with('category')
            ->where('is_active', true)
            ->where('id', '!=', $destination->id)
            ->get();

        // Score and rank each candidate
        $scored = $candidates->map(function (Destination $candidate) use ($date, $hour, $destination) {
            $prediction = $this->crowdService->predict($candidate, $date, $hour);

            $score = 0;

            // Prefer same category
            if ($candidate->destination_category_id === $destination->destination_category_id) {
                $score += 30;
            }

            // Prefer lower crowd
            $crowdPenalty = match ($prediction['crowd_level']) {
                'low' => 0,
                'moderate' => 10,
                'high' => 25,
                'packed' => 50,
                default => 10,
            };
            $score += (50 - $crowdPenalty);

            // Prefer destinations with reasonable capacity
            if ($candidate->max_capacity > 0) {
                $score += 10;
            }

            // Prefer destinations with reviews (social proof)
            $reviewCount = $candidate->reviews()->count();
            $score += min($reviewCount * 2, 10);

            return [
                'destination' => new DestinationResource($candidate),
                'crowd_status' => $prediction,
                'relevance_score' => $score,
            ];
        });

        // Filter out packed destinations and sort by score
        $recommendations = $scored
            ->filter(fn ($item) => $item['crowd_status']['crowd_level'] !== 'packed')
            ->sortByDesc('relevance_score')
            ->take($limit)
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Alternative destinations retrieved successfully',
            'data' => $recommendations,
        ]);
    }
}
