<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Tareas Programadas del ERP (Clase 14)
|--------------------------------------------------------------------------
| En producción, el Cron del servidor ejecuta 'schedule:run' cada minuto.
| En desarrollo se comprueba con: ./vendor/bin/sail php artisan schedule:run
*/
Schedule::command('erp:resumen-diario')
    ->dailyAt(config('erp.daily_summary_at', '19:00'))
    ->name('enviar-resumen-diario-erp')
    ->withoutOverlapping();

// Limpieza periódica de registros de auditoría forense (Clase 12)
Schedule::command('activitylog:clean')
    ->daily()
    ->name('limpiar-auditoria-antigua');
