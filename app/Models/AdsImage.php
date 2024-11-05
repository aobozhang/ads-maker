<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdsImage extends Model
{
    use SoftDeletes;

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
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
