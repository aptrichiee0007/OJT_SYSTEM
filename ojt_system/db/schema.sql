

CREATE TABLE IF NOT EXISTS `Users` (
  `user_id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `role` ENUM('Admin','Student','Supervisor') NOT NULL DEFAULT 'Student',
  `status` ENUM('Pending','Active','Suspended') NOT NULL DEFAULT 'Pending',
  `phone` VARCHAR(50) DEFAULT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `resume_file` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `Companies` (
  `company_id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `company_name` VARCHAR(255) NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  `contact_number` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `Deployments` (
  `deployment_id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT UNSIGNED NOT NULL,
  `company_id` INT UNSIGNED NOT NULL,
  `supervisor_id` INT UNSIGNED NOT NULL,
  `required_hours` INT UNSIGNED NOT NULL DEFAULT 0,
  `status` ENUM('Active','Completed','Cancelled') NOT NULL DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `Users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`company_id`) REFERENCES `Companies`(`company_id`) ON DELETE CASCADE,
  FOREIGN KEY (`supervisor_id`) REFERENCES `Users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `Attendance_Logs` (
  `log_id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `deployment_id` INT UNSIGNED NOT NULL,
  `log_date` DATE NOT NULL,
  `time_in` TIME DEFAULT NULL,
  `time_out` TIME DEFAULT NULL,
  `task_category` VARCHAR(255) DEFAULT NULL,
  `approval_status` ENUM('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`deployment_id`) REFERENCES `Deployments`(`deployment_id`) ON DELETE CASCADE,
  INDEX (`deployment_id`),
  INDEX (`log_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
