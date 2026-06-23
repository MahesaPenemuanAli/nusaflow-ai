<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Event;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $destinations = Destination::all();

        $events = [
            [
                'name' => 'Festival Seni Pantai',
                'description' => 'Festival seni dan musik tahunan di area pantai dengan pertunjukan budaya.',
                'days_from_now' => 7,
                'duration_days' => 3,
                'expected_impact' => 'high',
            ],
            [
                'name' => 'Pameran Kuliner Tradisional',
                'description' => 'Pameran makanan tradisional dari berbagai daerah.',
                'days_from_now' => 14,
                'duration_days' => 2,
                'expected_impact' => 'medium',
            ],
            [
                'name' => 'Workshop Fotografi Alam',
                'description' => 'Workshop fotografi alam terbuka untuk umum.',
                'days_from_now' => 21,
                'duration_days' => 1,
                'expected_impact' => 'low',
            ],
            [
                'name' => 'Perayaan Hari Budaya Nasional',
                'description' => 'Perayaan besar dengan parade, tari, dan pertunjukan musik.',
                'days_from_now' => 30,
                'duration_days' => 5,
                'expected_impact' => 'high',
            ],
        ];

        foreach ($events as $index => $event) {
            $destination = $destinations[$index % $destinations->count()];
            $startDate = Carbon::now()->addDays($event['days_from_now']);

            Event::create([
                'destination_id' => $destination->id,
                'name' => $event['name'],
                'description' => $event['description'],
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $startDate->copy()->addDays($event['duration_days'] - 1)->format('Y-m-d'),
                'expected_impact' => $event['expected_impact'],
            ]);
        }
    }
}
