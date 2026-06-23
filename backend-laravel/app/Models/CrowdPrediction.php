<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'destination_id',
    'prediction_date',
    'prediction_hour',
    'predicted_count',
    'crowd_score',
    'crowd_level',
    'confidence_score',
    'method',
    'model_version',
])]
class CrowdPrediction extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'prediction_date' => 'date',
            'prediction_hour' => 'integer',
            'predicted_count' => 'integer',
            'crowd_score' => 'decimal:2',
            'confidence_score' => 'decimal:2',
        ];
    }

    /**
     * Get the destination this prediction is for.
     *
     * @return BelongsTo<Destination, $this>
     */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }
}
