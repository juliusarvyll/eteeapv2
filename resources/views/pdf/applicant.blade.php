<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Information</title>
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
        }
        .applicant-id {
            font-size: 16px;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
        }
        h2 {
            color: #15803d; /* Tailwind green-700 */
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            font-size: 18px;
        }
        .info-box {
            background-color: #f0fdf4; /* Tailwind green-50 */
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            display: block;
            color: #666;
        }
        .info-value {
            display: block;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f0fdf4; /* Tailwind green-50 */
            color: #15803d; /* Tailwind green-700 */
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
    </style>
</head>
<body>
    <div class="header">
        @if(file_exists(public_path('images/SPUPLogo.png')))
            <img src="{{ public_path('images/SPUPLogo.png') }}" alt="Logo" class="logo">
        @endif
        <h1>Applicant Information Report</h1>
        <div class="applicant-id">ID: {{ $applicant->applicant_id }}</div>
        <div>
            <span class="status-badge status-{{ $applicant->status }}">
                {{ ucfirst($applicant->status) }}
            </span>
        </div>
    </div>

    <div class="section">
        <h2>Personal Information</h2>
        <div class="info-box">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Full Name:</span>
                    <span class="info-value">{{ $applicant->lastName }}, {{ $applicant->firstName }} {{ $applicant->middleName }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Birth Date:</span>
                    <span class="info-value">{{ $applicant->birthDate }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Sex:</span>
                    <span class="info-value">{{ ucfirst($applicant->sex ?? 'N/A') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Civil Status:</span>
                    <span class="info-value">{{ ucfirst($applicant->civilStatus ?? 'N/A') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Birth Place:</span>
                    <span class="info-value">{{ $applicant->birthPlace ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Religion:</span>
                    <span class="info-value">{{ $applicant->religion ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Contact Information</h2>
        <div class="info-box">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $applicant->email ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Mobile Number:</span>
                    <span class="info-value">{{ $applicant->mobileNumber ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Telephone:</span>
                    <span class="info-value">{{ $applicant->telephone ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Address:</span>
                    <span class="info-value">
                        {{ $applicant->houseNumber ?? '' }} {{ $applicant->street ?? '' }},
                        {{ $applicant->barangay ?? '' }}, {{ $applicant->city ?? '' }},
                        {{ $applicant->province ?? '' }} {{ $applicant->zipCode ?? '' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Application Details</h2>
        <div class="info-box">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Application Date:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($applicant->created_at)->format('F d, Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Last Updated:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($applicant->updated_at)->format('F d, Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Days Pending:</span>
                    <span class="info-value">
                        @if($applicant->status == 'pending')
                            {{ \App\Filament\Resources\PersonalInfoResource::getDaysPending($applicant->created_at) }}
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Program Choice:</span>
                    <span class="info-value">{{ $applicant->program ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Education Background</h2>
        <div class="info-box">
            <div class="info-item">
                <span class="info-label">Last School Attended:</span>
                <span class="info-value">{{ $applicant->lastSchoolAttended ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Highest Educational Attainment:</span>
                <span class="info-value">{{ $applicant->highestEducation ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>This document is an official record of the applicant information as recorded in our system.</p>
        <p>Generated on: {{ now()->format('F d, Y H:i:s') }}</p>
    </div>
</body>
</html>
