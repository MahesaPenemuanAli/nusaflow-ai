<?php

namespace App\Services;

use App\Models\CrowdPrediction;
use App\Models\Destination;
use App\Models\Event;
use App\Models\VisitorLog;
use Carbon\Carbon;

class CrowdPredictionService
{
    /**
     * Crowd level thresholds and labels.
     */
    protected const LEVELS = [
        ['max' => 0.30, 'level' => 'low',      'label' => 'Sepi'],
        ['max' => 0.60, 'level' => 'moderate',  'label' => 'Normal'],
        ['max' => 0.85, 'level' => 'high',      'label' => 'Ramai'],
        ['max' => 1.00, 'level' => 'packed',    'label' => 'Sangat Ramai'],
    ];

    /**
     * Event impact adjustments.
     */
    protected const EVENT_ADJUSTMENTS = [
        'low'    => 0.05,
        'medium' => 0.10,
        'high'   => 0.20,
    ];

    /**
     * Weekend adjustment value.
     */
    protected const WEEKEND_ADJUSTMENT = 0.10;

    /**
     * Generate a rule-based crowd prediction for a destination.
     */
    public function predict(Destination $destination, ?string $date = null, ?int $hour = null): array
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        $hour = $hour ?? (int) Carbon::now()->format('H');

        $visitorCount = $this->getVisitorCount($destination, $date, $hour);
        $maxCapacity = (int) $destination->max_capacity;

        // Calculate base crowd score
        $crowdScore = ($maxCapacity > 0) ? ($visitorCount / $maxCapacity) : 0.0;

        // Determine adjustment factors
        $isWeekend = $date->isWeekend();
        $activeEvent = $this->getActiveEvent($destination, $date);
        $hasEvent = $activeEvent !== null;
        $eventImpact = $hasEvent ? ($activeEvent->expected_impact ?? 'medium') : null;

        // Apply weekend adjustment
        if ($isWeekend) {
            $crowdScore += self::WEEKEND_ADJUSTMENT;
        }

        // Apply event adjustment
        if ($hasEvent && isset(self::EVENT_ADJUSTMENTS[$eventImpact])) {
            $crowdScore += self::EVENT_ADJUSTMENTS[$eventImpact];
        }

        // Cap score at 1.00
        $crowdScore = min(round($crowdScore, 2), 1.00);

        // Determine crowd level and label
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
                'event_impact' => $eventImpact,
            ],
        ];

        // Persist prediction
        $this->savePrediction($result);

        return $result;
    }

    /**
     * Get visitor count for a given destination, date, and hour.
     * Falls back to daily average if hourly data is unavailable.
     */
    protected function getVisitorCount(Destination $destination, Carbon $date, int $hour): int
    {
        // Try exact hour match first
        $log = VisitorLog::where('destination_id', $destination->id)
            ->whereDate('visit_date', $date)
            ->where('visit_hour', $hour)
            ->first();

        if ($log) {
            return (int) $log->visitor_count;
        }

        // Fall back to daily average
        $dailyAvg = VisitorLog::where('destination_id', $destination->id)
            ->whereDate('visit_date', $date)
            ->avg('visitor_count');

        return (int) ($dailyAvg ?? 0);
    }

    /**
     * Get the most impactful active event at this destination on the given date.
     */
    protected function getActiveEvent(Destination $destination, Carbon $date): ?Event
    {
        return Event::where('destination_id', $destination->id)
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->orderByRaw("CASE expected_impact WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 ELSE 4 END")
            ->first();
    }

    /**
     * Determine the crowd level and label from the score.
     *
     * @return array{0: string, 1: string}
     */
    protected function getCrowdLevel(float $score): array
    {
        foreach (self::LEVELS as $level) {
            if ($score <= $level['max']) {
                return [$level['level'], $level['label']];
            }
        }

        // Score exceeds 1.00 (shouldn't happen after capping, but just in case)
        return ['packed', 'Sangat Ramai'];
    }

    /**
     * Persist the prediction result to the crowd_predictions table.
     */
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
                'predicted_count' => $result['visitor_count'],
                'crowd_score' => $result['crowd_score'],
                'crowd_level' => $result['crowd_level'],
                'confidence_score' => 0.70,
                'model_version' => 'rule-based-v1',
            ]
        );
    }
}
