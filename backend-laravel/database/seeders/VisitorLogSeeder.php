<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\VisitorLog;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class VisitorLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $destinations = Destination::all();
        $weatherOptions = ['cerah', 'berawan', 'mendung', 'hujan ringan', 'hujan lebat'];

        foreach ($destinations->take(5) as $destination) {
            // Generate visitor logs for the past 7 days
            for ($day = 6; $day >= 0; $day--) {
                $date = Carbon::now()->subDays($day);
                $isWeekend = $date->isWeekend();

                // Generate 3 hourly logs per day (morning, midday, afternoon)
                foreach ([9, 12, 15] as $hour) {
                    $baseCount = $isWeekend
                        ? fake()->numberBetween(150, 400)
                        : fake()->numberBetween(50, 200);

                    // Midday tends to be busier
                    if ($hour === 12) {
                        $baseCount = (int) ($baseCount * 1.3);
                    }

                    VisitorLog::create([
                        'destination_id' => $destination->id,
                        'visit_date' => $date->format('Y-m-d'),
                        'visit_hour' => $hour,
                        'visitor_count' => $baseCount,
                        'weather' => fake()->randomElement($weatherOptions),
                        'source' => 'admin_input',
                        'notes' => null,
                    ]);
                }
            }
        }
    }
}
