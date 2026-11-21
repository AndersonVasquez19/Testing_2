<?php

return [
    'driver' => env('LARAVEL_PDF_DRIVER', 'dompdf'),
    'cache' => [
        'automatic' => false,
    ],
    'dompdf' => [
        'isRemoteEnabled' => false,
        'isHtml5ParserEnabled' => true,
    ],
];
