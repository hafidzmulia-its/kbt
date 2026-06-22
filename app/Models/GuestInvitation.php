<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuestInvitation extends Model
{
    protected $fillable = [
        'event_id',
        'guest_id',
        'public_token_hash',
        'public_token_last4',
        'checkin_token_hash',
        'checkin_token_last4',
        'invitation_url_cached',
        'gift_url_cached',
        'checkin_url_cached',
        'token_regenerated_at',
        'last_opened_at',
        'open_count',
    ];

    protected function casts(): array
    {
        return [
            'token_regenerated_at' => 'datetime',
            'last_opened_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(Rsvp::class);
    }

    public function attendanceCheckins(): HasMany
    {
        return $this->hasMany(AttendanceCheckin::class);
    }

    public function giftContributions(): HasMany
    {
        return $this->hasMany(GiftContribution::class);
    }
}
