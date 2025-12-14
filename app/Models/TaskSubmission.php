<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class TaskSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'participant_id',
        'file_path',
        'comment',
        'submitted_at',
        'grade',
        'feedback',
        'status',
        'graded_by',
        'graded_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
            'grade' => 'decimal:2',
        ];
    }

    /**
     * Scope for active (non-replaced) submissions
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for replaced submissions
     */
    public function scopeReplaced(Builder $query): Builder
    {
        return $query->where('status', 'replaced');
    }

    /**
     * Get the task
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the participant
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'participant_id');
    }

    /**
     * Get the mentor who graded this submission
     */
    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    /**
     * Check if submission is graded
     */
    public function isGraded(): bool
    {
        return $this->grade !== null;
    }

    /**
     * Check if this submission is active (not replaced)
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if this submission was replaced
     */
    public function isReplaced(): bool
    {
        return $this->status === 'replaced';
    }

    /**
     * Get status label for display
     */
    public function getStatusLabel(): string
    {
        if ($this->isReplaced()) {
            return 'Replaced';
        }
        
        if (!$this->isGraded()) {
            return 'Not Graded Yet';
        }

        return 'Graded';
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColor(): string
    {
        if ($this->isReplaced()) {
            return 'gray';
        }
        
        if (!$this->isGraded()) {
            return 'yellow';
        }

        return 'green';
    }

    /**
     * Get grade display with color
     */
    public function getGradeColor(): string
    {
        if (!$this->isGraded()) {
            return 'gray';
        }
        
        return match(true) {
            $this->grade >= 85 => 'green',
            $this->grade >= 70 => 'blue',
            $this->grade >= 55 => 'yellow',
            default => 'red',
        };
    }

    /**
     * Get grade letter (A, B, C, D, E)
     */
    public function getGradeLetter(): ?string
    {
        if (!$this->isGraded()) {
            return null;
        }

        return match(true) {
            $this->grade >= 85 => 'A',
            $this->grade >= 70 => 'B',
            $this->grade >= 55 => 'C',
            $this->grade >= 40 => 'D',
            default => 'E',
        };
    }

    /**
     * Check if submission was late
     */
    public function isLate(): bool
    {
        return $this->submitted_at->greaterThan($this->task->due_date);
    }

    /**
     * Mark this submission as replaced
     */
    public function markAsReplaced(): void
    {
        $this->update(['status' => 'replaced']);
    }
}
