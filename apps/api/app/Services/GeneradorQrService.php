<?php

declare(strict_types=1);

namespace App\Services;

use SimpleSoftwareIO\QrCode\Generator;

class GeneradorQrService
{
    public function __construct(private readonly Generator $generator) {}

    public function svg(string $payload, int $size = 140): string
    {
        /** @var mixed $result */
        $result = $this->generator
            ->format('svg')
            ->size($size)
            ->margin(1)
            ->errorCorrection('M')
            ->generate($payload);

        if (is_string($result)) {
            return $result;
        }

        if (is_object($result) && method_exists($result, '__toString')) {
            return (string) $result;
        }

        return '';
    }

    public function dataUriSvg(string $payload, int $size = 140): string
    {
        return 'data:image/svg+xml;base64,'.base64_encode($this->svg($payload, $size));
    }
}
