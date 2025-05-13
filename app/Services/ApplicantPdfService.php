<?php

namespace App\Services;

use App\Models\PersonalInfo;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class ApplicantPdfService
{
    /**
     * Generate PDF for a single applicant
     *
     * @param PersonalInfo $applicant
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generateSingleApplicantPdf(PersonalInfo $applicant)
    {
        try {
            $pdf = PDF::loadView('pdf.applicant-profile', [
                'applicant' => $applicant,
                'generatedAt' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            // Set PDF options
            $pdf->setPaper('a4');
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isRemoteEnabled', true);

            return $pdf;
        } catch (\Exception $e) {
            Log::error('PDF generation failed: ' . $e->getMessage(), [
                'applicant_id' => $applicant->applicant_id,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Generate PDF for multiple applicants
     *
     * @param Collection $applicants
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generateBulkApplicantsPdf(Collection $applicants)
    {
        try {
            $pdf = PDF::loadView('pdf.applicants-bulk', [
                'applicants' => $applicants,
                'generatedAt' => Carbon::now()->format('Y-m-d H:i:s'),
                'total' => $applicants->count(),
            ]);

            // Set PDF options
            $pdf->setPaper('a4', 'landscape');
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isRemoteEnabled', true);

            return $pdf;
        } catch (\Exception $e) {
            Log::error('Bulk PDF generation failed: ' . $e->getMessage(), [
                'applicant_count' => $applicants->count(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Generate comparative reports PDF
     *
     * @param string $reportType monthly|quarterly|annual
     * @param array $chartData
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generateReportPdf(string $reportType, array $chartData)
    {
        try {
            $pdf = PDF::loadView('pdf.comparative-report', [
                'reportType' => $reportType,
                'chartData' => $chartData,
                'generatedAt' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            // Set PDF options
            $pdf->setPaper('a4', 'landscape');
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isRemoteEnabled', true);

            return $pdf;
        } catch (\Exception $e) {
            Log::error('Report PDF generation failed: ' . $e->getMessage(), [
                'report_type' => $reportType,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
