<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Event;
use App\Services\EventFeatureService;
use Illuminate\Database\Eloquent\Model;

trait InteractsWithEventContext
{
    protected function authorizeEventView(Event $event): void
    {
        $this->authorize('view', $event);
    }

    protected function authorizeEventUpdate(Event $event): void
    {
        $this->authorize('update', $event);
    }

    protected function ensureBelongsToEvent(Model $resource, Event $event, string $foreignKey = 'event_id'): void
    {
        abort_unless($resource->getAttribute($foreignKey) === $event->id, 404);
    }

    protected function ensureTrackingEnabled(Event $event): void
    {
        abort_unless(
            app(EventFeatureService::class)->trackingEnabled($event),
            403,
            'Add-on tracking RSVP, gifting, dan check-in belum aktif untuk event ini.'
        );
    }

    protected function ensureBroadcastEnabled(Event $event): void
    {
        abort_unless(
            app(EventFeatureService::class)->broadcastEnabled($event),
            403,
            'Add-on automation kirim undangan belum aktif untuk event ini.'
        );
    }
}
