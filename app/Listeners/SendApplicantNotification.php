<?php

namespace App\Listeners;

use App\Events\ApplicantNotification;
use App\Notifications\NewApplicationSubmitted;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class SendApplicantNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ApplicantNotification $event): void
    {
        $admins = User::all();

        Notification::send(
            $admins,
            new NewApplicationSubmitted($event->application)
        );
    }
}
