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
    ];

    protected $casts = [
        'action' => 'string',
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
}
