<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\DestinationCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Destination>
 */
class DestinationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company() . ' ' . fake()->randomElement(['Beach', 'Park', 'Temple', 'Garden', 'Lake']);

        return [
            'destination_category_id' => DestinationCategory::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(2),
            'address' => fake()->address(),
            'latitude' => fake()->latitude(-8.9, -6.0),
            'longitude' => fake()->longitude(105.0, 115.5),
            'max_capacity' => fake()->numberBetween(100, 10000),
            'opening_hour' => '08:00',
            'closing_hour' => '17:00',
            'ticket_price' => fake()->randomElement([0, 5000, 10000, 15000, 25000, 50000]),
            'image' => null,
            'is_active' => true,
        ];
    }
}
