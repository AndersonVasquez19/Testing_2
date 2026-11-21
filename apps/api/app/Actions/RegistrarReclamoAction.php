<?php

declare(strict_types=1);

namespace App\Actions;

use App\Mail\ConstanciaReclamoMail;
use App\Models\Complaint;
use App\Services\CalculadoraDiasHabilesService;
use App\Services\GeneradorPdfService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class RegistrarReclamoAction
{
    public function __construct(
        private readonly CalculadoraDiasHabilesService $diasHabiles,
        private readonly GeneradorPdfService $pdfService,
        private readonly AuditarAccionAction $auditar,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data, ?Request $request = null): Complaint
    {
        $complaint = DB::transaction(function () use ($data, $request): Complaint {
            $rawTz = config('horus.timezone', 'America/Lima');
            $tz = is_string($rawTz) ? $rawTz : 'America/Lima';
            $now = CarbonImmutable::now($tz);
            $year = $now->format('Y');
            $prefix = "REC-{$year}-";

            /** @var Complaint|null $latest */
            $latest = Complaint::query()
                ->where('correlative_code', 'like', "{$prefix}%")
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            $nextNumber = 1;
            if ($latest !== null) {
                $parts = explode('-', $latest->correlative_code);
                $nextNumber = ((int) end($parts)) + 1;
            }

            $correlative = sprintf('%s%04d', $prefix, $nextNumber);
            $rawDays = config('horus.complaint_business_days', 15);
            $businessDays = is_numeric($rawDays) ? (int) $rawDays : 15;
            $legalDeadline = $this->diasHabiles->sumarDiasHabiles($now, $businessDays);

            $rawEmail = $data['consumer_email'] ?? '';
            $email = is_string($rawEmail) ? strtolower($rawEmail) : '';

            $complaint = Complaint::query()->create([
                'correlative_code' => $correlative,
                'consumer_name' => $data['consumer_name'],
                'consumer_document_type' => $data['consumer_document_type'],
                'consumer_document_number' => $data['consumer_document_number'],
                'consumer_phone' => $data['consumer_phone'],
                'consumer_email' => $email,
                'consumer_address' => $data['consumer_address'],
                'is_minor' => (bool) ($data['is_minor'] ?? false),
                'representative_name' => $data['representative_name'] ?? null,
                'representative_address' => $data['representative_address'] ?? null,
                'representative_phone' => $data['representative_phone'] ?? null,
                'representative_email' => $data['representative_email'] ?? null,
                'item_type' => $data['item_type'],
                'item_description' => $data['item_description'] ?? null,
                'claimed_amount' => $data['claimed_amount'] ?? null,
                'complaint_type' => $data['complaint_type'],
                'detail' => $data['detail'],
                'consumer_request' => $data['consumer_request'],
                'status' => Complaint::STATUS_REGISTERED,
                'legal_deadline' => $legalDeadline->toDateString(),
            ]);

            $pdfPath = $this->pdfService->generarConstanciaReclamo($complaint);
            $complaint->update(['receipt_pdf_path' => $pdfPath]);

            $this->auditar->execute(
                null,
                'complaint_registered',
                $complaint,
                [
                    'correlative_code' => $correlative,
                    'complaint_type' => $complaint->complaint_type,
                    'legal_deadline' => $complaint->legal_deadline->toDateString(),
                ],
                $request,
            );

            return $complaint;
        });

        Mail::to($complaint->consumer_email)->send(new ConstanciaReclamoMail($complaint));

        return $complaint->fresh() ?? $complaint;
    }
}
