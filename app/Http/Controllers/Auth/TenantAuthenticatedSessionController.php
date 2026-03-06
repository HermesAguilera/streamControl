<?php

namespace App\Http\Controllers\Auth;

use App\Models\Tenant;
use App\Support\Tenancy\TenantConnectionManager;
use App\Support\Tenancy\TenantSessionFingerprint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TenantAuthenticatedSessionController
{
    public function __construct(
        private readonly TenantConnectionManager $tenantConnectionManager,
    ) {
    }

    public function create(): View
    {
        return view('auth.tenant-login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $email = Str::lower(trim((string) $credentials['email']));
        $password = (string) $credentials['password'];

        $tenants = Tenant::query()
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        $matchingTenants = [];

        foreach ($tenants as $tenant) {
            $this->tenantConnectionManager->connect($tenant);

            $emailExistsInTenant = DB::connection('tenant')
                ->table('users')
                ->where('email', $email)
                ->exists();

            if (! $emailExistsInTenant) {
                continue;
            }

             $matchingTenants[] = $tenant;

            if (count($matchingTenants) > 1) {
                throw ValidationException::withMessages([
                    'email' => 'Este correo existe en multiples empresas. Contacta soporte para unificar el acceso.',
                ]);
            }

            if (! Auth::guard('tenant_web')->attempt([
                'email' => $email,
                'password' => $password,
            ], $request->boolean('remember'))) {
                continue;
            }

            $currentTenantId = (int) $request->session()->get('tenant_id', 0);

            if ($currentTenantId !== 0 && $currentTenantId !== (int) $tenant->id) {
                Auth::guard('tenant_web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if (! Auth::guard('tenant_web')->attempt([
                    'email' => $email,
                    'password' => $password,
                ], $request->boolean('remember'))) {
                    continue;
                }
            }

            $request->session()->regenerate();
            $request->session()->put('tenant_id', $tenant->id);
            $request->session()->put('tenant_slug', $tenant->slug);
            $request->session()->put('tenant_fingerprint', TenantSessionFingerprint::make($tenant, Auth::guard('tenant_web')->id(), $request->userAgent()));
            $request->session()->forget('url.intended');

            return redirect('/tenant/dashboard');
        }

        throw ValidationException::withMessages([
            'email' => 'Credenciales incorrectas.',
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('tenant_web')->logout();

        $request->session()->forget(['tenant_id', 'tenant_slug', 'tenant_fingerprint']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/tenant/login');
    }
}
