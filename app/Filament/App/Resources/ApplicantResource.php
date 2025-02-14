<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ApplicantResource\Pages;
use App\Filament\App\Resources\ApplicantResource\RelationManagers;
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
                Tables\Actions\Action::make('addSubjects')
                    ->label('Add Subjects')
                    ->form([
                        Forms\Components\TextInput::make('course_name')
                                    ->required()
                                    ->label('Course Name'),
                        Forms\Components\Repeater::make('subjects')
                            ->schema([
                                
                                Forms\Components\TextInput::make('subject_name')
                                    ->required()
                                    ->label('Subject Name'),
                            ])
                            ->columns(1)
                            ->itemLabel(fn (array $state): ?string => $state['subject_name'] ?? null)
                    ])
                    ->action(function (PersonalInfo $record, array $data) {
                        foreach ($data['subjects'] as $subject) {
                            $record->subjects()->create([
                                'applicant_id' => $record->applicant_id,
                                'course_name' => $data['course_name'],
                                'subject_name' => $subject['subject_name']
                            ]);
                        }
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
