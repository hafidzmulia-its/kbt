<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Guest;
use App\Models\GuestInvitation;
use App\Models\Rsvp;

class RsvpService
{
    public function __construct(
        private readonly GuestScheduleAccessService $guestScheduleAccessService,
    ) {
    }

    public function submit(
        Event $event,
        array $data,
        ?Guest $guest = null,
        ?GuestInvitation $invitation = null,
        string $source = 'general_link'
    ): Rsvp {
        $schedule = $this->guestScheduleAccessService->validateScheduleSelection(
            $event,
            $guest,
            isset($data['event_schedule_id']) ? (int) $data['event_schedule_id'] : null
        );

        return Rsvp::create([
            'event_id' => $event->id,
            'guest_id' => $guest?->id,
            'guest_invitation_id' => $invitation?->id,
            'event_schedule_id' => $schedule?->id,
            'name_snapshot' => $guest?->name ?? $data['name'],
            'phone_snapshot' => $guest?->phone ?? ($data['phone'] ?? null),
            'status' => $data['status'],
            'pax_count' => min((int) $data['pax_count'], $guest?->max_pax ?? (int) $data['pax_count']),
            'message' => $data['message'] ?? null,
            'submitted_at' => now(),
            'source' => $source,
        ]);
    }
}
