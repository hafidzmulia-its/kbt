<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Concerns\InteractsWithEventContext;
use App\Http\Controllers\Controller;
use App\Http\Requests\BroadcastCampaignRequest;
use App\Http\Requests\BroadcastTemplateRequest;
use App\Models\BroadcastCampaign;
use App\Models\Event;
use App\Services\BroadcastCampaignService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use RuntimeException;

class BroadcastCampaignController extends Controller
{
    use InteractsWithEventContext;

    public function __construct(private readonly BroadcastCampaignService $broadcastCampaignService)
    {
    }

    public function index(Event $event): View
    {
        $this->authorizeEventView($event);
        $this->ensureBroadcastEnabled($event);

        return view('dashboard.broadcasts.index', [
            'event' => $event,
            'integration' => request()->user()->fonnteIntegration,
            'groups' => $this->groupsForEvent($event),
            'templates' => request()->user()->broadcastTemplates()
                ->where(function ($query) use ($event) {
                    $query->whereNull('event_id')->orWhere('event_id', $event->id);
                })
                ->latest()
                ->get(),
            'campaigns' => $event->broadcastCampaigns()->with('template')->withCount([
                'logs as sent_count' => fn ($query) => $query->where('status', 'sent'),
                'logs as failed_count' => fn ($query) => $query->where('status', 'failed'),
                'logs as pending_count' => fn ($query) => $query->where('status', 'pending'),
                'logs as cancelled_count' => fn ($query) => $query->where('status', 'cancelled'),
            ])->get(),
            'preview' => session('broadcast_preview'),
        ]);
    }

    public function preview(BroadcastCampaignRequest $request, Event $event): RedirectResponse
    {
        $this->authorizeEventUpdate($event);
        $this->ensureBroadcastEnabled($event);

        try {
            $preview = $this->broadcastCampaignService->preview($event, $request->user(), [
                ...$request->validated(),
                'connect_only' => $request->boolean('connect_only', true),
                'vip_only' => $request->boolean('vip_only'),
                'physical_only' => $request->boolean('physical_only'),
            ]);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['broadcast' => $exception->getMessage()])->withInput();
        }

        return back()->withInput()->with('broadcast_preview', $preview);
    }

    public function store(BroadcastCampaignRequest $request, Event $event): RedirectResponse
    {
        $this->authorizeEventUpdate($event);
        $this->ensureBroadcastEnabled($event);

        try {
            $this->broadcastCampaignService->queue($event, $request->user(), [
                ...$request->validated(),
                'connect_only' => $request->boolean('connect_only', true),
                'vip_only' => $request->boolean('vip_only'),
                'physical_only' => $request->boolean('physical_only'),
            ]);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['broadcast' => $exception->getMessage()])->withInput();
        }

        return back()->with('status', 'Broadcast diantrikan.');
    }

    public function retryFailed(Event $event, BroadcastCampaign $broadcastCampaign): RedirectResponse
    {
        $this->authorizeEventUpdate($event);
        $this->ensureBroadcastEnabled($event);
        abort_unless($broadcastCampaign->event_id === $event->id, 404);

        $this->broadcastCampaignService->retryFailed($broadcastCampaign);

        return back()->with('status', 'Broadcast gagal berhasil diantrikan ulang.');
    }

    public function cancel(Event $event, BroadcastCampaign $broadcastCampaign): RedirectResponse
    {
        $this->authorizeEventUpdate($event);
        $this->ensureBroadcastEnabled($event);
        abort_unless($broadcastCampaign->event_id === $event->id, 404);

        try {
            $this->broadcastCampaignService->cancel($broadcastCampaign);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['broadcast' => $exception->getMessage()]);
        }

        return back()->with('status', 'Campaign berhasil dibatalkan.');
    }

    public function storeTemplate(BroadcastTemplateRequest $request, Event $event): RedirectResponse
    {
        $this->authorizeEventUpdate($event);
        $this->ensureBroadcastEnabled($event);

        $this->broadcastCampaignService->saveTemplate($event, $request->user(), [
            ...$request->validated(),
            'is_default' => $request->boolean('is_default'),
        ]);

        return back()->with('status', 'Template broadcast berhasil disimpan.');
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
