<?php

namespace App\Filament\App\Resources\ApplicantResource\Pages;

use App\Models\Subject;
use Filament\Actions;
use Filament\Notifications\Notification;
use App\Filament\App\Resources\ApplicantResource;
use Filament\Resources\Pages\ViewRecord;

class ViewApplicant extends ViewRecord
{
    protected static string $resource = ApplicantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('addSubjects')
                ->label('Add Subjects')
                ->icon('heroicon-o-book-open')
                ->form([
                    \Filament\Forms\Components\TextInput::make('course_name')
                        ->required()
                        ->label('Course Name'),
                    \Filament\Forms\Components\Repeater::make('subjects')
                        ->schema([
                            \Filament\Forms\Components\TextInput::make('subject_name')
                                ->required()
                                ->label('Subject Name')
                                ->maxLength(255),
                            \Filament\Forms\Components\TextInput::make('units')
                                ->required()
                                ->numeric()
                                ->default(3)
                                ->label('Units'),
                            \Filament\Forms\Components\TextInput::make('schedule')
                                ->required()
                                ->label('Schedule')
                        ])
                        ->itemLabel(fn (array $state): ?string => $state['subject_name'] ?? null)
                        ->collapsible()
                        ->minItems(1)
                        ->maxItems(10)
                        ->defaultItems(1)
                ])
                ->action(function (array $data, $record) {
                    foreach ($data['subjects'] as $subjectData) {
                        $record->subjects()->create([
                            'applicant_id' => $record->applicant_id,
                            'course_name' => $data['course_name'],
                            'subject_name' => $subjectData['subject_name'],
                            'units' => $subjectData['units'],
                            'schedule' => $subjectData['schedule']
                        ]);
                    }

                    Notification::make()
                        ->title('Subjects added successfully')
                        ->success()
                        ->send();

                    $this->form->fill();
                }),
            Actions\Action::make('generate_pdf')
                ->label('Generate PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->url(fn ($record) => route('assessment.pdf', $record))
                ->openUrlInNewTab()
        ];
    }
}
