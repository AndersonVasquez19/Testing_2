<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditarAccionAction
{
    /**
     * @param  array<string, mixed>  $properties
     */
    public function execute(
        ?User $actor,
        string $action,
        Model|string $subject,
        array $properties = [],
        ?Request $request = null,
    ): ActivityLog {
        $subjectType = $subject instanceof Model ? $subject::class : $subject;
        $subjectId = null;
        if ($subject instanceof Model) {
            $key = $subject->getKey();
            $subjectId = is_scalar($key) ? (string) $key : null;
        }

        return ActivityLog::query()->create([
            'user_id' => $actor?->id,
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'properties' => $properties,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'created_at' => now(),
        ]);
    }
}
