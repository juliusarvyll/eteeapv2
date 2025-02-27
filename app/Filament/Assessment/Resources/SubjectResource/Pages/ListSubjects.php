<?php

namespace App\Filament\Assessment\Resources\SubjectResource\Pages;

use App\Filament\Assessment\Resources\SubjectResource;
use Filament\Resources\Pages\ListRecords;

class ListSubjects extends ListRecords
{
    protected static string $resource = SubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No actions needed since this is view-only
        ];
    }
}
