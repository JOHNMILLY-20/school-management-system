-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2025 at 08:08 PM
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
-- Database: `maluti_primary_school`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` enum('present','absent') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `date`, `status`) VALUES
(1, 2, '2025-04-02', 'absent'),
(2, 3, '2025-04-02', 'absent'),
(3, 2, '2025-04-22', 'absent'),
(4, 3, '2025-04-22', 'absent'),
(5, 3, '2025-04-22', 'present'),
(6, 3, '2025-04-16', 'present'),
(7, 19, '2009-12-09', 'present'),
(8, 2, '0000-00-00', 'present'),
(9, 3, '0000-00-00', 'present'),
(10, 8, '0000-00-00', 'present'),
(11, 21, '2020-09-09', 'present'),
(12, 4, '2020-01-12', 'present'),
(13, 6, '2020-01-12', 'present'),
(14, 7, '2020-01-12', 'absent'),
(15, 9, '2020-01-12', 'absent'),
(16, 10, '2020-01-12', 'present'),
(17, 11, '2020-01-12', 'absent'),
(18, 18, '2020-01-12', 'absent');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`) VALUES
(1, '1A'),
(2, '2A'),
(4, '4A'),
(5, '5c');

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `paid_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fees`
--

INSERT INTO `fees` (`id`, `student_id`, `amount`, `paid_date`) VALUES
(1, 2, 1234.00, '2025-04-02'),
(5, 4, 7678.00, '2025-04-25'),
(6, 1, 76.00, '2025-04-25'),
(8, NULL, 123.03, '2025-04-26'),
(9, 8, 1767.00, '2025-04-26'),
(10, NULL, 1237.00, '2025-04-26'),
(11, NULL, 1237.00, '2025-04-26'),
(12, NULL, 1.00, '2025-04-26'),
(13, NULL, 90.00, '2025-04-26'),
(14, 20, 190.00, '2025-04-26'),
(15, NULL, 10.00, '2025-04-26');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `subject` varchar(50) DEFAULT NULL,
  `grade` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `student_id`, `subject`, `grade`) VALUES
