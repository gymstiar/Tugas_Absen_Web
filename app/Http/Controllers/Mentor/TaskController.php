<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\TaskDocument;
use App\Models\ClassGroup;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Task::where('mentor_id', $user->id)
            ->with(['classGroup'])
            ->withCount(['submissions' => function($q) {
                $q->where('status', 'active');
            }]);

        if ($request->filled('class_group_id')) {
            $query->where('class_group_id', $request->class_group_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $tasks = $query->orderBy('due_date', 'desc')->paginate(10);
        
        // Use mentorOfClass for mentors (1:1 relationship)
        $mentorClass = $user->mentorOfClass;
        $classes = $mentorClass ? collect([$mentorClass]) : collect();

        return view('mentor.tasks.index', compact('tasks', 'classes'));
    }

    public function create()
    {
        $mentor = auth()->user();
        $class = $mentor->mentorOfClass;
        
        // Wrap single class in a collection for the view dropdown
        $classes = $class ? collect([$class]) : collect();
        
        if ($classes->isEmpty()) {
            return redirect()->route('mentor.tasks.index')
                ->with('error', 'You are not assigned to any class.');
        }
        
        return view('mentor.tasks.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_group_id' => ['required', 'exists:class_groups,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['required', 'date', 'after:today'],
            'allow_resubmission' => ['boolean'],
            'max_file_size' => ['nullable', 'integer', 'min:1', 'max:51200'], // Max 50MB
            'allowed_file_types' => ['nullable', 'string', 'max:255'],
            // Documents validation removed - handled manually below
        ]);

        // Verify mentor has access to this class
        $mentor = auth()->user();
        $mentorClass = $mentor->mentorOfClass;
        if (!$mentorClass || $mentorClass->id != $request->class_group_id) {
            abort(403, 'You are not assigned to this class.');
        }

        $task = Task::create([
            'class_group_id' => $request->class_group_id,
            'mentor_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => Carbon::parse($request->due_date),
            'is_active' => true,
            'allow_resubmission' => $request->boolean('allow_resubmission', false),
            'max_file_size' => $request->max_file_size ?? 10240,
            'allowed_file_types' => $request->allowed_file_types ?? 'pdf,docx,doc,zip,jpg,jpeg,png',
        ]);

        // Handle document uploads with error handling
        $uploadErrors = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                try {
                    if ($file->isValid()) {
                        $path = $file->store('task-documents/' . $task->id, 'public');
                        TaskDocument::create([
                            'task_id' => $task->id,
                            'file_name' => $file->getClientOriginalName(),
                            'file_path' => $path,
                            'file_type' => $file->getClientOriginalExtension(),
                            'file_size' => $file->getSize(),
                        ]);
                    } else {
                        $uploadErrors[] = $file->getClientOriginalName();
                    }
                } catch (\Exception $e) {
                    $uploadErrors[] = $file->getClientOriginalName();
                }
            }
        }

        ActivityLog::log('Created task: ' . $task->title, auth()->id(), [
            'task_id' => $task->id,
            'class_group_id' => $task->class_group_id,
        ]);

        return redirect()->route('mentor.tasks.index')
            ->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        if ($task->mentor_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $task->load(['classGroup', 'documents']);
        
        // Load active submissions with participant
        $submissions = $task->activeSubmissions()->with('participant', 'gradedBy')->get();
        
        $stats = $task->getSubmissionStats();
        $gradingStats = $task->getGradingStats();

        // Get participants who haven't submitted
        $submittedIds = $submissions->pluck('participant_id');
        $notSubmitted = $task->classGroup->users()
            ->where('role', 'participant')
            ->whereNotIn('users.id', $submittedIds)
            ->get();

        return view('mentor.tasks.show', compact('task', 'submissions', 'stats', 'gradingStats', 'notSubmitted'));
    }

    public function edit(Task $task)
    {
        if ($task->mentor_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $task->load('documents');
        $classes = auth()->user()->classGroups;
        return view('mentor.tasks.edit', compact('task', 'classes'));
    }

    public function update(Request $request, Task $task)
    {
        if ($task->mentor_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['required', 'date'],
            'is_active' => ['boolean'],
            'allow_resubmission' => ['boolean'],
            'max_file_size' => ['nullable', 'integer', 'min:1', 'max:51200'],
            'allowed_file_types' => ['nullable', 'string', 'max:255'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
        ]);

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => Carbon::parse($request->due_date),
            'is_active' => $request->boolean('is_active', true),
            'allow_resubmission' => $request->boolean('allow_resubmission', false),
            'max_file_size' => $request->max_file_size ?? $task->max_file_size,
            'allowed_file_types' => $request->allowed_file_types ?? $task->allowed_file_types,
        ]);

        // Handle new document uploads
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('task-documents/' . $task->id, 'public');
                TaskDocument::create([
                    'task_id' => $task->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        ActivityLog::log('Updated task: ' . $task->title, auth()->id(), [
            'task_id' => $task->id,
        ]);

        return redirect()->route('mentor.tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        if ($task->mentor_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        // Delete associated documents from storage
        foreach ($task->documents as $doc) {
            Storage::disk('public')->delete($doc->file_path);
        }

        // Delete submission files
        foreach ($task->submissions as $submission) {
            Storage::disk('public')->delete($submission->file_path);
        }

        $title = $task->title;
        $task->delete();

        ActivityLog::log('Deleted task: ' . $title, auth()->id());

        return redirect()->route('mentor.tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    public function destroyDocument(TaskDocument $document)
    {
        $task = $document->task;
        if ($task->mentor_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Document deleted successfully.');
    }

    public function toggleActive(Task $task)
    {
        if ($task->mentor_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $task->is_active = !$task->is_active;
        $task->save();

        $status = $task->is_active ? 'activated' : 'deactivated';

        ActivityLog::log("Task {$status}: " . $task->title, auth()->id(), [
            'task_id' => $task->id,
            'is_active' => $task->is_active,
        ]);

        return back()->with('success', "Task {$status} successfully.");
    }

    /**
     * Toggle resubmission allowance for a task
     */
    public function toggleResubmission(Task $task)
    {
        if ($task->mentor_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $task->allow_resubmission = !$task->allow_resubmission;
        $task->save();

        $status = $task->allow_resubmission ? 'enabled' : 'disabled';

        ActivityLog::log("Resubmission {$status} for task: " . $task->title, auth()->id(), [
            'task_id' => $task->id,
        ]);

        return back()->with('success', "Resubmission {$status} for this task.");
    }

    /**
     * Allow specific participant to resubmit (override)
     */
    public function allowParticipantResubmission(Request $request, Task $task)
    {
        if ($task->mentor_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'participant_id' => ['required', 'exists:users,id'],
        ]);

        // Mark participant's current submission as replaced
        $submission = $task->getParticipantSubmission($request->participant_id);
        if ($submission) {
            $submission->markAsReplaced();

            ActivityLog::log('Allowed participant resubmission for: ' . $task->title, auth()->id(), [
                'task_id' => $task->id,
                'participant_id' => $request->participant_id,
            ]);

            return back()->with('success', 'Participant can now resubmit. Previous submission has been marked as replaced.');
        }

        return back()->with('error', 'No submission found for this participant.');
    }

    /**
     * Grade a submission
     */
    public function grade(Request $request, TaskSubmission $submission)
    {
        // Verify mentor owns this task
        if ($submission->task->mentor_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'grade' => ['required', 'numeric', 'min:0', 'max:100'],
            'feedback' => ['nullable', 'string', 'max:1000'],
        ]);

        $submission->update([
            'grade' => $request->grade,
            'feedback' => $request->feedback,
            'graded_by' => auth()->id(),
            'graded_at' => now(),
        ]);

        ActivityLog::log('Graded submission', auth()->id(), [
            'submission_id' => $submission->id,
            'task_id' => $submission->task_id,
            'participant_id' => $submission->participant_id,
            'grade' => $request->grade,
        ]);

        return back()->with('success', 'Submission graded successfully.');
    }

    /**
     * Download a submission file
     */
    public function downloadSubmission(TaskSubmission $submission)
    {
        if ($submission->task->mentor_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        return Storage::disk('public')->download($submission->file_path);
    }
}
