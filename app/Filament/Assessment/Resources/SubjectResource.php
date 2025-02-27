<?php

namespace App\Filament\Assessment\Resources;

use App\Filament\Assessment\Resources\SubjectResource\Pages;
use App\Models\PersonalInfo;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Forms;

class SubjectResource extends Resource
{
    protected static ?string $model = PersonalInfo::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'View Subjects';

    protected static ?string $modelLabel = 'Student Subjects';

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('firstName')
                    ->label('First Name'),
                TextEntry::make('lastName')
                    ->label('Last Name'),
                TextEntry::make('subjects.course_name')
                    ->label('Course')
                    ->formatStateUsing(fn ($record) => $record->subjects->first()?->course_name),
                TextEntry::make('subjects')
                    ->label('Enrolled Subjects')
                    ->html()
                    ->formatStateUsing(function ($record) {
                        $subjectsList = $record->subjects->map(function ($subject) {
                            return "
                                <div class='space-y-1 bg-gray-500/5 dark:bg-gray-500/15 p-3 rounded-lg mb-3'>
                                    <div class='font-medium text-primary-600 dark:text-primary-400'>{$subject->subject_name}</div>
                                    <div class='text-sm text-gray-600 dark:text-gray-400'>
                                        <div><span class='font-medium'>Units:</span> {$subject->units}</div>
                                        <div><span class='font-medium'>Schedule:</span> {$subject->schedule}</div>
                                    </div>
                                </div>
                            ";
                        })->join('');

                        return "
                            <div class='space-y-2'>
                                {$subjectsList}
                                <div class='pt-2 border-t border-gray-200 dark:border-gray-700'>
                                    <span class='font-medium'>Total Units:</span> {$record->subjects->sum('units')}
                                </div>
                            </div>
                        ";
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                static::getModel()::query()
                    ->where('status', 'approved')
                    ->whereHas('subjects')
            )
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

                Tables\Columns\TextColumn::make('subjects.course_name')
                    ->label('Course')
                    ->state(function (PersonalInfo $record): string {
                        return $record->subjects->first()?->course_name ?? 'N/A';
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('subjects_count')
                    ->label('Number of Subjects')
                    ->counts('subjects'),

                Tables\Columns\TextColumn::make('subjects.units')
                    ->label('Total Units')
                    ->state(function (PersonalInfo $record): string {
                        return $record->subjects->sum('units') . ' units';
                    }),

                Tables\Columns\TextColumn::make('subjects.schedule')
                    ->label('Schedules')
                    ->state(function (PersonalInfo $record): string {
                        return $record->subjects->pluck('schedule')->join(', ');
                    })
                    ->wrap(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('sendEmail')
                    ->label('Send Email')
                    ->icon('heroicon-o-envelope')
                    ->form([
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->label('Email Subject')
                            ->default('Information About Your Enrolled Subjects'),
                        Forms\Components\RichEditor::make('message')
                            ->required()
                            ->label('Email Message')
                            ->default(function (PersonalInfo $record) {
                                $courseName = $record->subjects->first()?->course_name ?? 'your course';
                                $totalUnits = $record->subjects->sum('units');
                                $subjectsList = $record->subjects->pluck('subject_name')->join(', ');

                                return "Dear {$record->firstName} {$record->lastName},\n\nThis is regarding your enrollment in {$courseName}.\n\nYou are currently enrolled in the following subjects: {$subjectsList}, for a total of {$totalUnits} units.\n\nPlease let us know if you have any questions about your schedule or enrollment.\n\nBest regards,\nRegistrar's Office";
                            }),
                    ])
                    ->action(function (array $data, PersonalInfo $record) {
                        // Check if email exists
                        if (empty($record->email)) {
                            \Filament\Notifications\Notification::make()
                                ->title('Email not found')
                                ->body('This student does not have an email address.')
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
            'index' => Pages\ListSubjects::route('/'),
            'view' => Pages\ViewSubject::route('/{record}'),
        ];
    }
}
