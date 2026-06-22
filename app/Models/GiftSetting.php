<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftSetting extends Model
{
    protected $fillable = [
        'event_id',
        'mode',
        'bank_name',
        'account_number',
        'account_holder',
        'static_qr_path',
        'no_gift_message',
        'instructions',
        'is_proof_upload_enabled',
    ];

    protected function casts(): array
    {
        return [
            'account_number' => 'encrypted',
            'is_proof_upload_enabled' => 'boolean',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
