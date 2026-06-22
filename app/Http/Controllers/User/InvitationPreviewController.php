<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Concerns\InteractsWithEventContext;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\InvitationViewDataService;
use Illuminate\View\View;

class InvitationPreviewController extends Controller
{
    use InteractsWithEventContext;

    public function __construct(private readonly InvitationViewDataService $invitationViewDataService)
    {
    }

    public function general(Event $event): View
    {
        $this->authorizeEventView($event);

        return view('invitation.show', $this->invitationViewDataService->forPreviewGeneral($event));
    }

    public function personal(Event $event): View
    {
        $this->authorizeEventView($event);

        return view('invitation.show', $this->invitationViewDataService->forPreviewPersonal($event));
    }
}
