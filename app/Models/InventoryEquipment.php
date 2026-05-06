<?php

namespace App\Models;

use App\Models\Scopes\LocationScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryEquipment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'inventory_item_id',
        'location_id',
        'serial_number',
        'current_room_id',
        'condition',
        'status',
        'purchased_at',
        'purchase_cost',
        'notes',
    ];

    protected $casts = [
        'condition' => 'string',
        'status' => 'string',
        'purchased_at' => 'date',
        'purchase_cost' => 'decimal:2',
    ];
    protected $table = 'inventory_equipment';

    protected static function booted(): void
    {
        static::addGlobalScope(new LocationScope());
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function currentRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'current_room_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(InventoryLog::class, 'inventory_equipment_id');
    }
}
