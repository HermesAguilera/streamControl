<?php

namespace App\Services\Tenancy;

use App\Models\Tenant;
use App\Support\Tenancy\TenantConnectionManager;
use Illuminate\Support\Facades\Schema;
use Throwable;

class TenantHealthCheckService
{
    private const REQUIRED_TABLES = [
        'migrations',
        'users',
        'roles',
        'permissions',
        'model_has_roles',
        'role_has_permissions',
        'cache',
        'cache_locks',
        'plataformas',
        'cuentas',
        'perfiles',
        'cuenta_perfiles',
        'cuentas_reportadas',
    ];

    public function __construct(
        private readonly TenantConnectionManager $tenantConnectionManager,
    ) {
    }

    /**
     * @return array{ok:bool,status:string,message:string,missing_tables:array<int,string>}
     */
    public function check(Tenant $tenant): array
    {
        try {
            $this->tenantConnectionManager->connect($tenant);

            $missing = [];

            foreach (self::REQUIRED_TABLES as $table) {
                if (! Schema::connection('tenant')->hasTable($table)) {
                    $missing[] = $table;
                }
            }

            if ($missing !== []) {
                return [
                    'ok' => false,
                    'status' => 'warning',
                    'message' => 'Faltan tablas requeridas: '.implode(', ', $missing),
                    'missing_tables' => $missing,
                ];
            }

            return [
                'ok' => true,
                'status' => 'ok',
                'message' => 'Conectividad y tablas requeridas en estado correcto.',
                'missing_tables' => [],
            ];
        } catch (Throwable $exception) {
            return [
                'ok' => false,
                'status' => 'error',
                'message' => 'Error de conexion/verificacion: '.$exception->getMessage(),
                'missing_tables' => [],
            ];
        } finally {
            $this->tenantConnectionManager->disconnect();
        }
    }
}
