<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\ClassGroup;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class AttendanceSessionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get mentor's assigned class (1:1 relationship)
        $class = $user->mentorOfClass;
        
        if (!$class) {
            return view('mentor.attendance.index', [
                'sessions' => new LengthAwarePaginator([], 0, 10),
                'class' => null,
            ]);
        }
        
        $query = AttendanceSession::where('class_group_id', $class->id)
            ->with(['classGroup']);

        if ($request->filled('status')) {
            if ($request->status === 'open') {
                $query->where('is_open', true);
            } elseif ($request->status === 'closed') {
                $query->where('is_open', false);
            }
        }

        $sessions = $query->orderBy('open_at', 'desc')->paginate(10);

        return view('mentor.attendance.index', compact('sessions', 'class'));
    }

    public function create()
    {
        $class = auth()->user()->mentorOfClass;
        
        if (!$class) {
            return redirect()->route('mentor.attendance.index')
                ->with('error', 'You are not assigned to any class.');
        }
        
        return view('mentor.attendance.create', compact('class'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $class = $user->mentorOfClass;
        
        if (!$class) {
            return redirect()->route('mentor.attendance.index')
                ->with('error', 'You are not assigned to any class.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'open_at' => ['required', 'date'],
            'close_at' => ['required', 'date', 'after:open_at'],
        ]);

        $session = AttendanceSession::create([
            'class_group_id' => $class->id,
            'mentor_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'open_at' => Carbon::parse($request->open_at),
            'close_at' => Carbon::parse($request->close_at),
            'is_open' => false,
        ]);

        ActivityLog::log('Created attendance session: ' . $session->title, $user->id, [
            'session_id' => $session->id,
            'class_group_id' => $class->id,
        ]);

        return redirect()->route('mentor.attendance.index')
            ->with('success', 'Attendance session created successfully.');
    }

    public function show(AttendanceSession $attendance)
    {
        $user = auth()->user();
        $class = $user->mentorOfClass;
        
        // Verify mentor owns this class
        if (!$class || $attendance->class_group_id !== $class->id) {
            abort(403, 'Unauthorized access.');
        }

        $attendance->load(['classGroup', 'attendances.participant']);
        $statusCounts = $attendance->getStatusCounts();

        // Get participants who haven't submitted (from the class)
        $submittedIds = $attendance->attendances->pluck('participant_id');
        $notSubmitted = $class->participants()
            ->whereNotIn('users.id', $submittedIds)
            ->get();

        return view('mentor.attendance.show', compact('attendance', 'statusCounts', 'notSubmitted'));
    }

    public function edit(AttendanceSession $attendance)
    {
        $user = auth()->user();
        $class = $user->mentorOfClass;
        
        if (!$class || $attendance->class_group_id !== $class->id) {
            abort(403, 'Unauthorized access.');
        }

        return view('mentor.attendance.edit', compact('attendance', 'class'));
    }

    public function update(Request $request, AttendanceSession $attendance)
    {
        $user = auth()->user();
        $class = $user->mentorOfClass;
        
        if (!$class || $attendance->class_group_id !== $class->id) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'open_at' => ['required', 'date'],
            'close_at' => ['required', 'date', 'after:open_at'],
        ]);

        $attendance->update([
            'title' => $request->title,
            'description' => $request->description,
            'open_at' => Carbon::parse($request->open_at),
            'close_at' => Carbon::parse($request->close_at),
        ]);

        ActivityLog::log('Updated attendance session: ' . $attendance->title, $user->id, [
            'session_id' => $attendance->id,
        ]);

        return redirect()->route('mentor.attendance.index')
            ->with('success', 'Attendance session updated successfully.');
    }

    public function destroy(AttendanceSession $attendance)
    {
        $user = auth()->user();
        $class = $user->mentorOfClass;
        
        if (!$class || $attendance->class_group_id !== $class->id) {
            abort(403, 'Unauthorized access.');
        }

        $title = $attendance->title;
        $attendance->delete();

        ActivityLog::log('Deleted attendance session: ' . $title, $user->id);

        return redirect()->route('mentor.attendance.index')
            ->with('success', 'Attendance session deleted successfully.');
    }

    public function toggle(AttendanceSession $attendance)
    {
        $user = auth()->user();
        $class = $user->mentorOfClass;
        
        if (!$class || $attendance->class_group_id !== $class->id) {
            abort(403, 'Unauthorized access.');
        }

        $attendance->is_open = !$attendance->is_open;
        $attendance->save();

        $status = $attendance->is_open ? 'opened' : 'closed';

        ActivityLog::log("Manually {$status} attendance session: " . $attendance->title, $user->id, [
            'session_id' => $attendance->id,
            'is_open' => $attendance->is_open,
        ]);

        return back()->with('success', "Attendance session {$status} successfully.");
    }

    public function markAttendance(Request $request, AttendanceSession $attendance)
    {
        $user = auth()->user();
        $class = $user->mentorOfClass;
        
        if (!$class || $attendance->class_group_id !== $class->id) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'participant_id' => ['required', 'exists:users,id'],
            'status' => ['required', 'in:present,permission,sick'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        // Check if already submitted
        if ($attendance->hasParticipantSubmitted($request->participant_id)) {
            return back()->with('error', 'This participant has already submitted attendance.');
        }

        Attendance::create([
            'attendance_session_id' => $attendance->id,
            'participant_id' => $request->participant_id,
            'status' => $request->status,
            'note' => $request->note ?? 'Marked by mentor',
            'submitted_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        ActivityLog::log('Mentor marked attendance for participant', $user->id, [
            'session_id' => $attendance->id,
            'participant_id' => $request->participant_id,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Attendance marked successfully.');
    }
}
