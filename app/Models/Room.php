<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\LocationScope;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_type_id',
        'room_name',
        'room_number',
        'floor',
        'status',
        'notes',
        'location_id',
    ];

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }


    protected static function booted()
    {
        static::saved(function ($model) {
            if (request()->has('amenities')) {
                $model->amenities()->sync(request('amenities', []));
            }
        });

        static::addGlobalScope(new LocationScope());
    }
}
