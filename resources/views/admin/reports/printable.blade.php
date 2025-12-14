<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Report - {{ $summary['generated_at'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            background: white;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 3px solid #4F46E5;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 24px;
            color: #4F46E5;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #666;
        }
        
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #4F46E5;
            padding: 10px 15px;
            background: #EEF2FF;
            border-left: 4px solid #4F46E5;
            margin-bottom: 15px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: #F3F4F6;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        
        .stat-card .number {
            font-size: 28px;
            font-weight: bold;
            color: #4F46E5;
        }
        
        .stat-card .label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #E5E7EB;
        }
        
        th {
            background: #F9FAFB;
            font-weight: 600;
            color: #374151;
            font-size: 11px;
            text-transform: uppercase;
        }
        
        tr:hover {
            background: #F9FAFB;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
        }
        
        .badge-green { background: #D1FAE5; color: #065F46; }
        .badge-yellow { background: #FEF3C7; color: #92400E; }
        .badge-red { background: #FEE2E2; color: #991B1B; }
        .badge-blue { background: #DBEAFE; color: #1E40AF; }
        
        .progress-bar {
            height: 20px;
            background: #E5E7EB;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4F46E5, #7C3AED);
            text-align: center;
            color: white;
            font-size: 11px;
            font-weight: bold;
            line-height: 20px;
        }
        
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #4F46E5;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .print-btn:hover {
            background: #4338CA;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            border-top: 1px solid #E5E7EB;
            margin-top: 30px;
        }
        
        @media print {
            .print-btn { display: none; }
            body { padding: 0; }
            .section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ Print / Save as PDF</button>
    
    <div class="container">
        <div class="header">
            <h1>📊 Attendance & Task Management Report</h1>
            <p>Generated: {{ $summary['generated_at'] }}</p>
        </div>
        
        <!-- Summary Stats -->
        <div class="section">
            <div class="section-title">📈 Summary Statistics</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="number">{{ $summary['total_participants'] }}</div>
                    <div class="label">Participants</div>
                </div>
                <div class="stat-card">
                    <div class="number">{{ $summary['total_mentors'] }}</div>
                    <div class="label">Mentors</div>
                </div>
                <div class="stat-card">
                    <div class="number">{{ $summary['total_classes'] }}</div>
                    <div class="label">Classes</div>
                </div>
                <div class="stat-card">
                    <div class="number">{{ $taskStats['total_tasks'] }}</div>
                    <div class="label">Tasks</div>
                </div>
            </div>
        </div>
        
        <!-- Classes -->
        <div class="section">
            <div class="section-title">🏫 Classes Overview</div>
            <table>
                <thead>
                    <tr>
                        <th>Class Name</th>
                        <th>Code</th>
                        <th>Mentor</th>
                        <th>Participants</th>
                        <th>Sessions</th>
                        <th>Tasks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classes as $class)
                        <tr>
                            <td><strong>{{ $class->name }}</strong></td>
                            <td>{{ $class->code }}</td>
                            <td>{{ $class->mentor->name ?? 'Unassigned' }}</td>
                            <td>{{ $class->participants->count() }}</td>
                            <td>{{ $class->attendance_sessions_count }}</td>
                            <td>{{ $class->tasks_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Attendance -->
        <div class="section">
            <div class="section-title">📅 Attendance Statistics</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="number" style="color: #059669;">{{ $attendanceStats['present'] }}</div>
                    <div class="label">Present</div>
                </div>
                <div class="stat-card">
                    <div class="number" style="color: #D97706;">{{ $attendanceStats['permission'] }}</div>
                    <div class="label">Permission</div>
                </div>
                <div class="stat-card">
                    <div class="number" style="color: #DC2626;">{{ $attendanceStats['sick'] }}</div>
                    <div class="label">Sick</div>
                </div>
                <div class="stat-card">
                    <div class="number">{{ $attendanceStats['total'] }}</div>
                    <div class="label">Total Records</div>
                </div>
            </div>
            
            <p style="margin-bottom: 5px;"><strong>Attendance Rate:</strong></p>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $attendanceStats['percentage'] }}%">
                    {{ $attendanceStats['percentage'] }}%
                </div>
            </div>
        </div>
        
        <!-- Task Stats -->
        <div class="section">
            <div class="section-title">📝 Task Submission Statistics</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="number">{{ $taskStats['total_submissions'] }}</div>
                    <div class="label">Submissions</div>
                </div>
                <div class="stat-card">
                    <div class="number" style="color: #059669;">{{ $taskStats['graded'] }}</div>
                    <div class="label">Graded</div>
                </div>
                <div class="stat-card">
                    <div class="number" style="color: #D97706;">{{ $taskStats['pending_grades'] }}</div>
                    <div class="label">Pending</div>
                </div>
                <div class="stat-card">
                    <div class="number">{{ $taskStats['active_tasks'] }}</div>
                    <div class="label">Active Tasks</div>
                </div>
            </div>
            
            <p style="margin-bottom: 5px;"><strong>Submission Rate:</strong></p>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $taskStats['submission_percentage'] }}%">
                    {{ $taskStats['submission_percentage'] }}%
                </div>
            </div>
        </div>
        
        <!-- Grade Distribution -->
        <div class="section">
            <div class="section-title">🎓 Grade Distribution</div>
            <table>
                <thead>
                    <tr>
                        <th>Grade</th>
                        <th>Range</th>
                        <th>Count</th>
                        <th>Visual</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $maxGrade = max(array_values($gradeDistribution)) ?: 1;
                    @endphp
                    <tr>
                        <td><span class="badge badge-green">A</span></td>
                        <td>85 - 100</td>
                        <td>{{ $gradeDistribution['A'] }}</td>
                        <td>
                            <div style="background: #D1FAE5; height: 15px; width: {{ ($gradeDistribution['A'] / $maxGrade) * 100 }}%; border-radius: 4px;"></div>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-blue">B</span></td>
                        <td>70 - 84</td>
                        <td>{{ $gradeDistribution['B'] }}</td>
                        <td>
                            <div style="background: #DBEAFE; height: 15px; width: {{ ($gradeDistribution['B'] / $maxGrade) * 100 }}%; border-radius: 4px;"></div>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-yellow">C</span></td>
                        <td>55 - 69</td>
                        <td>{{ $gradeDistribution['C'] }}</td>
                        <td>
                            <div style="background: #FEF3C7; height: 15px; width: {{ ($gradeDistribution['C'] / $maxGrade) * 100 }}%; border-radius: 4px;"></div>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="badge" style="background: #FED7AA; color: #9A3412;">D</span></td>
                        <td>40 - 54</td>
                        <td>{{ $gradeDistribution['D'] }}</td>
                        <td>
                            <div style="background: #FED7AA; height: 15px; width: {{ ($gradeDistribution['D'] / $maxGrade) * 100 }}%; border-radius: 4px;"></div>
                        </td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-red">E</span></td>
                        <td>0 - 39</td>
                        <td>{{ $gradeDistribution['E'] }}</td>
                        <td>
                            <div style="background: #FEE2E2; height: 15px; width: {{ ($gradeDistribution['E'] / $maxGrade) * 100 }}%; border-radius: 4px;"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Recent Attendance Records -->
        @if($attendances->count() > 0)
        <div class="section">
            <div class="section-title">📋 Recent Attendance Records (Last 100)</div>
            <table>
                <thead>
                    <tr>
                        <th>Participant</th>
                        <th>Class</th>
                        <th>Session</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances->take(20) as $attendance)
                        <tr>
                            <td>{{ $attendance->participant->name }}</td>
                            <td>{{ $attendance->attendanceSession->classGroup->name }}</td>
                            <td>{{ $attendance->attendanceSession->title }}</td>
                            <td>
                                <span class="badge badge-{{ $attendance->status == 'present' ? 'green' : ($attendance->status == 'permission' ? 'yellow' : 'red') }}">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </td>
                            <td>{{ $attendance->submitted_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($attendances->count() > 20)
                <p style="text-align: center; color: #666; font-style: italic;">... and {{ $attendances->count() - 20 }} more records</p>
            @endif
        </div>
        @endif
        
        <div class="footer">
            <p>Generated using Attendance & Task Management System</p>
            <p>Develope by Gymnastiar</p>
            <p>{{ $summary['generated_at'] }}</p>
        </div>
    </div>
</body>
</html>
