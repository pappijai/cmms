-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 26, 2019 at 02:17 AM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.1.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cmms`
--

-- --------------------------------------------------------

--
-- Table structure for table `buildings`
--

CREATE TABLE `buildings` (
  `BldgID` int(10) UNSIGNED NOT NULL,
  `BldgName` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `BldgCoordinates` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `buildings`
--

INSERT INTO `buildings` (`BldgID`, `BldgName`, `BldgCoordinates`, `created_at`, `updated_at`) VALUES
(1, 'Building A', '576,319,785,463', '2019-01-24 04:09:37', '2019-06-16 14:05:15'),
(2, 'Building B', '449,53,652,102', '2019-01-24 04:26:10', '2019-02-24 14:12:31'),
(4, 'Building C', '730,116,783,304', '2019-01-24 05:53:50', '2019-02-24 14:00:51');

-- --------------------------------------------------------

--
-- Table structure for table `classrooms`
--

CREATE TABLE `classrooms` (
  `ClassroomID` int(10) UNSIGNED NOT NULL,
  `ClassroomCode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ClassroomName` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ClassroomType` int(11) NOT NULL,
  `ClassroomIn` time NOT NULL,
  `ClassroomOut` time NOT NULL,
  `ClassroomBldg` int(11) NOT NULL,
  `ClassroomFloor` int(11) NOT NULL,
  `ClassroomCoordinates` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classrooms`
--

INSERT INTO `classrooms` (`ClassroomID`, `ClassroomCode`, `ClassroomName`, `ClassroomType`, `ClassroomIn`, `ClassroomOut`, `ClassroomBldg`, `ClassroomFloor`, `ClassroomCoordinates`, `created_at`, `updated_at`) VALUES
(2, 'A202', 'A202', 2, '08:00:00', '20:30:00', 1, 10, '183,31,332,161', '2019-01-28 03:57:24', '2019-02-25 04:48:42'),
(3, 'A203', 'A203', 2, '08:00:00', '20:30:00', 1, 10, '339,32,488,158', '2019-01-29 11:26:02', '2019-02-25 04:49:04'),
(4, 'A204', 'A204', 2, '08:00:00', '20:30:00', 1, 10, '500,32,649,159', '2019-01-29 11:26:37', '2019-02-25 04:49:28'),
(5, 'A205', 'Aboitiz Lab', 5, '08:00:00', '20:30:00', 1, 10, '661,30,812,161', '2019-01-29 11:27:05', '2019-02-25 04:49:53'),
(7, 'A301', 'A301', 2, '08:00:00', '20:30:00', 1, 11, '11,34,162,166', '2019-01-29 11:40:06', '2019-02-25 04:50:58'),
(8, 'A302', 'A302', 2, '08:00:00', '20:30:00', 1, 11, '174,33,322,167', '2019-01-29 11:47:10', '2019-02-25 04:51:28'),
(9, 'A102', 'A102', 7, '07:30:00', '20:00:00', 1, 1, '261,29,469,157', '2019-02-16 12:16:19', '2019-03-06 13:16:16'),
(10, 'A208', 'D.O.S.T', 5, '07:30:00', '20:00:00', 1, 10, '472,442,789,570', '2019-02-16 12:26:55', '2019-02-25 05:12:00'),
(11, 'A303', 'A303', 2, '07:30:00', '20:00:00', 1, 11, '332,35,482,164', '2019-02-16 12:29:59', '2019-02-25 04:52:26'),
(12, 'A304', 'A304', 2, '07:30:00', '20:00:00', 1, 11, '495,35,647,164', '2019-02-16 12:32:37', '2019-02-25 04:52:55'),
(13, 'A305', 'A305', 2, '07:30:00', '20:00:00', 1, 11, '660,35,809,165', '2019-02-16 12:33:52', '2019-02-25 04:53:24'),
(14, 'A307', 'A307', 2, '07:30:00', '20:00:00', 1, 11, '179,440,330,570', '2019-02-16 12:34:09', '2019-02-25 04:54:11'),
(15, 'A308', 'A308', 2, '07:30:00', '20:00:00', 1, 11, '343,439,492,571', '2019-02-16 12:34:35', '2019-02-25 04:54:41'),
(16, 'A309', 'A309', 2, '07:30:00', '20:00:00', 1, 11, '502,441,653,568', '2019-02-16 12:35:02', '2019-02-25 04:55:16'),
(17, 'A310', 'A310', 2, '07:30:00', '20:00:00', 1, 11, '664,439,817,569', '2019-02-16 12:35:27', '2019-02-25 04:55:45'),
(18, 'A401', 'A401', 2, '07:30:00', '20:00:00', 1, 12, '11,34,163,166', '2019-02-16 13:10:10', '2019-02-25 04:56:30'),
(19, 'A402', 'A402', 2, '07:30:00', '20:00:00', 1, 12, '174,35,324,164', '2019-02-16 13:10:36', '2019-02-25 04:57:01'),
(20, 'A403', 'A403', 2, '07:30:00', '20:00:00', 1, 12, '335,35,488,167', '2019-02-16 13:10:55', '2019-02-25 04:57:33'),
(21, 'A404', 'A404', 2, '07:30:00', '20:00:00', 1, 12, '497,33,648,165', '2019-02-16 13:11:14', '2019-02-25 04:58:09'),
(22, 'A405', 'A405', 2, '07:30:00', '20:00:00', 1, 12, '658,34,809,166', '2019-02-16 13:11:34', '2019-02-25 04:58:38'),
(23, 'A410', 'Keyboarding Laboratory', 4, '07:30:00', '20:00:00', 1, 12, '420,440,814,570', '2019-02-16 13:12:08', '2019-02-25 04:59:12'),
(25, 'B101', 'B101', 2, '07:30:00', '21:00:00', 2, 9, '145,64,255,199', '2019-02-25 03:50:31', '2019-02-25 04:59:50'),
(26, 'B102', 'B102', 2, '07:30:00', '21:00:00', 2, 9, '262,64,375,200', '2019-02-25 03:50:49', '2019-02-25 05:00:20'),
(27, 'B103', 'B103', 2, '07:30:00', '21:00:00', 2, 9, '380,65,492,199', '2019-02-25 03:51:11', '2019-02-25 05:00:49'),
(28, 'B104', 'ECE Lab', 9, '07:30:00', '21:00:00', 2, 9, '497,65,608,199', '2019-02-25 03:51:36', '2019-02-25 05:01:15'),
(29, 'C101', 'Mechanical Laboratory', 8, '07:30:00', '21:00:00', 4, 14, '205,57,559,282', '2019-02-25 03:52:20', '2019-02-25 05:02:16'),
(30, 'C102', 'C102', 2, '07:30:00', '21:00:00', 4, 14, '568,56,795,280', '2019-02-25 03:53:28', '2019-02-25 05:02:52'),
(31, 'P - Lab', 'Photoshop Lab', 10, '07:30:00', '21:30:00', 1, 12, 'unknown', '2019-06-26 00:02:26', '2019-06-26 00:02:26');

-- --------------------------------------------------------

--
-- Table structure for table `classroom_types`
--

CREATE TABLE `classroom_types` (
  `CTID` int(10) UNSIGNED NOT NULL,
  `CTName` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classroom_types`
