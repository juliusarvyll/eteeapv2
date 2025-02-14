<?php

namespace App\Filament\Widgets;

use App\Models\PersonalInfo;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';

    // Set to 3 columns for better layout
    protected int | string | array $columnSpan = 3;

    protected function getStats(): array
    {
        return Cache::remember('dashboard_stats', 10, function () {
            $now = Carbon::now();
            
            $stats = PersonalInfo::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN YEAR(created_at) = ? THEN 1 ELSE 0 END) as yearly,
                SUM(CASE WHEN YEAR(created_at) = ? AND MONTH(created_at) = ? THEN 1 ELSE 0 END) as monthly,
                SUM(CASE WHEN DATE(created_at) = ? THEN 1 ELSE 0 END) as daily
            ', [
                $now->year,
                $now->year,
                $now->month,
                $now->toDateString()
            ])->first();

            return [
                // Status Row (3 columns)
                Stat::make('Pending Applications', $stats->pending)
                    ->description('Awaiting review')
                    ->descriptionIcon('heroicon-m-clock')
                    ->chart([7, 4, 6, 8, $stats->pending])
                    ->color('warning'),

                Stat::make('Approved Applications', $stats->approved)
                    ->description('Approved applicants')
                    ->descriptionIcon('heroicon-m-check-circle')
                    ->chart([3, 5, 7, 8, $stats->approved])
                    ->color('success'),

                Stat::make('Rejected Applications', $stats->rejected)
                    ->description('Rejected applicants')
                    ->descriptionIcon('heroicon-m-x-circle')
                    ->chart([2, 3, 4, 5, $stats->rejected])
                    ->color('danger'),

                // Timeline Row (4 columns)
                Stat::make('Total Applicants', $stats->total)
                    ->description('All time')
                    ->descriptionIcon('heroicon-m-users')
                    ->chart([2, 4, 6, 8, $stats->total])
                    ->color('success'),

                Stat::make('Yearly Applicants', $stats->yearly)
                    ->description($now->year)
                    ->descriptionIcon('heroicon-m-calendar')
                    ->chart([1, 3, 5, 7, $stats->yearly])
                    ->color('info'),

                Stat::make('Monthly Applicants', $stats->monthly)
                    ->description($now->format('F'))
                    ->descriptionIcon('heroicon-m-calendar-days')
                    ->chart([1, 2, 3, 4, $stats->monthly])
                    ->color('info'),

                Stat::make('Daily Applicants', $stats->daily)
                    ->description('Today')
                    ->descriptionIcon('heroicon-m-clock')
                    ->chart([0, 1, 2, 3, $stats->daily])
                    ->color('info'),
            ];
        });
    }
}
