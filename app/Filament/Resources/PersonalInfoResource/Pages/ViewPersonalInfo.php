<?php

namespace App\Filament\Resources\PersonalInfoResource\Pages;

use App\Filament\Resources\PersonalInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use TCPDF;
use TCPDF_PARSER;

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
                    Log::info('Starting PDF export for applicant #' . $record->id);

                    // Create and verify temp directory
                    $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp');
                    if (!File::exists($tempDir)) {
                        File::makeDirectory($tempDir, 0755, true);
                    }

                    // Clean up old files
                    $this->cleanupTempFiles($tempDir);

                    // Generate the applicant info PDF
                    $infoPdf = Pdf::loadView('pdfs.personal-info', [
                        'record' => $record,
                    ]);
                    $infoPdf->setPaper('a4');
                    $infoPdf->setOption('isHtml5ParserEnabled', true);
                    $infoPdf->setOption('isRemoteEnabled', true);
                    $infoPdf->setOption('defaultFont', 'Arial');

                    // Save the info PDF
                    $infoPdfPath = $tempDir . DIRECTORY_SEPARATOR . 'info_' . time() . '.pdf';
                    file_put_contents($infoPdfPath, $infoPdf->output());
                    Log::info('Generated applicant info PDF');

                    // Collect document paths
                    $documentPaths = $this->collectDocumentPaths($record);
                    Log::info('Found ' . count($documentPaths) . ' document paths to process');

                    // Initialize the PDF merger
                    try {
                        $merger = PDFMerger::init([
                            'tempDir' => str_replace('\\', '/', $tempDir),
                            'orientation' => 'P'
                        ]);

                        // Add the info PDF first
                        $merger->addPDF($infoPdfPath, 'all');
                        Log::info('Added info PDF to merger');

                        // Process each document
                        $documentsAdded = 0;
                        foreach ($documentPaths as $documentInfo) {
                            $path = $documentInfo['path'];
                            $type = $documentInfo['type'];

                            try {
                                $fullPath = $this->getFullDocumentPath($path);

                                if (!$fullPath || !file_exists($fullPath)) {
                                    Log::warning("Document not found: {$path}");
                                    continue;
                                }

                                // Check file type
                                $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

                                if ($extension === 'pdf') {
                                    // It's a PDF, add it directly
                                    if ($this->isPdfValid($fullPath)) {
                                        $merger->addPDF($fullPath, 'all');
                                        $documentsAdded++;
                                        Log::info("Added document: {$type}");
                                    } else {
                                        Log::warning("Invalid PDF file: {$fullPath}");
                                    }
                                } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                    // It's an image, convert to PDF first
                                    $imagePdf = $this->convertImageToPdf($fullPath);
                                    if ($imagePdf) {
                                        $merger->addPDF($imagePdf, 'all');
                                        $documentsAdded++;
                                        Log::info("Added image document: {$type}");
                                    }
                                } else {
                                    Log::warning("Unsupported file type: {$extension} for {$path}");
                                }
                            } catch (\Exception $e) {
                                Log::error("Error processing document {$path}: " . $e->getMessage());
                                // Continue with next document
                            }
                        }

                        Log::info('Added ' . $documentsAdded . ' documents to the PDF merger');

                        // Merge PDFs
                        $merger->merge();
                        Log::info('Merged PDFs successfully');

                        // Get the merged output
                        $mergedPdfContent = $merger->output();

                        // Create final PDF file
                        $finalPdfPath = $tempDir . DIRECTORY_SEPARATOR . 'applicant_' . $record->id . '_' . time() . '.pdf';
                        file_put_contents($finalPdfPath, $mergedPdfContent);

                        Log::info('Created final PDF: ' . $finalPdfPath);

                        // Return the PDF for download
                        return response()->download($finalPdfPath, 'applicant-information.pdf', [
                            'Content-Type' => 'application/pdf',
                        ])->deleteFileAfterSend(true);

                    } catch (\Exception $e) {
                        Log::error('PDF merge failed: ' . $e->getMessage());
                        throw new \Exception('Failed to generate PDF. Please try again or contact support.');
                    }
                }),
            Actions\EditAction::make(),
        ];
    }

    /**
     * Collect all document paths from the applicant record
     *
     * @param mixed $record
     * @return array
     */
    private function collectDocumentPaths($record)
    {
        $documents = [];

        // Personal document
        if (!empty($record->document)) {
            $documents[] = [
                'path' => $record->document,
                'type' => 'Personal Document'
            ];
        }

        // Education - All types
        $eduFields = ['diploma_file', 'certificate', 'tor_file', 'attachment', 'transcript'];
        $eduTypes = [
            'elementaryEducation' => 'Elementary',
            'highSchoolEducation' => 'High School',
            'seniorHighEducation' => 'Senior High School',
            'juniorHighEducation' => 'Junior High School',
            'postSecondaryEducation' => 'Post Secondary',
            'tertiaryEducation' => 'Tertiary Education',
            'graduateEducation' => 'Graduate Studies',
            'education' => 'Education', // Generic education collection
        ];

        foreach ($eduTypes as $property => $label) {
            if (isset($record->$property)) {
                $items = $record->$property;

                // Handle items whether they're a collection, array, or single item
                if (is_object($items) && method_exists($items, 'toArray')) {
                    $items = $items->toArray();
                }

                if (!is_array($items)) {
                    $items = [$items];
                }

                foreach ($items as $item) {
                    foreach ($eduFields as $field) {
                        // Handle both object and array access
                        $value = null;

                        if (is_object($item) && isset($item->$field)) {
                            $value = $item->$field;
                        } elseif (is_array($item) && isset($item[$field])) {
                            $value = $item[$field];
                        }

                        if (!empty($value)) {
                            $fieldLabel = str_replace('_', ' ', $field);
                            $fieldLabel = str_replace('file', '', $fieldLabel);
                            $fieldLabel = ucwords(trim($fieldLabel));

                            $documents[] = [
                                'path' => $value,
                                'type' => "$label $fieldLabel"
                            ];
                        }
                    }
                }
            }
        }

        // Non-formal education
        if (isset($record->nonFormalEducation)) {
            $items = $record->nonFormalEducation;

            if (is_object($items) && method_exists($items, 'toArray')) {
                $items = $items->toArray();
            }

            if (!is_array($items)) {
                $items = [$items];
            }

            foreach ($items as $item) {
                $docFields = ['certificate', 'document', 'attachment'];

                foreach ($docFields as $field) {
                    $value = null;

                    if (is_object($item) && isset($item->$field)) {
                        $value = $item->$field;
                    } elseif (is_array($item) && isset($item[$field])) {
                        $value = $item[$field];
                    }

                    if (!empty($value)) {
                        $documents[] = [
                            'path' => $value,
                            'type' => 'Non-Formal Education ' . ucwords($field)
                        ];
                    }
                }
            }
        }

        // Certifications
        if (isset($record->certifications)) {
            $items = $record->certifications;

            if (is_object($items) && method_exists($items, 'toArray')) {
                $items = $items->toArray();
            }

            if (!is_array($items)) {
                $items = [$items];
            }

            foreach ($items as $item) {
                $docFields = ['document', 'certificate', 'attachment'];

                foreach ($docFields as $field) {
                    $value = null;

                    if (is_object($item) && isset($item->$field)) {
                        $value = $item->$field;
                    } elseif (is_array($item) && isset($item[$field])) {
                        $value = $item[$field];
                    }

                    if (!empty($value)) {
                        $documents[] = [
                            'path' => $value,
                            'type' => 'Certification ' . ucwords($field)
                        ];
                    }
                }
            }
        }

        // Work Experience
        if (isset($record->workExperiences)) {
            $experiences = $record->workExperiences;

            if (is_object($experiences) && method_exists($experiences, 'toArray')) {
                $experiences = $experiences->toArray();
            }

            if (!is_array($experiences)) {
                $experiences = [$experiences];
            }

            foreach ($experiences as $exp) {
                // Documents field (which can be array or string)
                if (is_object($exp) && isset($exp->documents)) {
                    $docs = is_array($exp->documents) ? $exp->documents : [$exp->documents];
                    foreach ($docs as $doc) {
                        if (!empty($doc)) {
                            $documents[] = [
                                'path' => $doc,
                                'type' => 'Work Experience Document'
                            ];
                        }
                    }
                } elseif (is_array($exp) && isset($exp['documents'])) {
                    $docs = is_array($exp['documents']) ? $exp['documents'] : [$exp['documents']];
                    foreach ($docs as $doc) {
                        if (!empty($doc)) {
                            $documents[] = [
                                'path' => $doc,
                                'type' => 'Work Experience Document'
                            ];
                        }
                    }
                }

                // Other potential work document fields
                $workDocFields = [
                    'certificate' => 'Certificate',
                    'recommendation_letter' => 'Recommendation Letter',
                    'performance_evaluation' => 'Performance Evaluation',
                    'cv' => 'CV/Resume',
                    'portfolio' => 'Portfolio',
                    'proof' => 'Proof of Employment'
                ];

                foreach ($workDocFields as $field => $label) {
                    $value = null;

                    if (is_object($exp) && isset($exp->$field)) {
                        $value = $exp->$field;
                    } elseif (is_array($exp) && isset($exp[$field])) {
                        $value = $exp[$field];
                    }

                    if (!empty($value)) {
                        $documents[] = [
                            'path' => $value,
                            'type' => "Work $label"
                        ];
                    }
                }
            }
        }

        // Awards (academic, community, work)
        $awardTypes = [
            'academicAwards' => 'Academic',
            'communityAwards' => 'Community',
            'workAwards' => 'Work',
            'awards' => 'General' // Generic awards collection if it exists
        ];

        foreach ($awardTypes as $property => $label) {
            if (isset($record->$property)) {
                $awards = $record->$property;

                // Handle whether it's a collection, array, or single item
                if (is_object($awards) && method_exists($awards, 'toArray')) {
                    $awards = $awards->toArray();
                }

                if (!is_array($awards)) {
                    $awards = [$awards];
                }

                foreach ($awards as $award) {
                    // Check for various document fields
                    $awardDocFields = ['document', 'certificate', 'proof', 'photo', 'attachment'];

                    foreach ($awardDocFields as $field) {
                        $value = null;

                        if (is_object($award) && isset($award->$field)) {
                            $value = $award->$field;
                        } elseif (is_array($award) && isset($award[$field])) {
                            $value = $award[$field];
                        }

                        if (!empty($value)) {
                            $fieldLabel = ucwords($field);
                            $documents[] = [
                                'path' => $value,
                                'type' => "$label Award $fieldLabel"
                            ];
                        }
                    }
                }
            }
        }

        // Creative Works
        if (isset($record->creativeWorks)) {
            $works = $record->creativeWorks;

            // Handle whether it's a collection, array, or single item
            if (is_object($works) && method_exists($works, 'toArray')) {
                $works = $works->toArray();
            }

            if (!is_array($works)) {
                $works = [$works];
            }

            foreach ($works as $work) {
                $workDocFields = ['document', 'attachment', 'proof', 'sample', 'photo'];

                foreach ($workDocFields as $field) {
                    $value = null;

                    if (is_object($work) && isset($work->$field)) {
                        $value = $work->$field;
                    } elseif (is_array($work) && isset($work[$field])) {
                        $value = $work[$field];
                    }

                    if (!empty($value)) {
                        $fieldLabel = ucwords($field);
                        $documents[] = [
                            'path' => $value,
                            'type' => "Creative Work $fieldLabel"
                        ];
                    }
                }
            }
        }

        // Lifelong Learning
        if (isset($record->lifelongLearning)) {
            $learnings = $record->lifelongLearning;

            if (is_object($learnings) && method_exists($learnings, 'toArray')) {
                $learnings = $learnings->toArray();
            }

            if (!is_array($learnings)) {
                $learnings = [$learnings];
            }

            foreach ($learnings as $learning) {
                $learningDocFields = ['document', 'evidence', 'attachment', 'certificate', 'proof'];

                foreach ($learningDocFields as $field) {
                    $value = null;

                    if (is_object($learning) && isset($learning->$field)) {
                        $value = $learning->$field;
                    } elseif (is_array($learning) && isset($learning[$field])) {
                        $value = $learning[$field];
                    }

                    if (!empty($value)) {
                        $fieldLabel = ucwords($field);
                        $documents[] = [
                            'path' => $value,
                            'type' => "Lifelong Learning $fieldLabel"
                        ];
                    }
                }
            }
        }

        // Essay attachment
        if (isset($record->essay)) {
            $essay = $record->essay;
            $essayDocFields = ['attachment', 'document', 'supporting_document', 'reference'];

            foreach ($essayDocFields as $field) {
                $value = null;

                if (is_object($essay) && isset($essay->$field)) {
                    $value = $essay->$field;
                } elseif (is_array($essay) && isset($essay[$field])) {
                    $value = $essay[$field];
                }

                if (!empty($value)) {
                    $fieldLabel = str_replace('_', ' ', $field);
                    $fieldLabel = ucwords($fieldLabel);
                    $documents[] = [
                        'path' => $value,
                        'type' => "Essay $fieldLabel"
                    ];
                }
            }
        }

        // Learning Objectives
        if (isset($record->learningObjective)) {
            $objective = $record->learningObjective;
            $objectiveDocFields = ['attachment', 'document', 'supporting_document', 'plan'];

            foreach ($objectiveDocFields as $field) {
                $value = null;

                if (is_object($objective) && isset($objective->$field)) {
                    $value = $objective->$field;
                } elseif (is_array($objective) && isset($objective[$field])) {
                    $value = $objective[$field];
                }

                if (!empty($value)) {
                    $fieldLabel = str_replace('_', ' ', $field);
                    $fieldLabel = ucwords($fieldLabel);
                    $documents[] = [
                        'path' => $value,
                        'type' => "Learning Objective $fieldLabel"
                    ];
                }
            }
        }

        return $documents;
    }

    /**
     * Get the full path for a document
     *
     * @param string $path
     * @return string|null
     */
    private function getFullDocumentPath($path)
    {
        if (empty($path)) {
            return null;
        }

        // Check public disk
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->path($path);
        }

        // Check documents folder
        if (Storage::disk('public')->exists('documents/' . basename($path))) {
            return Storage::disk('public')->path('documents/' . basename($path));
        }

        // Check default disk
        if (Storage::exists($path)) {
            return Storage::path($path);
        }

        // Check if already a full path
        if (file_exists($path)) {
            return $path;
        }

        return null;
    }

    /**
     * Check if a PDF file is valid
     *
     * @param string $path
     * @return bool
     */
    private function isPdfValid($path)
    {
        try {
            // Check file size
            if (!file_exists($path) || filesize($path) < 5) {
                return false;
            }

            // Check PDF header
            $handle = fopen($path, 'rb');
            if (!$handle) {
                return false;
            }

            $header = fread($handle, 4);
            fclose($handle);

            return $header === '%PDF';
        } catch (\Exception $e) {
            Log::error("Failed to validate PDF: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clean up temporary files in the specified directory
     *
     * @param string $tempDir
     * @return void
     */
    private function cleanupTempFiles($tempDir)
    {
        try {
            if (File::exists($tempDir)) {
                $files = File::files($tempDir);
                foreach ($files as $file) {
                    // Delete files older than 1 hour
                    if (time() - File::lastModified($file) > 3600) {
                        File::delete($file);
                        Log::info("Cleaned up old temp file: {$file}");
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to clean up temp files: " . $e->getMessage());
        }
    }

    /**
     * Convert an image file to PDF
     *
     * @param string $imagePath
     * @return string|null
     */
    private function convertImageToPdf($imagePath)
    {
        try {
            // Create a temporary directory if it doesn't exist
            $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp');
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }

            // Verify directory is writable
            if (!is_writable($tempDir)) {
                Log::error("Temp directory is not writable: {$tempDir}");
                return null;
            }

            // Create a temporary PDF file path
            $tempPdfPath = $tempDir . DIRECTORY_SEPARATOR . uniqid('img_') . '.pdf';
            Log::info("Converting image to PDF: {$imagePath} -> {$tempPdfPath}");

            // Check if file exists and is readable
            if (!file_exists($imagePath) || !is_readable($imagePath)) {
                Log::error("Image file does not exist or is not readable: {$imagePath}");
                return null;
            }

            // Check file size
            if (filesize($imagePath) === 0) {
                Log::error("Image file is empty: {$imagePath}");
                return null;
            }

            // Create a PDF with the image
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetCreator('ETEEAP Application');
            $pdf->SetTitle('Document Image');
            $pdf->SetMargins(10, 10, 10);
            $pdf->SetAutoPageBreak(true, 10);
            $pdf->AddPage();

            try {
                // Get image info
                $imgInfo = @getimagesize($imagePath);

                if ($imgInfo === false) {
                    // If can't get image info, use base64 approach
                    throw new \Exception("Could not get image info");
                }

                // Calculate dimensions to fit on page
                $pageWidth = $pdf->getPageWidth() - 20; // Account for margins
                $pageHeight = $pdf->getPageHeight() - 20;

                $width = $imgInfo[0];
                $height = $imgInfo[1];
                $ratio = $width / $height;

                if ($ratio > 1) {
                    // Landscape orientation
                    $newWidth = min($pageWidth, $width);
                    $newHeight = $newWidth / $ratio;
                } else {
                    // Portrait orientation
                    $newHeight = min($pageHeight, $height);
                    $newWidth = $newHeight * $ratio;
                }

                // Center the image
                $x = (($pageWidth + 20) - $newWidth) / 2;
                $y = (($pageHeight + 20) - $newHeight) / 2;

                // Add title
                $pdf->SetFont('helvetica', 'B', 14);
                $pdf->Cell(0, 10, "Document Image", 0, 1, 'C');
                $pdf->Ln(5);

                // Add the image
                $pdf->Image($imagePath, $x, $y + 15, $newWidth, $newHeight);
                Log::info("Added image to PDF successfully");
            } catch (\Exception $e) {
                // If direct image addition fails, try base64 approach
                Log::warning("Direct image addition failed: " . $e->getMessage() . ". Trying base64 approach.");

                try {
                    $pdf->SetFont('helvetica', 'B', 14);
                    $pdf->Cell(0, 10, "Document Image", 0, 1, 'C');
                    $pdf->Ln(5);

                    // Use base64 encoding
                    $imageData = @file_get_contents($imagePath);
                    if ($imageData) {
                        $base64 = base64_encode($imageData);
                        $imgType = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
                        if (!in_array($imgType, ['jpg', 'jpeg', 'png', 'gif'])) {
                            $imgType = 'jpeg'; // Default
                        }

                        // Create HTML with the image
                        $html = '<div style="text-align:center;">';
                        $html .= '<img src="data:image/' . $imgType . ';base64,' . $base64 . '" style="max-width:90%; max-height:200mm;">';
                        $html .= '</div>';

                        // Add the HTML
                        $pdf->writeHTML($html, true, false, true, false, '');
                        Log::info("Added image using base64 approach");
                    } else {
                        throw new \Exception("Could not read image data");
                    }
                } catch (\Exception $e2) {
                    // If both approaches fail, just add a placeholder text
                    Log::warning("Base64 approach also failed: " . $e2->getMessage());

                    $pdf->SetFont('helvetica', '', 12);
                    $pdf->MultiCell(0, 10, "The image could not be embedded. Please refer to the original document.", 0, 'C');

                    // Add image path for reference
                    $pdf->Ln(5);
                    $pdf->SetFont('helvetica', '', 10);
                    $pdf->MultiCell(0, 10, "Reference: " . basename($imagePath), 0, 'C');
                }
            }

            // Save the PDF
            $pdf->Output($tempPdfPath, 'F');

            // Verify the file was created
            if (file_exists($tempPdfPath) && filesize($tempPdfPath) > 0) {
                Log::info("Successfully created PDF from image: {$tempPdfPath}");
                return $tempPdfPath;
            } else {
                Log::error("Failed to create PDF from image or file is empty");
                return null;
            }
        } catch (\Exception $e) {
            Log::error("Image to PDF conversion failed: " . $e->getMessage());
            Log::error("Trace: " . $e->getTraceAsString());
            return null;
        }
    }
}
