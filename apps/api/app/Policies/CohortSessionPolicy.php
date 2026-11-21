<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CohortSession;
use App\Models\User;

class CohortSessionPolicy
{
    public function manage(User $user, CohortSession $session): bool
    {
        if ($user->hasPortalRole(User::ROLE_ADMIN)) {
            return true;
        }

        if ($user->hasPortalRole(User::ROLE_COORDINATOR)) {
            $cohort = $session->cohort;

            return $cohort !== null && ($cohort->coordinator_id === null || $cohort->coordinator_id === $user->id);
        }

        return false;
    }
}
