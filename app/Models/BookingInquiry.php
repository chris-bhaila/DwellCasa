<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BookingInquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'inquiry_ref',
        'guest_id',
        'room_type_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'check_in_date',
        'check_out_date',
        'num_guests',
        'stay_type',
        'message',
        'status',
        'admin_notes',
        'responded_at',
        'ip_address',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'responded_at' => 'datetime',
    ];

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function booking(): HasOne
    {
        return $this->hasOne(Booking::class);
    }
}