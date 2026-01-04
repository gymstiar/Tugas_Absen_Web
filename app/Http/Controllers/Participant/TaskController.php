<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Participants are assigned to class via class_group_id directly
        if (!$user->class_group_id) {
            return view('participant.tasks.index', [
                'tasks' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
                'classes' => collect(),
            ]);
        }

        $query = Task::where('class_group_id', $user->class_group_id)
            ->where('is_active', true)
            ->with(['classGroup', 'mentor']);

        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->whereDoesntHave('submissions', function($q) use ($user) {
                    $q->where('participant_id', $user->id)->where('status', 'active');
                });
            } elseif ($request->status === 'submitted') {
                $query->whereHas('submissions', function($q) use ($user) {
                    $q->where('participant_id', $user->id)->where('status', 'active');
                });
            }
        }

        $tasks = $query->orderBy('due_date', 'asc')->paginate(10);

        // Add submission info
        $tasks->getCollection()->transform(function($task) use ($user) {
            $task->submission = $task->getParticipantSubmission($user->id);
            return $task;
        });

        // Wrap class in collection for view compatibility
        $classes = $user->classGroup ? collect([$user->classGroup]) : collect();

        return view('participant.tasks.index', compact('tasks', 'classes'));
    }

    public function show(Task $task)
    {
        $user = auth()->user();
        
        // Verify participant belongs to this class (via class_group_id)
        if ($user->class_group_id !== $task->class_group_id) {
            abort(403, 'You are not a member of this class.');
        }

        $task->load(['classGroup', 'mentor', 'documents']);
        $submission = $task->getParticipantSubmission($user->id);
        $submissionHistory = $task->getParticipantSubmissionHistory($user->id);
        $canSubmit = $task->canSubmit($user->id);

        return view('participant.tasks.show', compact('task', 'submission', 'submissionHistory', 'canSubmit'));
    }

    public function submit(Request $request, Task $task)
    {
        $user = auth()->user();

        // Verify participant belongs to this class (via class_group_id)
        if ($user->class_group_id !== $task->class_group_id) {
            abort(403, 'You are not a member of this class.');
        }

        // Check if task is active
        if (!$task->is_active) {
            return back()->with('error', 'This task is no longer accepting submissions.');
        }

        // Check if can submit (handles resubmission logic)
        if (!$task->canSubmit($user->id)) {
            return back()->with('error', 'You have already submitted this task. Resubmission is not allowed for this task.');
        }

        // Build validation rules dynamically
        $allowedTypes = $task->getAllowedFileTypesArray();
        $maxSize = $task->max_file_size ?? 10240;

        $request->validate([
            'file' => [
                'required',
                'file',
                'max:' . $maxSize,
                File::types($allowedTypes),
            ],
            'comment' => ['nullable', 'string', 'max:1000'],
        ], [
            'file.max' => "The file must not be larger than {$task->getMaxFileSizeMB()} MB.",
            'file.mimetypes' => "The file must be one of the following types: " . implode(', ', $allowedTypes),
        ]);

        // Check for existing submission
        $existingSubmission = $task->getParticipantSubmission($user->id);

        // Store the file
        $filePath = $request->file('file')->store('task_submissions/' . $task->id, 'public');

        // If resubmitting, delete old file and update existing record
        if ($existingSubmission) {
            // Delete old file if it exists
            if ($existingSubmission->file_path && Storage::disk('public')->exists($existingSubmission->file_path)) {
                Storage::disk('public')->delete($existingSubmission->file_path);
            }

            // Update the existing submission
            $existingSubmission->update([
                'file_path' => $filePath,
                'comment' => $request->comment,
                'submitted_at' => now(),
                'grade' => null,       // Reset grade on resubmission
                'feedback' => null,    // Reset feedback on resubmission
                'graded_at' => null,
                'graded_by' => null,
            ]);

            ActivityLog::log('Resubmitted task: ' . $task->title, $user->id, [
                'task_id' => $task->id,
                'submission_id' => $existingSubmission->id,
            ]);
        } else {
            // Create new submission
            TaskSubmission::create([
                'task_id' => $task->id,
                'participant_id' => $user->id,
                'file_path' => $filePath,
                'comment' => $request->comment,
                'submitted_at' => now(),
                'status' => 'active',
            ]);

            ActivityLog::log('Submitted task: ' . $task->title, $user->id, [
                'task_id' => $task->id,
                'is_late' => $task->isPastDue(),
            ]);
        }

        $message = $existingSubmission 
            ? 'Task resubmitted successfully. Your previous submission has been updated.'
            : 'Task submitted successfully.';
        
        if ($task->isPastDue()) {
            $message .= ' (Submitted late)';
        }

        return redirect()->route('participant.tasks.index')->with('success', $message);
    }

    public function downloadSubmission(TaskSubmission $submission)
    {
        $user = auth()->user();

        // Policy check
        if ($submission->participant_id !== $user->id && $submission->task->mentor_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        if (!$submission->file_path) {
            abort(404, 'No file found.');
        }

        return Storage::disk('public')->download($submission->file_path);
    }
}
