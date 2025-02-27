<?php

namespace App\Mail;

use App\Models\PersonalInfo;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicantMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $messageContent;
    public $applicant;

    /**
     * Create a new message instance.
     */
    public function __construct(string $subject, string $messageContent, PersonalInfo $applicant)
    {
        $this->subject = $subject;
        $this->messageContent = $messageContent;
        $this->applicant = $applicant;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->subject)
            ->view('emails.applicant')
            ->with([
                'messageContent' => $this->messageContent,
                'applicant' => $this->applicant,
            ]);
    }
}
