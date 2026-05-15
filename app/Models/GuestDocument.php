<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuestDocument extends Model
{
    protected $fillable = [
        'guest_id',
        'document_type',
        'id_number',
        'nationality',
        'date_of_birth',
        'photo',
        'notes',
        'uploaded_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
