<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\GuestDocument;
use App\Models\Scopes\LocationScope;

class Guest extends Model
{
    use HasFactory, SoftDeletes, MassPrunable;

    public function prunable(): \Illuminate\Database\Eloquent\Builder
    {
        return static::onlyTrashed()->where('deleted_at', '<=', now()->subDays(90));
    }

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'nationality',
        'id_type',
        'id_number',
        'address',
        'notes',
        'location_id',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(GuestDocument::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new LocationScope());
    }
}
