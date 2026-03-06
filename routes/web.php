<?php

use App\Http\Controllers\Auth\TenantAuthenticatedSessionController;
use App\Http\Middleware\EnsureTenantSessionIsolation;
use App\Http\Middleware\InitializeTenantConnection;
use Illuminate\Support\Facades\Route;

// Rutas de Filament y otras personalizadas
if (file_exists(base_path('routes/filament.php'))) {
    require base_path('routes/filament.php');
}

Route::redirect('/', '/admin/login');

Route::prefix('tenant')->group(function (): void {
    Route::get('/login', [TenantAuthenticatedSessionController::class, 'create'])->name('tenant.login');
    Route::post('/login', [TenantAuthenticatedSessionController::class, 'store'])->name('tenant.login.store');

    Route::middleware([InitializeTenantConnection::class, EnsureTenantSessionIsolation::class, 'auth:tenant_web'])->group(function (): void {
        Route::get('/dashboard', function () {
            return 'Panel de empresa';
        })->name('tenant.dashboard');

        Route::post('/logout', [TenantAuthenticatedSessionController::class, 'destroy'])->name('tenant.logout');
    });
});