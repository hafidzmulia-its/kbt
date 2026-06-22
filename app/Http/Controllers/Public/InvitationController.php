<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\InvitationViewDataService;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function __construct(private readonly InvitationViewDataService $invitationViewDataService)
    {
    }

    public function showGeneral(Event $event): View
    {
        return view('invitation.show', $this->invitationViewDataService->forGeneral($event));
    }

    public function showPersonal(Event $event, string $guestToken): View
    {
        return view('invitation.show', $this->invitationViewDataService->forPersonal($event, $guestToken));
    }
}
