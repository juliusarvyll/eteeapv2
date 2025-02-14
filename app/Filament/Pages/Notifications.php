<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;

class Notifications extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationLabel = 'Notifications';

    protected static string $view = 'filament.pages.notifications';

    public function getTitle(): string
    {
        return 'Notifications';
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        Notification::make()
            ->success()
            ->title('All notifications marked as read')
            ->send();
    }
}
