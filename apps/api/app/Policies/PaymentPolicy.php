<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPortalRole(User::ROLE_ADMIN, User::ROLE_SECRETARY);
    }

    public function validatePayment(User $user): bool
    {
        return $user->hasPortalRole(User::ROLE_ADMIN, User::ROLE_SECRETARY);
    }

    public function rejectPayment(User $user): bool
    {
        return $user->hasPortalRole(User::ROLE_ADMIN, User::ROLE_SECRETARY);
    }
}
