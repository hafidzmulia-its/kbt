<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventSchedule;
use App\Models\Guest;
use App\Models\GuestGroup;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class GuestScheduleAccessService
{
    public function allowedSchedules(Event $event, ?Guest $guest = null): Collection
    {
        if (! $this->supportsScheduleGroups()) {
            return $event->schedules;
        }

        $event->loadMissing('schedules.guestGroups');

        if (! $guest?->guest_group_id) {
            return $event->schedules;
        }

        $hasAnyAssignments = $event->schedules->contains(fn (EventSchedule $schedule) => $schedule->guestGroups->isNotEmpty());
        $assignedSchedules = $event->schedules
            ->filter(fn (EventSchedule $schedule) => $schedule->guestGroups->contains('id', $guest->guest_group_id))
            ->values();

        if ($assignedSchedules->isNotEmpty()) {
            return $assignedSchedules;
        }

        return $hasAnyAssignments ? collect() : $event->schedules;
    }

    public function validateScheduleSelection(Event $event, ?Guest $guest, ?int $scheduleId): ?EventSchedule
    {
        if (! $scheduleId) {
            return null;
        }

        $schedule = $event->schedules()->findOrFail($scheduleId);
        $allowedScheduleIds = $this->allowedSchedules($event, $guest)->pluck('id');

        if (! $allowedScheduleIds->contains($schedule->id)) {
            throw new RuntimeException('Sesi acara yang dipilih tidak tersedia untuk tamu ini.');
        }

        return $schedule;
    }

    public function assignmentMatrix(Event $event): array
    {
        if (! $this->supportsScheduleGroups()) {
            return [];
        }

        $event->loadMissing(['schedules.guestGroups', 'guestGroups']);

        return $event->schedules->map(function (EventSchedule $schedule) use ($event) {
            $assignedGroupIds = $schedule->guestGroups->pluck('id')->all();

            return [
                'schedule' => $schedule,
                'groups' => $event->guestGroups->map(fn (GuestGroup $group) => [
                    'group' => $group,
                    'is_assigned' => in_array($group->id, $assignedGroupIds, true),
                ]),
            ];
        })->all();
    }

    public function syncAssignments(Event $event, array $assignments): void
    {
        if (! $this->supportsScheduleGroups()) {
            return;
        }

        $event->loadMissing(['schedules', 'guestGroups']);
        $scheduleIds = $event->schedules->pluck('id')->all();
        $groupIds = $event->guestGroups->pluck('id')->all();

        foreach ($event->schedules as $schedule) {
            $requestedGroupIds = collect($assignments[$schedule->id] ?? [])
                ->map(fn ($groupId) => (int) $groupId)
                ->filter(fn (int $groupId) => in_array($groupId, $groupIds, true))
                ->values();

            if (! in_array($schedule->id, $scheduleIds, true)) {
                continue;
            }

            $syncPayload = $requestedGroupIds
                ->mapWithKeys(fn (int $groupId) => [$groupId => ['event_id' => $event->id, 'allow_rsvp' => true]])
                ->all();

            $schedule->guestGroups()->sync($syncPayload);
        }
    }

    private function supportsScheduleGroups(): bool
    {
        return Schema::hasTable('guest_groups') && Schema::hasTable('event_schedule_guest_groups');
    }
}
