<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdsItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'user_id',
        'type',
        'path',
        'url',
        'tags',
        'payload',
    ];

    protected $casts = [
        'tags'    => 'array',
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
