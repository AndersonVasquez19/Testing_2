<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\User;

class CertificatePolicy
{
    public function issue(User $user): bool
    {
        return $user->hasPortalRole(User::ROLE_ADMIN, User::ROLE_COORDINATOR);
    }

    public function revoke(User $user, Certificate $certificate): bool
    {
        return $user->hasPortalRole(User::ROLE_ADMIN);
    }

    public function download(User $user, Certificate $certificate): bool
    {
        if ($user->hasPortalRole(User::ROLE_ADMIN, User::ROLE_COORDINATOR, User::ROLE_SECRETARY)) {
            return true;
        }

        /** @var Enrollment $enrollment */
        $enrollment = $certificate->enrollment;

        return $enrollment->user_id === $user->id;
    }
}
