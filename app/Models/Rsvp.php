<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rsvp extends Model
{
    protected $fillable = [
        'event_id',
        'guest_id',
        'guest_invitation_id',
        'event_schedule_id',
        'name_snapshot',
        'phone_snapshot',
        'status',
        'pax_count',
        'message',
        'submitted_at',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
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

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(EventSchedule::class, 'event_schedule_id');
    }
}
