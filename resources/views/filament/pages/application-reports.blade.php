<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <h2 class="text-xl font-semibold mb-4 text-green-700 dark:text-green-500">Application Reports Dashboard</h2>
            <p class="mb-4 dark:text-gray-300">Use the filters below to generate different types of application reports and visualizations.</p>

            {{ $this->form }}
        </div>

        @if(!empty($reportData))
            <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <h3 class="text-lg font-medium mb-4 text-green-700 dark:text-green-500">Report Summary</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 bg-green-50 dark:bg-gray-700 rounded-md">
                        <h4 class="font-medium text-green-700 dark:text-green-500">Period</h4>
                        <p class="text-2xl font-bold dark:text-white">{{ $reportData['period'] ?? 'All Time' }}</p>
                    </div>
                    <div class="p-4 bg-green-50 dark:bg-gray-700 rounded-md">
                        <h4 class="font-medium text-green-700 dark:text-green-500">Total Applications</h4>
                        <p class="text-2xl font-bold dark:text-white">{{ $reportData['total_applications'] ?? 0 }}</p>
                    </div>
                    <div class="p-4 bg-green-50 dark:bg-gray-700 rounded-md">
                        <h4 class="font-medium text-green-700 dark:text-green-500">Status Breakdown</h4>
                        <div class="flex space-x-2 mt-2">
                            @if(isset($reportData['status_data']))
                                @foreach($reportData['status_data'] as $status => $data)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $status == 'pending' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300' :
                                          ($status == 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' :
                                          'bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-300') }}">
                                        {{ ucfirst($status) }}: {{ $data['count'] }} ({{ $data['percentage'] }}%)
                                    </span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Status Distribution Chart -->
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                    <h3 class="text-lg font-medium mb-4 text-green-700 dark:text-green-500">Application Status Distribution</h3>
                    <div class="aspect-w-16 aspect-h-9 bg-green-50 dark:bg-gray-700 rounded-md p-4">
                        <!-- In a real implementation, this would be a Chart.js canvas element -->
                        <div class="flex items-center justify-center h-full">
                            <div class="text-center space-y-4">
                                <div class="h-64 flex items-center justify-center">
                                    @if(isset($reportData['status_data']))
                                        <div class="flex space-x-4">
                                            @foreach($reportData['status_data'] as $status => $data)
                                                <div class="flex flex-col items-center">
                                                    <div class="w-16 relative">
                                                        <div class="h-40 w-full bg-gray-200 dark:bg-gray-600 rounded-sm relative overflow-hidden">
                                                            <div class="absolute bottom-0 w-full
                                                                {{ $status == 'pending' ? 'bg-amber-500' :
                                                                  ($status == 'approved' ? 'bg-green-600' :
                                                                  'bg-rose-500') }}"
                                                                style="height: {{ min(100, $data['percentage']) }}%">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="mt-2 text-xs font-medium dark:text-gray-300">{{ ucfirst($status) }}</span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $data['percentage'] }}%</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-gray-500 dark:text-gray-400">No data available</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Application Trends Chart -->
                <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                    <h3 class="text-lg font-medium mb-4 text-green-700 dark:text-green-500">Application Trends</h3>
                    <div class="aspect-w-16 aspect-h-9 bg-green-50 dark:bg-gray-700 rounded-md p-4">
                        <!-- In a real implementation, this would be a Chart.js canvas element -->
                        <div class="flex items-center justify-center h-full">
                            <div class="text-center space-y-4">
                                <div class="h-64 flex items-center justify-center">
                                    @if(isset($reportData['trend_data']) && count($reportData['trend_data']) > 0)
                                        <div class="w-full h-full flex items-end space-x-1">
                                            @foreach($reportData['trend_data'] as $period => $data)
                                                <div class="flex-1 flex flex-col items-center">
                                                    <div class="w-full relative">
                                                        <div class="h-40 w-full flex items-end">
                                                            <div class="w-full bg-green-500 rounded-sm"
                                                                style="height: {{ min(100, ($data['count'] / max(array_column($reportData['trend_data'], 'count'))) * 100) }}%">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="mt-2 text-xs font-medium truncate w-full dark:text-gray-300" title="{{ $period }}">{{ $period }}</span>
                                                    <span class="text-xs
                                                        {{ $data['trend'] == 'up' ? 'text-green-600 dark:text-green-400' :
                                                          ($data['trend'] == 'down' ? 'text-rose-600 dark:text-rose-400' :
                                                          'text-amber-600 dark:text-amber-400') }}">
                                                        {{ $data['trend'] == 'up' ? '↑' : ($data['trend'] == 'down' ? '↓' : '→') }}
                                                        {{ $data['percentage_change'] }}%
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-gray-500 dark:text-gray-400">No trend data available</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($reportType === 'annual' && isset($reportData['year_comparison_data']))
            <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <h3 class="text-lg font-medium mb-4 text-green-700 dark:text-green-500">Year-over-Year Comparison</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-green-50 dark:bg-gray-700 text-left text-xs font-medium text-green-700 dark:text-green-500 uppercase tracking-wider">Year</th>
                                <th class="px-6 py-3 bg-green-50 dark:bg-gray-700 text-left text-xs font-medium text-green-700 dark:text-green-500 uppercase tracking-wider">Approved</th>
                                <th class="px-6 py-3 bg-green-50 dark:bg-gray-700 text-left text-xs font-medium text-green-700 dark:text-green-500 uppercase tracking-wider">Pending</th>
                                <th class="px-6 py-3 bg-green-50 dark:bg-gray-700 text-left text-xs font-medium text-green-700 dark:text-green-500 uppercase tracking-wider">Rejected</th>
                                <th class="px-6 py-3 bg-green-50 dark:bg-gray-700 text-left text-xs font-medium text-green-700 dark:text-green-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach($reportData['year_comparison_data'] as $year => $data)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">{{ $year }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <span class="text-green-600 dark:text-green-400">{{ $data['approved'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <span class="text-amber-600 dark:text-amber-400">{{ $data['pending'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <span class="text-rose-600 dark:text-rose-400">{{ $data['rejected'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200 font-semibold">{{ $data['total'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        @endif
    </div>
</x-filament-panels::page>
