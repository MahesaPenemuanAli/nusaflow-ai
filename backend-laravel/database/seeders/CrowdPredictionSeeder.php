<?php

namespace Database\Seeders;

use App\Models\CrowdPrediction;
use App\Models\Destination;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CrowdPredictionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $destinations = Destination::all();

        foreach ($destinations->take(5) as $destination) {
            // Generate predictions for the next 3 days
            for ($day = 0; $day < 3; $day++) {
                $date = Carbon::now()->addDays($day);
                $isWeekend = $date->isWeekend();

                $predictedCount = $isWeekend
                    ? fake()->numberBetween(200, 500)
                    : fake()->numberBetween(80, 250);

                $maxCapacity = $destination->max_capacity ?: 1000;
                $crowdScore = round(($predictedCount / $maxCapacity) * 100, 2);

                $crowdLevel = match (true) {
                    $crowdScore >= 80 => 'packed',
                    $crowdScore >= 60 => 'high',
                    $crowdScore >= 30 => 'moderate',
                    default => 'low',
                };

                CrowdPrediction::create([
                    'destination_id' => $destination->id,
                    'prediction_date' => $date->format('Y-m-d'),
                    'prediction_hour' => null,
                    'predicted_count' => $predictedCount,
                    'crowd_score' => $crowdScore,
                    'crowd_level' => $crowdLevel,
                    'confidence_score' => fake()->randomFloat(2, 55, 85),
                    'method' => 'rule_based',
                    'model_version' => 'rule-based-v1',
                ]);
            }
        }
    }
}
