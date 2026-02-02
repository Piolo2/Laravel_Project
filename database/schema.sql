-- Database Schema for Unisan Community Skill Mapping Platform

CREATE DATABASE IF NOT EXISTS unisan_skill_mapping;
USE unisan_skill_mapping;

-- 1. Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('resident', 'seeker', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Profiles Table (One-to-One with Users)
CREATE TABLE profiles (
    user_id INT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    address TEXT,
    contact_number VARCHAR(20),
    bio TEXT,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    profile_picture VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. Skill Categories
CREATE TABLE skill_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- 4. Skills (Lookup Table)
CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES skill_categories(id) ON DELETE CASCADE
);

-- 5. User Skills (Many-to-Many: Users <-> Skills)
CREATE TABLE user_skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    skill_id INT NOT NULL,
    description TEXT,
    availability_status ENUM('Available', 'Busy', 'Unavailable') DEFAULT 'Available',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE
);

-- 6. Service Requests Table
CREATE TABLE service_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seeker_id INT NOT NULL,
    provider_id INT NOT NULL,
    service_date DATETIME NOT NULL,
    status ENUM('Pending', 'Accepted', 'Declined', 'Completed') DEFAULT 'Pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seeker_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Initial Seed Data
-- Categories
INSERT INTO skill_categories (name) VALUES 
('Home Services'), 
('Professional Services'), 
('Education & Tutoring'), 
('Digital & Tech'), 
('Transport & Delivery'),
('Health & Wellness');

-- Sample Skills
INSERT INTO skills (category_id, name) VALUES 
(1, 'Plumbing'), (1, 'Carpentry'), (1, 'Electrical Repair'), (1, 'Cleaning'),
(2, 'Accounting'), (2, 'Legal Consultation'),
(3, 'Math Tutoring'), (3, 'English Tutoring'),
(4, 'Computer Repair'), (4, 'Web Development'),
(5, 'Trike Service'), (5, 'Pasabuy / Delivery'),
(6, 'Massage Therapy');

-- Default Admin Account (Password: admin123)
-- Hash generated using password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO users (username, password_hash, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
