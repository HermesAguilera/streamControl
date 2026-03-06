<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Support\Tenancy\TenantConnectionManager;
use App\Support\Tenancy\TenantSessionFingerprint;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantSessionIsolation
{
    public function __construct(
        private readonly TenantConnectionManager $tenantConnectionManager,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = $request->session()->get('tenant_id');

        if (! $tenantId) {
            return $next($request);
        }

        $tenant = Tenant::query()
            ->where('status', 'active')
            ->find($tenantId);

        if (! $tenant) {
            return $this->forceSessionReset($request, 'tenant_not_found');
        }

        if ($request->session()->get('tenant_slug') !== $tenant->slug) {
            return $this->forceSessionReset($request, 'tenant_slug_mismatch');
        }

        $userId = Auth::guard('tenant_web')->id();

        if (! $userId) {
            return $next($request);
        }

        $expected = TenantSessionFingerprint::make($tenant, $userId, $request->userAgent());
        $current = (string) $request->session()->get('tenant_fingerprint', '');

        if ($current === '') {
            $request->session()->put('tenant_fingerprint', $expected);

            return $next($request);
        }

        if (! hash_equals($current, $expected)) {
            return $this->forceSessionReset($request, 'tenant_fingerprint_mismatch');
        }

        return $next($request);
    }

    private function forceSessionReset(Request $request, string $reason): RedirectResponse
    {
        Log::warning('Tenant session isolation reset triggered.', [
            'reason' => $reason,
            'tenant_id' => $request->session()->get('tenant_id'),
            'tenant_slug' => $request->session()->get('tenant_slug'),
            'tenant_fingerprint' => $request->session()->get('tenant_fingerprint'),
            'tenant_user_id' => Auth::guard('tenant_web')->id(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        Auth::guard('tenant_web')->logout();
        $request->session()->forget(['tenant_id', 'tenant_slug', 'tenant_fingerprint']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $this->tenantConnectionManager->disconnect();

        return redirect('/admin/login');
    }
}
