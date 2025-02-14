<?php

namespace App\Filament\Resources\PersonalInfoResource\Pages;

use App\Filament\Resources\PersonalInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class ViewPersonalInfo extends ViewRecord
{
    protected static string $resource = PersonalInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    $record = $this->record;

                    // More detailed debug information
                    $debug = [
                        'personal_document' => [
                            'value' => $record->document,
                            'exists' => Storage::disk('public')->exists('documents/' . basename($record->document))
                        ],
                        'elementary' => [
                            'count' => $record->elementaryEducation->count(),
                            'raw' => $record->elementaryEducation->toArray(),
                            'files' => $record->elementaryEducation->pluck('diploma_file')->filter()
                        ],
                        'highschool' => [
                            'count' => $record->highSchoolEducation->count(),
                            'raw' => $record->highSchoolEducation->toArray(),
                            'files' => $record->highSchoolEducation->pluck('diploma_file')->filter()
                        ],
                        'work_experience' => [
                            'count' => $record->workExperiences->count(),
                            'raw' => $record->workExperiences->toArray(),
                            'files' => $record->workExperiences->pluck('documents')->filter()
                        ],
                    ];



                    Log::info('Starting PDF generation process');

                    // Generate info PDF using DomPDF
                    $infoPdf = Pdf::loadView('pdfs.personal-info', [
                        'record' => $record,
                    ]);
                    Log::info('Generated info PDF from template');

                    // Initialize PDFMerger
                    $merger = PDFMerger::init();
                    Log::info('Initialized PDF Merger');

                    // Add the info PDF as first page
                    try {
                        $merger->addString($infoPdf->output(), 'all');
                        Log::info('Added info PDF to merger');
                    } catch (\Exception $e) {
                        Log::error('Failed to add info PDF: ' . $e->getMessage());
                    }

                    // Helper function to add document
                    $addDocumentToPdf = function($path, $documentType) use ($merger) {
                        if (!empty($path)) {
                            $fullPath = Storage::disk('public')->path($path);
                            Log::info("Attempting to merge {$documentType}: {$fullPath}");
                            
                            if (file_exists($fullPath)) {
                                try {
                                    $merger->addString(file_get_contents($fullPath), 'all');
                                    Log::info("Successfully merged {$documentType}");
                                } catch (\Exception $e) {
                                    Log::error("Failed to merge {$documentType}: " . $e->getMessage());
                                }
                            } else {
                                Log::warning("File not found for {$documentType}: {$fullPath}");
                            }
                        }
                    };

                    // Add personal document
                    if ($record->document) {
                        Log::info('Processing personal document');
                        $addDocumentToPdf($record->document, 'Personal Document');
                    }

                    // Add education documents
                    foreach ($record->education as $edu) {
                        Log::info("Processing education documents for type: {$edu->type}");
                        
                        // Handle diploma files
                        if (!empty($edu->diploma_file)) {
                            $documentType = match($edu->type) {
                                'elementary' => 'Elementary Diploma',
                                'high_school' => 'High School Diploma',
                                'post_secondary' => 'Post Secondary Diploma',
                                default => 'Diploma'
                            };
                            $addDocumentToPdf($edu->diploma_file, $documentType);
                        }

                        // Handle certificates (for non-formal education)
                        if ($edu->type === 'non_formal' && !empty($edu->certificate)) {
                            $addDocumentToPdf($edu->certificate, 'Non-Formal Certificate');
                        }
                    }

                    // Add work experience documents
                    Log::info('Processing work experience documents');
                    foreach ($record->workExperiences as $exp) {
                        if (!empty($exp->documents)) {
                            $documents = is_array($exp->documents) ? $exp->documents : [$exp->documents];
                            foreach ($documents as $doc) {
                                $addDocumentToPdf($doc, 'Work Experience Document');
                            }
                        }
                    }

                    // Add award documents
                    Log::info('Processing award documents');
                    foreach ($record->academicAwards as $award) {
                        if (!empty($award->document)) {
                            $addDocumentToPdf($award->document, 'Academic Award');
                        }
                    }

                    foreach ($record->communityAwards as $award) {
                        if (!empty($award->document)) {
                            $addDocumentToPdf($award->document, 'Community Award');
                        }
                    }

                    foreach ($record->workAwards as $award) {
                        if (!empty($award->document)) {
                            $addDocumentToPdf($award->document, 'Work Award');
                        }
                    }

                    try {
                        Log::info('Starting final PDF merge');
                        // Merge PDFs
                        $merger->merge();
                        Log::info('PDF merge completed successfully');

                        // Stream the final merged PDF
                        return response()->streamDownload(
                            fn () => print($merger->output()),
                            'applicant-information.pdf',
                            [
                                'Content-Type' => 'application/pdf',
                            ]
                        );
                    } catch (\Exception $e) {
                        Log::error("PDF Merge failed: " . $e->getMessage());
                        throw new \Exception("Failed to generate PDF. Please try again or contact support.");
                    }
                }),
            Actions\EditAction::make(),
        ];
    }
}
