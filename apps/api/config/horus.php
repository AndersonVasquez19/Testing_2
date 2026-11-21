<?php

return [
    'timezone' => 'America/Lima',
    'web_url' => env('HORUS_WEB_URL', 'http://localhost:3000'),
    'whatsapp_number' => env('HORUS_WHATSAPP_NUMBER', '51972021374'),
    'yape' => [
        'number' => env('HORUS_YAPE_NUMBER', '+51 972 021 374'),
        'holder' => env('HORUS_YAPE_HOLDER', 'Horus Group SRL'),
    ],
    'plin' => [
        'number' => env('HORUS_PLIN_NUMBER', '+51 972 021 374'),
        'holder' => env('HORUS_PLIN_HOLDER', 'Horus Group SRL'),
    ],
    'reservation_days' => max(2, (int) env('HORUS_RESERVATION_DAYS', 2)),
    'complaint_business_days' => 15,
    'company' => [
        'name' => 'Horus Group SRL',
        'ruc' => '20611010977',
        'address' => 'Jr. José Gálvez 322, Cajamarca - Perú',
        'email' => 'horusgroupcajamarca@gmail.com',
        'phone' => '+51 972 021 374',
    ],
    /**
     * Feriados nacionales fijos de Perú (MM-DD) para el cálculo de 15 días hábiles del Libro de Reclamaciones.
     */
    'peru_fixed_holidays' => [
        '01-01', // Año Nuevo
        '05-01', // Día del Trabajo
        '06-07', // Batalla de Arica y Día de la Bandera
        '06-29', // San Pedro y San Pablo
        '07-23', // Día de la Fuerza Aérea del Perú
        '07-28', // Fiestas Patrias
        '07-29', // Fiestas Patrias
        '08-06', // Batalla de Junín
        '08-30', // Santa Rosa de Lima
        '10-08', // Combate de Angamos
        '11-01', // Día de Todos los Santos
        '12-08', // Inmaculada Concepción
        '12-09', // Batalla de Ayacucho
        '12-25', // Navidad
    ],
];
