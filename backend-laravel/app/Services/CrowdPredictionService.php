<?php

namespace App\Services;

use App\Models\CrowdPrediction;
use App\Models\Destination;
use App\Models\Event;
use App\Models\VisitorLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CrowdPredictionService
{
    protected const LEVELS = [
        ['max' => 0.30, 'level' => 'low',      'label' => 'Sepi'],
        ['max' => 0.60, 'level' => 'moderate',  'label' => 'Normal'],
        ['max' => 0.85, 'level' => 'high',      'label' => 'Ramai'],
        ['max' => 1.00, 'level' => 'packed',    'label' => 'Sangat Ramai'],
    ];

    protected const EVENT_ADJUSTMENTS = [
        'low'    => 0.05,
        'medium' => 0.10,
        'high'   => 0.20,
    ];

    protected const WEEKEND_ADJUSTMENT = 0.10;

    public function predict(Destination $destination, ?string $date = null, ?int $hour = null): array
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        $hour = $hour ?? (int) Carbon::now()->format('H');

        $visitorCount = $this->getVisitorCount($destination, $date, $hour);
        $maxCapacity = (int) $destination->max_capacity;

        $isWeekend = $date->isWeekend();
        $activeEvent = $this->getActiveEvent($destination, $date);
        $hasEvent = $activeEvent !== null;
        $eventImpact = $hasEvent ? ($activeEvent->expected_impact ?? 'medium') : 'none';

        $payload = [
            'destination_id' => $destination->id,
            'destination_name' => $destination->name,
            'category' => $destination->category->name ?? 'Unknown',
            'max_capacity' => $maxCapacity,
            'day_of_week' => $date->dayOfWeekIso - 1,
            'month' => $date->month,
            'hour' => $hour,
            'is_weekend' => $isWeekend,
            'is_holiday' => false,
            'has_event' => $hasEvent,
            'event_impact' => $eventImpact,
            'weather' => 'unknown',
            'temperature' => null,
            'rain_level' => null,
            'visitor_count' => $visitorCount,
            'prediction_date' => $date->format('Y-m-d'),
            'prediction_hour' => $hour,
        ];

        $aiService = app(AiServiceClient::class);
        $useMl = config('services.ai_service.use_ml', true);

        // 1. Try FastAPI ML
        if ($useMl) {
            $mlResult = $aiService->predictCrowdMl($payload);
            if ($mlResult) {
                $mlResult['prediction_date'] = $payload['prediction_date'];
                $mlResult['prediction_hour'] = $payload['prediction_hour'];
                $mlResult['visitor_count'] = $visitorCount;
                $this->savePrediction($mlResult);
                return $mlResult;
            }
        }

        // 2. Try FastAPI Rule-based fallback
        $rbResult = $aiService->predictCrowdRuleBased([
            'destination_id' => $destination->id,
            'destination_name' => $destination->name,
            'max_capacity' => $maxCapacity,
            'visitor_count' => $visitorCount,
            'prediction_date' => $payload['prediction_date'],
            'prediction_hour' => $hour,
            'is_weekend' => $isWeekend,
            'has_event' => $hasEvent,
            'event_impact' => $eventImpact === 'none' ? null : $eventImpact,
            'weather' => null,
        ]);

        if ($rbResult) {
            $rbResult['method'] = 'ai_service_rule_based';
            $this->savePrediction($rbResult);
            return $rbResult;
        }

        // 3. Fallback to Internal Rule-based
        Log::warning('AI Service failed. Falling back to internal rule-based prediction.');
        return $this->predictInternalRuleBased($destination, $date, $hour, $visitorCount, $maxCapacity, $isWeekend, $hasEvent, $eventImpact);
    }

    protected function predictInternalRuleBased(Destination $destination, Carbon $date, int $hour, int $visitorCount, int $maxCapacity, bool $isWeekend, bool $hasEvent, string $eventImpact): array
    {
        $crowdScore = ($maxCapacity > 0) ? ($visitorCount / $maxCapacity) : 0.0;

        if ($isWeekend) {
            $crowdScore += self::WEEKEND_ADJUSTMENT;
        }

        if ($hasEvent && isset(self::EVENT_ADJUSTMENTS[$eventImpact])) {
            $crowdScore += self::EVENT_ADJUSTMENTS[$eventImpact];
        }

        $crowdScore = min(round($crowdScore, 2), 1.00);

        [$crowdLevel, $crowdLabel] = $this->getCrowdLevel($crowdScore);

        $result = [
            'destination_id' => $destination->id,
            'prediction_date' => $date->format('Y-m-d'),
            'prediction_hour' => $hour,
            'visitor_count' => $visitorCount,
            'max_capacity' => $maxCapacity,
            'crowd_score' => $crowdScore,
            'crowd_level' => $crowdLevel,
            'crowd_label' => $crowdLabel,
            'method' => 'rule_based',
            'factors' => [
                'is_weekend' => $isWeekend,
                'has_event' => $hasEvent,
                'event_impact' => $eventImpact === 'none' ? null : $eventImpact,
            ],
        ];

        $this->savePrediction($result);

        return $result;
    }

    protected function getVisitorCount(Destination $destination, Carbon $date, int $hour): int
    {
        $log = VisitorLog::where('destination_id', $destination->id)
            ->whereDate('visit_date', $date)
            ->where('visit_hour', $hour)
            ->first();

        if ($log) {
            return (int) $log->visitor_count;
        }

        $dailyAvg = VisitorLog::where('destination_id', $destination->id)
            ->whereDate('visit_date', $date)
            ->avg('visitor_count');

        return (int) ($dailyAvg ?? 0);
    }

    protected function getActiveEvent(Destination $destination, Carbon $date): ?Event
    {
        return Event::where('destination_id', $destination->id)
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->orderByRaw("CASE expected_impact WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 ELSE 4 END")
            ->first();
    }

    protected function getCrowdLevel(float $score): array
    {
        foreach (self::LEVELS as $level) {
            if ($score <= $level['max']) {
                return [$level['level'], $level['label']];
            }
        }
        return ['packed', 'Sangat Ramai'];
    }

    protected function savePrediction(array $result): void
    {
        CrowdPrediction::updateOrCreate(
            [
                'destination_id' => $result['destination_id'],
                'prediction_date' => $result['prediction_date'],
                'prediction_hour' => $result['prediction_hour'],
                'method' => $result['method'],
            ],
            [
                'predicted_count' => $result['predicted_count'] ?? $result['visitor_count'],
                'crowd_score' => $result['crowd_score'],
                'crowd_level' => $result['crowd_level'],
                'confidence_score' => $result['confidence_score'] ?? 0.70,
                'model_version' => $result['model_version'] ?? 'rule-based-v1',
            ]
        );
    }
}
