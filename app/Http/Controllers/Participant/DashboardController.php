<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get participant's assigned class (1:1 relationship via class_group_id)
        $class = $user->classGroup;
        
        // If participant has no assigned class, show empty dashboard
        if (!$class) {
            return view('participant.dashboard', [
                'stats' => [
                    'total_classes' => 0,
                    'total_attendances' => 0,
                    'total_submissions' => 0,
                    'graded_submissions' => 0,
                    'pending_tasks' => 0,
                ],
                'activeSessions' => collect(),
                'upcomingSessions' => collect(),
                'activeTasks' => collect(),
                'recentSubmissions' => collect(),
                'class' => null,
            ]);
        }
        
        // Active attendance sessions for participant's class
        $activeSessions = AttendanceSession::where('class_group_id', $class->id)
            ->where('is_open', true)
            ->with(['classGroup', 'mentor'])
            ->get()
            ->map(function($session) use ($user) {
                $session->has_submitted = $session->hasParticipantSubmitted($user->id);
                return $session;
            });

        // Upcoming attendance sessions
        $upcomingSessions = AttendanceSession::where('class_group_id', $class->id)
            ->where('open_at', '>', now())
            ->orderBy('open_at')
            ->take(5)
            ->with(['classGroup'])
            ->get();

        // Active tasks with deadlines
        $activeTasks = Task::where('class_group_id', $class->id)
            ->where('is_active', true)
            ->orderBy('due_date')
            ->take(10)
            ->with(['classGroup', 'mentor'])
            ->get()
            ->map(function($task) use ($user) {
                $task->has_submitted = $task->hasParticipantSubmitted($user->id);
                $task->submission = $task->getParticipantSubmission($user->id);
                return $task;
            });

        // Recent submissions with grades (only active)
        $recentSubmissions = TaskSubmission::where('participant_id', $user->id)
            ->where('status', 'active')
            ->orderBy('submitted_at', 'desc')
            ->take(5)
            ->with(['task.classGroup', 'gradedBy'])
            ->get();

        // Stats
        $activeSubmissions = $user->taskSubmissions()->where('status', 'active');
        $stats = [
            'total_classes' => 1, // Participant belongs to exactly one class
            'total_attendances' => $user->attendances()->count(),
            'total_submissions' => (clone $activeSubmissions)->count(),
            'graded_submissions' => (clone $activeSubmissions)->whereNotNull('grade')->count(),
            'pending_tasks' => Task::where('class_group_id', $class->id)
                ->where('is_active', true)
                ->whereDoesntHave('submissions', function($q) use ($user) {
                    $q->where('participant_id', $user->id)->where('status', 'active');
                })->count(),
        ];

        return view('participant.dashboard', compact('activeSessions', 'upcomingSessions', 'activeTasks', 'recentSubmissions', 'stats', 'class'));
    }
}
