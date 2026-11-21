<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPortalRole(User::ROLE_ADMIN, User::ROLE_SECRETARY, User::ROLE_COORDINATOR);
    }

    public function view(User $user, Enrollment $enrollment): bool
    {
        if ($user->hasPortalRole(User::ROLE_ADMIN, User::ROLE_SECRETARY, User::ROLE_COORDINATOR)) {
            return true;
        }

        return $enrollment->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPortalRole(User::ROLE_ADMIN, User::ROLE_SECRETARY);
    }

    public function markWhatsapp(User $user): bool
    {
        return $user->hasPortalRole(User::ROLE_ADMIN, User::ROLE_SECRETARY);
    }

    public function approveAcademic(User $user): bool
    {
        return $user->hasPortalRole(User::ROLE_ADMIN, User::ROLE_COORDINATOR);
    }
}
