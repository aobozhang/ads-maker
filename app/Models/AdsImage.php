<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdsImage extends Model
{
    protected $fillable = [
        'name',
        'user_id',
        'path',
        'url',
        'thumb',
        'config',
        'payload',
    ];

    protected $casts = [
        'config'  => 'array',
        'payload' => 'array',
    ];

    /**
     * Get the user that owns the AdsImage
     *
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
