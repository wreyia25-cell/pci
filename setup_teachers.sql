-- SQL script to add teachers table and sample data
-- Run this script in your MySQL database to set up teacher login functionality

USE osrs_db;

-- Create teachers table
CREATE TABLE IF NOT EXISTS `teachers` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(200) NOT NULL,
  `last_name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` text NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `qualification` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create teacher_subject table (for many-to-many relationship)
CREATE TABLE IF NOT EXISTS `teacher_subject` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `teacher_id` int(30) NOT NULL,
  `subject_id` int(30) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `subject_id` (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create teacher_class table (for many-to-many relationship)
CREATE TABLE IF NOT EXISTS `teacher_class` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `teacher_id` int(30) NOT NULL,
  `class_id` int(30) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `class_id` (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample teacher with email: teacher@example.com and password: password123
INSERT INTO `teachers` (`first_name`, `last_name`, `email`, `password`, `phone_number`, `hire_date`, `qualification`) VALUES
('John', 'Doe', 'teacher@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123-456-7890', '2020-01-15', 'Bachelor of Education');

-- Note: The password hash above corresponds to 'password123'
-- To create your own password hash, use: password_hash('your_password', PASSWORD_DEFAULT) in PHP