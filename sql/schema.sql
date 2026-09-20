-- Student Management System Database Schema
-- Run this in phpMyAdmin or MySQL CLI

CREATE DATABASE IF NOT EXISTS sms_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sms_db;

-- Users table (for login)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    address VARCHAR(255) DEFAULT NULL,
    program VARCHAR(50) NOT NULL,
    year VARCHAR(10) NOT NULL,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Events table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id VARCHAR(20) NOT NULL UNIQUE,
    title VARCHAR(150) NOT NULL,
    event_date DATE NOT NULL,
    location VARCHAR(150) NOT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Attendance table
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    student_id INT NOT NULL,
    status ENUM('Present', 'Absent') DEFAULT 'Present',
    marked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY unique_attendance (event_id, student_id)
);

-- Sample data (optional)
INSERT INTO users (full_name, email, password) VALUES
('Neishan Regmi', 'neishan@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
-- Password is: password

INSERT INTO students (student_id, full_name, email, phone, address, program, year, status) VALUES
('ST001', 'Ayush Shrestha', 'ayush@mail.com', '9801234567', 'Kathmandu', 'BCA', '2nd', 'Active'),
('ST002', 'Priya Rai', 'priya@mail.com', '9801234568', 'Lalitpur', 'BCA', '2nd', 'Active'),
('ST003', 'Suman Thapa', 'suman@mail.com', '9801234569', 'Bhaktapur', 'BCA', '1st', 'Active'),
('ST004', 'Ramesh Magar', 'ramesh@mail.com', '9801234570', 'Pokhara', 'BCA', '2nd', 'Active'),
('ST005', 'Nisha Gurung', 'nisha@mail.com', '9801234571', 'Kathmandu', 'BCA', '1st', 'Active');

INSERT INTO events (event_id, title, event_date, location, description) VALUES
('EVT001', 'Welcome Program', '2026-08-15', 'College Hall', 'Orientation for new students'),
('EVT002', 'Tech Talk', '2026-08-25', 'Seminar Hall', 'Talk on latest technologies'),
('EVT003', 'Sports Day', '2026-09-05', 'College Ground', 'Annual sports competition'),
('EVT004', 'Workshop', '2026-09-15', 'Lab', 'Hands-on coding workshop');
