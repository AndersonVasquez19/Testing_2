<?php

declare(strict_types=1);

namespace App\Actions;

use App\Mail\ConstanciaReclamoMail;
use App\Models\Complaint;
use App\Models\User;
use App\Services\GeneradorPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ResponderReclamoAction
{
    public function __construct(
        private readonly GeneradorPdfService $pdfService,
        private readonly AuditarAccionAction $auditar,
    ) {}

    public function execute(
        Complaint $complaint,
        string $response,
        string $status,
        User $actor,
        ?Request $request = null,
    ): Complaint {
        $updated = DB::transaction(function () use ($complaint, $response, $status, $actor, $request): Complaint {
            /** @var Complaint $locked */
            $locked = Complaint::query()->lockForUpdate()->findOrFail($complaint->id);

            $locked->update([
                'company_response' => $response,
                'status' => $status,
                'responded_by' => $actor->id,
                'responded_at' => now(),
            ]);

            $pdfPath = $this->pdfService->generarConstanciaReclamo($locked);
            $locked->update(['receipt_pdf_path' => $pdfPath]);

            $this->auditar->execute(
                $actor,
                'complaint_responded',
                $locked,
                [
                    'correlative_code' => $locked->correlative_code,
                    'status' => $status,
                ],
                $request,
            );

            return $locked->fresh(['responder']) ?? $locked;
        });

        Mail::to($updated->consumer_email)->send(new ConstanciaReclamoMail($updated));

        return $updated;
    }
}
