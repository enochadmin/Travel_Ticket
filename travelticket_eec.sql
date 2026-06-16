-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 16, 2026 at 01:17 PM
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
-- Database: `travelticket_eec`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `region` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `name`, `region`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Addis Ababa', 'Addis Ababa', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(2, 'Dire Dawa', 'Dire Dawa', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(3, 'Semera', 'Afar', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(4, 'Asayita', 'Afar', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(5, 'Dubti', 'Afar', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(6, 'Awash', 'Afar', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(7, 'Bahir Dar', 'Amhara', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(8, 'Gondar', 'Amhara', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(9, 'Dessie', 'Amhara', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(10, 'Debre Markos', 'Amhara', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(11, 'Debre Birhan', 'Amhara', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(12, 'Kombolcha', 'Amhara', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(13, 'Woldiya', 'Amhara', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(14, 'Lalibela', 'Amhara', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(15, 'Debre Tabor', 'Amhara', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(16, 'Debre Sina', 'Amhara', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(17, 'Finote Selam', 'Amhara', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(18, 'Kemise', 'Amhara', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(19, 'Asosa', 'Benishangul-Gumuz', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(20, 'Pawe', 'Benishangul-Gumuz', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(21, 'Hosaena', 'Central Ethiopia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(22, 'Welkite', 'Central Ethiopia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(23, 'Butajira', 'Central Ethiopia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(24, 'Gambela', 'Gambela', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(25, 'Itang', 'Gambela', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(26, 'Harar', 'Harari', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(27, 'Adama', 'Oromia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(28, 'Jimma', 'Oromia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(29, 'Bishoftu', 'Oromia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(30, 'Nekemte', 'Oromia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(31, 'Shashamane', 'Oromia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(32, 'Ambo', 'Oromia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(33, 'Asella', 'Oromia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(34, 'Robe', 'Oromia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(35, 'Goba', 'Oromia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(36, 'Dembidolo', 'Oromia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(37, 'Waliso', 'Oromia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(38, 'Burayu', 'Oromia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(39, 'Sebeta', 'Oromia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(40, 'Ziway', 'Oromia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(41, 'Bule Hora', 'Oromia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(42, 'Gimbi', 'Oromia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(43, 'Metu', 'Oromia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(44, 'Ginir', 'Oromia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(45, 'Hawassa', 'Sidama', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(46, 'Yirgalem', 'Sidama', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(47, 'Dilla', 'Sidama', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(48, 'Aleta Wendo', 'Sidama', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(49, 'Jijiga', 'Somali', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(50, 'Gode', 'Somali', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(51, 'Degehabur', 'Somali', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(52, 'Kebri Dahar', 'Somali', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(53, 'Arba Minch', 'South Ethiopia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(54, 'Bonga', 'South Ethiopia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(55, 'Karat', 'South Ethiopia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(56, 'Konso', 'South Ethiopia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(57, 'Wolaita Sodo', 'South Ethiopia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(58, 'Mizan Teferi', 'Southwest Ethiopia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(59, 'Tepi', 'Southwest Ethiopia', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(60, 'Mekelle', 'Tigray', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(61, 'Axum', 'Tigray', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(62, 'Adigrat', 'Tigray', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(63, 'Shire', 'Tigray', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(64, 'Wukro', 'Tigray', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56'),
(65, 'Humera', 'Tigray', 1, '2026-06-15 04:54:56', '2026-06-15 04:54:56');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_02_28_142554_create_projects_table', 1),
(5, '2026_02_28_142558_create_travel_requests_table', 1),
(6, '2026_02_28_142705_add_project_id_to_users_table', 1),
(7, '2026_02_28_142922_create_permission_tables', 1),
(8, '2026_02_28_182800_add_fields_to_projects_table', 1),
(9, '2026_03_04_132413_update_status_enum_in_travel_requests_table', 1),
(10, '2026_03_04_203421_create_notifications_table', 1),
(11, '2026_03_04_204945_add_rejection_reason_to_travel_requests_table', 1),
(12, '2026_03_05_070425_add_pm_approved_at_to_travel_requests_table', 1),
(13, '2026_03_12_000000_add_origin_and_passenger_count_to_travel_requests_table', 1),
(14, '2026_03_27_000000_add_flight_type_and_pending_ceo_status_to_travel_requests_table', 1),
(15, '2026_03_27_000001_add_archived_fields_to_travel_requests_table', 1),
(16, '2026_06_05_000000_create_project_user_table', 1),
(17, '2026_06_06_000000_create_cities_table', 1),
(18, '2026_06_07_000000_add_must_change_password_to_users_table', 1),
(19, '2026_06_07_100000_add_hod_approved_at_to_travel_requests_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 20),
(2, 'App\\Models\\User', 22),
(2, 'App\\Models\\User', 23),
(2, 'App\\Models\\User', 24),
(2, 'App\\Models\\User', 25),
(2, 'App\\Models\\User', 26),
(2, 'App\\Models\\User', 27),
(2, 'App\\Models\\User', 28),
(2, 'App\\Models\\User', 29),
(2, 'App\\Models\\User', 30),
(2, 'App\\Models\\User', 31),
(2, 'App\\Models\\User', 32),
(2, 'App\\Models\\User', 33),
(3, 'App\\Models\\User', 34),
(3, 'App\\Models\\User', 35),
(3, 'App\\Models\\User', 36),
(3, 'App\\Models\\User', 37),
(3, 'App\\Models\\User', 38),
(3, 'App\\Models\\User', 39),
(4, 'App\\Models\\User', 41),
(6, 'App\\Models\\User', 40),
(8, 'App\\Models\\User', 42);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('20016778-b442-4f6e-967d-b956215ee919', 'App\\Notifications\\TicketStatusUpdated', 'App\\Models\\User', 22, '{\"ticket_id\":1,\"destination\":\"Mekelle\",\"message\":\"Your ticket for Mekelle has been submitted and is awaiting approval.\",\"type\":\"info\"}', '2026-06-15 06:30:01', '2026-06-15 06:23:30', '2026-06-15 06:30:01'),
('3268dca4-af1f-41ef-8379-8b28a7ccd4b1', 'App\\Notifications\\TicketStatusUpdated', 'App\\Models\\User', 42, '{\"ticket_id\":1,\"destination\":\"Mekelle\",\"message\":\"A ticket for Mekelle has been approved and is ready for processing.\",\"type\":\"info\"}', NULL, '2026-06-15 06:26:52', '2026-06-15 06:26:52'),
('4889a5c2-9c99-4d87-a087-3214f8cc5227', 'App\\Notifications\\TicketStatusUpdated', 'App\\Models\\User', 22, '{\"ticket_id\":2,\"destination\":\"Bahir Dar\",\"message\":\"Your ticket for Bahir Dar has been submitted and is awaiting approval.\",\"type\":\"info\"}', NULL, '2026-06-15 06:43:59', '2026-06-15 06:43:59'),
('55d90c2d-a917-4f9d-bf27-8b9752fa7971', 'App\\Notifications\\TicketStatusUpdated', 'App\\Models\\User', 34, '{\"ticket_id\":2,\"destination\":\"Bahir Dar\",\"message\":\"user 1 submitted a new ticket for Bahir Dar awaiting your approval.\",\"type\":\"warning\"}', NULL, '2026-06-15 06:43:59', '2026-06-15 06:43:59'),
('6a573070-1257-440c-b921-485a7500990a', 'App\\Notifications\\TicketStatusUpdated', 'App\\Models\\User', 40, '{\"ticket_id\":1,\"destination\":\"Mekelle\",\"message\":\"New ticket for Mekelle was approved by PM and awaits your approval.\",\"type\":\"warning\"}', NULL, '2026-06-15 06:23:58', '2026-06-15 06:23:58'),
('6fc05540-597e-457a-a0fc-8e828bb2c84c', 'App\\Notifications\\TicketStatusUpdated', 'App\\Models\\User', 22, '{\"ticket_id\":1,\"destination\":\"Mekelle\",\"message\":\"Your ticket for Mekelle was approved by your PM and sent to the Director.\",\"type\":\"info\"}', '2026-06-15 06:30:01', '2026-06-15 06:23:58', '2026-06-15 06:30:01'),
('bd553da0-4eb8-4435-a7bd-92a1e6fc53da', 'App\\Notifications\\TicketStatusUpdated', 'App\\Models\\User', 22, '{\"ticket_id\":1,\"destination\":\"Mekelle\",\"message\":\"Your ticket for Mekelle has been FINALLY APPROVED.\",\"type\":\"success\"}', '2026-06-15 06:30:01', '2026-06-15 06:26:52', '2026-06-15 06:30:01'),
('c871830e-c853-4b43-9088-6e5a8da3826e', 'App\\Notifications\\TicketStatusUpdated', 'App\\Models\\User', 40, '{\"ticket_id\":2,\"destination\":\"Bahir Dar\",\"message\":\"New ticket for Bahir Dar was approved by PM and awaits your approval.\",\"type\":\"warning\"}', NULL, '2026-06-15 07:53:14', '2026-06-15 07:53:14'),
('d3c820d0-1729-4f27-9129-d90073f7c50b', 'App\\Notifications\\TicketStatusUpdated', 'App\\Models\\User', 34, '{\"ticket_id\":1,\"destination\":\"Mekelle\",\"message\":\"user 1 submitted a new ticket for Mekelle awaiting your approval.\",\"type\":\"warning\"}', NULL, '2026-06-15 06:23:30', '2026-06-15 06:23:30'),
('d5be9f72-edb1-4ff0-8015-59dca03ba7a1', 'App\\Notifications\\TicketStatusUpdated', 'App\\Models\\User', 22, '{\"ticket_id\":2,\"destination\":\"Bahir Dar\",\"message\":\"Your ticket for Bahir Dar was approved by your PM and sent to the Director.\",\"type\":\"info\"}', NULL, '2026-06-15 07:53:13', '2026-06-15 07:53:13');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'dashboard.view', 'web', '2026-06-15 06:08:08', '2026-06-15 06:08:08'),
(2, 'users.view', 'web', '2026-06-15 06:08:08', '2026-06-15 06:08:08'),
(3, 'users.create', 'web', '2026-06-15 06:08:08', '2026-06-15 06:08:08'),
(4, 'users.update', 'web', '2026-06-15 06:08:08', '2026-06-15 06:08:08'),
(5, 'users.delete', 'web', '2026-06-15 06:08:08', '2026-06-15 06:08:08'),
(6, 'roles.view', 'web', '2026-06-15 06:08:08', '2026-06-15 06:08:08'),
(7, 'roles.create', 'web', '2026-06-15 06:08:08', '2026-06-15 06:08:08'),
(8, 'roles.update', 'web', '2026-06-15 06:08:08', '2026-06-15 06:08:08'),
(9, 'roles.delete', 'web', '2026-06-15 06:08:08', '2026-06-15 06:08:08'),
(10, 'projects.view', 'web', '2026-06-15 06:08:08', '2026-06-15 06:08:08'),
(11, 'projects.create', 'web', '2026-06-15 06:08:08', '2026-06-15 06:08:08'),
(12, 'projects.update', 'web', '2026-06-15 06:08:08', '2026-06-15 06:08:08'),
(13, 'projects.delete', 'web', '2026-06-15 06:08:08', '2026-06-15 06:08:08'),
(14, 'travel-requests.view', 'web', '2026-06-15 06:08:08', '2026-06-15 06:08:08'),
(15, 'travel-requests.create', 'web', '2026-06-15 06:08:08', '2026-06-15 06:08:08'),
(16, 'travel-requests.approve', 'web', '2026-06-15 06:08:08', '2026-06-15 06:08:08'),
(17, 'travel-requests.reject', 'web', '2026-06-15 06:08:08', '2026-06-15 06:08:08'),
(18, 'reports.view', 'web', '2026-06-15 06:08:08', '2026-06-15 06:08:08'),
(19, 'settings.view', 'web', '2026-06-15 06:08:08', '2026-06-15 06:08:08'),
(20, 'settings.update', 'web', '2026-06-15 06:08:08', '2026-06-15 06:08:08'),
(21, 'reception.view', 'web', '2026-06-15 06:08:08', '2026-06-15 06:08:08'),
(22, 'reception.process-tickets', 'web', '2026-06-15 06:08:08', '2026-06-15 06:08:08');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `project_code` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `discipline` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `name`, `project_code`, `location`, `region`, `discipline`, `description`, `created_at`, `updated_at`, `manager_id`, `start_date`, `end_date`, `status`) VALUES
(1, 'Project 1', 'EEC01', 'Addis Ababa', NULL, 'Building', NULL, '2026-06-15 06:17:54', '2026-06-15 07:08:56', 34, '2024-01-01', '2028-10-01', 'active'),
(2, 'Project 2', 'EEC02', 'Addis Ababa', NULL, 'Building', NULL, '2026-06-15 06:17:54', '2026-06-15 06:17:54', NULL, '1970-01-01', '1970-01-01', 'active'),
(3, 'Project 3', 'EEC03', 'Addis Ababa', NULL, 'Building', NULL, '2026-06-15 06:17:54', '2026-06-15 06:17:54', NULL, '1970-01-01', '1970-01-01', 'active'),
(4, 'Project 4', 'EEC04', 'Mekele', NULL, 'Building', NULL, '2026-06-15 06:17:54', '2026-06-15 06:17:54', NULL, '1970-01-01', '1970-01-01', 'completed'),
(5, 'Project 5', 'EEC05', 'Bahir Dar', NULL, 'Water', NULL, '2026-06-15 06:17:54', '2026-06-15 06:17:54', NULL, '1970-01-01', '1970-01-01', 'active'),
(6, 'Project 6', 'EEC06', 'Nekemet', NULL, 'Infrastructure', NULL, '2026-06-15 06:17:54', '2026-06-15 06:17:54', NULL, '1970-01-01', '1970-01-01', 'active'),
(7, 'Project 7', 'EEC07', 'Jimma', NULL, 'Infrastructure', NULL, '2026-06-15 06:17:54', '2026-06-15 06:17:54', NULL, '1970-01-01', '1970-01-01', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `project_user`
--

CREATE TABLE `project_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_user`
--

INSERT INTO `project_user` (`id`, `project_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 22, '2026-06-15 06:21:37', '2026-06-15 06:21:37'),
(2, 1, 34, '2026-06-15 06:21:37', '2026-06-15 06:21:37');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2026-06-15 04:54:53', '2026-06-15 04:54:53'),
(2, 'user', 'web', '2026-06-15 04:54:55', '2026-06-15 04:54:55'),
(3, 'project-manager', 'web', '2026-06-15 04:54:55', '2026-06-15 04:54:55'),
(4, 'head-office-manager', 'web', '2026-06-15 04:54:55', '2026-06-15 04:54:55'),
(5, 'head-office-director', 'web', '2026-06-15 04:54:55', '2026-06-15 04:54:55'),
(6, 'commercial-director', 'web', '2026-06-15 04:54:55', '2026-06-15 04:54:55'),
(7, 'ceo', 'web', '2026-06-15 04:54:55', '2026-06-15 04:54:55'),
(8, 'reception', 'web', '2026-06-15 04:54:55', '2026-06-15 04:54:55');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 6),
(10, 6),
(14, 6),
(15, 6),
(16, 6),
(17, 6);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('29k0cVvkIldOhrxFo0Fs8Tuw5EBk1qPQzUcy1WnS', 42, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMVJncEJOMURMUld3UkVsSTVsVklOOTY0WkloSXpqbXJTY3pCSWxKQSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9yZWNlcHRpb24vZGFzaGJvYXJkIjtzOjU6InJvdXRlIjtzOjE5OiJyZWNlcHRpb24uZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NDI7fQ==', 1781600966),
('Aaii2jRxhdSWhjvpflZzeXR9dZfTYWWTUCpVwwqg', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiT2kyeHljMEVRNG5PbFFIVzV0TVNNQ2xuaW8xWkRpQ0EwU1BMUEJYQSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2Rhc2hib2FyZCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvZGFzaGJvYXJkIjtzOjU6InJvdXRlIjtzOjk6ImRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1781515445),
('aILhULKRKYqPE6VnV916tm6wdxwkvB2XQoSzj4h0', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUWU4OXlyQUJPbjhMaFdaQWdpWWRMQXRIaUpZaDJkRXJBYUlRbm5ZRyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1781591509),
('bdUiPoOB5j9HF6pakuyTloOrQR2ULYJorgU8kbdT', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWFdSMDY0UFFPOVptMTNCalB0STZrdmdxQ3NMVlhVeTBMOGdoQ0p6VCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1MToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3RyYXZlbC1yZXF1ZXN0cz92aWV3PXBlcnNvbmFsIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1781540413),
('DKTpGlTGavX0wnKrW1NK8Ooysc3iLTklXVvYqzg8', 35, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTXhQZkFLN3lDczNVaVRWd2t1bE4zVDFMSlRoY2Rkd3NOaTFpMnZROSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MzU7fQ==', 1781520177),
('dOrXYMcRyr4VPiYdkYDPtuRbD9Azf48bZd2IC7Kf', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRklBWlhBZE16ZTFMVG51c2FrVmxUaUJjRms5T0o0aXBXNGY5Y0ZzMyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2Rhc2hib2FyZCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1781515453),
('f3QhH4rKxDNCsEJ0Ucw4xLUerrUyNLL1gV6GEvle', 20, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiT2hmZm9VeXphVDdXMjhWQm9zaU9JRVNqd0FqV0x4czBwZVRpYms5eiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM2OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvc2V0dGluZ3Mvcm9sZXMiO3M6NToicm91dGUiO3M6MjA6InNldHRpbmdzLnJvbGVzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjA7fQ==', 1781522934),
('h4eCSxq6IiRqABBmRgX3B8F751ZbdQJRbWeaHg1a', 20, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoickRQR2UxN3NiVEtGSE1MN0NURzBGSXh0Z1c5R0FZMzlBdmxidUU1byI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjA7fQ==', 1781591599),
('Hq8SXrPNRFLxPXTtr4hWlFkLHWSR8gXB886FAnyv', 20, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoibjZWbWtwTEJKTEJ1NWl6TmJNazNHenVHUXdHbUdOZTg4STAwOGd0YiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvZGFzaGJvYXJkIjtzOjU6InJvdXRlIjtzOjk6ImRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjIwO30=', 1781601382),
('kDA01gkPFgfViFaRCk0NFBAytRULWFHn55jDwYzi', 22, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiakh1VkJ4OXBad1JoWnY5aUIwS2UwTXF5anh4ekdDSjMwcVJXSW5NeCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjUxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvdHJhdmVsLXJlcXVlc3RzP3ZpZXc9cGVyc29uYWwiO3M6NToicm91dGUiO3M6MjE6InRyYXZlbC1yZXF1ZXN0cy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjIyO30=', 1781522841),
('ow4KTiCC2G3FZHKXQ90Ayz0KTJbfANuEsB2RXH9z', 42, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiblBwcGpUSWIyTFNpa2ZDdmkyUkgxSWVtejFxaENObGVtazFSTzk2dSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9yZWNlcHRpb24vdGlja2V0cy9hcmNoaXZlZCI7czo1OiJyb3V0ZSI7czoyNjoicmVjZXB0aW9uLnRpY2tldHMuYXJjaGl2ZWQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo0Mjt9', 1781520173),
('pWye4jbrO9VWAFhh9DjqgdWr3lILqUQFG1A4zIf7', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZUJWY1ozM3FwZ3pUcTN2aHFLS0tBaTRvclkxcUlkTU84UDdCbDFjNCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1781562040),
('TL3QS4xrNzy7fcJTLZQGugx7eRPiNFd9PPESswsD', 34, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiYVZHMHBBWXBDRmY2WnprRkVtUmdwQk9zTVlRVWVqcVp5TXNVdFpqciI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM5OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvdHJhdmVsLXJlcXVlc3RzLzIiO3M6NToicm91dGUiO3M6MjA6InRyYXZlbC1yZXF1ZXN0cy5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MzQ7fQ==', 1781521847),
('TXm5iT3wyccFW1wOLOkMA3dRNBVuWEeIgeFosf2z', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOU5Sb085UE11YUlWUU1HZWdEeXJHOFJtcEFSclNPd0hsUG96TkJhaSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1781515449),
('uhBW8sUt8lrg1U6PHFQC9SqbqymOOmokOQ2oWjEJ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTTdka2dDdG1GbkhHYnpZVzJiaE1NUXdKcDVkOEZvbHY5NEVyNzQyeCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozOToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3RyYXZlbC1yZXF1ZXN0cy8yIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1781591753),
('v6RnrNBPe8GqLrujdlaNsYNounQteH26tmaviLa5', 40, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZEg0UU5EZ0E3TUVQV204bDB2VEpRRVM5Um02eGl6MHc1VTBIOTBDbCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wcm9qZWN0cyI7czo1OiJyb3V0ZSI7czoxNDoicHJvamVjdHMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo0MDt9', 1781521845),
('zMYephvK1AlBMZ8L2fxqZbpN3bR2W0hJCEVyrHKB', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibkNWdGJyZ2dHMWpIem9PRTNRSzEyOWduaFczbjJiSmZnM2hheEY2SCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3NldHRpbmdzL3JvbGVzIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1781530284);

-- --------------------------------------------------------

--
-- Table structure for table `travel_requests`
--

CREATE TABLE `travel_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `destination` varchar(255) NOT NULL,
  `origin` varchar(255) DEFAULT NULL,
  `passenger_count` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `flight_type` enum('national','international') NOT NULL DEFAULT 'national',
  `travel_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `purpose` varchar(255) NOT NULL,
  `status` enum('pending_pm','pending_commercial','pending_hod','pending_ceo','approved','rejected') DEFAULT 'pending_pm',
  `rejection_reason` text DEFAULT NULL,
  `pm_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pm_approved_at` timestamp NULL DEFAULT NULL,
  `archived_at` timestamp NULL DEFAULT NULL,
  `archived_by` bigint(20) UNSIGNED DEFAULT NULL,
  `hod_id` bigint(20) UNSIGNED DEFAULT NULL,
  `hod_approved_at` timestamp NULL DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `travel_requests`
--

INSERT INTO `travel_requests` (`id`, `user_id`, `project_id`, `destination`, `origin`, `passenger_count`, `flight_type`, `travel_date`, `return_date`, `purpose`, `status`, `rejection_reason`, `pm_id`, `pm_approved_at`, `archived_at`, `archived_by`, `hod_id`, `hod_approved_at`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 22, 1, 'Mekelle', 'Addis Ababa', 1, 'national', '2026-06-17', '2026-06-22', 'Site Visit', 'approved', NULL, 34, '2026-06-15 06:23:58', '2026-06-15 06:29:13', 42, 40, '2026-06-15 06:26:52', NULL, '2026-06-15 06:23:30', '2026-06-15 06:29:13'),
(2, 22, 1, 'Bahir Dar', 'Addis Ababa', 1, 'national', '2026-06-18', NULL, 'Client Meeting', 'pending_commercial', NULL, 34, '2026-06-15 07:53:11', NULL, NULL, NULL, NULL, NULL, '2026-06-15 06:43:57', '2026-06-15 07:53:11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `project_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `must_change_password`, `remember_token`, `created_at`, `updated_at`, `project_id`) VALUES
(20, 'Admin User', 'admin@admin.com', NULL, '$2y$12$QPpGUYN.ao2UFZ68E.wZyOSTp5lUEIYItc/CoQE3uAixUsLRHv1hy', 0, NULL, '2026-06-15 04:54:56', '2026-06-15 04:54:56', NULL),
(22, 'user 1', 'user1@example.com', NULL, '$2y$12$cVAIb3fq4BONabGNNxFoq.2s4951MPpAhDWG4I8nMcLjEZRX5fmtC', 0, NULL, '2026-06-15 06:13:03', '2026-06-15 06:22:34', 1),
(23, 'User 2', 'user2@example.com', NULL, '$2y$12$n7uamjLtI.U4l06gDp6yEuP1ahRQEX2SSECzmHPxzLpjX.22Z51f2', 1, NULL, '2026-06-15 06:13:04', '2026-06-15 06:13:04', NULL),
(24, 'user 3', 'user3@example.com', NULL, '$2y$12$yscT4jUtqysfm7f5.9kVE.un.GBxrSZN07vM.WcokJAjWYO7mYkTi', 1, NULL, '2026-06-15 06:13:05', '2026-06-15 06:13:05', NULL),
(25, 'user 4', 'user4@example.com', NULL, '$2y$12$G2/SYjr8FkhUfrYQMJGkIOQF8YaOme5/TaURb.xO1HtB3CUFYMOSS', 1, NULL, '2026-06-15 06:13:06', '2026-06-15 06:13:06', NULL),
(26, 'user 5', 'user5@example.com', NULL, '$2y$12$VNVS.A8XiI.0a2I74tqr3ehFYcn2pTmdyHO9bN4UjY9AncltXG.ai', 1, NULL, '2026-06-15 06:13:07', '2026-06-15 06:13:07', NULL),
(27, 'user 6', 'user6@example.com', NULL, '$2y$12$nNPrgPkwwjdpin4DsI1h2./t0rNawF0B6uFkPs.Jjfeol8K4h1Qpa', 1, NULL, '2026-06-15 06:13:08', '2026-06-15 06:13:08', NULL),
(28, 'user 7', 'user7@example.com', NULL, '$2y$12$rFlxxYDz7hA4ffklFYQ7k.qN6kTiQ3vm2K5hDrcD0CpXdJnCYDMOW', 1, NULL, '2026-06-15 06:13:10', '2026-06-15 06:13:10', NULL),
(29, 'user 8', 'user8@example.com', NULL, '$2y$12$1YvdBBGbwRtjUnKkhaLfRuYg6cZarLLDm95VBh.usx2v181L8Q5gG', 1, NULL, '2026-06-15 06:13:11', '2026-06-15 06:13:11', NULL),
(30, 'user 9', 'user9@example.com', NULL, '$2y$12$FxhLTinrnbBCauy7Nf12XuNi6yrYyiqMl2DZKL4nOIE5VKQpRkIKe', 1, NULL, '2026-06-15 06:13:12', '2026-06-15 06:13:12', NULL),
(31, 'user 10', 'user10@example.com', NULL, '$2y$12$ARLBIdnn4VbfgbP.YzgHPOn5be9k07bE7D6Rv7giDE5xg0zsHhKvm', 1, NULL, '2026-06-15 06:13:13', '2026-06-15 06:13:13', NULL),
(32, 'user 11', 'user11@example.com', NULL, '$2y$12$bWyw6tdjIhJb.4oNRDFHweoCxEShIuj36QflI0lG/sOk/Czor2hkK', 1, NULL, '2026-06-15 06:13:15', '2026-06-15 06:13:15', NULL),
(33, 'user 12', 'user12@example.com', NULL, '$2y$12$8VCUoSZL8tnvowPAcRP9HeLlfzIWTeLPXT3GnFnll2YPJlGp/0U56', 1, NULL, '2026-06-15 06:13:16', '2026-06-15 06:13:16', NULL),
(34, 'PM 1', 'pm1@example.com', NULL, '$2y$12$ALNDm9ZltZHjVWnq5G.afeGI7E255hH80rua9niT853i0pEq59qGq', 0, NULL, '2026-06-15 06:13:16', '2026-06-15 07:08:56', 1),
(35, 'PM 2', 'pm2@example.com', NULL, '$2y$12$woP74dESqWsgfku4ily6J.wXcqILAXI3V906IZgawoO99hH0DV2SG', 0, NULL, '2026-06-15 06:13:17', '2026-06-15 07:44:55', NULL),
(36, 'PM 3', 'pm3@example.com', NULL, '$2y$12$nBA8tjDwkyQketzivrGw8OdfpHW5hHGRjeVqZfWUPXDHeIhBXse5y', 1, NULL, '2026-06-15 06:13:18', '2026-06-15 06:13:18', NULL),
(37, 'PM 4', 'pm4@example.com', NULL, '$2y$12$K7o9Z1xQKSd3/435Xe.af.rPJy1Ir3UvrwEct72fw2VvBdj2pAXKO', 1, NULL, '2026-06-15 06:13:18', '2026-06-15 06:13:18', NULL),
(38, 'PM 5', 'pm5@example.com', NULL, '$2y$12$Stmfbg5H.RFMTP4twhercOks27ngUyM1zoytu6sOuV/uESBafkl/S', 1, NULL, '2026-06-15 06:13:19', '2026-06-15 06:13:19', NULL),
(39, 'PM 6', 'pm6@example.com', NULL, '$2y$12$CESFSppCU6iX6tXGqAO5CeI5/p8PN3PHZlbfRzfTpc9nrk71UILdC', 1, NULL, '2026-06-15 06:13:20', '2026-06-15 06:13:20', NULL),
(40, 'Commercial Director', 'cm@example.com', NULL, '$2y$12$sPv4wdZuF8X/ckOrkoU4QujNgGtTgg7Z1rGBfEgwoYtAuXg7/bX3W', 0, NULL, '2026-06-15 06:13:22', '2026-06-15 06:24:55', NULL),
(41, 'Head Office Manager', 'ho@example.com', NULL, '$2y$12$nX51mOhhlIEiBT8.K3bjcu3xSQirtRJCYo31eejaHktwsbU3WUhuW', 1, NULL, '2026-06-15 06:13:22', '2026-06-15 06:13:22', NULL),
(42, 'Reception User', 'reception@example.com', NULL, '$2y$12$x7EGpuKVocaQ14o/.JGRt.8cG1VZ7FYbxxbgcrhm20Y6cbacOkHOy', 0, NULL, '2026-06-15 06:26:11', '2026-06-15 06:26:29', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cities_name_unique` (`name`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `projects_project_code_unique` (`project_code`),
  ADD KEY `projects_manager_id_foreign` (`manager_id`);

--
-- Indexes for table `project_user`
--
ALTER TABLE `project_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_user_project_id_user_id_unique` (`project_id`,`user_id`),
  ADD KEY `project_user_user_id_foreign` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `travel_requests`
--
ALTER TABLE `travel_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `travel_requests_user_id_foreign` (`user_id`),
  ADD KEY `travel_requests_project_id_foreign` (`project_id`),
  ADD KEY `travel_requests_pm_id_foreign` (`pm_id`),
  ADD KEY `travel_requests_hod_id_foreign` (`hod_id`),
  ADD KEY `travel_requests_archived_by_foreign` (`archived_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_project_id_foreign` (`project_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `project_user`
--
ALTER TABLE `project_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `travel_requests`
--
ALTER TABLE `travel_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `project_user`
--
ALTER TABLE `project_user`
  ADD CONSTRAINT `project_user_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `travel_requests`
--
ALTER TABLE `travel_requests`
  ADD CONSTRAINT `travel_requests_archived_by_foreign` FOREIGN KEY (`archived_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `travel_requests_hod_id_foreign` FOREIGN KEY (`hod_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `travel_requests_pm_id_foreign` FOREIGN KEY (`pm_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `travel_requests_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `travel_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
