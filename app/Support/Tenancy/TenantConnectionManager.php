<?php

namespace App\Support\Tenancy;

use App\Models\Tenant;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantConnectionManager
{
    private function getCentralConnectionName(): string
    {
        return (string) config('tenancy.central_connection', env('DB_CONNECTION', 'mysql'));
    }

    /**
     * Injects runtime credentials for the tenant connection and reconnects it.
     */
    public function connect(Tenant $tenant): void
    {
        $defaultConnection = DB::connection($this->getCentralConnectionName());

        Config::set('database.connections.tenant', [
            'driver' => $tenant->db_driver,
            'host' => $tenant->db_host,
            'port' => $tenant->db_port,
            'database' => $tenant->db_database,
            'username' => $tenant->db_username,
            'password' => $tenant->db_password,
            'unix_socket' => $tenant->db_driver === 'mysql' ? $defaultConnection->getConfig('unix_socket') : null,
            'charset' => $tenant->db_driver === 'pgsql' ? 'utf8' : 'utf8mb4',
            'collation' => $tenant->db_driver === 'pgsql' ? null : 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => $tenant->db_schema ?: 'public',
            'sslmode' => 'prefer',
        ]);

        DB::purge('tenant');
        DB::reconnect('tenant');
        DB::setDefaultConnection('tenant');
        Config::set('database.default', 'tenant');
    }

    public function disconnect(): void
    {
        DB::disconnect('tenant');
        DB::purge('tenant');

        $centralConnection = $this->getCentralConnectionName();
        DB::setDefaultConnection($centralConnection);
        Config::set('database.default', $centralConnection);
    }
}
