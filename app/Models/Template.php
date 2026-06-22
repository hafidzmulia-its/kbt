<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    protected $fillable = [
        'name',
        'code',
        'category',
        'preview_image_path',
        'status',
        'is_premium',
    ];

    protected function casts(): array
    {
        return [
            'is_premium' => 'boolean',
        ];
    }

    public function versions(): HasMany
    {
        return $this->hasMany(TemplateVersion::class);
    }

    public function getPreviewUrlAttribute(): ?string
    {
        if (! $this->preview_image_path) {
            return null;
        }

        return asset($this->preview_image_path);
    }
}
