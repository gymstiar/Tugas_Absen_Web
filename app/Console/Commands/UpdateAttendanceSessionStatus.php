<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AttendanceSession;
use Carbon\Carbon;

class UpdateAttendanceSessionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-open and auto-close attendance sessions based on scheduled time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        
        // Auto-open sessions that should be open
        $toOpen = AttendanceSession::where('is_open', false)
            ->where('open_at', '<=', $now)
            ->where('close_at', '>', $now)
            ->get();

        foreach ($toOpen as $session) {
            $session->is_open = true;
            $session->save();
            $this->info("Opened session: {$session->title}");
        }

        // Auto-close sessions that should be closed
        $toClose = AttendanceSession::where('is_open', true)
            ->where('close_at', '<=', $now)
            ->get();

        foreach ($toClose as $session) {
            $session->is_open = false;
            $session->save();
            $this->info("Closed session: {$session->title}");
        }

        $this->info("Attendance session status update completed. Opened: {$toOpen->count()}, Closed: {$toClose->count()}");
        
        return Command::SUCCESS;
    }
}