--

INSERT INTO `classroom_types` (`CTID`, `CTName`, `created_at`, `updated_at`) VALUES
(2, 'Standard Classroom', '2019-01-24 23:28:44', '2019-01-25 00:12:17'),
(3, 'Gymnasium', '2019-01-24 23:28:53', '2019-01-24 23:28:53'),
(4, 'Keyboarding Lab', '2019-01-24 23:35:14', '2019-01-24 23:35:14'),
(5, 'Computer Lab', '2019-01-24 23:38:52', '2019-01-24 23:38:52'),
(7, 'Multimedia', '2019-02-05 03:50:07', '2019-02-05 03:50:07'),
(8, 'Mechanical Lab', '2019-02-05 03:50:19', '2019-02-05 03:50:19'),
(9, 'ECE Lab', '2019-02-05 03:50:35', '2019-02-05 03:50:35'),
(10, 'Photoshop Laboratory', '2019-06-26 00:01:31', '2019-06-26 00:01:31');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `CourseID` int(10) UNSIGNED NOT NULL,
  `CourseCode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CourseDescription` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CourseYears` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`CourseID`, `CourseCode`, `CourseDescription`, `CourseYears`, `created_at`, `updated_at`) VALUES
(1, 'BSIT', 'Bachelor of Science in Information Technology', 4, '2019-02-05 04:49:23', '2019-02-05 04:49:23'),
(2, 'BSA', 'Bachelor of Science in Accountancy', 4, '2019-02-05 04:56:36', '2019-06-25 11:29:52'),
(5, 'BSAM', 'Bachelor of Science in Applied Mathematics', 4, '2019-02-06 16:10:49', '2019-02-06 16:10:49'),
(6, 'BSOA', 'Bachelor of Science in Office Management', 4, '2019-02-07 02:06:17', '2019-03-25 23:56:21'),
(7, 'BSMM', 'Bachelor of Science in Marketing Management', 4, '2019-03-25 06:52:38', '2019-03-25 06:52:38'),
(8, 'BSME', 'Bachelor of Science in Mechanical Engineering', 5, '2019-03-26 01:36:08', '2019-03-26 01:36:08'),
(9, 'PET', 'Petition Class', 1, '2019-04-15 02:37:45', '2019-04-15 02:37:45'),
(10, 'BSEd', 'Bachelor of Science in Secondary Education', 4, '2019-06-18 05:31:14', '2019-06-18 05:31:14');

-- --------------------------------------------------------

--
-- Table structure for table `course_subject_offereds`
--

CREATE TABLE `course_subject_offereds` (
  `CSOID` int(10) UNSIGNED NOT NULL,
  `SubjectID` int(11) NOT NULL,
  `CourseID` int(11) NOT NULL,
  `CSOYear` int(11) NOT NULL,
  `CSOSem` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_subject_offereds`
--

