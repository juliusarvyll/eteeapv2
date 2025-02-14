<?php

namespace App\Notifications;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationSubmitted extends Notification
{
    public $applicantName;
    public $applicantId;
    public $status;

    public function __construct(string $applicantName, string $applicantId, string $status = 'submitted')
    {
        $this->applicantName = $applicantName;
        $this->applicantId = $applicantId;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database', 'mail']; // Send to both database and email
    }

    public function toMail($notifiable)
    {
        $subject = $this->status === 'started'
            ? 'New Application Started: ' . $this->applicantName
            : 'Application Submitted: ' . $this->applicantName;

        return (new MailMessage)
            ->subject($subject)
            ->line($this->getStatusMessage())
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->getStatusTitle(),
            'message' => $this->getStatusMessage(),
            'applicant_id' => $this->applicantId,
            'status' => $this->status,
        ];
    }

    protected function getStatusTitle()
    {
        return $this->status === 'started'
            ? 'New Application Started'
            : 'Application Submitted';
    }

    protected function getStatusMessage()
    {
        return $this->status === 'started'
            ? "{$this->applicantName} has started a new application (ID: {$this->applicantId})"
            : "{$this->applicantName} has submitted their application (ID: {$this->applicantId})";
    }
}
