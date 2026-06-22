<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class EventManagementService
{
    public function create(
        User $owner,
        array $eventData,
        array $contentData,
        array $schedules,
        array $giftData,
        array $albumPhotos = [],
    ): Event {
        return DB::transaction(function () use ($owner, $eventData, $contentData, $schedules, $giftData, $albumPhotos) {
            $event = Event::create([
                ...$eventData,
                'user_id' => $owner->id,
            ]);

            $this->syncRelations($event, $contentData, $schedules, $giftData, $albumPhotos);
            $this->syncOrder($owner, $event, $eventData);

            return $event;
        });
    }

    public function update(
        Event $event,
        array $eventData,
        array $contentData,
        array $schedules,
        array $giftData,
        array $albumPhotos = [],
    ): Event {
        return DB::transaction(function () use ($event, $eventData, $contentData, $schedules, $giftData, $albumPhotos) {
            $event->update($eventData);
            $this->syncRelations($event, $contentData, $schedules, $giftData, $albumPhotos);
            $this->syncOrder($event->user, $event->fresh(), $eventData);

            return $event->fresh();
        });
    }

    private function syncRelations(
        Event $event,
        array $contentData,
        array $schedules,
        array $giftData,
        array $albumPhotos,
    ): void {
        $event->content()->updateOrCreate(['event_id' => $event->id], $contentData);

        $event->schedules()->delete();
        foreach ($schedules as $index => $schedule) {
            $event->schedules()->create([
                ...$schedule,
                'sort_order' => $index,
            ]);
        }

        $event->giftSetting()->updateOrCreate(['event_id' => $event->id], $giftData);

        if ($albumPhotos === []) {
            return;
        }

        $album = $event->albums()->firstOrCreate(['title' => 'Wedding Moments'], ['sort_order' => 0]);
        $existingCount = $album->photos()->count();

        foreach ($albumPhotos as $index => $photo) {
            if (! $photo instanceof UploadedFile) {
                continue;
            }

            $album->photos()->create([
                'image_path' => $photo->store('album-photos', 'public'),
                'caption' => null,
                'sort_order' => $existingCount + $index,
            ]);
        }
    }

    private function syncOrder(User $owner, Event $event, array $eventData): void
    {
        $order = Order::firstOrNew([
            'user_id' => $owner->id,
            'event_id' => $event->id,
        ]);

        $order->fill($this->buildOrderData($owner, $event, $eventData));
        $order->status = $order->exists ? $order->status : 'draft';
        $order->save();
    }

    private function buildOrderData(User $owner, Event $event, array $eventData): array
    {
        $pricing = config('nechcode.pricing');
        $addons = $eventData['settings_json']['addons'] ?? [];
        $experience = $eventData['settings_json']['experience'] ?? [];

        $trackingAddonPrice = ($eventData['is_rsvp_enabled'] || $eventData['is_gift_enabled'])
            ? $pricing['addons']['tracking_gift']['price']
            : 0;
        $broadcastAddonPrice = ! empty($addons['broadcast'])
            ? $pricing['addons']['broadcast']['price']
            : 0;
        $customDesignAddonPrice = ! empty($addons['custom_design'])
            ? $pricing['addons']['custom_design']['price']
            : 0;
        $bundleEnabled = ! empty($experience['bundle_offer_enabled']) && $trackingAddonPrice > 0;

        return [
            'user_id' => $owner->id,
            'event_id' => $event->id,
            'package_name' => $bundleEnabled
                ? 'Wedding + Gift Experience Bundle'
                : $pricing['base_package_name'],
            'base_price' => $pricing['base_package_price'],
            'addon_rsvp_gift_price' => $trackingAddonPrice,
            'addon_broadcast_price' => $broadcastAddonPrice,
            'addon_custom_design_price' => $customDesignAddonPrice,
            'total_price' => $pricing['base_package_price'] + $trackingAddonPrice + $broadcastAddonPrice + $customDesignAddonPrice,
        ];
    }
}