(1, NULL, 'maths', 78),
(2, 1, 'maths', 67),
(4, 3, 'sesotho', 23),
(5, 8, 'sesotho', 76),
(6, 18, 'maths', 89),
(7, 2, 'sesotho', 89),
(8, 2, 'CSA', 98),
(9, 2, 'English', 88),
(10, 8, 'sesotho', 78),
(11, 8, 'maths', 88),
(12, 8, 'CSA', 90),
(13, 8, 'English', 90),
(14, 11, 'sesotho', 100),
(15, 11, 'maths', 98),
(16, 11, 'CSA', 89),
(17, 11, 'English', 90),
(18, 20, 'sesotho', 100),
(19, 20, 'maths', 90),
(20, 20, 'CSA', 67),
(21, 20, 'English', 78);

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `invoice_date` datetime DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `status` enum('Pending','Paid','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `student_id`, `amount`, `invoice_date`, `due_date`, `status`) VALUES
(1, 5, 1237.00, '2025-04-26 13:02:29', '2023-01-23 00:00:00', 'Pending'),
(2, 10, 1237.00, '2025-04-26 13:25:55', '2029-12-31 00:00:00', 'Pending'),
(4, 11, 1237.00, '2025-04-26 14:39:21', '2323-12-12 00:00:00', 'Pending'),
(6, 11, 1237.00, '2025-04-26 14:39:48', '2323-12-12 00:00:00', 'Pending'),
(7, 18, 1237.00, '2025-04-26 14:57:52', '2322-12-12 00:00:00', 'Pending'),
(8, 10, 1237.00, '2025-04-27 19:39:24', '2222-12-12 00:00:00', 'Pending'),
(9, 10, 1237.00, '2025-04-27 19:43:42', '2222-12-12 00:00:00', 'Pending'),
(10, 10, 1237.00, '2025-04-27 19:44:13', '2222-12-12 00:00:00', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `sent_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `sent_at`) VALUES
(1, 1, 'bana fail why?????', '2025-04-25 21:21:35'),
(2, 1, 'time for marking soon', '2025-04-25 21:53:11'),
(3, 14, 'leave due soon', '2025-04-25 21:59:37'),
(4, 14, 'leave due soon,,finalise  reports', '2025-04-25 21:59:57'),
(5, 15, 'your child is behind with fees,make payment by 2025-09-08', '2025-04-25 22:04:34'),
(6, 15, 'note that  report will be uploaded with 24hrs', '2025-04-26 16:10:13'),
(7, 20, 'check your child\\\'s info', '2025-04-26 22:50:23');

-- --------------------------------------------------------

--
-- Table structure for table `parent_child_relationship`
--

CREATE TABLE `parent_child_relationship` (
  `parent_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parent_child_relationship`
--

INSERT INTO `parent_child_relationship` (`parent_id`, `child_id`) VALUES
(15, 17),
(15, 18),
(20, 21);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(89) NOT NULL,
  `phone` int(23) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `fee_status` enum('paid','unpaid') NOT NULL DEFAULT 'unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `email`, `phone`, `class_id`, `fee_status`) VALUES
(1, 'king', '', 0, NULL, 'unpaid'),
(2, 'nkopane matsaba', '', 0, 2, 'paid'),
(3, 'sthuzen', '', 0, 2, 'paid'),
(4, 'Sea Rose', '', 0, 1, 'paid'),
(5, 'Kim Berly', '', 0, 4, 'unpaid'),
(6, 'Sea Rose', '', 0, 1, 'paid'),
(7, 'lerato Nkone', '', 0, 1, 'paid'),
(8, 'thibos', '', 0, 2, 'paid'),
(9, 'thibos', '', 0, 1, 'paid'),
(10, 'student', '', 0, 1, 'unpaid'),
(11, 'seaya', '', 0, 1, 'unpaid'),
(18, 'thibos', 'thibi#@gmail.com', 52626262, 1, 'paid'),
(19, 'thibos', '', 0, 4, 'paid'),
(20, 'student2', '', 0, 4, 'paid'),
(21, 'student2', '', 0, 4, 'paid');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`) VALUES
(4, 'CSA'),
(3, 'english'),
(2, 'maths'),
(1, 'sesotho');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(89) NOT NULL,
  `subject` varchar(50) DEFAULT NULL,
  `class_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `name`, `email`, `subject`, `class_id`) VALUES
(1, 'kakapa pitso', 'kakapa@gmail.com', 'sesotho', 4),
(2, 'wiw', 'wiw@gmail.com', 'sesotho', 4);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher','student','parent') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'thabo', '$2y$10$gvChm2gKSX9mZh5PsgnD3.W/eRGF1rFjeX1j.cOUFxMQOD/sfS8wa', 'student'),
(2, 'johny@123', '$2y$10$0th9MyUU3d3scGGEDK92vOaMqpek5dZb1ibgfsIPU8kwzpqIAAYDG', 'teacher'),
(5, '12345', '$2y$10$/Su43r1kxe7e9TZIowkz6.TNHGJnq.D2QN4T.nZMNslgSNfFlriu.', 'teacher'),
(7, 'king', '$2y$10$qAFfQKov7RV5YvGSXZ06Me0KyzvVejqo5tkmae0ez1Gzuwwt1PyWy', 'student'),
(11, 'teacher', '$2y$10$gUsKneIBD5AxSJhUbonQBOY87LDIedY4ieGx2aIbO6QCZS2SugBFO', 'teacher'),
(13, 'admin', '$2y$10$9F7XC1oHvu4C4B9HWow3xuFDIxXyb0jvNkj0JkSlZmPQhtm23J54K', 'admin'),
(14, 'kakapa', '$2y$10$AnV8pH3PiNeMpA1kHF7P7eIebHDt2hVpi6y.kKOLU.bSBLTVE6u8e', 'teacher'),
(15, 'parent', '$2y$10$rM1ST.RmqOnbe0YNfIVrR.Ro4sO1am4RPJ8zcIyqvkEtwSXfYbgkC', 'parent'),
(17, 'seaya', '$2y$10$WCiekBNvcIIgc90TNSeSYOGMyZQiqnHVwSabVUx7RA7xPUJl2fJey', 'student'),
(18, 'thibos', 'default_password', 'student'),
(19, 'student', '$2y$10$f5hcvA1RPQF47ONRjcYqhO5SzecB3YSVXvVTQERavTE6WgE6nzXaq', 'student'),
(20, 'parent2', '$2y$10$sCFkQKJG/gT4iIApqR00u.DC.Uu49oHfd7rcKhNo5wtbgrvhj1NC2', 'parent'),
(21, 'student22', 'default_password', 'student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `parent_child_relationship`
--
ALTER TABLE `parent_child_relationship`
  ADD PRIMARY KEY (`parent_id`,`child_id`),
  ADD KEY `child_id` (`child_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `fees`
--
ALTER TABLE `fees`
  ADD CONSTRAINT `fees_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `parent_child_relationship`
--
ALTER TABLE `parent_child_relationship`
  ADD CONSTRAINT `parent_child_relationship_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `parent_child_relationship_ibfk_2` FOREIGN KEY (`child_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
