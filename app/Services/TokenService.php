<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Guest;
use App\Models\GuestInvitation;
use App\Models\StaffAccessLink;
use Illuminate\Support\Str;

class TokenService
{
    public function generateToken(int $bytes = 24): string
    {
        return rtrim(strtr(base64_encode(random_bytes($bytes)), '+/', '-_'), '=');
    }

    public function createGuestInvitation(Event $event, Guest $guest): array
    {
        $publicToken = $this->generateToken();
        $checkinToken = $this->generateToken();

        $invitation = GuestInvitation::updateOrCreate(
            ['guest_id' => $guest->id],
            [
                'event_id' => $event->id,
                'public_token_hash' => hash('sha256', $publicToken),
                'public_token_last4' => substr($publicToken, -4),
                'checkin_token_hash' => hash('sha256', $checkinToken),
                'checkin_token_last4' => substr($checkinToken, -4),
                'token_regenerated_at' => now(),
            ]
        );

        $invitation->update([
            'invitation_url_cached' => route('public.invitation.personal', ['event' => $event->slug, 'guestToken' => $publicToken]),
            'gift_url_cached' => route('public.gift.show', ['event' => $event->slug, 'guestToken' => $publicToken]),
            'checkin_url_cached' => sprintf('NCI:%s:%s', $event->public_code, $checkinToken),
        ]);

        return [$invitation->fresh(), $publicToken, $checkinToken];
    }

    public function createStaffLink(Event $event, string $label = 'Receptionist'): array
    {
        $token = $this->generateToken();

        $link = StaffAccessLink::create([
            'event_id' => $event->id,
            'token_hash' => hash('sha256', $token),
            'label' => $label,
            'permissions_json' => ['scan' => true, 'search' => true],
        ]);

        return [$link, $token];
    }

    public function resolveInvitationByPublicToken(string $token): ?GuestInvitation
    {
        return GuestInvitation::query()
            ->with(['guest', 'event.content', 'event.schedules', 'event.template', 'event.musicAsset', 'event.giftSetting', 'event.albums.photos'])
            ->where('public_token_hash', hash('sha256', $token))
            ->first();
    }

    public function resolveInvitationByCheckinToken(string $token): ?GuestInvitation
    {
        return GuestInvitation::query()
            ->with(['guest', 'event'])
            ->where('checkin_token_hash', hash('sha256', $token))
            ->first();
    }

    public function resolveStaffLink(string $token): ?StaffAccessLink
    {
        return StaffAccessLink::query()
            ->with('event')
            ->where('token_hash', hash('sha256', $token))
            ->first();
    }

    public function generateEventCode(): string
    {
        return strtoupper(Str::random(10));
    }
}
