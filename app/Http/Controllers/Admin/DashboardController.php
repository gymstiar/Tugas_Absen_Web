<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ClassGroup;
use App\Models\AttendanceSession;
use App\Models\Task;
use App\Models\Attendance;
use App\Models\TaskSubmission;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_mentors' => User::where('role', 'mentor')->count(),
            'total_participants' => User::where('role', 'participant')->count(),
            'total_classes' => ClassGroup::count(),
            'total_attendance_sessions' => AttendanceSession::count(),
            'active_sessions' => AttendanceSession::where('is_open', true)->count(),
            'total_tasks' => Task::count(),
            'total_attendances' => Attendance::count(),
            'total_submissions' => TaskSubmission::count(),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $recentClasses = ClassGroup::latest()->take(5)->get();
        $activeSessions = AttendanceSession::where('is_open', true)
            ->with(['classGroup', 'mentor'])
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentClasses', 'activeSessions'));
    }
}
