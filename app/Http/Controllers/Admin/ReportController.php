<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\ClassGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $classes = ClassGroup::with('mentor')->get();
        
        // Summary statistics
        $stats = [
            'total_participants' => User::where('role', 'participant')->count(),
            'total_mentors' => User::where('role', 'mentor')->count(),
            'total_classes' => ClassGroup::count(),
            'total_sessions' => AttendanceSession::count(),
            'total_tasks' => Task::count(),
            'total_submissions' => TaskSubmission::where('status', 'active')->count(),
            'graded_submissions' => TaskSubmission::where('status', 'active')->whereNotNull('grade')->count(),
        ];

        return view('admin.reports.index', compact('classes', 'stats'));
    }

    public function attendance(Request $request)
    {
        $request->validate([
            'class_group_id' => ['nullable', 'exists:class_groups,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $query = Attendance::with(['attendanceSession.classGroup', 'attendanceSession.mentor', 'participant']);

        if ($request->filled('class_group_id')) {
            $query->whereHas('attendanceSession', function($q) use ($request) {
                $q->where('class_group_id', $request->class_group_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->where('submitted_at', '>=', Carbon::parse($request->date_from)->startOfDay());
        }

        if ($request->filled('date_to')) {
            $query->where('submitted_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        $attendances = $query->orderBy('submitted_at', 'desc')->paginate(20);
        $classes = ClassGroup::with('mentor')->get();

        // Status breakdown
        $statusBreakdown = [
            'present' => Attendance::where('status', 'present')->count(),
            'permission' => Attendance::where('status', 'permission')->count(),
            'sick' => Attendance::where('status', 'sick')->count(),
        ];

        return view('admin.reports.attendance', compact('attendances', 'classes', 'statusBreakdown'));
    }

    /**
     * Export attendance report as CSV
     */
    public function exportAttendanceCsv(Request $request)
    {
        $query = Attendance::with(['attendanceSession.classGroup', 'participant']);

        if ($request->filled('class_group_id')) {
            $query->whereHas('attendanceSession', function($q) use ($request) {
                $q->where('class_group_id', $request->class_group_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->where('submitted_at', '>=', Carbon::parse($request->date_from)->startOfDay());
        }

        if ($request->filled('date_to')) {
            $query->where('submitted_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        $attendances = $query->orderBy('submitted_at', 'desc')->get();

        $csv = "No,Participant,Email,Class,Session,Status,Note,Submitted At\n";
        
        foreach ($attendances as $index => $attendance) {
            $csv .= implode(',', [
                $index + 1,
                '"' . str_replace('"', '""', $attendance->participant->name) . '"',
                $attendance->participant->email,
                '"' . str_replace('"', '""', $attendance->attendanceSession->classGroup->name) . '"',
                '"' . str_replace('"', '""', $attendance->attendanceSession->title) . '"',
                $attendance->status,
                '"' . str_replace('"', '""', $attendance->note ?? '') . '"',
                $attendance->submitted_at->format('Y-m-d H:i:s'),
            ]) . "\n";
        }

        $filename = 'attendance_report_' . now()->format('Y-m-d_His') . '.csv';
        
        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function tasks(Request $request)
    {
        $request->validate([
            'class_group_id' => ['nullable', 'exists:class_groups,id'],
        ]);

        $query = TaskSubmission::with(['task.classGroup', 'task.mentor', 'participant', 'gradedBy'])
            ->where('status', 'active');

        if ($request->filled('class_group_id')) {
            $query->whereHas('task', function($q) use ($request) {
                $q->where('class_group_id', $request->class_group_id);
            });
        }

        $submissions = $query->orderBy('submitted_at', 'desc')->paginate(20);
        $classes = ClassGroup::with('mentor')->get();

        return view('admin.reports.tasks', compact('submissions', 'classes'));
    }

    /**
     * Export task submissions as CSV
     */
    public function exportTasksCsv(Request $request)
    {
        $query = TaskSubmission::with(['task.classGroup', 'participant', 'gradedBy'])
            ->where('status', 'active');

        if ($request->filled('class_group_id')) {
            $query->whereHas('task', function($q) use ($request) {
                $q->where('class_group_id', $request->class_group_id);
            });
        }

        $submissions = $query->orderBy('submitted_at', 'desc')->get();

        $csv = "No,Participant,Email,Class,Task,Grade,Feedback,Graded By,Submitted At,Late\n";
        
        foreach ($submissions as $index => $submission) {
            $csv .= implode(',', [
                $index + 1,
                '"' . str_replace('"', '""', $submission->participant->name) . '"',
                $submission->participant->email,
                '"' . str_replace('"', '""', $submission->task->classGroup->name) . '"',
                '"' . str_replace('"', '""', $submission->task->title) . '"',
                $submission->grade ?? 'Not Graded',
                '"' . str_replace('"', '""', $submission->feedback ?? '') . '"',
                $submission->gradedBy ? $submission->gradedBy->name : '-',
                $submission->submitted_at->format('Y-m-d H:i:s'),
                $submission->isLate() ? 'Yes' : 'No',
            ]) . "\n";
        }

        $filename = 'task_submissions_report_' . now()->format('Y-m-d_His') . '.csv';
        
        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Show printable PDF report (browser print to PDF)
     * This allows users to print/save as PDF using browser's native print function
     */
    public function printableReport(Request $request)
    {
        // Gather all data
        $classes = ClassGroup::with(['mentor', 'participants'])->withCount(['attendanceSessions', 'tasks'])->get();
        
        // Attendance data
        $attendances = Attendance::with(['attendanceSession.classGroup', 'participant'])
            ->orderBy('submitted_at', 'desc')
            ->limit(100)
            ->get();

        $attendanceStats = [
            'present' => Attendance::where('status', 'present')->count(),
            'permission' => Attendance::where('status', 'permission')->count(),
            'sick' => Attendance::where('status', 'sick')->count(),
            'total' => Attendance::count(),
        ];

        // Calculate attendance percentage
        $totalPossibleAttendance = AttendanceSession::count() * User::where('role', 'participant')->count();
        $attendanceStats['percentage'] = $totalPossibleAttendance > 0 
            ? round(($attendanceStats['total'] / $totalPossibleAttendance) * 100, 1) 
            : 0;

        // Task submissions data
        $submissions = TaskSubmission::with(['task.classGroup', 'participant', 'gradedBy'])
            ->where('status', 'active')
            ->orderBy('submitted_at', 'desc')
            ->limit(100)
            ->get();

        $taskStats = [
            'total_tasks' => Task::count(),
            'active_tasks' => Task::where('is_active', true)->count(),
            'total_submissions' => TaskSubmission::where('status', 'active')->count(),
            'graded' => TaskSubmission::where('status', 'active')->whereNotNull('grade')->count(),
            'pending_grades' => TaskSubmission::where('status', 'active')->whereNull('grade')->count(),
        ];

        // Calculate submission percentage
        $totalPossibleSubmissions = Task::where('is_active', true)->count() * User::where('role', 'participant')->count();
        $taskStats['submission_percentage'] = $totalPossibleSubmissions > 0
            ? round(($taskStats['total_submissions'] / $totalPossibleSubmissions) * 100, 1)
            : 0;

        // Grade distribution
        $gradeDistribution = [
            'A' => TaskSubmission::where('status', 'active')->where('grade', '>=', 85)->count(),
            'B' => TaskSubmission::where('status', 'active')->whereBetween('grade', [70, 84.99])->count(),
            'C' => TaskSubmission::where('status', 'active')->whereBetween('grade', [55, 69.99])->count(),
            'D' => TaskSubmission::where('status', 'active')->whereBetween('grade', [40, 54.99])->count(),
            'E' => TaskSubmission::where('status', 'active')->where('grade', '<', 40)->count(),
        ];

        // Summary
        $summary = [
            'total_participants' => User::where('role', 'participant')->count(),
            'total_mentors' => User::where('role', 'mentor')->count(),
            'total_classes' => ClassGroup::count(),
            'generated_at' => now()->format('F d, Y H:i:s'),
        ];

        return view('admin.reports.printable', compact(
            'classes',
            'attendances',
            'attendanceStats',
            'submissions',
            'taskStats',
            'gradeDistribution',
            'summary'
        ));
    }

    /**
     * Export full report as CSV (combined)
     */
    public function exportFullCsv(Request $request)
    {
        // Summary section
        $csv = "=== FULL REPORT ===\n";
        $csv .= "Generated At," . now()->format('Y-m-d H:i:s') . "\n\n";
        
        // Stats
        $csv .= "=== SUMMARY ===\n";
        $csv .= "Total Participants," . User::where('role', 'participant')->count() . "\n";
        $csv .= "Total Mentors," . User::where('role', 'mentor')->count() . "\n";
        $csv .= "Total Classes," . ClassGroup::count() . "\n";
        $csv .= "Total Attendance Sessions," . AttendanceSession::count() . "\n";
        $csv .= "Total Tasks," . Task::count() . "\n";
        $csv .= "Total Submissions," . TaskSubmission::where('status', 'active')->count() . "\n\n";

        // Classes breakdown
        $csv .= "=== CLASSES ===\n";
        $csv .= "Class Name,Code,Mentor,Participants\n";
        $classes = ClassGroup::with(['mentor', 'participants'])->get();
        foreach ($classes as $class) {
            $csv .= implode(',', [
                '"' . str_replace('"', '""', $class->name) . '"',
                $class->code,
                $class->mentor ? '"' . str_replace('"', '""', $class->mentor->name) . '"' : 'Unassigned',
                $class->participants->count(),
            ]) . "\n";
        }
        $csv .= "\n";

        // Attendance summary
        $csv .= "=== ATTENDANCE BREAKDOWN ===\n";
        $csv .= "Status,Count\n";
        $csv .= "Present," . Attendance::where('status', 'present')->count() . "\n";
        $csv .= "Permission," . Attendance::where('status', 'permission')->count() . "\n";
        $csv .= "Sick," . Attendance::where('status', 'sick')->count() . "\n\n";

        // Grade distribution
        $csv .= "=== GRADE DISTRIBUTION ===\n";
        $csv .= "Grade,Count\n";
        $csv .= "A (85+)," . TaskSubmission::where('status', 'active')->where('grade', '>=', 85)->count() . "\n";
        $csv .= "B (70-84)," . TaskSubmission::where('status', 'active')->whereBetween('grade', [70, 84.99])->count() . "\n";
        $csv .= "C (55-69)," . TaskSubmission::where('status', 'active')->whereBetween('grade', [55, 69.99])->count() . "\n";
        $csv .= "D (40-54)," . TaskSubmission::where('status', 'active')->whereBetween('grade', [40, 54.99])->count() . "\n";
        $csv .= "E (<40)," . TaskSubmission::where('status', 'active')->where('grade', '<', 40)->count() . "\n";

        $filename = 'full_report_' . now()->format('Y-m-d_His') . '.csv';
        
        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
