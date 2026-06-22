<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Concerns\InteractsWithEventContext;
use App\Http\Controllers\Controller;
use App\Http\Requests\GuestBulkActionRequest;
use App\Http\Requests\GuestGroupRequest;
use App\Http\Requests\GuestImportPreviewRequest;
use App\Http\Requests\GuestRequest;
use App\Models\Event;
use App\Models\Guest;
use App\Services\AuditLogService;
use App\Services\CsvExportService;
use App\Services\GuestImportService;
use App\Services\GuestManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GuestController extends Controller
{
    use InteractsWithEventContext;

    public function __construct(
        private readonly GuestManagementService $guestManagementService,
        private readonly GuestImportService $guestImportService,
        private readonly CsvExportService $csvExportService,
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function index(Event $event): View
    {
        $this->authorizeEventView($event);
        $this->ensureTrackingEnabled($event);

        $filters = request()->only(['q', 'status', 'guest_group_id', 'lifecycle', 'vip_only', 'physical_only']);
        $guests = $this->guestManagementService
            ->queryForEvent($event, $filters)
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.guests.index', [
            'event' => $event,
            'guests' => $guests,
            'filters' => $filters,
            'groups' => $this->groupsForEvent($event),
            'importPreview' => $this->guestImportService->getPreview($event),
        ]);
    }

    public function export(Request $request, Event $event): StreamedResponse
    {
        $this->authorizeEventView($event);
        $this->ensureTrackingEnabled($event);

        $guests = $this->guestManagementService
            ->queryForEvent($event, $request->only(['q', 'status', 'guest_group_id', 'lifecycle', 'vip_only', 'physical_only']))
            ->get();

        $filename = sprintf('guests-%s-%s.csv', $event->slug, now()->format('Ymd-His'));

        return $this->csvExportService->download(
            $filename,
            [
                'name',
                'phone',
                'group_name',
                'status',
                'max_pax',
                'is_vip',
                'needs_physical_invitation',
                'invitation_url',
                'rsvp_status',
                'checked_in',
                'gift_status',
            ],
            $guests->map(function (Guest $guest) {
                $latestRsvp = $guest->rsvps->sortByDesc('submitted_at')->first();
                $gift = $guest->giftContributions->sortByDesc('updated_at')->first();

                return [
                    $guest->name,
                    $guest->phone,
                    $guest->resolved_group_name,
                    $guest->status,
                    $guest->max_pax,
                    $guest->is_vip ? 'yes' : 'no',
                    $guest->needs_physical_invitation ? 'yes' : 'no',
                    $guest->invitation?->invitation_url_cached,
                    $latestRsvp?->status ?? 'pending',
                    $guest->attendanceCheckins->isNotEmpty() ? 'yes' : 'no',
                    $gift?->status ?? 'none',
                ];
            })
        );
    }

    public function store(GuestRequest $request, Event $event): RedirectResponse
    {
        $this->authorizeEventUpdate($event);
        $this->ensureTrackingEnabled($event);

        $this->guestManagementService->create($event, [
            ...$request->safe()->only(['name', 'phone', 'guest_group_id', 'group_name', 'address_note', 'max_pax', 'status']),
            'max_pax' => $request->input('max_pax', 1),
            'status' => $request->input('status', 'active'),
            'is_vip' => $request->boolean('is_vip'),
            'needs_physical_invitation' => $request->boolean('needs_physical_invitation'),
        ]);

        return back()->with('status', 'Guest berhasil disimpan.');
    }

    public function update(GuestRequest $request, Event $event, Guest $guest): RedirectResponse
    {
        $this->authorizeEventUpdate($event);
        $this->ensureTrackingEnabled($event);
        $this->ensureBelongsToEvent($guest, $event);

        $this->guestManagementService->update(
            $guest,
            [
                ...$request->safe()->only(['name', 'phone', 'guest_group_id', 'group_name', 'address_note', 'max_pax', 'status']),
                'is_vip' => $request->boolean('is_vip'),
                'needs_physical_invitation' => $request->boolean('needs_physical_invitation'),
            ]
        );

        return back()->with('status', 'Guest berhasil diperbarui.');
    }

    public function destroy(Event $event, Guest $guest): RedirectResponse
    {
        $this->authorizeEventUpdate($event);
        $this->ensureTrackingEnabled($event);
        $this->ensureBelongsToEvent($guest, $event);

        $this->guestManagementService->deactivate($guest);

        return back()->with('status', 'Guest dinonaktifkan.');
    }

    public function restore(Event $event, int $guest): RedirectResponse
    {
        $this->authorizeEventUpdate($event);
        $this->ensureTrackingEnabled($event);

        $guestModel = Guest::withTrashed()->where('event_id', $event->id)->findOrFail($guest);
        $this->guestManagementService->restore($guestModel);

        return back()->with('status', 'Guest berhasil dipulihkan.');
    }

    public function regenerateToken(Event $event, Guest $guest): RedirectResponse
    {
        $this->authorizeEventUpdate($event);
        $this->ensureTrackingEnabled($event);
        $this->ensureBelongsToEvent($guest, $event);

        $this->guestManagementService->regenerateInvitation($event, $guest);

        return back()->with('status', 'Token undangan berhasil diperbarui.');
    }

    public function previewImport(GuestImportPreviewRequest $request, Event $event): RedirectResponse
    {
        $this->authorizeEventUpdate($event);
        $this->ensureTrackingEnabled($event);

        $preview = $this->guestImportService->buildPreview($event, $request->file('import_file'));

        return back()->with('status', sprintf(
            'Preview impor siap: %d valid, %d perlu diperiksa.',
            $preview['summary']['valid_rows'],
            $preview['summary']['invalid_rows']
        ));
    }

    public function commitImport(Event $event): RedirectResponse
    {
        $this->authorizeEventUpdate($event);
        $this->ensureTrackingEnabled($event);

        $result = $this->guestManagementService->commitPreviewImport($event);

        return back()->with('status', sprintf(
            'Impor selesai: %d tamu baru, %d tamu diperbarui.',
            $result['imported'],
            $result['updated']
        ));
    }

    public function clearImportPreview(Event $event): RedirectResponse
    {
        $this->authorizeEventUpdate($event);
        $this->ensureTrackingEnabled($event);

        $this->guestImportService->clearPreview($event);

        return back()->with('status', 'Preview impor dibersihkan.');
    }

    public function storeGroup(GuestGroupRequest $request, Event $event): RedirectResponse
    {
        $this->authorizeEventUpdate($event);
        $this->ensureTrackingEnabled($event);

        $group = $this->guestManagementService->createGroup($event, $request->validated());

        $this->auditLogService->log('user', $request->user()->id, $event, 'guest-group.created', get_class($group), $group->id);

        return back()->with('status', 'Grup tamu berhasil dibuat.');
    }

    public function bulkUpdate(GuestBulkActionRequest $request, Event $event): RedirectResponse
    {
        $this->authorizeEventUpdate($event);
        $this->ensureTrackingEnabled($event);

        $count = $this->guestManagementService->bulkApply(
            $event,
            $request->input('guest_ids', []),
            $request->string('action')->toString(),
            $request->input('guest_group_id')
        );

        return back()->with('status', "Bulk action berhasil dijalankan untuk {$count} tamu.");
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
