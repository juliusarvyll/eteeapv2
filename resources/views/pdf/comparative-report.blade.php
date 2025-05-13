<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Comparative Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #16a34a; /* Tailwind green-600 */
            padding-bottom: 10px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 15px;
        }
        h1 {
            color: #15803d; /* Tailwind green-700 */
            margin: 0 0 10px;
            font-size: 24px;
        }
        .report-meta {
            background-color: #f0fdf4; /* Tailwind green-50 */
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .section {
            margin-bottom: 30px;
        }
        h2 {
            color: #15803d; /* Tailwind green-700 */
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            font-size: 18px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f0fdf4; /* Tailwind green-50 */
            color: #15803d; /* Tailwind green-700 */
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .chart-container {
            margin: 20px 0;
            text-align: center;
        }
        .chart-image {
            max-width: 100%;
            height: auto;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            text-align: center;
            color: #666;
        }
        .page-break {
            page-break-after: always;
        }
        .trend-up {
            color: #16a34a; /* Tailwind green-600 */
            font-weight: bold;
        }
        .trend-down {
            color: #e11d48; /* Tailwind rose-600 */
            font-weight: bold;
        }
        .trend-neutral {
            color: #d97706; /* Tailwind amber-600 */
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        @if(file_exists(public_path('images/SPUPLogo.png')))
            <img src="{{ public_path('images/SPUPLogo.png') }}" alt="Logo" class="logo">
        @endif
        <h1>Application Comparative Report</h1>
    </div>

    <div class="report-meta">
        <p><strong>Report Period:</strong> {{ $reportPeriod }}</p>
        <p><strong>Generated:</strong> {{ now()->format('F d, Y H:i:s') }}</p>
        <p><strong>Total Applications:</strong> {{ $totalApplications }}</p>
    </div>

    <div class="section">
        <h2>Application Status Distribution</h2>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Pending</td>
                    <td>{{ $statusCounts['pending'] }}</td>
                    <td>{{ number_format(($statusCounts['pending'] / $totalApplications) * 100, 1) }}%</td>
                </tr>
                <tr>
                    <td>Approved</td>
                    <td>{{ $statusCounts['approved'] }}</td>
                    <td>{{ number_format(($statusCounts['approved'] / $totalApplications) * 100, 1) }}%</td>
                </tr>
                <tr>
                    <td>Rejected</td>
                    <td>{{ $statusCounts['rejected'] }}</td>
                    <td>{{ number_format(($statusCounts['rejected'] / $totalApplications) * 100, 1) }}%</td>
                </tr>
            </tbody>
        </table>

        @if(isset($charts['statusDistribution']))
            <div class="chart-container">
                <img src="{{ $charts['statusDistribution'] }}" alt="Status Distribution Chart" class="chart-image">
            </div>
        @endif
    </div>

    <div class="section">
        <h2>Application Trends ({{ $timeframe }})</h2>
        <table>
            <thead>
                <tr>
                    <th>Period</th>
                    <th>Total Applications</th>
                    <th>Approved</th>
                    <th>Approval Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trends as $period => $data)
                <tr>
                    <td>{{ $period }}</td>
                    <td>{{ $data['total'] }}</td>
                    <td>{{ $data['approved'] }}</td>
                    <td>{{ number_format($data['approvalRate'], 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if(isset($charts['trends']))
            <div class="chart-container">
                <img src="{{ $charts['trends'] }}" alt="Application Trends Chart" class="chart-image">
            </div>
        @endif
    </div>

    @if(isset($yearComparison) && count($yearComparison) > 0)
    <div class="section">
        <h2>Year-Over-Year Comparison</h2>
        <table>
            <thead>
                <tr>
                    <th>Metric</th>
                    @foreach($yearComparison['years'] as $year)
                        <th>{{ $year }}</th>
                    @endforeach
                    <th>Change</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Applications</td>
                    @foreach($yearComparison['years'] as $year)
                        <td>{{ $yearComparison['data'][$year]['total'] }}</td>
                    @endforeach
                    <td>
                        @php
                            $firstYear = $yearComparison['years'][0];
                            $lastYear = $yearComparison['years'][count($yearComparison['years'])-1];
                            $change = $yearComparison['data'][$lastYear]['total'] - $yearComparison['data'][$firstYear]['total'];
                            $changePercent = $yearComparison['data'][$firstYear]['total'] > 0
                                ? ($change / $yearComparison['data'][$firstYear]['total']) * 100
                                : 0;
                        @endphp

                        @if($change > 0)
                            <span class="trend-up">+{{ $change }} (+{{ number_format($changePercent, 1) }}%)</span>
                        @elseif($change < 0)
                            <span class="trend-down">{{ $change }} ({{ number_format($changePercent, 1) }}%)</span>
                        @else
                            <span class="trend-neutral">0 (0%)</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Approval Rate</td>
                    @foreach($yearComparison['years'] as $year)
                        <td>{{ number_format($yearComparison['data'][$year]['approvalRate'], 1) }}%</td>
                    @endforeach
                    <td>
                        @php
                            $firstYear = $yearComparison['years'][0];
                            $lastYear = $yearComparison['years'][count($yearComparison['years'])-1];
                            $change = $yearComparison['data'][$lastYear]['approvalRate'] - $yearComparison['data'][$firstYear]['approvalRate'];
                        @endphp

                        @if($change > 0)
                            <span class="trend-up">+{{ number_format($change, 1) }}%</span>
                        @elseif($change < 0)
                            <span class="trend-down">{{ number_format($change, 1) }}%</span>
                        @else
                            <span class="trend-neutral">0%</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Average Processing Time</td>
                    @foreach($yearComparison['years'] as $year)
                        <td>{{ number_format($yearComparison['data'][$year]['avgProcessingDays'], 1) }} days</td>
                    @endforeach
                    <td>
                        @php
                            $firstYear = $yearComparison['years'][0];
                            $lastYear = $yearComparison['years'][count($yearComparison['years'])-1];
                            $change = $yearComparison['data'][$lastYear]['avgProcessingDays'] - $yearComparison['data'][$firstYear]['avgProcessingDays'];
                            $changePercent = $yearComparison['data'][$firstYear]['avgProcessingDays'] > 0
                                ? ($change / $yearComparison['data'][$firstYear]['avgProcessingDays']) * 100
                                : 0;
                        @endphp

                        @if($change < 0)
                            <span class="trend-up">{{ number_format($change, 1) }} days ({{ number_format($changePercent, 1) }}%)</span>
                        @elseif($change > 0)
                            <span class="trend-down">+{{ number_format($change, 1) }} days (+{{ number_format($changePercent, 1) }}%)</span>
                        @else
                            <span class="trend-neutral">0 days (0%)</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        @if(isset($charts['yearComparison']))
            <div class="chart-container">
                <img src="{{ $charts['yearComparison'] }}" alt="Year-Over-Year Comparison Chart" class="chart-image">
            </div>
        @endif
    </div>
    @endif

    <div class="footer">
        <p>This document is an official comparative report of applications as recorded in our system.</p>
        <p>Generated by ETEEAP Application Management System</p>
    </div>
</body>
</html>
