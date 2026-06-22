<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Concerns\InteractsWithEventContext;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\CsvExportService;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class AttendanceDashboardController extends Controller
{
    use InteractsWithEventContext;

    public function __construct(private readonly CsvExportService $csvExportService)
    {
    }

    public function __invoke(Event $event): View
    {
        $this->authorizeEventView($event);
        $this->ensureTrackingEnabled($event);

        return view('dashboard.attendance.index', [
            'event' => $event,
            'checkins' => $event->attendanceCheckins()->with(['guest', 'staffAccessLink'])->latest('checked_in_at')->get(),
        ]);
    }

    public function export(Event $event): StreamedResponse
    {
        $this->authorizeEventView($event);
        $this->ensureTrackingEnabled($event);

        $checkins = $event->attendanceCheckins()->with(['guest', 'staffAccessLink'])->latest('checked_in_at')->get();

        return $this->csvExportService->download(
            sprintf('attendance-%s-%s.csv', $event->slug, now()->format('Ymd-His')),
            ['guest_name', 'group_name', 'max_pax', 'checked_in_at', 'scanner_label'],
            $checkins->map(fn ($checkin) => [
                $checkin->guest?->name,
                $checkin->guest?->resolved_group_name,
                $checkin->guest?->max_pax,
                optional($checkin->checked_in_at)->toDateTimeString(),
                $checkin->staffAccessLink?->label ?? $checkin->device_label,
            ])
        );
    }
}
