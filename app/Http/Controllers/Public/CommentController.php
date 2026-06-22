<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Models\Event;
use App\Services\AuditLogService;
use App\Services\TokenService;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function __construct(
        private readonly TokenService $tokenService,
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function store(CommentRequest $request, Event $event, ?string $guestToken = null): RedirectResponse
    {
        abort_unless($event->is_comment_enabled, 404);

        $guest = null;

        if ($guestToken) {
            $invitation = $this->tokenService->resolveInvitationByPublicToken($guestToken);
            abort_unless($invitation && $invitation->event_id === $event->id, 404);
            $guest = $invitation->guest;
        }

        $comment = $event->comments()->create([
            'guest_id' => $guest?->id,
            'name_snapshot' => $guest?->name ?? $request->string('name')->toString(),
            'message' => strip_tags($request->string('message')->toString()),
            'status' => 'pending',
            'submitted_at' => now(),
            'ip_hash' => hash('sha256', (string) $request->ip()),
        ]);

        $this->auditLogService->log('guest', null, $event, 'comment.submitted', get_class($comment), $comment->id);

        return back()->with('status', 'Ucapan berhasil dikirim dan menunggu moderasi.');
    }
}
