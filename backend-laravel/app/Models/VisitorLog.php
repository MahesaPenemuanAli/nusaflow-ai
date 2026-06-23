<?php

namespace App\Models;

use Database\Factories\VisitorLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'destination_id',
    'visit_date',
    'visit_hour',
    'visitor_count',
    'weather',
    'source',
    'notes',
])]
class VisitorLog extends Model
{
    /** @use HasFactory<VisitorLogFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'visit_hour' => 'integer',
            'visitor_count' => 'integer',
        ];
    }

    /**
     * Get the destination this log belongs to.
     *
     * @return BelongsTo<Destination, $this>
     */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }
}
