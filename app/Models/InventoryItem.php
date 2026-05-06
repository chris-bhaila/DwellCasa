<?php

namespace App\Models;

use App\Models\Scopes\LocationScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'location_id',
        'category_id',
        'name',
        'type',
        'unit',
        'minimum_stock',
        'description',
        'image',
    ];

    protected $casts = [
        'type' => 'string',
        'minimum_stock' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new LocationScope());
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function stock(): HasOne
    {
        return $this->hasOne(InventoryStock::class, 'inventory_item_id');
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(InventoryEquipment::class, 'inventory_item_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(InventoryLog::class, 'inventory_item_id');
    }
}
