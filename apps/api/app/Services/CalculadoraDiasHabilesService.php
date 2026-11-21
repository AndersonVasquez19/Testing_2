<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\CarbonImmutable;

class CalculadoraDiasHabilesService
{
    public function sumarDiasHabiles(CarbonImmutable $fechaBase, int $dias = 15): CarbonImmutable
    {
        $tz = config('horus.timezone', 'America/Lima');
        $timezone = is_string($tz) ? $tz : 'America/Lima';
        $fecha = $fechaBase->setTimezone($timezone)->startOfDay();
        /** @var list<string> $feriadosFijos */
        $feriadosFijos = config('horus.peru_fixed_holidays', []);
        $contados = 0;

        while ($contados < $dias) {
            $fecha = $fecha->addDay();
            if ($this->esDiaHabil($fecha, $feriadosFijos)) {
                $contados++;
            }
        }

        return $fecha;
    }

    /**
     * @param  list<string>  $feriadosFijos
     */
    public function esDiaHabil(CarbonImmutable $fecha, array $feriadosFijos = []): bool
    {
        if ($fecha->isWeekend()) {
            return false;
        }

        if ($feriadosFijos === []) {
            /** @var list<string> $feriadosFijos */
            $feriadosFijos = config('horus.peru_fixed_holidays', []);
        }

        return ! in_array($fecha->format('m-d'), $feriadosFijos, true);
    }
}
