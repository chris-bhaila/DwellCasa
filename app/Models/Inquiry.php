<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Scopes\LocationScope;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'inquiry_type',
        'message',
        'status',
        'location_id',

    ];

    protected $casts = [
        'inquiry_type' => 'string',
    ];
    
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new LocationScope());
    }
}
