-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Apr 09, 2026 at 02:23 PM
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
  `name` varchar(100) NOT NULL,
  `image` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accreditations`
--

INSERT INTO `accreditations` (`id`, `name`, `image`) VALUES
(3, 'AICTE', '/ai-tools/assets/uploads/accreditations/69d3b04c371bd2.34273701_1775480908.webp'),
(4, 'UGC', '/ai-tools/assets/uploads/accreditations/69cf5e5a3b7948.15630662_1775197786.webp'),
(5, 'AIU', '/ai-tools/assets/uploads/accreditations/69cf5e321943d1.42713342_1775197746.webp'),
(7, 'NAAC A+', '/ai-tools/assets/uploads/accreditations/69cf72731c31f8.49781822_1775202931.webp'),
(8, 'NIRF', '/ai-tools/assets/uploads/accreditations/69d78a1126a5c6.26676299_1775733265.webp'),
(9, 'QS', '/ai-tools/assets/uploads/accreditations/69d78a1c6435a1.24225040_1775733276.webp'),
(10, 'NAAC A++', '/ai-tools/assets/uploads/accreditations/69d78b5dd0aeb6.76866809_1775733597.webp'),
(11, 'WES', '/ai-tools/assets/uploads/accreditations/69d78b8a166903.15954328_1775733642.webp');

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

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `name`, `display_name`, `slug`, `course_level`, `program_eligibility`, `course_duration`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'test course', 'Test course display name', 'test-course', 'UG', 'Test Eligibility\r\nTest Eligibility 2\r\nTest Eligibility 3', '3 Years', 0, '2026-04-03 07:38:15', '2026-04-09 11:07:38'),
(2, 'BA', NULL, 'ba', 'UG', 'Minimum 50% marks in Graduation from a recognized university', '3 Years', 1, '2026-04-09 11:28:41', '2026-04-09 11:28:41'),
(3, 'MCA', NULL, 'mca', 'PG', 'Minimum 50% marks in Graduation from a recognized university', '2 Years', 1, '2026-04-09 11:29:19', '2026-04-09 11:29:19'),
(4, 'MBA', NULL, 'mba', 'PG', 'Minimum 50% marks in Graduation from a recognized university', '2 Years', 1, '2026-04-09 11:30:06', '2026-04-09 11:30:06'),
(5, 'MA', NULL, 'ma', 'PG', 'Minimum 50% marks in Graduation from a recognized university', '2 Years', 1, '2026-04-09 11:30:32', '2026-04-09 11:30:32'),
(6, 'BCA', NULL, 'bca', 'UG', 'Minimum 50% marks in Graduation from a recognized university', '3 Years', 1, '2026-04-09 11:30:53', '2026-04-09 11:30:53');

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
(1, 'Mangalayatan University Online', NULL, 'mangalayatan-university-online', '/ai-tools/assets/uploads/images/69ce5d00e05e62.02842284_1775131904.webp', '/ai-tools/assets/uploads/certificates/69ce5d00e0ce43.13923990_1775131904.webp', 4.9, NULL, '2006', 'Private', 'Aligarh', '6 LPA', 1, 0, 1, 'Dedicated LMS with interactive tools\r\nGirl Child Benefit Scholarships\r\nCurriculum aligned with industry requirements', 'https://distanceeducationschool.com/mangalayatan-university/', 1, '2026-04-02 12:10:32', '2026-04-09 11:07:26'),
(2, 'Sikkim Manipal University Online', NULL, 'sikkim-manipal-university-online', '/ai-tools/assets/uploads/images/69d789eb847742.93022413_1775733227.webp', '/ai-tools/assets/uploads/certificates/69d789eb8591d2.37501355_1775733227.webp', 5.0, NULL, '1995', 'Private', 'Sikkim', NULL, 1, 1, 1, 'Strong Alumni Network\r\nMentorship & Feedback Guidance\r\nComprehensive Curriculum', 'https://distanceeducationschool.com/sikkim-manipal-university/', 1, '2026-04-09 11:13:47', '2026-04-09 11:13:47'),
(3, 'Lovely Professional University Distance', NULL, 'lovely-professional-university-distance', '/ai-tools/assets/uploads/images/69d78bade71285.82359102_1775733677.webp', '/ai-tools/assets/uploads/certificates/69d78bade82cc6.13116975_1775733677.webp', 5.0, 31, '2005', 'Private', 'Phagwara', '8 LPA', 1, 1, 1, 'Smart Digital Learning Ecosystem\r\nLearn on Your Schedule\r\nMerit-Based Scholarships', 'https://degree4u.com/university/lovely-professional-university-distance/', 1, '2026-04-09 11:21:17', '2026-04-09 11:21:31'),
(4, 'Amity University Online', NULL, 'amity-university-online', '/ai-tools/assets/uploads/images/69d78c5ade1129.52971076_1775733850.webp', '/ai-tools/assets/uploads/certificates/69d78c5adec491.47569236_1775733850.webp', 5.0, 32, '2005', 'Private', 'Noida', '3-6 LPA', 1, 1, 1, 'AI-Professor AMI\r\nAMIGO LMS Platform\r\n1:1 Industry Mentorship\r\nInnovative Workshops & Industry Visits', 'https://degree4u.com/university/amity-university-online/', 1, '2026-04-09 11:24:10', '2026-04-09 11:24:17'),
(5, 'Manipal University Online', NULL, 'manipal-university-online', '/ai-tools/assets/uploads/images/69d78cdc9255a0.33094677_1775733980.webp', '/ai-tools/assets/uploads/certificates/69d78cdc939087.23889349_1775733980.webp', 5.0, 58, '2011', 'Private', 'Jaipur', '5 LPA', 1, 0, 1, NULL, 'https://degree4u.com/university/manipal-university-online/', 1, '2026-04-09 11:25:02', '2026-04-09 11:26:20');

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
(22, 1, 3, NULL),
(23, 1, 5, NULL),
(24, 1, 7, NULL),
(25, 1, 4, NULL),
(29, 2, 3, NULL),
(30, 2, 7, NULL),
(31, 2, 4, NULL),
(32, 2, 8, NULL),
(33, 2, 9, NULL),
(40, 3, 3, NULL),
(41, 3, 10, NULL),
(42, 3, 8, NULL),
(43, 3, 9, NULL),
(44, 3, 4, NULL),
(45, 3, 11, NULL),
(52, 4, 3, NULL),
(53, 4, 7, NULL),
(54, 4, 8, NULL),
(55, 4, 9, NULL),
(56, 4, 4, NULL),
(57, 4, 11, NULL),
(58, 5, 3, NULL),
(59, 5, 7, NULL),
(60, 5, 8, NULL),
(61, 5, 9, NULL),
(62, 5, 4, NULL);

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
  `fees_discount` decimal(10,2) DEFAULT NULL,
  `course_rating` decimal(3,1) DEFAULT NULL,
  `course_specializations` text DEFAULT NULL,
  `brochure_file` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `university_courses`
--

INSERT INTO `university_courses` (`id`, `university_id`, `course_id`, `education_mode_id`, `academic_fees`, `fees_discount`, `course_rating`, `course_specializations`, `brochure_file`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 1, 2, 2, 50000.00, 2000.00, 4.0, 'Human Resource Management\r\nFinance\r\nMarketing\r\nSystems and Operations Management\r\nGeneral Management\r\nLogistics and Supply Chain Management', '/ai-tools/assets/uploads/brochures/69d791280be603.98342550_1775735080.pdf', 1, '2026-04-09 11:42:14', '2026-04-09 11:44:40'),
(3, 1, 6, 2, 30000.00, 2000.00, 5.0, 'UI/UX\r\nCloud Computing\r\nData Science\r\nNetwork and Cyber Security\r\nAnimation and VFX\r\nBlockchain\r\nWeb design and Security\r\nMultimedia / Animation and Gaming\r\nEthical Hacking\r\nDatabase Management System', '/ai-tools/assets/uploads/brochures/69d79415d21131.14914612_1775735829.pdf', 1, '2026-04-09 11:57:09', '2026-04-09 11:57:09'),
(4, 5, 6, 2, 70000.00, 5000.00, 5.0, NULL, NULL, 1, '2026-04-09 11:59:50', '2026-04-09 11:59:50'),
(5, 4, 4, 2, 200000.00, 10000.00, 5.0, 'Finance & Accounting Management\r\nMarketing & Sales Management\r\nHuman Resource Management\r\nData Science\r\nEntrepreneurship & Leadership Management\r\nInformation Technology Management\r\nInternational Business Management', NULL, 1, '2026-04-09 12:01:23', '2026-04-09 12:01:23'),
(6, 3, 3, 2, 80000.00, 15000.00, 4.5, NULL, NULL, 1, '2026-04-09 12:02:58', '2026-04-09 12:02:58'),
(7, 2, 5, 2, 90000.00, 3000.00, 4.9, NULL, NULL, 1, '2026-04-09 12:03:39', '2026-04-09 12:03:39');

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
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(5, 2);

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
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1);

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
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `university_accreditations`
--
ALTER TABLE `university_accreditations`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `university_courses`
--
ALTER TABLE `university_courses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
