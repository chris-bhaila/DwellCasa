<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class RoomType extends Model
{
    use HasFactory, SoftDeletes;

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

    public function bookingInquiries(): HasMany
    {
        return $this->hasMany(BookingInquiry::class);
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
            $model->slug = Str::slug($model->name);
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') || empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });

        static::saved(function ($model) {
            if (request()->has('amenities')) {
                $model->amenities()->sync(request('amenities', []));
            }
        });
    }
}
