<?php

use App\Models\CuentaReportada;
use App\Models\Tenant;
use App\Services\Tenancy\TenantHealthCheckService;
use App\Support\Tenancy\TenantConnectionManager;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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

Artisan::command('tenancy:sanity-check {--tenant= : Slug de empresa especifica}', function (): int {
    $this->info('Iniciando verificacion multitenant...');

    try {
        DB::connection(config('tenancy.central_connection'))->getPdo();
        $this->info('Central DB: OK');
    } catch (\Throwable $exception) {
        $this->error('Central DB: ERROR - '.$exception->getMessage());

        return self::FAILURE;
    }

    $query = Tenant::query()->where('status', 'active');

    if ($slug = $this->option('tenant')) {
        $query->where('slug', $slug);
    }

    $tenants = $query->orderBy('id')->get();

    if ($tenants->isEmpty()) {
        $this->warn('No hay empresas activas para validar.');

        return self::SUCCESS;
    }

    /** @var TenantHealthCheckService $checker */
    $checker = app(TenantHealthCheckService::class);
    $hasErrors = false;

    foreach ($tenants as $tenant) {
        $report = $checker->check($tenant);

        if ($report['ok']) {
            $this->info("[{$tenant->slug}] OK - {$report['message']}");
            continue;
        }

        $hasErrors = true;
        $this->error("[{$tenant->slug}] {$report['status']} - {$report['message']}");
    }

    if ($hasErrors) {
        return self::FAILURE;
    }

    $this->info('Verificacion completada sin errores.');

    return self::SUCCESS;
})->purpose('Verifica conectividad y tablas criticas en esquema multitenant.');

Artisan::command('tenancy:migrate-active {--tenant= : Slug de empresa especifica}', function (): int {
    $query = Tenant::query()->where('status', 'active');

    if ($slug = $this->option('tenant')) {
        $query->where('slug', $slug);
    }

    $tenants = $query->orderBy('id')->get();

    if ($tenants->isEmpty()) {
        $this->warn('No hay empresas activas para migrar.');

        return self::SUCCESS;
    }

    /** @var TenantConnectionManager $manager */
    $manager = app(TenantConnectionManager::class);
    $hasErrors = false;

    foreach ($tenants as $tenant) {
        try {
            $manager->connect($tenant);

            $exitCode = Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => config('tenancy.tenant_migrations_path', 'database/migrations/tenant'),
                '--realpath' => false,
                '--force' => true,
            ]);

            if ($exitCode !== 0) {
                $hasErrors = true;
                $this->error("[{$tenant->slug}] ERROR ejecutando migraciones tenant.");
                continue;
            }

            $this->info("[{$tenant->slug}] Migraciones aplicadas correctamente.");
        } catch (\Throwable $exception) {
            $hasErrors = true;
            $this->error("[{$tenant->slug}] ERROR - {$exception->getMessage()}");
        } finally {
            $manager->disconnect();
        }
    }

    return $hasErrors ? self::FAILURE : self::SUCCESS;
})->purpose('Ejecuta migraciones tenant para empresas activas.');

Artisan::command('tenancy:seed-platforms-active {--tenant= : Slug de empresa especifica}', function (): int {
    $query = Tenant::query()->where('status', 'active');

    if ($slug = $this->option('tenant')) {
        $query->where('slug', $slug);
    }

    $tenants = $query->orderBy('id')->get();

    if ($tenants->isEmpty()) {
        $this->warn('No hay empresas activas para sembrar plataformas.');

        return self::SUCCESS;
    }

    /** @var TenantConnectionManager $manager */
    $manager = app(TenantConnectionManager::class);

    $defaults = [
        ['nombre' => 'Netflix', 'activa' => true, 'perfiles_por_cuenta' => 5],
        ['nombre' => 'Amazon Prime Video', 'activa' => true, 'perfiles_por_cuenta' => 5],
        ['nombre' => 'HBO Max', 'activa' => true, 'perfiles_por_cuenta' => 5],
    ];

    $hasErrors = false;

    foreach ($tenants as $tenant) {
        try {
            $manager->connect($tenant);

            foreach ($defaults as $row) {
                DB::connection('tenant')->table('plataformas')->updateOrInsert(
                    ['nombre' => $row['nombre']],
                    [
                        'activa' => $row['activa'],
                        'perfiles_por_cuenta' => $row['perfiles_por_cuenta'],
                        'updated_at' => now(),
                        'created_at' => now(),
                    ],
                );
            }

            $this->info("[{$tenant->slug}] Plataformas base listas.");
        } catch (\Throwable $exception) {
            $hasErrors = true;
            $this->error("[{$tenant->slug}] ERROR - {$exception->getMessage()}");
        } finally {
            $manager->disconnect();
        }
    }

    return $hasErrors ? self::FAILURE : self::SUCCESS;
})->purpose('Inserta/actualiza plataformas base en empresas activas.');

Artisan::command('tenancy:sync-perfiles-from-cuentas {--tenant= : Slug de empresa especifica}', function (): int {
    $query = Tenant::query()->where('status', 'active');

    if ($slug = $this->option('tenant')) {
        $query->where('slug', $slug);
    }

    $tenants = $query->orderBy('id')->get();

    if ($tenants->isEmpty()) {
        $this->warn('No hay empresas activas para sincronizar.');

        return self::SUCCESS;
    }

    /** @var TenantConnectionManager $manager */
    $manager = app(TenantConnectionManager::class);
    $hasErrors = false;

    foreach ($tenants as $tenant) {
        try {
            $manager->connect($tenant);

            $updatedCuentaData = DB::connection('tenant')->affectingStatement(
                'UPDATE perfiles p
                 INNER JOIN cuentas c ON c.id = p.cuenta_id
                 SET p.proveedor_nombre = c.proveedor,
                     p.correo_cuenta = LOWER(TRIM(c.correo)),
                     p.contrasena_cuenta = c.contrasena,
                     p.fecha_inicio = c.fecha_inicio,
                     p.fecha_corte = c.fecha_corte,
                     p.updated_at = NOW()'
            );

            $updatedPins = DB::connection('tenant')->affectingStatement(
                'UPDATE perfiles p
                 INNER JOIN cuenta_perfiles cp
                    ON cp.cuenta_id = p.cuenta_id
                   AND CAST(cp.numero_perfil AS CHAR) = p.nombre_perfil
                 SET p.pin = cp.pin,
                     p.updated_at = NOW()'
            );

            $this->info("[{$tenant->slug}] Sincronizado. Perfiles actualizados: {$updatedCuentaData}, PINs actualizados: {$updatedPins}.");
        } catch (\Throwable $exception) {
            $hasErrors = true;
            $this->error("[{$tenant->slug}] ERROR - {$exception->getMessage()}");
        } finally {
            $manager->disconnect();
        }
    }

    return $hasErrors ? self::FAILURE : self::SUCCESS;
})->purpose('Sincroniza datos de cuentas y PINs hacia perfiles en empresas activas.');
