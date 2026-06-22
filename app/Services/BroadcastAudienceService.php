<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Guest;
use App\Models\Rsvp;
use Illuminate\Support\Collection;

class BroadcastAudienceService
{
    public function resolve(Event $event, array $targeting = []): Collection
    {
        $guests = $event->guests()
            ->with([
                'guestGroup',
                'invitation',
                'rsvps' => fn ($query) => $query->latest('submitted_at'),
            ])
            ->whereNull('deleted_at')
            ->whereNotNull('phone')
            ->whereHas('invitation')
            ->get();

        return $guests
            ->filter(fn (Guest $guest) => $this->matchesTargeting($guest, $targeting))
            ->values();
    }

    public function summarize(Collection $guests): array
    {
        return [
            'total' => $guests->count(),
            'vip' => $guests->where('is_vip', true)->count(),
            'physical' => $guests->where('needs_physical_invitation', true)->count(),
            'opened' => $guests->filter(fn (Guest $guest) => ($guest->invitation?->open_count ?? 0) > 0)->count(),
            'not_opened' => $guests->filter(fn (Guest $guest) => ($guest->invitation?->open_count ?? 0) === 0)->count(),
        ];
    }

    private function matchesTargeting(Guest $guest, array $targeting): bool
    {
        $latestRsvp = $guest->rsvps->first();
        $latestRsvpStatus = $latestRsvp?->status;
        $invitationOpenCount = (int) ($guest->invitation?->open_count ?? 0);

        if (($targeting['guest_group_id'] ?? null) && (int) $targeting['guest_group_id'] !== (int) $guest->guest_group_id) {
            return false;
        }

        if (($targeting['guest_status'] ?? null) && $targeting['guest_status'] !== $guest->status) {
            return false;
        }

        if (($targeting['rsvp_status'] ?? null) && $targeting['rsvp_status'] !== $latestRsvpStatus) {
            return false;
        }

        if (($targeting['vip_only'] ?? false) && ! $guest->is_vip) {
            return false;
        }

        if (($targeting['physical_only'] ?? false) && ! $guest->needs_physical_invitation) {
            return false;
        }

        if (($targeting['opened_state'] ?? null) === 'opened' && $invitationOpenCount < 1) {
            return false;
        }

        if (($targeting['opened_state'] ?? null) === 'not_opened' && $invitationOpenCount > 0) {
            return false;
        }

        return true;
    }
}
