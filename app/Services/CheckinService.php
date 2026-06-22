<?php

namespace App\Services;

use App\Models\AttendanceCheckin;
use App\Models\GuestInvitation;
use App\Models\StaffAccessLink;

class CheckinService
{
    public function checkIn(GuestInvitation $invitation, ?StaffAccessLink $staffLink, ?string $deviceLabel = null): array
    {
        $existing = AttendanceCheckin::query()
            ->where('event_id', $invitation->event_id)
            ->where('guest_id', $invitation->guest_id)
            ->first();

        if ($existing) {
            return ['status' => 'duplicate', 'checkin' => $existing];
        }

        $checkin = AttendanceCheckin::create([
            'event_id' => $invitation->event_id,
            'guest_id' => $invitation->guest_id,
            'guest_invitation_id' => $invitation->id,
            'checked_in_at' => now(),
            'checked_in_by_type' => $staffLink ? 'staff_link' : 'user',
            'staff_access_link_id' => $staffLink?->id,
            'device_label' => $deviceLabel,
        ]);

        return ['status' => 'checked_in', 'checkin' => $checkin];
    }
}
