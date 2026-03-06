<?php

use App\Models\CuentaReportada;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function (): void {
    CuentaReportada::query()
        ->where('estado', 'solucionado')
        ->whereNotNull('solucionado_at')
        ->where('solucionado_at', '<=', now()->subHours(12))
        ->delete();
})->hourly()->name('cuentas-reportadas:purge-solved');
