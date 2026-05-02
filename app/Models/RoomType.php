<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use App\Models\Room;
use App\Models\Scopes\LocationScope;

class RoomType extends Model
{
    use HasFactory, SoftDeletes, MassPrunable;

    public function prunable(): \Illuminate\Database\Eloquent\Builder
    {
        return static::onlyTrashed()->where('deleted_at', '<=', now()->subDays(90));
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'max_occupancy',
        'price_per_night',
        'price_per_month',
        'size_sqft',
        'is_active',
        'sort_order',
        'is_standalone',
        'thumbnail',
        'location_id',
    ];

    protected $casts = [
        'price_per_night' => 'decimal:2',
        'price_per_month' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'amenity_room_type');
    }

    public function galleryImages(): MorphMany
    {
        return $this->morphMany(GalleryImage::class, 'imageable');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->slug = static::generateUniqueSlug($model->name, $model->location_id);
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') || empty($model->slug)) {
                $model->slug = static::generateUniqueSlug($model->name, $model->location_id, $model->id);
            }
        });

        static::saved(function ($model) {
            if (request()->has('amenities')) {
                $model->amenities()->sync(request('amenities', []));
            }
        });

        // Null out room_type_id on associated rooms before deleting so rooms
        // remain and can be reassigned, rather than being cascade-deleted.
        // withoutGlobalScopes() bypasses LocationScope to catch all rooms.
        static::deleting(function (RoomType $model) {
            Room::withoutGlobalScopes()->where('room_type_id', $model->id)->update(['room_type_id' => null]);
        });

        static::addGlobalScope(new LocationScope());
    }

    protected static function generateUniqueSlug(string $name, ?int $locationId, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (
            static::withoutGlobalScopes()
            ->where('slug', $slug)
            ->where('location_id', $locationId)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }
}
