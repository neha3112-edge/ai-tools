-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 18, 2026 at 09:22 AM
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
(11, 'WES', '/ai-tools/assets/uploads/accreditations/69d78b8a166903.15954328_1775733642.webp'),
(12, 'THE', '/ai-tools/assets/uploads/accreditations/6a311ff058eee1.96871744_1781604336.webp'),
(13, 'NAB', '/ai-tools/assets/uploads/accreditations/6a31202c2d5143.02332566_1781604396.webp'),
(14, 'WURI', '/ai-tools/assets/uploads/accreditations/6a3229e7709ea0.24768469_1781672423.webp'),
(15, 'IIRF', '/ai-tools/assets/uploads/accreditations/6a322ad846baf9.05699704_1781672664.webp');

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
(6, 'Rachit final test', 'emnrf3@gmail.com', '+91', '9364647622', 'BBA (UG)', 'Arunachal Pradesh', 'http://localhost/ai-tools/compare-universities.php', '127.0.0.1', '2026-05-12 09:50:07');

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
(15, 'Rachit final test', 'nfrfui@gmail.com', '+91', '9376446422', 'BBA (UG)', 'Assam', 'http://localhost/ai-tools/compare-universities.php', '127.0.0.1', '2026-05-12 15:18:49');

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
(4, 'Rachit final test', 'cern@gmail.com', '+91', '9335533222', 'BBA (UG)', 'Arunachal Pradesh', 'Shoolini University Online', 'http://localhost/ai-tools/compare-universities.php', '127.0.0.1', '2026-05-12 09:50:48');

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
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `name`, `display_name`, `slug`, `course_level`, `is_active`, `created_at`, `updated_at`) VALUES
(9, 'Master of Business Administration', 'MBA', 'master-of-business-administration', 'PG', 1, '2026-06-16 05:34:15', '2026-06-16 05:34:15'),
(10, 'Master of Computer Applications', 'MCA', 'master-of-computer-applications', 'PG', 1, '2026-06-16 05:53:12', '2026-06-16 05:53:12'),
(11, 'Master of Business Administration Dual', 'MBA Dual', 'master-of-business-administration-dual', 'PG', 1, '2026-06-16 06:46:54', '2026-06-16 06:46:54'),
(12, 'Master of Computer Applications Collaboration', 'MCA Collaboration', 'master-of-computer-applications-collaboration', 'PG', 1, '2026-06-16 07:03:59', '2026-06-16 07:03:59'),
(13, 'Master of Commerce', 'MCOM', 'master-of-commerce', 'PG', 1, '2026-06-16 07:07:20', '2026-06-16 07:07:20'),
(14, 'Master of Arts in Journalism and Mass Communication', 'MAJMC', 'master-of-arts-in-journalism-and-mass-communication', 'PG', 1, '2026-06-16 07:31:32', '2026-06-16 07:31:32'),
(15, 'Master of Arts  PPG', 'MA PPG', 'master-of-arts-ppg', 'PG', 1, '2026-06-16 07:39:13', '2026-06-16 10:24:24'),
(16, 'Master of Science Data Science', 'MSC Data Science', 'master-of-science-data-sci', 'PG', 1, '2026-06-16 07:50:05', '2026-06-16 10:14:49'),
(17, 'Bachelor of Arts', 'BA', 'bachelor-of-arts', 'UG', 1, '2026-06-16 07:54:47', '2026-06-16 07:54:47'),
(18, 'Bachelor of Commerce', 'BCOM', 'bachelor-of-commerce', 'UG', 1, '2026-06-16 08:43:22', '2026-06-16 08:43:22'),
(19, 'Bachelor of Business Administration', 'BBA', 'bachelor-of-business-administration', 'UG', 1, '2026-06-16 08:51:02', '2026-06-16 08:51:02'),
(20, 'Bachelor of Computer Applications', 'BCA', 'bachelor-of-computer-applications', 'UG', 1, '2026-06-16 09:10:58', '2026-06-16 09:10:58'),
(21, 'Bachelor of Computer Applications Collab', 'BCA Collab', 'bachelor-of-computer-applications-collab', 'UG', 1, '2026-06-16 09:16:17', '2026-06-16 09:16:17'),
(22, 'Bachelor of Arts JMC', 'BA JMC', 'bachelor-of-arts-jmc', 'UG', 1, '2026-06-16 09:24:22', '2026-06-16 10:23:55'),
(23, 'Bachelor of Commerce (Hons)', 'BCOM (Hons)', 'bachelor-of-commerce-hons', 'UG', 1, '2026-06-16 11:09:18', '2026-06-17 09:54:21'),
(24, 'Master of Business Administration MBA Premium', 'MBA Premium', 'master-of-business-administration-mba-premium', 'PG', 1, '2026-06-16 11:28:23', '2026-06-16 11:28:23'),
(25, 'Master of Arts in English', 'MA English', 'master-of-arts-english', 'PG', 1, '2026-06-16 11:57:43', '2026-06-17 06:24:35'),
(26, 'Master of Arts Sociology', 'MA Sociology', 'master-of-arts-sociology', 'PG', 1, '2026-06-16 12:29:48', '2026-06-17 06:23:01'),
(27, 'Master of Arts in Political Science', 'MA Political Science', 'master-of-arts-pol-sci', 'PG', 1, '2026-06-17 05:30:59', '2026-06-17 06:23:53'),
(28, 'Master of Arts in Sociology', 'MA Sociology', 'master-of-arts-sociolo', 'PG', 1, '2026-06-17 05:33:12', '2026-06-17 06:22:14'),
(29, 'Master of Arts in Economics', 'MA Economics', 'master-of-arts-in-economics', 'PG', 1, '2026-06-17 06:15:31', '2026-06-17 06:15:31'),
(30, 'Master of Science in Maths', 'MSC Maths', 'master-of-science-maths', 'PG', 1, '2026-06-17 06:18:20', '2026-06-17 06:26:02'),
(31, 'Master of Arts History', 'MA History', 'master-of-arts-history', 'PG', 1, '2026-06-17 07:21:52', '2026-06-17 07:21:52'),
(32, 'Diploma in Business Administration', 'DBA', 'diploma-in-business-administration', 'UG', 1, '2026-06-17 07:45:00', '2026-06-17 07:45:00'),
(33, 'Diploma in Computer Applications', 'DCA', 'diploma-in-computer-applications', 'UG', 1, '2026-06-17 07:45:40', '2026-06-17 07:45:40'),
(34, 'Master of Business Administration Plus', 'MBA Plus', 'master-of-business-administration-plus', 'PG', 1, '2026-06-17 08:53:58', '2026-06-17 08:53:58'),
(35, 'Master of Arts in Education', 'MA Education', 'master-of-arts-in-education', 'PG', 1, '2026-06-17 09:06:09', '2026-06-17 09:06:09'),
(36, 'Master of Arts in Public Administration', 'MA Public Administration', 'master-of-arts-in-public-administration', 'PG', 1, '2026-06-17 09:07:47', '2026-06-17 09:07:47'),
(37, 'Master of Arts in Mass Communication', 'MA Mass Communication', 'master-of-arts-in-mass-communication', 'PG', 1, '2026-06-17 09:36:08', '2026-06-17 09:36:08'),
(38, 'BBA Business Analytics KPMG', 'BBA Business Analytics KPMG', 'bba-business-analytics-kpmg', 'UG', 1, '2026-06-17 10:44:01', '2026-06-17 10:55:14'),
(39, 'MBA Business Analytics KPMG', 'MBA Business Analytics KPMG', 'mba-business-analytics-kpmg', 'PG', 1, '2026-06-17 10:54:28', '2026-06-17 10:54:28'),
(40, 'Master of Arts in Islamic', 'MA Islamic', 'master-of-arts-in-islamic', 'PG', 1, '2026-06-17 11:20:59', '2026-06-17 11:20:59'),
(41, 'Master of Arts in Political', 'MA Political', 'master-of-arts-in-political', 'PG', 1, '2026-06-17 11:21:37', '2026-06-17 11:21:37'),
(42, 'MBA digital finance& accounting analytics', 'MBA digital finance& accounting analytics', 'mba-digital-finance-accounting-analytics', 'PG', 1, '2026-06-17 12:19:49', '2026-06-17 12:19:49'),
(43, 'MBA international finance  ACCA', 'MBA international finance  ACCA', 'mba-international-finance-acca', 'PG', 1, '2026-06-17 12:20:02', '2026-06-17 12:20:02'),
(44, 'Master of Commerce in Public Accounting', 'MCom Public Accounting', 'master-of-commerce-in-public-accounting', 'PG', 1, '2026-06-17 12:47:37', '2026-06-17 12:47:37'),
(45, 'MBA Data Science and Analytics', 'MBA Data Science and Analytics', 'mba-data-science-and-analytics', 'PG', 1, '2026-06-17 12:54:53', '2026-06-17 12:54:53'),
(46, 'Master of Arts', 'MA', 'master-of-arts', 'PG', 1, '2026-06-18 05:38:23', '2026-06-18 05:38:23'),
(47, 'Bachelor of Computer Applications Computer Science', 'BCA Computer Science', 'bachelor-of-computer-applications-computer-science', 'UG', 1, '2026-06-18 06:15:42', '2026-06-18 06:15:42'),
(48, 'Bachelor of Computer Applications data Science', 'BCA  data Science', 'bachelor-of-computer-applications-data-science', 'UG', 1, '2026-06-18 06:16:43', '2026-06-18 06:16:43'),
(49, 'Bachelor of Computer Applications AI', 'BCA AI', 'bachelor-of-computer-applications-ai', 'UG', 1, '2026-06-18 06:18:08', '2026-06-18 06:18:19'),
(50, 'Bachelor of Computer Applications Cyber', 'BCA Cyber', 'bachelor-of-computer-applications-cyber', 'UG', 1, '2026-06-18 06:19:29', '2026-06-18 06:19:29'),
(51, 'Bachelor of Computer Applications cloud comp', 'BCA Cloud Comp', 'bachelor-of-computer-applications-cloud-comp', 'UG', 1, '2026-06-18 06:20:11', '2026-06-18 06:20:11'),
(52, 'Master of Business Administration in Human Resource Management', 'MBA Human Resource Management', 'master-of-business-administration-in-human-resource-management', 'PG', 1, '2026-06-18 06:30:37', '2026-06-18 06:30:37'),
(53, 'Master of Business Administration Finance', 'MBA Finance', 'master-of-business-administration-finance', 'PG', 1, '2026-06-18 06:33:06', '2026-06-18 06:33:06'),
(54, 'Master of Business Administration Marketing', 'MBA Marketing', 'master-of-business-administration-marketing', 'PG', 1, '2026-06-18 06:34:11', '2026-06-18 06:34:11'),
(55, 'Master of Business Administration General', 'MBA  General', 'master-of-business-administration-general', 'PG', 1, '2026-06-18 06:40:03', '2026-06-18 06:40:03'),
(56, 'Master of Business Administration in International Finance (ACCA)', 'MBA in International Finance (ACCA)', 'master-of-business-administration-in-international-finance-acca', 'PG', 1, '2026-06-18 06:47:24', '2026-06-18 06:47:24'),
(57, 'Master of Commerce Association of Chartered Certified Accountants', 'M.Com Association of Chartered Certified Accountants', 'master-of-commerce-association-of-chartered-certified-accountants', 'PG', 1, '2026-06-18 06:52:55', '2026-06-18 06:52:55'),
(58, 'Bachelor of Commerce with Association of Chartered Certified Accountants', 'B.Com Association of Chartered Certified Accountants', 'bachelor-of-commerce-with-association-of-chartered-certified-accountants', 'PG', 1, '2026-06-18 06:53:47', '2026-06-18 06:53:47'),
(59, 'Master of Computer Applications Computer Science Information Technology', 'MCA Computer Science Information Technology', 'master-of-computer-applications-computer-science-information-technology', 'PG', 1, '2026-06-18 06:56:07', '2026-06-18 06:56:07'),
(60, 'Master of Computer Applications Data analytics', 'MCA Data analytics', 'master-of-computer-applications-data-analytics', 'PG', 1, '2026-06-18 06:57:13', '2026-06-18 06:57:13'),
(61, 'Master of Computer Applications Cyber Security', 'MCA Cyber Security', 'master-of-computer-applications-cyber-security', 'PG', 1, '2026-06-18 06:58:05', '2026-06-18 06:58:05');

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
(4, 'Rachit final test', 'mrfui@gmail.com', '+91', '9447645784', 'BBA (UG)', 'Arunachal Pradesh', 'Shoolini University Online', 'http://localhost/ai-tools/compare-universities.php', '127.0.0.1', '2026-05-12 09:49:31');

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
(7, 'Amity Univesity Online', NULL, 'amity-univesity-online', '/ai-tools/assets/uploads/images/6a30e00406e917.85074817_1781587972.webp', '/ai-tools/assets/uploads/certificates/6a30e004070175.31229441_1781587972.jpg', 4.5, 32, '2009', 2, 'Noida, Uttar Pradesh', '5 LPA', 1, 1, 1, 'Strong Alumni Network\r\nMentorship & Feedback Guidance\r\nComprehensive Curriculum\r\nKnown for a large campus, industry exposure, and global tie-ups', 'https://distanceeducationschool.com/amity-university/', 1, '2026-06-16 05:32:00', '2026-06-16 05:32:52'),
(8, 'Shoolini University Online', NULL, 'shoolini-university-online', '/ai-tools/assets/uploads/images/6a31241f210780.24399239_1781605407.webp', '/ai-tools/assets/uploads/certificates/6a31241f212b47.74352839_1781605407.webp', NULL, 69, '2009', 2, 'Solan, Himachal Pradesh', NULL, 0, 1, 1, '1st Pay-After-Placement Options\r\nCareer-Focused Learning\r\nTop Faculty & Mentors\r\nBest Learning Platform\r\nGlobal Opportunities', 'https://distanceeducationschool.com/Shoolini-university/', 1, '2026-06-16 10:23:27', '2026-06-16 10:50:48'),
(9, 'Andra University Online', NULL, 'andra-university-online', '/ai-tools/assets/uploads/images/6a313f9e273ff0.76053206_1781612446.webp', '/ai-tools/assets/uploads/certificates/6a313f9e2758e5.15442988_1781612446.webp', NULL, 23, '1926', 1, 'Visakhapatnam, Andhra Pradesh', NULL, 0, 0, 0, NULL, 'https://distanceeducationschool.com/andhra-university/', 1, '2026-06-16 12:20:46', '2026-06-16 12:20:46'),
(10, 'Sikkim Manipal University', NULL, 'sikkim-manipal-university', '/ai-tools/assets/uploads/images/6a322b8c91edc6.35341895_1781672844.png', '/ai-tools/assets/uploads/certificates/6a322b8c920d39.74308042_1781672844.webp', 4.3, NULL, '1995', 2, 'Sikkim Gangtok', '6 LPA', 1, 1, 1, 'Quality Education \r\nAttractive Scholarships\r\nVast alumni network\r\nExperienced faculty \r\ncomprehensive curriculum', 'https://distanceeducationschool.com/sikkim-manipal-university/', 1, '2026-06-16 13:01:59', '2026-06-17 05:07:24'),
(11, 'Manipal University Online', NULL, 'manipal-university-online', '/ai-tools/assets/uploads/images/6a3233f46fda09.63913276_1781674996.png', '/ai-tools/assets/uploads/certificates/6a3233f46ff395.64478985_1781674996.webp', NULL, 58, '2011', 2, 'Jaipur, Rajasthan', NULL, 1, 1, 1, 'Industry-focused training\r\nOur Advanced Learning System Designed for Your Course\r\nCareer Support for Online Manipal Learners\r\nHands-On Campus Learning Experiences\r\nCampus Immersions\r\nWeekly Webinars with industry leaders', 'https://distanceeducationschool.com/online-manipal-university/', 1, '2026-06-17 05:43:16', '2026-06-17 06:38:23'),
(12, 'Uttranchal University Online', NULL, 'uttranchal-university-online', '/ai-tools/assets/uploads/images/6a323f96c96b67.22031952_1781677974.png', '/ai-tools/assets/uploads/certificates/6a323f96c98a79.25817951_1781677974.webp', NULL, NULL, '2013', 2, 'Dehradun, Uttarakhand', NULL, 1, 1, 1, 'Prodigious Faculty and World-Class Curriculum\r\n1-on-1 Personalised Mentorship\r\nReputed Degree from a Top-Ranked University\r\nIntegrated LMS & e-library', 'https://distanceeducationschool.com/uttaranchal-university/', 1, '2026-06-17 06:32:54', '2026-06-17 12:59:55'),
(13, 'Lovely Professional University Online', NULL, 'lovely-professional-university-online', '/ai-tools/assets/uploads/images/6a32489a0608f0.63841838_1781680282.png', '/ai-tools/assets/uploads/certificates/6a32489a0623a8.66355699_1781680282.jpg', NULL, 30, '2005', 2, 'Phagwara, Punjab, India', NULL, 1, 1, 1, 'Strong Placements\r\nIndustry Curriculum\r\nOnline Flexibility\r\nWide Specializations', 'https://distanceeducationschool.com/lovely-professional-university/', 1, '2026-06-17 07:11:22', '2026-06-17 10:00:42'),
(14, 'Mangalayatan University Online', NULL, 'mangalayatan-university-online', '/ai-tools/assets/uploads/images/6a32533a2b4038.06053005_1781683002.png', '/ai-tools/assets/uploads/certificates/6a32533a2b5c17.31302879_1781683002.webp', NULL, NULL, '2006', 2, 'Aligarh, Uttar Pradesh,', '3.0 LPA to ₹6.0 LPA', 1, 1, 1, 'Affordable career-oriented programs', 'https://distanceeducationschool.com/mangalayatan-university/', 1, '2026-06-17 07:56:42', '2026-06-17 10:01:27'),
(15, 'Kurukshetra University', NULL, 'kurukshetra-university', '/ai-tools/assets/uploads/images/6a3265e51a6431.95774554_1781687781.png', '/ai-tools/assets/uploads/certificates/6a3265e51a7ef2.95342902_1781687781.webp', NULL, NULL, '1956', 1, 'Thanesar, Haryana', NULL, 1, 1, 1, 'Known for traditional academic strength + affordability', 'https://distanceeducationschool.com/kurukshetra-university/', 1, '2026-06-17 09:16:21', '2026-06-17 09:16:21'),
(16, 'Vignan University', NULL, 'vignan-university', '/ai-tools/assets/uploads/images/6a326c83d85e97.51383765_1781689475.png', '/ai-tools/assets/uploads/certificates/6a326c83d87982.95705907_1781689475.jpg', NULL, 75, '2008', 2, 'Guntur, Andhra Pradesh', NULL, 1, 1, 1, 'Strong in engineering and technical education', 'https://distanceeducationschool.com/vignan-university/', 1, '2026-06-17 09:44:35', '2026-06-17 09:44:35'),
(17, 'Chandigarh University', NULL, 'chandigarh-university', '/ai-tools/assets/uploads/images/6a3278b960aa93.55093307_1781692601.png', '/ai-tools/assets/uploads/certificates/6a3278b960bea6.68681899_1781692601.jpg', NULL, 19, '2012', 2, 'Ajitgarh, Punjab', '6 LPA', 1, 1, 1, 'Famous for record placements and industry-linked programs', 'https://distanceeducationschool.com/online-chandigarh-university/', 1, '2026-06-17 10:36:41', '2026-06-17 10:36:41'),
(18, 'Jamia Hamdard', NULL, 'jamia-hamdard', '/ai-tools/assets/uploads/images/6a3281b8b5c035.56452678_1781694904.webp', '/ai-tools/assets/uploads/certificates/6a3281b8b5da21.88226133_1781694904.webp', NULL, 40, '1989', 1, 'Hamdard Nagar, New Delhi', NULL, 1, 1, 1, NULL, 'https://distanceeducationschool.com/jamia-hamdard-university/', 1, '2026-06-17 11:15:04', '2026-06-17 11:15:04'),
(19, 'Bharathidasan University', NULL, 'bharathidasan-university', '/ai-tools/assets/uploads/images/6a3285d0ae15a2.84011853_1781695952.webp', '/ai-tools/assets/uploads/certificates/6a3285d0ae2be9.03744371_1781695952.webp', NULL, 36, '1982', 1, 'Tiruchirappalli, Tamil Nadu', NULL, 1, 1, 1, 'Strong in science and research programs', 'https://distanceeducationschool.com/Bharathidasan-University/', 1, '2026-06-17 11:32:32', '2026-06-17 11:33:00'),
(20, 'Vivekananda Global University', NULL, 'vivekananda-global-university', '/ai-tools/assets/uploads/images/6a328a7c1e75c5.96796534_1781697148.webp', '/ai-tools/assets/uploads/certificates/6a328a7c1e9425.89795401_1781697148.webp', NULL, NULL, '2012', 2, 'Jaipur, Rajasthan', NULL, 1, 1, 1, 'Popular for design, architecture, and emerging tech courses', 'https://distanceeducationschool.com/vivekananda-global-university/', 1, '2026-06-17 11:52:28', '2026-06-17 11:52:28'),
(21, 'Sharda University Online', NULL, 'sharda-university-online', '/ai-tools/assets/uploads/images/6a32926112b6d6.35586295_1781699169.webp', '/ai-tools/assets/uploads/certificates/6a32926112d904.95907957_1781699169.webp', NULL, NULL, '2009', 2, 'Greater Noida, Uttar Pradesh', NULL, 0, 0, 0, NULL, 'https://distanceeducationschool.com/sharda-university/', 1, '2026-06-17 12:26:09', '2026-06-17 12:41:39'),
(22, 'Galgotias Online University', NULL, 'galgotias-online-university', '/ai-tools/assets/uploads/images/6a339865b692f3.09146395_1781766245.png', '/ai-tools/assets/uploads/certificates/6a339865b6b477.80468835_1781766245.webp', 4.2, NULL, '2011', 2, 'Greater Noida, Uttar Pradesh', '5.40 Lakhs', 1, 1, 1, 'Known for placements + proximity to corporate hubs (Noida), Corporate-driven learning', 'https://distanceeducationschool.com/galgotias-university/', 1, '2026-06-18 05:10:26', '2026-06-18 07:04:05'),
(23, 'Jain University', NULL, 'jain-university', '/ai-tools/assets/uploads/images/6a3385c325f712.60798107_1781761475.png', '/ai-tools/assets/uploads/certificates/6a3385c3261949.63873705_1781761475.jpg', 3.9, 65, '1990', 2, 'Bengaluru, Karnataka', '7 LPA', 1, 1, 1, 'Famous for management, commerce & entrepreneurship focus, Premium programs', 'https://distanceeducationschool.com/jain-university/', 1, '2026-06-18 05:44:35', '2026-06-18 07:04:54');

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
(157, 7, 3, NULL),
(158, 7, 5, NULL),
(159, 7, 7, NULL),
(160, 7, 4, NULL),
(166, 8, 10, NULL),
(167, 8, 13, NULL),
(168, 8, 9, NULL),
(169, 8, 12, NULL),
(170, 8, 4, NULL),
(171, 9, 10, NULL),
(172, 9, 4, NULL),
(177, 10, 3, NULL),
(178, 10, 7, NULL),
(179, 10, 9, NULL),
(180, 10, 4, NULL),
(181, 10, 14, NULL),
(182, 10, 15, NULL),
(201, 11, 3, NULL),
(202, 11, 7, NULL),
(203, 11, 13, NULL),
(204, 11, 9, NULL),
(205, 11, 4, NULL),
(206, 11, 11, NULL),
(229, 15, 3, NULL),
(230, 15, 10, NULL),
(231, 15, 8, NULL),
(232, 15, 4, NULL),
(233, 16, 7, NULL),
(234, 16, 4, NULL),
(255, 13, 3, NULL),
(256, 13, 10, NULL),
(257, 13, 9, NULL),
(258, 13, 4, NULL),
(259, 13, 11, NULL),
(260, 13, 14, NULL),
(261, 14, 3, NULL),
(262, 14, 5, NULL),
(263, 14, 7, NULL),
(264, 14, 4, NULL),
(265, 17, 7, NULL),
(266, 17, 8, NULL),
(267, 17, 4, NULL),
(268, 18, 7, NULL),
(269, 18, 4, NULL),
(273, 19, 3, NULL),
(274, 19, 10, NULL),
(275, 19, 4, NULL),
(276, 20, 3, NULL),
(277, 20, 7, NULL),
(278, 20, 4, NULL),
(283, 21, 3, NULL),
(284, 21, 8, NULL),
(285, 21, 9, NULL),
(286, 21, 4, NULL),
(287, 12, 3, NULL),
(288, 12, 5, NULL),
(289, 12, 7, NULL),
(290, 12, 9, NULL),
(291, 12, 4, NULL),
(292, 12, 11, NULL),
(299, 22, 7, NULL),
(300, 22, 4, NULL),
(301, 23, 10, NULL),
(302, 23, 4, NULL);

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
  `course_duration` varchar(100) DEFAULT NULL,
  `min_eligibility_percentage` int(3) DEFAULT NULL,
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

