<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\VisitorLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VisitorLog>
 */
class VisitorLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'destination_id' => Destination::factory(),
            'visit_date' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'visit_hour' => fake()->numberBetween(6, 21),
            'visitor_count' => fake()->numberBetween(10, 500),
            'weather' => fake()->randomElement(['cerah', 'berawan', 'hujan ringan', 'hujan lebat', 'mendung']),
            'source' => 'admin_input',
            'notes' => null,
        ];
    }
}
