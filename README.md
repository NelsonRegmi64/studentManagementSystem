# Student Management System (PHP + MySQL)

Fully functional Student Management System with Login, Signup, Students CRUD, Events CRUD, Attendance, and Settings.

## Requirements

- PHP 7.4+ (recommended 8.x)
- MySQL 5.7+ / MariaDB
- Apache (XAMPP, WAMP, MAMP, or Laragon)

## Setup Instructions

### 1. Copy files
Copy the entire `sms-php` folder into your web server directory:
- **XAMPP**: `C:\xampp\htdocs\sms-php`
- **WAMP**: `C:\wamp64\www\sms-php`
- **Laragon**: `C:\laragon\www\sms-php`

### 2. Create the database
1. Open **phpMyAdmin** → http://localhost/phpmyadmin
2. Go to **Import** tab
3. Select the file: `sql/schema.sql`
4. Click **Go**

Or run in MySQL terminal:
```sql
SOURCE /path/to/sms-php/sql/schema.sql;
```

### 3. Configure database (if needed)
Edit `config/database.php` if your MySQL username/password is different:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');        // change if you set a password
define('DB_NAME', 'sms_db');
```

### 4. Run the app
Open in browser:
```
http://localhost/sms-php/
```


## Update to latest code (from GitHub)

If you already copied the project into XAMPP, replace the old files with the latest from GitHub:

1. Open https://github.com/NelsonRegmi64/studentManagementSystem
2. Click the green **Code** button → **Download ZIP**
3. Unzip it
4. Copy **all files** into your existing folder (`C:\xampp\htdocs\sms-php`) and choose **Replace** when asked
5. In the browser, press **Ctrl+F5** (hard refresh) so old CSS is not cached

Or with git:
```
cd C:\xampp\htdocs\sms-php
git pull origin main
```
Then hard-refresh the page (Ctrl+F5).

## Default Login

| Email | Password |
|-------|----------|
| neishan@gmail.com | password |

You can also create a new account from the **Sign Up** page.

## Features

- **Login / Signup** with password hashing
- **Dashboard** – live stats + recent students
- **Students** – Add, Edit, Delete, Search, Pagination
- **Events** – Add, Edit, Delete
- **Attendance** – Mark Present/Absent per event
- **Settings** – Change password + Update profile
- Session-based authentication
- Responsive UI matching the original design

## Folder Structure

```
sms-php/
├── assets/css/style.css
├── config/database.php
├── includes/
│   ├── auth_check.php
│   └── sidebar.php
├── sql/schema.sql
├── index.php
├── login.php
├── signup.php
├── logout.php
├── dashboard.php
├── students.php
├── add-student.php
├── edit-student.php
├── delete-student.php
├── events.php
├── add-event.php
├── edit-event.php
├── delete-event.php
├── attendance.php
├── settings.php
└── README.md
```
