<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ApplicantResource\Pages;
use App\Models\PersonalInfo;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

class ApplicantResource extends Resource
{
    protected static ?string $model = PersonalInfo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('firstName')
                    ->label('First Name'),
                TextEntry::make('middleName')
                    ->label('Middle Name'),
                TextEntry::make('lastName')
                    ->label('Last Name'),
                TextEntry::make('suffix'),
                TextEntry::make('birthDate')
                    ->label('Birth Date')
                    ->date(),
                TextEntry::make('placeOfBirth'),
                TextEntry::make('civilStatus'),
                ImageEntry::make('document')
                    ->label('Document')
                    ->visibility('private'),
                TextEntry::make('sex'),
                TextEntry::make('religion'),
                TextEntry::make('languages'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(static::getModel()::query()->where('status', 'approved'))
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
            ])
            ->actions([
                Tables\Actions\Action::make('generate_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn (PersonalInfo $record) => route('assessment.pdf', $record))
                    ->openUrlInNewTab(),
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
                Tables\Actions\Action::make('addSubjects')
                    ->label('Add Subjects')
                    ->form(function ($record) {
                        // Check if student already has subjects with a course
                        $existingCourse = $record->subjects->first()?->course_name;

                        return [
                            Forms\Components\TextInput::make('course_name')
                                ->required()
                                ->label('Course Name')
                                ->default($existingCourse)
                                ->disabled($existingCourse !== null)
                                ->helperText($existingCourse
                                    ? 'Course is already set and cannot be changed.'
                                    : 'Enter the course name for this student.'),
                            Forms\Components\Repeater::make('subjects')
                                ->schema([
                                    Forms\Components\TextInput::make('subject_name')
                                        ->required()
                                        ->label('Subject Name'),
                                    Forms\Components\TextInput::make('units')
                                        ->required()
                                        ->numeric()
                                        ->default(3)
                                        ->label('Units'),
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\CheckboxList::make('days')
                                                ->required()
                                                ->label('Days')
                                                ->options([
                                                    'Monday' => 'Monday',
                                                    'Tuesday' => 'Tuesday',
                                                    'Wednesday' => 'Wednesday',
                                                    'Thursday' => 'Thursday',
                                                    'Friday' => 'Friday',
                                                    'Saturday' => 'Saturday',
                                                ])
                                                ->columns(3),
                                            Forms\Components\Grid::make(2)
                                                ->schema([
                                                    Forms\Components\TimePicker::make('start_time')
                                                        ->required()
                                                        ->label('Start Time')
                                                        ->seconds(false),
                                                    Forms\Components\TimePicker::make('end_time')
                                                        ->required()
                                                        ->label('End Time')
                                                        ->seconds(false),
                                                ]),
                                        ])
                                ])
                                ->columns(1)
                                ->itemLabel(fn (array $state): ?string => $state['subject_name'] ?? null)
                        ];
                    })
                    ->action(function (array $data, $record) {
                        // Get existing course or use the one from the form
                        $courseName = $record->subjects->first()?->course_name ?? $data['course_name'];

                        foreach ($data['subjects'] as $subjectData) {
                            // Format days
                            $days = implode('/', $subjectData['days']);

                            // Format times
                            $startTime = date('g:i A', strtotime($subjectData['start_time']));
                            $endTime = date('g:i A', strtotime($subjectData['end_time']));
                            $timeRange = "{$startTime} - {$endTime}";

                            // Combine into schedule
                            $schedule = "{$days} {$timeRange}";

                            $record->subjects()->create([
                                'applicant_id' => $record->applicant_id,
                                'course_name' => $courseName,
                                'subject_name' => $subjectData['subject_name'],
                                'units' => $subjectData['units'],
                                'schedule' => $schedule
                            ]);
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Subjects added successfully')
                            ->success()
                            ->send();
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListApplicants::route('/'),
            'view' => Pages\ViewApplicant::route('/{record}'),
        ];
    }
}
