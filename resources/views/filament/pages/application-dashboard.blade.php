<x-filament-panels::page>
    <div class="mt-6">
        <div class="p-6 bg-white rounded-lg shadow-sm">
            <h2 class="text-xl font-semibold mb-4">Application Alerts Information</h2>

            <div class="prose max-w-none">
                <p>This dashboard shows priority alerts for pending applications that require attention.</p>
                <ul>
                    <li><span class="text-red-600 font-bold">Critical</span>: Applications pending for more than 5 days</li>
                    <li><span class="text-yellow-600 font-bold">Warning</span>: Applications pending for exactly 5 days</li>
                    <li><span class="text-green-600 font-bold">Normal</span>: Applications pending for less than 5 days</li>
                </ul>

                <p class="mt-4">Click on any of the alert cards above to view the corresponding applications.</p>

                <div class="mt-6">
                    <h3 class="font-medium text-lg mb-2">Automated Notifications</h3>
                    <p>The system automatically sends email notifications to applicants according to the following schedule:</p>
                    <ul>
                        <li>Warning notifications (5 days): Standard follow-up</li>
                        <li>Critical notifications (> 5 days): Urgent follow-up</li>
                    </ul>
                    <p>Notifications are sent daily at 8:00 AM.</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
