<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use App\Filament\Pages\Notifications;
use App\Filament\Pages\ApplicationDashboard;
use App\Filament\Pages\ApplicationReports;
use App\Filament\Widgets\PriorityAlertsWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandLogo(asset('images/SPUPLogo.png'))
            ->brandLogoHeight('50px')
            ->colors([
                'primary' => Color::Green,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                Notifications::class,
                ApplicationDashboard::class,
                ApplicationReports::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\StatsOverviewWidget::class,
                \App\Filament\Widgets\UniversityInfoWidget::class,
                PriorityAlertsWidget::class,
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
                \App\Http\Middleware\RedirectByRoleMiddleware::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentEditProfilePlugin::make()
                ->slug('my-profile')
                ->setTitle('My Profile')
                ->setNavigationLabel('My Profile')
                ->setIcon('heroicon-o-user')
                ->setSort(10)
                ->canAccess(true)
                ->shouldRegisterNavigation(true)
                ->shouldShowBrowserSessionsForm(true)
            ]);
    }
}
