<?php

namespace App\Http\Controllers;

use App\Models\AttendanceCheckin;
use App\Models\BroadcastLog;
use App\Models\Comment;
use App\Models\Event;
use App\Models\GiftContribution;
use App\Models\Rsvp;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        $user = request()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $eventIds = Event::query()->where('user_id', $user->id)->pluck('id');

        return view('dashboard.index', [
            'events' => Event::query()->with(['template', 'schedules'])->whereIn('id', $eventIds)->latest()->get(),
            'metrics' => [
                'events' => $eventIds->count(),
                'rsvps' => Rsvp::query()->whereIn('event_id', $eventIds)->count(),
                'attendance' => AttendanceCheckin::query()->whereIn('event_id', $eventIds)->count(),
                'gift_pending' => GiftContribution::query()->whereIn('event_id', $eventIds)->where('status', 'proof_uploaded')->count(),
                'broadcast_sent' => BroadcastLog::query()->whereHas('campaign', fn ($query) => $query->whereIn('event_id', $eventIds))->where('status', 'sent')->count(),
                'comments' => Comment::query()->whereIn('event_id', $eventIds)->where('status', 'pending')->count(),
            ],
        ]);
    }
}
