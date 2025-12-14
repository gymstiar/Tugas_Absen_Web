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
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // ============================
        // Create Mentor
        // ============================
        $mentor = User::create([
            'name' => 'Mentor Pak Junaedi',
            'email' => 'junaedi@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'mentor',
            'email_verified_at' => now(),
        ]);

        // ============================
        // Create Class
        // ============================
        $class = ClassGroup::create([
            'name' => 'Web Developer',
            'code' => 'WEB DEVV - 01',
            'description' => 'Web development program',
            'mentor_id' => $mentor->id,
        ]);

        // ============================
        // Create Participants
        // ============================
        $participant1 = User::create([
            'name' => 'Mahasiswa',
            'email' => 'mahasiswa@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'participant',
            'email_verified_at' => now(),
            'class_group_id' => $class->id,
        ]);

        $participant2 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'role' => 'participant',
            'email_verified_at' => now(),
            'class_group_id' => $class->id,
        ]);

        // ============================
        // Create Sample Attendance Session
        // ============================
        $now = Carbon::now();

        AttendanceSession::create([
            'class_group_id' => $class->id,
            'mentor_id' => $mentor->id,
            'title' => 'Web Dev - Pertemuan 1',
            'description' => 'Pertemuan awal kelas Web Developer',
            'open_at' => $now->copy()->subHour(),
            'close_at' => $now->copy()->addHours(2),
            'is_open' => true,
        ]);

        // ============================
        // Create Sample Task
        // ============================
        Task::create([
            'class_group_id' => $class->id,
            'mentor_id' => $mentor->id,
            'title' => 'Tugas 1: Buat Website Portofolio',
            'description' => 'Buat website portofolio sederhana menggunakan HTML & CSS.',
            'due_date' => $now->copy()->addDays(7),
            'is_active' => true,
        ]);

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
