<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_group_id',
        'mentor_id',
        'title',
        'description',
        'due_date',
        'is_active',
        'allow_resubmission',
        'max_file_size',
        'allowed_file_types',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'is_active' => 'boolean',
            'allow_resubmission' => 'boolean',
            'max_file_size' => 'integer',
        ];
    }

    /**
     * Get the class group
     */
    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class);
    }

    /**
     * Get the mentor who created this task
     */
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    /**
     * Get submissions for this task
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(TaskSubmission::class);
    }

    /**
     * Get only active (non-replaced) submissions
     */
    public function activeSubmissions(): HasMany
    {
        return $this->hasMany(TaskSubmission::class)->where('status', 'active');
    }

    /**
     * Get documents uploaded by mentor for this task
     */
    public function documents(): HasMany
    {
        return $this->hasMany(TaskDocument::class);
    }

    /**
     * Check if task is past due
     */
    public function isPastDue(): bool
    {
        return Carbon::now()->greaterThan($this->due_date);
    }

    /**
     * Check if participant can submit to this task
     */
    public function canSubmit(int $participantId): bool
    {
        // Task must be active
        if (!$this->is_active) {
            return false;
        }

        // Check if already submitted
        $hasSubmitted = $this->hasParticipantSubmitted($participantId);
        
        // If not submitted, can submit
        if (!$hasSubmitted) {
            return true;
        }

        // If submitted and resubmission allowed, can resubmit
        return $this->allow_resubmission;
    }

    /**
     * Check if submission is allowed based on deadline
     */
    public function isSubmissionAllowed(): bool
    {
        return $this->is_active;
    }

    /**
     * Get time remaining until due date
     */
    public function getTimeRemaining(): string
    {
        if ($this->isPastDue()) {
            return 'Overdue';
        }
        return $this->due_date->diffForHumans();
    }

    /**
     * Check if participant has already submitted (active submission)
     */
    public function hasParticipantSubmitted(int $participantId): bool
    {
        return $this->activeSubmissions()->where('participant_id', $participantId)->exists();
    }

    /**
     * Get participant's active submission
     */
    public function getParticipantSubmission(int $participantId): ?TaskSubmission
    {
        return $this->activeSubmissions()->where('participant_id', $participantId)->first();
    }

    /**
     * Get all participant submissions (including replaced)
     */
    public function getParticipantSubmissionHistory(int $participantId)
    {
        return $this->submissions()
            ->where('participant_id', $participantId)
            ->orderBy('submitted_at', 'desc')
            ->get();
    }

    /**
     * Get allowed file types as array
     */
    public function getAllowedFileTypesArray(): array
    {
        return array_map('trim', explode(',', $this->allowed_file_types ?? 'pdf,docx,doc,zip,jpg,jpeg,png'));
    }

    /**
     * Get max file size in MB for display
     */
    public function getMaxFileSizeMB(): float
    {
        return round(($this->max_file_size ?? 10240) / 1024, 1);
    }

    /**
     * Get submission statistics
     */
    public function getSubmissionStats(): array
    {
        $total = $this->activeSubmissions()->count();
        $graded = $this->activeSubmissions()->whereNotNull('grade')->count();
        
        return [
            'total' => $total,
            'graded' => $graded,
            'pending' => $total - $graded,
        ];
    }

    /**
     * Get grading statistics
     */
    public function getGradingStats(): array
    {
        $submissions = $this->activeSubmissions()->whereNotNull('grade')->get();
        
        if ($submissions->isEmpty()) {
            return [
                'average' => null,
                'highest' => null,
                'lowest' => null,
                'count' => 0,
            ];
        }

        return [
            'average' => round($submissions->avg('grade'), 2),
            'highest' => $submissions->max('grade'),
            'lowest' => $submissions->min('grade'),
            'count' => $submissions->count(),
        ];
    }
}
