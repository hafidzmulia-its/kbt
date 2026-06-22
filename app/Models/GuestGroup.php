<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuestGroup extends Model
{
    protected $fillable = [
        'event_id',
        'name',
        'slug',
        'description',
        'sort_order',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(EventSchedule::class, 'event_schedule_guest_groups')
            ->withPivot('event_id', 'allow_rsvp')
            ->withTimestamps()
            ->orderBy('event_schedules.sort_order');
    }
}
