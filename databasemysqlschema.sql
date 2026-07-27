-- ============================================================================
-- STUDENT MANAGEMENT SYSTEM SCHEMA (GRADES 1 - 6)
-- MySQL / phpMyAdmin Compatible (Schema Only)
-- ============================================================================

-- 1. CREATE DATABASE (IF IT DOES NOT EXIST)
CREATE DATABASE IF NOT EXISTS primary_school_db
  DEFAULT CHARACTER SET utf8mb4 
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE primary_school_db;

-- ============================================================================
-- 2. CREATE TABLES
-- ============================================================================

-- Teachers Table
CREATE TABLE IF NOT EXISTS teachers (
    teacher_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    hire_date DATE DEFAULT (CURRENT_DATE)
) ENGINE=InnoDB;

-- Guardians / Parents Table
CREATE TABLE IF NOT EXISTS guardians (
    guardian_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    relationship VARCHAR(30) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    address TEXT NOT NULL
) ENGINE=InnoDB;

-- Students Table
CREATE TABLE IF NOT EXISTS students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    dob DATE NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    admission_date DATE DEFAULT (CURRENT_DATE),
    medical_notes TEXT,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

-- Junction Table: Students <-> Guardians
CREATE TABLE IF NOT EXISTS student_guardians (
    student_id INT NOT NULL,
    guardian_id INT NOT NULL,
    is_primary_contact BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (student_id, guardian_id),
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (guardian_id) REFERENCES guardians(guardian_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Classes Table (Strictly Grades 1 to 6)
CREATE TABLE IF NOT EXISTS classes (
    class_id INT AUTO_INCREMENT PRIMARY KEY,
    grade_level INT NOT NULL CHECK (grade_level BETWEEN 1 AND 6),
    section VARCHAR(5) NOT NULL,
    academic_year VARCHAR(9) NOT NULL,
    homeroom_teacher_id INT,
    CONSTRAINT unique_class_per_year UNIQUE (grade_level, section, academic_year),
    FOREIGN KEY (homeroom_teacher_id) REFERENCES teachers(teacher_id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Subjects Table
CREATE TABLE IF NOT EXISTS subjects (
    subject_id INT AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
) ENGINE=InnoDB;

-- Enrollments Table
CREATE TABLE IF NOT EXISTS enrollments (
    enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    class_id INT NOT NULL,
    enrollment_date DATE DEFAULT (CURRENT_DATE),
    CONSTRAINT unique_student_class UNIQUE (student_id, class_id),
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(class_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Attendance Table
CREATE TABLE IF NOT EXISTS attendance (
    attendance_id INT AUTO_INCREMENT PRIMARY KEY,
    enrollment_id INT NOT NULL,
    date DATE NOT NULL DEFAULT (CURRENT_DATE),
    status ENUM('Present', 'Absent', 'Late', 'Excused') NOT NULL,
    remarks VARCHAR(255),
    CONSTRAINT unique_attendance_per_day UNIQUE (enrollment_id, date),
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(enrollment_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Academic Grades Table
CREATE TABLE IF NOT EXISTS grades (
    grade_id INT AUTO_INCREMENT PRIMARY KEY,
    enrollment_id INT NOT NULL,
    subject_id INT NOT NULL,
    term ENUM('Term 1', 'Term 2', 'Term 3', 'Term 4') NOT NULL,
    score DECIMAL(5,2) CHECK (score >= 0 AND score <= 100),
    letter_grade VARCHAR(5),
    teacher_comments TEXT,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT unique_grade_per_term UNIQUE (enrollment_id, subject_id, term),
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(enrollment_id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================================
-- 3. INDEXES FOR PERFORMANCE
-- ============================================================================
CREATE INDEX idx_students_active ON students(is_active);
CREATE INDEX idx_attendance_date ON attendance(date);