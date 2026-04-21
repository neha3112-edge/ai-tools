-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 21, 2026 at 09:56 AM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u120175788_ai_tools_db`
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
(1, 'Rachit', 'admin@sode.com', '$2y$10$WqprT7QfaYgNkw5C6O0Dj.3kykRbxh988ajqy2urzqJgon1xWN0X2', 'admin', 1, '2026-04-02 07:55:16', '2026-04-14 12:10:49');

-- --------------------------------------------------------

--
-- Table structure for table `brochure_leads`
--

CREATE TABLE `brochure_leads` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `country_code` varchar(10) NOT NULL DEFAULT '+91',
  `phone` varchar(50) NOT NULL,
  `course` varchar(150) NOT NULL,
  `state` varchar(100) NOT NULL,
  `page_url` varchar(255) DEFAULT NULL,
  `user_ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brochure_leads`
--

INSERT INTO `brochure_leads` (`id`, `name`, `email`, `country_code`, `phone`, `course`, `state`, `page_url`, `user_ip`, `created_at`) VALUES
(1, 'rachit', 'test@gmail.com', '+91', '9876543211', 'BCA (UG)', 'Maharashtra', 'http://localhost/ai-tools/compare-universities.php', '127.0.0.1', '2026-04-10 07:36:16'),
(2, 'Rachit test 2', 'ecoewf@gmail.com', '+91', '9833836353', 'MBA (PG)', 'Assam', 'http://localhost/ai-tools/compare-universities.php', '127.0.0.1', '2026-04-10 10:57:23'),
(3, 'Rachit new test', 'mweoifn@gmail.com', '+91', '9878989898', 'MBA (PG)', 'Assam', 'http://localhost/ai-tools/compare-universities.php', '127.0.0.1', '2026-04-10 11:13:20'),
(4, 'Rachit final test brochurw', 'ewkjfn@gmail.com', '+91', '9866767676', 'MBA (PG)', 'Assam', 'http://localhost/ai-tools/compare-universities.php', '127.0.0.1', '2026-04-10 12:00:29'),
(5, 'ecoie test', 'doie@gmail.com', '+91', '6545654565', 'MBA (PG)', 'Assam', 'http://localhost/ai-tools/compare-universities.php', '127.0.0.1', '2026-04-10 12:04:18');

-- --------------------------------------------------------

--
-- Table structure for table `compare_unlock_leads`
--

CREATE TABLE `compare_unlock_leads` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(200) NOT NULL,
  `country_code` varchar(10) DEFAULT '+91',
  `phone` varchar(20) NOT NULL,
  `course` varchar(200) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `page_url` text DEFAULT NULL,
  `user_ip` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `compare_unlock_leads`
--

INSERT INTO `compare_unlock_leads` (`id`, `name`, `email`, `country_code`, `phone`, `course`, `state`, `page_url`, `user_ip`, `created_at`) VALUES
(1, 'test rachit', 'ewdoi@gmail.com', '+91', '1234567890', 'MBA (PG)', 'Assam', 'http://localhost/ai-tools/compare-universities.php', '127.0.0.1', '2026-04-16 11:49:23'),
(2, 'Rachit new testing', 'mweifn@gmail.com', '+91', '9827272622', 'MBA (PG)', 'Arunachal Pradesh', 'http://localhost/ai-tools/compare-universities.php', '127.0.0.1', '2026-04-16 11:52:49'),
(3, 'Rachit final test', 'ewiub@gmail.com', '+91', '9822828282', 'MBA (PG)', 'Assam', 'http://localhost/ai-tools/compare-universities.php', '127.0.0.1', '2026-04-16 12:08:22'),
(4, 'Rachit final testing', 'mweoif@gmail.com', '+91', '9823030303', 'MCA (PG)', 'Bihar', 'http://localhost/ai-tools/compare-universities.php', '127.0.0.1', '2026-04-16 12:40:45'),
(5, 'Rachit final test', 'dofn@gmail.com', '+91', '9822727272', 'BA (UG)', 'Assam', 'http://localhost/ai-tools/compare-universities.php', '127.0.0.1', '2026-04-16 14:12:33'),
(6, 'test rachit', 'enwuc@gmail.com', '+91', '982828282', 'MBA (PG)', 'Assam', 'http://localhost/ai-tools/compare-universities.php', '::1', '2026-04-16 16:22:38'),
(7, 'Shyam Sunder', 'shyam@edgetechnosoft.com', '+91', '9898767677', 'BA (UG)', 'Mizoram', 'https://degree4u.com/ai-tools/compare-universities.php', '2401:4900:8847:b9cc:99d:603a:2382:6911', '2026-04-16 11:28:50'),
(8, 'Rachit Aggarwal', 'rachitaggarwal1202@gmail.com', '+91', '1234567890', 'MBA (PG)', 'Uttar Pradesh', 'https://degree4u.com/ai-tools/compare-universities.php', '2405:201:403b:90c9:a71d:1e44:35f3:263f', '2026-04-16 11:33:30'),
(9, 'Rachit Aggarwal', 'rachitaggarwal1202@gmail.com', '+91', '65646599595', 'MBA (PG)', 'Uttar Pradesh', 'https://degree4u.com/ai-tools/compare-universities.php', '2405:201:403b:90c9:a71d:1e44:35f3:263f', '2026-04-16 11:45:25'),
(10, 'Rachit Aggarwal', 'rachitaggarwal1202@gmail.com', '+91', '7678559902', 'MBA (PG)', 'Uttar Pradesh', 'https://degree4u.com/ai-tools/compare-universities.php', '2405:201:403b:90c9:a71d:1e44:35f3:263f', '2026-04-16 11:47:36'),
(11, 'Shyam Sunder', 'shyam@edgetechnosoft.com', '+91', '9999999999', 'BA (UG)', 'Nagaland', 'https://degree4u.com/ai-tools/compare-universities.php', '2401:4900:8847:b9cc:b1ab:c3c8:fcb6:b3f6', '2026-04-17 06:26:16'),
(12, 'Shyam Sunder', 'shyam@edgetechnosoft.com', '+91', '9898787879', 'BCA (UG)', 'Nagaland', 'https://degree4u.com/ai-tools/compare-universities.php', '2401:4900:8845:15f9:bcf7:6587:dbc8:3bd3', '2026-04-20 09:01:47'),
(13, 'Shyam Sunder', 'shyam@edgetechnosoft.com', '+91', '9898767675', 'BA (UG)', 'Mizoram', 'https://degree4u.com/ai-tools/compare-universities.php', '2401:4900:8845:15f9:310d:3209:2c6e:d5ce', '2026-04-21 08:38:52');

-- --------------------------------------------------------

--
-- Table structure for table `counseling_leads`
--

CREATE TABLE `counseling_leads` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `country_code` varchar(10) NOT NULL DEFAULT '+91',
  `phone` varchar(50) NOT NULL,
  `course` varchar(255) NOT NULL,
  `state` varchar(100) NOT NULL,
  `uni_name` varchar(255) DEFAULT NULL,
  `page_url` text DEFAULT NULL,
  `user_ip` varchar(45) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `counseling_leads`
--

INSERT INTO `counseling_leads` (`id`, `name`, `email`, `country_code`, `phone`, `course`, `state`, `uni_name`, `page_url`, `user_ip`, `created_at`) VALUES
(1, 'Rachit test counseling amity', 'wenf@gmail.com', '+91', '93474847474', 'MBA (PG)', 'Assam', 'Amity University Online', 'http://localhost/ai-tools/compare-universities.php', '127.0.0.1', '2026-04-10 12:09:27'),
(2, 'rachit test counseling', 'dlkefn@gmail.com', '+91', '8373837377', 'MCA (PG)', 'Bihar', 'Sikkim Manipal University Online', 'http://localhost/ai-tools/compare-universities.php', '127.0.0.1', '2026-04-10 12:10:23'),
(3, 'Rachit new test main counseling', 'wenfown@gmail.com', '+91', '9399339939', 'MBA (PG)', 'Assam', 'General Counseling', 'http://localhost/ai-tools/compare-universities.php', '127.0.0.1', '2026-04-10 12:39:16');

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
-- Table structure for table `scholarship_leads`
--

CREATE TABLE `scholarship_leads` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `country_code` varchar(10) NOT NULL DEFAULT '+91',
  `phone` varchar(50) NOT NULL,
  `course` varchar(255) NOT NULL,
  `state` varchar(100) NOT NULL,
  `uni_name` varchar(255) DEFAULT NULL,
  `page_url` text DEFAULT NULL,
  `user_ip` varchar(45) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarship_leads`
