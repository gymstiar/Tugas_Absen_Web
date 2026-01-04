<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ClassGroup;
use App\Models\AttendanceSession;
use App\Models\Task;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============================
        // Create Admin
        // ============================
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // ============================
        // Create Mentor
        // ============================
        $mentor = User::firstOrCreate(
            ['email' => 'junaedi@gmail.com'],
            [
                'name' => 'Mentor Pak Junaedi',
                'password' => Hash::make('password'),
                'role' => 'mentor',
                'email_verified_at' => now(),
            ]
        );

        // ============================
        // Create Class
        // ============================
        $class = ClassGroup::firstOrCreate(
            ['code' => 'WEB DEVV - 01'],
            [
                'name' => 'Web Developer',
                'description' => 'Web development program',
                'mentor_id' => $mentor->id,
            ]
        );

        // ============================
        // Create Participants
        // ============================
        $participant1 = User::firstOrCreate(
            ['email' => 'mahasiswa@gmail.com'],
            [
                'name' => 'Mahasiswa',
                'password' => Hash::make('password'),
                'role' => 'participant',
                'email_verified_at' => now(),
                'class_group_id' => $class->id,
            ]
        );

        $participant2 = User::firstOrCreate(
            ['email' => 'john@example.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'role' => 'participant',
                'email_verified_at' => now(),
                'class_group_id' => $class->id,
            ]
        );

        // ============================
        // Create Sample Attendance Session
        // ============================
        $now = Carbon::now();

        AttendanceSession::firstOrCreate(
            [
                'class_group_id' => $class->id,
                'title' => 'Web Dev - Pertemuan 1',
            ],
            [
                'mentor_id' => $mentor->id,
                'description' => 'Pertemuan awal kelas Web Developer',
                'open_at' => $now->copy()->subHour(),
                'close_at' => $now->copy()->addHours(2),
                'is_open' => true,
            ]
        );

        // ============================
        // Create Sample Task
        // ============================
        Task::firstOrCreate(
            [
                'class_group_id' => $class->id,
                'title' => 'Tugas 1: Buat Website Portofolio',
            ],
            [
                'mentor_id' => $mentor->id,
                'description' => 'Buat website portofolio sederhana menggunakan HTML & CSS.',
                'due_date' => $now->copy()->addDays(7),
                'is_active' => true,
            ]
        );

        // ============================
        // Console Output
        // ============================
        $this->command->info("\n===================================");
        $this->command->info("   DATABASE SEEDED SUCCESSFULLY!");
        $this->command->info("===================================\n");

        $this->command->info("Login Credentials:");
        $this->command->info(" ADMIN:");
        $this->command->info("  admin@example.com / password\n");

        $this->command->info(" MENTOR:");
        $this->command->info("  junaedi@gmail.com / password\n");

        $this->command->info(" PARTICIPANTS:");
        $this->command->info("  mahasiswa@gmail.com / password");
        $this->command->info("  john@example.com / password\n");
    }
}
