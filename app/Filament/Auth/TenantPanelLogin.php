<?php

namespace App\Filament\Auth;

use App\Models\Tenant;
use App\Support\Tenancy\TenantConnectionManager;
use App\Support\Tenancy\TenantSessionFingerprint;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TenantPanelLogin extends Login
{
    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        $email = Str::lower(trim((string) ($data['email'] ?? '')));
        $password = (string) ($data['password'] ?? '');
        $remember = (bool) ($data['remember'] ?? false);

        $matchingTenants = [];

        $tenants = Tenant::query()
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        foreach ($tenants as $tenant) {
            app(TenantConnectionManager::class)->connect($tenant);

            try {
                if (! Schema::connection('tenant')->hasTable('users')) {
                    continue;
                }

                $exists = DB::connection('tenant')
                    ->table('users')
                    ->where('email', $email)
                    ->exists();

                if ($exists) {
                    $matchingTenants[] = $tenant;
                }
            } finally {
                app(TenantConnectionManager::class)->disconnect();
            }
        }

        if (count($matchingTenants) > 1) {
            throw ValidationException::withMessages([
                'data.email' => 'Este correo existe en multiples empresas. Contacta soporte para unificar el acceso.',
            ]);
        }

        if (count($matchingTenants) === 0) {
            $this->throwFailureValidationException();
        }

        $tenant = $matchingTenants[0];

        $currentTenantId = (int) session()->get('tenant_id', 0);

        if ($currentTenantId !== 0 && $currentTenantId !== (int) $tenant->id) {
            Filament::auth()->logout();
            session()->invalidate();
            session()->regenerateToken();
        }

        app(TenantConnectionManager::class)->connect($tenant);

        if (! Filament::auth()->attempt([
            'email' => $email,
            'password' => $password,
        ], $remember)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        session()->regenerate();
        session()->put('tenant_id', $tenant->id);
        session()->put('tenant_slug', $tenant->slug);
        session()->put('tenant_fingerprint', TenantSessionFingerprint::make($tenant, Filament::auth()->id(), request()->userAgent()));
        session()->forget('url.intended');

        Notification::make()
            ->title('Bienvenido a tu empresa')
            ->success()
            ->send();

        return app(LoginResponse::class);
    }
}
