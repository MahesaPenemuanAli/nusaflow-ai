<?php

namespace App\Models;

use Database\Factories\DestinationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'destination_category_id',
    'name',
    'slug',
    'description',
    'address',
    'latitude',
    'longitude',
    'max_capacity',
    'opening_hour',
    'closing_hour',
    'ticket_price',
    'image',
    'is_active',
])]
class Destination extends Model
{
    /** @use HasFactory<DestinationFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'max_capacity' => 'integer',
            'ticket_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the category of this destination.
     *
     * @return BelongsTo<DestinationCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(DestinationCategory::class, 'destination_category_id');
    }

    /**
     * Get the visitor logs for this destination.
     *
     * @return HasMany<VisitorLog, $this>
     */
    public function visitorLogs(): HasMany
    {
        return $this->hasMany(VisitorLog::class);
    }

    /**
     * Get the events at this destination.
     *
     * @return HasMany<Event, $this>
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the reviews for this destination.
     *
     * @return HasMany<Review, $this>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the crowd predictions for this destination.
     *
     * @return HasMany<CrowdPrediction, $this>
     */
    public function crowdPredictions(): HasMany
    {
        return $this->hasMany(CrowdPrediction::class);
    }

    /**
     * Get the checkins at this destination.
     *
     * @return HasMany<Checkin, $this>
     */
    public function checkins(): HasMany
    {
        return $this->hasMany(Checkin::class);
    }
}
