<?php

namespace App\Services\Tenancy;

use App\Models\Empresa;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use App\Support\Tenancy\TenantConnectionManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class ProvisionTenantService
{
    public function __construct(
        private readonly TenantConnectionManager $tenantConnectionManager,
    ) {
    }

    /**
     * Provisions a full tenant environment in an all-or-nothing flow.
     *
     * @param  array<string, mixed>  $payload
     */
    public function provision(array $payload, User $actor): Tenant
    {
        if (! $actor->is_super_admin) {
            throw new RuntimeException('Solo el SuperAdmin global puede crear empresas.');
        }

        $defaultConnection = DB::connection(config('database.default'));
        $defaultUsername = (string) $defaultConnection->getConfig('username');
        $defaultPassword = (string) $defaultConnection->getConfig('password');
        $defaultHost = (string) $defaultConnection->getConfig('host');
        $defaultPort = (int) $defaultConnection->getConfig('port');

        $slug = Str::slug((string) Arr::get($payload, 'slug', Arr::get($payload, 'company_name')));

        if (Tenant::withTrashed()->where('slug', $slug)->exists()) {
            throw ValidationException::withMessages([
                'slug' => 'El slug ya existe. Usa uno diferente.',
            ]);
        }

        $databaseName = $this->resolveDatabaseName((string) Arr::get($payload, 'database_name'), (string) Arr::get($payload, 'company_name'));

        if (Tenant::withTrashed()->where('db_database', $databaseName)->exists()) {
            throw ValidationException::withMessages([
                'database_name' => 'El nombre de base de datos ya existe para otro tenant.',
            ]);
        }

        $adminEmail = Str::lower(trim((string) Arr::get($payload, 'admin_email')));
        $this->ensureAdminEmailIsUniqueAcrossTenants($adminEmail);

        $tenant = null;

        DB::beginTransaction();

        try {
            $empresa = Empresa::query()->create([
                'nombre' => Arr::get($payload, 'company_name'),
                'pais_id' => Arr::get($payload, 'pais_id'),
                'departamento_id' => Arr::get($payload, 'departamento_id'),
                'municipio_id' => Arr::get($payload, 'municipio_id'),
                'direccion' => Arr::get($payload, 'direccion'),
                'telefono' => Arr::get($payload, 'telefono'),
                'rtn' => Arr::get($payload, 'rtn', $this->generateFallbackRtn()),
            ]);

            $tenant = Tenant::query()->create([
                'empresa_id' => $empresa->id,
                'name' => Arr::get($payload, 'company_name'),
                'slug' => $slug,
                'status' => 'provisioning',
                'db_driver' => Arr::get($payload, 'db_driver', config('tenancy.tenant_default_driver')),
                'db_host' => Arr::get($payload, 'db_host', $defaultHost),
                'db_port' => (int) Arr::get($payload, 'db_port', $defaultPort),
                'db_database' => $databaseName,
                'db_username' => Arr::get($payload, 'db_username', $defaultUsername),
                'db_password' => Arr::get($payload, 'db_password', $defaultPassword),
                'bootstrap_admin_name' => Arr::get($payload, 'admin_name'),
                'bootstrap_admin_email' => $adminEmail,
                'bootstrap_admin_password' => Arr::get($payload, 'admin_password'),
                'db_schema' => Arr::get($payload, 'db_schema', 'public'),
                'created_by' => $actor->id,
            ]);

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

        try {
            $this->createPhysicalDatabase($tenant);
            $this->runTenantMigrations($tenant);
            $this->seedDefaultPlatforms($tenant);
            $this->seedInitialTenantAdmin($tenant, [
                'name' => Arr::get($payload, 'admin_name'),
                'email' => Arr::get($payload, 'admin_email'),
                'password' => Arr::get($payload, 'admin_password'),
            ]);

            $tenant->forceFill([
                'status' => 'active',
                'provisioning_error' => null,
                'provisioned_at' => now(),
            ])->save();
        } catch (Throwable $exception) {
            $this->safeRollbackProvisioning($tenant, $exception);

            throw $exception;
        }

        return $tenant->fresh();
    }

    /**
     * @param  array{name:mixed,email:mixed,password:mixed}  $adminData
     */
    public function retryFailedProvisioning(Tenant $tenant, User $actor, array $adminData): Tenant
    {
        if (! $actor->is_super_admin) {
            throw new RuntimeException('Solo el SuperAdmin global puede reintentar aprovisionamiento.');
        }

        if ($tenant->status !== 'failed') {
            throw ValidationException::withMessages([
                'tenant' => 'Solo se puede reintentar aprovisionamiento en tenants con estado failed.',
            ]);
        }

        $tenant->forceFill([
            'status' => 'provisioning',
            'provisioning_error' => null,
            'db_host' => (string) $this->getDefaultDbConfig('host'),
            'db_port' => (int) $this->getDefaultDbConfig('port'),
            'db_username' => (string) $this->getDefaultDbConfig('username'),
            'db_password' => (string) $this->getDefaultDbConfig('password'),
            'bootstrap_admin_name' => Arr::get($adminData, 'admin_name'),
            'bootstrap_admin_email' => Str::lower(trim((string) Arr::get($adminData, 'admin_email'))),
            'bootstrap_admin_password' => Arr::get($adminData, 'admin_password'),
        ])->save();

        $this->ensureAdminEmailIsUniqueAcrossTenants($tenant->bootstrap_admin_email, $tenant->id);

        try {
            // Start from a clean state before replaying the provisioning workflow.
            $this->dropPhysicalDatabase($tenant);
        } catch (Throwable) {
            // Ignore if the database does not exist yet.
        }

        try {
            $this->createPhysicalDatabase($tenant);
            $this->runTenantMigrations($tenant);
            $this->seedDefaultPlatforms($tenant);
            $this->seedInitialTenantAdmin($tenant, $adminData);

            $tenant->forceFill([
                'status' => 'active',
                'provisioning_error' => null,
                'provisioned_at' => now(),
            ])->save();
        } catch (Throwable $exception) {
            $this->safeRollbackProvisioning($tenant, $exception);

            throw $exception;
        }

        return $tenant->fresh();
    }

    private function resolveDatabaseName(string $explicitName, string $companyName): string
    {
        if ($explicitName !== '') {
            return $this->sanitizeIdentifier($explicitName);
        }

        $prefix = (string) config('tenancy.tenant_database_prefix', 'tenant_');
        $slug = Str::slug($companyName, '_');

        return $this->sanitizeIdentifier($prefix.$slug);
    }

    private function sanitizeIdentifier(string $value): string
    {
        return (string) Str::of($value)
            ->lower()
            ->replaceMatches('/[^a-z0-9_]/', '_')
            ->limit(50, '')
            ->trim('_');
    }

    private function getDefaultDbConfig(string $key): mixed
    {
        return DB::connection(config('database.default'))->getConfig($key);
    }

    private function ensureAdminEmailIsUniqueAcrossTenants(string $email, ?int $ignoreTenantId = null): void
    {
        if ($email === '') {
            throw ValidationException::withMessages([
                'admin_email' => 'El correo del admin es obligatorio.',
            ]);
        }

        $tenants = Tenant::query()
            ->where('status', 'active')
            ->when($ignoreTenantId !== null, fn ($query) => $query->where('id', '!=', $ignoreTenantId))
            ->orderBy('id')
            ->get();

        foreach ($tenants as $tenant) {
            try {
                $this->tenantConnectionManager->connect($tenant);

                if (! Schema::connection('tenant')->hasTable('users')) {
                    continue;
                }

                $exists = DB::connection('tenant')
                    ->table('users')
                    ->where('email', $email)
                    ->exists();

                if ($exists) {
                    throw ValidationException::withMessages([
                        'admin_email' => 'Este correo ya existe en otra empresa. Debe ser unico entre tenants.',
                    ]);
                }
            } finally {
                $this->tenantConnectionManager->disconnect();
            }
        }
    }

    private function generateFallbackRtn(): string
    {
        do {
            $candidate = 'AUTO'.substr(strtoupper((string) Str::ulid()), 0, 16);
        } while (Empresa::query()->where('rtn', $candidate)->exists());

        return $candidate;
    }

    private function createPhysicalDatabase(Tenant $tenant): void
    {
        $driver = $tenant->db_driver;
        $databaseName = $tenant->db_database;

        if ($driver === 'mysql') {
            DB::statement(sprintf('CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci', $databaseName));

            return;
        }

        if ($driver === 'pgsql') {
            $exists = DB::selectOne('SELECT 1 FROM pg_database WHERE datname = ?', [$databaseName]);

            if (! $exists) {
                DB::statement(sprintf('CREATE DATABASE "%s"', $databaseName));
            }

            return;
        }

        throw new RuntimeException('Driver no soportado para aprovisionamiento: '.$driver);
    }

    private function runTenantMigrations(Tenant $tenant): void
    {
        $this->tenantConnectionManager->connect($tenant);

        $exitCode = Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => config('tenancy.tenant_migrations_path', 'database/migrations/tenant'),
            '--realpath' => false,
            '--force' => true,
        ]);

        if ($exitCode !== 0) {
            throw new RuntimeException('No se pudieron ejecutar las migraciones del tenant.');
        }
    }

    private function seedDefaultPlatforms(Tenant $tenant): void
    {
        $this->tenantConnectionManager->connect($tenant);

        $defaults = [
            ['nombre' => 'Netflix', 'activa' => true, 'perfiles_por_cuenta' => 5],
            ['nombre' => 'Amazon Prime Video', 'activa' => true, 'perfiles_por_cuenta' => 5],
            ['nombre' => 'HBO Max', 'activa' => true, 'perfiles_por_cuenta' => 5],
        ];

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
    }

    /**
     * @param  array{name:mixed,email:mixed,password:mixed}  $adminData
     */
    private function seedInitialTenantAdmin(Tenant $tenant, array $adminData): void
    {
        $this->tenantConnectionManager->connect($tenant);

        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');

        $admin = TenantUser::query()->create([
            'name' => (string) $adminData['name'],
            'email' => Str::lower(trim((string) $adminData['email'])),
            'password' => Hash::make((string) $adminData['password']),
        ]);

        $permissions = [
            'dashboard.view',
            'plataformas.view',
            'plataformas.create',
            'plataformas.edit',
            'plataformas.delete',
            'clientes.view',
            'clientes.create',
            'clientes.edit',
            'clientes.delete',
            'cuentas.view',
            'cuentas.create',
            'cuentas.edit',
            'cuentas.delete',
            'cuentas_reportadas.view',
            'cuentas_reportadas.solve',
            'cuentas_reportadas.delete',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.roles.manage',
        ];

        $permissionIds = [];

        foreach ($permissions as $permissionName) {
            $permissionId = DB::connection('tenant')
                ->table($tableNames['permissions'])
                ->where('name', $permissionName)
                ->where('guard_name', 'tenant_web')
                ->value('id');

            if (! $permissionId) {
                $permissionId = DB::connection('tenant')
                    ->table($tableNames['permissions'])
                    ->insertGetId([
                        'name' => $permissionName,
                        'guard_name' => 'tenant_web',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            $permissionIds[] = (int) $permissionId;
        }

        $roleId = DB::connection('tenant')
            ->table($tableNames['roles'])
            ->where('name', 'admin_empresa')
            ->where('guard_name', 'tenant_web')
            ->value('id');

        if (! $roleId) {
            $roleId = DB::connection('tenant')
                ->table($tableNames['roles'])
                ->insertGetId([
                    'name' => 'admin_empresa',
                    'guard_name' => 'tenant_web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        foreach ($permissionIds as $permissionId) {
            DB::connection('tenant')
                ->table($tableNames['role_has_permissions'])
                ->updateOrInsert([
                    $columnNames['permission_pivot_key'] ?? 'permission_id' => $permissionId,
                    $columnNames['role_pivot_key'] ?? 'role_id' => $roleId,
                ], []);
        }

        DB::connection('tenant')
            ->table($tableNames['model_has_roles'])
            ->updateOrInsert([
                $columnNames['role_pivot_key'] ?? 'role_id' => $roleId,
                'model_type' => TenantUser::class,
                $columnNames['model_morph_key'] => $admin->id,
            ], []);
    }

    private function safeRollbackProvisioning(Tenant $tenant, Throwable $reason): void
    {
        try {
            $this->dropPhysicalDatabase($tenant);
        } catch (Throwable) {
            // Keep the original exception as the main failure reason.
        }

        $tenant->forceFill([
            'status' => 'failed',
            'provisioning_error' => mb_substr($reason->getMessage(), 0, 65535),
        ])->save();
    }

    private function dropPhysicalDatabase(Tenant $tenant): void
    {
        if ($tenant->db_driver === 'mysql') {
            DB::statement(sprintf('DROP DATABASE IF EXISTS `%s`', $tenant->db_database));

            return;
        }

        if ($tenant->db_driver === 'pgsql') {
            DB::statement(sprintf('DROP DATABASE IF EXISTS "%s"', $tenant->db_database));

            return;
        }

        throw new RuntimeException('Driver no soportado para rollback: '.$tenant->db_driver);
    }
}
