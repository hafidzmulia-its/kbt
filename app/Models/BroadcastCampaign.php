<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BroadcastCampaign extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'broadcast_template_id',
        'name',
        'channel',
        'message_template',
        'status',
        'country_code',
        'delay',
        'connect_only',
        'scheduled_at',
        'targeting_json',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'connect_only' => 'boolean',
            'targeting_json' => 'array',
            'cancelled_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(BroadcastTemplate::class, 'broadcast_template_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(BroadcastLog::class, 'campaign_id');
    }
}
