<!DOCTYPE html>
<html>
<head>
    <title>Unofficial Student Assessment</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            line-height: 1.2;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }

        .header {
            text-align: center;
            font-weight: bold;
            font-size: 20px;
            color: #006937;
            margin-bottom: 15px;
        }

        .sub-header {
            font-size: 14px;
            margin-bottom: 20px;
            text-align: center;
        }

        .details-table, .details-table th, .details-table td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 5px;
        }

        .details-table {
            width: 100%;
            margin-top: 10px;
            margin-bottom: 15px;
            border-radius: 8px;
            overflow: hidden;
        }

        .details-table tr:first-child th:first-child {
            border-top-left-radius: 7px;
        }

        .details-table tr:first-child th:last-child {
            border-top-right-radius: 7px;
        }

        .details-table tr:last-child td:first-child {
            border-bottom-left-radius: 7px;
        }

        .details-table tr:last-child td:last-child {
            border-bottom-right-radius: 7px;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
        }

        .total-row {
            background-color: #f3f4f6;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/spup-logo.png') }}" style="height: 40px; margin-bottom: 5px; display: block; margin-left: auto; margin-right: auto;" alt="School Logo">
        <div class="sub-header">
            St. Paul University Philippines<br>
        </div>
    </div>

    <div class="student-info">
        <table class="details-table">
            <tr>
                <th colspan="4" style="text-align: center; background: #f3f4f6;">Student Information</th>
            </tr>
            <tr>
                <td width="15%"><strong>Name:</strong></td>
                <td colspan="3">
                    {{ $student->firstName }}
                    @if($student->middleName) {{ $student->middleName }} @endif
                    {{ $student->lastName }}
                    @if($student->suffix) {{ $student->suffix }} @endif
                </td>
            </tr>
        </table>
        <p><strong>Student ID:</strong> {{ $student->applicant_id }}</p>
        @if($student->subjects->isNotEmpty())
        <p><strong>Program:</strong> {{ $student->subjects->first()->course_name }}</p>
        @else
        <p><strong>Program:</strong> No subjects assigned</p>
        @endif
        <p><strong>Assessment Date:</strong> {{ now()->format('F d, Y') }}</p>
    </div>

    <table class="details-table">
        <tr>
            <th colspan="4" style="text-align: center; background: #f3f4f6;">Subject Details</th>
        </tr>
        <tr>
            <th>Subject Code</th>
            <th>Subject Name</th>
            <th>Units</th>
            <th>Schedule</th>
        </tr>
        @foreach($student->subjects as $subject)
        <tr>
            <td>{{ strtoupper(substr($subject->subject_name, 0, 3)) }}-{{ str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</td>
            <td>{{ $subject->subject_name }}</td>
            <td>{{ $subject->units }}</td>
            <td>{{ $subject->schedule }}</td>
        </tr>
        @endforeach

        @if($student->subjects->isNotEmpty())
        <tr class="total-row">
            <td colspan="2" style="text-align: right;"><strong>Total Units:</strong></td>
            <td>{{ $student->subjects->sum('units') }}</td>
            <td></td>
        </tr>
        @endif
    </table>
</body>
</html>
