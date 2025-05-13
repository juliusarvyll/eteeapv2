<?php

namespace App\Filament\Pages;

use App\Services\ApplicationReportingService;
use App\Services\ApplicantPdfService;
use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApplicationReports extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.application-reports';

    protected static ?string $navigationLabel = 'Application Reports';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Application Reporting Dashboard';

    public $reportType = 'monthly';
    public $period = 12;
    public $years = 3;

    public $reportData = [];

    public function mount(): void
    {
        $this->form->fill();
        $this->generateReport();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('reportType')
                    ->label('Report Type')
                    ->options([
                        'monthly' => 'Monthly',
                        'quarterly' => 'Quarterly',
                        'annual' => 'Annual',
                    ])
                    ->default('monthly')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function () {
                        $this->generateReport();
                    }),

                Select::make('period')
                    ->label(function () {
                        if ($this->reportType === 'monthly') {
                            return 'Months to Include';
                        } elseif ($this->reportType === 'quarterly') {
                            return 'Quarters to Include';
                        } else {
                            return 'Years to Include';
                        }
                    })
                    ->options(function () {
                        if ($this->reportType === 'monthly') {
                            return [
                                3 => 'Last 3 Months',
                                6 => 'Last 6 Months',
                                12 => 'Last 12 Months',
                                24 => 'Last 24 Months',
                            ];
                        } elseif ($this->reportType === 'quarterly') {
                            return [
                                2 => 'Last 2 Quarters',
                                4 => 'Last 4 Quarters',
                                8 => 'Last 8 Quarters',
                            ];
                        } else {
                            return [
                                2 => 'Last 2 Years',
                                3 => 'Last 3 Years',
                                5 => 'Last 5 Years',
                            ];
                        }
                    })
                    ->default(function () {
                        if ($this->reportType === 'monthly') {
                            return 12;
                        } elseif ($this->reportType === 'quarterly') {
                            return 4;
                        } else {
                            return 3;
                        }
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(function () {
                        $this->generateReport();
                    }),
            ]);
    }

    public function generateReport(): void
    {
        $reportingService = app(ApplicationReportingService::class);

        switch ($this->reportType) {
            case 'monthly':
                $this->reportData = $reportingService->getMonthlyApplicationData((int) $this->period);
                break;
            case 'quarterly':
                $this->reportData = $reportingService->getQuarterlyApplicationData((int) $this->period);
                break;
            case 'annual':
                $this->reportData = $reportingService->getAnnualApplicationData((int) $this->period);
                break;
            default:
                $this->reportData = $reportingService->getMonthlyApplicationData();
        }
    }

    public function exportReportPdf(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $reportingService = app(ApplicationReportingService::class);
        $pdfService = app(ApplicantPdfService::class);

        // Mock chart paths (in a real implementation, would generate actual charts)
        $mockChartData = [
            'period' => $this->reportData['period'] ?? 'All Time',
            'statusData' => $this->reportData['status_data'] ?? [],
            'trendData' => $this->reportData['trend_data'] ?? [],
        ];

        if ($this->reportType === 'annual') {
            $mockChartData['yearComparisonData'] = $this->reportData['year_comparison_data'] ?? [];
        }

        // Generate PDF
        $pdf = $pdfService->generateReportPdf($this->reportType, $mockChartData);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            "application_report_{$this->reportType}_" . date('Y-m-d') . '.pdf'
        );
    }

    protected function getActions(): array
    {
        return [
            Action::make('export_pdf')
                ->label('Export to PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action('exportReportPdf'),
        ];
    }
}
