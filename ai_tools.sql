-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 02, 2026 at 03:02 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ai_tools`
--

-- --------------------------------------------------------

--
-- Table structure for table `accreditations`
--

CREATE TABLE `accreditations` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accreditations`
--

INSERT INTO `accreditations` (`id`, `name`) VALUES
(3, 'AICTE'),
(5, 'AIU'),
(2, 'NAAC'),
(4, 'NIRF');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','admin') NOT NULL DEFAULT 'admin',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Rachit', 'admin@sode.com', '$2y$10$WqprT7QfaYgNkw5C6O0Dj.3kykRbxh988ajqy2urzqJgon1xWN0X2', 'superadmin', 1, '2026-04-02 07:55:16', '2026-04-02 08:56:52');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `course_level` enum('UG','PG') NOT NULL,
  `program_eligibility` text DEFAULT NULL,
  `course_duration` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `education_modes`
--

CREATE TABLE `education_modes` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `mode_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `education_modes`
--

INSERT INTO `education_modes` (`id`, `mode_name`) VALUES
(1, 'Distance'),
(3, 'Distance & Online'),
(2, 'Online');

-- --------------------------------------------------------

--
-- Table structure for table `exam_modes`
--

CREATE TABLE `exam_modes` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `mode_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_modes`
--

INSERT INTO `exam_modes` (`id`, `mode_name`) VALUES
(2, 'Offline'),
(1, 'Online');

-- --------------------------------------------------------

--
-- Table structure for table `universities`
--

CREATE TABLE `universities` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `sample_certificate` varchar(500) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT NULL,
  `nirf_ranking` smallint(5) UNSIGNED DEFAULT NULL,
  `year_of_establishment` year(4) DEFAULT NULL,
  `university_type` enum('Government','Private','Deemed','Autonomous') DEFAULT NULL,
  `campus_location` varchar(255) DEFAULT NULL,
  `avg_placement_package` varchar(100) DEFAULT NULL,
  `placement_assistance` tinyint(1) NOT NULL DEFAULT 0,
  `emi_facility` tinyint(1) NOT NULL DEFAULT 0,
  `scholarship` tinyint(1) NOT NULL DEFAULT 0,
  `key_advantages` text DEFAULT NULL,
  `view_university_link` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `universities`
--

INSERT INTO `universities` (`id`, `name`, `display_name`, `slug`, `image`, `sample_certificate`, `rating`, `nirf_ranking`, `year_of_establishment`, `university_type`, `campus_location`, `avg_placement_package`, `placement_assistance`, `emi_facility`, `scholarship`, `key_advantages`, `view_university_link`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Test University', 'Test Display Name', 'test-university', '/ai-tools/assets/uploads/images/69ce5d00e05e62.02842284_1775131904.webp', '/ai-tools/assets/uploads/certificates/69ce5d00e0ce43.13923990_1775131904.webp', 5.0, 20, '2005', 'Private', 'Noida UP', '20LPA', 1, 1, 0, 'Test Advantages\r\nTest Advantage 1\r\nTest Advantage  2', 'https://distanceeducationschool.com/mangalayatan-university/', 1, '2026-04-02 12:10:32', '2026-04-02 12:11:44');

-- --------------------------------------------------------

--
-- Table structure for table `university_accreditations`
--

CREATE TABLE `university_accreditations` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `university_id` int(10) UNSIGNED NOT NULL,
  `accreditation_id` smallint(5) UNSIGNED NOT NULL,
  `image` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `university_accreditations`
--

INSERT INTO `university_accreditations` (`id`, `university_id`, `accreditation_id`, `image`) VALUES
(7, 1, 3, NULL),
(8, 1, 2, NULL),
(9, 1, 4, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `university_courses`
--

CREATE TABLE `university_courses` (
  `id` int(10) UNSIGNED NOT NULL,
  `university_id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `education_mode_id` tinyint(3) UNSIGNED NOT NULL,
  `academic_fees` decimal(10,2) DEFAULT NULL,
  `fees_discount` decimal(5,2) DEFAULT NULL,
  `course_rating` decimal(3,1) DEFAULT NULL,
  `brochure_file` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `university_education_modes`
