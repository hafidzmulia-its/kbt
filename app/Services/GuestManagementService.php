<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Guest;
use App\Models\GuestGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class GuestManagementService
{
    public function __construct(
        private readonly TokenService $tokenService,
        private readonly GuestImportService $guestImportService,
    ) {
    }

    public function create(Event $event, array $guestData): Guest
    {
        return DB::transaction(function () use ($event, $guestData) {
            $normalized = $this->normalizeGuestData($event, $guestData);

            $guest = Guest::create([
                'event_id' => $event->id,
                ...$normalized,
            ]);

            $this->tokenService->createGuestInvitation($event, $guest);

            return $guest->fresh(['guestGroup', 'invitation']);
        });
    }

    public function update(Guest $guest, array $guestData): void
    {
        $guest->update($this->normalizeGuestData($guest->event, $guestData));
    }

    public function queryForEvent(Event $event, array $filters = [])
    {
        $lifecycle = $filters['lifecycle'] ?? 'active';

        return $event->guests()
            ->when($lifecycle === 'archived', fn ($query) => $query->onlyTrashed())
            ->when($lifecycle === 'all', fn ($query) => $query->withTrashed())
            ->with(['guestGroup', 'invitation', 'rsvps', 'attendanceCheckins', 'giftContributions'])
            ->when($filters['q'] ?? null, function ($query, string $search) {
                $query->where(function ($guestQuery) use ($search) {
                    $guestQuery
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%')
                        ->orWhere('group_name', 'like', '%'.$search.'%');
                });
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['guest_group_id'] ?? null, fn ($query, string $groupId) => $query->where('guest_group_id', $groupId))
            ->when(($filters['vip_only'] ?? false) === '1', fn ($query) => $query->where('is_vip', true))
            ->when(($filters['physical_only'] ?? false) === '1', fn ($query) => $query->where('needs_physical_invitation', true))
            ->latest();
    }

    public function deactivate(Guest $guest): void
    {
        $guest->delete();
    }

    public function restore(Guest $guest): void
    {
        $guest->restore();
    }

    public function regenerateInvitation(Event $event, Guest $guest): void
    {
        $this->tokenService->createGuestInvitation($event, $guest);
    }

    public function createGroup(Event $event, array $data): GuestGroup
    {
        $name = trim((string) ($data['name'] ?? ''));

        if ($name === '') {
            throw new RuntimeException('Nama grup tamu wajib diisi.');
        }

        return GuestGroup::create([
            'event_id' => $event->id,
            'name' => $name,
            'slug' => $this->uniqueGroupSlug($event, $name),
            'description' => $data['description'] ?? null,
            'sort_order' => (int) $event->guestGroups()->count(),
        ]);
    }

    public function commitPreviewImport(Event $event): array
    {
        $preview = $this->guestImportService->commitPreview($event);
        $rows = collect($preview['rows'])->filter(fn ($row) => $row['errors'] === []);
        $groupNames = $rows->pluck('data.group_name')->filter()->unique()->values()->all();
        $groupMap = $this->guestImportService->resolveGroupMap($event, $groupNames);
        $imported = 0;
        $updated = 0;

        DB::transaction(function () use ($event, $rows, $groupMap, &$imported, &$updated) {
            foreach ($rows as $row) {
                $normalized = $this->normalizeGuestData($event, [
                    ...$row['data'],
                    'guest_group_id' => $groupMap[$row['data']['group_name'] ?? '']->id ?? null,
                ]);

                $existing = null;

                if ($normalized['phone']) {
                    $existing = Guest::withTrashed()
                        ->where('event_id', $event->id)
                        ->where('phone', $normalized['phone'])
                        ->first();
                }

                if (! $existing) {
                    $existing = Guest::withTrashed()
                        ->where('event_id', $event->id)
                        ->where('name', $normalized['name'])
                        ->where(function ($query) use ($normalized) {
                            $query->whereNull('phone')->orWhere('phone', '');
                            if ($normalized['phone'] === null) {
                                return;
                            }
                            $query->orWhere('phone', $normalized['phone']);
                        })
                        ->first();
                }

                if ($existing) {
                    if ($existing->trashed()) {
                        $existing->restore();
                    }

                    $existing->update($normalized);
                    $updated++;
                    $guest = $existing;
                } else {
                    $guest = Guest::create([
                        'event_id' => $event->id,
                        ...$normalized,
                    ]);
                    $imported++;
                }

                $this->tokenService->createGuestInvitation($event, $guest);
            }
        });

        $this->guestImportService->clearPreview($event);

        return [
            'imported' => $imported,
            'updated' => $updated,
        ];
    }

    public function bulkApply(Event $event, array $guestIds, string $action, ?int $guestGroupId = null): int
    {
        $guests = Guest::withTrashed()
            ->where('event_id', $event->id)
            ->whereIn('id', $guestIds)
            ->get();

        if ($guests->isEmpty()) {
            throw new RuntimeException('Pilih minimal satu tamu untuk menjalankan bulk action.');
        }

        $group = null;

        if ($guestGroupId) {
            $group = $event->guestGroups()->findOrFail($guestGroupId);
        }

        DB::transaction(function () use ($event, $guests, $action, $group) {
            foreach ($guests as $guest) {
                match ($action) {
                    'assign_group' => $guest->update([
                        'guest_group_id' => $group?->id,
                        'group_name' => $group?->name,
                    ]),
                    'clear_group' => $guest->update([
                        'guest_group_id' => null,
                        'group_name' => null,
                    ]),
                    'mark_vip' => $guest->update(['is_vip' => true]),
                    'unmark_vip' => $guest->update(['is_vip' => false]),
                    'require_physical' => $guest->update(['needs_physical_invitation' => true]),
                    'clear_physical' => $guest->update(['needs_physical_invitation' => false]),
                    'activate' => $guest->update(['status' => 'active']),
                    'deactivate' => $guest->update(['status' => 'inactive']),
                    'archive' => ! $guest->trashed() ? $guest->delete() : null,
                    'restore' => $guest->trashed() ? $guest->restore() : null,
                    'regenerate_tokens' => ! $guest->trashed() ? $this->tokenService->createGuestInvitation($event, $guest) : null,
                    default => throw new RuntimeException('Bulk action tidak dikenali.'),
                };
            }
        });

        return $guests->count();
    }

    private function normalizeGuestData(Event $event, array $guestData): array
    {
        $guestGroupId = $guestData['guest_group_id'] ?? null;
        $group = $guestGroupId ? $event->guestGroups()->find($guestGroupId) : null;
        $legacyGroupName = trim((string) ($guestData['group_name'] ?? ''));

        if (! $group && $legacyGroupName !== '') {
            $group = $event->guestGroups()->firstWhere('name', $legacyGroupName)
                ?? $event->guestGroups()->create([
                    'name' => $legacyGroupName,
                    'slug' => $this->uniqueGroupSlug($event, $legacyGroupName),
                ]);
        }

        return [
            'name' => trim((string) ($guestData['name'] ?? '')),
            'phone' => $this->normalizePhone($guestData['phone'] ?? null),
            'guest_group_id' => $group?->id,
            'group_name' => $group?->name ?: ($legacyGroupName ?: null),
            'address_note' => $guestData['address_note'] ?? null,
            'max_pax' => max(1, min(10, (int) ($guestData['max_pax'] ?? 1))),
            'status' => in_array(($guestData['status'] ?? 'active'), ['active', 'inactive'], true) ? ($guestData['status'] ?? 'active') : 'active',
            'is_vip' => (bool) ($guestData['is_vip'] ?? false),
            'needs_physical_invitation' => (bool) ($guestData['needs_physical_invitation'] ?? false),
        ];
    }

    private function uniqueGroupSlug(Event $event, string $name): string
    {
        $baseSlug = Str::slug($name) ?: 'group';
        $slug = $baseSlug;
        $index = 2;

        while ($event->guestGroups()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$index;
            $index++;
        }

        return $slug;
    }

    private function normalizePhone(?string $phone): ?string
    {
        $value = trim((string) $phone);

        return $value !== '' ? $value : null;
    }
}
