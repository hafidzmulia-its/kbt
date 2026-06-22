<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'status',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function broadcastCampaigns(): HasMany
    {
        return $this->hasMany(BroadcastCampaign::class);
    }

    public function broadcastTemplates(): HasMany
    {
        return $this->hasMany(BroadcastTemplate::class);
    }

    public function fonnteIntegration(): HasOne
    {
        return $this->hasOne(FonnteIntegration::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
