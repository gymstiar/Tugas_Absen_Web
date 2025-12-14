<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Full Report - {{ $summary['generated_at'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #3b82f6;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 20px;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .header p {
            color: #6b7280;
            font-size: 11px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background: #3b82f6;
            color: white;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            text-align: left;
            font-size: 9px;
        }
        th {
            background: #f3f4f6;
            font-weight: bold;
            color: #374151;
        }
        tr:nth-child(even) {
            background: #f9fafb;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #3b82f6;
        }
        .stat-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-green { background: #d1fae5; color: #065f46; }
        .badge-yellow { background: #fef3c7; color: #92400e; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-gray { background: #e5e7eb; color: #374151; }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            padding: 10px;
            font-size: 8px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }
        .page-break {
            page-break-after: always;
        }
        .summary-row {
            background: #eff6ff !important;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>📊 Attendance & Task Management Report</h1>
        <p>Generated on: {{ $summary['generated_at'] }}</p>
        <p>AttendanceApp - Comprehensive System Report</p>
    </div>

    <!-- Summary Statistics -->
    <div class="section">
        <div class="section-title">📈 Summary Statistics</div>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">{{ $summary['total_participants'] }}</div>
                <div class="stat-label">Participants</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $summary['total_mentors'] }}</div>
                <div class="stat-label">Mentors</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $summary['total_classes'] }}</div>
                <div class="stat-label">Classes</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $attendanceStats['percentage'] }}%</div>
                <div class="stat-label">Attendance Rate</div>
            </div>
        </div>
    </div>

    <!-- Attendance Statistics -->
    <div class="section">
        <div class="section-title">✅ Attendance Overview</div>
        <table>
            <tr>
                <th width="25%">Present</th>
                <th width="25%">Permission</th>
                <th width="25%">Sick</th>
                <th width="25%">Total Records</th>
            </tr>
            <tr class="summary-row">
                <td style="text-align:center"><span class="badge badge-green">{{ $attendanceStats['present'] }}</span></td>
                <td style="text-align:center"><span class="badge badge-yellow">{{ $attendanceStats['permission'] }}</span></td>
                <td style="text-align:center"><span class="badge badge-red">{{ $attendanceStats['sick'] }}</span></td>
                <td style="text-align:center"><span class="badge badge-blue">{{ $attendanceStats['total'] }}</span></td>
            </tr>
        </table>

        @if($attendances->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Participant</th>
                    <th>Class</th>
                    <th>Session</th>
                    <th>Status</th>
                    <th>Submitted</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances->take(30) as $index => $attendance)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $attendance->participant->name }}</td>
                    <td>{{ $attendance->attendanceSession->classGroup->name }}</td>
                    <td>{{ Str::limit($attendance->attendanceSession->title, 20) }}</td>
                    <td>
                        <span class="badge badge-{{ $attendance->status === 'present' ? 'green' : ($attendance->status === 'sick' ? 'red' : 'yellow') }}">
                            {{ ucfirst($attendance->status) }}
                        </span>
                    </td>
                    <td>{{ $attendance->submitted_at->format('M d, H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($attendances->count() > 30)
        <p style="color: #6b7280; font-style: italic; text-align: center;">Showing 30 of {{ $attendances->count() }} records. Export CSV for full data.</p>
        @endif
        @endif
    </div>

    <div class="page-break"></div>

    <!-- Task Statistics -->
    <div class="section">
        <div class="section-title">📝 Task Submissions Overview</div>
        <table>
            <tr>
                <th width="20%">Total Tasks</th>
                <th width="20%">Active Tasks</th>
                <th width="20%">Submissions</th>
                <th width="20%">Graded</th>
                <th width="20%">Pending</th>
            </tr>
            <tr class="summary-row">
                <td style="text-align:center">{{ $taskStats['total_tasks'] }}</td>
                <td style="text-align:center">{{ $taskStats['active_tasks'] }}</td>
                <td style="text-align:center"><span class="badge badge-blue">{{ $taskStats['total_submissions'] }}</span></td>
                <td style="text-align:center"><span class="badge badge-green">{{ $taskStats['graded'] }}</span></td>
                <td style="text-align:center"><span class="badge badge-yellow">{{ $taskStats['pending_grades'] }}</span></td>
            </tr>
        </table>

        <p style="margin-bottom: 10px;"><strong>Submission Rate:</strong> {{ $taskStats['submission_percentage'] }}%</p>
    </div>

    <!-- Grade Distribution -->
    <div class="section">
        <div class="section-title">🎓 Grade Distribution</div>
        <table>
            <tr>
                <th>Grade A (85-100)</th>
                <th>Grade B (70-84)</th>
                <th>Grade C (55-69)</th>
                <th>Grade D (40-54)</th>
                <th>Grade E (&lt;40)</th>
            </tr>
            <tr>
                <td style="text-align:center"><span class="badge badge-green">{{ $gradeDistribution['A'] }}</span></td>
                <td style="text-align:center"><span class="badge badge-blue">{{ $gradeDistribution['B'] }}</span></td>
                <td style="text-align:center"><span class="badge badge-yellow">{{ $gradeDistribution['C'] }}</span></td>
                <td style="text-align:center"><span class="badge badge-gray">{{ $gradeDistribution['D'] }}</span></td>
                <td style="text-align:center"><span class="badge badge-red">{{ $gradeDistribution['E'] }}</span></td>
            </tr>
        </table>
    </div>

    <!-- Task Submissions Table -->
    @if($submissions->count() > 0)
    <div class="section">
        <div class="section-title">📋 Recent Task Submissions</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Participant</th>
                    <th>Class</th>
                    <th>Task</th>
                    <th>Grade</th>
                    <th>Feedback</th>
                    <th>Submitted</th>
                    <th>Late</th>
                </tr>
            </thead>
            <tbody>
                @foreach($submissions->take(30) as $index => $submission)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ Str::limit($submission->participant->name, 15) }}</td>
                    <td>{{ Str::limit($submission->task->classGroup->name, 12) }}</td>
                    <td>{{ Str::limit($submission->task->title, 18) }}</td>
                    <td>
                        @if($submission->isGraded())
                            <span class="badge badge-{{ $submission->getGradeColor() }}">{{ $submission->grade }}</span>
                        @else
                            <span class="badge badge-gray">-</span>
                        @endif
                    </td>
                    <td>{{ Str::limit($submission->feedback ?? '-', 20) }}</td>
                    <td>{{ $submission->submitted_at->format('M d, H:i') }}</td>
                    <td>
                        @if($submission->isLate())
                            <span class="badge badge-red">Yes</span>
                        @else
                            <span class="badge badge-green">No</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($submissions->count() > 30)
        <p style="color: #6b7280; font-style: italic; text-align: center;">Showing 30 of {{ $submissions->count() }} submissions. Export CSV for full data.</p>
        @endif
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        AttendanceApp Report | Generated {{ now()->format('Y-m-d H:i:s') }} | Page 1
    </div>
</body>
</html>
