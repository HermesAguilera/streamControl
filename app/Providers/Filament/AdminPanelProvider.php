<?php

namespace App\Providers\Filament;

use App\Filament\Pages\AjustesGenerales;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\InformeVentas;
use Filament\Navigation\MenuItem;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->darkMode()
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                'Streaming',
                'Administración',
            ])
            ->renderHook(
                'panels::topbar.end',
                fn () => view('components.user-topbar-actions'))
            ->renderHook(
                'panels::body.end',
                fn () => view('components.sidebar-expiry-badge'))
            ->userMenuItems([
                MenuItem::make()
                    ->label('Ajustes')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn (): string => AjustesGenerales::getUrl()),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->pages([
                Dashboard::class,
                InformeVentas::class,
                AjustesGenerales::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}