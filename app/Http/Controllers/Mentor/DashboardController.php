<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\ClassGroup;
use App\Models\AttendanceSession;
use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get mentor's assigned class (1:1 relationship)
        $class = $user->mentorOfClass;
        
        // If mentor has no assigned class, show empty dashboard
        if (!$class) {
            return view('mentor.dashboard', [
                'stats' => [
                    'total_classes' => 0,
                    'active_sessions' => 0,
                    'total_sessions' => 0,
                    'total_tasks' => 0,
                    'pending_submissions' => 0,
                    'total_participants' => 0,
                ],
                'activeSessions' => collect(),
                'upcomingSessions' => collect(),
                'recentTasks' => collect(),
                'pendingSubmissions' => collect(),
                'participants' => collect(),
                'class' => null,
            ]);
        }
        
        $stats = [
            'total_classes' => 1, // Mentor has exactly one class
            'active_sessions' => AttendanceSession::where('class_group_id', $class->id)->where('is_open', true)->count(),
            'total_sessions' => AttendanceSession::where('class_group_id', $class->id)->count(),
            'total_tasks' => Task::where('class_group_id', $class->id)->count(),
            'pending_submissions' => TaskSubmission::whereHas('task', function($q) use ($class) {
                $q->where('class_group_id', $class->id);
            })->whereNull('grade')->count(),
            'total_participants' => $class->participants()->count(),
        ];

        $activeSessions = AttendanceSession::where('class_group_id', $class->id)
            ->where('is_open', true)
            ->with(['classGroup'])
            ->get();

        $upcomingSessions = AttendanceSession::where('class_group_id', $class->id)
            ->where('open_at', '>', now())
            ->orderBy('open_at')
            ->take(5)
            ->with(['classGroup'])
            ->get();

        $recentTasks = Task::where('class_group_id', $class->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->with(['classGroup'])
            ->get();

        $pendingSubmissions = TaskSubmission::whereHas('task', function($q) use ($class) {
            $q->where('class_group_id', $class->id);
        })->whereNull('grade')
          ->with(['task', 'participant'])
          ->orderBy('submitted_at', 'desc')
          ->take(5)
          ->get();

        // Get all participants in this class
        $participants = $class->participants()->orderBy('name')->get();

        return view('mentor.dashboard', compact('stats', 'activeSessions', 'upcomingSessions', 'recentTasks', 'pendingSubmissions', 'participants', 'class'));
    }
}
