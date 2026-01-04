<?php

namespace App\Policies;

use App\Models\TaskSubmission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskSubmissionPolicy
{
    /**
     * Determine if the user can view the submission
     */
    public function view(User $user, TaskSubmission $submission): bool
    {
        // Participant can view their own submission
        if ($submission->participant_id === $user->id) {
            return true;
        }

        // Mentor who owns the task can view
        if ($submission->task->mentor_id === $user->id) {
            return true;
        }

        // Admin can view all
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can grade the submission
     */
    public function grade(User $user, TaskSubmission $submission): bool
    {
        // Only the mentor who created the task can grade
        return $submission->task->mentor_id === $user->id;
    }

    /**
     * Determine if the user can delete the submission
     */
    public function delete(User $user, TaskSubmission $submission): bool
    {
        // Participant can delete their own active submission
        if ($submission->participant_id === $user->id && $submission->isActive()) {
            return true;
        }

        // Mentor who owns the task can delete
        if ($submission->task->mentor_id === $user->id) {
            return true;
        }

        // Admin can delete all
        return $user->isAdmin();
    }

    /**
     * Determine if the user can download the submission file
     */
    public function download(User $user, TaskSubmission $submission): bool
    {
        return $this->view($user, $submission);
    }
}
