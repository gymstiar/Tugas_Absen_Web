<?php

namespace App\Http\Controllers;

use App\Models\TaskDocument;
use App\Models\TaskSubmission;
use App\Models\Attendance;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Download or view a task document
     */
    public function taskDocument(TaskDocument $document, ?string $action = null)
    {
        $user = auth()->user();
        $task = $document->task;

        // Authorization: User must be the mentor of this task OR a participant of the class
        if ($user->role === 'mentor') {
            if ($task->mentor_id !== $user->id) {
                abort(403, 'Unauthorized access.');
            }
        } elseif ($user->role === 'participant') {
            if ($user->class_group_id !== $task->class_group_id) {
                abort(403, 'You are not a member of this class.');
            }
        } elseif ($user->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        if ($action === 'download') {
            return Storage::disk('public')->download($document->file_path, $document->file_name);
        }

        // Default: stream the file for viewing
        return response()->file(Storage::disk('public')->path($document->file_path));
    }

    /**
     * Download or view a task submission file
     */
    public function taskSubmission(TaskSubmission $submission, ?string $action = null)
    {
        $user = auth()->user();
        $task = $submission->task;

        // Authorization: User must be the participant who submitted OR the mentor of the task OR admin
        if ($user->role === 'mentor') {
            if ($task->mentor_id !== $user->id) {
                abort(403, 'Unauthorized access.');
            }
        } elseif ($user->role === 'participant') {
            if ($submission->participant_id !== $user->id) {
                abort(403, 'You can only view your own submissions.');
            }
        } elseif ($user->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        if (!$submission->file_path) {
            abort(404, 'No file found.');
        }

        if (!Storage::disk('public')->exists($submission->file_path)) {
            abort(404, 'File not found.');
        }

        // Get original filename from path
        $filename = basename($submission->file_path);

        if ($action === 'download') {
            return Storage::disk('public')->download($submission->file_path, $filename);
        }

        // Default: stream the file for viewing
        return response()->file(Storage::disk('public')->path($submission->file_path));
    }

    /**
     * Download or view an attendance proof file
     */
    public function attendanceProof(Attendance $attendance, ?string $action = null)
    {
        $user = auth()->user();
        $session = $attendance->attendanceSession;

        // Authorization: User must be the participant who submitted OR the mentor of the session OR admin
        if ($user->role === 'mentor') {
            if ($session->mentor_id !== $user->id) {
                abort(403, 'Unauthorized access.');
            }
        } elseif ($user->role === 'participant') {
            if ($attendance->participant_id !== $user->id) {
                abort(403, 'You can only view your own attendance proof.');
            }
        } elseif ($user->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        if (!$attendance->proof_file_path) {
            abort(404, 'No proof file found.');
        }

        if (!Storage::disk('public')->exists($attendance->proof_file_path)) {
            abort(404, 'File not found.');
        }

        $filename = basename($attendance->proof_file_path);

        if ($action === 'download') {
            return Storage::disk('public')->download($attendance->proof_file_path, $filename);
        }

        // Default: stream the file for viewing
        return response()->file(Storage::disk('public')->path($attendance->proof_file_path));
    }
}