INSERT INTO `university_courses` (`id`, `university_id`, `course_id`, `education_mode_id`, `academic_fees`, `course_duration`, `min_eligibility_percentage`, `fees_discount`, `course_rating`, `course_specializations`, `brochure_file`, `is_active`, `created_at`, `updated_at`) VALUES
(11, 7, 9, 2, 56300.00, '2 Years', 30, 20.00, 4.5, 'General Management\r\nDigital Marketing Management\r\nSpecialization in Data Science\r\nBusiness Analytics\r\nInternational Finance\r\nHospital & Healthcare Management\r\nDual Specialization\r\nConstruction Project Management', '/ai-tools/assets/uploads/brochures/6a30e1d54e8330.58580544_1781588437.pdf', 1, '2026-06-16 05:40:37', '2026-06-16 05:42:48'),
(12, 7, 10, 2, 49800.00, '2 Years', 30, NULL, 4.5, 'General\r\nCyber Security\r\nSoftware Engineering\r\nMachine Learning & Artificial Intelligence\r\nBlockchain Technology  & Management\r\nFinancial Technology & AI', '/ai-tools/assets/uploads/brochures/6a30ef91550ac5.71060988_1781591953.pdf', 1, '2026-06-16 06:39:13', '2026-06-16 07:19:32'),
(13, 7, 11, 2, 82300.00, '2 Years', 30, NULL, 4.5, 'Finance & Accounting Management\r\nMarketing & Sales Management\r\nHuman Resource Management\r\nBusiness Analytics\r\nProduction & Operations Management\r\nDigital Marketing Management', '/ai-tools/assets/uploads/brochures/6a30f50308b492.04270320_1781593347.pdf', 1, '2026-06-16 07:02:27', '2026-06-16 07:19:22'),
(14, 7, 12, 2, 68800.00, '2 Years', 30, NULL, 4.5, NULL, '/ai-tools/assets/uploads/brochures/6a30f5e8e030c8.91103458_1781593576.pdf', 1, '2026-06-16 07:06:16', '2026-06-16 07:20:12'),
(15, 7, 13, 2, 37500.00, '2 Years', 30, NULL, 4.5, 'Financial Management \r\nFinTech', '/ai-tools/assets/uploads/brochures/6a30f7f7195471.93519522_1781594103.pdf', 1, '2026-06-16 07:15:03', '2026-06-16 07:17:38'),
(16, 7, 14, 2, 47500.00, '2 Years', 30, NULL, 4.5, 'Digital Media\r\nJournalism & Media mgmt', '/ai-tools/assets/uploads/brochures/6a30fc83ee4fb6.65298442_1781595267.pdf', 1, '2026-06-16 07:34:27', '2026-06-16 07:34:27'),
(17, 7, 15, 2, 37500.00, '2 Years', 30, NULL, 4.5, NULL, '/ai-tools/assets/uploads/brochures/6a30ff85786890.42748929_1781596037.pdf', 1, '2026-06-16 07:47:17', '2026-06-16 07:52:12'),
(18, 7, 16, 2, 68800.00, '2 Years', 30, NULL, 4.5, NULL, '/ai-tools/assets/uploads/brochures/6a310099a22d95.46599903_1781596313.pdf', 1, '2026-06-16 07:51:53', '2026-06-16 07:51:53'),
(19, 7, 17, 2, 19200.00, '3 Years', 30, NULL, 4.5, 'English\r\nPsychology', '/ai-tools/assets/uploads/brochures/6a3101e34cdfa6.16034250_1781596643.pdf', 1, '2026-06-16 07:57:23', '2026-06-16 09:09:42'),
(20, 7, 18, 2, 19200.00, '3 Years', 30, NULL, 4.5, 'General\r\nInternational Finance & Accounting\r\nHonours', '/ai-tools/assets/uploads/brochures/6a310e1b364d34.51034611_1781599771.pdf', 1, '2026-06-16 08:49:31', '2026-06-16 09:09:14'),
(21, 7, 19, 2, 33200.00, '3 Years', 30, NULL, 4.5, 'Travel and Tourism Mgmt \r\nData Analytics General\r\nDigital Marketing\r\nBusiness Analytics\r\nInternational Finance\r\nEntrepreneurship & Leadership\r\nHuman Resource Management\r\nMarketing Management', '/ai-tools/assets/uploads/brochures/6a3110e3d3c138.07547773_1781600483.pdf', 1, '2026-06-16 08:55:43', '2026-06-16 09:08:29'),
(22, 7, 20, 2, 29200.00, '3 Years', 30, NULL, 4.5, 'Data Analytics\r\nCloud & Security\r\nSoftware Engineering\r\nData Engineering\r\nArtificial Intelligence\r\nCyber Security', '/ai-tools/assets/uploads/brochures/6a3114f8c78c30.42725431_1781601528.pdf', 1, '2026-06-16 09:13:33', '2026-06-16 09:18:48'),
(23, 7, 21, 2, 41699.99, '3 Years', 30, NULL, 4.5, 'Cloud & Security (with KPMG)\r\nSoftware Engineering (with HCLTech)\r\nData Engineering\r\nApplied Data Engineering\r\nFinancial Technology & AI (with Paytm)\r\nData Analytics', '/ai-tools/assets/uploads/brochures/6a3114eac9b0e9.25152402_1781601514.pdf', 1, '2026-06-16 09:18:34', '2026-06-16 09:18:34'),
(24, 7, 22, 2, 31700.00, '3 Years', 30, NULL, 4.5, NULL, '/ai-tools/assets/uploads/brochures/6a3116d7a98214.86309874_1781602007.pdf', 1, '2026-06-16 09:26:47', '2026-06-16 09:27:01'),
(25, 8, 19, 2, 17500.00, '3 Years', 40, 15.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a312938549144.48475040_1781606712.pdf', 1, '2026-06-16 10:45:12', '2026-06-16 10:46:43'),
(26, 8, 20, 2, 17500.00, '3 Years', 40, 15.00, 0.0, NULL, '/ai-tools/assets/uploads/brochures/6a312d81380ea8.89553716_1781607809.pdf', 1, '2026-06-16 11:03:29', '2026-06-16 11:03:29'),
(27, 8, 23, 2, 15000.00, '3 Years', 40, 15.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a313067717e00.66947443_1781608551.pdf', 1, '2026-06-16 11:15:51', '2026-06-16 11:15:51'),
(28, 8, 9, 2, 35500.00, '2 Years', 50, 15.00, NULL, 'Marketing Management\r\nHuman Resource Management\r\nFinancial Management\r\nDigital Marketing\r\nRetail Management\r\nBanking & Insurance\r\nOperation & Supply Chain Management\r\nIT Management', '/ai-tools/assets/uploads/brochures/6a3131c53a3ad2.86461268_1781608901.pdf', 1, '2026-06-16 11:21:41', '2026-06-16 11:21:41'),
(29, 8, 24, 2, 45000.00, '2 Years', 50, 15.00, NULL, 'Marketing Management\r\nHuman Resource Management\r\nFinancial Management\r\nDigital Marketing\r\nRetail Management\r\nBanking & Insurance\r\nOperation & Supply Chain Management\r\nIT Management\r\nTourism Management\r\nReal Estate Management\r\nData Science & Business Analytics\r\nAgri Business Management\r\nBiotechnology Management\r\nFood Technology Management\r\nPharma & Health Care Management', '/ai-tools/assets/uploads/brochures/6a3133c71396b3.21489062_1781609415.pdf', 1, '2026-06-16 11:30:15', '2026-06-16 11:30:15'),
(30, 8, 10, 2, 32500.00, '2 Years', 50, 15.00, 0.0, NULL, '/ai-tools/assets/uploads/brochures/6a3135ef007559.51095593_1781609967.pdf', 1, '2026-06-16 11:39:27', '2026-06-16 11:39:27'),
(31, 8, 16, 2, 32500.00, '2 Years', 50, 15.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a313807cc92c8.57435159_1781610503.pdf', 1, '2026-06-16 11:48:23', '2026-06-16 11:48:23'),
(32, 8, 14, 2, 20000.00, '2 Years', 50, 15.00, NULL, NULL, NULL, 1, '2026-06-16 11:54:28', '2026-06-16 11:54:28'),
(33, 8, 25, 2, 15000.00, '2 Years', 50, 15.00, NULL, NULL, NULL, 1, '2026-06-16 11:58:32', '2026-06-16 11:58:32'),
(34, 9, 9, 2, 15300.00, '2 Years', 50, NULL, NULL, 'General', '/ai-tools/assets/uploads/brochures/6a3140bbab2158.30144166_1781612731.pdf', 1, '2026-06-16 12:23:40', '2026-06-16 12:25:31'),
(35, 9, 26, 2, 14125.00, '2 Years', 50, NULL, NULL, 'General', '/ai-tools/assets/uploads/brochures/6a3142244e5b41.35745050_1781613092.pdf', 1, '2026-06-16 12:31:32', '2026-06-16 12:31:32'),
(36, 9, 10, 2, 18800.00, '2 Years', 50, NULL, NULL, 'General', '/ai-tools/assets/uploads/brochures/6a3143a14b07a8.28745418_1781613473.pdf', 1, '2026-06-16 12:37:53', '2026-06-16 12:37:53'),
(37, 10, 9, 2, 30000.00, '2 Years', 30, 10.00, NULL, 'Marketing\r\nFinance\r\nHuman Resource Management Systems \r\nOperations & Supply Chain Management Healthcare', '/ai-tools/assets/uploads/brochures/6a322d68665ff4.88936417_1781673320.pdf', 1, '2026-06-17 05:15:20', '2026-06-17 05:15:20'),
(38, 10, 10, 2, 27500.00, '2 Years', 50, 10.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a322e60d65664.12931796_1781673568.pdf', 1, '2026-06-17 05:19:28', '2026-06-17 05:19:28'),
(39, 10, 13, 2, 18750.00, '2 Years', 30, 10.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a322f02699fb0.22435999_1781673730.pdf', 1, '2026-06-17 05:22:10', '2026-06-17 05:22:10'),
(40, 10, 17, 2, 12500.00, '3 Years', 30, 10.00, NULL, 'English \r\nSociology \r\nPolitical Science', '/ai-tools/assets/uploads/brochures/6a322f59a46698.09720259_1781673817.pdf', 1, '2026-06-17 05:23:37', '2026-06-17 05:23:37'),
(41, 10, 18, 2, 12500.00, '3 Years', 30, 10.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a322fc2d69349.20848581_1781673922.pdf', 1, '2026-06-17 05:25:22', '2026-06-17 05:25:22'),
(42, 10, 19, 2, 15000.00, '3 Years', 30, 10.00, NULL, 'Business Analytics & FinTech\r\nEntrepreneurship\r\nOperations & Supply Chain\r\nManagement\r\nBanking & Insurance', '/ai-tools/assets/uploads/brochures/6a323022156673.95950879_1781674018.pdf', 1, '2026-06-17 05:26:58', '2026-06-17 05:26:58'),
(43, 10, 25, 2, 18750.00, '2 Years', 30, 10.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a32307b565961.31880860_1781674107.pdf', 1, '2026-06-17 05:28:27', '2026-06-17 05:28:27'),
(44, 10, 27, 2, 18750.00, '2 Years', 30, 10.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a323143b5c718.61743411_1781674307.pdf', 1, '2026-06-17 05:31:47', '2026-06-17 05:31:47'),
(45, 10, 28, 2, 18750.00, '2 Years', 30, 10.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3231d42e95b4.41157305_1781674452.pdf', 1, '2026-06-17 05:34:12', '2026-06-17 05:34:12'),
(46, 11, 18, 2, 16500.00, '2 Years', 40, 15.00, NULL, 'Banking & FinTech\r\nBusiness Accounting & Taxation\r\nAccounting with AI\r\nEconomics\r\nBusiness Analytics\r\nFinancial Analytics\r\nE-Commerce \r\nDigital Marketing', '/ai-tools/assets/uploads/brochures/6a3234a408bc93.45929616_1781675172.pdf', 1, '2026-06-17 05:46:12', '2026-06-17 05:46:12'),
(47, 11, 19, 2, 23250.00, '3 Years', 40, 15.00, NULL, 'Marketing \r\nFinance & Accounting\r\nEntrepreneurship, Management, and Family Business\r\nData Analytics \r\nRetail and E-commerce \r\nHuman Resource Management \r\nDigital Marketing', '/ai-tools/assets/uploads/brochures/6a3235456cbff3.57999343_1781675333.pdf', 1, '2026-06-17 05:48:53', '2026-06-17 05:48:53'),
(48, 11, 20, 2, 23250.00, '3 Years', 40, 15.00, NULL, 'Data Science and Analytics \r\nCloud Computing \r\nCyber Security', '/ai-tools/assets/uploads/brochures/6a3235af138dd0.85687401_1781675439.pdf', 1, '2026-06-17 05:50:39', '2026-06-17 05:50:39'),
(49, 11, 9, 2, 45000.00, '2 Years', 30, 15.00, NULL, 'Finance\r\nAnalytics and data science\r\nHuman Resource Management\r\nMarketing \r\nProject Management\r\nOperations Management\r\nInternational Business \r\nSupply Chain Management\r\nBFSI\r\nIT and Fintech\r\nInformation System Management \r\nRetail Management\r\nDigital Marketing', '/ai-tools/assets/uploads/brochures/6a3236744e5731.81963635_1781675636.pdf', 1, '2026-06-17 05:53:56', '2026-06-17 05:53:56'),
(50, 11, 10, 2, 39500.00, '2 Years', 50, 15.00, NULL, 'AI and Data Science \r\nCloud COmputing \r\nCyber Secuurity\r\nComprehensive Emerging Technologies\r\nAI & ML', '/ai-tools/assets/uploads/brochures/6a32385348fd49.04186020_1781676115.pdf', 1, '2026-06-17 06:01:55', '2026-06-17 06:01:55'),
(51, 11, 13, 2, 27000.00, '2 Years', 30, 15.00, NULL, 'General', '/ai-tools/assets/uploads/brochures/6a323a4c2daf23.94808110_1781676620.pdf', 1, '2026-06-17 06:10:20', '2026-06-17 06:10:20'),
(52, 11, 14, 2, 20000.00, '2 Years', 30, 15.00, NULL, 'General', '/ai-tools/assets/uploads/brochures/6a323aabe96a44.63739067_1781676715.pdf', 1, '2026-06-17 06:11:55', '2026-06-17 06:11:55'),
(53, 11, 29, 2, 20000.00, '2 Years', 30, 15.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a323bd74ba405.03174298_1781677015.pdf', 1, '2026-06-17 06:16:55', '2026-06-17 06:16:55'),
(54, 11, 30, 2, 20000.00, '2 Years', 30, 15.00, NULL, 'Mathematics \r\nData Science\r\nComputational science \r\nEconometrics', '/ai-tools/assets/uploads/brochures/6a323c999f1547.18922419_1781677209.pdf', 1, '2026-06-17 06:20:09', '2026-06-17 06:20:09'),
(55, 12, 17, 2, 10200.00, '3 Years', 30, 5.00, NULL, 'General', '/ai-tools/assets/uploads/brochures/6a3242a3bb08b6.82815390_1781678755.pdf', 1, '2026-06-17 06:45:55', '2026-06-17 06:45:55'),
(56, 12, 19, 2, 17000.00, '3 Years', 30, 5.00, NULL, 'General', '/ai-tools/assets/uploads/brochures/6a32432ed80886.27811831_1781678894.pdf', 1, '2026-06-17 06:48:14', '2026-06-17 06:48:14'),
(57, 12, 20, 2, 17000.00, '3 Years', 30, 5.00, NULL, 'General', '/ai-tools/assets/uploads/brochures/6a3243b3729469.32371198_1781679027.pdf', 1, '2026-06-17 06:50:27', '2026-06-17 06:50:27'),
(58, 12, 9, 2, 24500.00, '2 Years', 30, 5.00, NULL, 'Marketing Management\r\nDigital Marketing\r\nHuman Resources\r\nBusiness Analytics\r\nFinancial Management\r\nInternational Business\r\nInformation Technology\r\nLogistics and Supply Chain', '/ai-tools/assets/uploads/brochures/6a3244139989b5.81995212_1781679123.pdf', 1, '2026-06-17 06:52:03', '2026-06-17 06:52:03'),
(59, 12, 10, 2, 24000.00, '2 Years', 30, 5.00, NULL, 'General', '/ai-tools/assets/uploads/brochures/6a3244a185c4e9.63714865_1781679265.pdf', 1, '2026-06-17 06:54:25', '2026-06-17 06:54:25'),
(60, 13, 29, 2, 25000.00, '2 Years', 30, 20.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a324a8f51f0f0.91563879_1781680783.pdf', 1, '2026-06-17 07:19:43', '2026-06-17 07:19:43'),
(61, 13, 25, 2, 25000.00, '2 Years', 30, 20.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a324ace8878f2.26549219_1781680846.pdf', 1, '2026-06-17 07:20:46', '2026-06-17 07:20:46'),
(62, 13, 31, 2, 25000.00, '2 Years', 30, 20.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a324b57a9aef5.93768681_1781680983.pdf', 1, '2026-06-17 07:23:03', '2026-06-17 07:23:03'),
(63, 13, 30, 2, 25000.00, '2 Years', 30, 20.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a324bf8df3771.85107243_1781681144.pdf', 1, '2026-06-17 07:25:44', '2026-06-17 07:25:44'),
(64, 13, 27, 2, 25000.00, '2 Years', 30, 20.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a324c3483dd56.14589408_1781681204.pdf', 1, '2026-06-17 07:26:44', '2026-06-17 07:26:44'),
(65, 13, 26, 2, 25000.00, '2 Years', 30, 20.00, NULL, NULL, NULL, 1, '2026-06-17 07:28:07', '2026-06-17 07:28:07'),
(66, 13, 13, 2, 25000.00, '2 Years', 30, 20.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a324cfdadf910.28650350_1781681405.pdf', 1, '2026-06-17 07:29:13', '2026-06-17 07:30:05'),
(67, 13, 10, 2, 40000.00, '2 Years', 30, 20.00, NULL, 'AR/VR (Game Development)\r\nMachine Learning and AI \r\nData Science \r\nFull Stack Web Development \r\nCybersecurity', '/ai-tools/assets/uploads/brochures/6a324d5d41d1d5.33057061_1781681501.pdf', 1, '2026-06-17 07:31:41', '2026-06-17 07:34:48'),
(68, 13, 24, 2, 50000.00, '2 Years', 30, 20.00, NULL, 'Digital Marketing \r\nIT\r\nInternational Business\r\nBanking and Financial Services \r\nHospital and Healthcare Management \r\nFinance\r\nLogistics and Supply Chain \r\nHuman Resource Management \r\nMarketing \r\nData Science \r\nOperations management \r\nBusiness Analytics', '/ai-tools/assets/uploads/brochures/6a324e069f4856.18521847_1781681670.pdf', 1, '2026-06-17 07:34:30', '2026-06-17 07:34:30'),
(69, 13, 17, 2, 20000.00, '3 Years', 30, 20.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a324eb2cad468.20179670_1781681842.pdf', 1, '2026-06-17 07:37:22', '2026-06-17 07:37:22'),
(70, 13, 20, 2, 25000.00, '3 Years', 30, 20.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a324f189da004.30885844_1781681944.pdf', 1, '2026-06-17 07:39:04', '2026-06-17 07:39:04'),
(71, 13, 19, 2, 25000.00, '3 Years', 30, 20.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a324f6a85da98.02143697_1781682026.pdf', 1, '2026-06-17 07:40:26', '2026-06-17 07:40:26'),
(72, 13, 32, 2, 25000.00, '1 Years', 30, 20.00, NULL, NULL, NULL, 1, '2026-06-17 07:48:08', '2026-06-17 07:48:08'),
(73, 13, 33, 2, 25000.00, '1 Years', 30, 20.00, NULL, NULL, NULL, 1, '2026-06-17 07:49:38', '2026-06-17 07:49:38'),
(74, 14, 17, 3, 6500.00, '3 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3253f5b85bf8.64917831_1781683189.pdf', 1, '2026-06-17 07:59:49', '2026-06-17 07:59:49'),
(75, 14, 19, 3, 10499.99, '3 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a325e6a105525.86107697_1781685866.pdf', 1, '2026-06-17 08:44:26', '2026-06-17 09:27:11'),
(76, 14, 9, 3, 16500.00, '2 Years', 30, NULL, NULL, 'HR\r\nMarketing\r\nOperations\r\nFinance', '/ai-tools/assets/uploads/brochures/6a325eda182c68.88231658_1781685978.pdf', 1, '2026-06-17 08:46:18', '2026-06-17 09:27:01'),
(77, 14, 20, 3, 11500.00, '3 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a32600fe8deb0.45445488_1781686287.pdf', 1, '2026-06-17 08:51:27', '2026-06-17 09:26:53'),
(78, 14, 10, 3, 16499.99, '2 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a32605c12c094.82409370_1781686364.pdf', 1, '2026-06-17 08:52:44', '2026-06-17 09:26:45'),
(79, 14, 34, 3, 19500.00, '2 Years', 30, NULL, NULL, 'Supply Chain Management\r\nBusines analytics\r\nInformation technology\r\ndigital marketing', '/ai-tools/assets/uploads/brochures/6a32614672e088.73598350_1781686598.pdf', 1, '2026-06-17 08:56:38', '2026-06-17 09:26:35'),
(80, 14, 30, 3, 13499.99, '2 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a32619f382638.44558398_1781686687.pdf', 1, '2026-06-17 08:58:07', '2026-06-17 09:26:27'),
(81, 14, 13, 3, 8500.00, '2 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3261f61a00b6.09392969_1781686774.pdf', 1, '2026-06-17 08:59:34', '2026-06-17 09:26:18'),
(82, 14, 25, 3, 8500.00, '2 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a32627659f1f3.15773954_1781686902.pdf', 1, '2026-06-17 09:01:42', '2026-06-17 09:26:09'),
(83, 14, 27, 3, 8500.00, '2 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a32630bdf1791.11135673_1781687051.pdf', 1, '2026-06-17 09:04:11', '2026-06-17 09:25:59'),
(84, 14, 35, 3, 8500.00, '2 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3263a49ea421.00669175_1781687204.pdf', 1, '2026-06-17 09:06:44', '2026-06-17 09:25:49'),
(85, 14, 36, 3, 8500.00, '2 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a32642164f8b8.77657582_1781687329.pdf', 1, '2026-06-17 09:08:49', '2026-06-17 09:25:41'),
(86, 14, 14, 3, 8500.00, '2 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a326465489505.15247637_1781687397.pdf', 1, '2026-06-17 09:09:57', '2026-06-17 09:25:33'),
(87, 15, 17, 3, 12000.00, '3 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3266e2636147.00674932_1781688034.pdf', 1, '2026-06-17 09:20:34', '2026-06-17 09:20:34'),
(88, 15, 19, 3, 12000.00, '3 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3268bacf23b7.87256925_1781688506.pdf', 1, '2026-06-17 09:28:26', '2026-06-17 09:28:26'),
(89, 15, 18, 3, 12000.00, '3 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a326903a3fa46.75684408_1781688579.pdf', 1, '2026-06-17 09:29:39', '2026-06-17 09:29:39'),
(90, 15, 9, 3, 25499.99, '2 Years', 30, NULL, NULL, 'Business analytics\r\nIT mgmt\r\nfinance\r\nmarketing mgmt\r\nhr mgmt', '/ai-tools/assets/uploads/brochures/6a32695c64ed45.93102418_1781688668.pdf', 1, '2026-06-17 09:31:08', '2026-06-17 09:31:08'),
(91, 15, 10, 3, 18900.00, '2 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3269cded3789.07095534_1781688781.pdf', 1, '2026-06-17 09:33:01', '2026-06-17 09:33:01'),
(92, 15, 13, 3, 14999.98, '2 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3269f4420da4.23617936_1781688820.pdf', 1, '2026-06-17 09:33:40', '2026-06-17 09:33:40'),
(93, 15, 25, 3, 15000.00, '2 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a326a36b53043.47668328_1781688886.pdf', 1, '2026-06-17 09:34:46', '2026-06-17 09:34:46'),
(94, 15, 27, 3, 15000.00, '2 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a326a550412e9.35261535_1781688917.pdf', 1, '2026-06-17 09:35:17', '2026-06-17 09:35:17'),
(95, 15, 37, 3, 15000.00, '2 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a326ac037b908.84834463_1781689024.pdf', 1, '2026-06-17 09:37:04', '2026-06-17 09:37:04'),
(96, 16, 19, 2, 17999.99, '3 Years', 30, NULL, NULL, 'Marketing & HR\r\nMarketing & analytics', '/ai-tools/assets/uploads/brochures/6a326d4ee2f121.47192542_1781689678.pdf', 1, '2026-06-17 09:47:58', '2026-06-17 10:11:21'),
(97, 16, 20, 2, 17999.98, '3 Years', 30, NULL, NULL, 'Data Science', '/ai-tools/assets/uploads/brochures/6a326dfce47786.22977718_1781689852.pdf', 1, '2026-06-17 09:50:52', '2026-06-17 10:11:28'),
(98, 16, 9, 2, 22500.00, '2 Years', 30, NULL, NULL, 'Marketing\r\nhr\r\nfinance\r\nbusiness analytics\r\nIT\r\nHealthcare and hosp\r\nlogistics and supply chain', '/ai-tools/assets/uploads/brochures/6a326f40ae3168.96233772_1781690176.pdf', 1, '2026-06-17 09:56:16', '2026-06-17 10:11:33'),
(99, 16, 10, 2, 22500.00, '2 Years', 30, NULL, NULL, 'Computer science & IT\r\nData science', '/ai-tools/assets/uploads/brochures/6a327325116031.22489334_1781691173.pdf', 1, '2026-06-17 10:12:53', '2026-06-17 10:12:53'),
(100, 17, 19, 2, 21875.00, '3 Years', 30, 25.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a327950c04c19.67235691_1781692752.pdf', 1, '2026-06-17 10:39:12', '2026-06-18 09:20:25'),
(101, 17, 38, 2, 20550.00, '3 Years', 30, 25.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a327ac80d49b6.86843734_1781693128.pdf', 1, '2026-06-17 10:45:28', '2026-06-18 09:20:19'),
(102, 17, 20, 2, 22125.00, '3 Years', 30, 25.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a327b084991d6.10190670_1781693192.pdf', 1, '2026-06-17 10:46:32', '2026-06-18 09:20:14'),
(103, 17, 22, 2, 21875.00, '3 Years', 28, 25.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a327b44cf5336.69351540_1781693252.pdf', 1, '2026-06-17 10:47:32', '2026-06-18 09:20:09'),
(104, 17, 14, 2, 27188.00, '2 Years', 30, 25.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a327ba2743d33.34975722_1781693346.pdf', 1, '2026-06-17 10:49:06', '2026-06-18 09:20:04'),
(105, 17, 9, 2, 41249.99, '2 Years', 30, 25.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a327bfd4f12e1.22657248_1781693437.pdf', 1, '2026-06-17 10:50:37', '2026-06-18 09:20:00'),
(106, 17, 39, 2, 45000.00, '2 Years', 30, 25.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a327d5da7df49.24334837_1781693789.pdf', 1, '2026-06-17 10:56:29', '2026-06-18 09:19:55'),
(107, 17, 10, 2, 29063.00, '2 Years', 30, 25.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a327dc94d93a7.95169454_1781693897.pdf', 1, '2026-06-17 10:58:17', '2026-06-18 09:19:49'),
(108, 17, 30, 2, 18750.00, '2 Years', 30, 25.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a327e24cbd540.48677266_1781693988.pdf', 1, '2026-06-17 10:59:48', '2026-06-18 09:19:45'),
(109, 17, 16, 2, 27500.00, '2 Years', 30, 25.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a327e4c3b9db6.03421870_1781694028.pdf', 1, '2026-06-17 11:00:28', '2026-06-18 09:19:40'),
(110, 17, 25, 3, 18750.00, '2 Years', 30, 25.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a327ed2dccfb7.58767741_1781694162.pdf', 1, '2026-06-17 11:02:42', '2026-06-18 09:19:35'),
(111, 17, 29, 3, 18750.00, '2 Years', 30, 25.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a327f05a77669.10532060_1781694213.pdf', 1, '2026-06-17 11:03:33', '2026-06-18 09:19:31'),
(112, 18, 19, 3, 14000.00, '3 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3281de1598c5.61269799_1781694942.pdf', 1, '2026-06-17 11:15:42', '2026-06-17 12:09:38'),
(113, 18, 20, 3, 16000.00, '3 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3281f32043b3.66320029_1781694963.pdf', 1, '2026-06-17 11:16:03', '2026-06-17 12:09:17'),
(114, 18, 18, 3, 9000.00, '3 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a32820a8ee292.84069957_1781694986.pdf', 1, '2026-06-17 11:16:26', '2026-06-17 12:09:04'),
(115, 18, 9, 3, 25750.00, '2 Years', 45, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3282538cbc36.97571087_1781695059.pdf', 1, '2026-06-17 11:17:39', '2026-06-17 12:08:47'),
(116, 18, 10, 3, 21750.00, '2 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3282c31d7529.95904039_1781695171.pdf', 1, '2026-06-17 11:19:31', '2026-06-17 12:08:34'),
(117, 18, 40, 3, 8000.00, '2 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a32836de47494.85376690_1781695341.pdf', 1, '2026-06-17 11:22:21', '2026-06-17 12:08:20'),
(118, 18, 41, 3, 6500.00, '2 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a328386605db3.23053324_1781695366.pdf', 1, '2026-06-17 11:22:46', '2026-06-17 12:08:06'),
(119, 19, 17, 3, 45800.00, '3 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a328651e72ff5.85334536_1781696081.pdf', 1, '2026-06-17 11:34:41', '2026-06-17 12:07:03'),
(120, 19, 19, 3, 90800.00, '3 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3286a15c32b2.55232395_1781696161.pdf', 1, '2026-06-17 11:36:01', '2026-06-17 12:07:16'),
(121, 19, 9, 3, 91500.00, '2 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3286ffa671c4.99558340_1781696255.pdf', 1, '2026-06-17 11:37:35', '2026-06-17 12:06:34'),
(122, 19, 25, 3, 76000.00, '2 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a328743c947d3.41265394_1781696323.pdf', 1, '2026-06-17 11:38:43', '2026-06-17 12:06:16'),
(123, 19, 31, 3, 76000.00, '2 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a32875df20998.42923334_1781696349.pdf', 1, '2026-06-17 11:39:09', '2026-06-17 12:06:06'),
(124, 19, 27, 3, 76000.00, '2 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3287731fa761.75420173_1781696371.pdf', 1, '2026-06-17 11:39:31', '2026-06-17 12:05:55'),
(125, 19, 29, 3, 76000.00, '2 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a32878b9fc094.23244759_1781696395.pdf', 1, '2026-06-17 11:39:55', '2026-06-17 12:05:42'),
(126, 19, 36, 3, 76000.00, '2 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3288646a94c8.34099292_1781696612.pdf', 1, '2026-06-17 11:43:32', '2026-06-17 12:05:30'),
(127, 20, 17, 3, 12000.00, '3 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a328c23ae3db9.01118405_1781697571.pdf', 1, '2026-06-17 11:59:31', '2026-06-17 12:05:00'),
(128, 20, 19, 3, 22000.00, '3 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a328cccebcd87.56417488_1781697740.pdf', 1, '2026-06-17 12:02:20', '2026-06-17 12:04:48'),
(129, 20, 20, 3, 22000.00, '3 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a328d457b2867.11493455_1781697861.pdf', 1, '2026-06-17 12:04:21', '2026-06-17 12:04:21'),
(130, 20, 25, 3, 18000.00, '2 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a328f2c69db77.45419027_1781698348.pdf', 1, '2026-06-17 12:12:28', '2026-06-17 12:12:28'),
(131, 20, 14, 3, 18000.00, '2 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a328f971cd364.91065004_1781698455.pdf', 1, '2026-06-17 12:14:15', '2026-06-17 12:15:44'),
(132, 20, 9, 3, 37500.00, '2 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a328fe9219f46.52650563_1781698537.pdf', 1, '2026-06-17 12:15:37', '2026-06-17 12:15:37'),
(133, 20, 10, 3, 37500.00, '2 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a329067dc4bb2.57460010_1781698663.pdf', 1, '2026-06-17 12:17:43', '2026-06-17 12:17:43'),
(134, 20, 30, 3, 18000.00, '2 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3290afc509c0.22301150_1781698735.pdf', 1, '2026-06-17 12:18:55', '2026-06-17 12:18:55'),
(135, 20, 43, 3, 60000.00, '2 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a329123e83ae7.98617557_1781698851.pdf', 1, '2026-06-17 12:20:51', '2026-06-17 12:20:51'),
(136, 20, 42, 3, 60000.00, '2 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a329159a6b389.62891495_1781698905.pdf', 1, '2026-06-17 12:21:45', '2026-06-17 12:21:45'),
(137, 21, 17, 3, 57600.00, '3 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a32962d868ac1.12053452_1781700141.pdf', 1, '2026-06-17 12:42:21', '2026-06-17 12:42:21'),
(138, 21, 19, 3, 20000.00, '3 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3296978dcd11.57389361_1781700247.pdf', 1, '2026-06-17 12:44:07', '2026-06-17 12:44:07'),
(139, 21, 20, 3, 20000.00, '3 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3296d9c69312.67178523_1781700313.pdf', 1, '2026-06-17 12:45:13', '2026-06-17 12:45:13'),
(140, 21, 44, 3, 12500.00, '2 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3297a5b13092.08348430_1781700517.pdf', 1, '2026-06-17 12:48:37', '2026-06-17 12:48:37'),
(141, 21, 13, 3, 35000.00, '2 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3297cc721734.56012734_1781700556.pdf', 1, '2026-06-17 12:49:16', '2026-06-17 12:49:16'),
(142, 21, 9, 3, 35000.00, '2 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3298e120d3f0.86402402_1781700833.pdf', 1, '2026-06-17 12:53:53', '2026-06-17 12:53:53'),
(143, 21, 45, 3, 35000.00, '2 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a32994667bf31.34396260_1781700934.pdf', 1, '2026-06-17 12:55:34', '2026-06-17 12:55:34'),
(144, 21, 10, 3, 30000.00, '2 Years', NULL, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a329970d15356.29828561_1781700976.pdf', 1, '2026-06-17 12:56:16', '2026-06-17 12:56:16'),
(145, 22, 19, 2, 21000.00, '3 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a337e899603c7.88104375_1781759625.pdf', 1, '2026-06-18 05:13:45', '2026-06-18 09:06:06'),
(146, 22, 20, 3, 23000.00, '3 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a337ff735fd46.04827881_1781759991.pdf', 1, '2026-06-18 05:19:51', '2026-06-18 09:06:01'),
(147, 22, 9, 3, 35000.00, '2 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a338272d8e343.52823932_1781760626.pdf', 1, '2026-06-18 05:30:26', '2026-06-18 09:05:56'),
(148, 22, 10, 3, 37000.00, '2 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3382b6e4e634.45242632_1781760694.pdf', 1, '2026-06-18 05:31:34', '2026-06-18 09:05:50'),
(149, 22, 13, 3, 20000.00, '2 Years', NULL, 30.00, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3383a883dc11.03929642_1781760936.pdf', 1, '2026-06-18 05:35:36', '2026-06-18 09:05:08'),
(150, 22, 46, 3, 15000.00, '2 Years', 30, NULL, NULL, NULL, '/ai-tools/assets/uploads/brochures/6a3384803e98e0.56132302_1781761152.pdf', 1, '2026-06-18 05:39:12', '2026-06-18 09:04:24'),
(151, 23, 18, 3, 20000.00, '3 Years', 30, 10.00, NULL, NULL, NULL, 1, '2026-06-18 06:22:32', '2026-06-18 09:03:26'),
(152, 23, 47, 3, 22500.00, '3 Years', 30, NULL, NULL, 'Computer Science & Information Technology (CS & IT)\r\nData Science (DS)\r\nArtificial Intelligence & Machine Learning (AI & ML)\r\nCybersecurity (CS)\r\nCloud Computing (CC)', NULL, 1, '2026-06-18 06:23:12', '2026-06-18 09:00:03'),
(153, 23, 48, 3, 25000.00, '2 Years', 30, NULL, NULL, 'Computer Science & Information Technology (CS & IT)\r\nData Science (DS)\r\nArtificial Intelligence & Machine Learning (AI & ML)\r\nCybersecurity (CS)\r\nCloud Computing (CC)', NULL, 1, '2026-06-18 06:23:40', '2026-06-18 08:59:34'),
(154, 23, 49, 3, 25000.00, '3 Years', 30, NULL, NULL, 'Computer Science & Information Technology (CS & IT)\r\nData Science (DS)\r\nArtificial Intelligence & Machine Learning (AI & ML)\r\nCybersecurity (CS)\r\nCloud Computing (CC)', NULL, 1, '2026-06-18 06:24:35', '2026-06-18 08:59:21'),
(155, 23, 21, 3, 25000.00, '3 Years', 30, NULL, NULL, 'Computer Science & Information Technology (CS & IT)\r\nData Science (DS)\r\nArtificial Intelligence & Machine Learning (AI & ML)\r\nCybersecurity (CS)\r\nCloud Computing (CC)', NULL, 1, '2026-06-18 06:25:15', '2026-06-18 08:59:11'),
(156, 23, 51, 3, 25000.00, '3 Years', 30, NULL, NULL, 'Computer Science & Information Technology (CS & IT)\r\nData Science (DS)\r\nArtificial Intelligence & Machine Learning (AI & ML)\r\nCybersecurity (CS)\r\nCloud Computing (CC)', NULL, 1, '2026-06-18 06:28:29', '2026-06-18 08:58:58'),
(157, 23, 52, 3, 43750.00, '2 Years', 30, NULL, NULL, 'Hr\r\nFinance\r\nMarketing', NULL, 1, '2026-06-18 06:35:53', '2026-06-18 08:58:46'),
(158, 23, 53, 3, 40000.00, '2 Years', 30, NULL, NULL, 'Hr\r\nFinance\r\nMarketing', NULL, 1, '2026-06-18 06:37:54', '2026-06-18 08:58:37'),
(159, 23, 54, 3, 40000.00, '2 Years', 30, NULL, NULL, 'Hr\r\nFinance\r\nMarketing', NULL, 1, '2026-06-18 06:38:56', '2026-06-18 08:58:25'),
(160, 23, 55, 3, 40000.00, '2 Years', 30, NULL, NULL, 'Systems & Operations Management\r\nFinance & Marketing\r\nHuman Resource Management & Finance\r\nHuman Resource Management & Marketing\r\nLogistics & Supply Chain Management\r\nInternational Business', NULL, 1, '2026-06-18 06:46:05', '2026-06-18 08:58:09'),
(161, 23, 56, 3, 97000.00, '2 Years', 30, NULL, NULL, NULL, NULL, 1, '2026-06-18 06:48:26', '2026-06-18 08:57:54'),
(162, 23, 13, 3, 31250.00, '2 Years', 30, NULL, NULL, NULL, NULL, 1, '2026-06-18 06:49:16', '2026-06-18 08:57:32'),
(163, 23, 19, 3, 22500.00, '3 Years', 30, NULL, NULL, NULL, NULL, 1, '2026-06-18 06:50:01', '2026-06-18 08:57:18'),
(164, 23, 59, 3, 32500.00, '2 Years', 30, NULL, NULL, 'Computer Science & IT\r\nData Analytics\r\nCyber Security', NULL, 1, '2026-06-18 06:59:59', '2026-06-18 08:57:08'),
(165, 23, 60, 3, 40000.00, '2 Years', 30, NULL, NULL, 'Computer Science & IT\r\nData Analytics\r\nCyber Security', NULL, 1, '2026-06-18 07:01:19', '2026-06-18 08:56:53'),
(166, 23, 61, 3, 40000.00, '2 Years', 30, NULL, NULL, 'Computer Science & IT\r\nData Analytics\r\nCyber Security', NULL, 1, '2026-06-18 07:02:12', '2026-06-18 08:56:46'),
(167, 23, 57, 3, 60500.00, '2 Years', 30, NULL, NULL, NULL, NULL, 1, '2026-06-18 07:02:44', '2026-06-18 08:56:39'),
(168, 23, 58, 3, 56000.00, '3 Years', 30, NULL, NULL, NULL, NULL, 1, '2026-06-18 07:03:19', '2026-06-18 08:56:06');

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
(13, 1),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 2),
(12, 2),
(13, 2),
(14, 2),
(15, 3),
(16, 3),
(17, 3),
(18, 3),
(19, 3),
(20, 3),
(21, 3),
(22, 3),
(23, 3);

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
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1);

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
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `brochure_leads`
--
ALTER TABLE `brochure_leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `compare_unlock_leads`
--
ALTER TABLE `compare_unlock_leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `counseling_leads`
--
ALTER TABLE `counseling_leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `universities`
--
ALTER TABLE `universities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `university_accreditations`
--
ALTER TABLE `university_accreditations`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=303;

--
-- AUTO_INCREMENT for table `university_courses`
--
ALTER TABLE `university_courses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

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
