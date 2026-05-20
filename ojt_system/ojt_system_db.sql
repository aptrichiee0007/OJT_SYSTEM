








SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


;
;
;
;











CREATE TABLE `attendance_logs` (
  `log_id` int(11) NOT NULL,
  `deployment_id` int(11) DEFAULT NULL,
  `log_date` date DEFAULT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `narrative` text DEFAULT NULL,
  `task_category` varchar(100) DEFAULT NULL,
  `approval_status` enum('Pending','Approved','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;







CREATE TABLE `companies` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;





INSERT INTO `companies` (`company_id`, `company_name`, `address`, `contact_number`) VALUES
(1, 'JD TECH HUB', 'Makati City.', '0923235252'),
(2, 'Computer Hub', 'Paranaque City', '0923252422');







CREATE TABLE `deployments` (
  `deployment_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `supervisor_id` int(11) DEFAULT NULL,
  `required_hours` int(11) DEFAULT 300,
  `status` enum('Active','Completed') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;





INSERT INTO `deployments` (`deployment_id`, `student_id`, `company_id`, `supervisor_id`, `required_hours`, `status`) VALUES
(1, 17, 1, 18, 500, 'Active'),
(2, 15, 1, 18, 500, 'Active');







CREATE TABLE `documents` (
  `doc_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `document_type` varchar(50) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;







CREATE TABLE `evaluations` (
  `eval_id` int(11) NOT NULL,
  `deployment_id` int(11) DEFAULT NULL,
  `punctuality` int(11) DEFAULT NULL,
  `skills` int(11) DEFAULT NULL,
  `attitude` int(11) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;







CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('Admin','Supervisor','Student') NOT NULL,
  `status` enum('Pending','Active') DEFAULT 'Pending',
  `phone` varchar(20) DEFAULT NULL,
  `home_address` text DEFAULT NULL,
  `resume_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;





INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password_hash`, `role`, `status`, `phone`, `home_address`, `resume_file`) VALUES
(13, 'System', 'Admin', 'admin@ojt.com', '$2y$10$txnfvj23XzDZ6G9ijjCQqu5uXFtaAwdYkAwc7Fan67XhgYwjD/itu', 'Admin', 'Active', NULL, NULL, NULL),
(14, 'Jane', 'Smith', 'jane.smith@university.edu', '$2y$10$yn04713R8Pp/wqxRRg3yVOvC8vxJXOofxsTtNmHo4HpfVTPKzssMa', 'Student', 'Active', NULL, NULL, NULL),
(15, 'Michael', 'Johnson', 'mjohnson@university.edu', '$2y$10$8EweZV5XrzU2fll0iscYe.F0S1qsS71Iipm/pstKcKII1TPoYNq7m', 'Student', 'Active', NULL, NULL, NULL),
(16, 'Sarah', 'Williams', 'swilliams@university.edu', '$2y$10$xbZlqGK.EcUZStEuIQorDe17Vxe4e19Us7OEcb3megn7QOD3tMMHq', 'Student', 'Active', NULL, NULL, NULL),
(17, 'David', 'Brown', 'dbrown22@university.edu', '$2y$10$Pfeci1B0AxBqzyJPYfxdKO4lp.35M2UIYvM.3aIc6ucZzjWivDZNq', 'Student', 'Active', NULL, NULL, NULL),
(18, 'GIL', 'KALBO', 'supervisor1', '$2y$10$ZqYRHZQVklnyZ6H3eggE3.T3VPbwSI32VdRzMMM/zgWWXoVcbNPd6', 'Supervisor', 'Active', NULL, NULL, NULL),
(19, 'Christian', 'June', 'Supervisor2', '$2y$10$sa4vOlJ6FJaEgt3fJ5CLiOTgUVeRUYQendjMGWzNhXLrLL5GUKzCG', 'Supervisor', 'Active', NULL, NULL, NULL),
(20, 'Juan', 'Dela Cruz', 'juan.delacruz@olivarezcollege.edu.ph', '$2y$10$9BVMILnurkNG.DlBmf2QNO.YforjWoLSjJfL/Jp8VpDNnAcKvhbOS', 'Student', 'Active', NULL, NULL, NULL),
(21, 'Maria', 'Santos', 'maria.santos@olivarezcollege.edu.ph', '$2y$10$IMEd7fr2LUgtesjEIzDTnuwSgN7y7XthCBDJQfyGhdMbX7fy2kjDG', 'Student', 'Active', NULL, NULL, NULL),
(22, 'Mark', 'Reyes', 'mark.reyes@olivarezcollege.edu.ph', '$2y$10$9Flu.Ats2i6cFPzRm9yv0en0iR5.9VhxaY/nNzDThUfei9fTIv7ti', 'Student', 'Active', NULL, NULL, NULL),
(23, 'Ana', 'Garcia', 'ana.garcia@olivarezcollege.edu.ph', '$2y$10$/HQtknSQ6zTCYWalcsnD6O2yfJ22BQNB0rZ9uiGIncay5Rku/uhym', 'Student', 'Active', NULL, NULL, NULL),
(24, 'Pedro', 'Mendoza', 'pedro.mendoza@olivarezcollege.edu.ph', '$2y$10$dQ3YW0.uG3RWLmiYFouQ/OBSfk8a4kEXdq6QU68A/XgU5c5Wdtg8q', 'Student', 'Active', NULL, NULL, NULL),
(25, 'Elena', 'Bautista', 'elena.bautista@olivarezcollege.edu.ph', '$2y$10$6uon6SdJ9eqbYUXwRTWh5.f9RtKdyibEqcYCIilwGRekr9ZLwnFIu', 'Student', 'Active', NULL, NULL, NULL),
(26, 'Jose', 'Torres', 'jose.torres@olivarezcollege.edu.ph', '$2y$10$hylEaH1.LRzjT75/L4Soa.iDU7rY/pYTKzqVUA5e9TZHlzBcMXr2q', 'Student', 'Active', NULL, NULL, NULL),
(27, 'Carmela', 'Villanueva', 'carmela.villanueva@olivarezcollege.edu.ph', '$2y$10$6r17xlNEyCMaT0.kbbXtUeD/K1wdLvXORHuVdeB5ReemVYwHumMkO', 'Student', 'Active', NULL, NULL, NULL),
(28, 'Ramon', 'Gonzales', 'ramon.gonzales@olivarezcollege.edu.ph', '$2y$10$M80SIVkWbY5n8Cq1Xq5d0.RJLzYOAkKEneKy46BNiRdO20Ul62aaa', 'Student', 'Active', NULL, NULL, NULL),
(29, 'Sofia', 'Ramos', 'sofia.ramos@olivarezcollege.edu.ph', '$2y$10$XxYprgMgI5HZH/DqrolGTu/Cgtnnoy8h7YsZZfrzorqXyiNNLs1kq', 'Student', 'Active', NULL, NULL, NULL);








ALTER TABLE `attendance_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `deployment_id` (`deployment_id`);




ALTER TABLE `companies`
  ADD PRIMARY KEY (`company_id`);




ALTER TABLE `deployments`
  ADD PRIMARY KEY (`deployment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `supervisor_id` (`supervisor_id`);




ALTER TABLE `documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `student_id` (`student_id`);




ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`eval_id`),
  ADD KEY `deployment_id` (`deployment_id`);




ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);








ALTER TABLE `attendance_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;




ALTER TABLE `companies`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;




ALTER TABLE `deployments`
  MODIFY `deployment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;




ALTER TABLE `documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT;




ALTER TABLE `evaluations`
  MODIFY `eval_id` int(11) NOT NULL AUTO_INCREMENT;




ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;








ALTER TABLE `attendance_logs`
  ADD CONSTRAINT `attendance_logs_ibfk_1` FOREIGN KEY (`deployment_id`) REFERENCES `deployments` (`deployment_id`) ON DELETE CASCADE;




ALTER TABLE `deployments`
  ADD CONSTRAINT `deployments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `deployments_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `deployments_ibfk_3` FOREIGN KEY (`supervisor_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;




ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;




ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`deployment_id`) REFERENCES `deployments` (`deployment_id`) ON DELETE CASCADE;
COMMIT;

;
;
;
