<?php

/*
|--------------------------------------------------------------------------
| Parámetros de Negocio del ERP (Clase 14)
|--------------------------------------------------------------------------
| Centraliza umbrales y horarios operativos para el motor de automatización.
*/
return [
    // Por debajo (o igual) de esta cantidad de stock se considera stock bajo y se alerta a compras
    'low_stock_threshold' => (int) env('ERP_LOW_STOCK', 5),

    // Hora de emisión del resumen nocturno para gerencia (formato 24h)
    'daily_summary_at' => env('ERP_DAILY_SUMMARY_AT', '19:00'),
];
