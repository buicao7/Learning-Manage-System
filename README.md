# Database
CREATE DATABASE lms;
USE lms;

-- ==========================
-- USERS
-- ==========================
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','lecturer','student') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================
-- COURSES
-- ==========================
CREATE TABLE courses (
    course_id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(150) NOT NULL,
    description TEXT,
    lecturer_id INT NOT NULL,
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_course_lecturer
    FOREIGN KEY (lecturer_id)
    REFERENCES users(user_id)
    ON DELETE CASCADE
);

-- ==========================
-- ENROLLMENTS
-- ==========================
CREATE TABLE enrollments (
    enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    enroll_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE(student_id, course_id),

    CONSTRAINT fk_enroll_student
    FOREIGN KEY(student_id)
    REFERENCES users(user_id)
    ON DELETE CASCADE,

    CONSTRAINT fk_enroll_course
    FOREIGN KEY(course_id)
    REFERENCES courses(course_id)
    ON DELETE CASCADE
);

-- ==========================
-- MATERIALS
-- ==========================
CREATE TABLE materials (
    material_id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    file_path VARCHAR(255),
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_material_course
    FOREIGN KEY(course_id)
    REFERENCES courses(course_id)
    ON DELETE CASCADE
);

-- ==========================
-- ASSIGNMENTS
-- ==========================
CREATE TABLE assignments (
    assignment_id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    due_date DATETIME,

    CONSTRAINT fk_assignment_course
    FOREIGN KEY(course_id)
    REFERENCES courses(course_id)
    ON DELETE CASCADE
);

-- ==========================
-- SUBMISSIONS
-- ==========================
CREATE TABLE submissions (
    submission_id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    student_id INT NOT NULL,
    file_path VARCHAR(255),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_submission_assignment
    FOREIGN KEY(assignment_id)
    REFERENCES assignments(assignment_id)
    ON DELETE CASCADE,

    CONSTRAINT fk_submission_student
    FOREIGN KEY(student_id)
    REFERENCES users(user_id)
    ON DELETE CASCADE
);

-- ==========================
-- GRADES
-- ==========================
CREATE TABLE grades (
    grade_id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL UNIQUE,
    score DECIMAL(5,2),
    feedback TEXT,
    graded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_grade_submission
    FOREIGN KEY(submission_id)
    REFERENCES submissions(submission_id)
    ON DELETE CASCADE
);

-- ==========================
-- NOTIFICATIONS
-- ==========================
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_notification_user
    FOREIGN KEY(user_id)
    REFERENCES users(user_id)
    ON DELETE CASCADE
);
