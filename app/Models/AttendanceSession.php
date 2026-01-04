<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_group_id',
        'mentor_id',
        'title',
        'description',
        'open_at',
        'close_at',
        'is_open',
    ];

    protected function casts(): array
    {
        return [
            'open_at' => 'datetime',
            'close_at' => 'datetime',
            'is_open' => 'boolean',
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
     * Get the mentor who created this session
     */
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    /**
     * Get attendances for this session
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Check if session is currently open based on time
     */
    public function isCurrentlyOpen(): bool
    {
        $now = Carbon::now();
        return $this->is_open && 
               $now->greaterThanOrEqualTo($this->open_at) && 
               $now->lessThan($this->close_at);
    }

    /**
     * Check if session should be open based on schedule
     */
    public function shouldBeOpen(): bool
    {
        $now = Carbon::now();
        return $now->greaterThanOrEqualTo($this->open_at) && 
               $now->lessThan($this->close_at);
    }

    /**
     * Check if session has ended
     */
    public function hasEnded(): bool
    {
        return Carbon::now()->greaterThanOrEqualTo($this->close_at);
    }

    /**
     * Check if participant has already submitted attendance
     */
    public function hasParticipantSubmitted(int $participantId): bool
    {
        return $this->attendances()->where('participant_id', $participantId)->exists();
    }

    /**
     * Get attendance count by status
     */
    public function getStatusCounts(): array
    {
        return [
            'present' => $this->attendances()->where('status', 'present')->count(),
            'permission' => $this->attendances()->where('status', 'permission')->count(),
            'sick' => $this->attendances()->where('status', 'sick')->count(),
            'total' => $this->attendances()->count(),
        ];
    }
}
