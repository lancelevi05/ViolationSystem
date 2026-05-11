# Campus Violation Management System

A web-based application designed to manage and track student violations on campus. This system provides role-based access for administrators, teachers, guidance counselors, and non-teaching staff to report, monitor, and manage student violations effectively.

## 📋 Table of Contents

- [Features](#features)
- [System Architecture](#system-architecture)
- [Installation](#installation)
- [Configuration](#configuration)
- [User Roles](#user-roles)
- [Database Structure](#database-structure)
- [Directory Structure](#directory-structure)
- [Usage](#usage)
- [File Descriptions](#file-descriptions)
- [Security Features](#security-features)
- [Requirements](#requirements)
- [Troubleshooting](#troubleshooting)

## ✨ Features

- **Role-Based Access Control**: Different interfaces and permissions for Admin, Teachers, Guidance Counselors, and Non-teaching Staff
- **Student Violation Tracking**: Report, document, and manage student violations with evidence uploads
- **Advisory Class Management**: Manage advisory classes across different departments and levels
- **Department Management**: Organize courses and departments (both College and Senior High School)
- **Reports & Analytics**: Generate comprehensive violation reports and summaries
- **Student Archive**: Archive and manage student records
- **Evidence Documentation**: Upload and attach evidence files to violation reports
- **Secure Authentication**: Password hashing and secure session management
- **User Management**: Admin interface for managing user accounts and permissions

## 🏗️ System Architecture

```
Campus Violation System
├── Frontend Layer (PHP/HTML/CSS/JavaScript)
├── Business Logic Layer (PHP)
├── Database Layer (MySQL)
└── Role-Based Access Control
```

## 📦 Installation

### Prerequisites

- XAMPP or similar PHP/Apache/MySQL stack
- PHP 8.0 or higher
- MySQL/MariaDB
- Web browser (Chrome, Firefox, Edge, Safari)

### Setup Instructions

1. **Clone/Copy the project**:
   ```bash
   Copy the ViolationSystem1 folder to your htdocs directory
   cd C:\xampp\htdocs\ViolationSystem1
   ```

2. **Create the database**:
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Import the database file: `config/campus_violation_db.sql`
   - The database `campus_violation_db` will be created automatically

3. **Start XAMPP Services**:
   - Start Apache and MySQL from XAMPP Control Panel

4. **Access the application**:
   - Navigate to: `http://localhost/ViolationSystem1/`

## ⚙️ Configuration

### Database Connection

Edit `config/db_connect.php`:

```php
$conn = mysqli_connect("localhost", "root", "", "campus_violation_db");
```

**Parameters**:
- **Host**: `localhost` (or your server address)
- **User**: `root` (default XAMPP user)
- **Password**: `` (empty for default XAMPP)
- **Database**: `campus_violation_db`

### Default Credentials

Check the database dump files for initial user credentials:
- Admin accounts
- Teacher accounts
- Guidance Counselor accounts

## 👥 User Roles

### 1. **Administrator**
   - Access: Full system control
   - Dashboard: `admin/admin_dashboard.php`
   - Capabilities:
     - Manage all users
     - Add/edit/delete students
     - Manage college courses and departments
     - Manage senior high school courses
     - View all violation reports
     - Archive student records
     - System-wide reports and summaries

### 2. **Teacher**
   - Access: Report violations, class management
   - Dashboard: `teacher/teacher_dashboard.php`
   - Capabilities:
     - Report student violations
     - View class advisory dashboard
     - Submit violations to guidance
     - View personal reports
     - Receive notifications

### 3. **Guidance Counselor**
   - Access: Manage violations and student records
   - Dashboard: `guidance/guidance_dashboard.php`
   - Capabilities:
     - View all reported violations
     - Manage student cases
     - Advisory dashboard
     - Generate reports and summaries
     - Track student violations by department/course

### 4. **Non-Teaching Staff**
   - Access: Limited violation reporting
   - Dashboard: `nonteacher/nonteacher_dashboard.php`
   - Capabilities:
     - Report violations
     - View personal reports

## 🗄️ Database Structure

### Core Tables

| Table | Purpose |
|-------|---------|
| `users` | User accounts and authentication |
| `student_tbl` | Student information and records |
| `advisory_tbl` | Advisory class assignments |
| `collegecourse_tbl` | College courses (BSIT, BSCS, BSBA, etc.) |
| `collegedep_tbl` | College departments |
| `strandcourse_tbl` | Senior High School strands |
| `violations_tbl` | Violation reports and details |
| `evidences_tbl` | Evidence files and attachments |

### Key Fields

- **Users**: id, email, password (hashed), fname, lname, role, profile
- **Students**: id, student_id, fname, lname, mname, gender, level, section
- **Violations**: id, student_id, reporter_id, violation_type, description, date, status
- **Evidence**: id, violation_id, file_name, file_path, upload_date

## 📁 Directory Structure

```
ViolationSystem1/
├── admin/                          # Admin-only functionality
│   ├── admin_dashboard.php         # Main admin dashboard
│   ├── admin_addstudent.php        # Add new students
│   ├── admin_college.php           # Manage college departments
│   ├── admin_seniorhigh.php        # Manage SHS departments
│   ├── admin_violation.php         # View all violations
│   ├── user_management.php         # User account management
│   ├── report_violation.php        # Report violation (admin)
│   └── fetch_*.php                 # Data fetching endpoints
│
├── teacher/                        # Teacher-specific features
│   ├── teacher_dashboard.php       # Teacher main dashboard
│   ├── teacher_advisorydashboard.php # Advisory class view
│   ├── report_violation.php        # Report violation form
│   ├── teacher_notification.php    # Notifications
│   ├── submit_to_guidance.php      # Submit to guidance
│   └── fetch_student_name.php      # Student data endpoint
│
├── guidance/                       # Guidance counselor features
│   ├── guidance_dashboard.php      # Counselor main dashboard
│   ├── guidance_advisorydashboard.php # Advisory class management
│   ├── guidance_college.php        # College student management
│   ├── guidance_seniorhigh.php     # SHS student management
│   ├── guidance_violation.php      # Violation management
│   ├── guidance_myreport.php       # Personal reports
│   └── fetch_*.php                 # Data fetching endpoints
│
├── nonteacher/                     # Non-teaching staff area
│   ├── nonteacher_dashboard.php    # Staff dashboard
│   ├── nonteacher_myreport.php     # Personal reports
│   └── report_violation.php        # Report violations
│
├── config/                         # Configuration files
│   ├── db_connect.php              # Database connection
│   ├── campus_violation_db.sql     # Database schema and data
│   ├── script.sql                  # Additional scripts
│   └── userPass.sql                # User credentials setup
│
├── assets/                         # Static resources
│   └── style.css                   # Main stylesheet
│
├── uploads/                        # User-uploaded files
│   └── evidences/                  # Violation evidence storage
│
├── js/                             # JavaScript files
│   └── main.js                     # Main JavaScript functionality
│
├── img/                            # Image files
│
├── index.php                       # Login page (entry point)
├── dashboard.php                   # Main dashboard router
├── logout.php                      # Logout handler
└── README.md                       # This file
```

## 🔐 Security Features

- **Password Hashing**: Uses PHP's `password_verify()` for secure password comparison
- **Prepared Statements**: SQL injections prevented with prepared statements (`mysqli_prepare`)
- **Session Management**: Secure session handling with `session_start()`
- **Role-Based Access Control**: Different permissions for different user roles
- **Input Validation**: User input validation across forms
- **Evidence Upload Management**: Organized file uploads in `uploads/evidences/`

## 📝 Usage

### Logging In

1. Navigate to `http://localhost/ViolationSystem1/`
2. Enter your email and password
3. The system will redirect to your role-appropriate dashboard

### Reporting a Violation

**As a Teacher/Non-Teaching Staff**:
1. Go to your dashboard
2. Click "Report Violation"
3. Select the student and violation type
4. Enter violation details
5. Upload evidence (optional)
6. Submit the report

**Administrative Actions** (Admin only):
1. Access Admin Dashboard
2. Manage users, students, departments, courses
3. View comprehensive reports and statistics

### Accessing Reports

1. Navigate to "My Reports" in your dashboard
2. Filter by date, student, violation type, or status
3. Download or print reports as needed

## 📄 File Descriptions

### Core Files

- **index.php**: Login page with secure authentication
- **dashboard.php**: Role-based dashboard router
- **logout.php**: Session terminator and cleanup

### Admin Files (admin/)

- **admin_dashboard.php**: Administrative overview and controls
- **admin_addstudent.php**: Add new student interface
- **admin_addstudent2.php**: Secondary student addition process
- **user_management.php**: User account CRUD operations
- **admin_violation.php**: View and manage all violations
- **admin_summary.php**: System-wide violation summaries
- **admin_myreport.php**: Administrative reports
- **admin_studentarchived.php**: Manage archived students

### Teacher Files (teacher/)

- **teacher_dashboard.php**: Teacher main interface
- **teacher_advisorydashboard.php**: Advisory class management
- **report_violation.php**: Violation reporting form
- **teacher_notification.php**: Notification system

### Guidance Files (guidance/)

- **guidance_dashboard.php**: Counselor work area
- **guidance_violation.php**: Violation case management
- **guidance_summary.php**: Counselor reports and analytics

### Database/Config Files (config/)

- **db_connect.php**: MySQL connection handler
- **campus_violation_db.sql**: Complete database schema
- **userPass.sql**: User credential initialization

## 📋 Requirements

### Server Requirements

- PHP: 8.0 or higher
- MySQL/MariaDB: 5.7+
- Apache: 2.4+

### Minimum Specifications

- RAM: 512 MB
- Storage: 500 MB
- Network: Local or remote server connectivity

### Browser Support

- Chrome/Chromium 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## 🔧 Troubleshooting

### Database Connection Error

**Problem**: "Database connection failed"

**Solution**:
1. Check XAMPP MySQL is running
2. Verify credentials in `config/db_connect.php`
3. Ensure database `campus_violation_db` exists
4. Reimport the SQL file if needed

### Login Issues

**Problem**: "Email not found" or "Incorrect password"

**Solution**:
1. Verify user exists in database
2. Check email format
3. Reset password via admin panel if needed
4. Check user role is active

### File Upload Failures

**Problem**: Evidence files won't upload

**Solution**:
1. Check `uploads/evidences/` folder permissions (755)
2. Verify file size is within limits
3. Check server PHP upload limits in `php.ini`
4. Ensure folder exists and is writable

### Session Errors

**Problem**: "Session expired" or logged out unexpectedly

**Solution**:
1. Check PHP session timeout settings
2. Clear browser cookies
3. Restart Apache
4. Check server clock synchronization

## 📞 Support

For issues or questions:

1. Check the database schema in `config/campus_violation_db.sql`
2. Review error logs in XAMPP or PHP logs
3. Test database connectivity with phpMyAdmin
4. Verify all folders have correct permissions

## 📜 License

This project is proprietary software developed for campus violation management.

## ✅ Version History

- **Version 1.0** - Initial release with core violation management features

---

**Last Updated**: May 11, 2026

**System Database**: campus_violation_db (MySQL/MariaDB)

**Tested Environment**: XAMPP with PHP 8.0+, MySQL 5.7+
