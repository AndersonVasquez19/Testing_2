<?php

declare(strict_types=1);

use App\Services\CalculadoraDiasHabilesService;
use Carbon\CarbonImmutable;

test('calculates 15 business days skipping weekends and Peruvian fixed holidays', function (): void {
    $service = new CalculadoraDiasHabilesService;

    // Lunes 2026-11-02 -> 15 días hábiles sin feriados intermedios hasta el 23-11-2026 (3 semanas = 15 días hábiles)
    $start = CarbonImmutable::parse('2026-11-02', 'America/Lima');
    $deadline = $service->sumarDiasHabiles($start, 15);
    expect($deadline->toDateString())->toBe('2026-11-23');

    // Con feriados de Fiestas Patrias (28 y 29 de julio) y 23 de julio:
    // Jueves 2026-07-16 + 15 días hábiles salta 23/07, 28/07, 29/07 y 06/08
    $julyStart = CarbonImmutable::parse('2026-07-16', 'America/Lima');
    $julyDeadline = $service->sumarDiasHabiles($julyStart, 15);
    expect($julyDeadline->isWeekend())->toBeFalse();
    expect($julyDeadline->toDateString())->toBe('2026-08-12');
});
