<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Order;

class EventFeatureService
{
    public function capabilities(Event $event): array
    {
        $order = $this->latestOrder($event);
        $addons = $event->settings_json['addons'] ?? [];

        return [
            'tracking' => $order
                ? $this->hasTrackingAddon($order)
                : ($event->is_rsvp_enabled || $event->is_gift_enabled || $event->is_guest_personalization_enabled),
            'broadcast' => $order
                ? $this->hasBroadcastAddon($order)
                : ! empty($addons['broadcast']),
            'custom_design' => $order
                ? $this->hasCustomDesignAddon($order)
                : ! empty($addons['custom_design']),
            'bundle' => ! empty($event->settings_json['experience']['bundle_offer_enabled']),
        ];
    }

    public function trackingEnabled(Event $event): bool
    {
        return $this->capabilities($event)['tracking'];
    }

    public function broadcastEnabled(Event $event): bool
    {
        return $this->capabilities($event)['broadcast'];
    }

    public function customDesignEnabled(Event $event): bool
    {
        return $this->capabilities($event)['custom_design'];
    }

    public function bundleEnabled(Event $event): bool
    {
        return $this->capabilities($event)['bundle'];
    }

    private function latestOrder(Event $event): ?Order
    {
        if ($event->relationLoaded('orders')) {
            return $event->orders->sortByDesc('created_at')->first();
        }

        return $event->orders()->latest()->first();
    }

    private function hasTrackingAddon(?Order $order): bool
    {
        if ($order) {
            return (int) $order->addon_rsvp_gift_price > 0;
        }

        return false;
    }

    private function hasBroadcastAddon(?Order $order): bool
    {
        if ($order) {
            return (int) $order->addon_broadcast_price > 0;
        }

        return false;
    }

    private function hasCustomDesignAddon(?Order $order): bool
    {
        if ($order) {
            return (int) $order->addon_custom_design_price > 0;
        }

        return false;
    }
}
