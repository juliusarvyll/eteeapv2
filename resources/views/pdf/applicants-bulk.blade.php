<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicants Summary Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #16a34a; /* Tailwind green-600 */
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        h1 {
            color: #15803d; /* Tailwind green-700 */
            font-size: 24px;
            margin-bottom: 5px;
        }
        .summary {
            background-color: #f0fdf4; /* Tailwind green-50 */
            padding: 10px;
            margin: 15px 0;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
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
        .status-badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending {
            background-color: #fef3c7; /* Tailwind amber-100 */
            color: #92400e; /* Tailwind amber-800 */
        }
        .status-approved {
            background-color: #dcfce7; /* Tailwind green-100 */
            color: #166534; /* Tailwind green-800 */
        }
        .status-rejected {
            background-color: #ffe4e6; /* Tailwind rose-100 */
            color: #9f1239; /* Tailwind rose-800 */
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-number:after {
            content: counter(page);
        }
    </style>
</head>
<body>
    <div class="header">
        @if(file_exists(public_path('images/SPUPLogo.png')))
            <img src="{{ public_path('images/SPUPLogo.png') }}" alt="Logo" class="logo">
        @endif
        <h1>Applicants Summary Report</h1>
        <p>Generated on: {{ $generatedAt }}</p>
    </div>

    <div class="summary">
        <p><strong>Total Applicants:</strong> {{ $total }}</p>
        <p><strong>Status Breakdown:</strong>
           <span style="color: #d97706;">Pending: {{ $applicants->where('status', 'pending')->count() }}</span>,
           <span style="color: #16a34a;">Approved: {{ $applicants->where('status', 'approved')->count() }}</span>,
           <span style="color: #e11d48;">Rejected: {{ $applicants->where('status', 'rejected')->count() }}</span>
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Birth Date</th>
                <th>Sex</th>
                <th>Civil Status</th>
                <th>Status</th>
                <th>Submitted</th>
                <th>Days Pending</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applicants as $applicant)
            <tr>
                <td>{{ $applicant->applicant_id }}</td>
                <td>{{ $applicant->lastName }}, {{ $applicant->firstName }} {{ $applicant->middleName }}</td>
                <td>{{ $applicant->birthDate }}</td>
                <td>{{ ucfirst($applicant->sex ?? 'N/A') }}</td>
                <td>{{ ucfirst($applicant->civilStatus ?? 'N/A') }}</td>
                <td>
                    <div class="status-badge status-{{ $applicant->status }}">
                        {{ ucfirst($applicant->status) }}
                    </div>
                </td>
                <td>{{ \Carbon\Carbon::parse($applicant->created_at)->format('Y-m-d') }}</td>
                <td>
                    @if($applicant->status == 'pending')
                        {{ \App\Filament\Resources\PersonalInfoResource::getDaysPending($applicant->created_at) }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This document is an official summary of applicants as recorded in our system.</p>
        <p class="page-number">Page </p>
    </div>
</body>
</html>
