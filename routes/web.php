<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ClassGroupController as AdminClassGroupController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;

// Mentor Controllers
use App\Http\Controllers\Mentor\DashboardController as MentorDashboardController;
use App\Http\Controllers\Mentor\AttendanceSessionController as MentorAttendanceController;
use App\Http\Controllers\Mentor\TaskController as MentorTaskController;

// Participant Controllers
use App\Http\Controllers\Participant\DashboardController as ParticipantDashboardController;
use App\Http\Controllers\Participant\AttendanceController as ParticipantAttendanceController;
use App\Http\Controllers\Participant\TaskController as ParticipantTaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Clear Cache Route (for hosting without terminal access)
// Access via: https://yourdomain.com/clear-cache?key=YOUR_SECRET_KEY
Route::get('/clear-cache', function () {
    $secretKey = request('key');
    
    // Change this secret key to something unique!
    if ($secretKey !== 'Gm8Ks3Lp7Nx2Qw4R') {
        abort(404);
    }
    
    // Clear all caches
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    
    return response()->json([
        'success' => true,
        'message' => 'All caches cleared successfully!',
        'cleared' => ['views', 'routes', 'config', 'cache']
    ]);
});

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Main Dashboard - redirects based on role
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Secure File Routes (works on all hosting)
Route::middleware('auth')->prefix('files')->name('files.')->group(function () {
    // Task Documents
    Route::get('/document/{document}/{action?}', [FileController::class, 'taskDocument'])->name('document');
    
    // Task Submissions
    Route::get('/submission/{submission}/{action?}', [FileController::class, 'taskSubmission'])->name('submission');
    
    // Attendance Proofs
    Route::get('/attendance-proof/{attendance}/{action?}', [FileController::class, 'attendanceProof'])->name('attendance-proof');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::get('users/import', [\App\Http\Controllers\Admin\UserImportController::class, 'showImportForm'])->name('users.import');
    Route::post('users/import', [\App\Http\Controllers\Admin\UserImportController::class, 'import']);
    Route::get('users/import/template', [\App\Http\Controllers\Admin\UserImportController::class, 'downloadTemplate'])->name('users.import.template');
    Route::resource('users', AdminUserController::class)->except(['show']);
    
    // Class Group Management
    Route::resource('classes', AdminClassGroupController::class);
    Route::post('classes/{class}/members', [AdminClassGroupController::class, 'addMember'])->name('classes.addMember');
    Route::delete('classes/{class}/members/{user}', [AdminClassGroupController::class, 'removeMember'])->name('classes.removeMember');
    
    // Reports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/attendance', [AdminReportController::class, 'attendance'])->name('reports.attendance');
    Route::get('/reports/attendance/export-csv', [AdminReportController::class, 'exportAttendanceCsv'])->name('reports.attendance.exportCsv');
    Route::get('/reports/tasks', [AdminReportController::class, 'tasks'])->name('reports.tasks');
    Route::get('/reports/tasks/export-csv', [AdminReportController::class, 'exportTasksCsv'])->name('reports.tasks.exportCsv');
    Route::get('/reports/printable', [AdminReportController::class, 'printableReport'])->name('reports.printable');
    Route::get('/reports/export-full-csv', [AdminReportController::class, 'exportFullCsv'])->name('reports.exportFullCsv');
});

/*
|--------------------------------------------------------------------------
| Mentor Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:mentor'])->prefix('mentor')->name('mentor.')->group(function () {
    Route::get('/dashboard', [MentorDashboardController::class, 'index'])->name('dashboard');
    
    // Attendance Sessions
    Route::resource('attendance', MentorAttendanceController::class);
    Route::post('attendance/{attendance}/toggle', [MentorAttendanceController::class, 'toggle'])->name('attendance.toggle');
    Route::post('attendance/{attendance}/mark', [MentorAttendanceController::class, 'markAttendance'])->name('attendance.mark');
    
    // Tasks
    Route::resource('tasks', MentorTaskController::class);
    Route::post('tasks/{task}/toggle', [MentorTaskController::class, 'toggleActive'])->name('tasks.toggle');
    Route::post('tasks/{task}/toggle-resubmission', [MentorTaskController::class, 'toggleResubmission'])->name('tasks.toggleResubmission');
    Route::post('tasks/{task}/allow-resubmission', [MentorTaskController::class, 'allowParticipantResubmission'])->name('tasks.allowParticipantResubmission');
    Route::delete('documents/{document}', [MentorTaskController::class, 'destroyDocument'])->name('documents.destroy');
    Route::post('submissions/{submission}/grade', [MentorTaskController::class, 'grade'])->name('submissions.grade');
    Route::get('submissions/{submission}/download', [MentorTaskController::class, 'downloadSubmission'])->name('submissions.download');
});

/*
|--------------------------------------------------------------------------
| Participant Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:participant'])->prefix('participant')->name('participant.')->group(function () {
    Route::get('/dashboard', [ParticipantDashboardController::class, 'index'])->name('dashboard');
    
    // Attendance
    Route::get('/attendance', [ParticipantAttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/{session}', [ParticipantAttendanceController::class, 'show'])->name('attendance.show');
    Route::post('/attendance/{session}/submit', [ParticipantAttendanceController::class, 'submit'])->name('attendance.submit');
    Route::get('/attendance/proof/{attendance}/download', [ParticipantAttendanceController::class, 'downloadProof'])->name('attendance.downloadProof');
    
    // Tasks
    Route::get('/tasks', [ParticipantTaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{task}', [ParticipantTaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{task}/submit', [ParticipantTaskController::class, 'submit'])->name('tasks.submit');
    Route::get('/submissions/{submission}/download', [ParticipantTaskController::class, 'downloadSubmission'])->name('submissions.download');
});

require __DIR__.'/auth.php';
