<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Event;

class AuditLogService
{
    public function log(
        string $actorType,
        ?int $actorId,
        ?Event $event,
        string $action,
        string $subjectType,
        ?int $subjectId,
        array $metadata = []
    ): void {
        AuditLog::create([
            'actor_type' => $actorType,
            'actor_id' => $actorId,
            'event_id' => $event?->id,
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'metadata_json' => $metadata,
        ]);
    }
}
