# 🎓 TugasWeb — Academic Task & Attendance Management System

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge\&logo=laravel\&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge\&logo=php\&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.x-7952B3?style=for-the-badge\&logo=bootstrap\&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge\&logo=mysql\&logoColor=white)

**A modern, role-based academic management system for attendance tracking, task assignment, and student submissions — built with Laravel.**

</div>

---

## 📌 Overview

**TugasWeb** is a web-based academic platform designed to streamline **attendance management**, **task assignments**, and **grading workflows** in educational environments. The system supports multiple user roles with clear access control, ensuring an organized and efficient learning process.

---

## ✨ Features

### 👨‍💼 Admin Panel

* 🔐 **User Management** — Create, update, and delete users with role-based access
* 📥 **CSV Import** — Bulk user import with ID numbers via CSV
* 🏫 **Class Management** — Create and manage class groups
* 📊 **Reports** — Generate attendance and task reports

### 👨‍🏫 Mentor Dashboard

* 📚 **Class Overview** — View assigned classes and student lists
* 🕒 **Attendance Sessions** — Create, manage, and monitor attendance sessions
* 📝 **Task Management** — Create tasks with file upload submissions
* 🧮 **Grading System** — Grade student submissions with feedback
* 🔁 **Resubmission Control** — Enable or disable task resubmissions

### 👨‍🎓 Participant Dashboard

* 📤 **Task Submissions** — Upload assignments securely
* ✅ **Attendance** — Mark attendance for active sessions
* 🏆 **Grade Tracking** — View grades and mentor feedback
* ℹ️ **Class Information** — View class and mentor details

---

## 🛠️ Tech Stack

* **Backend Framework:** Laravel 12.x
* **Language:** PHP 8.2+
* **Frontend:** Bootstrap 5, Bootstrap Icons
* **Database:** MySQL 8.0 / MariaDB
* **Build Tool:** Vite
* **Authentication:** Laravel Breeze

---

## 📦 Installation

### Prerequisites

Make sure you have the following installed:

* PHP 8.2 or higher
* Composer
* Node.js & NPM
* MySQL 8.0 or MariaDB

### 🚀 Quick Setup

```bash
# Clone the repository
git clone https://github.com/yourusername/Tugas_Absen_Web.git
cd Tugas_Absen_Web

# Install backend dependencies
composer install

# Install frontend dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database credentials in .env
# DB_DATABASE=tugasweb
# DB_USERNAME=root
# DB_PASSWORD=

<<<<<<< HEAD
# Run database migrations with seeder
php artisan migrate:fresh --seed

# Note: Seeder is idempotent - can be re-run without duplicate errors
# php artisan db:seed

# Create storage symlink (skip if already exists)
=======
# Run database migrations
php artisan migrate

php artisan migrate:fresh --seed

php artisan db:seed

# Create storage symlink
>>>>>>> a57a2bef9c347d89cde0d37d93f095330f0d603c
php artisan storage:link

# Build frontend assets
npm run build

# Run the application
php artisan serve
```

### 🧪 Development Mode (Hot Reload)

```bash
composer dev
```

---

## 👥 User Roles

| Role            | Description                                    |
| --------------- | ---------------------------------------------- |
| **Admin**       | Full system access, user and class management  |
| **Mentor**      | Manage assigned classes, tasks, and attendance |
| **Participant** | Submit tasks, mark attendance, and view grades |

---

## 📁 Project Structure

```text
tugasweb/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/          # Admin controllers
│   │   ├── Mentor/         # Mentor controllers
│   │   └── Participant/    # Participant controllers
│   └── Models/             # Eloquent models
├── database/
│   └── migrations/         # Database migrations
├── resources/
│   └── views/
│       ├── admin/          # Admin views
│       ├── mentor/         # Mentor views
│       └── participant/    # Participant views
└── routes/
    └── web.php             # Web routes
```

---

## 📝 CSV Import Format

The CSV file must contain the following columns:

| Column             | Required | Description                |
| ------------------ | -------- | -------------------------- |
| `name`             | Yes      | Full name                  |
| `email`            | Yes      | Unique email address       |
| `id_number`        | Yes      | Student / Employee ID      |
| `role`             | Yes      | admin, mentor, participant |
| `password`         | Yes      | Minimum 8 characters       |
| `confirm_password` | Yes      | Must match password        |

---

## 🔐 Default Credentials (Development)

> ⚠️ **For development/testing purposes only**

```
Admin
Email: admin@example.com
Password: password

Mentor
Email: junaedi@gmail.com
Password: password

Participant
Email: mahasiswa@gmail.com
Password: password

Participant
Email: john@example.com
Password: password
```

---

## 📄 License

This project is open-sourced software licensed under the **MIT License**.

---

## 🤝 Contributing

Contributions are welcome and appreciated!

1. Fork this repository
2. Create your feature branch (`git checkout -b feature/awesome-feature`)
3. Commit your changes (`git commit -m 'Add awesome feature'`)
4. Push to the branch (`git push origin feature/awesome-feature`)
5. Open a Pull Request

---

<div align="center">

Develope by gymstiar

</div>
