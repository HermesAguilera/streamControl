<?php

namespace App\Http\Middleware;

use App\Models\TenantPermission;
use App\Models\TenantRole;
use App\Models\Tenant;
use App\Support\Tenancy\TenantConnectionManager;
use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenantConnection
{
    public function __construct(
        private readonly TenantConnectionManager $tenantConnectionManager,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = $request->session()->get('tenant_id');

        if (! $tenantId) {
            $this->tenantConnectionManager->disconnect();

            return $next($request);
        }

        if ($tenantId) {
            $tenant = Tenant::query()
                ->where('status', 'active')
                ->find($tenantId);

            if ($tenant) {
                $this->tenantConnectionManager->connect($tenant);

                $permissionRegistrar = app(PermissionRegistrar::class);
                $permissionRegistrar
                    ->setRoleClass(TenantRole::class)
                    ->setPermissionClass(TenantPermission::class);
                $permissionRegistrar->clearPermissionsCollection();

                return $next($request);
            }

            $request->session()->forget(['tenant_id', 'tenant_slug']);
            $this->tenantConnectionManager->disconnect();
        }

        return $next($request);
    }
}