--

CREATE TABLE `university_education_modes` (
  `university_id` int(10) UNSIGNED NOT NULL,
  `education_mode_id` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `university_education_modes`
--

INSERT INTO `university_education_modes` (`university_id`, `education_mode_id`) VALUES
(1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `university_exam_modes`
--

CREATE TABLE `university_exam_modes` (
  `university_id` int(10) UNSIGNED NOT NULL,
  `exam_mode_id` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `university_exam_modes`
--

INSERT INTO `university_exam_modes` (`university_id`, `exam_mode_id`) VALUES
(1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accreditations`
--
ALTER TABLE `accreditations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_course_slug` (`slug`),
  ADD KEY `idx_course_level` (`course_level`),
  ADD KEY `idx_course_active` (`is_active`);

--
-- Indexes for table `education_modes`
--
ALTER TABLE `education_modes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mode_name` (`mode_name`);

--
-- Indexes for table `exam_modes`
--
ALTER TABLE `exam_modes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mode_name` (`mode_name`);

--
-- Indexes for table `universities`
--
ALTER TABLE `universities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_uni_slug` (`slug`),
  ADD KEY `idx_uni_active` (`is_active`),
  ADD KEY `idx_uni_nirf` (`nirf_ranking`);

--
-- Indexes for table `university_accreditations`
--
ALTER TABLE `university_accreditations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_uni_accr` (`university_id`,`accreditation_id`),
  ADD KEY `accreditation_id` (`accreditation_id`);

--
-- Indexes for table `university_courses`
--
ALTER TABLE `university_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_uni_course_mode` (`university_id`,`course_id`,`education_mode_id`),
  ADD KEY `idx_uc_course` (`course_id`),
  ADD KEY `idx_uc_mode` (`education_mode_id`),
  ADD KEY `idx_uc_active` (`is_active`);

--
-- Indexes for table `university_education_modes`
--
ALTER TABLE `university_education_modes`
  ADD PRIMARY KEY (`university_id`,`education_mode_id`),
  ADD KEY `education_mode_id` (`education_mode_id`);

--
-- Indexes for table `university_exam_modes`
--
ALTER TABLE `university_exam_modes`
  ADD PRIMARY KEY (`university_id`,`exam_mode_id`),
  ADD KEY `exam_mode_id` (`exam_mode_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accreditations`
--
ALTER TABLE `accreditations`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `education_modes`
--
ALTER TABLE `education_modes`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `exam_modes`
--
ALTER TABLE `exam_modes`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `universities`
--
ALTER TABLE `universities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `university_accreditations`
--
ALTER TABLE `university_accreditations`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `university_courses`
--
ALTER TABLE `university_courses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `university_accreditations`
--
ALTER TABLE `university_accreditations`
  ADD CONSTRAINT `university_accreditations_ibfk_1` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `university_accreditations_ibfk_2` FOREIGN KEY (`accreditation_id`) REFERENCES `accreditations` (`id`);

--
-- Constraints for table `university_courses`
--
ALTER TABLE `university_courses`
  ADD CONSTRAINT `university_courses_ibfk_1` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `university_courses_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `university_courses_ibfk_3` FOREIGN KEY (`education_mode_id`) REFERENCES `education_modes` (`id`);

--
-- Constraints for table `university_education_modes`
--
ALTER TABLE `university_education_modes`
  ADD CONSTRAINT `university_education_modes_ibfk_1` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `university_education_modes_ibfk_2` FOREIGN KEY (`education_mode_id`) REFERENCES `education_modes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `university_exam_modes`
--
ALTER TABLE `university_exam_modes`
  ADD CONSTRAINT `university_exam_modes_ibfk_1` FOREIGN KEY (`university_id`) REFERENCES `universities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `university_exam_modes_ibfk_2` FOREIGN KEY (`exam_mode_id`) REFERENCES `exam_modes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
