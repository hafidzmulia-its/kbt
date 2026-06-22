<?php

namespace App\Jobs;

use App\Models\BroadcastCampaign;
use App\Models\BroadcastLog;
use App\Services\AuditLogService;
use App\Services\FonnteClient;
use App\Services\FonnteMessageRenderer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendFonnteBroadcastJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $campaignId)
    {
    }

    public function handle(FonnteClient $client, FonnteMessageRenderer $renderer, AuditLogService $auditLogService): void
    {
        $campaign = BroadcastCampaign::query()->with(['event', 'user.fonnteIntegration', 'logs.guest', 'logs.invitation'])->findOrFail($this->campaignId);

        if ($campaign->status === 'cancelled') {
            return;
        }

        $campaign->update(['status' => 'running']);
        $integration = $campaign->user->fonnteIntegration;

        if (! $integration?->hasUsableDeviceToken()) {
            $campaign->update(['status' => 'failed']);

            $campaign->logs()->where('status', 'pending')->update([
                'status' => 'failed',
                'error_message' => 'Fonnte integration is not active for this user.',
            ]);

            return;
        }

        foreach ($campaign->logs as $log) {
            if ($campaign->fresh()->status === 'cancelled') {
                break;
            }

            if ($log->status === 'cancelled') {
                continue;
            }

            $message = $renderer->render($campaign, $log->guest, $log->invitation);
            $result = $client->send($integration->device_token, $log->phone, $message, [
                'countryCode' => $campaign->country_code,
                'delay' => $campaign->delay,
                'connectOnly' => $campaign->connect_only,
                'preview' => true,
            ]);

            $log->update([
                'personalized_message' => $message,
                'provider_message_id' => $result['payload']['id'] ?? null,
                'status' => $result['ok'] ? 'sent' : 'failed',
                'error_message' => $result['ok'] ? null : json_encode($result['payload']),
                'sent_at' => $result['ok'] ? now() : null,
            ]);
        }

        $campaign->update([
            'status' => $campaign->logs()->where('status', 'failed')->exists() ? 'failed' : 'completed',
        ]);

        $auditLogService->log('system', null, $campaign->event, 'broadcast.processed', BroadcastLog::class, null, [
            'campaign_id' => $campaign->id,
        ]);
    }
}
