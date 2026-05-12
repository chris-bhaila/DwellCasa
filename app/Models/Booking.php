<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\LocationScope;
use App\Models\User;

class Booking extends Model
{
    use HasFactory, SoftDeletes, MassPrunable;

    public function prunable(): \Illuminate\Database\Eloquent\Builder
    {
        return static::onlyTrashed()->where('deleted_at', '<=', now()->subDays(90));
    }

    protected $fillable = [
        'booking_ref',
        'guest_id',
        'room_id',
        'room_type_id',
        'booking_inquiry_id',
        'check_in_date',
        'check_out_date',
        'num_guests',
        'stay_type',
        'rate_per_night',
        'rate_per_month',
        'total_amount',
        'discount',
        'deposit_amount',
        'amount_paid',
        'refund_amount',
        'refunded_at',
        'status',
        'payment_status',
        'checked_in_at',
        'checked_out_at',
        'special_requests',
        'admin_notes',
        'review_token',
        'location_id',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'rate_per_night' => 'decimal:2',
        'rate_per_month' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'refund_amount' => 'decimal:2',
        'refunded_at'   => 'datetime',
    ];

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function checkIn(): HasOne
    {
        return $this->hasOne(CheckIn::class);
    }

    public function checkOut(): HasOne
    {
        return $this->hasOne(CheckOut::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function isEditableBy(User $user): bool
    {
        if ($this->status !== 'checked_out') return true;
        if (!$this->checked_out_at) return true;

        $hours = $user->hasAnyRole(['admin', 'super_admin']) ? 72 : 24;
        return $this->checked_out_at->diffInHours(now()) <= $hours;
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new LocationScope());
    }
}
