# Task Management System

A role-based Task Management System built using PHP, MySQL, JavaScript (AJAX), and Bootstrap.

## 🔗 Live Demo
https://taskmanagementapexkrish.infinityfreeapp.com

## 📌 Features

### 👤 User
- Register with Email OTP Verification
- Login / Logout
- Add, Edit, Delete Tasks
- Task Status: Pending / In Progress / Completed
- AJAX-based task filtering
- Secure session handling

### 🛠 Admin
- Admin Dashboard
- View all users
- View all tasks
- Edit/Delete any task
- Role-based access control
- AJAX delete without page reload

## ⚙️ Tech Stack
- PHP (Core PHP)
- MySQL
- JavaScript (AJAX / Fetch API)
- Bootstrap 5
- PHPMailer (SMTP Email)

## 📂 Project Structure
htdocs/
│
├── admin/
│ ├── dashboard.php
│ ├── manage_tasks.php
│ ├── edit_task.php
│ └── delete_task.php
│
├── user/
│ ├── tasks.php
│ ├── add_task.php
│ ├── edit_task.php
│ ├── delete_task.php
│ └── ajax_tasks.php
│
├── assets/
│ ├── css/
│ └── js/app.js
│
├── config/
│ ├── db.php
│ └── mail.php
│
├── login.php
├── register.php
├── verify_otp.php
└── index.php

markdown
Copy code

## 🔐 Default Roles
- **Admin**: role_id = 1
- **User**: role_id = 2

## 🧪 AJAX Features
- Task filtering without page reload
- Task delete without page reload
- Smooth UI updates

## 📸 Screenshots
Screenshots are included showing:
- User Task Page
- Admin Task Management
- Edit Task
- OTP Verification

## 🎓 Internship Details
- **Internship Program**: Apex Planet Internship
- **Task Number**: Task 5
- **Project Name**: Task Management System

## 👤 Author
**Krishnashis Goswami**
