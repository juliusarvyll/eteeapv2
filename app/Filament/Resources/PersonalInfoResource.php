<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalInfoResource\Pages;
use App\Filament\Resources\PersonalInfoResource\RelationManagers;
use App\Models\PersonalInfo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\RepeatableEntry;
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;
use Illuminate\Support\Facades\Storage;
use Filament\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;

class PersonalInfoResource extends Resource
{
    protected static ?string $model = PersonalInfo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Applicant';

    protected static ?string $modelLabel = 'Applicant Information';

    protected static function boot()
    {
        parent::boot();

        static::deleteDraftApplications();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('firstName')
                    ->required()
                    ->label('First Name'),
                Forms\Components\TextInput::make('middleName')
                    ->label('Middle Name'),
                Forms\Components\TextInput::make('lastName')
                    ->required()
                    ->label('Last Name'),
                Forms\Components\TextInput::make('suffix')
                    ->label('Suffix'),
                Forms\Components\DatePicker::make('birthDate')
                    ->required()
                    ->label('Birth Date')
                    ->format('Y-m-d'),
                Forms\Components\TextInput::make('placeOfBirth')
                    ->label('Place of Birth'),
                Forms\Components\Select::make('civilStatus')
                    ->options([
                        'single' => 'Single',
                        'married' => 'Married',
                        'divorced' => 'Divorced',
                        'widowed' => 'Widowed',
                    ])
                    ->label('Civil Status'),
                Forms\Components\FileUpload::make('document')
                    ->label('Document'),
                Forms\Components\Select::make('sex')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ])
                    ->label('Sex'),
                Forms\Components\TextInput::make('religion')
                    ->label('Religion'),
                Forms\Components\TextInput::make('languages')
                    ->label('Languages'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Personal Information')
                    ->schema([
                        TextEntry::make('firstName')
                            ->label('First Name'),
                        TextEntry::make('middleName')
                            ->label('Middle Name'),
                        TextEntry::make('lastName')
                            ->label('Last Name'),
                        TextEntry::make('suffix')
                            ->label('Suffix'),
                        TextEntry::make('birthDate')
                            ->label('Birth Date')
                            ->date('Y-m-d'),
                        TextEntry::make('placeOfBirth')
                            ->label('Place of Birth'),
                        TextEntry::make('civilStatus')
                            ->label('Civil Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'single' => 'info',
                                'married' => 'success',
                                'divorced' => 'danger',
                                'widowed' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('document')
                            ->label('Document')
                            ->visible(fn ($record) => filled($record->document))
                            ->badge()
                            ->color('success')
                            ->formatStateUsing(fn () => 'Document Uploaded'),

                        TextEntry::make('sex')
                            ->label('Sex')
                            ->badge(),
                        TextEntry::make('religion')
                            ->label('Religion'),
                        TextEntry::make('languages')
                            ->label('Languages'),
                        TextEntry::make('status')
                            ->label('Application Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            }),
                    ])->columns(2),

                Section::make('Learning Objectives')
                    ->schema([
                        TextEntry::make('learningObjective.firstPriority')
                            ->label('First Priority'),
                        TextEntry::make('learningObjective.secondPriority')
                            ->label('Second Priority'),
                        TextEntry::make('learningObjective.thirdPriority')
                            ->label('Third Priority'),
                        TextEntry::make('learningObjective.goalStatement')
                            ->label('Goal Statement')
                            ->markdown(),
                        TextEntry::make('learningObjective.timeCommitment')
                            ->label('Time Commitment'),
                        TextEntry::make('learningObjective.overseasPlan')
                            ->label('Overseas Plan'),
                        TextEntry::make('learningObjective.costPayment')
                            ->label('Cost Payment'),
                        TextEntry::make('learningObjective.completionTimeline')
                            ->label('Completion Timeline'),
                    ])->columns(2),

                Section::make('Education')
                    ->schema([
                        // Elementary Education
                        TextEntry::make('elementaryEducation.school_name')
                            ->label('Elementary School'),
                        TextEntry::make('elementaryEducation.address')
                            ->label('School Address'),
                        TextEntry::make('elementaryEducation.date_from')
                            ->label('From')
                            ->date(),
                        TextEntry::make('elementaryEducation.date_to')
                            ->label('To')
                            ->date(),
                        TextEntry::make('elementaryEducation.has_diploma')
                            ->label('Has Diploma')
                            ->badge(),

                        // High School Education
                        RepeatableEntry::make('highSchoolEducation')
                            ->schema([
                                TextEntry::make('school_name')
                                    ->label('School Name'),
                                TextEntry::make('address')
                                    ->label('Address'),
                                TextEntry::make('school_type')
                                    ->label('School Type'),
                                TextEntry::make('date_from')
                                    ->label('From')
                                    ->date(),
                                TextEntry::make('date_to')
                                    ->label('To')
                                    ->date(),
                            ])->columns(2),

                        // Post Secondary Education
                        RepeatableEntry::make('postSecondaryEducation')
                            ->schema([
                                TextEntry::make('program')
                                    ->label('Program'),
                                TextEntry::make('institution')
                                    ->label('Institution'),
                                TextEntry::make('school_year')
                                    ->label('School Year'),
                            ])->columns(3),

                        // Non-Formal Education
                        RepeatableEntry::make('nonFormalEducation')
                            ->schema([
                                TextEntry::make('title')
                                    ->label('Title'),
                                TextEntry::make('organization')
                                    ->label('Organization'),
                                TextEntry::make('date_from')
                                    ->label('Date')
                                    ->date(),
                                TextEntry::make('certificate')
                                    ->label('Certificate'),
                                TextEntry::make('participation')
                                    ->label('Participation'),
                            ])->columns(2),

                        // Certifications
                        RepeatableEntry::make('certifications')
                            ->schema([
                                TextEntry::make('title')
                                    ->label('Title'),
                                TextEntry::make('agency')
                                    ->label('Agency'),
                                TextEntry::make('date_certified')
                                    ->label('Date Certified')
                                    ->date(),
                                TextEntry::make('rating')
                                    ->label('Rating'),
                            ])->columns(2),
                    ]),

                Section::make('Work Experience')
                    ->schema([
                        RepeatableEntry::make('workExperiences')
                            ->schema([
                                TextEntry::make('designation')
                                    ->label('Designation'),
                                TextEntry::make('companyName')
                                    ->label('Company Name'),
                                TextEntry::make('companyAddress')
                                    ->label('Company Address'),
                                TextEntry::make('dateFrom')
                                    ->label('From')
                                    ->date(),
                                TextEntry::make('dateTo')
                                    ->label('To')
                                    ->date(),
                                TextEntry::make('employmentStatus')
                                    ->label('Employment Status'),
                                TextEntry::make('supervisorName')
                                    ->label('Supervisor'),
                                TextEntry::make('reasonForLeaving')
                                    ->label('Reason for Leaving'),
                                TextEntry::make('responsibilities')
                                    ->label('Responsibilities')
                                    ->markdown(),
                                TextEntry::make('references')
                                    ->label('References')
                                    ->listWithLineBreaks(),
                            ])->columns(2),
                    ]),

                Section::make('Awards and Recognition')
                    ->schema([
                        // Academic Awards
                        RepeatableEntry::make('academicAwards')
                            ->schema([
                                TextEntry::make('title')
                                    ->label('Title'),
                                TextEntry::make('organization')
                                    ->label('Organization'),
                                TextEntry::make('dateAwarded')
                                    ->label('Date Awarded')
                                    ->date(),
                            ])->columns(3),

                        // Community Awards
                        RepeatableEntry::make('communityAwards')
                            ->schema([
                                TextEntry::make('title')
                                    ->label('Title'),
                                TextEntry::make('organization')
                                    ->label('Organization'),
                                TextEntry::make('dateAwarded')
                                    ->label('Date Awarded')
                                    ->date(),
                            ])->columns(3),

                        // Work Awards
                        RepeatableEntry::make('workAwards')
                            ->schema([
                                TextEntry::make('title')
                                    ->label('Title'),
                                TextEntry::make('organization')
                                    ->label('Organization'),
                                TextEntry::make('dateAwarded')
                                    ->label('Date Awarded')
                                    ->date(),
                            ])->columns(3),
                    ]),

                Section::make('Creative Works')
                    ->schema([
                        RepeatableEntry::make('creativeWorks')
                            ->schema([
                                TextEntry::make('title')
                                    ->label('Title'),
                                TextEntry::make('description')
                                    ->label('Description')
                                    ->markdown(),
                                TextEntry::make('significance')
                                    ->label('Significance')
                                    ->markdown(),
                                TextEntry::make('date_completed')
                                    ->label('Date Completed')
                                    ->date(),
                                TextEntry::make('corroborating_body')
                                    ->label('Corroborating Body'),
                            ])->columns(2),
                    ]),

                Section::make('Lifelong Learning')
                    ->schema([
                        RepeatableEntry::make('lifelongLearning')
                            ->schema([
                                TextEntry::make('type')
                                    ->label('Type')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'hobby' => 'info',
                                        'skill' => 'success',
                                        'work' => 'warning',
                                        'volunteer' => 'primary',
                                        'travel' => 'danger',
                                        default => 'gray',
                                    }),
                                TextEntry::make('description')
                                    ->label('Description')
                                    ->markdown(),
                            ])->columns(2),
                    ]),

                Section::make('Essay')
                    ->schema([
                        TextEntry::make('essay.content')
                            ->label('Content')
                            ->markdown(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('applicant_id')
                    ->label('Application ID')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('firstName')
                    ->label('First Name')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('lastName')
                    ->label('Last Name')
                    ->searchable(),
                
                Tables\Columns\SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->selectablePlaceholder(false)
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function ($record) {
                        try {
                            // Load all necessary relationships
                            $record->load([
                                'lifelongLearning',
                                'workExperiences',
                                'academicAwards',
                                'communityAwards',
                                'workAwards',
                                'education',
                                'learningObjective',  // Added missing relationship
                                'creativeWorks',      // Added missing relationship
                                'essay'               // Added missing relationship
                            ]);

                            // Generate info PDF using DomPDF with explicit configuration
                            $infoPdf = Pdf::loadView('pdfs.personal-info', [
                                'record' => $record
                            ])->setPaper('a4', 'portrait')
                              ->setOptions([
                                  'isHtml5ParserEnabled' => true,
                                  'isRemoteEnabled' => true,
                                  'defaultFont' => 'Poppins',
                                  'chroot' => public_path(),
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
                                throw $e;
                            }

                            // Modified helper function with existence check
                            $addDocumentToPdf = function($path, $documentType) use ($merger) {
                                if (empty($path)) {
                                    Log::info("Skipped empty {$documentType} path");
                                    return;
                                }
                                
                                $fullPath = Storage::disk('public')->path($path);
                                if (!file_exists($fullPath)) {
                                    Log::warning("Skipping nonexistent {$documentType}: {$fullPath}");
                                    return;
                                }

                                try {
                                    $merger->addString(file_get_contents($fullPath), 'all');
                                    Log::info("Successfully merged {$documentType}");
                                } catch (\Exception $e) {
                                    Log::error("Failed to merge {$documentType}: " . $e->getMessage());
                                }
                            };

                            // Add personal document
                            if ($record->document) {
                                Log::info('Processing personal document');
                                $addDocumentToPdf($record->document, 'Personal Document');
                            }

                            // Add education documents
                            foreach ($record->education as $edu) {
                                Log::info("Processing education documents for type: {$edu['type']}");
                                
                                // Handle diploma files
                                $diplomaFile = data_get($edu, 'diploma_file');
                                if (!empty($diplomaFile)) {
                                    $documentType = match(data_get($edu, 'type')) {
                                        'elementary' => 'Elementary Diploma',
                                        'high_school' => 'High School Diploma',
                                        'post_secondary' => 'Post Secondary Diploma',
                                        default => 'Diploma'
                                    };
                                    $addDocumentToPdf($diplomaFile, $documentType);
                                }

                                // Handle certificates (for non-formal education)
                                if (data_get($edu, 'type') === 'non_formal') {
                                    $certificate = data_get($edu, 'certificate');
                                    if (!empty($certificate)) {
                                        $addDocumentToPdf($certificate, 'Non-Formal Certificate');
                                    }
                                }
                            }

                            // Add work experience documents
                            Log::info('Processing work experience documents');
                            foreach ($record->workExperiences as $exp) {
                                if (!empty($exp['documents'])) {
                                    $documents = is_array($exp['documents']) ? $exp['documents'] : [$exp['documents']];
                                    foreach ($documents as $doc) {
                                        $addDocumentToPdf($doc, 'Work Experience Document');
                                    }
                                }
                            }

                            // Add award documents
                            Log::info('Processing award documents');
                            foreach ($record->academicAwards as $award) {
                                if (!empty($award['document'])) {
                                    $addDocumentToPdf($award['document'], 'Academic Award');
                                }
                            }

                            foreach ($record->communityAwards as $award) {
                                if (!empty($award['document'])) {
                                    $addDocumentToPdf($award['document'], 'Community Award');
                                }
                            }

                            foreach ($record->workAwards as $award) {
                                if (!empty($award['document'])) {
                                    $addDocumentToPdf($award['document'], 'Work Award');
                                }
                            }

                            try {
                                Log::info('Starting final PDF merge');
                                // Merge PDFs
                                $merger->merge();
                                Log::info('PDF merge completed successfully');

                                $content = $merger->output();
                                
                                if (empty($content)) {
                                    throw new \Exception("Generated PDF content is empty");
                                }

                                return response()->streamDownload(
                                    function () use ($content) {
                                        echo $content;
                                    },
                                    'applicant-information.pdf',
                                    [
                                        'Content-Type' => 'application/pdf',
                                        'Content-Disposition' => 'attachment; filename="applicant-information.pdf"'
                                    ]
                                );
                            } catch (\Exception $e) {
                                Log::error("PDF Generation failed: " . $e->getMessage(), [
                                    'trace' => $e->getTraceAsString(),
                                    'record_id' => $record->id
                                ]);
                                throw new \Exception("Failed to generate PDF: " . $e->getMessage());
                            }
                        } catch (\Exception $e) {
                            Log::error("PDF Generation failed: " . $e->getMessage(), [
                                'trace' => $e->getTraceAsString(),
                                'record_id' => $record->id
                            ]);
                            throw new \Exception("Failed to generate PDF: " . $e->getMessage());
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->searchable();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonalInfos::route('/'),
            'create' => Pages\CreatePersonalInfo::route('/create'),
            'view' => Pages\ViewPersonalInfo::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query()
            ->where('status', 'pending')
            ->count();
    }

    protected static function getNavigationBadgePollingInterval(): ?string
    {
        return '10s';  // Will update every 10 seconds
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::query()->where('status', 'pending')->count() > 0 
            ? 'warning'
            : 'primary';
    }

    public static function deleteDraftApplications(): void
    {
        try {
            \Illuminate\Support\Facades\Log::info("Starting draft applications check");
            
            $drafts = static::getModel()::query()
                ->where('status', 'draft')
                ->where('created_at', '<=', now());

            $count = $drafts->count();
            
            \Illuminate\Support\Facades\Log::info("Found {$count} draft applications", [
                'timestamp' => now()->toDateTimeString(),
                'count' => $count
            ]);
            
            if ($count > 0) {
                $drafts->delete();
                \Illuminate\Support\Facades\Log::info("Deleted {$count} draft applications", [
                    'timestamp' => now()->toDateTimeString(),
                    'deleted_count' => $count
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error deleting draft applications", [
                'timestamp' => now()->toDateTimeString(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
