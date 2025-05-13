<?php

namespace App\Http\Controllers;

use App\Services\ApplicationReportingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChartController extends Controller
{
    protected $reportingService;

    public function __construct(ApplicationReportingService $reportingService)
    {
        $this->reportingService = $reportingService;
    }

    /**
     * Generate status distribution chart image
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateStatusChart(Request $request)
    {
        try {
            $type = $request->input('type', 'monthly');
            $period = (int) $request->input('period', 12);

            // Get appropriate data based on report type
            $data = [];
            switch ($type) {
                case 'monthly':
                    $data = $this->reportingService->getMonthlyApplicationData($period);
                    break;
                case 'quarterly':
                    $data = $this->reportingService->getQuarterlyApplicationData($period);
                    break;
                case 'annual':
                    $data = $this->reportingService->getAnnualApplicationData($period);
                    break;
                default:
                    $data = $this->reportingService->getMonthlyApplicationData();
            }

            if (isset($data['error'])) {
                return response()->json(['error' => $data['error']], 500);
            }

            // Prepare data for chart
            $statusData = $data['status_data'] ?? [];
            $labels = array_map('ucfirst', array_keys($statusData));
            $values = array_column($statusData, 'count');

            // Generate unique filename
            $fileName = 'status_chart_' . $type . '_' . Str::random(8) . '.png';
            $filePath = 'charts/' . $fileName;

            // In a real implementation, you would use a chart library to generate the image
            // For DomPDF compatibility, we would save this image file and then reference it in the PDF

            // Simulating chart generation (in reality, use a library like Chart.js server-side)
            $chartUrl = url('storage/' . $filePath);

            return response()->json([
                'chart_url' => $chartUrl,
                'chart_path' => $filePath,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Chart generation error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Generate trend chart image
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateTrendChart(Request $request)
    {
        try {
            $type = $request->input('type', 'monthly');
            $period = (int) $request->input('period', 12);

            // Get appropriate data based on report type
            $data = [];
            switch ($type) {
                case 'monthly':
                    $data = $this->reportingService->getMonthlyApplicationData($period);
                    $chartData = $data['monthly_data'] ?? [];
                    break;
                case 'quarterly':
                    $data = $this->reportingService->getQuarterlyApplicationData($period);
                    $chartData = $data['quarterly_data'] ?? [];
                    break;
                case 'annual':
                    $data = $this->reportingService->getAnnualApplicationData($period);
                    $chartData = $data['yearly_data'] ?? [];
                    break;
                default:
                    $data = $this->reportingService->getMonthlyApplicationData();
                    $chartData = $data['monthly_data'] ?? [];
            }

            if (isset($data['error'])) {
                return response()->json(['error' => $data['error']], 500);
            }

            // Prepare data for chart
            $labels = array_keys($chartData);
            $approved = array_column($chartData, 'approved');
            $rejected = array_column($chartData, 'rejected');
            $pending = array_column($chartData, 'pending');

            // Generate unique filename
            $fileName = 'trend_chart_' . $type . '_' . Str::random(8) . '.png';
            $filePath = 'charts/' . $fileName;

            // In a real implementation, you would use a chart library to generate the image
            // For DomPDF compatibility, we would save this image file and then reference it in the PDF

            // Simulating chart generation (in reality, use a library like Chart.js server-side)
            $chartUrl = url('storage/' . $filePath);

            return response()->json([
                'chart_url' => $chartUrl,
                'chart_path' => $filePath,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Chart generation error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Generate year comparison chart image
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateYearComparisonChart(Request $request)
    {
        try {
            $years = (int) $request->input('years', 3);

            // Get annual data
            $data = $this->reportingService->getAnnualApplicationData($years);

            if (isset($data['error'])) {
                return response()->json(['error' => $data['error']], 500);
            }

            // Prepare data for chart
            $yearlyData = $data['yearly_data'] ?? [];
            $labels = array_keys($yearlyData);
            $approved = array_column($yearlyData, 'approved');
            $rejected = array_column($yearlyData, 'rejected');
            $pending = array_column($yearlyData, 'pending');

            // Generate unique filename
            $fileName = 'year_comparison_chart_' . Str::random(8) . '.png';
            $filePath = 'charts/' . $fileName;

            // In a real implementation, you would use a chart library to generate the image
            // For DomPDF compatibility, we would save this image file and then reference it in the PDF

            // Simulating chart generation (in reality, use a library like Chart.js server-side)
            $chartUrl = url('storage/' . $filePath);

            return response()->json([
                'chart_url' => $chartUrl,
                'chart_path' => $filePath,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Chart generation error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
