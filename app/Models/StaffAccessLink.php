<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaffAccessLink extends Model
{
    protected $fillable = [
        'event_id',
        'token_hash',
        'label',
        'permissions_json',
        'revoked_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'permissions_json' => 'array',
            'revoked_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(AttendanceCheckin::class);
    }

    public function isRevoked(): bool
    {
        return (bool) $this->revoked_at;
    }
}
