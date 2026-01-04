<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ClassGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'mentor_id',
    ];

    /**
     * Get the assigned mentor for this class
     */
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    /**
     * Get participants in this class (users with class_group_id = this class)
     */
    public function participants(): HasMany
    {
        return $this->hasMany(User::class, 'class_group_id')
                    ->where('role', 'participant');
    }

    /**
     * Get all users assigned to this class (participants)
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'class_group_id');
    }

    /**
     * Legacy: Get users in this class group through membership (for backward compatibility)
     */
    public function membershipUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'memberships')
                    ->withPivot('role_in_class')
                    ->withTimestamps();
    }

    /**
     * Get memberships
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    /**
     * Get attendance sessions for this class group
     */
    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class);
    }

    /**
     * Get tasks for this class group
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get participant count
     */
    public function getParticipantCountAttribute(): int
    {
        return $this->participants()->count();
    }
}
