<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceCheckin extends Model
{
    protected $fillable = [
        'event_id',
        'guest_id',
        'guest_invitation_id',
        'checked_in_at',
        'checked_in_by_type',
        'staff_access_link_id',
        'device_label',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
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

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(GuestInvitation::class, 'guest_invitation_id');
    }

    public function staffAccessLink(): BelongsTo
    {
        return $this->belongsTo(StaffAccessLink::class);
    }
}