INSERT INTO `course_subject_offereds` (`CSOID`, `SubjectID`, `CourseID`, `CSOYear`, `CSOSem`, `created_at`, `updated_at`) VALUES
(42, 20, 1, 1, 'First Semester', '2019-02-11 14:08:46', '2019-02-11 14:08:46'),
(43, 41, 1, 1, 'Second Semester', '2019-02-11 14:08:52', '2019-02-11 14:08:52'),
(44, 21, 1, 1, 'First Semester', '2019-02-11 14:08:55', '2019-02-11 14:08:55'),
(45, 42, 1, 1, 'Second Semester', '2019-02-11 14:08:57', '2019-02-11 14:08:57'),
(46, 26, 1, 1, 'First Semester', '2019-02-11 14:09:00', '2019-02-11 14:09:00'),
(47, 43, 1, 1, 'Second Semester', '2019-02-11 14:09:39', '2019-02-11 14:09:39'),
(48, 15, 1, 1, 'First Semester', '2019-02-11 14:09:43', '2019-02-11 14:09:43'),
(49, 19, 1, 1, 'Second Semester', '2019-02-11 14:09:46', '2019-02-11 14:09:46'),
(50, 17, 1, 1, 'First Semester', '2019-02-11 14:09:50', '2019-02-11 14:09:50'),
(51, 18, 1, 1, 'Second Semester', '2019-02-11 14:09:52', '2019-02-11 14:09:52'),
(52, 23, 1, 1, 'First Semester', '2019-02-11 14:09:58', '2019-02-11 14:09:58'),
(53, 49, 1, 1, 'Second Semester', '2019-02-11 14:10:02', '2019-02-11 14:10:02'),
(56, 40, 1, 2, 'First Semester', '2019-02-11 14:10:26', '2019-02-11 14:10:26'),
(57, 45, 1, 2, 'Second Semester', '2019-02-11 14:10:27', '2019-02-11 14:10:27'),
(58, 27, 1, 2, 'First Semester', '2019-02-11 14:10:35', '2019-02-11 14:10:35'),
(59, 25, 1, 2, 'Second Semester', '2019-02-11 14:10:39', '2019-02-11 14:10:39'),
(60, 30, 1, 2, 'First Semester', '2019-02-11 14:10:44', '2019-02-11 14:10:44'),
(61, 28, 1, 1, 'First Semester', '2019-02-11 14:11:00', '2019-02-11 14:11:00'),
(63, 44, 1, 2, 'Second Semester', '2019-02-22 12:35:25', '2019-02-22 12:35:25'),
(64, 48, 1, 2, 'Second Semester', '2019-02-22 12:35:27', '2019-02-22 12:35:27'),
(65, 30, 1, 2, 'Second Semester', '2019-02-22 12:35:30', '2019-02-22 12:35:30'),
(66, 20, 1, 3, 'First Semester', '2019-02-22 12:35:35', '2019-02-22 12:35:35'),
(67, 21, 1, 3, 'First Semester', '2019-02-22 12:35:37', '2019-02-22 12:35:37'),
(68, 44, 1, 3, 'First Semester', '2019-02-22 12:35:40', '2019-02-22 12:35:40'),
(69, 47, 1, 3, 'First Semester', '2019-02-22 12:35:43', '2019-02-22 12:35:43'),
(70, 43, 1, 3, 'Second Semester', '2019-02-22 12:35:46', '2019-02-22 12:35:46'),
(71, 22, 1, 3, 'Second Semester', '2019-02-22 12:35:49', '2019-02-22 12:35:49'),
(72, 18, 1, 3, 'Second Semester', '2019-02-22 12:35:52', '2019-02-22 12:35:52'),
(73, 26, 1, 3, 'Second Semester', '2019-02-22 12:36:08', '2019-02-22 12:36:08'),
(74, 48, 1, 3, 'Second Semester', '2019-02-22 12:36:19', '2019-02-22 12:36:19'),
(75, 26, 1, 4, 'First Semester', '2019-02-22 12:37:31', '2019-02-22 12:37:31'),
(76, 19, 1, 4, 'First Semester', '2019-02-22 12:37:33', '2019-02-22 12:37:33'),
(77, 23, 1, 4, 'First Semester', '2019-02-22 12:37:36', '2019-02-22 12:37:36'),
(78, 27, 1, 4, 'First Semester', '2019-02-22 12:37:40', '2019-02-22 12:37:40'),
(79, 22, 1, 4, 'Second Semester', '2019-02-22 12:37:43', '2019-02-22 12:37:43'),
(80, 26, 1, 4, 'Second Semester', '2019-02-22 12:37:45', '2019-02-22 12:37:45'),
(81, 18, 1, 4, 'Second Semester', '2019-02-22 12:37:48', '2019-02-22 12:37:48'),
(82, 23, 1, 4, 'Second Semester', '2019-02-22 12:37:50', '2019-02-22 12:37:50'),
(85, 25, 1, 1, 'Summer Semester', '2019-03-12 13:26:24', '2019-03-12 13:26:24'),
(86, 17, 1, 1, 'Summer Semester', '2019-03-12 13:26:33', '2019-03-12 13:26:33'),
(88, 16, 1, 1, 'Second Semester', '2019-03-24 07:35:04', '2019-03-24 07:35:04'),
(89, 15, 1, 4, 'Second Semester', '2019-03-24 08:03:31', '2019-03-24 08:03:31'),
(90, 19, 1, 4, 'Second Semester', '2019-03-24 08:03:33', '2019-03-24 08:03:33'),
(91, 30, 1, 4, 'Second Semester', '2019-03-24 08:03:35', '2019-03-24 08:03:35'),
(93, 20, 2, 1, 'First Semester', '2019-03-25 06:53:00', '2019-03-25 06:53:00'),
(94, 41, 2, 1, 'Second Semester', '2019-03-25 06:53:08', '2019-03-25 06:53:08'),
(95, 22, 2, 1, 'Second Semester', '2019-03-25 23:58:12', '2019-03-25 23:58:12'),
(96, 24, 6, 1, 'First Semester', '2019-03-26 01:34:23', '2019-03-26 01:34:23'),
(97, 18, 6, 1, 'Second Semester', '2019-03-26 01:34:37', '2019-03-26 01:34:37'),
(98, 20, 8, 1, 'Second Semester', '2019-03-26 01:39:45', '2019-03-26 01:39:45'),
(99, 21, 8, 1, 'Second Semester', '2019-03-26 01:39:53', '2019-03-26 01:39:53'),
(100, 22, 8, 1, 'Second Semester', '2019-03-26 01:39:58', '2019-03-26 01:39:58'),
(101, 21, 1, 1, 'Summer Semester', '2019-04-15 02:23:13', '2019-04-15 02:23:13');

-- --------------------------------------------------------

--
-- Table structure for table `days`
--

