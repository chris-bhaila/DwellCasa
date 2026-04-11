<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckOut extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'room_id',
        'checked_out_by',
        'checked_out_at',
        'late_check_out',
        'room_condition',
        'damage_notes',
        'extra_charges',
        'notes',
    ];

    protected $casts = [
        'checked_out_at' => 'datetime',
        'late_check_out' => 'boolean',
        'extra_charges' => 'decimal:2',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function checkedOutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }
}