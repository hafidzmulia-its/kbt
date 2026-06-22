<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Guest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'event_id',
        'name',
        'phone',
        'guest_group_id',
        'group_name',
        'address_note',
        'max_pax',
        'status',
        'is_vip',
        'needs_physical_invitation',
    ];

    protected function casts(): array
    {
        return [
            'is_vip' => 'boolean',
            'needs_physical_invitation' => 'boolean',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function guestGroup(): BelongsTo
    {
        return $this->belongsTo(GuestGroup::class);
    }

    public function invitation(): HasOne
    {
        return $this->hasOne(GuestInvitation::class);
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

    public function getResolvedGroupNameAttribute(): ?string
    {
        return $this->guestGroup?->name ?: $this->group_name;
    }
}