CREATE TABLE `days` (
  `DayID` int(10) UNSIGNED NOT NULL,
  `DayName` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `days`
--

INSERT INTO `days` (`DayID`, `DayName`, `created_at`, `updated_at`) VALUES
(1, 'Monday', NULL, NULL),
(2, 'Tuesday', NULL, NULL),
(3, 'Wednesday', NULL, NULL),
(4, 'Thursday', NULL, NULL),
(5, 'Friday', NULL, NULL),
(6, 'Saturday', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `floorplans`
--

CREATE TABLE `floorplans` (
  `FloorplanID` int(10) UNSIGNED NOT NULL,
  `FloorplanName` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `FloorplanPhoto` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `floorplans`
--

INSERT INTO `floorplans` (`FloorplanID`, `FloorplanName`, `FloorplanPhoto`, `created_at`, `updated_at`) VALUES
(1, 'PUP Taguig', '1551017479.png', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `floors`
--

CREATE TABLE `floors` (
  `BFID` int(10) UNSIGNED NOT NULL,
  `BldgID` int(11) NOT NULL,
  `BFName` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `BFPhoto` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `floors`
--

INSERT INTO `floors` (`BFID`, `BldgID`, `BFName`, `BFPhoto`, `created_at`, `updated_at`) VALUES
(1, 1, '1st Floor', '1551068186.png', '2019-01-24 07:59:27', '2019-06-16 14:05:34'),
(9, 2, '1st Floor', '1551068215.png', '2019-01-24 08:33:33', '2019-02-25 04:16:55'),
(10, 1, '2nd Floor', '1551068194.png', '2019-01-24 08:42:39', '2019-02-25 04:16:34'),
(11, 1, '3rd Floor', '1551068201.png', '2019-01-29 11:24:45', '2019-02-25 04:16:41'),
(12, 1, '4th Floor', '1551068207.png', '2019-01-29 11:24:57', '2019-02-25 04:16:47'),
(14, 4, '1st Floor', '1551068220.png', '2019-01-29 11:25:15', '2019-02-25 04:17:00');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(4, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(5, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(6, '2016_06_01_000004_create_oauth_clients_table', 1),
(7, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(8, '2019_01_24_033210_create_buildings_table', 2),
(9, '2019_01_24_062049_create_floors_table', 3),
(10, '2019_01_24_131724_create_rooms_table', 4),
(11, '2019_01_24_133030_create_classrooms_table', 4),
(12, '2019_01_24_133240_create_classroom_types_table', 4),
(13, '2019_01_29_115843_create_subjects_table', 5),
(14, '2019_01_31_110638_create_subject_meetings_table', 6),
(15, '2019_02_05_042938_create_courses_table', 7),
(16, '2019_02_06_112128_create_course_subject_offereds_table', 8),
(17, '2019_02_07_022110_create_professors_table', 9),
(18, '2019_02_11_081855_create_sections_table', 10),
(19, '2019_02_17_034836_create_subject_taggings_table', 11),
(20, '2019_02_17_124020_create_subject_tagging_schedules_table', 12),
(21, '2019_02_17_143834_create_schedules_table', 13),
(22, '2019_02_17_145935_create_days_table', 14),
(23, '2019_02_23_035247_create_floorplans_table', 15);

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

CREATE TABLE `oauth_personal_access_clients` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `professors`
--

CREATE TABLE `professors` (
  `ProfessorID` int(10) UNSIGNED NOT NULL,
  `ProfessorName` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `professors`
--

INSERT INTO `professors` (`ProfessorID`, `ProfessorName`, `created_at`, `updated_at`) VALUES
(1, 'Prof. Elvin Catantan', '2019-02-07 02:31:04', '2019-02-07 02:50:50'),
(2, 'Prof. Ron Alayon', '2019-02-07 02:34:10', '2019-02-07 02:34:10'),
(3, 'Prof. Larry Aumentado', '2019-02-17 03:35:51', '2019-02-17 03:35:51'),
(4, 'Prof. Bernadette Canlas', '2019-02-17 03:36:02', '2019-02-17 03:36:02'),
(5, 'Prof. Gina Dela Cruz', '2019-02-17 03:36:29', '2019-02-17 03:36:29'),
(7, 'Prof. Marian Arada', '2019-02-17 03:37:28', '2019-02-17 03:37:28'),
(8, 'Prof. Mayghie Galarce', '2019-02-17 03:37:51', '2019-02-17 03:37:51'),
(9, 'Prof. Florante Andres', '2019-02-17 03:38:28', '2019-02-17 03:38:28'),
(10, 'Prof. Marilou Novida', '2019-02-17 03:38:38', '2019-02-17 03:38:38'),
(11, 'Prof. Asuncion Gabasa', '2019-02-17 03:38:48', '2019-02-17 03:38:48'),
(12, 'Prof. Alyssa Teodoro', '2019-02-17 03:39:05', '2019-03-21 15:09:49'),
(13, 'Prof. Rosita Canlas', '2019-02-17 03:39:14', '2019-02-17 03:39:14'),
(14, 'Prof. Bryan Llenarizas', '2019-02-17 03:39:26', '2019-02-17 03:39:26'),
(15, 'Prof. Elias Austria', '2019-02-17 03:39:45', '2019-02-17 03:39:45'),
(16, 'Prof. Flordeliz Garcia', '2019-02-17 03:39:55', '2019-02-17 03:39:55'),
(17, 'Prof. Marvin Arriola', '2019-02-17 03:40:07', '2019-02-17 03:40:07'),
(18, 'Prof. Jose Malate', '2019-02-17 03:40:18', '2019-02-17 03:40:18'),
(19, 'Prof. Marifel Javier', '2019-02-17 03:40:26', '2019-02-17 03:40:26'),
(21, 'Prof. Mhel Gracia', '2019-03-26 01:38:24', '2019-03-26 01:38:24'),
(22, 'Prof. Galarce', '2019-06-18 05:27:54', '2019-06-18 05:27:54');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `SchedID` int(10) UNSIGNED NOT NULL,
  `SchedTime` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`SchedID`, `SchedTime`, `created_at`, `updated_at`) VALUES
(1, '07:30:00', NULL, NULL),
(2, '08:00:00', NULL, NULL),
(3, '08:30:00', NULL, NULL),
(4, '09:00:00', NULL, NULL),
(5, '09:30:00', NULL, NULL),
(6, '10:00:00', NULL, NULL),
(7, '10:30:00', NULL, NULL),
(8, '11:00:00', NULL, NULL),
(9, '11:30:00', NULL, NULL),
(10, '12:00:00', NULL, NULL),
(11, '12:30:00', NULL, NULL),
(12, '13:00:00', NULL, NULL),
(13, '13:30:00', NULL, NULL),
(14, '14:00:00', NULL, NULL),
(15, '14:30:00', NULL, NULL),
(16, '15:00:00', NULL, NULL),
(17, '15:30:00', NULL, NULL),
(18, '16:00:00', NULL, NULL),
(19, '16:30:00', NULL, NULL),
(20, '17:00:00', NULL, NULL),
(21, '17:30:00', NULL, NULL),
(22, '18:00:00', NULL, NULL),
(23, '18:30:00', NULL, NULL),
(24, '19:30:00', NULL, NULL),
(25, '20:00:00', NULL, NULL),
(26, '20:30:00', NULL, NULL),
(27, '21:00:00', NULL, NULL),
(28, '19:00:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `SectionID` int(10) UNSIGNED NOT NULL,
  `CourseID` int(11) NOT NULL,
  `SectionYear` year(4) NOT NULL,
  `SectionName` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SectionStatus` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`SectionID`, `CourseID`, `SectionYear`, `SectionName`, `SectionStatus`, `created_at`, `updated_at`) VALUES
(1, 1, 2015, '1', 'Inactive', '2019-02-11 09:16:14', '2019-03-25 23:56:33'),
(2, 1, 2018, '2', 'Active', '2019-02-11 09:22:34', '2019-02-17 06:37:18'),
(3, 1, 2016, '3', 'Active', '2019-02-17 03:44:40', '2019-02-17 06:12:12'),
(4, 2, 2018, '1', 'Active', '2019-02-25 11:31:03', '2019-02-25 11:33:51'),
(5, 5, 2018, '1', 'Active', '2019-02-25 11:47:11', '2019-02-25 11:51:29'),
(6, 1, 2014, '3', 'Inactive', '2019-03-12 11:12:42', '2019-03-12 11:38:29'),
(8, 8, 2018, '1', 'Active', '2019-03-26 01:37:50', '2019-03-26 01:37:50'),
(9, 5, 2019, '4', 'Active', '2019-06-18 05:28:57', '2019-06-25 23:12:40');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `SubjectID` int(10) UNSIGNED NOT NULL,
  `SubjectCode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SubjectDescription` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`SubjectID`, `SubjectCode`, `SubjectDescription`, `created_at`, `updated_at`) VALUES
(15, 'COMPROG 1', 'Computer Programming 1', '2019-01-31 12:20:20', '2019-02-03 10:51:25'),
(16, 'DBMS 1', 'Database Management 1', '2019-01-31 12:20:40', '2019-01-31 12:20:40'),
(17, 'FIL 1', 'Filipino 1', '2019-01-31 12:21:51', '2019-01-31 12:21:51'),
(18, 'FIL 2', 'Filipino 2', '2019-02-05 03:21:30', '2019-02-05 03:21:30'),
(19, 'COMPROG 2', 'Computer Programming 2', '2019-02-05 14:10:40', '2019-02-06 03:57:18'),
(20, 'ACP 1', 'Accounting Principles', '2019-02-06 03:11:58', '2019-02-11 13:58:34'),
(21, 'ICT 114', 'Basic Computer Hardware Servicing', '2019-02-06 03:16:27', '2019-02-06 03:16:27'),
(22, 'ITMT 111', 'College Algebra', '2019-02-06 03:17:24', '2019-02-06 03:17:24'),
(23, 'ICT 115', 'Keyboarding 1', '2019-02-06 03:18:53', '2019-02-06 03:18:53'),
(24, 'PE 111', 'Physical Education 1', '2019-02-06 03:20:52', '2019-02-06 03:20:52'),
(25, 'ITEN 111', 'Study and Thinking Skills in English', '2019-02-06 03:24:34', '2019-02-06 03:24:34'),
(26, 'ICT 124', 'Basic Electronics', '2019-02-06 03:26:13', '2019-02-06 03:26:13'),
(27, 'ITEN 112', 'Speech Communication', '2019-02-06 03:28:01', '2019-02-06 03:28:01'),
(28, 'ICT 125', 'Professional Ethics', '2019-02-06 03:30:29', '2019-02-06 03:30:29'),
(29, 'PE 112', 'Physical Education 2', '2019-02-06 03:33:32', '2019-02-06 03:33:32'),
(30, 'IT 121', 'Integrated Application Software', '2019-02-06 03:35:45', '2019-02-06 03:35:45'),
(40, 'PE 113', 'Physical Education 3', '2019-02-06 03:50:47', '2019-02-06 03:56:20'),
(41, 'ACP 2', 'Accounting Principles 2', '2019-02-11 13:58:57', '2019-02-11 13:58:57'),
(42, 'BHS 111', 'Basic Computer Hardware Servicing 2', '2019-02-11 13:59:33', '2019-02-11 13:59:33'),
(43, 'BE 2', 'Basic Electronics 2', '2019-02-11 14:00:27', '2019-02-11 14:00:27'),
(44, 'CA 2', 'College Algebra 2', '2019-02-11 14:01:04', '2019-02-11 14:01:04'),
(45, 'PE 114', 'Physical Education 4', '2019-02-11 14:01:44', '2019-02-11 14:01:44'),
(46, 'COMPROG 3', 'Computer Programming 3', '2019-02-11 14:03:36', '2019-02-11 14:03:36'),
(47, 'COMPROG 4', 'Computer Programming 4', '2019-02-11 14:04:12', '2019-02-11 14:04:12'),
(48, 'DBMS 2', 'Database Management 2', '2019-02-11 14:04:43', '2019-02-11 14:04:43'),
(49, 'ICT 116', 'Keyboarding 2', '2019-02-11 14:05:17', '2019-02-11 14:05:17'),
(51, 'ICT 199', 'COBOL 1', '2019-03-25 06:42:26', '2019-03-25 06:42:26'),
(52, 'ICT 123', 'Object Oriented Programming', '2019-03-25 06:50:59', '2019-03-25 06:50:59'),
(54, 'PE 1', 'Physical Education- Rythmic', '2019-03-26 01:33:34', '2019-04-18 07:49:23'),
(55, 'ENG 123', 'English', '2019-06-18 05:30:02', '2019-06-18 05:30:02');

-- --------------------------------------------------------

--
-- Table structure for table `subject_meetings`
--

CREATE TABLE `subject_meetings` (
  `SMID` int(10) UNSIGNED NOT NULL,
  `SubjectID` int(11) NOT NULL,
  `CTID` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SubjectHours` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subject_meetings`
--

INSERT INTO `subject_meetings` (`SMID`, `SubjectID`, `CTID`, `SubjectHours`, `created_at`, `updated_at`) VALUES
(10, 15, '2', 2, '2019-01-31 12:20:20', '2019-02-05 03:47:42'),
(11, 15, '5', 2, '2019-01-31 12:20:20', '2019-02-05 03:47:48'),
(12, 16, '2', 2, '2019-01-31 12:20:40', '2019-02-05 03:48:15'),
(13, 16, '5', 2, '2019-01-31 12:20:40', '2019-02-05 03:48:13'),
(14, 17, '2', 2, '2019-01-31 12:21:51', '2019-02-05 03:48:25'),
(15, 18, '2', 2, '2019-02-05 03:21:30', '2019-02-05 03:48:38'),
(17, 19, '5', 2, '2019-02-05 14:10:40', '2019-02-06 03:56:59'),
(18, 20, '2', 2, '2019-02-06 03:11:58', '2019-02-11 13:58:41'),
(19, 21, '2', 3, '2019-02-06 03:16:27', '2019-02-06 03:16:43'),
(20, 22, '2', 2, '2019-02-06 03:17:24', '2019-02-06 03:18:30'),
(21, 23, '4', 2, '2019-02-06 03:18:53', '2019-02-06 03:19:28'),
(22, 24, '3', 2, '2019-02-06 03:20:52', '2019-02-06 03:21:09'),
(23, 25, '2', 2, '2019-02-06 03:24:34', '2019-02-06 03:24:52'),
(24, 26, '2', 2, '2019-02-06 03:26:13', '2019-02-06 03:27:32'),
(25, 27, '2', 2, '2019-02-06 03:28:02', '2019-02-06 03:28:57'),
(26, 28, '2', 2, '2019-02-06 03:30:29', '2019-02-06 03:30:50'),
(27, 29, '3', 2, '2019-02-06 03:33:32', '2019-02-06 03:35:18'),
(28, 30, '5', 2, '2019-02-06 03:35:45', '2019-02-06 03:36:08'),
(40, 40, '3', 2, '2019-02-06 03:56:01', '2019-02-06 03:56:29'),
(41, 19, '2', 2, '2019-02-06 03:57:18', '2019-02-06 03:57:28'),
(42, 41, '2', 2, '2019-02-11 13:58:57', '2019-02-11 13:59:01'),
(43, 42, '2', 3, '2019-02-11 13:59:33', '2019-02-11 13:59:40'),
(44, 43, '2', 3, '2019-02-11 14:00:27', '2019-02-11 14:00:30'),
(45, 44, '2', 2, '2019-02-11 14:01:04', '2019-02-11 14:01:07'),
(46, 45, '3', 2, '2019-02-11 14:01:44', '2019-02-11 14:01:49'),
(47, 46, '2', 2, '2019-02-11 14:03:36', '2019-02-11 14:03:48'),
(48, 46, '5', 2, '2019-02-11 14:03:36', '2019-02-11 14:03:50'),
(49, 47, '2', 2, '2019-02-11 14:04:12', '2019-02-11 14:04:20'),
(50, 47, '5', 2, '2019-02-11 14:04:12', '2019-02-11 14:04:19'),
(51, 48, '2', 2, '2019-02-11 14:04:43', '2019-02-11 14:04:50'),
(52, 48, '5', 2, '2019-02-11 14:04:43', '2019-02-11 14:04:49'),
(53, 49, '4', 2, '2019-02-11 14:05:17', '2019-02-11 14:05:24'),
(56, 51, '5', 2, '2019-03-25 06:42:26', '2019-03-25 06:42:39'),
(57, 52, '5', 2, '2019-03-25 06:50:59', '2019-03-25 06:51:23'),
(58, 52, '2', 2, '2019-03-25 06:50:59', '2019-03-25 06:51:33'),
(61, 54, '3', 2, '2019-03-26 01:33:34', '2019-03-26 01:33:55');

-- --------------------------------------------------------

--
-- Table structure for table `subject_taggings`
--

CREATE TABLE `subject_taggings` (
  `STID` int(10) UNSIGNED NOT NULL,
  `SubjectID` int(11) NOT NULL,
  `ProfessorID` int(11) NOT NULL,
  `SectionID` int(11) NOT NULL,
  `STUnits` int(11) NOT NULL,
  `STSem` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `STYear` int(11) NOT NULL,
  `STYearFrom` year(4) NOT NULL,
  `STYearTo` year(4) NOT NULL,
  `STStatus` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subject_taggings`
--

INSERT INTO `subject_taggings` (`STID`, `SubjectID`, `ProfessorID`, `SectionID`, `STUnits`, `STSem`, `STYear`, `STYearFrom`, `STYearTo`, `STStatus`, `created_at`, `updated_at`) VALUES
(3, 18, 11, 1, 3, 'Second Semester', 4, 2018, 2019, 'Inactive', '2019-03-25 10:06:27', '2019-03-25 10:06:27'),
(8, 30, 11, 1, 3, 'Second Semester', 4, 2018, 2019, 'Inactive', '2019-03-25 10:08:00', '2019-03-25 10:08:00'),
(10, 42, 11, 2, 3, 'Second Semester', 1, 2018, 2019, 'Inactive', '2019-03-25 10:08:35', '2019-03-25 10:08:35'),
(11, 43, 12, 2, 3, 'Second Semester', 1, 2018, 2019, 'Inactive', '2019-03-25 10:08:41', '2019-03-25 10:08:41'),
(12, 19, 14, 2, 3, 'Second Semester', 1, 2018, 2019, 'Inactive', '2019-03-25 10:08:47', '2019-03-25 10:15:07'),
(13, 18, 12, 2, 3, 'Second Semester', 1, 2018, 2019, 'Inactive', '2019-03-25 10:08:53', '2019-03-25 10:08:53'),
(14, 49, 12, 2, 3, 'Second Semester', 1, 2018, 2019, 'Inactive', '2019-03-25 10:09:01', '2019-03-25 10:09:01'),
(17, 16, 12, 2, 3, 'Second Semester', 1, 2018, 2019, 'Inactive', '2019-03-25 10:11:17', '2019-03-25 10:11:17'),
(21, 41, 12, 2, 3, 'Second Semester', 1, 2018, 2019, 'Inactive', '2019-03-25 10:14:27', '2019-03-25 10:14:27'),
(22, 20, 21, 8, 3, 'Second Semester', 1, 2018, 2019, 'Inactive', '2019-03-26 01:40:28', '2019-03-26 01:40:28'),
(23, 21, 1, 8, 3, 'Second Semester', 1, 2018, 2019, 'Inactive', '2019-03-26 01:40:59', '2019-03-26 01:40:59'),
(24, 22, 15, 8, 3, 'Second Semester', 1, 2018, 2019, 'Inactive', '2019-03-26 01:41:24', '2019-03-26 01:41:24'),
(32, 22, 11, 1, 3, 'Second Semester', 4, 2018, 2019, 'Inactive', '2019-03-28 05:20:27', '2019-03-28 05:20:27'),
(37, 26, 11, 1, 3, 'Second Semester', 4, 2018, 2019, 'Inactive', '2019-03-28 05:48:38', '2019-03-28 05:48:38'),
(41, 15, 11, 1, 3, 'Second Semester', 4, 2018, 2019, 'Inactive', '2019-03-28 06:32:37', '2019-03-28 06:32:37'),
(48, 41, 11, 3, 3, 'Summer Semester', 3, 2018, 2019, 'Inactive', '2019-04-15 02:34:59', '2019-04-15 02:34:59'),
(59, 15, 9, 2, 3, 'Summer Semester', 1, 2018, 2019, 'Inactive', '2019-04-18 08:27:58', '2019-04-18 08:27:58'),
(60, 20, 15, 2, 3, 'Summer Semester', 1, 2018, 2019, 'Inactive', '2019-04-20 03:33:07', '2019-04-20 03:33:07'),
(63, 20, 11, 3, 3, 'Summer Semester', 3, 2018, 2019, 'Inactive', '2019-05-02 04:09:52', '2019-05-02 04:09:52'),
(64, 20, 11, 8, 3, 'Summer Semester', 1, 2018, 2019, 'Inactive', '2019-05-02 04:39:55', '2019-05-02 04:39:55'),
(65, 22, 12, 3, 1, 'Summer Semester', 3, 2018, 2019, 'Inactive', '2019-05-02 04:53:40', '2019-05-02 04:53:40'),
(66, 20, 12, 4, 2, 'First Semester', 2, 2019, 2020, 'Active', '2019-06-09 06:37:05', '2019-06-09 06:37:05'),
(67, 51, 8, 4, 3, 'First Semester', 2, 2019, 2020, 'Active', '2019-06-13 07:00:17', '2019-06-13 07:00:17'),
(68, 22, 12, 4, 3, 'First Semester', 2, 2019, 2020, 'Active', '2019-06-18 11:32:37', '2019-06-18 11:32:37'),
(69, 17, 1, 4, 3, 'First Semester', 2, 2019, 2020, 'Active', '2019-06-25 23:58:48', '2019-06-25 23:58:48'),
(70, 47, 22, 4, 3, 'First Semester', 2, 2019, 2020, 'Active', '2019-06-26 00:09:03', '2019-06-26 00:09:03');

-- --------------------------------------------------------

--
-- Table structure for table `subject_tagging_schedules`
--

CREATE TABLE `subject_tagging_schedules` (
  `STSID` int(10) UNSIGNED NOT NULL,
  `STID` int(11) NOT NULL,
  `ClassroomID` int(11) NOT NULL,
  `STSHours` int(11) NOT NULL,
  `STSTimeStart` time NOT NULL,
  `STSTimeEnd` time NOT NULL,
  `STSDay` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `STSStatus` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subject_tagging_schedules`
--

INSERT INTO `subject_tagging_schedules` (`STSID`, `STID`, `ClassroomID`, `STSHours`, `STSTimeStart`, `STSTimeEnd`, `STSDay`, `STSStatus`, `created_at`, `updated_at`) VALUES
(3, 3, 4, 2, '07:30:00', '09:30:00', 'Friday', 'Inactive', '2019-03-25 10:06:28', '2019-03-25 10:06:28'),
(9, 8, 5, 2, '07:30:00', '09:30:00', 'Monday', 'Inactive', '2019-03-25 10:08:00', '2019-03-25 10:08:00'),
(11, 10, 13, 3, '09:30:00', '12:30:00', 'Monday', 'Inactive', '2019-03-25 10:08:35', '2019-03-25 10:08:35'),
(12, 11, 12, 3, '07:30:00', '10:30:00', 'Thursday', 'Inactive', '2019-03-25 10:08:41', '2019-03-25 10:08:41'),
(13, 12, 10, 2, '07:30:00', '09:30:00', 'Wednesday', 'Inactive', '2019-03-25 10:08:47', '2019-03-25 10:08:47'),
(14, 12, 3, 2, '10:30:00', '12:30:00', 'Thursday', 'Inactive', '2019-03-25 10:08:47', '2019-03-25 10:08:47'),
(15, 13, 17, 2, '07:30:00', '09:30:00', 'Friday', 'Inactive', '2019-03-25 10:08:53', '2019-03-25 10:08:53'),
(16, 14, 23, 2, '09:30:00', '11:30:00', 'Saturday', 'Inactive', '2019-03-25 10:09:01', '2019-03-25 10:09:01'),
(17, 17, 27, 2, '11:30:00', '13:30:00', 'Saturday', 'Inactive', '2019-03-25 10:11:17', '2019-03-25 10:11:17'),
(18, 17, 5, 2, '09:30:00', '11:30:00', 'Friday', 'Inactive', '2019-03-25 10:11:17', '2019-03-25 10:11:17'),
(20, 21, 26, 2, '12:30:00', '14:30:00', 'Monday', 'Inactive', '2019-03-25 10:14:27', '2019-03-25 10:14:27'),
(21, 22, 25, 2, '07:30:00', '09:30:00', 'Saturday', 'Inactive', '2019-03-26 01:40:28', '2019-03-26 01:40:28'),
(22, 23, 15, 3, '07:30:00', '10:30:00', 'Wednesday', 'Inactive', '2019-03-26 01:40:59', '2019-03-26 01:40:59'),
(23, 24, 3, 2, '07:30:00', '09:30:00', 'Tuesday', 'Inactive', '2019-03-26 01:41:24', '2019-03-26 01:41:24'),
(31, 32, 19, 2, '07:30:00', '09:30:00', 'Wednesday', 'Inactive', '2019-03-28 05:20:27', '2019-03-28 05:20:27'),
(36, 37, 11, 2, '11:30:00', '13:30:00', 'Wednesday', 'Inactive', '2019-03-28 05:48:38', '2019-03-28 05:48:38'),
(41, 41, 3, 2, '14:00:00', '16:00:00', 'Wednesday', 'Inactive', '2019-03-28 06:32:37', '2019-03-28 06:32:37'),
(42, 41, 5, 2, '10:00:00', '12:00:00', 'Saturday', 'Inactive', '2019-03-28 06:32:37', '2019-03-28 06:32:37'),
(49, 48, 16, 2, '11:00:00', '13:00:00', 'Wednesday', 'Inactive', '2019-04-15 02:34:59', '2019-04-15 02:34:59'),
(60, 59, 2, 2, '07:30:00', '09:30:00', 'Wednesday', 'Inactive', '2019-04-18 08:27:58', '2019-04-18 08:27:58'),
(61, 59, 2, 2, '07:30:00', '09:30:00', 'Saturday', 'Inactive', '2019-04-18 08:27:58', '2019-04-18 08:27:58'),
(62, 60, 14, 3, '08:30:00', '11:30:00', 'Monday', 'Inactive', '2019-04-20 03:33:07', '2019-04-20 03:33:07'),
(67, 63, 13, 3, '08:30:00', '11:30:00', 'Monday', 'Inactive', '2019-05-02 04:09:52', '2019-05-02 04:09:52'),
(68, 63, 5, 3, '07:30:00', '10:30:00', 'Tuesday', 'Inactive', '2019-05-02 04:09:52', '2019-05-02 04:09:52'),
(69, 64, 30, 3, '07:30:00', '10:30:00', 'Wednesday', 'Inactive', '2019-05-02 04:39:56', '2019-05-02 04:39:56'),
(70, 65, 14, 3, '13:00:00', '16:00:00', 'Monday', 'Inactive', '2019-05-02 04:53:40', '2019-05-02 04:53:40'),
(71, 66, 12, 3, '09:00:00', '12:00:00', 'Monday', 'Active', '2019-06-09 06:37:05', '2019-06-09 06:37:05'),
(72, 67, 20, 3, '07:30:00', '10:30:00', 'Wednesday', 'Active', '2019-06-13 07:00:18', '2019-06-13 07:00:18'),
(73, 67, 5, 3, '07:30:00', '10:30:00', 'Tuesday', 'Active', '2019-06-13 07:00:18', '2019-06-13 07:00:18'),
(74, 68, 11, 3, '11:00:00', '14:00:00', 'Saturday', 'Active', '2019-06-18 11:32:38', '2019-06-18 11:32:38'),
(75, 69, 3, 3, '12:30:00', '15:30:00', 'Friday', 'Active', '2019-06-25 23:58:48', '2019-06-25 23:58:48'),
(76, 70, 10, 3, '14:30:00', '17:30:00', 'Saturday', 'Active', '2019-06-26 00:09:03', '2019-06-26 00:09:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `photo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'profile.png',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `type`, `photo`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'John Alfred Intia', 'johnintia30@gmail.com', '$2y$10$girG7V0cDZIOxgL.VOCWd.Jm.ECtzaqMr9..o1GQ7/xN9z58.fxPi', 'super admin', '1551099073.jpeg', 'qfG5XIQrMHNTZtQluRYn4jQFfuAVuv2te4UhsqtVdbQyNeR6Q8jBFuPythoA', '2019-01-23 02:28:38', '2019-02-25 04:51:14'),
(7, 'test1', 'admin@gmail.com', '$2y$10$MMxzXjzJslHuBubliUDi8uPXaAet3gyC7Fch2aR80PVgmUnIvWI5m', 'admin', '1553181066.jpeg', 'F6lDNUlAvA47arlaDOCAcNSBM1iI32ROFCbI3Bh3ZjTo9z5492U51TSn3xLE', '2019-01-23 13:45:15', '2019-06-18 11:13:42'),
(8, 'test2', 'administrative@gmail.com', '$2y$10$GDTcvjJc4FF3GfKaMSAiK.1by6SjBzPHkEna5tpPkuA9soUdAtzzK', 'administrative', '1551099215.png', 'ZBUan085Et4tcpsrf45KRBKlWBOFNkEEmwHZ6kE0SW4241pikwwwM7cb2tvA', '2019-02-25 12:43:39', '2019-06-18 11:13:52'),
(11, 'head of academics', 'hap@gmail.com', '$2y$10$lUXLKNV0kL3LB0WrmOEqJuw7V/MQJHmr/BBH7nB6cdgunMDxoeQAS', 'head of academics', '1560856460.jpeg', '66BgNZOukOR72Su7H06Z7auK1qzVQWw7eqfVDK2QZcfqyREob6J1XizZ9Xh4', '2019-06-18 05:18:25', '2019-06-25 23:04:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `buildings`
--
ALTER TABLE `buildings`
  ADD PRIMARY KEY (`BldgID`);

--
-- Indexes for table `classrooms`
--
ALTER TABLE `classrooms`
  ADD PRIMARY KEY (`ClassroomID`);

--
-- Indexes for table `classroom_types`
--
ALTER TABLE `classroom_types`
  ADD PRIMARY KEY (`CTID`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`CourseID`);

--
-- Indexes for table `course_subject_offereds`
--
ALTER TABLE `course_subject_offereds`
  ADD PRIMARY KEY (`CSOID`);

--
-- Indexes for table `days`
--
ALTER TABLE `days`
  ADD PRIMARY KEY (`DayID`);

--
-- Indexes for table `floorplans`
--
ALTER TABLE `floorplans`
  ADD PRIMARY KEY (`FloorplanID`);

--
-- Indexes for table `floors`
--
ALTER TABLE `floors`
  ADD PRIMARY KEY (`BFID`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_access_tokens_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_auth_codes`
--
ALTER TABLE `oauth_auth_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_clients_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_personal_access_clients_client_id_index` (`client_id`);

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `professors`
--
ALTER TABLE `professors`
  ADD PRIMARY KEY (`ProfessorID`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`SchedID`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`SectionID`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`SubjectID`);

--
-- Indexes for table `subject_meetings`
--
ALTER TABLE `subject_meetings`
  ADD PRIMARY KEY (`SMID`);

--
-- Indexes for table `subject_taggings`
--
ALTER TABLE `subject_taggings`
  ADD PRIMARY KEY (`STID`);

--
-- Indexes for table `subject_tagging_schedules`
--
ALTER TABLE `subject_tagging_schedules`
  ADD PRIMARY KEY (`STSID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `buildings`
--
ALTER TABLE `buildings`
  MODIFY `BldgID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `classrooms`
--
ALTER TABLE `classrooms`
  MODIFY `ClassroomID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `classroom_types`
--
ALTER TABLE `classroom_types`
  MODIFY `CTID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `CourseID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `course_subject_offereds`
--
ALTER TABLE `course_subject_offereds`
  MODIFY `CSOID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `days`
--
ALTER TABLE `days`
  MODIFY `DayID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `floorplans`
--
ALTER TABLE `floorplans`
  MODIFY `FloorplanID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `floors`
--
ALTER TABLE `floors`
  MODIFY `BFID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `professors`
--
ALTER TABLE `professors`
  MODIFY `ProfessorID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `SchedID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `SectionID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `SubjectID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `subject_meetings`
--
ALTER TABLE `subject_meetings`
  MODIFY `SMID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `subject_taggings`
--
ALTER TABLE `subject_taggings`
  MODIFY `STID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `subject_tagging_schedules`
--
ALTER TABLE `subject_tagging_schedules`
  MODIFY `STSID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
