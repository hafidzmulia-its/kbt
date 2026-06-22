<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EventSchedule extends Model
{
    protected $fillable = [
        'event_id',
        'label',
        'date',
        'start_time',
        'end_time',
        'timezone',
        'venue_name',
        'address',
        'maps_url',
        'latitude',
        'longitude',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function guestGroups(): BelongsToMany
    {
        return $this->belongsToMany(GuestGroup::class, 'event_schedule_guest_groups')
            ->withPivot('event_id', 'allow_rsvp')
            ->withTimestamps()
            ->orderBy('guest_groups.name');
    }
}
