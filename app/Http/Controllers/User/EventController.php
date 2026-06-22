<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Concerns\InteractsWithEventContext;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Models\Event;
use App\Models\Template;
use App\Services\AuditLogService;
use App\Services\EventFeatureService;
use App\Services\EventFormOptions;
use App\Services\InvitationLanguageService;
use App\Services\EventManagementService;
use App\Services\EventWorkflowService;
use App\Services\TokenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EventController extends Controller
{
    use InteractsWithEventContext;

    public function __construct(
        private readonly TokenService $tokenService,
        private readonly AuditLogService $auditLogService,
        private readonly EventManagementService $eventManagementService,
        private readonly EventFormOptions $eventFormOptions,
        private readonly EventFeatureService $eventFeatureService,
        private readonly InvitationLanguageService $invitationLanguageService,
        private readonly EventWorkflowService $eventWorkflowService,
    ) {
    }

    public function index(): View
    {
        $events = Event::query()
            ->ownedBy(request()->user())
            ->with(['template', 'schedules'])
            ->latest()
            ->get();

        return view('dashboard.events.index', compact('events'));
    }

    public function create(): View
    {
        return view('dashboard.events.form', array_merge([
            'event' => new Event(['status' => 'draft']),
            'languageOptions' => $this->invitationLanguageService->optionMap(),
            'builderSteps' => $this->eventWorkflowService->builderSteps(new Event(['status' => 'draft'])),
        ], $this->eventFormOptions->get()));
    }

    public function store(EventRequest $request): RedirectResponse
    {
        $event = $this->eventManagementService->create(
            $request->user(),
            $this->eventData($request),
            $this->eventContentData($request),
            $this->eventSchedulesData($request),
            $this->eventGiftData($request),
            $request->file('album_photos', [])
        );

        $this->auditLogService->log('user', $request->user()->id, $event, 'event.created', Event::class, $event->id);

        return redirect()->route('dashboard.events.edit', $event)->with('status', 'Event berhasil dibuat.');
    }

    public function show(Event $event): RedirectResponse
    {
        $this->authorizeEventView($event);

        return redirect()->route('dashboard.events.edit', $event);
    }

    public function edit(Event $event): View
    {
        $this->authorizeEventUpdate($event);

        $event->load(['content', 'schedules', 'giftSetting', 'guests.invitation', 'broadcastCampaigns.logs', 'comments', 'attendanceCheckins.guest']);

        return view('dashboard.events.form', array_merge([
            'event' => $event,
            'languageOptions' => $this->invitationLanguageService->optionMap(),
            'builderSteps' => $this->eventWorkflowService->builderSteps($event),
        ], $this->eventFormOptions->get()));
    }

    public function workspace(Event $event): View
    {
        $this->authorizeEventView($event);

        $event->loadCount([
            'guests',
            'rsvps',
            'attendanceCheckins',
            'giftContributions',
            'comments',
            'broadcastCampaigns',
        ])->load([
            'schedules',
            'template',
            'musicAsset',
            'giftSetting',
            'staffAccessLinks' => fn ($query) => $query->latest(),
            'orders' => fn ($query) => $query->latest(),
        ]);

        $capabilities = $this->eventFeatureService->capabilities($event);

        return view('dashboard.events.workspace', [
            'event' => $event,
            'latestOrder' => $event->orders->first(),
            'pricing' => config('nechcode.pricing'),
            'capabilities' => $capabilities,
            'languagePack' => $this->invitationLanguageService->resolveForEvent($event),
            'readiness' => $this->eventWorkflowService->readiness($event, $capabilities),
            'nextActions' => $this->eventWorkflowService->nextActions($event, $capabilities),
        ]);
    }

    public function update(EventRequest $request, Event $event): RedirectResponse
    {
        $this->authorizeEventUpdate($event);

        $this->eventManagementService->update(
            $event,
            $this->eventData($request, $event),
            $this->eventContentData($request),
            $this->eventSchedulesData($request),
            $this->eventGiftData($request),
            $request->file('album_photos', [])
        );

        $this->auditLogService->log('user', $request->user()->id, $event, 'event.updated', Event::class, $event->id);

        return back()->with('status', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event);
        $event->delete();

        return redirect()->route('dashboard.events.index')->with('status', 'Event dihapus.');
    }

    private function eventData(EventRequest $request, ?Event $event = null): array
    {
        return [
            'title' => $request->string('title')->toString(),
            'slug' => $request->string('slug')->toString(),
            'public_code' => $event?->public_code ?? $this->tokenService->generateEventCode(),
            'couple_name_display' => $request->string('couple_name_display')->toString(),
            'bride_name' => $request->string('bride_name')->toString(),
            'groom_name' => $request->string('groom_name')->toString(),
            'status' => $request->string('status')->toString(),
            'template_id' => $request->input('template_id'),
            'music_asset_id' => $request->input('music_asset_id'),
            'is_rsvp_enabled' => $request->boolean('is_rsvp_enabled'),
            'is_comment_enabled' => $request->boolean('is_comment_enabled'),
            'is_gift_enabled' => $request->boolean('is_gift_enabled'),
            'is_guest_personalization_enabled' => $request->boolean('is_guest_personalization_enabled', true),
            'published_at' => $request->input('status') === 'published'
                ? ($event?->published_at ?? now())
                : $event?->published_at,
            'settings_json' => array_replace_recursive($event?->settings_json ?? [], [
                'addons' => [
                    'broadcast' => $request->boolean('wants_broadcast_addon'),
                    'custom_design' => $request->boolean('wants_custom_design_addon'),
                ],
                'experience' => [
                    'language_variant' => $request->input('language_variant', 'id_formal'),
                    'bundle_offer_enabled' => $request->boolean('bundle_offer_enabled'),
                    'ai_style_brief' => $request->input('ai_style_brief'),
                    'broadcast_message_template_seed' => $request->input('broadcast_message_template_seed'),
                ],
            ]),
        ];
    }

    private function eventContentData(EventRequest $request): array
    {
        return $request->safe()->only([
            'opening_text',
            'invitation_text',
            'closing_text',
            'bride_bio',
            'groom_bio',
            'no_gift_message',
        ]);
    }

    private function eventSchedulesData(EventRequest $request): array
    {
        return $request->input('schedules', []);
    }

    private function eventGiftData(EventRequest $request): array
    {
        return [
            'mode' => $request->input('gift_mode', 'no_gift'),
            'bank_name' => $request->input('bank_name'),
            'account_number' => $request->input('account_number'),
            'account_holder' => $request->input('account_holder'),
            'instructions' => $request->input('gift_instructions'),
            'no_gift_message' => $request->input('no_gift_message'),
            'is_proof_upload_enabled' => $request->boolean('is_proof_upload_enabled'),
        ];
    }
}
