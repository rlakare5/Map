-- Create database and tables for MAP Management System
CREATE DATABASE IF NOT EXISTS map_management;
USE map_management;

-- Students table
CREATE TABLE IF NOT EXISTS students (
    prn VARCHAR(20) PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    last_name VARCHAR(50) NOT NULL,
    dept VARCHAR(100) NOT NULL,
    year INT NOT NULL,
    programme VARCHAR(50) NOT NULL,
    course_duration INT NOT NULL,
    admission_year VARCHAR(9) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Coordinators table
CREATE TABLE IF NOT EXISTS coordinators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coordinator_id VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    assigned_class VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- HoDs table
CREATE TABLE IF NOT EXISTS hods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hod_id VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    department VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id CHAR(1) PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Activities master table
CREATE TABLE IF NOT EXISTS activities_master (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id CHAR(1) NOT NULL,
    activity_name VARCHAR(150) NOT NULL,
    document_evidence VARCHAR(150) NOT NULL,
    points_type ENUM('Fixed','Level') NOT NULL,
    min_points INT DEFAULT NULL,
    max_points INT DEFAULT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Activity levels table
CREATE TABLE IF NOT EXISTS activity_levels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activity_id INT NOT NULL,
    level VARCHAR(50) NOT NULL,
    points INT NOT NULL,
    FOREIGN KEY (activity_id) REFERENCES activities_master(id) ON DELETE CASCADE
);

-- Programme rules table
CREATE TABLE IF NOT EXISTS programme_rules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admission_year VARCHAR(9) NOT NULL,
    programme VARCHAR(50) NOT NULL,
    duration INT NOT NULL,
    technical INT NOT NULL,
    sports_cultural INT NOT NULL,
    community_outreach INT NOT NULL,
    innovation INT NOT NULL,
    leadership INT NOT NULL,
    total_points INT NOT NULL
);

-- Activities submitted by students
CREATE TABLE IF NOT EXISTS activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prn VARCHAR(20) NOT NULL,
    category CHAR(1) NOT NULL,
    activity_type VARCHAR(100) NOT NULL,
    level VARCHAR(20),
    certificate VARCHAR(255) NOT NULL,
    proof_document VARCHAR(255),
    date DATE NOT NULL,
    remarks TEXT,
    status ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
    points INT DEFAULT 0,
    coordinator_remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (prn) REFERENCES students(prn) ON DELETE CASCADE
);

-- Insert default data
INSERT IGNORE INTO categories (id, name) VALUES
('A', 'Technical Skills'),
('B', 'Sports & Cultural'),
('C', 'Community Outreach & Social Initiatives'),
('D', 'Innovation / IPR / Entrepreneurship'),
('E', 'Leadership / Management');

-- Insert programme rules for 2025-2026
INSERT IGNORE INTO programme_rules 
(admission_year, programme, duration, technical, sports_cultural, community_outreach, innovation, leadership, total_points)
VALUES
('2025-2026', 'B.Tech', 4, 45, 10, 10, 25, 10, 100),
('2025-2026', 'B.Tech (DSY)', 3, 30, 10, 10, 15, 10, 75),
('2025-2026', 'Integrated B.Tech', 6, 50, 10, 15, 25, 15, 120),
('2025-2026', 'B.Pharm', 4, 45, 10, 15, 20, 10, 100),
('2025-2026', 'BCA', 3, 20, 10, 10, 10, 10, 60),
('2025-2026', 'MCA', 2, 20, 5, 10, 5, 10, 50),
('2025-2026', 'B.Sc', 3, 20, 10, 10, 10, 10, 60),
('2025-2026', 'M.Sc', 2, 20, 5, 5, 10, 10, 50),
('2025-2026', 'B.Com', 3, 20, 10, 10, 10, 10, 60),
('2025-2026', 'M.Com', 2, 20, 5, 5, 10, 10, 50),
('2025-2026', 'BBA', 3, 20, 10, 10, 10, 10, 60),
('2025-2026', 'MBA', 2, 20, 10, 10, 10, 10, 60);

-- Insert programme rules for 2024-2025
INSERT IGNORE INTO programme_rules 
(admission_year, programme, duration, technical, sports_cultural, community_outreach, innovation, leadership, total_points)
VALUES
('2024-2025', 'B.Tech', 4, 30, 5, 10, 20, 10, 75),
('2024-2025', 'B.Tech (DSY)', 3, 20, 5, 5, 15, 5, 50),
('2024-2025', 'B.Com', 3, 15, 5, 5, 10, 10, 45),
('2024-2025', 'BBA', 3, 20, 5, 5, 5, 10, 45),
('2024-2025', 'MBA', 2, 10, 5, 5, 5, 5, 30);

-- Sample activities for Category A (Technical Skills)
INSERT IGNORE INTO activities_master (category_id, activity_name, document_evidence, points_type) VALUES
('A', 'Paper Presentation', 'Certificate', 'Level'),
('A', 'Project Competition', 'Certificate', 'Level'),
('A', 'Hackathons / Ideathons', 'Certificate', 'Level'),
('A', 'Workshop Attendance', 'Certificate', 'Level');

INSERT IGNORE INTO activities_master (category_id, activity_name, document_evidence, points_type, min_points, max_points) VALUES
('A', 'MOOC with Final Assessment', 'Certificate', 'Fixed', 5, 5),
('A', 'Internship / Professional Certification', 'Certificate', 'Fixed', 5, 5),
('A', 'Industrial Visit', 'Report', 'Fixed', 5, 5);

-- Insert level-based points for technical activities
INSERT IGNORE INTO activity_levels (activity_id, level, points)
SELECT id, 'College', 3 FROM activities_master WHERE activity_name='Paper Presentation' AND category_id='A';
INSERT IGNORE INTO activity_levels (activity_id, level, points)
SELECT id, 'District', 6 FROM activities_master WHERE activity_name='Paper Presentation' AND category_id='A';
INSERT IGNORE INTO activity_levels (activity_id, level, points)
SELECT id, 'State', 9 FROM activities_master WHERE activity_name='Paper Presentation' AND category_id='A';
INSERT IGNORE INTO activity_levels (activity_id, level, points)
SELECT id, 'National', 12 FROM activities_master WHERE activity_name='Paper Presentation' AND category_id='A';
INSERT IGNORE INTO activity_levels (activity_id, level, points)
SELECT id, 'International', 15 FROM activities_master WHERE activity_name='Paper Presentation' AND category_id='A';

-- Default admin user
INSERT IGNORE INTO admins (admin_id, name, password) VALUES 
('ADMIN001', 'System Administrator', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Sample student
INSERT IGNORE INTO students (prn, first_name, last_name, dept, year, programme, course_duration, admission_year, password) VALUES
('2025001', 'John', 'Doe', 'Computer Engineering', 1, 'B.Tech', 4, '2025-2026', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');