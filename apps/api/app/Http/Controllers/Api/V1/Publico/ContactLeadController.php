<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Publico;

use App\Http\Controllers\Controller;
use App\Http\Requests\Publico\StoreContactLeadRequest;
use App\Http\Resources\ContactLeadResource;
use App\Models\ContactLead;
use Illuminate\Http\JsonResponse;

class ContactLeadController extends Controller
{
    public function store(StoreContactLeadRequest $request): JsonResponse
    {
        $lead = ContactLead::query()->create(array_merge(
            $request->validated(),
            ['status' => ContactLead::STATUS_NEW]
        ));

        return response()->json([
            'message' => 'Tu mensaje fue registrado correctamente. Nuestro equipo se comunicará contigo pronto.',
            'data' => new ContactLeadResource($lead),
        ], 201);
    }
}
