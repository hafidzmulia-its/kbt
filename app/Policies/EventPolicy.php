<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Event $event): bool
    {
        return $user->isAdmin() || $event->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'user'], true);
    }

    public function update(User $user, Event $event): bool
    {
        return $user->isAdmin() || $event->user_id === $user->id;
    }

    public function delete(User $user, Event $event): bool
    {
        return $user->isAdmin() || $event->user_id === $user->id;
    }

    public function restore(User $user, Event $event): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Event $event): bool
    {
        return $user->isAdmin();
    }
}
