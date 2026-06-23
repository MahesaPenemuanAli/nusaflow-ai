<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rating = fake()->numberBetween(1, 5);

        return [
            'user_id' => User::factory(),
            'destination_id' => Destination::factory(),
            'rating' => $rating,
            'comment' => fake()->optional(0.7)->sentence(),
            'sentiment' => match (true) {
                $rating >= 4 => 'positive',
                $rating === 3 => 'neutral',
                default => 'negative',
            },
            'visited_at' => fake()->optional(0.8)->dateTimeBetween('-90 days', 'now')?->format('Y-m-d'),
        ];
    }
}
