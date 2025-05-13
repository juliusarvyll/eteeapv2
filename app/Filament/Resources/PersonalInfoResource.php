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
use Carbon\Carbon;
use Filament\Widgets\Widget;
use App\Services\ApplicantPdfService;

class PersonalInfoResource extends Resource
{
    protected static ?string $model = PersonalInfo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Applicant';

    protected static ?string $modelLabel = 'Applicant Information';

    // Constants for alert thresholds
    public const WARNING_THRESHOLD = 5; // 5 days
    public const CRITICAL_THRESHOLD = 5; // More than 5 days

    protected static function boot()
    {
        parent::boot();

        static::deleteDraftApplications();
    }

    // Method to calculate days since creation
    public static function getDaysPending($createdAt)
    {
        return Carbon::parse($createdAt)->diffInDays(Carbon::now());
    }

    // Method to determine alert status based on days pending
    public static function getAlertStatus($createdAt)
    {
        $daysPending = self::getDaysPending($createdAt);

        if ($daysPending > self::CRITICAL_THRESHOLD) {
            return 'critical';
        } elseif ($daysPending == self::WARNING_THRESHOLD) {
            return 'warning';
        } else {
            return 'normal';
        }
    }

    // Method to get alert color based on status
    public static function getAlertColor($status)
    {
        return match ($status) {
            'warning' => 'warning',
            'critical' => 'danger',
            default => 'success',
        };
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

                // Days Pending Column with color coding
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Days Pending')
                    ->formatStateUsing(function ($state) {
                        $daysPending = self::getDaysPending($state);
                        return $daysPending . ' days';
                    })
                    ->badge()
                    ->color(function ($state) {
                        $alertStatus = self::getAlertStatus($state);
                        return self::getAlertColor($alertStatus);
                    })
                    ->sortable(),

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
                // Filter for applications by age
                Tables\Filters\Filter::make('pending_priority')
                    ->form([
                        Forms\Components\Select::make('priority')
                            ->options([
                                'warning' => 'Warning (5 days)',
                                'critical' => 'Critical (> 5 days)',
                                'all_pending' => 'All Pending',
                            ])
                            ->placeholder('Select Priority')
                            ->default('all_pending'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!$data['priority'] || $data['priority'] === 'all_pending') {
                            return $query->where('status', 'pending');
                        }

                        if ($data['priority'] === 'warning') {
                            $date = Carbon::now()->subDays(self::WARNING_THRESHOLD);
                            return $query->where('status', 'pending')
                                ->whereDate('created_at', $date->toDateString());
                        }

                        if ($data['priority'] === 'critical') {
                            $date = Carbon::now()->subDays(self::CRITICAL_THRESHOLD);
                            return $query->where('status', 'pending')
                                ->whereDate('created_at', '<', $date->toDateString());
                        }

                        return $query;
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['priority'] || $data['priority'] === 'all_pending') {
                            return null;
                        }

                        return 'Priority: ' . match ($data['priority']) {
                            'warning' => 'Warning',
                            'critical' => 'Critical',
                            default => null,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                // Detailed PDF Export Action
                Tables\Actions\Action::make('export_detailed_pdf')
                    ->label('Export Detailed PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function (PersonalInfo $record) {
                        $pdfService = app(ApplicantPdfService::class);
                        $pdf = $pdfService->generateSingleApplicantPdf($record);

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "applicant_{$record->applicant_id}_detailed.pdf"
                        );
                    }),

                Tables\Actions\Action::make('sendEmail')
                    ->label('Send Email')
                    ->icon('heroicon-o-envelope')
                    ->form([
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->label('Email Subject')
                            ->default('Important Information from Our Institution'),
                        Forms\Components\RichEditor::make('message')
                            ->required()
                            ->label('Email Message')
                            ->default(function (PersonalInfo $record) {
                                return "Dear {$record->firstName} {$record->lastName},\n\nThank you for your application.\n\nBest regards,\nAdmissions Office";
                            }),
                    ])
                    ->action(function (array $data, PersonalInfo $record) {
                        // Check if email exists
                        if (empty($record->email)) {
                            \Filament\Notifications\Notification::make()
                                ->title('Email not found')
                                ->body('This applicant does not have an email address.')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Send email
                        try {
                            \Mail::to($record->email)
                                ->send(new \App\Mail\ApplicantMail(
                                    $data['subject'],
                                    $data['message'],
                                    $record
                                ));

                            \Filament\Notifications\Notification::make()
                                ->title('Email sent successfully')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Failed to send email')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                // New action to send reminder email for pending applications
                Tables\Actions\Action::make('sendReminderEmail')
                    ->label('Send Reminder')
                    ->icon('heroicon-o-bell')
                    ->color(fn ($record) => self::getAlertColor(self::getAlertStatus($record->created_at)))
                    ->hidden(fn ($record) => $record->status !== 'pending')
                    ->form([
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->label('Reminder Subject')
                            ->default(function (PersonalInfo $record) {
                                $status = self::getAlertStatus($record->created_at);
                                $prefix = $status === 'critical' ? 'URGENT: ' : '';
                                return $prefix . 'Follow-up on Your Pending Application';
                            }),
                        Forms\Components\RichEditor::make('message')
                            ->required()
                            ->label('Reminder Message')
                            ->default(function (PersonalInfo $record) {
                                $daysPending = self::getDaysPending($record->created_at);
                                return "Dear {$record->firstName} {$record->lastName},\n\nYour application has been pending for {$daysPending} days. Please update your information or contact our office for assistance.\n\nBest regards,\nAdmissions Office";
                            }),
                    ])
                    ->action(function (array $data, PersonalInfo $record) {
                        // Same email sending logic as above
                        if (empty($record->email)) {
                            \Filament\Notifications\Notification::make()
                                ->title('Email not found')
                                ->body('This applicant does not have an email address.')
                                ->danger()
                                ->send();
                            return;
                        }

                        try {
                            \Mail::to($record->email)
                                ->send(new \App\Mail\ApplicantMail(
                                    $data['subject'],
                                    $data['message'],
                                    $record
                                ));

                            \Filament\Notifications\Notification::make()
                                ->title('Reminder email sent successfully')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Failed to send reminder email')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    // Bulk Export PDF Action
                    Tables\Actions\BulkAction::make('export_bulk_pdf')
                        ->label('Export Bulk PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $pdfService = app(ApplicantPdfService::class);
                            $pdf = $pdfService->generateBulkApplicantsPdf($records);

                            return response()->streamDownload(
                                fn () => print($pdf->output()),
                                "applicants_bulk_" . date('Y-m-d') . ".pdf"
                            );
                        }),

                    // Bulk Reminder Action
                    Tables\Actions\BulkAction::make('sendBulkReminders')
                        ->label('Send Bulk Reminders')
                        ->icon('heroicon-o-bell')
                        ->action(function ($records) {
                            $sent = 0;
                            $failed = 0;

                            foreach ($records as $record) {
                                if ($record->status !== 'pending' || empty($record->email)) {
                                    $failed++;
                                    continue;
                                }

                                $status = self::getAlertStatus($record->created_at);
                                $prefix = $status === 'critical' ? 'URGENT: ' : '';
                                $subject = $prefix . 'Follow-up on Your Pending Application';

                                $daysPending = self::getDaysPending($record->created_at);
                                $message = "Dear {$record->firstName} {$record->lastName},\n\nYour application has been pending for {$daysPending} days. Please update your information or contact our office for assistance.\n\nBest regards,\nAdmissions Office";

                                try {
                                    \Mail::to($record->email)
                                        ->send(new \App\Mail\ApplicantMail(
                                            $subject,
                                            $message,
                                            $record
                                        ));
                                    $sent++;
                                } catch (\Exception $e) {
                                    $failed++;
                                    Log::error("Failed to send reminder to {$record->email}: " . $e->getMessage());
                                }
                            }

                            \Filament\Notifications\Notification::make()
                                ->title("Reminders sent: {$sent}, Failed: {$failed}")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
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
