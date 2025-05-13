<?php

namespace App\Services;

use App\Models\PersonalInfo;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApplicationReportingService
{
    /**
     * Get monthly application data
     *
     * @param int $months Number of months to include
     * @return array
     */
    public function getMonthlyApplicationData(int $months = 12): array
    {
        try {
            $endDate = Carbon::now()->endOfMonth();
            $startDate = Carbon::now()->subMonths($months - 1)->startOfMonth();

            // Generate month labels
            $periods = collect();
            $currentDate = $startDate->copy();

            while ($currentDate->lte($endDate)) {
                $periods->push($currentDate->format('Y-m'));
                $currentDate->addMonth();
            }

            // Get application counts by month and status
            $applications = PersonalInfo::select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    'status',
                    DB::raw('COUNT(*) as count')
                )
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->groupBy('year', 'month', 'status')
                ->get();

            // Organize data by month
            $monthlyData = [];
            $trendData = [];
            $previousCount = null;

            foreach ($periods as $period) {
                list($year, $month) = explode('-', $period);

                $pending = $applications->where('year', $year)
                    ->where('month', $month)
                    ->where('status', 'pending')
                    ->sum('count');

                $approved = $applications->where('year', $year)
                    ->where('month', $month)
                    ->where('status', 'approved')
                    ->sum('count');

                $rejected = $applications->where('year', $year)
                    ->where('month', $month)
                    ->where('status', 'rejected')
                    ->sum('count');

                $total = $pending + $approved + $rejected;

                $monthlyData[$period] = [
                    'pending' => $pending,
                    'approved' => $approved,
                    'rejected' => $rejected,
                    'total' => $total
                ];

                // Calculate trend data
                $trend = 'neutral';
                $percentageChange = 0;

                if ($previousCount !== null && $previousCount > 0) {
                    $percentageChange = round((($total - $previousCount) / $previousCount) * 100, 1);
                    $trend = $percentageChange > 0 ? 'up' : ($percentageChange < 0 ? 'down' : 'neutral');
                }

                $trendData[$period] = [
                    'count' => $total,
                    'trend' => $trend,
                    'percentage_change' => abs($percentageChange)
                ];

                $previousCount = $total;
            }

            // Calculate overall status distribution
            $statusTotals = [
                'pending' => $applications->where('status', 'pending')->sum('count'),
                'approved' => $applications->where('status', 'approved')->sum('count'),
                'rejected' => $applications->where('status', 'rejected')->sum('count'),
            ];

            $totalApplications = array_sum($statusTotals);
            $statusData = [];

            foreach ($statusTotals as $status => $count) {
                $percentage = $totalApplications > 0 ? round(($count / $totalApplications) * 100, 1) : 0;
                $statusData[$status] = [
                    'count' => $count,
                    'percentage' => $percentage
                ];
            }

            return [
                'period' => $startDate->format('M Y') . ' - ' . $endDate->format('M Y'),
                'monthly_data' => $monthlyData,
                'trend_data' => $trendData,
                'status_data' => $statusData,
                'total_applications' => $totalApplications
            ];

        } catch (\Exception $e) {
            Log::error('Error generating monthly application data: ' . $e->getMessage());
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get quarterly application data
     *
     * @param int $quarters Number of quarters to include
     * @return array
     */
    public function getQuarterlyApplicationData(int $quarters = 4): array
    {
        try {
            $endDate = Carbon::now()->endOfQuarter();
            $startDate = Carbon::now()->subQuarters($quarters - 1)->startOfQuarter();

            // Generate quarter labels
            $periods = collect();
            $currentDate = $startDate->copy();

            while ($currentDate->lte($endDate)) {
                $year = $currentDate->year;
                $quarter = ceil($currentDate->month / 3);
                $periods->push("$year-Q$quarter");
                $currentDate->addQuarter();
            }

            // Get application counts by quarter and status
            $applications = PersonalInfo::select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('QUARTER(created_at) as quarter'),
                    'status',
                    DB::raw('COUNT(*) as count')
                )
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->groupBy('year', 'quarter', 'status')
                ->get();

            // Organize data by quarter
            $quarterlyData = [];
            $trendData = [];
            $previousCount = null;

            foreach ($periods as $period) {
                list($year, $quarter) = explode('-Q', $period);

                $pending = $applications->where('year', $year)
                    ->where('quarter', $quarter)
                    ->where('status', 'pending')
                    ->sum('count');

                $approved = $applications->where('year', $year)
                    ->where('quarter', $quarter)
                    ->where('status', 'approved')
                    ->sum('count');

                $rejected = $applications->where('year', $year)
                    ->where('quarter', $quarter)
                    ->where('status', 'rejected')
                    ->sum('count');

                $total = $pending + $approved + $rejected;

                $quarterlyData[$period] = [
                    'pending' => $pending,
                    'approved' => $approved,
                    'rejected' => $rejected,
                    'total' => $total
                ];

                // Calculate trend data
                $trend = 'neutral';
                $percentageChange = 0;

                if ($previousCount !== null && $previousCount > 0) {
                    $percentageChange = round((($total - $previousCount) / $previousCount) * 100, 1);
                    $trend = $percentageChange > 0 ? 'up' : ($percentageChange < 0 ? 'down' : 'neutral');
                }

                $trendData[$period] = [
                    'count' => $total,
                    'trend' => $trend,
                    'percentage_change' => abs($percentageChange)
                ];

                $previousCount = $total;
            }

            // Calculate overall status distribution
            $statusTotals = [
                'pending' => $applications->where('status', 'pending')->sum('count'),
                'approved' => $applications->where('status', 'approved')->sum('count'),
                'rejected' => $applications->where('status', 'rejected')->sum('count'),
            ];

            $totalApplications = array_sum($statusTotals);
            $statusData = [];

            foreach ($statusTotals as $status => $count) {
                $percentage = $totalApplications > 0 ? round(($count / $totalApplications) * 100, 1) : 0;
                $statusData[$status] = [
                    'count' => $count,
                    'percentage' => $percentage
                ];
            }

            return [
                'period' => $startDate->format('M Y') . ' - ' . $endDate->format('M Y'),
                'quarterly_data' => $quarterlyData,
                'trend_data' => $trendData,
                'status_data' => $statusData,
                'total_applications' => $totalApplications
            ];

        } catch (\Exception $e) {
            Log::error('Error generating quarterly application data: ' . $e->getMessage());
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get annual application data
     *
     * @param int $years Number of years to include
     * @return array
     */
    public function getAnnualApplicationData(int $years = 3): array
    {
        try {
            $endDate = Carbon::now()->endOfYear();
            $startDate = Carbon::now()->subYears($years - 1)->startOfYear();

            // Generate year labels
            $periods = collect();
            $currentDate = $startDate->copy();

            while ($currentDate->lte($endDate)) {
                $periods->push($currentDate->year);
                $currentDate->addYear();
            }

            // Get application counts by year and status
            $applications = PersonalInfo::select(
                    DB::raw('YEAR(created_at) as year'),
                    'status',
                    DB::raw('COUNT(*) as count')
                )
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->groupBy('year', 'status')
                ->get();

            // Organize data by year
            $yearlyData = [];
            $trendData = [];
            $previousCount = null;

            foreach ($periods as $year) {
                $pending = $applications->where('year', $year)
                    ->where('status', 'pending')
                    ->sum('count');

                $approved = $applications->where('year', $year)
                    ->where('status', 'approved')
                    ->sum('count');

                $rejected = $applications->where('year', $year)
                    ->where('status', 'rejected')
                    ->sum('count');

                $total = $pending + $approved + $rejected;

                $yearlyData[$year] = [
                    'pending' => $pending,
                    'approved' => $approved,
                    'rejected' => $rejected,
                    'total' => $total
                ];

                // Calculate trend data
                $trend = 'neutral';
                $percentageChange = 0;

                if ($previousCount !== null && $previousCount > 0) {
                    $percentageChange = round((($total - $previousCount) / $previousCount) * 100, 1);
                    $trend = $percentageChange > 0 ? 'up' : ($percentageChange < 0 ? 'down' : 'neutral');
                }

                $trendData[$year] = [
                    'count' => $total,
                    'trend' => $trend,
                    'percentage_change' => abs($percentageChange)
                ];

                $previousCount = $total;
            }

            // Calculate overall status distribution
            $statusTotals = [
                'pending' => $applications->where('status', 'pending')->sum('count'),
                'approved' => $applications->where('status', 'approved')->sum('count'),
                'rejected' => $applications->where('status', 'rejected')->sum('count'),
            ];

            $totalApplications = array_sum($statusTotals);
            $statusData = [];

            foreach ($statusTotals as $status => $count) {
                $percentage = $totalApplications > 0 ? round(($count / $totalApplications) * 100, 1) : 0;
                $statusData[$status] = [
                    'count' => $count,
                    'percentage' => $percentage
                ];
            }

            return [
                'period' => $startDate->format('Y') . ' - ' . $endDate->format('Y'),
                'yearly_data' => $yearlyData,
                'trend_data' => $trendData,
                'status_data' => $statusData,
                'total_applications' => $totalApplications,
                'year_comparison_data' => $yearlyData
            ];

        } catch (\Exception $e) {
            Log::error('Error generating annual application data: ' . $e->getMessage());
            return [
                'error' => $e->getMessage()
            ];
        }
    }
}
