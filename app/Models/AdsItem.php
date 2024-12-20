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
        'remote',
        'tags',
        'status',
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
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
