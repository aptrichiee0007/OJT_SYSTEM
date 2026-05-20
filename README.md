[ojt_system_db.sql](https://github.com/user-attachments/files/28058682/ojt_system_db.sql)OJT Student Portal
A comprehensive web-based OJT Student Portal designed to streamline the On-the-Job Training process, allowing students to manage their progress and administrators to oversee deployments, document verification, and performance tracking.

Key Features
Student Dashboard:

Time Clock: Integrated system to log daily OJT hours.

Progress Tracking: Visual representation of required vs. completed hours.

Document Vault: Secure upload and management of OJT requirements.

Leave Requests: Automated filing and tracking of absence/leave requests.

DTR Generation: One-click "Print to PDF" functionality for Daily Time Records.

Admin Dashboard:

Student Management: Bulk import/export of student masterlists.

Deployment Oversight: Assign students to specific companies and supervisors.

Verification System: Centralized review and approval of student-submitted documents.

Account Recovery: Administrative control over password resets and account status.

Analytics: Visual dashboard overview using Chart.js to monitor deployment status.

📸 Screenshots
SIGN IN:
<img width="1912" height="960" alt="1" src="https://github.com/user-attachments/assets/48d71b32-6822-415f-a2a2-8b73adeb4d62" />
REGISTER
<img width="1918" height="961" alt="2" src="https://github.com/user-attachments/assets/a9c095f6-4746-4811-9671-d17a9ceb5027" />
ADMIN DASHBOARD:
<img width="1914" height="967" alt="3" src="https://github.com/user-attachments/assets/49840998-8f29-436a-9ce6-ad4eb0cf2d6b" />
STUDENT DASHBOARD:
<img width="1918" height="962" alt="6" src="https://github.com/user-attachments/assets/0347d03c-0e8c-45a3-8c07-082f7dd88cfe" />
TIMESHEET HISTORY:
<img width="1914" height="962" alt="7" src="https://github.com/user-attachments/assets/9d0aefc4-ac9b-4098-9725-9ce9af729b73" />
PERSONAL INFORMATION
<img width="1917" height="958" alt="8" src="https://github.com/user-attachments/assets/7ed99405-7006-4138-a7b6-f567ac9dc12b" />
STUDENT MASTERLIST:
<img width="1912" height="959" alt="4" src="https://github.com/user-attachments/assets/76ced86f-31e2-4960-8640-cec0d5ae4de2" />
SUPERVISOR DASHBOARD:
<img width="1915" height="962" alt="5" src="https://github.com/user-attachments/assets/47be610b-b656-48d0-ad37-178c3b8754f7" />


Technical Stack:
Backend: PHP
Database: MySQL
Frontend: HTML5, CSS3, JavaScript (with Boxicons for UI and Chart.js for analytics)

Setup & Installation:
Prerequisites
PHP (v7.4 or higher recommended)
MySQL/MariaDB
Apache or Nginx web server

Database Setup:
Import this Database from MySQL[Uploading -- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2026 at 02:58 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ojt_system_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance_logs`
--

CREATE TABLE `attendance_logs` (
  `log_id` int(11) NOT NULL,
  `deployment_id` int(11) NOT NULL,
  `log_date` date NOT NULL,
  `time_in` time NOT NULL,
  `time_out` time DEFAULT NULL,
  `task_category` varchar(50) DEFAULT NULL,
  `approval_status` enum('Pending','Approved','Rejected','Absent') DEFAULT 'Pending',
  `narrative` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_logs`
--

INSERT INTO `attendance_logs` (`log_id`, `deployment_id`, `log_date`, `time_in`, `time_out`, `task_category`, `approval_status`, `narrative`) VALUES
(1, 1, '2026-05-13', '18:42:02', NULL, NULL, 'Pending', NULL),
(2, 1, '2026-05-14', '08:57:24', '09:01:11', NULL, 'Approved', NULL),
(6, 1, '2026-05-14', '12:14:40', '12:14:58', NULL, 'Approved', 'Coding'),
(7, 1, '2026-05-14', '13:16:42', '17:13:01', NULL, 'Approved', 'No narrative required.'),
(8, 1, '2026-05-15', '00:00:00', NULL, 'LEAVE: My head hurts', 'Approved', NULL),
(9, 1, '2026-05-20', '15:21:11', '16:34:18', NULL, 'Pending', 'No narrative required.'),
(10, 1, '2026-05-20', '18:25:11', '18:25:13', NULL, 'Pending', 'No narrative required.'),
(11, 1, '2026-05-20', '18:25:13', '18:25:14', NULL, 'Pending', 'No narrative required.'),
(12, 1, '2026-05-20', '18:25:14', '18:25:15', NULL, 'Pending', 'No narrative required.'),
(13, 1, '2026-05-20', '00:00:00', '18:51:47', 'LEAVE: Masakit Ulo', 'Pending', 'No narrative required.'),
(14, 2, '2026-05-20', '20:33:04', '20:33:04', NULL, 'Pending', 'No narrative required.'),
(15, 2, '2026-05-20', '20:33:04', '20:33:04', NULL, 'Pending', 'No narrative required.'),
(16, 2, '2026-05-20', '20:33:05', '20:33:05', NULL, 'Pending', 'No narrative required.'),
(17, 2, '2026-05-20', '20:33:05', '20:33:05', NULL, 'Pending', 'No narrative required.'),
(18, 2, '2026-05-20', '20:33:05', '20:33:07', NULL, 'Pending', 'No narrative required.'),
(19, 2, '2026-05-20', '20:33:07', '20:33:07', NULL, 'Pending', 'No narrative required.'),
(20, 2, '2026-05-20', '20:33:08', '20:33:08', NULL, 'Pending', 'No narrative required.');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`company_id`, `company_name`, `address`, `contact_number`) VALUES
(1, 'JD TECH HUB', '12TH STREET MANILA', '091232232'),
(888, 'Screenshot Corp', 'Manila', '123-4567'),
(997, 'Dummy Corp', '123 Tech Lane', '123-4567'),
(998, 'Company Test 2', 'Paranaque City', '09232542232');

-- --------------------------------------------------------

--
-- Table structure for table `deployments`
--

CREATE TABLE `deployments` (
  `deployment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `required_hours` int(11) NOT NULL DEFAULT 300,
  `status` enum('Active','Completed','Suspended') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deployments`
--

INSERT INTO `deployments` (`deployment_id`, `student_id`, `company_id`, `supervisor_id`, `required_hours`, `status`) VALUES
(1, 1, 1, 8, 700, 'Active'),
(2, 12, 1, 8, 300, 'Active'),
(8, 999, 997, 998, 300, 'Active'),
(9, 1016, 998, 998, 300, 'Active'),
(10, 1005, 997, 1000, 500, 'Active'),
(11, 1010, 998, 998, 300, 'Active'),
(12, 1011, 1, 998, 300, 'Active'),
(13, 1015, 1, 998, 300, 'Active'),
(14, 10, 998, 998, 300, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `doc_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`doc_id`, `student_id`, `document_type`, `file_path`, `status`, `uploaded_at`) VALUES
(1, 1, 'Medical Certificate', 'MedicalCertificate_1_1778752403.pdf', 'Rejected', '2026-05-14 09:53:23'),
(2, 1, 'Memorandum of Agreement (MOA)', 'MemorandumofAgreementMOA_1_1779274191.pdf', 'Approved', '2026-05-20 10:49:51');

-- --------------------------------------------------------

--
-- Table structure for table `evaluations`
--

CREATE TABLE `evaluations` (
  `eval_id` int(11) NOT NULL,
  `deployment_id` int(11) NOT NULL,
  `punctuality` int(11) NOT NULL,
  `skills` int(11) NOT NULL,
  `attitude` int(11) NOT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluations`
--

INSERT INTO `evaluations` (`eval_id`, `deployment_id`, `punctuality`, `skills`, `attitude`, `comments`, `created_at`) VALUES
(1, 1, 77, 79, 78, 'good', '2026-05-14 09:49:20'),
(2, 2, 85, 85, 85, 'Very Well', '2026-05-20 08:36:53');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `role` enum('Admin','Student','Supervisor') NOT NULL,
  `status` enum('Pending','Active','Inactive') DEFAULT 'Pending',
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `resume_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `first_name`, `last_name`, `role`, `status`, `phone`, `address`, `resume_file`) VALUES
(1, 'JohnDoe@gmail.com', '$2y$10$HlOZGn6qYXywh3BrQQ652.q4go2Ao0Ao4lqXT4i5sOva2M9X11eVe', 'John', 'Doe', 'Student', 'Active', '', '', 'resume_user1_1778722692.pdf'),
(7, 'admin', '$2y$10$zR428LrvGs7kSxbxUDD0yu84RBAmF8kjAHCAhm1.F6.De2p9jtDhC', 'System', 'Admin', 'Admin', 'Active', NULL, NULL, NULL),
(8, 'supervisor1', '$2y$10$pMzhYq.w.6FnX8rR2BP.wO89wTmmLoc0uByhJepzk6NzUGJn65Q8a', 'GIL', 'KALBO', 'Supervisor', 'Active', NULL, NULL, NULL),
(9, 'jane.smith@university.edu', '$2y$10$IbbN2VA61EICoiA5MbeALuld3NYep5kVCT0j/gcwI47k6371L1DLy', 'Jane', 'Smith', 'Student', 'Active', NULL, NULL, NULL),
(10, 'mjohnson@university.edu', '$2y$10$YjGEEfK9pBm/CQrM72iwK.ciKHPTTNsJzywDudbie8haJKdsrP6K2', 'Michael', 'Johnson', 'Student', 'Active', NULL, NULL, NULL),
(11, 'swilliams@university.edu', '$2y$10$Z.WcS/Q.DaD4fr5D5hYhQefACoF8Wm12dCJgPLlQPBLSgxyW/zbRe', 'Sarah', 'Williams', 'Student', 'Active', NULL, NULL, NULL),
(12, 'dbrown22@university.edu', '$2y$10$TPSmelNjAPJhIXToBcfqy.K22gTYNnwHyHO907EZQI37nUi4earJe', 'David', 'Brown', 'Student', 'Active', NULL, NULL, NULL),
(888, 'test888@email.com', 'pass', 'Test', 'User', 'Student', 'Active', NULL, NULL, NULL),
(998, 'supervisor998@university.edu', 'password123', 'Dummy', 'Supervisor', 'Supervisor', 'Active', NULL, NULL, NULL),
(999, 'student999@university.edu', 'password123', 'Dummy', 'Student', 'Student', 'Active', NULL, NULL, NULL),
(1000, 'Supervisor2', '$2y$10$e2wBdQ87LDy4w7pm64QyeOUfWyh9IS36RxRnE9a9vT1WxPkf5d5dW', 'Jay', 'Cole', 'Supervisor', 'Active', NULL, NULL, NULL),
(1001, 'juan.delacruz@olivarezcollege.edu.ph', '$2y$10$Fni0PdnxUhLOqjkDpU/mDe2dUNjXYw./ng7vQFjIfL3bx5.RmDUk2', 'Juan', 'Dela Cruz', 'Student', 'Active', NULL, NULL, NULL),
(1002, 'maria.santos@olivarezcollege.edu.ph', '$2y$10$ftH3A/gxrKnppjIMEvAd.ORaZzPsht6BS4ku6lRtA9Sb.w7/yAuH6', 'Maria', 'Santos', 'Student', 'Active', NULL, NULL, NULL),
(1003, 'jose.rizal@olivarezcollege.edu.ph', '$2y$10$JSUisxyBddaDpZ9uApdsy.I3x4.W6B4/LJgaJtcTZ89eEYWwRVNTm', 'Jose', 'Rizal', 'Student', 'Active', NULL, NULL, NULL),
(1004, 'ana.reyes@olivarezcollege.edu.ph', '$2y$10$Ll0gKEc/DPJi016R5392Re5xnSk4llLvWjfxVt3QuyFDczbXLqQWm', 'Ana', 'Reyes', 'Student', 'Active', NULL, NULL, NULL),
(1005, 'pedro.penduko@olivarezcollege.edu.ph', '$2y$10$P4wOjTWrHL89YoP.nGa9UOQpV90X9UvZwk4Idd3QIbzt/BY0Vglau', 'Pedro', 'Penduko', 'Student', 'Active', NULL, NULL, NULL),
(1006, 'elena.garcia@olivarezcollege.edu.ph', '$2y$10$fMLYnWkS39GPxzvqGbaPzuwtNbk6rOiblgENrPj5HlDdZHNNhUd1y', 'Elena', 'Garcia', 'Student', 'Active', NULL, NULL, NULL),
(1007, 'carlos.torres@olivarezcollege.edu.ph', '$2y$10$MIzZxa.D8IrxWsmIKIXo4eSSbuUlz9wQmfJBtYZ9Pva9O1x.ekZBG', 'Carlos', 'Torres', 'Student', 'Active', NULL, NULL, NULL),
(1008, 'sofia.mendoza@olivarezcollege.edu.ph', '$2y$10$QvdJelJPiz5gnRJnY8CjH.9teBfq66JBv5QS.XasJnlxHnwwO0kE2', 'Sofia', 'Mendoza', 'Student', 'Active', NULL, NULL, NULL),
(1009, 'luis.bautista@olivarezcollege.edu.ph', '$2y$10$T0zHQFSvPK2cdffmv7EVI.YIjHngGCqWEvBcPXhgSjg933PDjHfQC', 'Luis', 'Bautista', 'Student', 'Active', NULL, NULL, NULL),
(1010, 'isabella.castro@olivarezcollege.edu.ph', '$2y$10$NTgIWPSX2wUAvVGMQ7FzjOtbuzgEZTYXAjJEQb8NscC4TPwCre4Vu', 'Isabella', 'Castro', 'Student', 'Active', NULL, NULL, NULL),
(1011, 'ricardo.dalisay@olivarezcollege.edu.ph', '$2y$10$TBKOp0OoumreiG3uagJyuuCDsgBdQF...7rus3t7soXCCyYUvR542', 'Ricardo', 'Dalisay', 'Student', 'Active', NULL, NULL, NULL),
(1012, 'clara.valderama@olivarezcollege.edu.ph', '$2y$10$fMvxHGwHMHADeZzLaA/gievedYH6/pzQ3zUW0Z45RI0tbBSQy52Xm', 'Clara', 'Valderama', 'Student', 'Active', NULL, NULL, NULL),
(1013, 'antonio.luna@olivarezcollege.edu.ph', '$2y$10$9oPrtFIXgDwn46C2BXvVL.XkSeEgNMSAGQcPRaNu2iXeT18iIflBe', 'Antonio', 'Luna', 'Student', 'Active', NULL, NULL, NULL),
(1014, 'gabriela.silang@olivarezcollege.edu.ph', '$2y$10$zajI.55xA9xu832.ujTZQ.f6Pwi/YnrgJYWbCDkMzfRU86V6S6Oq2', 'Gabriela', 'Silang', 'Student', 'Active', NULL, NULL, NULL),
(1015, 'miguel.lopez@olivarezcollege.edu.ph', '$2y$10$1RGNxIv0oRqXuS3mimH8bOqjJXhDrqYtZd0FYn5LF7AK.O5PC2MaG', 'Miguel', 'Lopez', 'Student', 'Active', NULL, NULL, NULL),
(1016, 'lucia.fernandez@olivarezcollege.edu.ph', '$2y$10$YZIBosr6X2P5rupfrTJWkeBP9Ay2aHsD7si4Pk4yPSnPeUuZNxX3i', 'Lucia', 'Fernandez', 'Student', 'Active', NULL, NULL, NULL),
(1017, 'fernando.poe@olivarezcollege.edu.ph', '$2y$10$N4cVFQVv1zQZpby952y5o.iA6mo3mssDIjyEh69GPKTSPJlx3ig42', 'Fernando', 'Poe', 'Student', 'Active', NULL, NULL, NULL),
(1018, 'teresa.magbanua@olivarezcollege.edu.ph', '$2y$10$1BXLI2aaSfl7S297jZfeoeJ8OwE7zoX4UQjkrdJ2R6ZtSe49Ambr6', 'Teresa', 'Magbanua', 'Student', 'Active', NULL, NULL, NULL),
(1019, 'roberto.gomez@olivarezcollege.edu.ph', '$2y$10$/p389G.529Td6KbJkc30xeh32mTR3hH8bW0oew5rjaC/gUfVrERze', 'Roberto', 'Gomez', 'Student', 'Active', NULL, NULL, NULL),
(1020, 'victoria.cruz@olivarezcollege.edu.ph', '$2y$10$Mugt21NyZvOaFokmlcEQ/urN0gj1S2VmTyAqCqCufz2tSBytPaX5C', 'Victoria', 'Cruz', 'Student', 'Active', NULL, NULL, NULL),
(1021, 'richbrian.delfin@olivarezcollege.edu.ph', '$2y$10$DPZ1hopEeEIVxjSpQeFhKec8esK8a4MQ25palbiAAwhOoGqznrjH6', 'Rich Brian', 'Delfin', 'Student', 'Pending', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`company_id`),
  ADD UNIQUE KEY `company_name` (`company_name`);

--
-- Indexes for table `deployments`
--
ALTER TABLE `deployments`
  ADD PRIMARY KEY (`deployment_id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `supervisor_id` (`supervisor_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`doc_id`);

--
-- Indexes for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`eval_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=999;

--
-- AUTO_INCREMENT for table `deployments`
--
ALTER TABLE `deployments`
  MODIFY `deployment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `eval_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1022;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD CONSTRAINT `attendance_logs_ibfk_1` FOREIGN KEY (`deployment_id`) REFERENCES `deployments` (`deployment_id`) ON DELETE CASCADE;

--
-- Constraints for table `deployments`
--
ALTER TABLE `deployments`
  ADD CONSTRAINT `deployments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `deployments_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`),
  ADD CONSTRAINT `deployments_ibfk_3` FOREIGN KEY (`supervisor_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
ojt_system_db.sql…]()

PHP
private $host = 'localhost';
private $db_name = 'ojt_system';
private $username = 'your_username';
private $password = 'your_password';
Running the Application
Place the project folder in your server's root directory (e.g., htdocs for XAMPP).

Start your Apache and MySQL services.

Access the portal via your browser at http://localhost/ojt_system.

Project Structure
/assets/: CSS styles, JavaScript, and custom icons.

/controller/: PHP logic handling form submissions, actions, and authentication.

/model/: Database connection and data retrieval logic.

/uploads/: Secure storage for student documents and resumes.

student_dashboard.php: Main student interface.

admin_dashboard.php: Main administrative interface.

Usage Notes
Admin Panel: Access is restricted based on user_role. Ensure the 'Admin' role is correctly assigned in the users table.

Bulk Import: When adding students via CSV, ensure your file follows the strictly required format: First Name, Last Name, Email, Password.

PDF Generation: The "Print to PDF" feature uses the deployment ID to fetch and render student-specific timesheets. Ensure print_timesheet.php has appropriate permissions.
