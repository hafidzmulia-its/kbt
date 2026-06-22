<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\RsvpRequest;
use App\Models\Event;
use App\Services\AuditLogService;
use App\Services\EventFeatureService;
use App\Services\RsvpService;
use App\Services\TokenService;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

class RsvpController extends Controller
{
    public function __construct(
        private readonly RsvpService $rsvpService,
        private readonly TokenService $tokenService,
        private readonly AuditLogService $auditLogService,
        private readonly EventFeatureService $eventFeatureService,
    ) {
    }

    public function storeGeneral(RsvpRequest $request, Event $event): RedirectResponse
    {
        abort_unless($event->is_rsvp_enabled, 404);

        try {
            $rsvp = $this->rsvpService->submit($event, $request->validated(), null, null, 'general_link');
        } catch (RuntimeException $exception) {
            return back()->withErrors(['rsvp' => $exception->getMessage()])->withInput();
        }

        $this->auditLogService->log('guest', null, $event, 'rsvp.submitted', get_class($rsvp), $rsvp->id);

        return back()->with('status', 'Konfirmasi kehadiran berhasil dikirim.');
    }

    public function storePersonal(RsvpRequest $request, Event $event, string $guestToken): RedirectResponse
    {
        abort_unless($event->is_rsvp_enabled, 404);
        abort_unless($this->eventFeatureService->trackingEnabled($event), 404);

        $invitation = $this->tokenService->resolveInvitationByPublicToken($guestToken);
        abort_unless($invitation && $invitation->event_id === $event->id, 404);

        try {
            $rsvp = $this->rsvpService->submit($event, $request->validated(), $invitation->guest, $invitation, 'personal_link');
        } catch (RuntimeException $exception) {
            return back()->withErrors(['rsvp' => $exception->getMessage()])->withInput();
        }

        $this->auditLogService->log('guest', null, $event, 'rsvp.submitted', get_class($rsvp), $rsvp->id);

        return back()->with('status', 'Konfirmasi kehadiran berhasil dikirim.');
    }
}
