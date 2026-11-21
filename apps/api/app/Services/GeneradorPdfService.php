<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Certificate;
use App\Models\Complaint;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Enums\Orientation;
use Spatie\LaravelPdf\Facades\Pdf;

class GeneradorPdfService
{
    public function __construct(private readonly GeneradorQrService $qrService) {}

    public function generarConstanciaReclamo(Complaint $complaint): string
    {
        $path = "complaints/{$complaint->correlative_code}.pdf";

        Pdf::view('pdf.reclamo', [
            'complaint' => $complaint,
            'company' => config('horus.company'),
        ])
            ->driver('dompdf')
            ->format(Format::A4)
            ->disk('local')
            ->save($path);

        return $path;
    }

    public function generarCertificado(Certificate $certificate): string
    {
        $path = "certificates/{$certificate->certificate_code}.pdf";
        $qrDataUri = $this->qrService->dataUriSvg($certificate->qr_payload, 140);

        Pdf::view('pdf.certificado', [
            'certificate' => $certificate,
            'snapshot' => $certificate->snapshot_data,
            'qrDataUri' => $qrDataUri,
            'company' => config('horus.company'),
        ])
            ->driver('dompdf')
            ->format(Format::A4)
            ->orientation(Orientation::Landscape)
            ->disk('local')
            ->save($path);

        return $path;
    }
}
