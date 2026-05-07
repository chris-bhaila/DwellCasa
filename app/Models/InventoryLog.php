<?php

namespace App\Models;

use App\Models\Scopes\LocationScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryLog extends Model
{
    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'location_id',
        'inventory_item_id',
        'inventory_equipment_id',
        'action',
        'quantity',
        'room_id',
        'performed_by',
        'cost',
        'previous_condition',
        'new_condition',
        'notes',
        'corrected_log_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'action' => 'string', // restocked|used|assigned|returned|condition_changed|written_off|adjusted|corrected
        'quantity' => 'decimal:2',
        'cost' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new LocationScope());

        static::creating(function ($model) {
            $model->created_at = $model->created_at ?? now();
        });
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(InventoryEquipment::class, 'inventory_equipment_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function correctedLog(): BelongsTo
    {
        return $this->belongsTo(InventoryLog::class, 'corrected_log_id');
    }

    public function isWithinCorrectionWindow(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        $minutes = $user->hasAnyRole(['admin', 'super_admin']) ? 1440 : 30;

        return $this->created_at->diffInMinutes(now()) <= $minutes;
    }
}
