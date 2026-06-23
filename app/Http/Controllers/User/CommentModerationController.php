<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Concerns\InteractsWithEventContext;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommentModerationRequest;
use App\Models\Comment;
use App\Models\Event;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CommentModerationController extends Controller
{
    use InteractsWithEventContext;

    public function __construct(private readonly AuditLogService $auditLogService)
    {
    }

    public function index(Event $event): View
    {
        $this->authorizeEventView($event);

        return view('dashboard.comments.index', [
            'event' => $event,
            'comments' => $event->comments()->latest('submitted_at')->get(),
        ]);
    }

    public function update(CommentModerationRequest $request, Event $event, Comment $comment): RedirectResponse
    {
        $this->authorizeEventUpdate($event);
        $this->ensureBelongsToEvent($comment, $event);

        $status = $request->string('status')->toString();
        $comment->update(['status' => $status]);

        $this->auditLogService->log('user', $request->user()->id, $event, 'comment.moderated', Comment::class, $comment->id, [
            'status' => $status,
        ]);

        return back()->with('status', 'Status komentar diperbarui.');
    }
}
