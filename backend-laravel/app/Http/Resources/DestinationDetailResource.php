<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DestinationDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'description' => $this->description,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'max_capacity' => $this->max_capacity,
            'opening_hour' => $this->opening_hour,
            'closing_hour' => $this->closing_hour,
            'ticket_price' => $this->ticket_price,
            'image' => $this->image,
            'is_active' => $this->is_active,
            'events' => $this->whenLoaded('events', fn () => $this->events->map(fn ($event) => [
                'id' => $event->id,
                'name' => $event->name,
                'description' => $event->description,
                'start_date' => $event->start_date?->format('Y-m-d'),
                'end_date' => $event->end_date?->format('Y-m-d'),
                'expected_impact' => $event->expected_impact,
            ])),
            'latest_reviews' => $this->whenLoaded('reviews', fn () => $this->reviews
                ->sortByDesc('created_at')
                ->take(5)
                ->values()
                ->map(fn ($review) => [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'sentiment' => $review->sentiment,
                    'visited_at' => $review->visited_at?->format('Y-m-d'),
                    'created_at' => $review->created_at?->format('Y-m-d H:i'),
                ])),
            'crowd_status' => $this->when(
                isset($this->additional['crowd_status']),
                fn () => $this->additional['crowd_status'] ?? null,
            ),
        ];
    }
}