--

INSERT INTO `scholarship_leads` (`id`, `name`, `email`, `country_code`, `phone`, `course`, `state`, `uni_name`, `page_url`, `user_ip`, `created_at`) VALUES
(1, 'Rachit test', 'mwefoinw@gmail.com', '+91', '9876543211', 'MBA (PG)', 'Arunachal Pradesh', 'Amity University Online', 'http://localhost/ai-tools/compare-universities.php', '127.0.0.1', '2026-04-10 11:33:12'),
(2, 'Rachit test claim', 'kewjfnoi@gmail.com', '+91', '9363836346', 'MBA (PG)', 'Assam', 'Amity University Online', 'http://localhost/ai-tools/compare-universities.php', '127.0.0.1', '2026-04-10 12:01:51'),
(3, 'Rachit test claim 2', 'weofn@gmail.com', '+91', '9823838383', 'MBA (PG)', 'Assam', 'Sikkim Manipal University Online', 'http://localhost/ai-tools/compare-universities.php', '127.0.0.1', '2026-04-10 12:03:09');

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
  `university_type_id` int(11) DEFAULT NULL,
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

INSERT INTO `universities` (`id`, `name`, `display_name`, `slug`, `image`, `sample_certificate`, `rating`, `nirf_ranking`, `year_of_establishment`, `university_type_id`, `campus_location`, `avg_placement_package`, `placement_assistance`, `emi_facility`, `scholarship`, `key_advantages`, `view_university_link`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Mangalayatan University Online', NULL, 'mangalayatan-university-online', '/ai-tools/assets/uploads/images/69e07781902268.69807336_1776318337.webp', '/ai-tools/assets/uploads/certificates/69ce5d00e0ce43.13923990_1775131904.webp', 4.9, NULL, '2006', 2, 'Aligarh', '6 LPA', 1, 0, 1, 'Dedicated LMS with interactive tools\r\nGirl Child Benefit Scholarships\r\nCurriculum aligned with industry requirements', 'https://distanceeducationschool.com/mangalayatan-university/', 1, '2026-04-02 12:10:32', '2026-04-16 05:45:37'),
(2, 'Sikkim Manipal University Online', NULL, 'sikkim-manipal-university-online', '/ai-tools/assets/uploads/images/69e0777a3ef492.58836449_1776318330.webp', '/ai-tools/assets/uploads/certificates/69d789eb8591d2.37501355_1775733227.webp', 5.0, NULL, '1995', 2, 'Sikkim', NULL, 1, 1, 1, 'Strong Alumni Network\r\nMentorship & Feedback Guidance\r\nComprehensive Curriculum', 'https://distanceeducationschool.com/sikkim-manipal-university/', 1, '2026-04-09 11:13:47', '2026-04-16 05:45:30'),
(3, 'Lovely Professional University Distance', NULL, 'lovely-professional-university-distance', '/ai-tools/assets/uploads/images/69e0777397d535.34927888_1776318323.webp', '/ai-tools/assets/uploads/certificates/69d78bade82cc6.13116975_1775733677.webp', 5.0, 31, '2005', 2, 'Phagwara', '8 LPA', 1, 1, 1, 'Smart Digital Learning Ecosystem\r\nLearn on Your Schedule\r\nMerit-Based Scholarships', 'https://degree4u.com/university/lovely-professional-university-distance/', 1, '2026-04-09 11:21:17', '2026-04-16 05:45:23'),
(4, 'Amity University Online', NULL, 'amity-university', '/ai-tools/assets/uploads/images/69e0776d435de8.46610788_1776318317.webp', '/ai-tools/assets/uploads/certificates/69d78c5adec491.47569236_1775733850.webp', 5.0, 32, '2005', 2, 'Noida', '3-6 LPA', 1, 1, 1, 'AI-Professor AMI\r\nAMIGO LMS Platform\r\n1:1 Industry Mentorship\r\nInnovative Workshops & Industry Visits', 'https://degree4u.com/university/amity-university-online/', 1, '2026-04-09 11:24:10', '2026-04-16 05:45:17'),
(5, 'Manipal University Online', NULL, 'manipal-university-online', '/ai-tools/assets/uploads/images/69e077657f2367.51084653_1776318309.webp', '/ai-tools/assets/uploads/certificates/69d78cdc939087.23889349_1775733980.webp', 5.0, 58, '2011', 2, 'Jaipur', '5 LPA', 1, 0, 1, NULL, 'https://degree4u.com/university/manipal-university-online/', 1, '2026-04-09 11:25:02', '2026-04-16 05:45:09');

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
(85, 5, 3, NULL),
(86, 5, 5, NULL),
(87, 5, 7, NULL),
(88, 5, 10, NULL),
(89, 5, 8, NULL),
(90, 5, 9, NULL),
(91, 5, 4, NULL),
(92, 4, 3, NULL),
(93, 4, 5, NULL),
(94, 4, 7, NULL),
(95, 4, 8, NULL),
(96, 4, 9, NULL),
(97, 4, 4, NULL),
(98, 4, 11, NULL),
(99, 3, 3, NULL),
(100, 3, 5, NULL),
(101, 3, 10, NULL),
(102, 3, 8, NULL),
(103, 3, 9, NULL),
(104, 3, 4, NULL),
(105, 3, 11, NULL),
(106, 2, 3, NULL),
(107, 2, 5, NULL),
(108, 2, 7, NULL),
(109, 2, 8, NULL),
(110, 2, 9, NULL),
(111, 2, 4, NULL),
(112, 1, 3, NULL),
(113, 1, 5, NULL),
(114, 1, 7, NULL),
(115, 1, 10, NULL),
(116, 1, 4, NULL);

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
(5, 4, 4, 2, 200000.00, 10000.00, 5.0, 'Finance & Accounting Management\r\nMarketing & Sales Management\r\nHuman Resource Management\r\nData Science\r\nEntrepreneurship & Leadership Management\r\nInformation Technology Management\r\nInternational Business Management', '/ai-tools/assets/uploads/brochures/69d8956320a935.77783403_1775801699.pdf', 1, '2026-04-09 12:01:23', '2026-04-10 06:14:59'),
(6, 3, 3, 1, 80000.00, 15000.00, 4.5, NULL, NULL, 1, '2026-04-09 12:02:58', '2026-04-09 12:34:21'),
(7, 2, 4, 2, 90000.00, 3000.00, 4.9, NULL, NULL, 1, '2026-04-09 12:03:39', '2026-04-10 05:50:03'),
(8, 2, 4, 1, 0.00, 0.00, 0.0, NULL, NULL, 1, '2026-04-10 05:49:46', '2026-04-10 05:49:46'),
(9, 1, 4, 2, 30000.00, 2000.00, 4.0, 'Test Spec.', NULL, 1, '2026-04-16 10:20:48', '2026-04-16 10:20:48');

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
(5, 2),
(1, 3),
(2, 3),
(3, 3),
(4, 3),
(5, 3);

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
(5, 1),
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `university_types`
--

CREATE TABLE `university_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(150) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `university_types`
--

INSERT INTO `university_types` (`id`, `type_name`, `is_active`) VALUES
(1, 'Government', 1),
(2, 'Private', 1),
(3, 'Deemed', 1),
(4, 'Autonomous', 1);

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
-- Indexes for table `brochure_leads`
--
ALTER TABLE `brochure_leads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `compare_unlock_leads`
--
ALTER TABLE `compare_unlock_leads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `counseling_leads`
--
ALTER TABLE `counseling_leads`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `scholarship_leads`
--
ALTER TABLE `scholarship_leads`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `university_types`
--
ALTER TABLE `university_types`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `brochure_leads`
--
ALTER TABLE `brochure_leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `compare_unlock_leads`
--
ALTER TABLE `compare_unlock_leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `counseling_leads`
--
ALTER TABLE `counseling_leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- AUTO_INCREMENT for table `scholarship_leads`
--
ALTER TABLE `scholarship_leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `universities`
--
ALTER TABLE `universities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `university_accreditations`
--
ALTER TABLE `university_accreditations`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `university_courses`
--
ALTER TABLE `university_courses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `university_types`
--
ALTER TABLE `university_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
