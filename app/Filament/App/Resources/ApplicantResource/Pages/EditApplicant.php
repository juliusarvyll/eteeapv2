<?php

namespace App\Filament\App\Resources\ApplicantResource\Pages;

use App\Filament\App\Resources\ApplicantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApplicant extends EditRecord
{
    protected static string $resource = ApplicantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
