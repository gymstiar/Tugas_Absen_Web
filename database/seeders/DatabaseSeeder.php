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
<<<<<<< HEAD
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
=======
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
>>>>>>> a57a2bef9c347d89cde0d37d93f095330f0d603c

        // ============================
        // Create Mentor
        // ============================
<<<<<<< HEAD
        $mentor = User::firstOrCreate(
            ['email' => 'junaedi@gmail.com'],
            [
                'name' => 'Mentor Pak Junaedi',
                'password' => Hash::make('password'),
                'role' => 'mentor',
                'email_verified_at' => now(),
            ]
        );
=======
        $mentor = User::create([
            'name' => 'Mentor Pak Junaedi',
            'email' => 'junaedi@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'mentor',
            'email_verified_at' => now(),
        ]);
>>>>>>> a57a2bef9c347d89cde0d37d93f095330f0d603c

        // ============================
        // Create Class
        // ============================
<<<<<<< HEAD
        $class = ClassGroup::firstOrCreate(
            ['code' => 'WEB DEVV - 01'],
            [
                'name' => 'Web Developer',
                'description' => 'Web development program',
                'mentor_id' => $mentor->id,
            ]
        );
=======
        $class = ClassGroup::create([
            'name' => 'Web Developer',
            'code' => 'WEB DEVV - 01',
            'description' => 'Web development program',
            'mentor_id' => $mentor->id,
        ]);
>>>>>>> a57a2bef9c347d89cde0d37d93f095330f0d603c

        // ============================
        // Create Participants
        // ============================
<<<<<<< HEAD
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
=======
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
>>>>>>> a57a2bef9c347d89cde0d37d93f095330f0d603c

        // ============================
        // Create Sample Attendance Session
        // ============================
        $now = Carbon::now();

<<<<<<< HEAD
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
=======
        AttendanceSession::create([
            'class_group_id' => $class->id,
            'mentor_id' => $mentor->id,
            'title' => 'Web Dev - Pertemuan 1',
            'description' => 'Pertemuan awal kelas Web Developer',
            'open_at' => $now->copy()->subHour(),
            'close_at' => $now->copy()->addHours(2),
            'is_open' => true,
        ]);
>>>>>>> a57a2bef9c347d89cde0d37d93f095330f0d603c

        // ============================
        // Create Sample Task
        // ============================
<<<<<<< HEAD
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
=======
        Task::create([
            'class_group_id' => $class->id,
            'mentor_id' => $mentor->id,
            'title' => 'Tugas 1: Buat Website Portofolio',
            'description' => 'Buat website portofolio sederhana menggunakan HTML & CSS.',
            'due_date' => $now->copy()->addDays(7),
            'is_active' => true,
        ]);
>>>>>>> a57a2bef9c347d89cde0d37d93f095330f0d603c

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
