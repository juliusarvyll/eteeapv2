<?php

namespace App\Filament\Resources\PersonalInfoResource\Pages;

use App\Filament\Resources\PersonalInfoResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Log;

class ListPersonalInfos extends ListRecords
{
    protected static string $resource = PersonalInfoResource::class;

    public function getTabs(): array
    {
        return [
            null => Tab::make('All')
                ->query(fn ($query) => $query->where('status', '!=', 'draft')),
            'pending' => Tab::make()->query(fn ($query) => $query->where('status', 'pending')),
            'approved' => Tab::make()->query(fn ($query) => $query->where('status', 'approved')),
            'rejected' => Tab::make()->query(fn ($query) => $query->where('status', 'rejected')),
        ];
    }

    public function mount(): void
    {
        try {
            Log::info('Attempting to delete draft applications');
            
            $count = $this->getResource()::getModel()::query()
                ->where('status', 'draft')
                ->where('created_at', '<=', now()->subHours(8))
                ->delete();

            Log::info("Deleted {$count} draft applications");
        } catch (\Exception $e) {
            Log::error('Failed to delete draft applications', [
                'error' => $e->getMessage()
            ]);
        }

        parent::mount();
    }
}
