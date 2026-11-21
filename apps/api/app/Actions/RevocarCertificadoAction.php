<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RevocarCertificadoAction
{
    public function __construct(private readonly AuditarAccionAction $auditar) {}

    public function execute(Certificate $certificate, User $actor, string $reason, ?Request $request = null): Certificate
    {
        return DB::transaction(function () use ($certificate, $actor, $reason, $request): Certificate {
            /** @var Certificate $locked */
            $locked = Certificate::query()->lockForUpdate()->findOrFail($certificate->id);

            if ($locked->isRevoked()) {
                throw ValidationException::withMessages([
                    'revoked_at' => ['Este certificado ya se encuentra revocado.'],
                ]);
            }

            $locked->update([
                'revoked_at' => now(),
                'revoked_by' => $actor->id,
                'revocation_reason' => $reason,
            ]);

            $this->auditar->execute(
                $actor,
                'certificate_revoked',
                $locked,
                [
                    'certificate_code' => $locked->certificate_code,
                    'reason' => $reason,
                ],
                $request,
            );

            return $locked->fresh(['enrollment.student', 'revoker']) ?? $locked;
        });
    }
}
