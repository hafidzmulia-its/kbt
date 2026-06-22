<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Concerns\InteractsWithEventContext;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\StaffAccessLink;
use App\Services\AuditLogService;
use App\Services\TokenService;
use Illuminate\Http\RedirectResponse;

class StaffAccessLinkController extends Controller
{
    use InteractsWithEventContext;

    public function __construct(
        private readonly TokenService $tokenService,
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function store(Event $event): RedirectResponse
    {
        $this->authorizeEventUpdate($event);
        $this->ensureTrackingEnabled($event);

        [$staffLink, $token] = $this->tokenService->createStaffLink($event, request('label', 'Receptionist'));

        $this->auditLogService->log('user', request()->user()->id, $event, 'staff_link.created', StaffAccessLink::class, $staffLink->id);

        return back()->with('status', 'Link staff dibuat: '.route('staff.checkin.scanner', ['staffToken' => $token]));
    }

    public function revoke(Event $event, StaffAccessLink $staffAccessLink): RedirectResponse
    {
        $this->authorizeEventUpdate($event);
        $this->ensureTrackingEnabled($event);
        $this->ensureBelongsToEvent($staffAccessLink, $event);

        $staffAccessLink->update(['revoked_at' => now()]);

        $this->auditLogService->log('user', request()->user()->id, $event, 'staff_link.revoked', StaffAccessLink::class, $staffAccessLink->id);

        return back()->with('status', 'Link staff dicabut.');
    }
}
