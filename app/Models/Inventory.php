<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'name',
        'category',
        'stock',
        'unit',
        'unit_condition',
        'minimum_stock',
        'description',
        'status',
        'image',
        'unit_price',
    ];

    protected $casts = [
        'stock'         => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'unit_price'    => 'decimal:2',
    ];

    // Auto-calculate status based on stock levels
    public static function boot(): void
    {
        parent::boot();

        static::saving(function ($inventory) {
            if ($inventory->stock <= 0) {
                $inventory->status = 'out_of_stock';
            } elseif ($inventory->stock <= $inventory->minimum_stock) {
                $inventory->status = 'low_stock';
            } else {
                $inventory->status = 'available';
            }
        });
    }
}