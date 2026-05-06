<?php

namespace App\Models;

use App\Models\Scopes\LocationScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryStock extends Model
{
    protected $fillable = [
        'inventory_item_id',
        'location_id',
        'quantity_on_hand',
        'status',
        'total_cost',
    ];

    protected $casts = [
        'quantity_on_hand' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'status' => 'string',
    ];

    protected $table = 'inventory_stock';

    protected static function booted(): void
    {
        static::addGlobalScope(new LocationScope());
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}
