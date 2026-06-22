<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MusicAsset extends Model
{
    protected $fillable = [
        'title',
        'artist',
        'source_name',
        'source_url',
        'license_note',
        'audio_path',
        'external_url',
        'duration_seconds',
        'status',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function getResolvedUrlAttribute(): ?string
    {
        if ($this->external_url) {
            return $this->external_url;
        }

        if (! $this->audio_path) {
            return null;
        }

        if (Str::startsWith($this->audio_path, ['http://', 'https://'])) {
            return $this->audio_path;
        }

        if (Str::startsWith($this->audio_path, 'music/')) {
            return asset($this->audio_path);
        }

        return asset('storage/'.$this->audio_path);
    }
}
