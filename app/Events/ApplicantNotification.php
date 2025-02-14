<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\PersonalInfo;
use Illuminate\Support\Facades\Validator;

class ApplicantNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $application;

    /**
     * Create a new event instance.
     */
    public function __construct(PersonalInfo $application)
    {
        $this->application = $application;
        $this->validateEducationYears();
    }

    /**
     * Validate education years
     */
    protected function validateEducationYears()
    {
        $currentYear = date('Y');
        
        // Load education records if not already loaded
        $education = $this->application->education;
        
        foreach ($education as $record) {
            $validator = Validator::make([
                'date_from' => $record->date_from,
                'date_to' => $record->date_to,
                'pept_year' => $record->pept_year,
                'date_certified' => $record->date_certified,
            ], [
                'date_from' => ['nullable', 'integer', 'min:1900', "max:$currentYear"],
                'date_to' => [
                    'nullable', 
                    'integer', 
                    'min:1900', 
                    "max:$currentYear",
                    function ($attribute, $value, $fail) use ($record) {
                        if ($value && $record->date_from && $value < $record->date_from) {
                            $fail('End year must be after start year.');
                        }
                    }
                ],
                'pept_year' => ['nullable', 'integer', 'min:1900', "max:$currentYear"],
                'date_certified' => ['nullable', 'integer', 'min:1900', "max:$currentYear"],
            ]);

            if ($validator->fails()) {
                throw new \InvalidArgumentException(
                    'Invalid education years: ' . implode(', ', $validator->errors()->all())
                );
            }
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
