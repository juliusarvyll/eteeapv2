<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Profile - {{ $applicant->firstName }} {{ $applicant->lastName }}</title>
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
            border-bottom: 2px solid #008000;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .app-id {
            background-color: #f5f5f5;
            padding: 5px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: right;
        }
        h1 {
            color: #008000;
            font-size: 24px;
            margin-bottom: 5px;
        }
        h2 {
            color: #008000;
            font-size: 18px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-top: 20px;
        }
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending {
            background-color: #FFC107;
            color: #000;
        }
        .status-approved {
            background-color: #4CAF50;
            color: #fff;
        }
        .status-rejected {
            background-color: #F44336;
            color: #fff;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
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
        <h1>Applicant Profile</h1>
        <p>Generated on: {{ $generatedAt }}</p>
    </div>

    <div class="app-id">
        Application ID: {{ $applicant->applicant_id }}
    </div>

    <div class="section">
        <h2>Personal Information</h2>
        <table>
            <tr>
                <th width="30%">Name</th>
                <td>{{ $applicant->firstName }} {{ $applicant->middleName }} {{ $applicant->lastName }} {{ $applicant->suffix }}</td>
            </tr>
            <tr>
                <th>Birth Date</th>
                <td>{{ $applicant->birthDate }}</td>
            </tr>
            <tr>
                <th>Place of Birth</th>
                <td>{{ $applicant->placeOfBirth }}</td>
            </tr>
            <tr>
                <th>Civil Status</th>
                <td>{{ ucfirst($applicant->civilStatus ?? 'Not specified') }}</td>
            </tr>
            <tr>
                <th>Sex</th>
                <td>{{ ucfirst($applicant->sex ?? 'Not specified') }}</td>
            </tr>
            <tr>
                <th>Religion</th>
                <td>{{ $applicant->religion ?? 'Not specified' }}</td>
            </tr>
            <tr>
                <th>Languages</th>
                <td>{{ $applicant->languages ?? 'Not specified' }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <div class="status-badge status-{{ $applicant->status }}">
                        {{ ucfirst($applicant->status) }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    @if($applicant->learningObjective)
    <div class="section">
        <h2>Learning Objectives</h2>
        <table>
            <tr>
                <th width="30%">First Priority</th>
                <td>{{ $applicant->learningObjective->firstPriority ?? 'Not specified' }}</td>
            </tr>
            <tr>
                <th>Second Priority</th>
                <td>{{ $applicant->learningObjective->secondPriority ?? 'Not specified' }}</td>
            </tr>
            <tr>
                <th>Third Priority</th>
                <td>{{ $applicant->learningObjective->thirdPriority ?? 'Not specified' }}</td>
            </tr>
            <tr>
                <th>Goal Statement</th>
                <td>{{ $applicant->learningObjective->goalStatement ?? 'Not specified' }}</td>
            </tr>
            <tr>
                <th>Time Commitment</th>
                <td>{{ $applicant->learningObjective->timeCommitment ?? 'Not specified' }}</td>
            </tr>
        </table>
    </div>
    @endif

    @if($applicant->elementaryEducation)
    <div class="section">
        <h2>Elementary Education</h2>
        <table>
            <tr>
                <th width="30%">School Name</th>
                <td>{{ $applicant->elementaryEducation->first()->school_name ?? 'Not specified' }}</td>
            </tr>
            <tr>
                <th>School Address</th>
                <td>{{ $applicant->elementaryEducation->first()->address ?? 'Not specified' }}</td>
            </tr>
            <tr>
                <th>Date Attended</th>
                <td>
                    @if($applicant->elementaryEducation->first()->date_from && $applicant->elementaryEducation->first()->date_to)
                        {{ $applicant->elementaryEducation->first()->date_from }} to {{ $applicant->elementaryEducation->first()->date_to }}
                    @else
                        Not specified
                    @endif
                </td>
            </tr>
        </table>
    </div>
    @endif

    @if($applicant->highSchoolEducation && count($applicant->highSchoolEducation) > 0)
    <div class="section">
        <h2>High School Education</h2>
        @foreach($applicant->highSchoolEducation as $index => $highSchool)
            <table>
                <tr>
                    <th width="30%">School Name</th>
                    <td>{{ $highSchool['school_name'] ?? 'Not specified' }}</td>
                </tr>
                <tr>
                    <th>School Address</th>
                    <td>{{ $highSchool['address'] ?? 'Not specified' }}</td>
                </tr>
                <tr>
                    <th>School Type</th>
                    <td>{{ $highSchool['school_type'] ?? 'Not specified' }}</td>
                </tr>
                <tr>
                    <th>Date Attended</th>
                    <td>
                        @if($highSchool['date_from'] && $highSchool['date_to'])
                            {{ $highSchool['date_from'] }} to {{ $highSchool['date_to'] }}
                        @else
                            Not specified
                        @endif
                    </td>
                </tr>
            </table>
            @if($index < count($applicant->highSchoolEducation) - 1)
                <hr>
            @endif
        @endforeach
    </div>
    @endif

    @if($applicant->workExperiences && count($applicant->workExperiences) > 0)
    <div class="section page-break">
        <h2>Work Experience</h2>
        @foreach($applicant->workExperiences as $index => $work)
            <table>
                <tr>
                    <th width="30%">Company</th>
                    <td>{{ $work->companyName ?? 'Not specified' }}</td>
                </tr>
                <tr>
                    <th>Designation</th>
                    <td>{{ $work->designation ?? 'Not specified' }}</td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td>{{ $work->companyAddress ?? 'Not specified' }}</td>
                </tr>
                <tr>
                    <th>Period</th>
                    <td>
                        @if($work->dateFrom && $work->dateTo)
                            {{ $work->dateFrom }} to {{ $work->dateTo }}
                        @else
                            Not specified
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Employment Status</th>
                    <td>{{ $work->employmentStatus ?? 'Not specified' }}</td>
                </tr>
                <tr>
                    <th>Responsibilities</th>
                    <td>{{ $work->responsibilities ?? 'Not specified' }}</td>
                </tr>
            </table>
            @if($index < count($applicant->workExperiences) - 1)
                <hr>
            @endif
        @endforeach
    </div>
    @endif

    @if($applicant->essay)
    <div class="section">
        <h2>Essay</h2>
        <div style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9;">
            {{ $applicant->essay->content ?? 'No essay provided.' }}
        </div>
    </div>
    @endif

    <div class="footer">
        <p>This document is an official record of the applicant's information as submitted to our institution.</p>
        <p>Page 1</p>
    </div>
</body>
</html>
