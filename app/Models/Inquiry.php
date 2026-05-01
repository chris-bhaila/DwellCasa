<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    
    protected static function booted(): void
    {
        static::addGlobalScope(new LocationScope());
    }
}
