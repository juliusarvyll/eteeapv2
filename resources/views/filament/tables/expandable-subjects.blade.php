<div class="p-2">
    <div class="mb-2">
        <span class="font-medium text-primary-600 dark:text-primary-400">Course:</span> {{ $courseName }}
    </div>

    @if($subjects->isNotEmpty())
        <div class="rounded-lg overflow-hidden border border-gray-300 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subject Name</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Units</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Schedule</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($subjects as $subject)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $subject->subject_name }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $subject->units }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $subject->schedule }}</td>
                        </tr>
                    @endforeach
                    <tr class="bg-gray-50 dark:bg-gray-800">
                        <td class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-gray-100 text-right" colspan="1">Total Units:</td>
                        <td class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $totalUnits }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    @else
        <div class="text-sm text-gray-600 dark:text-gray-400">No subjects assigned yet.</div>
    @endif
</div>
