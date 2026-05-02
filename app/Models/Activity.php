<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    protected $fillable = [
        'log_name', 'description', 'subject_type', 'subject_id',
        'causer_type', 'causer_id', 'properties', 'event',
        'batch_uuid', 'location_id',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    protected static function booted(): void
    {
        // Whenever an activity is created, promote location_id from the
        // properties JSON payload to the dedicated column so that admin-level
        // filtering can use a direct indexed column query instead of a JSON path.
        static::creating(function (self $activity) {
            if ($activity->properties?->has('location_id')) {
                $activity->location_id = $activity->properties->get('location_id');
            }
        });
    }
}
