<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewApplicationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $applicantName,
        public string $applicantId
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'New Application Submitted',
            'message' => "New application submitted by {$this->applicantName}",
            'applicant_id' => $this->applicantId,
        ];
    }
}
