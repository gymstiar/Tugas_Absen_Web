<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'id_number',
        'password',
        'role',
        'avatar',
        'class_group_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is mentor
     */
    public function isMentor(): bool
    {
        return $this->role === 'mentor';
    }

    /**
     * Check if user is participant
     */
    public function isParticipant(): bool
    {
        return $this->role === 'participant';
    }

    /**
     * Get the class this user belongs to (for participants)
     */
    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class);
    }

    /**
     * Get the class this mentor is assigned to (for mentors)
     * Returns the ClassGroup where this user is the mentor
     */
    public function mentorOfClass(): HasOne
    {
        return $this->hasOne(ClassGroup::class, 'mentor_id');
    }

    /**
     * Get the mentor's class or participant's class
     */
    public function getAssignedClass()
    {
        if ($this->isMentor()) {
            return $this->mentorOfClass;
        }
        return $this->classGroup;
    }

    /**
     * Legacy: Get class groups the user belongs to through membership (for backward compatibility)
     */
    public function classGroups(): BelongsToMany
    {
        return $this->belongsToMany(ClassGroup::class, 'memberships')
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
     * Get attendance sessions created by mentor
     */
    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class, 'mentor_id');
    }

    /**
     * Get attendances submitted by participant
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'participant_id');
    }

    /**
     * Get tasks created by mentor
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'mentor_id');
    }

    /**
     * Get task submissions by participant
     */
    public function taskSubmissions(): HasMany
    {
        return $this->hasMany(TaskSubmission::class, 'participant_id');
    }

    /**
     * Get activity logs
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
