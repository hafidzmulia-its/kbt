<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'public_code',
        'couple_name_display',
        'bride_name',
        'groom_name',
        'status',
        'template_id',
        'music_asset_id',
        'is_rsvp_enabled',
        'is_comment_enabled',
        'is_gift_enabled',
        'is_guest_personalization_enabled',
        'published_at',
        'settings_json',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'settings_json' => 'array',
            'is_rsvp_enabled' => 'boolean',
            'is_comment_enabled' => 'boolean',
            'is_gift_enabled' => 'boolean',
            'is_guest_personalization_enabled' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function musicAsset(): BelongsTo
    {
        return $this->belongsTo(MusicAsset::class);
    }

    public function content(): HasOne
    {
        return $this->hasOne(EventContent::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(EventSchedule::class)->orderBy('sort_order');
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    public function guestGroups(): HasMany
    {
        return $this->hasMany(GuestGroup::class)->orderBy('sort_order')->orderBy('name');
    }

    public function guestInvitations(): HasMany
    {
        return $this->hasMany(GuestInvitation::class);
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(Rsvp::class);
    }

    public function attendanceCheckins(): HasMany
    {
        return $this->hasMany(AttendanceCheckin::class);
    }

    public function giftSetting(): HasOne
    {
        return $this->hasOne(GiftSetting::class);
    }

    public function giftContributions(): HasMany
    {
        return $this->hasMany(GiftContribution::class);
    }

    public function albums(): HasMany
    {
        return $this->hasMany(Album::class)->orderBy('sort_order');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest('submitted_at');
    }

    public function broadcastCampaigns(): HasMany
    {
        return $this->hasMany(BroadcastCampaign::class)->latest();
    }

    public function broadcastTemplates(): HasMany
    {
        return $this->hasMany(BroadcastTemplate::class)->latest();
    }

    public function staffAccessLinks(): HasMany
    {
        return $this->hasMany(StaffAccessLink::class)->latest();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }
}
