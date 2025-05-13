<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PersonalInfo;
use App\Filament\Resources\PersonalInfoResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendApplicationReminderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-application-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails for pending applications that have exceeded the warning or critical thresholds';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting application reminder emails...');
        $sentCount = 0;
        $failedCount = 0;

        // Get warning applications (exactly 5 days old)
        $warningDate = Carbon::now()->subDays(PersonalInfoResource::WARNING_THRESHOLD);
        $warningApplications = PersonalInfo::where('status', 'pending')
            ->whereDate('created_at', $warningDate->toDateString())
            ->get();

        $this->info("Found {$warningApplications->count()} applications at warning threshold.");

        // Get critical applications (more than 5 days old)
        $criticalDate = Carbon::now()->subDays(PersonalInfoResource::CRITICAL_THRESHOLD);
        $criticalApplications = PersonalInfo::where('status', 'pending')
            ->whereDate('created_at', '<', $criticalDate->toDateString())
            ->get();

        $this->info("Found {$criticalApplications->count()} applications at critical threshold.");

        // Process warning applications
        foreach ($warningApplications as $application) {
            if (empty($application->email)) {
                $this->error("No email found for application ID: {$application->applicant_id}");
                $failedCount++;
                continue;
            }

            $daysPending = PersonalInfoResource::getDaysPending($application->created_at);
            $subject = "Follow-up on Your Pending Application";
            $message = "Dear {$application->firstName} {$application->lastName},\n\n";
            $message .= "Your application has been pending for {$daysPending} days. ";
            $message .= "Please update your information or contact our office for assistance.\n\n";
            $message .= "Best regards,\nAdmissions Office";

            try {
                Mail::to($application->email)
                    ->send(new \App\Mail\ApplicantMail(
                        $subject,
                        $message,
                        $application
                    ));

                $this->info("Sent warning reminder to {$application->email}");
                $sentCount++;
            } catch (\Exception $e) {
                $this->error("Failed to send warning reminder to {$application->email}: " . $e->getMessage());
                Log::error("Failed to send warning reminder to {$application->email}: " . $e->getMessage());
                $failedCount++;
            }
        }

        // Process critical applications
        foreach ($criticalApplications as $application) {
            if (empty($application->email)) {
                $this->error("No email found for application ID: {$application->applicant_id}");
                $failedCount++;
                continue;
            }

            $daysPending = PersonalInfoResource::getDaysPending($application->created_at);
            $subject = "URGENT: Follow-up on Your Pending Application";
            $message = "Dear {$application->firstName} {$application->lastName},\n\n";
            $message .= "Your application has been pending for {$daysPending} days. ";
            $message .= "This is an urgent reminder that action is required. ";
            $message .= "Please update your information or contact our office immediately.\n\n";
            $message .= "Best regards,\nAdmissions Office";

            try {
                Mail::to($application->email)
                    ->send(new \App\Mail\ApplicantMail(
                        $subject,
                        $message,
                        $application
                    ));

                $this->info("Sent critical reminder to {$application->email}");
                $sentCount++;
            } catch (\Exception $e) {
                $this->error("Failed to send critical reminder to {$application->email}: " . $e->getMessage());
                Log::error("Failed to send critical reminder to {$application->email}: " . $e->getMessage());
                $failedCount++;
            }
        }

        $this->info("Task completed. Sent: {$sentCount}, Failed: {$failedCount}");

        return Command::SUCCESS;
    }
}
