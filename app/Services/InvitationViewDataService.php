<?php

namespace App\Services;

use App\Models\Event;
use App\Models\GuestInvitation;

class InvitationViewDataService
{
    private const EVENT_RELATIONS = [
        'content',
        'schedules',
        'template',
        'musicAsset',
        'giftSetting',
        'albums.photos',
    ];

    public function __construct(
        private readonly TokenService $tokenService,
        private readonly EventFeatureService $eventFeatureService,
        private readonly InvitationLanguageService $invitationLanguageService,
        private readonly GuestScheduleAccessService $guestScheduleAccessService,
    ) {
    }

    public function forGeneral(Event $event): array
    {
        $this->ensurePublished($event);

        return $this->buildViewData($this->loadEvent($event));
    }

    public function forPersonal(Event $event, string $guestToken): array
    {
        $this->ensurePublished($event);
        abort_unless($this->eventFeatureService->trackingEnabled($event), 404);

        $invitation = $this->tokenService->resolveInvitationByPublicToken($guestToken);
        abort_unless($invitation && $invitation->event_id === $event->id, 404);

        $invitation->increment('open_count');
        $invitation->update(['last_opened_at' => now()]);

        return $this->buildViewData(
            $this->loadEvent($event->fresh()),
            $invitation
        );
    }

    public function forPreviewGeneral(Event $event): array
    {
        return $this->buildViewData($this->loadEvent($event));
    }

    public function forPreviewPersonal(Event $event): array
    {
        $event = $this->loadEvent($event);
        $invitation = $event->guestInvitations()->with('guest')->latest()->first();

        abort_unless($invitation !== null, 404, 'Tambahkan minimal satu tamu untuk preview personal invitation.');

        return $this->buildViewData($event, $invitation);
    }

    private function buildViewData(Event $event, ?GuestInvitation $invitation = null): array
    {
        return [
            'event' => $event,
            'guest' => $invitation?->guest,
            'invitation' => $invitation,
            'allowedSchedules' => $this->guestScheduleAccessService->allowedSchedules($event, $invitation?->guest),
            'comments' => $event->comments()->where('status', 'approved')->latest('submitted_at')->take(20)->get(),
            'languagePack' => $this->invitationLanguageService->resolveForEvent($event),
        ];
    }

    private function loadEvent(Event $event): Event
    {
        return $event->load(self::EVENT_RELATIONS);
    }

    private function ensurePublished(Event $event): void
    {
        abort_if($event->status !== 'published', 404);
    }
}
