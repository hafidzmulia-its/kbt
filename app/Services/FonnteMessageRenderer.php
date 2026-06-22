<?php

namespace App\Services;

use App\Models\BroadcastCampaign;
use App\Models\Guest;
use App\Models\GuestInvitation;
use Illuminate\Support\Carbon;

class FonnteMessageRenderer
{
    public function __construct(
        private readonly InvitationLanguageService $invitationLanguageService,
    ) {
    }

    public function render(BroadcastCampaign $campaign, Guest $guest, GuestInvitation $invitation): string
    {
        $event = $campaign->relationLoaded('event')
            ? $campaign->getRelation('event')->loadMissing('schedules')
            : $campaign->event()->with('schedules')->first();
        $firstSchedule = $event?->schedules->first();
        $language = $event ? $this->invitationLanguageService->resolveForEvent($event) : null;
        $dateLabel = '';

        if ($firstSchedule?->date) {
            $date = $firstSchedule->date instanceof Carbon
                ? $firstSchedule->date
                : Carbon::parse($firstSchedule->date);

            $dateLabel = $date
                ->locale($language['carbon_locale'] ?? 'id')
                ->translatedFormat('d F Y');
        }

        return strtr($campaign->message_template, [
            '{{guest_name}}' => $guest->name,
            '{{guest_group}}' => $guest->resolved_group_name ?? '',
            '{{couple_names}}' => $event?->couple_name_display ?? '',
            '{{event_date}}' => $dateLabel,
            '{{event_session}}' => $firstSchedule?->label ?? '',
            '{{invitation_link}}' => $invitation->invitation_url_cached ?? '',
            '{{rsvp_link}}' => $invitation->invitation_url_cached ? $invitation->invitation_url_cached.'#rsvp' : '',
            '{{checkin_qr_hint}}' => 'Tunjukkan QR pada halaman undangan saat check-in.',
        ]);
    }
}
