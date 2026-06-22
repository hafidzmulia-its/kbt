<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Concerns\InteractsWithEventContext;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\CsvExportService;
use App\Services\GuestScheduleAccessService;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;
use App\Http\Requests\RsvpScheduleAssignmentRequest;
use Illuminate\Http\RedirectResponse;

class RsvpDashboardController extends Controller
{
    use InteractsWithEventContext;

    public function __construct(
        private readonly CsvExportService $csvExportService,
        private readonly GuestScheduleAccessService $guestScheduleAccessService,
    )
    {
    }

    public function __invoke(Request $request, Event $event): View
    {
        $this->authorizeEventView($event);
        $this->ensureTrackingEnabled($event);

        $filters = $request->only(['q', 'status', 'guest_group_id', 'event_schedule_id', 'source']);
        $rsvps = $this->queryRsvps($event, $filters);
        $summary = [
            'total' => $rsvps->count(),
            'hadir' => $rsvps->where('status', 'hadir')->count(),
            'tidak_hadir' => $rsvps->where('status', 'tidak_hadir')->count(),
            'ragu' => $rsvps->where('status', 'ragu')->count(),
            'avg_pax' => round((float) $rsvps->avg('pax_count'), 1),
        ];

        return view('dashboard.rsvps.index', [
            'event' => $event,
            'rsvps' => $rsvps,
            'filters' => $filters,
            'summary' => $summary,
            'groups' => $this->groupsForEvent($event),
            'schedules' => $event->schedules()->get(),
            'assignmentMatrix' => $this->guestScheduleAccessService->assignmentMatrix($event),
        ]);
    }

    public function export(Request $request, Event $event): StreamedResponse
    {
        $this->authorizeEventView($event);
        $this->ensureTrackingEnabled($event);

        $rsvps = $this->queryRsvps($event, $request->only(['q', 'status', 'guest_group_id', 'event_schedule_id', 'source']));

        return $this->csvExportService->download(
            sprintf('rsvps-%s-%s.csv', $event->slug, now()->format('Ymd-His')),
            ['guest_name', 'phone', 'group_name', 'schedule', 'status', 'pax_count', 'source', 'message', 'submitted_at'],
            $rsvps->map(fn ($rsvp) => [
                $rsvp->name_snapshot,
                $rsvp->phone_snapshot,
                $rsvp->guest?->resolved_group_name,
                $rsvp->schedule?->label,
                $rsvp->status,
                $rsvp->pax_count,
                $rsvp->source,
                $rsvp->message,
                optional($rsvp->submitted_at)->toDateTimeString(),
            ])
        );
    }

    public function updateAssignments(RsvpScheduleAssignmentRequest $request, Event $event): RedirectResponse
    {
        $this->authorizeEventUpdate($event);
        $this->ensureTrackingEnabled($event);

        $this->guestScheduleAccessService->syncAssignments($event, $request->input('assignments', []));

        return back()->with('status', 'Akses sesi RSVP per grup berhasil diperbarui.');
    }

    private function queryRsvps(Event $event, array $filters): Collection
    {
        return $event->rsvps()
            ->with(['guest.guestGroup', 'schedule'])
            ->latest('submitted_at')
            ->get()
            ->filter(function ($rsvp) use ($filters) {
                if (($filters['status'] ?? null) && $rsvp->status !== $filters['status']) {
                    return false;
                }

                if (($filters['guest_group_id'] ?? null) && (int) $rsvp->guest?->guest_group_id !== (int) $filters['guest_group_id']) {
                    return false;
                }

                if (($filters['event_schedule_id'] ?? null) && (int) $rsvp->event_schedule_id !== (int) $filters['event_schedule_id']) {
                    return false;
                }

                if (($filters['source'] ?? null) && $rsvp->source !== $filters['source']) {
                    return false;
                }

                $search = trim((string) ($filters['q'] ?? ''));

                if ($search !== '') {
                    $haystacks = [
                        $rsvp->name_snapshot,
                        $rsvp->phone_snapshot,
                        $rsvp->guest?->resolved_group_name,
                        $rsvp->schedule?->label,
                    ];

                    return collect($haystacks)
                        ->filter()
                        ->contains(fn ($value) => str_contains(mb_strtolower((string) $value), mb_strtolower($search)));
                }

                return true;
            })
            ->values();
    }

    private function groupsForEvent(Event $event)
    {
        if (! Schema::hasTable('guest_groups')) {
            return collect();
        }

        return $event->guestGroups()
            ->withCount(['guests' => fn ($query) => $query->withoutTrashed()])
            ->get();
    }
}
