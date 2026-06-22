<?php

namespace App\Services;

use App\Jobs\SendFonnteBroadcastJob;
use App\Models\BroadcastCampaign;
use App\Models\BroadcastLog;
use App\Models\BroadcastTemplate;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Carbon;
use RuntimeException;

class BroadcastCampaignService
{
    public function __construct(
        private readonly BroadcastAudienceService $broadcastAudienceService,
        private readonly FonnteMessageRenderer $messageRenderer,
    ) {
    }

    public function preview(Event $event, User $user, array $data): array
    {
        $messageTemplate = $this->resolveMessageTemplate($data);
        $targeting = $this->extractTargeting($data);
        $guests = $this->broadcastAudienceService->resolve($event, $targeting);
        $draftCampaign = new BroadcastCampaign([
            'message_template' => $messageTemplate,
            'targeting_json' => $targeting,
        ]);
        $draftCampaign->setRelation('event', $event);

        return [
            'summary' => $this->broadcastAudienceService->summarize($guests),
            'scheduled_at' => ! empty($data['scheduled_at']) ? Carbon::parse($data['scheduled_at'])->toDateTimeString() : null,
            'samples' => $guests->take(3)->map(function ($guest) use ($draftCampaign) {
                return [
                    'name' => $guest->name,
                    'phone' => $guest->phone,
                    'group' => $guest->resolved_group_name,
                    'message' => $this->messageRenderer->render($draftCampaign, $guest, $guest->invitation),
                ];
            })->all(),
        ];
    }

    public function queue(Event $event, User $user, array $data): BroadcastCampaign
    {
        $integration = $user->fonnteIntegration;

        if (! $integration?->hasUsableDeviceToken()) {
            throw new RuntimeException('Fonnte belum aktif. Simpan device token dan verifikasi device terlebih dahulu.');
        }

        $messageTemplate = $this->resolveMessageTemplate($data);
        $targeting = $this->extractTargeting($data);
        $audience = $this->broadcastAudienceService->resolve($event, $targeting);

        if ($audience->isEmpty()) {
            throw new RuntimeException('Tidak ada tamu yang cocok dengan segmentasi broadcast ini.');
        }

        $scheduledAt = ! empty($data['scheduled_at']) ? Carbon::parse($data['scheduled_at']) : null;
        $campaign = BroadcastCampaign::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'broadcast_template_id' => $data['broadcast_template_id'] ?? null,
            'name' => $data['name'],
            'message_template' => $messageTemplate,
            'status' => $scheduledAt && $scheduledAt->isFuture() ? 'scheduled' : 'queued',
            'country_code' => $data['country_code'] ?? $integration->default_country_code,
            'delay' => $data['delay'] ?? null,
            'connect_only' => (bool) ($data['connect_only'] ?? true),
            'scheduled_at' => $scheduledAt,
            'targeting_json' => $targeting,
        ]);

        $audience->each(function ($guest) use ($campaign) {
                BroadcastLog::create([
                    'campaign_id' => $campaign->id,
                    'guest_id' => $guest->id,
                    'guest_invitation_id' => $guest->invitation->id,
                    'phone' => $guest->phone,
                    'personalized_message' => '',
                    'status' => 'pending',
                ]);
            });

        $job = SendFonnteBroadcastJob::dispatch($campaign->id);

        if ($scheduledAt && $scheduledAt->isFuture()) {
            $job->delay($scheduledAt);
        }

        return $campaign;
    }

    public function retryFailed(BroadcastCampaign $campaign): void
    {
        $campaign->logs()->where('status', 'failed')->update([
            'status' => 'pending',
            'error_message' => null,
            'sent_at' => null,
        ]);

        $campaign->update(['status' => 'queued']);

        SendFonnteBroadcastJob::dispatch($campaign->id);
    }

    public function cancel(BroadcastCampaign $campaign): void
    {
        if (! in_array($campaign->status, ['queued', 'scheduled'], true)) {
            throw new RuntimeException('Campaign ini tidak bisa dibatalkan lagi.');
        }

        $campaign->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        $campaign->logs()->where('status', 'pending')->update([
            'status' => 'cancelled',
            'error_message' => 'Campaign cancelled by user before processing.',
        ]);
    }

    public function saveTemplate(Event $event, User $user, array $data): BroadcastTemplate
    {
        if (! empty($data['is_default'])) {
            BroadcastTemplate::query()
                ->where('user_id', $user->id)
                ->where(function ($query) use ($event) {
                    $query->whereNull('event_id')->orWhere('event_id', $event->id);
                })
                ->update(['is_default' => false]);
        }

        return BroadcastTemplate::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'name' => $data['name'],
            'template_body' => $data['template_body'],
            'is_default' => (bool) ($data['is_default'] ?? false),
        ]);
    }

    private function extractTargeting(array $data): array
    {
        return [
            'guest_group_id' => $data['guest_group_id'] ?? null,
            'guest_status' => $data['guest_status'] ?? 'active',
            'rsvp_status' => $data['rsvp_status'] ?? null,
            'opened_state' => $data['opened_state'] ?? 'all',
            'vip_only' => (bool) ($data['vip_only'] ?? false),
            'physical_only' => (bool) ($data['physical_only'] ?? false),
        ];
    }

    private function resolveMessageTemplate(array $data): string
    {
        if (! empty($data['message_template'])) {
            return $data['message_template'];
        }

        if (! empty($data['broadcast_template_id'])) {
            return BroadcastTemplate::query()->findOrFail($data['broadcast_template_id'])->template_body;
        }

        throw new RuntimeException('Template pesan broadcast tidak ditemukan.');
    }
}
