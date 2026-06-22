<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FonnteIntegration extends Model
{
    protected $fillable = [
        'user_id',
        'account_token',
        'device_token',
        'device_token_last4',
        'device_name',
        'device_number',
        'device_status',
        'package_name',
        'quota',
        'expires_label',
        'default_country_code',
        'last_error_message',
        'verified_at',
        'is_enabled',
    ];

    protected function casts(): array
    {
        return [
            'account_token' => 'encrypted',
            'device_token' => 'encrypted',
            'verified_at' => 'datetime',
            'is_enabled' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasUsableDeviceToken(): bool
    {
        return $this->is_enabled && filled($this->device_token);
    }
}
