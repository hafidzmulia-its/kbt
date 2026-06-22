<?php

namespace App\Services;

use App\Models\Event;
use App\Models\GiftContribution;
use App\Models\Guest;
use App\Models\GuestInvitation;

class GiftService
{
    public function intentFor(Event $event, Guest $guest, GuestInvitation $invitation): GiftContribution
    {
        return GiftContribution::firstOrCreate(
            [
                'event_id' => $event->id,
                'guest_id' => $guest->id,
                'guest_invitation_id' => $invitation->id,
            ],
            [
                'reference_code' => 'GFT-'.strtoupper(substr(md5($event->id.'-'.$guest->id), 0, 10)),
                'status' => 'intent_created',
            ]
        );
    }
}
