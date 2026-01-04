<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get participant's assigned class (1:1 relationship)
        $class = $user->classGroup;
        
        if (!$class) {
            return view('participant.attendance.index', [
                'activeSessions' => collect(),
                'pastSessions' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
                'class' => null,
            ]);
        }

        // Get active and upcoming sessions for participant's class
        $activeSessions = AttendanceSession::where('class_group_id', $class->id)
            ->where('is_open', true)
            ->with(['classGroup', 'mentor'])
            ->get()
            ->map(function($session) use ($user) {
                $session->has_submitted = $session->hasParticipantSubmitted($user->id);
                $session->submission = $session->attendances()->where('participant_id', $user->id)->first();
                return $session;
            });

        // Past sessions with participant's attendance
        $pastSessions = AttendanceSession::where('class_group_id', $class->id)
            ->where(function($q) {
                $q->where('close_at', '<', now())
                  ->orWhere('is_open', false);
            })
            ->with(['classGroup', 'mentor'])
            ->orderBy('close_at', 'desc')
            ->paginate(10);

        // Add participant's submission info
        $pastSessions->getCollection()->transform(function($session) use ($user) {
            $session->submission = $session->attendances()->where('participant_id', $user->id)->first();
            return $session;
        });

        return view('participant.attendance.index', compact('activeSessions', 'pastSessions', 'class'));
    }

    public function show(AttendanceSession $session)
    {
        $user = auth()->user();
        $class = $user->classGroup;
        
        // Verify participant belongs to this class
        if (!$class || $session->class_group_id !== $class->id) {
            abort(403, 'You are not a member of this class.');
        }

        $session->load(['classGroup', 'mentor']);
        $submission = $session->attendances()->where('participant_id', $user->id)->first();

        return view('participant.attendance.show', compact('session', 'submission'));
    }

    public function submit(Request $request, AttendanceSession $session)
    {
        $user = auth()->user();
        $class = $user->classGroup;

        // Verify participant belongs to this class
        if (!$class || $session->class_group_id !== $class->id) {
            abort(403, 'You are not a member of this class.');
        }

        // Check if session is open
        if (!$session->isCurrentlyOpen()) {
            return back()->with('error', 'This attendance session is not currently open.');
        }

        // Check if already submitted
        if ($session->hasParticipantSubmitted($user->id)) {
            return back()->with('error', 'You have already submitted attendance for this session.');
        }

        $request->validate([
            'status' => ['required', 'in:present,permission,sick'],
            'note' => ['nullable', 'string', 'max:500'],
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:3072'],
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $proofPath = $request->file('proof_file')->store('attendance_proofs/' . $session->id, 'public');
        }

        Attendance::create([
            'attendance_session_id' => $session->id,
            'participant_id' => $user->id,
            'status' => $request->status,
            'note' => $request->note,
            'proof_file_path' => $proofPath,
            'submitted_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        ActivityLog::log('Submitted attendance', $user->id, [
            'session_id' => $session->id,
            'status' => $request->status,
        ]);

        return redirect()->route('participant.attendance.index')
            ->with('success', 'Attendance submitted successfully.');
    }

    public function downloadProof(Attendance $attendance)
    {
        $user = auth()->user();

        // Only allow participant to download their own proof
        if ($attendance->participant_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        if (!$attendance->proof_file_path) {
            abort(404, 'No proof file found.');
        }

        return Storage::disk('public')->download($attendance->proof_file_path);
    }
}
