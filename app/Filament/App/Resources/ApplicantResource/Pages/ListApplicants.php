<?php

namespace App\Filament\App\Resources\ApplicantResource\Pages;

use App\Filament\App\Resources\ApplicantResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApplicants extends ListRecords
{
    protected static string $resource = ApplicantResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
