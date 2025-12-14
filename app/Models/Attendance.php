<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_session_id',
        'participant_id',
        'status',
        'note',
        'proof_file_path',
        'submitted_at',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    /**
     * Get the attendance session
     */
    public function attendanceSession(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class);
    }

    /**
     * Get the participant
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'participant_id');
    }

    /**
     * Get status badge color
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'present' => 'green',
            'permission' => 'yellow',
            'sick' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get status display label
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'present' => 'Hadir',
            'permission' => 'Izin',
            'sick' => 'Sakit',
            default => 'Unknown',
        };
    }
}
