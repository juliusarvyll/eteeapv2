<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\PriorityAlertsWidget;

class ApplicationDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static string $view = 'filament.pages.application-dashboard';

    protected static ?string $navigationLabel = 'Applications Dashboard';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Application Priority Alerts Dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            PriorityAlertsWidget::class,
        ];
    }
}
