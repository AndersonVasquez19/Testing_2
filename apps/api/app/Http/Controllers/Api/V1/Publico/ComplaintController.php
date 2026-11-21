<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Publico;

use App\Actions\RegistrarReclamoAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Publico\StoreComplaintRequest;
use App\Http\Resources\ComplaintResource;
use App\Models\Complaint;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ComplaintController extends Controller
{
    public function store(StoreComplaintRequest $request, RegistrarReclamoAction $action): JsonResponse
    {
        $complaint = $action->execute($request->validated(), $request);

        return response()->json([
            'message' => 'Hoja de reclamación registrada correctamente.',
            'data' => new ComplaintResource($complaint),
        ], 201);
    }

    public function receipt(string $correlativeCode): StreamedResponse|JsonResponse
    {
        $complaint = Complaint::query()
            ->where('correlative_code', $correlativeCode)
            ->firstOrFail();

        if ($complaint->receipt_pdf_path === null || ! Storage::disk('local')->exists($complaint->receipt_pdf_path)) {
            return response()->json(['message' => 'Constancia PDF no disponible.'], 404);
        }

        return Storage::disk('local')->download(
            $complaint->receipt_pdf_path,
            "{$complaint->correlative_code}.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }
}
