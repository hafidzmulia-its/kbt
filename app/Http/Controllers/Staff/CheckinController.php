<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Services\AuditLogService;
use App\Services\CheckinService;
use App\Services\EventFeatureService;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckinController extends Controller
{
    public function __construct(
        private readonly TokenService $tokenService,
        private readonly CheckinService $checkinService,
        private readonly EventFeatureService $eventFeatureService,
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function scanner(string $staffToken): View
    {
        $staffLink = $this->tokenService->resolveStaffLink($staffToken);
        abort_unless($staffLink && ! $staffLink->isRevoked(), 403);
        abort_unless($this->eventFeatureService->trackingEnabled($staffLink->event), 403);

        return view('staff.scanner', [
            'staffLink' => $staffLink,
            'event' => $staffLink->event()->withCount(['guests', 'rsvps', 'attendanceCheckins'])->first(),
        ]);
    }

    public function search(Request $request, string $staffToken): JsonResponse
    {
        $staffLink = $this->tokenService->resolveStaffLink($staffToken);
        abort_unless($staffLink && ! $staffLink->isRevoked(), 403);
        abort_unless($this->eventFeatureService->trackingEnabled($staffLink->event), 403);

        $query = trim((string) $request->input('q', ''));
        abort_if($query === '', 422, 'Search query is required.');

        $guests = Guest::query()
            ->where('event_id', $staffLink->event_id)
            ->where(function ($guestQuery) use ($query) {
                $guestQuery
                    ->where('name', 'like', '%'.$query.'%')
                    ->orWhere('phone', 'like', '%'.$query.'%')
                    ->orWhere('group_name', 'like', '%'.$query.'%');
            })
            ->with(['guestGroup', 'rsvps' => fn ($rsvpQuery) => $rsvpQuery->latest('submitted_at')->limit(1), 'attendanceCheckins'])
            ->limit(10)
            ->get()
            ->map(fn (Guest $guest) => [
                'id' => $guest->id,
                'name' => $guest->name,
                'phone' => $guest->phone,
                'group_name' => $guest->resolved_group_name,
                'max_pax' => $guest->max_pax,
                'rsvp_status' => $guest->rsvps->first()?->status ?? 'pending',
                'checked_in' => $guest->attendanceCheckins->isNotEmpty(),
            ]);

        return response()->json(['guests' => $guests]);
    }

    public function scan(Request $request, string $staffToken): JsonResponse
    {
        $staffLink = $this->tokenService->resolveStaffLink($staffToken);
        abort_unless($staffLink && ! $staffLink->isRevoked(), 403);
        abort_unless($this->eventFeatureService->trackingEnabled($staffLink->event), 403);

        $payload = (string) $request->input('payload', '');
        $guestId = $request->input('guest_id');

        if ($guestId) {
            $guest = Guest::query()->where('event_id', $staffLink->event_id)->findOrFail($guestId);
            $invitation = $guest->invitation;
        } else {
            [$prefix, $eventCode, $checkinToken] = array_pad(explode(':', $payload, 3), 3, null);
            abort_unless($prefix === 'NCI' && $eventCode === $staffLink->event->public_code && $checkinToken, 422, 'Invalid QR payload.');
            $invitation = $this->tokenService->resolveInvitationByCheckinToken($checkinToken);
        }

        abort_unless($invitation && $invitation->event_id === $staffLink->event_id, 404);

        $result = $this->checkinService->checkIn($invitation, $staffLink, $request->input('device_label'));

        $this->auditLogService->log(
            'staff_link',
            $staffLink->id,
            $staffLink->event,
            $result['status'] === 'duplicate' ? 'attendance.duplicate_scan' : 'attendance.checked_in',
            get_class($result['checkin']),
            $result['checkin']->id,
            ['guest_id' => $invitation->guest_id]
        );

        return response()->json([
            'status' => $result['status'],
            'guest' => [
                'id' => $invitation->guest->id,
                'name' => $invitation->guest->name,
                'group_name' => $invitation->guest->resolved_group_name,
                'max_pax' => $invitation->guest->max_pax,
            ],
            'checked_in_at' => optional($result['checkin']->checked_in_at)->toDateTimeString(),
        ]);
    }
}
