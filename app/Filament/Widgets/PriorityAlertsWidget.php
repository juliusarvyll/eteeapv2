<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\PersonalInfo;
use App\Filament\Resources\PersonalInfoResource;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Colors\Color;
use Illuminate\Support\HtmlString;

class PriorityAlertsWidget extends BaseWidget
{
    protected ?string $heading = 'Application Priority Alerts';

    protected int|string|array $columnSpan = 'full';

    // Gets information from PersonalInfoResource
    protected function getStats(): array
    {
        // Calculate critical applications (more than 5 days old)
        $criticalDate = Carbon::now()->subDays(PersonalInfoResource::CRITICAL_THRESHOLD);
        $criticalCount = PersonalInfo::where('status', 'pending')
            ->whereDate('created_at', '<', $criticalDate->toDateString())
            ->count();

        // Calculate warning applications (exactly 5 days old)
        $warningDate = Carbon::now()->subDays(PersonalInfoResource::WARNING_THRESHOLD);
        $warningCount = PersonalInfo::where('status', 'pending')
            ->whereDate('created_at', $warningDate->toDateString())
            ->count();

        // Calculate normal applications (less than 5 days old)
        $normalCount = PersonalInfo::where('status', 'pending')
            ->whereDate('created_at', '>', $warningDate->toDateString())
            ->count();

        // Create stat widgets
        return [
            Stat::make('Critical Applications', $criticalCount)
                ->description('More than 5 days pending')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger')
                ->url(route('filament.admin.resources.personal-infos.index', [
                    'tableFilters[pending_priority][priority]' => 'critical'
                ]))
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Warning Applications', $warningCount)
                ->description('Exactly 5 days pending')
                ->descriptionIcon('heroicon-m-bell-alert')
                ->color('warning')
                ->url(route('filament.admin.resources.personal-infos.index', [
                    'tableFilters[pending_priority][priority]' => 'warning'
                ]))
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Normal Applications', $normalCount)
                ->description('Less than 5 days pending')
                ->descriptionIcon('heroicon-m-clock')
                ->color('success')
                ->url(route('filament.admin.resources.personal-infos.index', [
                    'tableFilters[pending_priority][priority]' => 'all_pending'
                ]))
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),
        ];
    }
}
