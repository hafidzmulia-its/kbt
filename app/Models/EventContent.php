<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventContent extends Model
{
    protected $fillable = [
        'event_id',
        'opening_text',
        'invitation_text',
        'closing_text',
        'bride_bio',
        'groom_bio',
        'love_story_json',
        'no_gift_message',
    ];

    protected function casts(): array
    {
        return [
            'love_story_json' => 'array',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
