<!DOCTYPE html>
<html>
<head>
    <title>Student Assessment</title>
    <style>
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
        }
        .logo {
            height: 80px;
            margin-bottom: 10px;
        }
        .student-info {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
        }
        .subject-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .subject-table th, .subject-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #000;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ storage_path('app/public/images/spup-logo.png') }}" class="logo" alt="School Logo">
        <h2>Saint Paul University Philippines</h2>
        <h3>Student Assessment Report</h3>
    </div>

    <div class="student-info">
        <p><strong>Name:</strong> {{ $student->fullName() }}</p>
        <p><strong>Student ID:</strong> {{ $student->applicant_id }}</p>
        @if($student->subjects->isNotEmpty())
        <p><strong>Program:</strong> {{ $student->subjects->first()->course_name }}</p>
        @else
        <p><strong>Program:</strong> No subjects assigned</p>
        @endif
        <p><strong>Assessment Date:</strong> {{ now()->format('F d, Y') }}</p>
    </div>

    <table class="subject-table">
        <thead>
            <tr>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Units</th>
                <th>Schedule</th>
            </tr>
        </thead>
        <tbody>
            @foreach($student->subjects as $subject)
            <tr>
                <td>{{ strtoupper(substr($subject->subject_name, 0, 3)) }}-{{ str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $subject->subject_name }}</td>
                <td>3</td>
                <td>MWF 8:00-9:00 AM</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Assessor's Comments:</strong></p>
        <p>_________________________________________________________</p>
        <p style="margin-top: 30px;">
            <strong>Assessor's Signature:</strong> ___________________________
            <span style="float: right;">
                <strong>Date:</strong> ___________________________
            </span>
        </p>
    </div>
</body>
</html> 