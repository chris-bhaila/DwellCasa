<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Scopes\LocationScope;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'avatar',
        'location_id',
        'booking_id',
        'room_type_id',
        'guest_id',
        'rating',
        'title',
        'body',
        'type',
        'status',
        'review_token',
        'token_used',
    ];

    protected $casts = [
        'rating'     => 'integer',
        'token_used' => 'boolean',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    // Scope for approved reviews only
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Scope for hotel reviews
    public function scopeHotel($query)
    {
        return $query->where('type', 'hotel');
    }

    // Scope for room type reviews
    public function scopeRoomType($query)
    {
        return $query->where('type', 'room_type');
    }
    
    protected static function booted(): void
    {
        static::addGlobalScope(new LocationScope());
    }
}
