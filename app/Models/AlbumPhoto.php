<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlbumPhoto extends Model
{
    protected $fillable = [
        'album_id',
        'image_path',
        'caption',
        'sort_order',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }
}
