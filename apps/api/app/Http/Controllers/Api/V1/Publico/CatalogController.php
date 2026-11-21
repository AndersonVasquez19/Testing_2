<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Publico;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProgramResource;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CatalogController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Program::query()
            ->where('is_published', true)
            ->where('status', 'active')
            ->with(['cohorts' => function ($q): void {
                $q->whereIn('status', ['open', 'in_progress'])
                    ->orderBy('start_date');
            }]);

        if ($request->filled('type')) {
            $query->where('type', $request->string('type')->toString());
        }

        if ($request->filled('modality')) {
            $modality = $request->string('modality')->toString();
            $query->whereHas('cohorts', fn ($q) => $q->where('modality', $modality)->whereIn('status', ['open', 'in_progress']));
        }

        return ProgramResource::collection($query->orderBy('id')->get());
    }

    public function show(string $slug): ProgramResource
    {
        $program = Program::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->where('status', 'active')
            ->with(['cohorts' => function ($q): void {
                $q->whereIn('status', ['open', 'in_progress'])->orderBy('start_date');
            }])
            ->firstOrFail();

        return new ProgramResource($program);
    }
}
