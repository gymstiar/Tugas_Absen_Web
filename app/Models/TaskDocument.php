<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get human-readable file size
     */
    public function getHumanFileSizeAttribute()
    {
        $size = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . ' ' . $units[$i];
    }

    /**
     * Get file icon based on type
     */
    public function getFileIconAttribute()
    {
        return match($this->file_type) {
            'pdf' => '📄',
            'doc', 'docx' => '📝',
            'jpg', 'jpeg', 'png' => '🖼️',
            default => '📎',
        };
    }
}
