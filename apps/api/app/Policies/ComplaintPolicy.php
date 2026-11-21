<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Complaint;
use App\Models\User;

class ComplaintPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPortalRole(User::ROLE_ADMIN, User::ROLE_SECRETARY);
    }

    public function respond(User $user, Complaint $complaint): bool
    {
        return $user->hasPortalRole(User::ROLE_ADMIN);
    }
}
