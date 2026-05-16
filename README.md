# Collaborative File Versioning & Access Control System

A collaborative file versioning and access control system built with Laravel that allows users to securely upload, manage, reserve, edit, and track shared files inside controlled groups.

The system prevents concurrent editing conflicts using a safe source mechanism based on file locking (`in-check` / `out-check`) while providing user permissions, activity tracing, notifications, automatic backup, reporting, and a responsive user interface.

---

# Features

- Secure File Upload & Management
- File Reservation System (Safe Source)
- Concurrent Access Protection
- Group-Based File Permissions
- File Check-In / Check-Out Workflow
- Activity Logs & Tracing
- Automatic Backup System
- Reporting & Exporting
- Responsive & User-Friendly Interface
- Multi-User Parallel Processing Support

---

# Core Functionalities

## Authentication & Authorization

- User registration and login
- Role-based permissions
- Secure access control for groups and files

---

## Group Management

Users can:

- Create groups
- Invite other users to groups
- Search for users before inviting them
- Manage group members

### Group Owner Permissions

The group creator can:

- Add files
- Edit files
- Delete files
- Approve uploaded files from members
- Review member activity logs

### Group Member Permissions

Members can:

- Browse joined groups
- Upload new files
- View accessible files
- Request file operations

---

## File Management

- Upload digital files securely
- Organize files inside groups
- Track file states:
  - Free
  - Reserved/In Use

Each file includes:
- File metadata
- Reservation status
- Current editor
- Operation history

---

## Safe Source Mechanism (Check-In / Check-Out)

The system prevents two users from editing the same file simultaneously.

### Check-Out (Out-Check)

- User reserves a free file
- File becomes locked for other users
- User downloads and edits the file locally

### Check-In (In-Check)

- User uploads the modified version
- New file must have:
  - Same filename
  - Same extension

- System replaces the old version
- File becomes available again


---

## Concurrent Access Protection

The system guarantees:

- No two users can reserve the same file simultaneously
- Atomic reservation operations
- Multi-file reservation support:
  - Either all selected files are reserved successfully
  - Or none are reserved

---

## Activity Logging & Tracing

### File Logs

Each group contains file-level logs showing:

- File operations
- Who performed them
- Operation timestamps

Accessible to all group members.

### User Logs

The group creator can review:

- Member activities
- File modifications
- Reservation history
- User actions timeline

### Admin Tracing

Admin users can:

- Monitor all system operations
- Track who edited files
- Review timestamps and modifications
- Access all logs and histories

---

## Backup System

Automatic backups are created:

- Before check-out operations
- After check-in operations

This allows restoring previous file versions when needed.

---

## Notifications System

The system sends notifications when:

- File status changes
- Files are reserved
- Files are released
- Files are modified

Notifications are sent to users who have access to the related files.

---

## Reporting System

Generate reports based on:

- File activities
- User operations
- Reservation history
- Modification tracking

### Export Support

Reports can be exported as:

- CSV
- PDF

---

## Parallel User Support

The system is designed to support high concurrency and simultaneous users.

Includes:
- Concurrent processing handling
- Reservation synchronization
- Performance testing support using tools like:
  - JMeter

---

## Responsive User Interface

The application is designed to work across:

- Desktop devices
- Tablets
- Smartphones

Compatible with modern web browsers.

---

# User Roles

## Admin

- Full system access
- Monitor all users and groups
- Access all logs and reports
- Track file modifications and activities

---

## Group Owner

- Create and manage groups
- Invite/remove members
- Manage files
- Approve uploads
- Review member activity logs

---

## Group Member

- Access authorized groups
- Upload files
- Reserve files
- Edit and return files
- View accessible logs

---

# Technology Stack

| Technology | Description |
|------------|-------------|
| PHP 8+ | Backend Language |
| Laravel | Backend Framework |
| Blade | Frontend Templating |
| MySQL | Database |
| JavaScript | Client-side Functionality |
| RESTful Architecture | API & System Structure |

---

# System Architecture

The project follows a layered architecture:

- Views + Controllers → Presentation Layer
- Services → Business Logic Layer
- Models + Database → Data Layer
- Repository Layer (optional for reusable queries)

This structure improves:

- Scalability
- Maintainability
- Clean code organization
- Separation of concerns

---

# Non-Functional Requirements

Implemented non-functional requirements include:

- Concurrent multi-user support
- Automatic backup system
- Responsive UI
- System usability
- Reporting export functionality

---

# Getting Started

## Prerequisites

- PHP ≥ 8.0
- Composer
- MySQL/MariaDB
- Node.js & NPM

---

# Installation

## Clone the Repository

```bash
git clone https://github.com/Daniaketaz/collaborative-file-versioning-system.git
cd collaborative-file-versioning-system
```

---

## Install PHP Dependencies

```bash
composer install
```

## Configure Environment

```bash
cp .env.example .env
```

Update your database credentials inside `.env`.

---

## Generate Application Key

```bash
php artisan key:generate
```

---

## Run Migrations

```bash
php artisan migrate
```

---

## Run the Application

```bash
php artisan serve
```

Open:

```text
http://localhost:8000
```

---

# Usage Workflow

1. Register/Login
2. Create or join groups
3. Upload files
4. Reserve files using check-out
5. Edit files locally
6. Upload modified files using check-in
7. Review logs and reports

---

# Project Structure

- `app/` → Controllers, Models, Services
- `resources/views/` → Blade templates
- `routes/` → Web & API routes
- `public/` → Public assets
- `database/` → Migrations & seeders

---

# License

This project is licensed under the MIT License.

---

# Author

GitHub: [Daniaketaz](https://github.com/Daniaketaz)

---
