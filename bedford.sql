-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: custsql-ipg20.eigbox.net
-- Generation Time: Oct 26, 2022 at 04:34 AM
-- Server version: 5.6.50-90.0-log
-- PHP Version: 7.0.33-0ubuntu0.16.04.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bedford`
--

-- --------------------------------------------------------

--
-- Table structure for table `activations`
--

CREATE TABLE `activations` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activations`
--

INSERT INTO `activations` (`id`, `user_id`, `code`, `completed`, `completed_at`, `created_at`, `updated_at`) VALUES
(1, 1, '4dZuuegowhwrv2Hmc7iOqgDFnhlxGuHF', 1, '2018-09-10 10:01:19', '2018-09-10 10:01:19', '2018-09-10 10:01:19'),
(5, 0, '1wqpyNjP61e8NiRRWZExlolVLUxTtUzV', 1, '2019-02-04 17:15:29', '2019-02-04 17:15:29', '2019-02-04 17:15:29'),
(42, 22, 'sIBDAXTWVIE8lhwTEzr593hK3ezaLatz', 1, '2022-10-08 11:55:58', '2022-10-08 11:55:58', '2022-10-08 11:55:58'),
(43, 23, 'JitRpLzeQgfMI5omKY83hNkDHthS4AJN', 1, '2022-10-08 12:18:58', '2022-10-08 12:18:58', '2022-10-08 12:18:58'),
(44, 24, 'ytQVyJqAMkG1zeChefwfVSF2lleb0xvu', 1, '2022-10-08 12:20:14', '2022-10-08 12:20:14', '2022-10-08 12:20:14'),
(45, 25, 'GjnfQlBbes7f11DNA21x9tzGzjmk6CZ9', 1, '2022-10-08 12:21:26', '2022-10-08 12:21:26', '2022-10-08 12:21:26'),
(46, 26, 'SVgf69FE0qMAOBx8kxqzCmy4joLTMecQ', 1, '2022-10-08 12:22:52', '2022-10-08 12:22:52', '2022-10-08 12:22:52'),
(47, 27, 'ClW0Xa2XdChEJQgs9KTAHKx46muGplQx', 1, '2022-10-08 12:25:51', '2022-10-08 12:25:51', '2022-10-08 12:25:51');

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` int(10) UNSIGNED NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `asset_type_id` int(11) DEFAULT NULL,
  `office_id` int(11) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_price` decimal(65,2) DEFAULT NULL,
  `value` decimal(65,2) DEFAULT NULL,
  `life_span` int(11) DEFAULT NULL,
  `salvage_value` decimal(65,2) DEFAULT NULL,
  `serial_number` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `files` text COLLATE utf8mb4_unicode_ci,
  `purchase_year` text COLLATE utf8mb4_unicode_ci,
  `status` enum('active','inactive','sold','damaged','written_off') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(4) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_depreciation`
--

CREATE TABLE `asset_depreciation` (
  `id` int(10) UNSIGNED NOT NULL,
  `asset_id` int(11) DEFAULT NULL,
  `year` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `beginning_value` decimal(65,2) DEFAULT NULL,
  `depreciation_value` decimal(65,2) DEFAULT NULL,
  `rate` decimal(65,2) DEFAULT NULL,
  `cost` decimal(65,2) DEFAULT NULL,
  `accumulated` decimal(65,2) DEFAULT NULL,
  `ending_value` decimal(65,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_types`
--

CREATE TABLE `asset_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gl_account_fixed_asset_id` int(11) DEFAULT NULL,
  `gl_account_asset_id` int(11) DEFAULT NULL,
  `gl_account_contra_asset_id` int(11) DEFAULT NULL,
  `gl_account_expense_id` int(11) DEFAULT NULL,
  `gl_account_liability_id` int(11) DEFAULT NULL,
  `gl_account_income_id` int(11) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `asset_types`
--

INSERT INTO `asset_types` (`id`, `name`, `gl_account_fixed_asset_id`, `gl_account_asset_id`, `gl_account_contra_asset_id`, `gl_account_expense_id`, `gl_account_liability_id`, `gl_account_income_id`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Motor Vehicles', 11, 1, 7, 49, NULL, 81, NULL, '2019-02-19 23:38:12', '2019-02-19 23:38:12'),
(2, 'Computers', 8, 120, 8, 48, NULL, 81, NULL, '2019-04-27 23:00:22', '2019-04-27 23:00:22');

-- --------------------------------------------------------

--
-- Table structure for table `audit_trail`
--

CREATE TABLE `audit_trail` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `office_id` int(11) DEFAULT NULL,
  `module` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_trail`
--

INSERT INTO `audit_trail` (`id`, `user_id`, `name`, `office_id`, `module`, `action`, `notes`, `created_at`, `updated_at`) VALUES
(1, 25, 'Lubinda Haabazoka', 1, 'Branches', 'Update', '1', '2022-10-09 23:56:41', '2022-10-09 23:56:41'),
(2, 25, 'Lubinda Haabazoka', 1, 'Branches', 'Update', '1', '2022-10-09 23:58:30', '2022-10-09 23:58:30'),
(3, 25, 'Lubinda Haabazoka', 1, 'Branches', 'Update', '1', '2022-10-09 23:59:25', '2022-10-09 23:59:25'),
(4, 26, 'Maria Haabazoka', 1, 'Clients', 'Create', '1', '2022-10-10 19:44:06', '2022-10-10 19:44:06'),
(5, 26, 'Maria Haabazoka', 1, 'Clients', 'Create', '2', '2022-10-10 19:44:10', '2022-10-10 19:44:10'),
(6, 26, 'Maria Haabazoka', 1, 'Clients', 'Delete', '2', '2022-10-11 01:03:09', '2022-10-11 01:03:09'),
(7, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '1', '2022-10-11 01:12:17', '2022-10-11 01:12:17'),
(8, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '1', '2022-10-11 01:30:22', '2022-10-11 01:30:22'),
(9, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '1', '2022-10-11 01:32:22', '2022-10-11 01:32:22'),
(10, 26, 'Maria Haabazoka', 1, 'Clients', 'Approve', '1', '2022-10-11 01:33:42', '2022-10-11 01:33:42'),
(11, 26, 'Maria Haabazoka', 1, 'Loans', 'Create', '1', '2022-10-11 01:41:57', '2022-10-11 01:41:57'),
(12, 26, 'Maria Haabazoka', 1, 'Loans', 'Update', '1', '2022-10-11 01:44:16', '2022-10-11 01:44:16'),
(13, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '1', '2022-10-11 01:51:15', '2022-10-11 01:51:15'),
(14, 26, 'Maria Haabazoka', 1, 'Loan Product', 'Update', '2', '2022-10-11 02:06:31', '2022-10-11 02:06:31'),
(15, 26, 'Maria Haabazoka', 1, 'Loans', 'Unapprove', '1', '2022-10-11 02:07:18', '2022-10-11 02:07:18'),
(16, 26, 'Maria Haabazoka', 1, 'Loans', 'Create', '2', '2022-10-11 02:10:46', '2022-10-11 02:10:46'),
(17, 26, 'Maria Haabazoka', 1, 'Loans', 'Update', '2', '2022-10-11 02:11:32', '2022-10-11 02:11:32'),
(18, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '2', '2022-10-11 02:12:06', '2022-10-11 02:12:06'),
(19, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '2', '2022-10-11 02:12:54', '2022-10-11 02:12:54'),
(20, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '2', '2022-10-11 02:15:44', '2022-10-11 02:15:44'),
(21, 26, 'Maria Haabazoka', 1, 'Loans', 'Withdraw', '1', '2022-10-11 02:24:48', '2022-10-11 02:24:48'),
(22, 26, 'Maria Haabazoka', 1, 'Loans', 'Undo Disburse', '2', '2022-10-11 02:27:57', '2022-10-11 02:27:57'),
(23, 26, 'Maria Haabazoka', 1, 'Loans', 'Unapprove', '2', '2022-10-11 02:28:35', '2022-10-11 02:28:35'),
(24, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '2', '2022-10-11 02:30:31', '2022-10-11 02:30:31'),
(25, 26, 'Maria Haabazoka', 1, 'Loans', 'Unapprove', '2', '2022-10-11 02:30:50', '2022-10-11 02:30:50'),
(26, 26, 'Maria Haabazoka', 1, 'Loans', 'Update', '2', '2022-10-11 02:31:49', '2022-10-11 02:31:49'),
(27, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '2', '2022-10-11 02:32:21', '2022-10-11 02:32:21'),
(28, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '2', '2022-10-11 02:34:37', '2022-10-11 02:34:37'),
(29, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Charge', '2', '2022-10-11 02:36:48', '2022-10-11 02:36:48'),
(30, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Charge', '2', '2022-10-11 02:36:50', '2022-10-11 02:36:50'),
(31, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Charge', '2', '2022-10-11 02:37:57', '2022-10-11 02:37:57'),
(32, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Charge', '2', '2022-10-11 02:37:58', '2022-10-11 02:37:58'),
(33, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '1', '2022-10-11 14:50:56', '2022-10-11 14:50:56'),
(34, 26, 'Maria Haabazoka', 1, 'Clients', 'Create', '3', '2022-10-11 15:05:15', '2022-10-11 15:05:15'),
(35, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '3', '2022-10-11 15:06:54', '2022-10-11 15:06:54'),
(36, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '122', '2022-10-11 15:10:20', '2022-10-11 15:10:20'),
(37, 26, 'Maria Haabazoka', 1, 'Clients', 'Approve', '3', '2022-10-11 15:11:04', '2022-10-11 15:11:04'),
(38, 26, 'Maria Haabazoka', 1, 'Loan Product', 'Delete', '4', '2022-10-11 15:13:34', '2022-10-11 15:13:34'),
(39, 26, 'Maria Haabazoka', 1, 'Loan Product', 'Delete', '1', '2022-10-11 15:13:50', '2022-10-11 15:13:50'),
(40, 26, 'Maria Haabazoka', 1, 'Loan Product', 'Delete', '5', '2022-10-11 15:14:07', '2022-10-11 15:14:07'),
(41, 26, 'Maria Haabazoka', 1, 'Loan Product', 'Delete', '6', '2022-10-11 15:14:15', '2022-10-11 15:14:15'),
(42, 26, 'Maria Haabazoka', 1, 'Loan Product', 'Create', '7', '2022-10-11 15:25:03', '2022-10-11 15:25:03'),
(43, 26, 'Maria Haabazoka', 1, 'Loan Product', 'Update', '2', '2022-10-11 15:37:32', '2022-10-11 15:37:32'),
(44, 26, 'Maria Haabazoka', 1, 'Loan Product', 'Update', '3', '2022-10-11 15:44:02', '2022-10-11 15:44:02'),
(45, 26, 'Maria Haabazoka', 1, 'Loan Product', 'Create', '8', '2022-10-11 15:54:45', '2022-10-11 15:54:45'),
(46, 26, 'Maria Haabazoka', 1, 'Loan Product', 'Update', '3', '2022-10-11 15:56:02', '2022-10-11 15:56:02'),
(47, 26, 'Maria Haabazoka', 1, 'Loan Product', 'Update', '2', '2022-10-11 15:57:00', '2022-10-11 15:57:00'),
(48, 26, 'Maria Haabazoka', 1, 'Loans', 'Undo Disburse', '2', '2022-10-11 16:03:25', '2022-10-11 16:03:25'),
(49, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '2', '2022-10-11 16:06:04', '2022-10-11 16:06:04'),
(50, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '2', '2022-10-11 16:08:02', '2022-10-11 16:08:02'),
(51, 26, 'Maria Haabazoka', 1, 'Loans', 'Create', '3', '2022-10-11 16:20:52', '2022-10-11 16:20:52'),
(52, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '3', '2022-10-11 16:22:01', '2022-10-11 16:22:01'),
(53, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '3', '2022-10-11 16:27:14', '2022-10-11 16:27:14'),
(54, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '3', '2022-10-11 16:28:37', '2022-10-11 16:28:37'),
(55, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '3', '2022-10-11 16:30:53', '2022-10-11 16:30:53'),
(56, 26, 'Maria Haabazoka', 1, 'Loans', 'Waive Interest', '3', '2022-10-11 16:34:21', '2022-10-11 16:34:21'),
(57, 26, 'Maria Haabazoka', 1, 'Loans', 'Waive Interest', '3', '2022-10-11 16:35:30', '2022-10-11 16:35:30'),
(58, 26, 'Maria Haabazoka', 1, 'Loans', 'Waive Interest', '3', '2022-10-11 16:36:26', '2022-10-11 16:36:26'),
(59, 26, 'Maria Haabazoka', 1, 'Loans', 'Waive Interest', '3', '2022-10-11 16:37:21', '2022-10-11 16:37:21'),
(60, 26, 'Maria Haabazoka', 1, 'Loans', 'Waive Interest', '3', '2022-10-11 16:42:23', '2022-10-11 16:42:23'),
(61, 26, 'Maria Haabazoka', 1, 'Clients', 'Create', '4', '2022-10-11 16:52:01', '2022-10-11 16:52:01'),
(62, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '123', '2022-10-11 16:53:13', '2022-10-11 16:53:13'),
(63, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '4', '2022-10-11 16:54:24', '2022-10-11 16:54:24'),
(64, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '4', '2022-10-11 16:54:46', '2022-10-11 16:54:46'),
(65, 26, 'Maria Haabazoka', 1, 'Clients', 'Approve', '4', '2022-10-11 16:55:26', '2022-10-11 16:55:26'),
(66, 26, 'Maria Haabazoka', 1, 'Loans', 'Create', '4', '2022-10-11 16:58:02', '2022-10-11 16:58:02'),
(67, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '4', '2022-10-11 16:58:58', '2022-10-11 16:58:58'),
(68, 26, 'Maria Haabazoka', 1, 'Loans', 'Unapprove', '4', '2022-10-11 19:17:02', '2022-10-11 19:17:02'),
(69, 26, 'Maria Haabazoka', 1, 'Loans', 'Update', '4', '2022-10-11 19:17:20', '2022-10-11 19:17:20'),
(70, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '4', '2022-10-11 19:20:46', '2022-10-11 19:20:46'),
(71, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '4', '2022-10-11 19:21:53', '2022-10-11 19:21:53'),
(72, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '4', '2022-10-11 19:27:33', '2022-10-11 19:27:33'),
(73, 26, 'Maria Haabazoka', 1, 'Loans', 'Waive Interest', '3', '2022-10-11 19:33:39', '2022-10-11 19:33:39'),
(74, 26, 'Maria Haabazoka', 1, 'Clients', 'Create', '5', '2022-10-11 19:45:43', '2022-10-11 19:45:43'),
(75, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '5', '2022-10-11 19:47:22', '2022-10-11 19:47:22'),
(76, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '5', '2022-10-11 19:48:41', '2022-10-11 19:48:41'),
(77, 26, 'Maria Haabazoka', 1, 'Clients', 'Approve', '5', '2022-10-11 19:50:28', '2022-10-11 19:50:28'),
(78, 26, 'Maria Haabazoka', 1, 'Clients', 'Approve', '5', '2022-10-11 19:50:29', '2022-10-11 19:50:29'),
(79, 26, 'Maria Haabazoka', 1, 'Loans', 'Create', '5', '2022-10-11 19:57:21', '2022-10-11 19:57:21'),
(80, 26, 'Maria Haabazoka', 1, 'Loans', 'Update', '5', '2022-10-11 20:01:24', '2022-10-11 20:01:24'),
(81, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '5', '2022-10-11 20:02:12', '2022-10-11 20:02:12'),
(82, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '5', '2022-10-11 20:03:13', '2022-10-11 20:03:13'),
(83, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '5', '2022-10-11 20:04:18', '2022-10-11 20:04:18'),
(84, 26, 'Maria Haabazoka', 1, 'Loans', 'Create', '6', '2022-10-11 20:08:15', '2022-10-11 20:08:15'),
(85, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '6', '2022-10-11 20:08:54', '2022-10-11 20:08:54'),
(86, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '6', '2022-10-11 20:09:53', '2022-10-11 20:09:53'),
(87, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '6', '2022-10-11 20:15:20', '2022-10-11 20:15:20'),
(88, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '6', '2022-10-11 20:16:47', '2022-10-11 20:16:47'),
(89, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '6', '2022-10-11 20:17:56', '2022-10-11 20:17:56'),
(90, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '6', '2022-10-11 20:19:01', '2022-10-11 20:19:01'),
(91, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '6', '2022-10-11 20:20:56', '2022-10-11 20:20:56'),
(92, 26, 'Maria Haabazoka', 1, 'Clients', 'Create', '6', '2022-10-13 14:24:48', '2022-10-13 14:24:48'),
(93, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '6', '2022-10-13 14:26:08', '2022-10-13 14:26:08'),
(94, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '6', '2022-10-13 14:26:09', '2022-10-13 14:26:09'),
(95, 26, 'Maria Haabazoka', 1, 'Clients', 'Approve', '6', '2022-10-13 14:28:55', '2022-10-13 14:28:55'),
(96, 26, 'Maria Haabazoka', 1, 'Clients', 'Approve', '6', '2022-10-13 14:28:55', '2022-10-13 14:28:55'),
(97, 26, 'Maria Haabazoka', 1, 'Loans', 'Create', '7', '2022-10-13 14:37:44', '2022-10-13 14:37:44'),
(98, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '7', '2022-10-13 14:39:27', '2022-10-13 14:39:27'),
(99, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '7', '2022-10-13 14:44:38', '2022-10-13 14:44:38'),
(100, 26, 'Maria Haabazoka', 1, 'Loans', 'Undo Disburse', '7', '2022-10-13 14:45:00', '2022-10-13 14:45:00'),
(101, 26, 'Maria Haabazoka', 1, 'Loans', 'Unapprove', '7', '2022-10-13 14:45:17', '2022-10-13 14:45:17'),
(102, 26, 'Maria Haabazoka', 1, 'Loans', 'Update', '7', '2022-10-13 14:45:43', '2022-10-13 14:45:43'),
(103, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '7', '2022-10-13 14:46:06', '2022-10-13 14:46:06'),
(104, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '7', '2022-10-13 14:46:31', '2022-10-13 14:46:31'),
(105, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '7', '2022-10-13 14:49:14', '2022-10-13 14:49:14'),
(106, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '6', '2022-10-13 14:51:35', '2022-10-13 14:51:35'),
(107, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '6', '2022-10-13 14:52:30', '2022-10-13 14:52:30'),
(108, 26, 'Maria Haabazoka', 1, 'Loans', 'Writeoff Loan', '7', '2022-10-13 14:54:20', '2022-10-13 14:54:20'),
(109, 26, 'Maria Haabazoka', 1, 'Loans', 'Create', '8', '2022-10-13 15:30:58', '2022-10-13 15:30:58'),
(110, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '8', '2022-10-13 15:31:37', '2022-10-13 15:31:37'),
(111, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '8', '2022-10-13 15:32:06', '2022-10-13 15:32:06'),
(112, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '8', '2022-10-13 15:33:51', '2022-10-13 15:33:51'),
(113, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '8', '2022-10-13 15:39:09', '2022-10-13 15:39:09'),
(114, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '8', '2022-10-13 15:39:47', '2022-10-13 15:39:47'),
(115, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '8', '2022-10-13 15:41:39', '2022-10-13 15:41:39'),
(116, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '8', '2022-10-13 15:43:06', '2022-10-13 15:43:06'),
(117, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '8', '2022-10-13 15:45:52', '2022-10-13 15:45:52'),
(118, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '8', '2022-10-13 15:46:52', '2022-10-13 15:46:52'),
(119, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Charge', '8', '2022-10-13 15:53:32', '2022-10-13 15:53:32'),
(120, 26, 'Maria Haabazoka', 1, 'Loans', 'Reverse Repayment', '49', '2022-10-13 15:55:37', '2022-10-13 15:55:37'),
(121, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '8', '2022-10-13 15:57:22', '2022-10-13 15:57:22'),
(122, 26, 'Maria Haabazoka', 1, 'Clients', 'Create', '7', '2022-10-13 16:52:39', '2022-10-13 16:52:39'),
(123, 26, 'Maria Haabazoka', 1, 'Clients', 'Approve', '7', '2022-10-13 16:53:58', '2022-10-13 16:53:58'),
(124, 26, 'Maria Haabazoka', 1, 'Clients', 'Approve', '7', '2022-10-13 16:53:59', '2022-10-13 16:53:59'),
(125, 26, 'Maria Haabazoka', 1, 'Clients', 'Closed', '6', '2022-10-13 19:07:54', '2022-10-13 19:07:54'),
(126, 26, 'Maria Haabazoka', 1, 'Loans', 'Create', '9', '2022-10-13 20:04:38', '2022-10-13 20:04:38'),
(127, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '9', '2022-10-13 20:05:08', '2022-10-13 20:05:08'),
(128, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '9', '2022-10-13 20:05:28', '2022-10-13 20:05:28'),
(129, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '9', '2022-10-13 20:06:37', '2022-10-13 20:06:37'),
(130, 26, 'Maria Haabazoka', 1, 'Loans', 'Create', '10', '2022-10-13 20:09:35', '2022-10-13 20:09:35'),
(131, 26, 'Maria Haabazoka', 1, 'Loans', 'Update', '10', '2022-10-13 20:10:02', '2022-10-13 20:10:02'),
(132, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '10', '2022-10-13 20:10:20', '2022-10-13 20:10:20'),
(133, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '10', '2022-10-13 20:11:22', '2022-10-13 20:11:22'),
(134, 26, 'Maria Haabazoka', 1, 'Loans', 'Writeoff Loan', '10', '2022-10-13 20:13:05', '2022-10-13 20:13:05'),
(135, 26, 'Maria Haabazoka', 1, 'Clients', 'Create', '8', '2022-10-14 14:38:16', '2022-10-14 14:38:16'),
(136, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '50', '2022-10-14 14:42:10', '2022-10-14 14:42:10'),
(137, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '8', '2022-10-14 14:45:58', '2022-10-14 14:45:58'),
(138, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '1', '2022-10-14 14:46:41', '2022-10-14 14:46:41'),
(139, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '8', '2022-10-14 14:48:13', '2022-10-14 14:48:13'),
(140, 26, 'Maria Haabazoka', 1, 'Clients', 'Approve', '8', '2022-10-14 14:51:24', '2022-10-14 14:51:24'),
(141, 26, 'Maria Haabazoka', 1, 'Clients', 'Approve', '8', '2022-10-14 14:51:25', '2022-10-14 14:51:25'),
(142, 26, 'Maria Haabazoka', 1, 'Loans', 'Create', '11', '2022-10-14 14:53:51', '2022-10-14 14:53:51'),
(143, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '11', '2022-10-14 14:55:13', '2022-10-14 14:55:13'),
(144, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '11', '2022-10-14 14:55:54', '2022-10-14 14:55:54'),
(145, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '11', '2022-10-14 14:57:56', '2022-10-14 14:57:56'),
(146, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '11', '2022-10-14 14:58:55', '2022-10-14 14:58:55'),
(147, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Collateral', '11', '2022-10-14 15:01:48', '2022-10-14 15:01:48'),
(148, 26, 'Maria Haabazoka', 1, 'Loans', 'Update Collateral', '1', '2022-10-14 15:03:38', '2022-10-14 15:03:38'),
(149, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '11', '2022-10-14 15:06:37', '2022-10-14 15:06:37'),
(150, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '11', '2022-10-14 15:08:30', '2022-10-14 15:08:30'),
(151, 26, 'Maria Haabazoka', 1, 'Loans', 'Waive Interest', '11', '2022-10-14 15:12:12', '2022-10-14 15:12:12'),
(152, 26, 'Maria Haabazoka', 1, 'Loans', 'Waive Interest', '11', '2022-10-14 15:12:44', '2022-10-14 15:12:44'),
(153, 26, 'Maria Haabazoka', 1, 'Loans', 'Reverse Repayment', '64', '2022-10-14 15:13:50', '2022-10-14 15:13:50'),
(154, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '11', '2022-10-14 15:14:44', '2022-10-14 15:14:44'),
(155, 26, 'Maria Haabazoka', 1, 'Loans', 'Waive Interest', '11', '2022-10-14 15:15:23', '2022-10-14 15:15:23'),
(156, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '11', '2022-10-14 15:16:07', '2022-10-14 15:16:07'),
(157, 26, 'Maria Haabazoka', 1, 'Loans', 'Waive Interest', '11', '2022-10-14 15:16:46', '2022-10-14 15:16:46'),
(158, 26, 'Maria Haabazoka', 1, 'Loans', 'Undo Disburse', '11', '2022-10-14 15:20:58', '2022-10-14 15:20:58'),
(159, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '11', '2022-10-14 15:21:52', '2022-10-14 15:21:52'),
(160, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '11', '2022-10-14 15:22:38', '2022-10-14 15:22:38'),
(161, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '11', '2022-10-14 15:23:24', '2022-10-14 15:23:24'),
(162, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '11', '2022-10-14 15:24:04', '2022-10-14 15:24:04'),
(163, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '11', '2022-10-14 15:24:46', '2022-10-14 15:24:46'),
(164, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '11', '2022-10-14 15:25:46', '2022-10-14 15:25:46'),
(165, 26, 'Maria Haabazoka', 1, 'Loans', 'Waive Interest', '11', '2022-10-14 15:27:40', '2022-10-14 15:27:40'),
(166, 26, 'Maria Haabazoka', 1, 'Loans', 'Update Repayment', '77', '2022-10-14 15:29:57', '2022-10-14 15:29:57'),
(167, 26, 'Maria Haabazoka', 1, 'Clients', 'Create', '9', '2022-10-14 15:50:29', '2022-10-14 15:50:29'),
(168, 26, 'Maria Haabazoka', 1, 'Clients', 'Approve', '9', '2022-10-14 15:51:19', '2022-10-14 15:51:19'),
(169, 26, 'Maria Haabazoka', 1, 'Loan Product', 'Create', '9', '2022-10-14 15:57:46', '2022-10-14 15:57:46'),
(170, 26, 'Maria Haabazoka', 1, 'Loans', 'Create', '12', '2022-10-14 16:00:23', '2022-10-14 16:00:23'),
(171, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Collateral', '12', '2022-10-14 16:01:38', '2022-10-14 16:01:38'),
(172, 26, 'Maria Haabazoka', 1, 'Loans', 'Update Collateral', '2', '2022-10-14 16:02:18', '2022-10-14 16:02:18'),
(173, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '12', '2022-10-14 16:03:50', '2022-10-14 16:03:50'),
(174, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '12', '2022-10-14 16:04:44', '2022-10-14 16:04:44'),
(175, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '12', '2022-10-14 16:05:34', '2022-10-14 16:05:34'),
(176, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '12', '2022-10-14 16:06:50', '2022-10-14 16:06:50'),
(177, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '12', '2022-10-14 16:07:25', '2022-10-14 16:07:25'),
(178, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '12', '2022-10-14 16:08:19', '2022-10-14 16:08:19'),
(179, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '12', '2022-10-14 16:09:01', '2022-10-14 16:09:01'),
(180, 26, 'Maria Haabazoka', 1, 'Clients', 'Create', '10', '2022-10-14 16:30:00', '2022-10-14 16:30:00'),
(181, 26, 'Maria Haabazoka', 1, 'Clients', 'Approve', '10', '2022-10-14 16:30:23', '2022-10-14 16:30:23'),
(182, 26, 'Maria Haabazoka', 1, 'Clients', 'Approve', '10', '2022-10-14 16:30:25', '2022-10-14 16:30:25'),
(183, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '10', '2022-10-14 16:31:48', '2022-10-14 16:31:48'),
(184, 26, 'Maria Haabazoka', 1, 'Loans', 'Create', '13', '2022-10-14 16:35:14', '2022-10-14 16:35:14'),
(185, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Collateral', '13', '2022-10-14 16:35:58', '2022-10-14 16:35:58'),
(186, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Guarantor', '13', '2022-10-14 16:37:13', '2022-10-14 16:37:13'),
(187, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '13', '2022-10-14 16:37:49', '2022-10-14 16:37:49'),
(188, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '13', '2022-10-14 16:38:16', '2022-10-14 16:38:16'),
(189, 26, 'Maria Haabazoka', 1, 'Loans', 'Undo Disburse', '13', '2022-10-14 16:38:54', '2022-10-14 16:38:54'),
(190, 26, 'Maria Haabazoka', 1, 'Loans', 'Unapprove', '13', '2022-10-14 16:39:03', '2022-10-14 16:39:03'),
(191, 26, 'Maria Haabazoka', 1, 'Loans', 'Update', '13', '2022-10-14 16:39:17', '2022-10-14 16:39:17'),
(192, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '13', '2022-10-14 16:39:32', '2022-10-14 16:39:32'),
(193, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '13', '2022-10-14 16:39:58', '2022-10-14 16:39:58'),
(194, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '13', '2022-10-14 16:40:55', '2022-10-14 16:40:55'),
(195, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '13', '2022-10-14 16:41:38', '2022-10-14 16:41:38'),
(196, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '13', '2022-10-14 16:42:05', '2022-10-14 16:42:05'),
(197, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '13', '2022-10-14 16:43:40', '2022-10-14 16:43:40'),
(198, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '13', '2022-10-14 16:44:14', '2022-10-14 16:44:14'),
(199, 26, 'Maria Haabazoka', 1, 'Loans', 'Create', '14', '2022-10-14 16:51:32', '2022-10-14 16:51:32'),
(200, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Collateral', '14', '2022-10-14 16:52:25', '2022-10-14 16:52:25'),
(201, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Guarantor', '14', '2022-10-14 16:54:02', '2022-10-14 16:54:02'),
(202, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '14', '2022-10-14 16:54:22', '2022-10-14 16:54:22'),
(203, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '14', '2022-10-14 16:54:41', '2022-10-14 16:54:41'),
(204, 26, 'Maria Haabazoka', 1, 'Loans', 'Undo Disburse', '14', '2022-10-14 16:55:10', '2022-10-14 16:55:10'),
(205, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '14', '2022-10-14 16:55:49', '2022-10-14 16:55:49'),
(206, 26, 'Maria Haabazoka', 1, 'Loans', 'Undo Disburse', '14', '2022-10-14 16:56:08', '2022-10-14 16:56:08'),
(207, 26, 'Maria Haabazoka', 1, 'Loans', 'Unapprove', '14', '2022-10-14 16:56:22', '2022-10-14 16:56:22'),
(208, 26, 'Maria Haabazoka', 1, 'Loans', 'Update', '14', '2022-10-14 16:56:36', '2022-10-14 16:56:36'),
(209, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '14', '2022-10-14 16:57:36', '2022-10-14 16:57:36'),
(210, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '14', '2022-10-14 16:57:50', '2022-10-14 16:57:50'),
(211, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-14 16:58:27', '2022-10-14 16:58:27'),
(212, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-14 16:59:01', '2022-10-14 16:59:01'),
(213, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-14 16:59:25', '2022-10-14 16:59:25'),
(214, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-14 16:59:53', '2022-10-14 16:59:53'),
(215, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-14 17:00:23', '2022-10-14 17:00:23'),
(216, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-14 17:00:48', '2022-10-14 17:00:48'),
(217, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-14 17:02:07', '2022-10-14 17:02:07'),
(218, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-14 17:02:42', '2022-10-14 17:02:42'),
(219, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-14 17:03:19', '2022-10-14 17:03:19'),
(220, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-14 17:04:56', '2022-10-14 17:04:56'),
(221, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-14 17:05:37', '2022-10-14 17:05:37'),
(222, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-14 17:06:17', '2022-10-14 17:06:17'),
(223, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-14 17:06:19', '2022-10-14 17:06:19'),
(224, 26, 'Maria Haabazoka', 1, 'Loans', 'Update Repayment', '113', '2022-10-14 19:51:56', '2022-10-14 19:51:56'),
(225, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-14 19:56:00', '2022-10-14 19:56:00'),
(226, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-14 19:56:41', '2022-10-14 19:56:41'),
(227, 26, 'Maria Haabazoka', 1, 'Loans', 'Update Repayment', '115', '2022-10-14 19:58:14', '2022-10-14 19:58:14'),
(228, 26, 'Maria Haabazoka', 1, 'Loans', 'Undo Disburse', '14', '2022-10-17 13:14:10', '2022-10-17 13:14:10'),
(229, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '14', '2022-10-17 13:14:36', '2022-10-17 13:14:36'),
(230, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-17 13:50:50', '2022-10-17 13:50:50'),
(231, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-17 13:51:25', '2022-10-17 13:51:25'),
(232, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-17 13:52:08', '2022-10-17 13:52:08'),
(233, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-17 13:52:37', '2022-10-17 13:52:37'),
(234, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-17 13:53:31', '2022-10-17 13:53:31'),
(235, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-17 13:54:00', '2022-10-17 13:54:00'),
(236, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-17 13:55:08', '2022-10-17 13:55:08'),
(237, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-17 13:55:40', '2022-10-17 13:55:40'),
(238, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-17 13:56:14', '2022-10-17 13:56:14'),
(239, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-17 13:57:10', '2022-10-17 13:57:10'),
(240, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-17 13:58:05', '2022-10-17 13:58:05'),
(241, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-17 13:58:44', '2022-10-17 13:58:44'),
(242, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-17 13:59:26', '2022-10-17 13:59:26'),
(243, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-17 13:59:54', '2022-10-17 13:59:54'),
(244, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '14', '2022-10-17 14:00:24', '2022-10-17 14:00:24'),
(245, 26, 'Maria Haabazoka', 1, 'Loans', 'Create', '15', '2022-10-17 14:56:26', '2022-10-17 14:56:26'),
(246, 26, 'Maria Haabazoka', 1, 'Loans', 'Decline', '15', '2022-10-17 14:57:33', '2022-10-17 14:57:33'),
(247, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '3', '2022-10-17 16:46:12', '2022-10-17 16:46:12'),
(248, 26, 'Maria Haabazoka', 1, 'Clients', 'Create', '11', '2022-10-17 19:23:41', '2022-10-17 19:23:41'),
(249, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '11', '2022-10-17 19:24:03', '2022-10-17 19:24:03'),
(250, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '52', '2022-10-17 19:24:18', '2022-10-17 19:24:18'),
(251, 26, 'Maria Haabazoka', 1, 'Clients', 'Update', '11', '2022-10-17 19:25:27', '2022-10-17 19:25:27'),
(252, 26, 'Maria Haabazoka', 1, 'Clients', 'Approve', '11', '2022-10-17 19:25:50', '2022-10-17 19:25:50'),
(253, 26, 'Maria Haabazoka', 1, 'Clients', 'Approve', '11', '2022-10-17 19:25:51', '2022-10-17 19:25:51'),
(254, 26, 'Maria Haabazoka', 1, 'Charges', 'Create', '3', '2022-10-19 13:56:16', '2022-10-19 13:56:16'),
(255, 26, 'Maria Haabazoka', 1, 'Loan Product', 'Update', '2', '2022-10-19 14:01:10', '2022-10-19 14:01:10'),
(256, 26, 'Maria Haabazoka', 1, 'Loan Product', 'Update', '2', '2022-10-19 14:09:38', '2022-10-19 14:09:38'),
(257, 26, 'Maria Haabazoka', 1, 'Charges', 'Create', '4', '2022-10-19 14:15:32', '2022-10-19 14:15:32'),
(258, 26, 'Maria Haabazoka', 1, 'Charges', 'Create', '5', '2022-10-19 14:16:29', '2022-10-19 14:16:29'),
(259, 26, 'Maria Haabazoka', 1, 'Charges', 'Create', '6', '2022-10-19 14:17:29', '2022-10-19 14:17:29'),
(260, 26, 'Maria Haabazoka', 1, 'Loan Product', 'Update', '2', '2022-10-19 14:20:12', '2022-10-19 14:20:12'),
(261, 26, 'Maria Haabazoka', 1, 'Loan Product', 'Update', '2', '2022-10-19 14:21:56', '2022-10-19 14:21:56'),
(262, 26, 'Maria Haabazoka', 1, 'Loans', 'Create', '16', '2022-10-19 14:24:40', '2022-10-19 14:24:40'),
(263, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '16', '2022-10-19 14:25:13', '2022-10-19 14:25:13'),
(264, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '16', '2022-10-19 14:25:53', '2022-10-19 14:25:53'),
(265, 26, 'Maria Haabazoka', 1, 'Loans', 'Undo Disburse', '16', '2022-10-19 14:26:34', '2022-10-19 14:26:34'),
(266, 26, 'Maria Haabazoka', 1, 'Loans', 'Unapprove', '16', '2022-10-19 14:26:56', '2022-10-19 14:26:56'),
(267, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '16', '2022-10-19 14:27:16', '2022-10-19 14:27:16'),
(268, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '16', '2022-10-19 14:27:42', '2022-10-19 14:27:42'),
(269, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Charge', '16', '2022-10-19 14:28:20', '2022-10-19 14:28:20'),
(270, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Charge', '16', '2022-10-19 14:28:22', '2022-10-19 14:28:22'),
(271, 26, 'Maria Haabazoka', 1, 'Loans', 'Undo Disburse', '16', '2022-10-19 14:34:16', '2022-10-19 14:34:16'),
(272, 26, 'Maria Haabazoka', 1, 'Loans', 'Unapprove', '16', '2022-10-19 14:41:29', '2022-10-19 14:41:29'),
(273, 26, 'Maria Haabazoka', 1, 'Loans', 'Update', '16', '2022-10-19 14:41:59', '2022-10-19 14:41:59'),
(274, 26, 'Maria Haabazoka', 1, 'Loans', 'Approve', '16', '2022-10-19 14:42:21', '2022-10-19 14:42:21'),
(275, 26, 'Maria Haabazoka', 1, 'Loans', 'Disburse', '16', '2022-10-19 14:42:50', '2022-10-19 14:42:50'),
(276, 26, 'Maria Haabazoka', 1, 'Branches', 'Update', '1', '2022-10-19 14:53:33', '2022-10-19 14:53:33'),
(277, 26, 'Maria Haabazoka', 1, 'Users', 'Update', '23', '2022-10-19 15:09:32', '2022-10-19 15:09:32'),
(278, 26, 'Maria Haabazoka', 1, 'Users', 'Update', '24', '2022-10-19 15:09:56', '2022-10-19 15:09:56'),
(279, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '16', '2022-10-19 16:11:41', '2022-10-19 16:11:41'),
(280, 26, 'Maria Haabazoka', 1, 'Loans', 'Create Repayment', '16', '2022-10-19 16:14:59', '2022-10-19 16:14:59');

-- --------------------------------------------------------

--
-- Table structure for table `charges`
--

CREATE TABLE `charges` (
  `id` int(10) UNSIGNED NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `product` enum('loan','savings','shares','client') COLLATE utf8mb4_unicode_ci NOT NULL,
  `charge_type` enum('disbursement','disbursement_repayment','specified_due_date','installment_fee','overdue_installment_fee','loan_rescheduling_fee','overdue_maturity','savings_activation','withdrawal_fee','annual_fee','monthly_fee','activation','shares_purchase','shares_redeem') COLLATE utf8mb4_unicode_ci NOT NULL,
  `charge_option` enum('flat','percentage','installment_principal_due','installment_principal_interest_due','installment_interest_due','installment_total_due','total_due','principal_due','interest_due','total_outstanding','original_principal') COLLATE utf8mb4_unicode_ci NOT NULL,
  `charge_frequency` tinyint(4) NOT NULL DEFAULT '0',
  `charge_frequency_type` enum('days','weeks','months','years') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'days',
  `charge_frequency_amount` int(11) NOT NULL DEFAULT '0',
  `amount` decimal(65,2) DEFAULT NULL,
  `minimum_amount` decimal(65,2) DEFAULT NULL,
  `maximum_amount` decimal(65,2) DEFAULT NULL,
  `charge_payment_mode` enum('regular','account_transfer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'regular',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `penalty` tinyint(4) NOT NULL DEFAULT '0',
  `override` tinyint(4) NOT NULL DEFAULT '0',
  `gl_account_income_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `charges`
--

INSERT INTO `charges` (`id`, `created_by_id`, `name`, `currency_id`, `product`, `charge_type`, `charge_option`, `charge_frequency`, `charge_frequency_type`, `charge_frequency_amount`, `amount`, `minimum_amount`, `maximum_amount`, `charge_payment_mode`, `active`, `penalty`, `override`, `gl_account_income_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'Penalty', 37, 'loan', 'overdue_installment_fee', 'installment_interest_due', 1, 'months', 10, '10.00', NULL, NULL, 'regular', 1, 0, 0, NULL, '2019-01-03 10:20:51', '2019-01-20 14:12:55'),
(2, 1, 'Penalty Interest', 37, 'loan', 'overdue_installment_fee', 'installment_principal_due', 0, 'days', 0, '15.00', NULL, NULL, 'regular', 1, 0, 0, NULL, '2019-01-29 14:47:05', '2019-01-29 14:47:05'),
(3, 26, 'PROCESSING FEE 1ST TIME BORROWER', 37, 'loan', 'disbursement', 'flat', 0, 'days', 0, '150.00', NULL, NULL, 'regular', 1, 0, 0, NULL, '2022-10-19 13:56:15', '2022-10-19 13:56:15'),
(4, 26, 'DDAC fee', 37, 'loan', 'disbursement', 'flat', 0, 'days', 0, '50.00', NULL, NULL, 'regular', 1, 0, 0, NULL, '2022-10-19 14:15:32', '2022-10-19 14:15:32'),
(5, 26, 'PROCESSING FEE RETURNING CLIENT', 37, 'loan', 'disbursement', 'flat', 0, 'days', 0, '100.00', NULL, NULL, 'regular', 1, 0, 0, NULL, '2022-10-19 14:16:29', '2022-10-19 14:16:29'),
(6, 26, 'PROCESSING FEE LOANS BELOW K1,000', 37, 'loan', 'disbursement', 'flat', 0, 'days', 0, '50.00', NULL, NULL, 'regular', 1, 0, 0, NULL, '2022-10-19 14:17:29', '2022-10-19 14:17:29');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) UNSIGNED NOT NULL,
  `country_id` int(11) DEFAULT NULL,
  `office_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `referred_by_id` int(11) DEFAULT NULL,
  `account_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `external_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `middle_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incorporation_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `picture` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('male','female','other','unspecified') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_type` enum('individual','business','ngo','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','active','inactive','declined','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `marital_status` enum('married','single','divorced','widowed','unspecified') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `street` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ward` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `joined_date` date DEFAULT NULL,
  `activated_date` date DEFAULT NULL,
  `reactivated_date` date DEFAULT NULL,
  `declined_date` date DEFAULT NULL,
  `declined_reason` text COLLATE utf8mb4_unicode_ci,
  `closed_reason` text COLLATE utf8mb4_unicode_ci,
  `closed_date` date DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `inactive_reason` text COLLATE utf8mb4_unicode_ci,
  `inactive_date` date DEFAULT NULL,
  `inactive_by_id` int(11) DEFAULT NULL,
  `activated_by_id` int(11) DEFAULT NULL,
  `reactivated_by_id` int(11) DEFAULT NULL,
  `declined_by_id` int(11) DEFAULT NULL,
  `closed_by_id` int(11) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `country_id`, `office_id`, `user_id`, `staff_id`, `referred_by_id`, `account_no`, `external_id`, `title`, `first_name`, `middle_name`, `last_name`, `full_name`, `incorporation_number`, `display_name`, `picture`, `mobile`, `phone`, `email`, `gender`, `client_type`, `status`, `marital_status`, `dob`, `street`, `ward`, `district`, `region`, `address`, `joined_date`, `activated_date`, `reactivated_date`, `declined_date`, `declined_reason`, `closed_reason`, `closed_date`, `created_by_id`, `inactive_reason`, `inactive_date`, `inactive_by_id`, `activated_by_id`, `reactivated_by_id`, `declined_by_id`, `closed_by_id`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 1, NULL, 22, NULL, 'MV-HQ-000-1', '255876/66/1', NULL, 'JOHN', NULL, 'MULOLANI', NULL, NULL, NULL, NULL, '966459975', '966459975', NULL, 'male', 'individual', 'active', 'married', '1974-04-12', 'plot N 57/2344, Mariandale, Lilay,', NULL, NULL, NULL, NULL, '1994-08-29', '2022-10-10', NULL, NULL, NULL, NULL, NULL, 26, NULL, NULL, NULL, 26, NULL, NULL, NULL, 'working place - LICS', '2022-10-10 19:44:06', '2022-10-11 14:50:56', NULL),
(2, NULL, 1, NULL, 22, NULL, 'MV-HQ-000-2', '255876/66/1', NULL, 'JOHN', NULL, 'MULOLANI', NULL, NULL, NULL, NULL, '966459975', '966459975', NULL, 'male', 'individual', 'pending', 'married', '1974-04-12', NULL, NULL, NULL, NULL, NULL, '1994-08-29', NULL, NULL, NULL, NULL, NULL, NULL, 26, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-10 19:44:10', '2022-10-11 01:03:09', '2022-10-11 01:03:09'),
(3, NULL, 1, NULL, 24, NULL, 'MV-HQ-000-3', '413706/74/1', NULL, 'BANDA', NULL, 'SIPHO', NULL, NULL, NULL, NULL, '0977812040', NULL, NULL, 'female', 'individual', 'active', 'single', '1994-03-13', NULL, NULL, NULL, NULL, 'FLAT 9, KULAKULEWA, CHONGWE', '2011-02-11', '0000-00-00', NULL, NULL, NULL, NULL, NULL, 26, NULL, NULL, NULL, 26, NULL, NULL, NULL, 'WORKING PLACE: CHALIMBANA UNIVERSITY', '2022-10-11 15:05:15', '2022-10-11 15:11:04', NULL),
(4, NULL, 1, NULL, 23, NULL, 'MV-HQ-000-4', '135220/10/1', NULL, 'CRUSIVIA', NULL, 'HICHIKUMBA', NULL, NULL, NULL, NULL, '0977597102', '0979920696', NULL, 'male', 'individual', 'active', 'married', '1990-11-04', NULL, NULL, NULL, NULL, '0937,  DR AGGREY ROAD, KABWATA , LUSAKA', '2007-07-06', '2022-08-22', NULL, NULL, NULL, NULL, NULL, 26, NULL, NULL, NULL, 26, NULL, NULL, NULL, 'WORKING PLACE: NAPSA HEAD OFFICE', '2022-10-11 16:52:01', '2022-10-11 16:55:26', NULL),
(5, NULL, 1, NULL, 23, NULL, 'MV-HQ-000-5', '758224/11/1', NULL, 'JUSTIN', NULL, 'TINDISA', NULL, NULL, NULL, NULL, '0979916929', '0979916929', NULL, 'male', 'individual', 'active', 'married', '1981-01-05', NULL, NULL, NULL, NULL, '11, ELIAS MUTALE STREET , PHI, LUSAKA', '1999-01-28', '2021-04-13', NULL, NULL, NULL, NULL, NULL, 26, NULL, NULL, NULL, 26, NULL, NULL, NULL, 'SELFEMPLOYED (RDA CONTRACTOR, FARMING)', '2022-10-11 19:45:43', '2022-10-11 19:50:29', NULL),
(6, NULL, 1, NULL, 22, NULL, 'MV-HQ-000-6', '199035/47/1', NULL, 'BRIDGET', NULL, 'NACHIZYA', NULL, NULL, NULL, NULL, '0978304148', NULL, NULL, 'female', 'individual', 'closed', 'married', '1976-05-24', NULL, NULL, NULL, NULL, '8, CHIBELO SCHOOL, LAKE ROAD, LUSAKA', '1995-12-27', '2020-10-22', NULL, NULL, NULL, 'Loan was written off, client is black listed.', '2021-12-30', 26, NULL, NULL, NULL, 26, NULL, NULL, 26, 'Working place: Chingwele Primary School', '2022-10-13 14:24:48', '2022-10-13 19:07:54', NULL),
(7, NULL, 1, NULL, 1, NULL, 'MV-HQ-000-7', '253360/68/1', NULL, 'NANCY', NULL, 'LUBEMBA', NULL, NULL, NULL, NULL, '0976631600', NULL, NULL, 'female', 'individual', 'active', 'married', '1985-02-07', NULL, NULL, NULL, NULL, '11731,GARDENIA ROAD, AVONDALE, LUSAKA', '2019-11-25', '2020-10-17', NULL, NULL, NULL, NULL, NULL, 26, NULL, NULL, NULL, 26, NULL, NULL, NULL, NULL, '2022-10-13 16:52:39', '2022-10-13 16:53:59', NULL),
(8, NULL, 1, NULL, 22, NULL, 'MV-HQ-000-8', '741755/11/1', NULL, 'NATASHA', NULL, 'MUSONDA', NULL, NULL, NULL, NULL, '0976975218', NULL, NULL, 'female', 'individual', 'active', 'married', '1981-10-23', NULL, NULL, NULL, NULL, 'PLOT 8, CHELSTONE, OFF GREAT EAST ROAD, LUSAKA', '1998-09-19', '0000-00-00', NULL, NULL, NULL, NULL, NULL, 26, NULL, NULL, NULL, 26, NULL, NULL, NULL, 'WORKING PLACE: STANBIC BANK. CUSTOMER CARE EXECUTIVE', '2022-10-14 14:38:16', '2022-10-14 14:51:25', NULL),
(9, NULL, 1, NULL, 22, NULL, 'MV-HQ-000-9', '878578/11/1', NULL, 'KENNEDY', 'NOWA KWETO', 'SIMBEYE', NULL, NULL, NULL, NULL, '0977774182', NULL, NULL, 'male', 'individual', 'active', 'married', '1971-09-26', NULL, NULL, NULL, NULL, 'PLOT 135/401/A MAKENI LUSAKA', '2001-10-23', '0000-00-00', NULL, NULL, NULL, NULL, NULL, 26, NULL, NULL, NULL, 26, NULL, NULL, NULL, 'WORKING PLACE: BUSINNESMAN, SHAREHOLDER AND DIRECTOR OF PATICHI-PATICHI. KALEBILIKA REAL ESTATE, WAY BRIDGES', '2022-10-14 15:50:29', '2022-10-14 15:51:19', NULL),
(10, NULL, 1, NULL, 24, NULL, 'MV-HQ-000-10', '182634/65/1', NULL, NULL, NULL, NULL, 'UPTOWN TRANSPORT', '320170002357', NULL, NULL, '0977810896', NULL, NULL, NULL, 'business', 'active', NULL, NULL, NULL, NULL, NULL, NULL, 'PLOT 175A/50A BONAVENTURE/MAKENI', '2017-02-14', '2021-02-13', NULL, NULL, NULL, NULL, NULL, 26, NULL, NULL, NULL, 26, NULL, NULL, NULL, 'TYPE OF BUSINESS: TRANSPORT COMAPANY, MINIBUSSES OWNER', '2022-10-14 16:30:00', '2022-10-14 16:30:25', NULL),
(11, NULL, 1, NULL, 23, NULL, 'MV-HQ-000-11', '329380/61/1', NULL, 'EVANS', NULL, 'NJOVU', NULL, NULL, NULL, NULL, '0955660761', '0975103471', NULL, 'male', 'individual', 'active', 'married', '1981-11-02', NULL, NULL, NULL, NULL, '14B, JULIA CHIKAMONEKA CRESCENT, PHI, LUSAKA', '1998-09-28', '2022-06-10', NULL, NULL, NULL, NULL, NULL, 26, NULL, NULL, NULL, 26, NULL, NULL, NULL, 'WORKING PLACE: UNZA, TECHNOLOGIST', '2022-10-17 19:23:41', '2022-10-17 19:25:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `client_identifications`
--

CREATE TABLE `client_identifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `client_identification_type_id` int(11) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `attachment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `client_identifications`
--

INSERT INTO `client_identifications` (`id`, `client_id`, `client_identification_type_id`, `name`, `active`, `notes`, `attachment`, `created_at`, `updated_at`) VALUES
(1, 3, 1, '413706/74/1', 1, NULL, NULL, '2022-10-17 16:46:12', '2022-10-17 16:46:12'),
(2, 11, 1, '329380/61/1', 1, NULL, NULL, '2022-10-17 19:24:03', '2022-10-17 19:24:03');

-- --------------------------------------------------------

--
-- Table structure for table `client_identification_types`
--

CREATE TABLE `client_identification_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `client_identification_types`
--

INSERT INTO `client_identification_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'NRC', NULL, NULL),
(2, 'PASSPORT', NULL, NULL),
(3, 'DRIVING LISENCE', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `client_next_of_kin`
--

CREATE TABLE `client_next_of_kin` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `client_relationship_id` int(11) DEFAULT NULL,
  `qualification` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `middle_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ward` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `picture` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('male','female','other','unspecified') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `client_next_of_kin`
--

INSERT INTO `client_next_of_kin` (`id`, `client_id`, `client_relationship_id`, `qualification`, `first_name`, `middle_name`, `last_name`, `ward`, `street`, `district`, `region`, `address`, `picture`, `mobile`, `phone`, `email`, `gender`, `notes`, `created_at`, `updated_at`) VALUES
(1, 86, 16, NULL, 'Betty', NULL, 'Shankwaya', NULL, NULL, NULL, NULL, 'Ngombe Compound', NULL, '0975730052', NULL, NULL, 'female', NULL, '2018-12-01 09:37:35', '2018-12-01 09:37:35'),
(2, 87, 17, NULL, 'Enoch', NULL, 'Musenga', NULL, NULL, NULL, NULL, 'Chawama  306/52', NULL, '0971793801', NULL, NULL, 'male', NULL, '2018-12-01 09:44:19', '2018-12-01 09:44:19'),
(3, 88, 15, NULL, 'Mulumbenji', NULL, 'Sinasilia', NULL, NULL, NULL, NULL, 'Katete', NULL, '0976300326', NULL, NULL, 'male', NULL, '2018-12-01 09:53:51', '2018-12-01 09:53:51'),
(4, 89, 16, NULL, 'Dora', NULL, 'Mumba', NULL, NULL, NULL, NULL, 'Mtendere H/No D314/9', NULL, '0971042088', NULL, NULL, 'female', NULL, '2018-12-01 10:08:04', '2018-12-01 10:08:04'),
(5, 90, 4, NULL, 'Maureen', NULL, 'Mpangalwendo', NULL, NULL, NULL, NULL, 'Luangwa', NULL, '0976262262', NULL, NULL, 'female', NULL, '2018-12-01 10:15:21', '2018-12-01 10:15:21'),
(6, 91, 6, NULL, 'Greygoh', NULL, 'Tembo', NULL, NULL, NULL, NULL, 'Kamwala', NULL, '0977911865', NULL, NULL, 'male', NULL, '2018-12-01 10:20:56', '2018-12-01 10:20:56'),
(7, 92, 16, NULL, 'Susan', NULL, 'Mwamba', NULL, NULL, NULL, NULL, 'John Laing H/No 230', NULL, '0977619030', NULL, NULL, 'female', NULL, '2018-12-01 10:25:04', '2018-12-01 10:25:04'),
(8, 93, 5, NULL, 'Steward', NULL, 'Chibinga', NULL, NULL, NULL, NULL, 'Chawama plot 307/78', NULL, '0960937772', NULL, NULL, 'male', NULL, '2018-12-01 10:29:22', '2018-12-01 10:29:56'),
(9, 94, 16, NULL, 'Osborn', NULL, 'Kangwa', NULL, NULL, NULL, NULL, 'Hse No 72/23 Chawama', NULL, '0976796952', NULL, NULL, 'female', NULL, '2018-12-01 10:35:48', '2018-12-01 10:36:02'),
(10, 95, 17, NULL, 'Brian', NULL, 'Siakabeya', NULL, NULL, NULL, NULL, 'Plot No 1012 New Kasama', NULL, '0977766096', NULL, NULL, 'male', NULL, '2018-12-01 10:41:33', '2018-12-01 10:41:33'),
(11, 96, 6, NULL, 'Trybee', NULL, 'Haliti', NULL, NULL, NULL, NULL, 'Zambezi RD 12040', NULL, '0968449712', NULL, NULL, 'male', NULL, '2018-12-01 10:45:51', '2018-12-01 10:45:51'),
(12, 97, 16, NULL, 'Beatrice', 'K', 'Zulu', NULL, NULL, NULL, NULL, '20/25 Ngombe', NULL, '0976841380', NULL, NULL, 'female', NULL, '2018-12-01 10:49:33', '2018-12-01 10:49:33'),
(13, 98, 17, NULL, 'James', NULL, 'Kamana', NULL, NULL, NULL, NULL, 'Farm No B3 Ngwerere', NULL, '0977873836', NULL, NULL, 'male', NULL, '2018-12-01 10:53:30', '2018-12-01 10:53:30'),
(14, 99, 16, NULL, 'Mirriam', NULL, 'Tembo', NULL, NULL, NULL, NULL, 'Plot 33180 Kabwata Estates', NULL, '0953076445', NULL, NULL, 'male', NULL, '2018-12-01 10:59:05', '2018-12-01 10:59:05'),
(15, 100, 16, NULL, 'Beauty', NULL, 'Zimba', NULL, NULL, NULL, NULL, 'C/o Lusaka Hotel', NULL, '0962241062', NULL, NULL, 'female', NULL, '2018-12-01 11:03:59', '2018-12-05 10:04:07'),
(16, 101, 2, NULL, 'Isabel', 'Mwango', 'Mwansa', NULL, NULL, NULL, NULL, 'Boma, Kalomo', NULL, '0979004551', NULL, NULL, 'female', NULL, '2018-12-01 11:18:08', '2018-12-01 11:18:08'),
(17, 103, 15, NULL, 'Peter', NULL, 'Mutanuka', NULL, NULL, NULL, NULL, '6 Mubuyu Complex Kabengele Road Kitwe', NULL, '0977619566', NULL, NULL, 'male', NULL, '2018-12-01 11:24:21', '2018-12-01 11:24:21'),
(18, 104, 3, NULL, 'Jacky', NULL, 'Ngoma', NULL, NULL, NULL, NULL, 'Kabanana', NULL, '0964356789', NULL, NULL, 'male', NULL, '2018-12-01 11:28:15', '2018-12-01 11:28:15'),
(19, 105, 16, NULL, 'Esther', NULL, 'Sibajene', NULL, NULL, NULL, NULL, 'Chalala Woodlands', NULL, '0975068668', NULL, NULL, 'female', NULL, '2018-12-01 11:33:25', '2018-12-01 11:33:25'),
(20, 106, 15, NULL, 'Chikondesa', NULL, 'Moyo', NULL, NULL, NULL, NULL, 'Mulamba Compound', NULL, '0975551337', NULL, NULL, 'male', NULL, '2018-12-01 11:37:47', '2018-12-01 11:37:47'),
(21, 107, 7, NULL, 'Prisca', NULL, 'Zyambo', NULL, NULL, NULL, NULL, 'Lusaka', NULL, '0977820667', NULL, NULL, 'male', NULL, '2018-12-03 10:43:40', '2018-12-03 10:43:40'),
(22, 108, 16, NULL, 'Precious', NULL, 'Phiri', NULL, NULL, NULL, NULL, NULL, NULL, '0977262611', NULL, NULL, 'male', NULL, '2018-12-03 11:04:14', '2018-12-03 11:04:14'),
(23, 70, 3, NULL, 'Willie', NULL, 'Masempela', NULL, NULL, NULL, NULL, 'Mupapa Rd Chilenje south', NULL, '0973345517', NULL, NULL, 'male', NULL, '2018-12-03 11:15:58', '2018-12-05 08:19:46'),
(24, 110, 3, NULL, 'derrick', NULL, 'Masempela', NULL, NULL, NULL, NULL, 'Mupapa Rd Chilenje south', NULL, NULL, NULL, NULL, 'male', NULL, '2018-12-03 11:20:57', '2018-12-03 11:20:57'),
(25, 111, 5, NULL, 'Darius', NULL, 'Masempela', NULL, NULL, NULL, NULL, 'Mupapa Rd Chilenje south', NULL, NULL, NULL, NULL, 'male', NULL, '2018-12-03 11:25:59', '2018-12-03 11:25:59'),
(26, 112, 15, NULL, 'Derrick', NULL, 'Sichimba', NULL, NULL, NULL, NULL, 'Town centre Lusaka', NULL, '+260238447', NULL, NULL, 'male', NULL, '2018-12-03 11:40:49', '2018-12-03 11:40:49'),
(27, 113, 15, NULL, 'Adams', 'Matamio', 'simbaya', NULL, NULL, NULL, NULL, 'Office Suite 6 Ew tarry Bildind Cairo rd', NULL, '0977763135', NULL, NULL, 'male', NULL, '2018-12-03 11:49:31', '2018-12-03 11:49:31'),
(28, 114, 17, NULL, 'Michael', 'Mwansa', 'Lyoba', NULL, NULL, NULL, NULL, 'Lusaka', NULL, '0978630166', NULL, NULL, 'male', NULL, '2018-12-03 11:57:48', '2018-12-03 11:57:48'),
(29, 115, 16, NULL, 'Sandra', 'Lwendi', 'Nketani', NULL, NULL, NULL, NULL, 'Munali Lusaka', NULL, '0979953850', NULL, NULL, 'male', NULL, '2018-12-03 12:06:35', '2018-12-03 12:06:35'),
(30, 116, 15, NULL, 'Salimu', NULL, 'Banda', NULL, NULL, NULL, NULL, 'Woodgate House Suite 601B 6Th Floor', NULL, '0955888550', NULL, NULL, 'male', NULL, '2018-12-03 12:20:48', '2018-12-03 12:20:48'),
(31, 117, 7, NULL, 'Angela', NULL, 'Phiri', NULL, NULL, NULL, NULL, 'Chilenje, Lusaka', NULL, '0977-722201', NULL, NULL, 'female', NULL, '2018-12-03 12:25:34', '2018-12-03 12:25:34'),
(32, 118, 2, NULL, 'Mileni', NULL, 'Kimetu', NULL, NULL, NULL, NULL, 'Kanyama Site And Service', NULL, '0973-611460', NULL, NULL, 'female', NULL, '2018-12-03 12:32:39', '2018-12-03 12:32:39'),
(33, 119, 15, NULL, 'Johabie', NULL, 'Mtonga', NULL, NULL, NULL, NULL, 'Plot 1482, Church Road, Chipata', NULL, '0979-477936', NULL, NULL, 'male', NULL, '2018-12-03 12:39:21', '2018-12-03 12:39:21'),
(34, 120, 6, NULL, 'Abson', NULL, 'Ng\'ombe', NULL, NULL, NULL, NULL, 'House no 16/38 Matero East', NULL, '0977-883373', NULL, NULL, 'male', NULL, '2018-12-03 12:50:39', '2018-12-03 12:50:39'),
(35, 121, 3, NULL, 'Benson', NULL, 'Mbewe', NULL, NULL, NULL, NULL, 'Plot No 52/13 John Laing', NULL, '0966-674938', NULL, NULL, 'male', NULL, '2018-12-03 12:57:39', '2018-12-03 12:57:39'),
(36, 122, 16, NULL, 'Mary', NULL, 'Siabulula', NULL, NULL, NULL, NULL, 'Makeni', NULL, '0978-700455', NULL, NULL, 'female', NULL, '2018-12-03 13:02:36', '2018-12-03 13:02:36'),
(37, 123, 16, NULL, 'Mazimba', NULL, 'Sinyangwe', NULL, NULL, NULL, NULL, 'Lusaka', NULL, '0972-546498', NULL, NULL, 'male', NULL, '2018-12-03 13:09:15', '2018-12-03 13:09:15'),
(38, 124, 17, NULL, 'Gardner', NULL, 'Mwape', NULL, NULL, NULL, NULL, 'katete Boarding Secondary School', NULL, '0977-495249', NULL, NULL, 'male', NULL, '2018-12-03 13:14:44', '2018-12-03 13:14:44'),
(39, 125, 15, NULL, 'Owen', NULL, 'Mungabo', NULL, NULL, NULL, NULL, 'Plot 25944 Woodlands Chalala', NULL, '0977781070', NULL, NULL, 'male', NULL, '2018-12-03 13:22:22', '2018-12-03 13:22:22'),
(40, 126, 4, NULL, 'Bernandette', 'Sepiso', 'Akatama', NULL, NULL, NULL, NULL, 'Plot 6665 Mberere Road, Olympia Extension', NULL, '0978-610385', NULL, NULL, 'female', NULL, '2018-12-03 13:30:08', '2018-12-03 13:30:08'),
(41, 127, 17, NULL, 'Daniel', NULL, 'Phiri', NULL, NULL, NULL, NULL, 'Silverest', NULL, '0965-717173', NULL, NULL, 'male', NULL, '2018-12-03 13:34:16', '2018-12-03 13:34:16'),
(42, 128, 16, NULL, 'Mary', NULL, 'Nakanyika', NULL, NULL, NULL, NULL, '1037 Garden Park', NULL, '0977-747123', NULL, NULL, 'female', NULL, '2018-12-03 13:41:43', '2018-12-03 13:41:43'),
(43, 129, 6, NULL, 'Pearson', NULL, 'Kachenga', NULL, NULL, NULL, NULL, 'Jesmondin', NULL, '0977-759429', NULL, NULL, 'male', NULL, '2018-12-03 13:47:34', '2018-12-03 13:47:34'),
(44, 130, 5, NULL, 'Cyrus', NULL, 'Undi', NULL, NULL, NULL, NULL, 'Plot 158/18 Chawama', NULL, '0977-667186', NULL, NULL, 'male', NULL, '2018-12-03 13:51:53', '2018-12-03 13:51:53'),
(45, 131, 16, NULL, 'Hellen', NULL, 'Bwalya', NULL, NULL, NULL, NULL, 'Kanyama', NULL, '0977619636', NULL, NULL, 'male', NULL, '2018-12-03 14:03:03', '2018-12-03 14:03:03'),
(46, 132, 3, NULL, 'Nacious', NULL, 'Mwitwa', NULL, NULL, NULL, NULL, 'Kaoma western', NULL, '0970156828', NULL, NULL, 'male', NULL, '2018-12-03 14:14:03', '2018-12-03 14:14:03'),
(47, 133, 3, NULL, 'Mulenga', NULL, 'Sikasote', NULL, NULL, NULL, NULL, 'flat 4 Block 6 Judicial Flats,Thornpark', NULL, '0964078301', NULL, NULL, 'male', NULL, '2018-12-03 14:21:22', '2018-12-03 14:21:22'),
(48, 134, 3, NULL, 'Mulenga', NULL, 'Sikasote', NULL, NULL, NULL, NULL, 'Thornpark Lusaka', NULL, '0964078301', NULL, NULL, 'male', NULL, '2018-12-03 14:29:23', '2018-12-03 14:29:23'),
(49, 7, 16, NULL, 'Priscilla', NULL, 'Mutale', NULL, NULL, NULL, NULL, 'Kuku Compound', NULL, '0974-432196', NULL, NULL, 'male', NULL, '2018-12-04 09:09:32', '2018-12-04 09:09:32'),
(51, 9, 16, NULL, 'Mirriam', NULL, 'kaayungwa', NULL, NULL, NULL, NULL, 'Libala Lusaka', NULL, NULL, NULL, NULL, 'male', NULL, '2018-12-04 09:32:28', '2018-12-04 09:32:28'),
(53, 12, 16, NULL, 'Rosemary', NULL, 'Zulu', NULL, NULL, NULL, NULL, '11a/A/24', NULL, '0964-007129', NULL, NULL, 'female', NULL, '2018-12-04 09:55:29', '2018-12-04 09:55:29'),
(54, 13, 16, NULL, 'alice', NULL, 'Sinonge', NULL, NULL, NULL, NULL, 'Johnlaig Lusaka', NULL, '0', NULL, NULL, 'male', NULL, '2018-12-04 10:03:53', '2018-12-04 10:03:53'),
(55, 14, 16, NULL, 'Emelda', NULL, 'Kadambe', NULL, NULL, NULL, NULL, 'Plot No 3375 Leopars Hill Rd', NULL, '0961-690836', NULL, NULL, 'male', NULL, '2018-12-04 10:15:09', '2018-12-04 10:15:09'),
(56, 15, 16, NULL, 'Priscilla', NULL, 'Chileshe', NULL, NULL, NULL, NULL, 'Lusaka', NULL, '0962-080952', NULL, NULL, 'male', NULL, '2018-12-04 10:25:49', '2018-12-04 10:25:49'),
(57, 16, 16, NULL, 'Changa', NULL, 'Maimbo', NULL, NULL, NULL, NULL, 'stand No. 33892 Mass Media', NULL, '0974-049034', NULL, NULL, 'female', NULL, '2018-12-04 10:38:52', '2018-12-04 10:38:52'),
(58, 17, 14, NULL, 'Joyce', NULL, 'Phiri', NULL, NULL, NULL, NULL, NULL, NULL, '0978-678817', NULL, NULL, 'female', NULL, '2018-12-04 10:45:01', '2018-12-04 10:45:01'),
(59, 18, 4, NULL, 'Mother', NULL, 'Mother', NULL, NULL, NULL, NULL, 'House No. 24/2 Garden House', NULL, '0965-547023', NULL, NULL, 'female', NULL, '2018-12-04 10:51:13', '2018-12-04 10:51:13'),
(60, 19, 16, NULL, 'Ngambo', NULL, 'Lukama', NULL, NULL, NULL, NULL, '1964/11 Kabanana', NULL, '0964-645735', NULL, NULL, 'female', NULL, '2018-12-04 10:58:32', '2018-12-04 10:58:32'),
(61, 20, 6, NULL, 'Anorld', NULL, 'Mwewa', NULL, NULL, NULL, NULL, 'House No. 10B/A Kalingalinga', NULL, '0971-622907', NULL, NULL, 'male', NULL, '2018-12-04 11:05:32', '2018-12-04 11:05:32'),
(62, 21, 15, NULL, 'China', 'State', 'Construction', NULL, NULL, NULL, NULL, '28393 Mass Media  Complex Lsk', NULL, NULL, NULL, NULL, 'male', NULL, '2018-12-04 11:15:26', '2018-12-04 11:15:26'),
(63, 22, 15, NULL, 'Lusaka', NULL, 'Hotel', NULL, NULL, NULL, NULL, 'Plot No 9/10 Cairo Road', NULL, NULL, NULL, NULL, 'male', NULL, '2018-12-04 11:26:28', '2018-12-04 11:26:28'),
(64, 23, 16, NULL, 'Mutale', NULL, 'Sikuka', NULL, NULL, NULL, NULL, 'House No 1114/48 Mukuni Street, Kabwata Street', NULL, '0977-445247', NULL, NULL, 'female', NULL, '2018-12-04 11:37:27', '2018-12-04 11:37:27'),
(65, 24, 16, NULL, 'Ethel', NULL, 'Chalwe', NULL, NULL, NULL, NULL, 'mtendere', NULL, '0973-855212', NULL, NULL, 'female', NULL, '2018-12-04 11:46:35', '2018-12-04 11:46:35'),
(66, 25, 6, NULL, 'Esau', NULL, 'Mwanza', NULL, NULL, NULL, NULL, 'Mtendere', NULL, '0975-301120', NULL, NULL, 'male', NULL, '2018-12-04 11:55:19', '2018-12-04 11:55:19'),
(67, 26, 4, NULL, 'Florence', NULL, 'Banda', NULL, NULL, NULL, NULL, 'Mkanda Clinic, Chipata', NULL, '0970-063944', NULL, NULL, 'female', NULL, '2018-12-04 12:09:18', '2018-12-04 12:09:18'),
(68, 27, 1, NULL, 'Richard', NULL, 'Phiri', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'male', NULL, '2018-12-04 12:15:34', '2018-12-04 12:15:34'),
(69, 28, 16, NULL, 'Peggy', NULL, 'Chibale', NULL, NULL, NULL, NULL, 'Kabwata Estates House No 1/11 Kalonga Street', NULL, '0971-138163/0961-541695', NULL, NULL, 'female', NULL, '2018-12-04 12:24:48', '2018-12-04 12:24:48'),
(70, 29, 16, NULL, 'Sara', NULL, 'Mbewe', NULL, NULL, NULL, NULL, 'House No 1220 Chendauke Road, Chelstone', NULL, '0963-259794', NULL, NULL, 'female', NULL, '2018-12-04 12:34:37', '2018-12-04 12:34:37'),
(71, 30, 5, NULL, 'Louis', NULL, 'Mutawale', NULL, NULL, NULL, NULL, 'Ang\'ombe', NULL, '0978-861383', NULL, NULL, 'male', NULL, '2018-12-04 12:41:34', '2018-12-04 12:41:34'),
(72, 31, 16, NULL, 'Lipezenji', NULL, 'Banda', NULL, NULL, NULL, NULL, 'Kalingalinga', NULL, '0968-102664', NULL, NULL, 'male', NULL, '2018-12-04 12:53:07', '2018-12-04 12:53:07'),
(73, 32, 16, NULL, 'Aginess', NULL, 'Chirwa', NULL, NULL, NULL, NULL, 'John Laign Compound', NULL, '0973-981856', NULL, NULL, 'female', NULL, '2018-12-04 12:58:56', '2018-12-04 12:58:56'),
(74, 33, 11, NULL, 'Joseph', NULL, 'Mulenga', NULL, NULL, NULL, NULL, 'Linda Compound', NULL, '0973-796071', NULL, NULL, 'male', NULL, '2018-12-04 13:04:35', '2018-12-04 13:04:35'),
(75, 34, 14, NULL, 'Anorld', NULL, 'Katai', NULL, NULL, NULL, NULL, NULL, NULL, '0961-965800', NULL, NULL, 'male', NULL, '2018-12-04 13:09:15', '2018-12-04 13:09:15'),
(76, 36, 6, NULL, 'Joseph', NULL, 'Banda', NULL, NULL, NULL, NULL, 'Livingstone', NULL, '0962-484080', NULL, NULL, 'male', NULL, '2018-12-04 13:15:24', '2018-12-04 13:15:24'),
(77, 35, 16, NULL, 'Evalyn', NULL, 'Bwalya', NULL, NULL, NULL, NULL, 'Kamanga Overspill', NULL, '0963-993346', NULL, NULL, 'male', NULL, '2018-12-04 13:20:23', '2018-12-04 13:20:23'),
(78, 37, 16, NULL, 'Anna', NULL, 'Banda', NULL, NULL, NULL, NULL, 'Mtendere East', NULL, '0964-275187', NULL, NULL, 'female', NULL, '2018-12-04 13:24:57', '2018-12-04 13:24:57'),
(79, 39, 16, NULL, 'Tasila', 'Soko', 'Siwale', NULL, NULL, NULL, NULL, 'John Laign Compound', NULL, '0963-536115', NULL, NULL, 'female', NULL, '2018-12-04 13:29:57', '2018-12-04 13:29:57'),
(80, 135, 2, NULL, 'Agness', NULL, 'Tembo', NULL, NULL, NULL, NULL, 'Kanyama', NULL, '0972-617414', NULL, NULL, 'female', NULL, '2018-12-04 13:40:53', '2018-12-04 13:41:45'),
(81, 40, 16, NULL, 'Veronica', 'Chirwa', 'Mwanza', NULL, NULL, NULL, NULL, 'Chelstone', NULL, '0977-204881', NULL, NULL, 'female', NULL, '2018-12-04 13:47:32', '2018-12-04 13:47:32'),
(82, 42, 16, NULL, 'Esnart', NULL, 'Sakala', NULL, NULL, NULL, NULL, 'Chiparamba', NULL, '0972-455928', NULL, NULL, 'female', NULL, '2018-12-04 13:51:20', '2018-12-04 13:51:20'),
(83, 43, 16, NULL, 'Mary', NULL, 'Zulu', NULL, NULL, NULL, NULL, 'Ngombe Compound', NULL, '0975-043877', NULL, NULL, 'female', NULL, '2018-12-04 13:58:10', '2018-12-04 13:58:10'),
(84, 44, 16, NULL, 'Jeania', NULL, 'Sefuke', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'female', NULL, '2018-12-04 14:02:16', '2018-12-04 14:02:16'),
(85, 45, 15, NULL, 'China State', NULL, 'Construction Company', NULL, NULL, NULL, NULL, 'Mass Media', NULL, NULL, NULL, NULL, 'male', NULL, '2018-12-04 14:07:43', '2018-12-04 14:07:43'),
(86, 46, 16, NULL, 'Jane', NULL, 'Chisenga', NULL, NULL, NULL, NULL, 'HNo. 89/04 Mtendere Copmound', NULL, '0967-734492', NULL, NULL, 'female', NULL, '2018-12-04 14:11:26', '2018-12-04 14:11:49'),
(87, 47, 16, NULL, 'Hellen', NULL, 'Sampa', NULL, NULL, NULL, NULL, 'C354/A Mtendere', NULL, '0972-385206', NULL, NULL, 'female', NULL, '2018-12-04 14:15:45', '2018-12-04 14:15:45'),
(88, 48, 14, NULL, 'Henry', NULL, 'Silwamba', NULL, NULL, NULL, NULL, NULL, NULL, '0978-500078', NULL, NULL, 'male', NULL, '2018-12-04 14:19:04', '2018-12-04 14:19:04'),
(89, 49, 17, NULL, 'David', NULL, 'Phiri', NULL, NULL, NULL, NULL, 'Chamiwawa Katete Eastern', NULL, '0966-053199', NULL, NULL, 'male', NULL, '2018-12-04 14:25:20', '2018-12-04 14:25:20'),
(90, 50, 14, NULL, 'Nawa', NULL, 'Njekwa', NULL, NULL, NULL, NULL, 'ZAF HQ Lusaka', NULL, '0977-654816', NULL, NULL, 'male', NULL, '2018-12-04 14:30:54', '2018-12-04 14:30:54'),
(91, 51, 6, NULL, 'Oshen', NULL, 'Banda', NULL, NULL, NULL, NULL, 'Hno. 182/4 Mandevu', NULL, '0979-688300', NULL, NULL, 'male', NULL, '2018-12-04 14:36:35', '2018-12-04 14:36:35'),
(92, 52, 16, NULL, 'Carol', 'M', 'Kalwa', NULL, NULL, NULL, NULL, '1963, Libala South, Lusaka', NULL, '0966-928831', NULL, NULL, 'female', NULL, '2018-12-05 06:37:37', '2018-12-05 06:37:37'),
(93, 53, 6, NULL, 'l', NULL, 'Kaluba', NULL, NULL, NULL, NULL, 'PO Box 38411, Lusaka', NULL, '0979-707565', NULL, NULL, 'male', NULL, '2018-12-05 06:43:44', '2018-12-05 06:43:44'),
(94, 54, 17, NULL, 'Hastings', NULL, 'Chabu', NULL, NULL, NULL, NULL, 'Zamwich Investments Company Limited', NULL, '0973-341249', NULL, NULL, 'male', NULL, '2018-12-05 06:49:57', '2018-12-05 06:49:57'),
(95, 55, 16, NULL, 'Mercy', NULL, 'Mushipe', NULL, NULL, NULL, NULL, '181 A/C Lusaka West', NULL, '0967-594546', NULL, NULL, 'female', NULL, '2018-12-05 06:54:46', '2018-12-05 06:54:46'),
(96, 56, 5, NULL, 'Arnold', NULL, 'Chichoni', NULL, NULL, NULL, NULL, '307/78 Chawama', NULL, '0979-786761', NULL, NULL, 'male', NULL, '2018-12-05 06:59:06', '2018-12-05 06:59:06'),
(97, 57, 6, NULL, 'Webster', NULL, 'Siyanga', NULL, NULL, NULL, NULL, 'M8 Chilonga Close, Chelstone', NULL, '0977-756288', NULL, NULL, 'male', NULL, '2018-12-05 07:04:52', '2018-12-05 07:04:52'),
(98, 58, 16, NULL, 'Ruth', NULL, 'Mugala', NULL, NULL, NULL, NULL, 'House no 5525 Kabanana', NULL, NULL, NULL, NULL, 'female', NULL, '2018-12-05 07:10:04', '2018-12-05 07:10:04'),
(99, 60, 10, NULL, 'Marydoro', NULL, 'Phiri', NULL, NULL, NULL, NULL, '04 ZNS Chongwe', NULL, '0971-694803', NULL, NULL, 'female', NULL, '2018-12-05 07:21:20', '2018-12-05 07:21:20'),
(100, 61, 15, NULL, 'Blandina', NULL, 'Zulu', NULL, NULL, NULL, NULL, 'Twibukishe, Kitwe Copperbelt', NULL, '0977-762176', NULL, NULL, 'female', NULL, '2018-12-05 07:27:15', '2018-12-05 07:27:15'),
(101, 62, 4, NULL, 'Anna', NULL, 'Banda', NULL, NULL, NULL, NULL, 'Po Box 350086', NULL, '0964-269770', NULL, NULL, 'female', NULL, '2018-12-05 07:32:54', '2018-12-05 07:32:54'),
(102, 63, 16, NULL, 'Stella', NULL, 'Phiri', NULL, NULL, NULL, NULL, 'Chawama Compound', NULL, '0960-347819', NULL, NULL, 'female', NULL, '2018-12-05 07:43:11', '2018-12-05 07:43:11'),
(103, 64, 7, NULL, 'Rose', NULL, 'Banda', NULL, NULL, NULL, NULL, 'House No 272/24 Chipata Compound', NULL, '0979-571441', NULL, NULL, 'female', NULL, '2018-12-05 07:49:19', '2018-12-05 07:49:19'),
(104, 65, 16, NULL, 'Margarate', NULL, 'Chisompe', NULL, NULL, NULL, NULL, 'House No D181 Mtendere Compound', NULL, '0977-898503', NULL, NULL, 'female', NULL, '2018-12-05 07:54:38', '2018-12-05 07:54:38'),
(105, 66, 4, NULL, 'Esnart', NULL, 'Sakala', NULL, NULL, NULL, NULL, 'F401A/16 Makeni Bonaventure', NULL, '0975-495231', NULL, NULL, 'female', NULL, '2018-12-05 08:00:54', '2018-12-05 08:00:54'),
(106, 67, 16, NULL, 'Memory', NULL, 'Mutema', NULL, NULL, NULL, NULL, 'B 905 Mtendere', NULL, '0962-326443', NULL, NULL, 'female', NULL, '2018-12-05 08:05:02', '2018-12-05 08:05:02'),
(107, 68, 1, NULL, 'Malita', NULL, 'Ngandu', NULL, NULL, NULL, NULL, NULL, NULL, '0211-222288', NULL, NULL, 'male', NULL, '2018-12-05 08:12:28', '2018-12-05 08:12:28'),
(108, 70, 17, NULL, 'Willie', 'Mwinga', 'Kabudula', NULL, NULL, NULL, NULL, 'Libala South Stage 3', NULL, '0979-851080', NULL, NULL, 'male', NULL, '2018-12-05 08:22:06', '2018-12-05 08:22:06'),
(109, 71, 16, NULL, 'Jane', NULL, 'Nzombola', NULL, NULL, NULL, NULL, 'Chambavalley Meanwood', NULL, '0977-315480', NULL, NULL, 'female', NULL, '2018-12-05 08:27:02', '2018-12-05 08:27:02'),
(110, 73, 16, NULL, 'Jane', NULL, 'Tembulo', NULL, NULL, NULL, NULL, 'Hno. 905 Mtendere East', NULL, '0977-330303', NULL, NULL, 'male', NULL, '2018-12-05 08:33:07', '2018-12-05 08:33:07'),
(111, 74, 4, NULL, 'Mwamba', NULL, 'Chileshe', NULL, NULL, NULL, NULL, 'Kabwata', NULL, '0969-305513', NULL, NULL, 'male', NULL, '2018-12-05 08:38:11', '2018-12-05 08:38:11'),
(112, 75, 16, NULL, 'Jacquline', 'Musanda', 'Chiluba', NULL, NULL, NULL, NULL, 'Makeni Villa', NULL, '226469', NULL, NULL, 'male', NULL, '2018-12-05 08:42:36', '2018-12-05 08:42:36'),
(113, 77, 6, NULL, 'Hagai', NULL, 'Mwenda', NULL, NULL, NULL, NULL, '0', NULL, '0968-059914', NULL, NULL, 'male', NULL, '2018-12-05 08:49:23', '2018-12-05 08:49:23'),
(114, 78, 6, NULL, 'Andrew', NULL, 'Mbozi', NULL, NULL, NULL, NULL, 'Kanyama Site & Service', NULL, '0974-302150', NULL, NULL, 'male', NULL, '2018-12-05 08:53:07', '2018-12-05 08:53:07'),
(115, 79, 16, NULL, 'Edith', NULL, 'Mulenga', NULL, NULL, NULL, NULL, 'Kalingalinga', NULL, '0970-368157', NULL, NULL, 'male', NULL, '2018-12-05 08:58:08', '2018-12-05 08:58:08'),
(116, 80, 1, NULL, 'Edeson', NULL, 'Sikatali', NULL, NULL, NULL, NULL, NULL, NULL, '0976-490525', NULL, NULL, 'male', NULL, '2018-12-05 09:05:23', '2018-12-05 09:05:23'),
(117, 81, 6, NULL, 'Rick', NULL, 'Mwiinga', NULL, NULL, NULL, NULL, 'Plot No 169/F Kasupe', NULL, '0975-033520', NULL, NULL, 'male', NULL, '2018-12-05 09:16:32', '2018-12-05 09:16:32'),
(118, 82, 16, NULL, 'Elezedi', NULL, 'Mwale', NULL, NULL, NULL, NULL, '45 Mugovu Sreet,New Kabwata', NULL, '0965-830893', NULL, NULL, 'male', NULL, '2018-12-05 09:26:22', '2018-12-05 09:26:22'),
(119, 83, 6, NULL, 'Chris', NULL, 'Kangwa', NULL, NULL, NULL, NULL, 'F401A Makeni Bonaventure', NULL, '0971-508053', NULL, NULL, 'male', NULL, '2018-12-05 09:36:01', '2018-12-05 09:36:01'),
(120, 84, 14, NULL, 'Rodges', NULL, 'Kangwa', NULL, NULL, NULL, NULL, 'F401A Makeni Bonaventure', NULL, '0976474472', NULL, NULL, 'male', NULL, '2018-12-05 09:43:46', '2018-12-05 09:43:46'),
(121, 2, 7, NULL, 'Bridget', NULL, 'Kaumba', NULL, NULL, NULL, NULL, '12360 Off Alick Nkhata road', NULL, '0973164333', NULL, NULL, 'female', NULL, '2018-12-05 09:54:31', '2018-12-05 09:54:31'),
(124, 5, 6, NULL, 'Amos', NULL, 'Zuze', NULL, NULL, NULL, NULL, NULL, NULL, '0965250105', NULL, NULL, 'male', NULL, '2018-12-05 10:00:41', '2018-12-05 10:00:41'),
(125, 136, 15, NULL, 'Premium medicals services', NULL, 'PMS', NULL, NULL, NULL, NULL, 'Kamwala', NULL, '0972-916290', NULL, NULL, 'female', NULL, '2018-12-14 13:15:41', '2018-12-14 13:15:41'),
(126, 137, 17, NULL, 'Patrick', NULL, 'Nyangu', NULL, NULL, NULL, NULL, 'Ministry Of chiefs and Traditional affairs, PO Box 570072, Nyimba', NULL, '0976-325724', NULL, NULL, 'male', NULL, '2018-12-15 08:55:29', '2018-12-15 08:55:29'),
(127, 138, 16, NULL, 'Bertha', NULL, 'Njovu', NULL, NULL, NULL, NULL, 'David Kaunda Compound', NULL, '0977-997802', NULL, NULL, 'female', NULL, '2018-12-15 09:47:08', '2018-12-15 09:47:08'),
(128, 139, 3, NULL, 'Situmbeko', NULL, 'Undi', NULL, NULL, NULL, NULL, 'Chawama', NULL, '0955-693770', NULL, NULL, 'male', NULL, '2018-12-15 10:04:56', '2018-12-15 10:04:56'),
(129, 141, 6, NULL, 'Eward', NULL, 'Malasha', NULL, NULL, NULL, NULL, 'Kamwala South off Chilumbulu RD', NULL, '0979310206', NULL, NULL, 'male', NULL, '2018-12-17 07:38:57', '2018-12-17 07:38:57'),
(130, 142, 6, NULL, 'Canan', NULL, 'Shipunga', NULL, NULL, NULL, NULL, 'John Laing House No 86/64 off Kafue RD', NULL, '0974049034', NULL, NULL, 'male', NULL, '2018-12-17 08:02:35', '2018-12-17 08:02:35'),
(131, 143, 6, NULL, 'Boyd', NULL, 'Sitwala', NULL, NULL, NULL, NULL, '25785 Habasunde Drive Chalala', NULL, '0977771931', NULL, NULL, 'male', NULL, '2018-12-17 08:21:23', '2018-12-17 08:21:23'),
(135, 148, 17, NULL, 'Joseph', NULL, 'Banda', NULL, NULL, NULL, NULL, 'Farm 46, Chamba Valley', NULL, '0955-55487', NULL, NULL, 'male', NULL, '2019-02-10 20:26:30', '2019-02-10 20:26:30'),
(136, 149, 9, NULL, 'Enock', NULL, 'Walobele', NULL, NULL, NULL, NULL, 'Buluwe Court F5, Woodlands Extensions, Off Buluwe Road', NULL, '0950-902177', NULL, NULL, 'male', NULL, '2019-02-10 22:20:06', '2019-02-10 22:20:06'),
(137, 151, 16, NULL, 'Precious', NULL, 'Phiri', NULL, NULL, NULL, NULL, 'Limson House FirstFloor Room 13, Freedom Way', NULL, '0962-286062', NULL, NULL, 'female', NULL, '2019-02-10 23:33:37', '2019-02-10 23:33:37'),
(138, 152, 6, NULL, 'Peter', NULL, 'Mutanuka', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'male', NULL, '2019-02-11 18:36:55', '2019-02-11 18:36:55'),
(139, 153, 17, NULL, 'Jethro', NULL, 'Keyala', NULL, NULL, NULL, NULL, '29 SEP Old Avondale', NULL, '0971-838977', NULL, NULL, 'male', NULL, '2019-02-11 18:58:53', '2019-02-11 18:58:53'),
(140, 154, 16, NULL, 'Rhonda', NULL, 'Chabala', NULL, NULL, NULL, NULL, 'Kabanana Site and Service 105/23', NULL, '0976-111247', NULL, NULL, 'female', NULL, '2019-02-11 19:16:20', '2019-02-11 19:16:20'),
(141, 156, 16, NULL, 'Nkole', 'Lizzy', 'Mumbulumina', NULL, NULL, NULL, NULL, 'House No 54/9 Lima township Lusaka Zambia', NULL, '0977-539720', NULL, NULL, 'female', NULL, '2019-02-11 22:41:39', '2019-02-11 22:41:39'),
(142, 160, 16, NULL, 'Bwalya', NULL, 'Musakanya', NULL, NULL, NULL, NULL, 'Block 2 Lilayi Garden Parkview, Lilayi', NULL, '0964-618610', NULL, NULL, 'female', NULL, '2019-02-13 19:03:43', '2019-02-13 19:03:43'),
(143, 161, 6, NULL, 'James', NULL, 'Tembo', NULL, NULL, NULL, NULL, '7708/05 Woodlands', NULL, '0977-649367', NULL, NULL, 'male', NULL, '2019-02-13 22:32:10', '2019-02-13 22:32:10'),
(144, 162, 17, NULL, 'Salimu', NULL, 'Banda', NULL, NULL, NULL, NULL, 'Plot L5858/m Off mumbwa Road, kalundu Lusaka west', NULL, '0966-628689', NULL, NULL, 'male', NULL, '2019-02-13 22:37:14', '2019-02-13 22:37:14'),
(145, 163, 4, NULL, 'Mable', NULL, 'Ngoma', NULL, NULL, NULL, NULL, 'Garden compound 05/01', NULL, '0979-410805', NULL, NULL, 'female', NULL, '2019-02-13 22:41:32', '2019-02-13 22:41:32'),
(146, 164, 17, NULL, 'Zakeyo', NULL, 'Mvula', NULL, NULL, NULL, NULL, NULL, NULL, '0979-702714', NULL, NULL, 'male', NULL, '2019-02-13 22:49:19', '2019-02-13 22:49:19'),
(147, 165, 6, NULL, 'Lawrence', NULL, 'Mazala', NULL, NULL, NULL, NULL, 'kalundu Lusaka West/Farm 37212', NULL, '0974-141634', NULL, NULL, 'male', NULL, '2019-02-13 22:55:11', '2019-02-13 22:55:11'),
(148, 166, 7, NULL, 'Chishala', NULL, 'Kapansa', NULL, NULL, NULL, NULL, '34521/1080', NULL, '0968-026755', NULL, NULL, 'female', NULL, '2019-02-13 23:00:45', '2019-02-13 23:00:45'),
(149, 167, 3, NULL, 'Menyani', NULL, 'Kachingwe', NULL, NULL, NULL, NULL, '37348 Lusaka', NULL, '0955-323295', NULL, NULL, 'male', NULL, '2019-02-13 23:08:00', '2019-02-13 23:08:00'),
(150, 168, 17, NULL, 'Mweene', NULL, 'Mwinga', NULL, NULL, NULL, NULL, 'Kamwala Main', NULL, '0977-572552', NULL, NULL, 'male', NULL, '2019-02-19 22:11:38', '2019-02-19 22:11:38'),
(151, 169, 17, NULL, 'Misheck', NULL, 'Shakemba', NULL, NULL, NULL, NULL, 'Zesco Head office', NULL, NULL, NULL, NULL, 'male', NULL, '2019-02-19 22:25:20', '2019-02-19 22:25:20'),
(152, 170, 16, NULL, 'Josephine', NULL, 'Chisanga', NULL, NULL, NULL, NULL, 'House No 10942/49 Kuomboka', NULL, NULL, NULL, NULL, 'female', NULL, '2019-02-19 22:42:07', '2019-02-19 22:42:07'),
(153, 171, 10, NULL, 'Chewe', NULL, 'Mwali', NULL, NULL, NULL, NULL, '518, Muzumbu Road Chelstone', NULL, '0975-464357', NULL, NULL, 'female', NULL, '2019-02-20 14:53:38', '2019-02-20 14:53:38'),
(154, 172, 15, NULL, 'Kasemuka', 'K', 'Kakoma', NULL, NULL, NULL, NULL, 'Plot No 34 Mass Media', NULL, NULL, NULL, NULL, 'male', NULL, '2019-02-21 22:41:10', '2019-02-21 22:41:10'),
(155, 173, 16, NULL, 'Doris', NULL, 'Mainda', NULL, NULL, NULL, NULL, 'Mtendere D442/F', NULL, NULL, NULL, NULL, 'female', NULL, '2019-02-26 00:04:54', '2019-02-26 00:04:54'),
(156, 174, 16, NULL, 'Natasha', NULL, 'Chomba', NULL, NULL, NULL, NULL, 'Plot 4 Ten Miles Off Kabwe Road', NULL, '538675/61/1', NULL, NULL, 'female', NULL, '2019-02-26 00:18:53', '2019-02-26 00:18:53'),
(157, 705, 9, NULL, 'Joe', NULL, 'Mulenga', NULL, NULL, NULL, NULL, 'Month Extension', NULL, '0979758246', NULL, NULL, 'male', NULL, '2019-03-14 23:09:49', '2019-03-14 23:09:49'),
(158, 707, 1, NULL, 'Aswell', NULL, 'Mulenga', NULL, NULL, NULL, NULL, 'Kapata ZRA Hse', NULL, '0977658909', NULL, NULL, 'male', NULL, '2019-03-14 23:51:26', '2019-03-14 23:51:26'),
(159, 708, 11, NULL, 'Mdoka', NULL, 'Tembo', NULL, NULL, NULL, NULL, 'Moth Hse No 4', NULL, '0977943910', NULL, NULL, 'male', NULL, '2019-03-16 18:47:32', '2019-03-16 18:47:32'),
(160, 144, 15, NULL, 'Enock', NULL, 'Musenge', NULL, NULL, NULL, NULL, 'Premium Medical Services', NULL, '0976-873117/0967-152469', NULL, NULL, 'male', NULL, '2019-04-11 23:45:22', '2019-04-11 23:45:22'),
(161, 145, 15, NULL, 'aaron', NULL, 'sinkala', NULL, NULL, NULL, NULL, '7660 off buluwe road, woodlands', NULL, '0977-972197', NULL, NULL, 'male', NULL, '2019-04-12 00:07:51', '2019-04-12 00:07:51'),
(162, 146, 16, NULL, 'Mercy', NULL, 'Banda', NULL, NULL, NULL, NULL, '305/66 chawama', NULL, NULL, NULL, NULL, 'female', NULL, '2019-04-12 00:19:33', '2019-04-12 00:19:33'),
(163, 147, 15, NULL, 'Muuka', NULL, 'Muleya', NULL, NULL, NULL, NULL, 'plot 3550 meanwood', NULL, '0979705490', NULL, NULL, 'female', NULL, '2019-04-12 00:27:27', '2019-04-12 00:27:27'),
(164, 268, 5, NULL, 'Ernest', 'Mtembo', 'Kashinga', NULL, NULL, NULL, NULL, 'A road, Mukuba Natwange', NULL, '0973500661', NULL, NULL, 'male', NULL, '2020-04-28 16:52:38', '2020-04-28 16:52:38'),
(165, 269, 1, NULL, 'Ernest', 'Mtembo', 'Kashinga', NULL, NULL, NULL, NULL, 'A road, Mukuba Natwange', NULL, '0973500661', NULL, NULL, 'male', NULL, '2020-04-28 16:57:43', '2020-04-28 16:57:43'),
(166, 270, 1, NULL, 'Audrey', NULL, 'Mulilo', NULL, NULL, NULL, NULL, 'Chimwemwe', NULL, '0764549626', NULL, NULL, 'male', NULL, '2020-04-28 17:15:28', '2020-04-28 17:15:28'),
(167, 271, 1, NULL, 'Hellen', NULL, 'Mukosa', NULL, NULL, NULL, NULL, '80 Kwacha Strret, Town Center, Chingola', NULL, '0977 514600', NULL, NULL, 'male', NULL, '2020-04-28 19:23:51', '2020-04-28 19:23:51'),
(168, 272, 1, NULL, 'Dorothy', NULL, 'Daka', NULL, NULL, NULL, NULL, 'Hse #. 1357, New Ndeke, Kitwe', NULL, '0976976498', NULL, NULL, 'female', NULL, '2020-04-28 19:46:02', '2020-04-28 19:46:02'),
(169, 274, 1, NULL, 'Masauso', NULL, 'Mulima', NULL, NULL, NULL, NULL, 'Hse #. Zambia Catholic Universty Complex, Lusaka', NULL, '0955512411', NULL, NULL, 'male', NULL, '2020-04-28 20:16:55', '2020-04-28 20:16:55'),
(170, 275, 1, NULL, 'Vivian', NULL, 'Tonga', NULL, NULL, NULL, NULL, 'Hse #. 14,  Mpandala RD, Kalulushi', NULL, '0977304938', NULL, NULL, 'female', NULL, '2020-04-28 20:47:07', '2020-04-28 20:47:07'),
(171, 278, 1, NULL, 'Elizabeth', NULL, 'Namonga', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'male', NULL, '2020-05-07 19:21:02', '2020-05-07 19:21:02'),
(172, 1, 16, NULL, 'HARRIET', 'MUTINTA', 'Matongo', NULL, NULL, NULL, NULL, 'PLOT 57/2344, MARILANDALE, LILAY', NULL, '967033570', NULL, NULL, 'female', NULL, '2022-10-11 01:30:22', '2022-10-11 01:30:22'),
(173, 3, 1, NULL, 'ANGELA', NULL, 'MUSHABATI', NULL, NULL, NULL, NULL, 'LUSAKA', NULL, '0777812030', NULL, NULL, 'female', NULL, '2022-10-11 15:06:54', '2022-10-11 15:06:54'),
(174, 4, 16, NULL, 'KANKOMBA', NULL, 'MALUPENGA', NULL, NULL, NULL, NULL, '0937, DR AGGREY ROAD, KABWATA, LUSAKA', NULL, '0979920696', NULL, NULL, 'female', NULL, '2022-10-11 16:54:24', '2022-10-11 16:54:24'),
(175, 6, 15, NULL, 'DANIEL', NULL, 'YOKONDE', NULL, NULL, NULL, NULL, NULL, NULL, '0978525657', NULL, NULL, 'male', NULL, '2022-10-13 14:51:35', '2022-10-13 14:51:35'),
(176, 6, 15, NULL, 'Roselia', NULL, 'Unknown', NULL, NULL, NULL, NULL, NULL, NULL, '0977848192', NULL, NULL, 'male', 'Head teacher', '2022-10-13 14:52:30', '2022-10-13 14:52:30'),
(177, 8, 17, NULL, 'NOAH', NULL, 'CHIDYAKA', NULL, NULL, NULL, NULL, '8, OFF GREAT EAST ROAD, CHELSTON, LUSAKA', NULL, '0974057284', NULL, NULL, 'male', NULL, '2022-10-14 14:48:13', '2022-10-14 14:48:13'),
(178, 10, 16, NULL, 'CHABALA', 'NACHELA', 'YAMBA', NULL, NULL, NULL, NULL, 'PLOT 175A/50A BONAVENTURE/MAKENI', NULL, NULL, NULL, NULL, 'male', NULL, '2022-10-14 16:31:48', '2022-10-14 16:31:48'),
(179, 11, 16, NULL, 'THELMA', NULL, 'KIPUPUTWA', NULL, NULL, NULL, NULL, '14B, JULIA CHIKAMONEKA CRESENT, PHI', NULL, '0969400895', NULL, NULL, 'male', NULL, '2022-10-17 19:25:27', '2022-10-17 19:25:27');

-- --------------------------------------------------------

--
-- Table structure for table `client_profession`
--

CREATE TABLE `client_profession` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client_relationships`
--

CREATE TABLE `client_relationships` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `client_relationships`
--

INSERT INTO `client_relationships` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Friend', NULL, NULL),
(2, 'Daughter', NULL, NULL),
(3, 'Son', NULL, NULL),
(4, 'Mother', NULL, NULL),
(5, 'Father', NULL, NULL),
(6, 'Brother', NULL, NULL),
(7, 'Sister', NULL, NULL),
(8, 'Aunty', NULL, NULL),
(9, 'Uncle', NULL, NULL),
(10, 'Niece', NULL, NULL),
(11, 'Nephew', NULL, NULL),
(12, 'Grandmother', NULL, NULL),
(13, 'Grand Father', NULL, NULL),
(14, 'Cousin', NULL, NULL),
(15, 'Others', NULL, NULL),
(16, 'Wife', NULL, NULL),
(17, 'Husband', NULL, NULL),
(18, 'UNSPECIFIED', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `client_users`
--

CREATE TABLE `client_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `collateral`
--

CREATE TABLE `collateral` (
  `id` int(10) UNSIGNED NOT NULL,
  `loan_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `collateral_type_id` int(11) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` decimal(65,4) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `picture` text COLLATE utf8mb4_unicode_ci,
  `gallery` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `collateral`
--

INSERT INTO `collateral` (`id`, `loan_id`, `client_id`, `collateral_type_id`, `name`, `serial`, `value`, `description`, `picture`, `gallery`, `created_at`, `updated_at`) VALUES
(1, 11, NULL, 1, NULL, 'BLA1059ZM, MODEL: MERCEDES- BENZ, CLASS C, COLOUR: WHITE, YEAR OF MAKE: 2010, CHASSIS NUMBER: WDD2040482A367355', '100000.0000', 'loans tied to the net and salary day', NULL, NULL, '2022-10-14 15:01:48', '2022-10-14 15:03:38'),
(2, 12, NULL, 2, NULL, 'LANDED PROPERTY LOCATED AT PLOT No. 27736, LOT No. 1263/M, CHALALA AREA BELONGING TO MR, KENNEDY SIMBEYE.', '100000.0000', 'INTEREST FIRST', NULL, NULL, '2022-10-14 16:01:38', '2022-10-14 16:02:18'),
(3, 13, NULL, 1, NULL, 'VEHICLE: Toyota HAICE. Reg No. ALC4376. Color: multicolor. Original white book deposited with BML', '20000.0000', 'Flat Rate', NULL, NULL, '2022-10-14 16:35:58', '2022-10-14 16:35:58'),
(4, 14, NULL, 1, NULL, 'VEHICLE: Toyota HAICE. Reg No. ALC4376. Color: multicolor. Original white book deposited with BML', '60000.0000', 'Flat Rate', NULL, NULL, '2022-10-14 16:52:25', '2022-10-14 16:52:25');

-- --------------------------------------------------------

--
-- Table structure for table `collateral_types`
--

CREATE TABLE `collateral_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `collateral_types`
--

INSERT INTO `collateral_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Motor Vehicle', '2020-05-27 23:28:56', '2020-05-27 23:28:56'),
(2, 'Property', '2020-05-27 23:39:03', '2020-05-27 23:39:03'),
(3, 'LAND', '2022-10-17 19:34:18', '2022-10-17 19:34:18'),
(4, 'Household items', '2022-10-17 19:34:43', '2022-10-17 19:34:43'),
(5, 'company fixed assets', '2022-10-17 19:35:03', '2022-10-17 19:35:03');

-- --------------------------------------------------------

--
-- Table structure for table `communication_campaigns`
--

CREATE TABLE `communication_campaigns` (
  `id` int(10) UNSIGNED NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `type` enum('sms','email') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `report_start_date` date DEFAULT NULL,
  `report_start_time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recurrence_type` enum('none','schedule') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recur_frequency` enum('days','months','weeks','years') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recur_interval` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_recipients` text COLLATE utf8mb4_unicode_ci,
  `email_subject` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `email_attachment_file_format` enum('pdf','csv','xls') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recipients_category` enum('all_clients','active_clients','prospective_clients','active_loans','loans_in_arrears','overdue_loans','happy_birthday') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `report_attachment` enum('loan_schedule','loan_statement','savings_statement','audit_report','group_indicator_report') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_day` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_day` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `office_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loan_officer_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gl_account_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manual_entries` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loan_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loan_product_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_run_date` date DEFAULT NULL,
  `next_run_date` date DEFAULT NULL,
  `last_run_time` date DEFAULT NULL,
  `next_run_time` date DEFAULT NULL,
  `number_of_runs` int(11) NOT NULL DEFAULT '0',
  `number_of_recipients` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `sent` tinyint(4) NOT NULL DEFAULT '0',
  `status` enum('pending','active','declined','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(10) UNSIGNED NOT NULL,
  `sortname` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `sortname`, `name`) VALUES
(1, 'AF', 'Afghanistan'),
(2, 'AL', 'Albania'),
(3, 'DZ', 'Algeria'),
(4, 'AS', 'American Samoa'),
(5, 'AD', 'Andorra'),
(6, 'AO', 'Angola'),
(7, 'AI', 'Anguilla'),
(8, 'AQ', 'Antarctica'),
(9, 'AG', 'Antigua And Barbuda'),
(10, 'AR', 'Argentina'),
(11, 'AM', 'Armenia'),
(12, 'AW', 'Aruba'),
(13, 'AU', 'Australia'),
(14, 'AT', 'Austria'),
(15, 'AZ', 'Azerbaijan'),
(16, 'BS', 'Bahamas The'),
(17, 'BH', 'Bahrain'),
(18, 'BD', 'Bangladesh'),
(19, 'BB', 'Barbados'),
(20, 'BY', 'Belarus'),
(21, 'BE', 'Belgium'),
(22, 'BZ', 'Belize'),
(23, 'BJ', 'Benin'),
(24, 'BM', 'Bermuda'),
(25, 'BT', 'Bhutan'),
(26, 'BO', 'Bolivia'),
(27, 'BA', 'Bosnia and Herzegovina'),
(28, 'BW', 'Botswana'),
(29, 'BV', 'Bouvet Island'),
(30, 'BR', 'Brazil'),
(31, 'IO', 'British Indian Ocean Territory'),
(32, 'BN', 'Brunei'),
(33, 'BG', 'Bulgaria'),
(34, 'BF', 'Burkina Faso'),
(35, 'BI', 'Burundi'),
(36, 'KH', 'Cambodia'),
(37, 'CM', 'Cameroon'),
(38, 'CA', 'Canada'),
(39, 'CV', 'Cape Verde'),
(40, 'KY', 'Cayman Islands'),
(41, 'CF', 'Central African Republic'),
(42, 'TD', 'Chad'),
(43, 'CL', 'Chile'),
(44, 'CN', 'China'),
(45, 'CX', 'Christmas Island'),
(46, 'CC', 'Cocos (Keeling) Islands'),
(47, 'CO', 'Colombia'),
(48, 'KM', 'Comoros'),
(49, 'CG', 'Congo'),
(50, 'CD', 'Congo The Democratic Republic Of The'),
(51, 'CK', 'Cook Islands'),
(52, 'CR', 'Costa Rica'),
(53, 'CI', 'Cote D\'Ivoire (Ivory Coast)'),
(54, 'HR', 'Croatia (Hrvatska)'),
(55, 'CU', 'Cuba'),
(56, 'CY', 'Cyprus'),
(57, 'CZ', 'Czech Republic'),
(58, 'DK', 'Denmark'),
(59, 'DJ', 'Djibouti'),
(60, 'DM', 'Dominica'),
(61, 'DO', 'Dominican Republic'),
(62, 'TP', 'East Timor'),
(63, 'EC', 'Ecuador'),
(64, 'EG', 'Egypt'),
(65, 'SV', 'El Salvador'),
(66, 'GQ', 'Equatorial Guinea'),
(67, 'ER', 'Eritrea'),
(68, 'EE', 'Estonia'),
(69, 'ET', 'Ethiopia'),
(70, 'XA', 'External Territories of Australia'),
(71, 'FK', 'Falkland Islands'),
(72, 'FO', 'Faroe Islands'),
(73, 'FJ', 'Fiji Islands'),
(74, 'FI', 'Finland'),
(75, 'FR', 'France'),
(76, 'GF', 'French Guiana'),
(77, 'PF', 'French Polynesia'),
(78, 'TF', 'French Southern Territories'),
(79, 'GA', 'Gabon'),
(80, 'GM', 'Gambia The'),
(81, 'GE', 'Georgia'),
(82, 'DE', 'Germany'),
(83, 'GH', 'Ghana'),
(84, 'GI', 'Gibraltar'),
(85, 'GR', 'Greece'),
(86, 'GL', 'Greenland'),
(87, 'GD', 'Grenada'),
(88, 'GP', 'Guadeloupe'),
(89, 'GU', 'Guam'),
(90, 'GT', 'Guatemala'),
(91, 'XU', 'Guernsey and Alderney'),
(92, 'GN', 'Guinea'),
(93, 'GW', 'Guinea-Bissau'),
(94, 'GY', 'Guyana'),
(95, 'HT', 'Haiti'),
(96, 'HM', 'Heard and McDonald Islands'),
(97, 'HN', 'Honduras'),
(98, 'HK', 'Hong Kong S.A.R.'),
(99, 'HU', 'Hungary'),
(100, 'IS', 'Iceland'),
(101, 'IN', 'India'),
(102, 'ID', 'Indonesia'),
(103, 'IR', 'Iran'),
(104, 'IQ', 'Iraq'),
(105, 'IE', 'Ireland'),
(106, 'IL', 'Israel'),
(107, 'IT', 'Italy'),
(108, 'JM', 'Jamaica'),
(109, 'JP', 'Japan'),
(110, 'XJ', 'Jersey'),
(111, 'JO', 'Jordan'),
(112, 'KZ', 'Kazakhstan'),
(113, 'KE', 'Kenya'),
(114, 'KI', 'Kiribati'),
(115, 'KP', 'Korea North'),
(116, 'KR', 'Korea South'),
(117, 'KW', 'Kuwait'),
(118, 'KG', 'Kyrgyzstan'),
(119, 'LA', 'Laos'),
(120, 'LV', 'Latvia'),
(121, 'LB', 'Lebanon'),
(122, 'LS', 'Lesotho'),
(123, 'LR', 'Liberia'),
(124, 'LY', 'Libya'),
(125, 'LI', 'Liechtenstein'),
(126, 'LT', 'Lithuania'),
(127, 'LU', 'Luxembourg'),
(128, 'MO', 'Macau S.A.R.'),
(129, 'MK', 'Macedonia'),
(130, 'MG', 'Madagascar'),
(131, 'MW', 'Malawi'),
(132, 'MY', 'Malaysia'),
(133, 'MV', 'Maldives'),
(134, 'ML', 'Mali'),
(135, 'MT', 'Malta'),
(136, 'XM', 'Man (Isle of)'),
(137, 'MH', 'Marshall Islands'),
(138, 'MQ', 'Martinique'),
(139, 'MR', 'Mauritania'),
(140, 'MU', 'Mauritius'),
(141, 'YT', 'Mayotte'),
(142, 'MX', 'Mexico'),
(143, 'FM', 'Micronesia'),
(144, 'MD', 'Moldova'),
(145, 'MC', 'Monaco'),
(146, 'MN', 'Mongolia'),
(147, 'MS', 'Montserrat'),
(148, 'MA', 'Morocco'),
(149, 'MZ', 'Mozambique'),
(150, 'MM', 'Myanmar'),
(151, 'NA', 'Namibia'),
(152, 'NR', 'Nauru'),
(153, 'NP', 'Nepal'),
(154, 'AN', 'Netherlands Antilles'),
(155, 'NL', 'Netherlands The'),
(156, 'NC', 'New Caledonia'),
(157, 'NZ', 'New Zealand'),
(158, 'NI', 'Nicaragua'),
(159, 'NE', 'Niger'),
(160, 'NG', 'Nigeria'),
(161, 'NU', 'Niue'),
(162, 'NF', 'Norfolk Island'),
(163, 'MP', 'Northern Mariana Islands'),
(164, 'NO', 'Norway'),
(165, 'OM', 'Oman'),
(166, 'PK', 'Pakistan'),
(167, 'PW', 'Palau'),
(168, 'PS', 'Palestinian Territory Occupied'),
(169, 'PA', 'Panama'),
(170, 'PG', 'Papua new Guinea'),
(171, 'PY', 'Paraguay'),
(172, 'PE', 'Peru'),
(173, 'PH', 'Philippines'),
(174, 'PN', 'Pitcairn Island'),
(175, 'PL', 'Poland'),
(176, 'PT', 'Portugal'),
(177, 'PR', 'Puerto Rico'),
(178, 'QA', 'Qatar'),
(179, 'RE', 'Reunion'),
(180, 'RO', 'Romania'),
(181, 'RU', 'Russia'),
(182, 'RW', 'Rwanda'),
(183, 'SH', 'Saint Helena'),
(184, 'KN', 'Saint Kitts And Nevis'),
(185, 'LC', 'Saint Lucia'),
(186, 'PM', 'Saint Pierre and Miquelon'),
(187, 'VC', 'Saint Vincent And The Grenadines'),
(188, 'WS', 'Samoa'),
(189, 'SM', 'San Marino'),
(190, 'ST', 'Sao Tome and Principe'),
(191, 'SA', 'Saudi Arabia'),
(192, 'SN', 'Senegal'),
(193, 'RS', 'Serbia'),
(194, 'SC', 'Seychelles'),
(195, 'SL', 'Sierra Leone'),
(196, 'SG', 'Singapore'),
(197, 'SK', 'Slovakia'),
(198, 'SI', 'Slovenia'),
(199, 'XG', 'Smaller Territories of the UK'),
(200, 'SB', 'Solomon Islands'),
(201, 'SO', 'Somalia'),
(202, 'ZA', 'South Africa'),
(203, 'GS', 'South Georgia'),
(204, 'SS', 'South Sudan'),
(205, 'ES', 'Spain'),
(206, 'LK', 'Sri Lanka'),
(207, 'SD', 'Sudan'),
(208, 'SR', 'Suriname'),
(209, 'SJ', 'Svalbard And Jan Mayen Islands'),
(210, 'SZ', 'Swaziland'),
(211, 'SE', 'Sweden'),
(212, 'CH', 'Switzerland'),
(213, 'SY', 'Syria'),
(214, 'TW', 'Taiwan'),
(215, 'TJ', 'Tajikistan'),
(216, 'TZ', 'Tanzania'),
(217, 'TH', 'Thailand'),
(218, 'TG', 'Togo'),
(219, 'TK', 'Tokelau'),
(220, 'TO', 'Tonga'),
(221, 'TT', 'Trinidad And Tobago'),
(222, 'TN', 'Tunisia'),
(223, 'TR', 'Turkey'),
(224, 'TM', 'Turkmenistan'),
(225, 'TC', 'Turks And Caicos Islands'),
(226, 'TV', 'Tuvalu'),
(227, 'UG', 'Uganda'),
(228, 'UA', 'Ukraine'),
(229, 'AE', 'United Arab Emirates'),
(230, 'GB', 'United Kingdom'),
(231, 'US', 'United States'),
(232, 'UM', 'United States Minor Outlying Islands'),
(233, 'UY', 'Uruguay'),
(234, 'UZ', 'Uzbekistan'),
(235, 'VU', 'Vanuatu'),
(236, 'VA', 'Vatican City State (Holy See)'),
(237, 'VE', 'Venezuela'),
(238, 'VN', 'Vietnam'),
(239, 'VG', 'Virgin Islands (British)'),
(240, 'VI', 'Virgin Islands (US)'),
(241, 'WF', 'Wallis And Futuna Islands'),
(242, 'EH', 'Western Sahara'),
(243, 'YE', 'Yemen'),
(244, 'YU', 'Yugoslavia'),
(245, 'ZM', 'Zambia'),
(246, 'ZW', 'Zimbabwe');

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `symbol` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `decimals` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '2',
  `xrate` decimal(65,8) DEFAULT NULL,
  `international_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `name`, `code`, `symbol`, `decimals`, `xrate`, `international_code`, `active`, `created_at`, `updated_at`) VALUES
(1, 'United Arab Emirates Dirham', 'AED', 'ARE', '2', '3.67310000', NULL, 1, NULL, NULL),
(2, 'Australian Dollar', 'AUD', '$', '2', '1.30887000', NULL, 1, NULL, NULL),
(3, 'Brazilian Real', 'BRL', 'R$', '2', '3.28990000', NULL, 1, NULL, NULL),
(4, 'Canadian Dollar', 'CAD', '$', '2', '1.28699000', NULL, 1, NULL, NULL),
(5, 'Swiss Franc', 'CHF', 'Fr', '2', '0.99879000', NULL, 1, NULL, NULL),
(6, 'Chilean Peso', 'CLP', '$', '2', '634.92703000', NULL, 1, NULL, NULL),
(7, 'Chinese Yuan', 'CNY', '¥', '2', '6.65090000', NULL, 1, NULL, NULL),
(8, 'Czech Koruna', 'CZK', 'Kč', '2', '22.07896000', NULL, 1, NULL, NULL),
(9, 'Danish Krone', 'DKK', 'kr', '2', '6.39641000', NULL, 1, NULL, NULL),
(10, 'Euro', 'EUR', '€', '2', '0.85947000', NULL, 1, NULL, NULL),
(11, 'British Pound', 'GBP', '£', '2', '0.76160000', NULL, 1, NULL, NULL),
(12, 'Hong Kong Dollar', 'HKD', '$', '2', '7.80429000', NULL, 1, NULL, NULL),
(13, 'Hungarian Forint', 'HUF', 'Ft', '2', '266.94000000', NULL, 1, NULL, NULL),
(14, 'Indonesian Rupiah', 'IDR', 'Rp', '2', '13579.08005000', NULL, 1, NULL, NULL),
(15, 'Israeli New Shekel', 'ILS', '₪', '2', '3.52770000', NULL, 1, NULL, NULL),
(16, 'Indian Rupee', 'INR', 'INR', '2', '65.02500000', NULL, 1, NULL, NULL),
(17, 'Japanese Yen', 'JPY', '¥', '2', '114.15367000', NULL, 1, NULL, NULL),
(18, 'Kenya shillings', 'KES', 'kes', '2', '103.83500000', NULL, 1, NULL, NULL),
(19, 'Korean Won', 'KRW', '₩', '2', '1130.15833000', NULL, 1, NULL, NULL),
(20, 'Mexican Peso', 'MXN', '$', '2', '19.22180000', NULL, 1, NULL, NULL),
(21, 'Malaysian Ringgit', 'MYR', 'RM', '2', '4.23999000', NULL, 1, NULL, NULL),
(22, 'Norwegian Krone', 'NOK', 'kr', '2', '8.18854000', NULL, 1, NULL, NULL),
(23, 'New Zealand Dollar', 'NZD', '$', '2', '1.46185000', NULL, 1, NULL, NULL),
(24, 'Philippine Peso', 'PHP', '₱', '2', '51.82000000', NULL, 1, NULL, NULL),
(25, 'Pakistan Rupee', 'PKR', '₨', '2', '105.34574000', NULL, 1, NULL, NULL),
(26, 'Polish Zloty', 'PLN', 'zł', '2', '3.65669000', NULL, 1, NULL, NULL),
(27, 'Russian Ruble', 'RUB', '₽', '2', '57.79350000', NULL, 1, NULL, NULL),
(28, 'Swedish Krona', 'SEK', 'kr', '2', '8.37433000', NULL, 1, NULL, NULL),
(29, 'Singapore Dollar', 'SGD', '$', '2', '1.36899000', NULL, 1, NULL, NULL),
(30, 'Thai Baht', 'THB', '฿', '2', '33.28950000', NULL, 1, NULL, NULL),
(31, 'Turkish Lira', 'TRY', '₺', '2', '3.82340000', NULL, 1, NULL, NULL),
(32, 'Taiwan Dollar', 'TWD', '$', '2', '30.27400000', NULL, 1, NULL, NULL),
(33, 'US Dollar', 'USD', '$', '2', '1.00000000', NULL, 1, NULL, NULL),
(34, 'Bolívar Fuerte', 'VEF', 'Bs.', '2', '10.06907000', NULL, 1, NULL, NULL),
(35, 'South African Rand', 'ZAR', 'R', '2', '14.24180000', NULL, 1, NULL, NULL),
(36, 'Zim Dollar', 'ZWD', '$', '2', NULL, NULL, 1, NULL, NULL),
(37, 'Zambian Kwacha', 'ZMW', 'ZMW', '2', NULL, NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `custom_fields`
--

CREATE TABLE `custom_fields` (
  `id` int(10) UNSIGNED NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `field_type` enum('number','textfield','date','decimal','textarea','checkbox','radiobox','select') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'textfield',
  `required` tinyint(4) NOT NULL DEFAULT '0',
  `radio_box_values` text COLLATE utf8mb4_unicode_ci,
  `checkbox_values` text COLLATE utf8mb4_unicode_ci,
  `select_values` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `custom_fields_meta`
--

CREATE TABLE `custom_fields_meta` (
  `id` int(10) UNSIGNED NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `custom_field_id` int(11) DEFAULT NULL,
  `name` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` enum('client','loan','group','savings','identification','shares','repayment') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(10) UNSIGNED NOT NULL,
  `office_id` int(10) UNSIGNED DEFAULT NULL,
  `created_by_id` int(10) UNSIGNED DEFAULT NULL,
  `expense_type_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(65,2) NOT NULL DEFAULT '0.00',
  `date` date DEFAULT NULL,
  `year` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `month` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recurring` tinyint(4) NOT NULL DEFAULT '0',
  `recur_frequency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '31',
  `recur_start_date` date DEFAULT NULL,
  `recur_end_date` date DEFAULT NULL,
  `recur_next_date` date DEFAULT NULL,
  `recur_type` enum('day','week','month','year') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'month',
  `status` enum('pending','approved','declined') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `approved_date` date DEFAULT NULL,
  `approved_by_id` int(10) UNSIGNED DEFAULT NULL,
  `declined_date` date DEFAULT NULL,
  `declined_by_id` int(10) UNSIGNED DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `files` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_budgets`
--

CREATE TABLE `expense_budgets` (
  `id` int(10) UNSIGNED NOT NULL,
  `created_by_id` int(10) UNSIGNED DEFAULT NULL,
  `office_id` int(10) UNSIGNED DEFAULT NULL,
  `expense_type_id` int(11) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `month` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `amount` decimal(65,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','declined') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_types`
--

CREATE TABLE `expense_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gl_account_asset_id` int(11) DEFAULT NULL,
  `gl_account_expense_id` int(11) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_types`
--

INSERT INTO `expense_types` (`id`, `name`, `gl_account_asset_id`, `gl_account_expense_id`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Office Expenses', 120, 24, NULL, '2018-12-27 06:25:17', '2019-03-14 21:47:16'),
(2, 'License and Licensing Expenses', 120, 55, NULL, '2018-12-27 06:27:23', '2019-03-14 21:45:28'),
(3, 'Fuel and Lubricant Expenses', 120, 23, NULL, '2018-12-27 06:42:29', '2019-03-14 21:45:07'),
(4, 'Medical Expenses', 120, 52, NULL, '2018-12-27 06:43:51', '2019-03-14 21:46:00'),
(5, 'Consultancy Expenses', 120, 20, NULL, '2018-12-27 07:00:22', '2019-03-14 21:44:14'),
(6, 'Communication Expenses', 117, 21, NULL, '2018-12-27 09:32:36', '2019-05-20 17:30:42'),
(7, 'Stationary and Printing Expenses', 120, 26, NULL, '2018-12-27 09:41:53', '2019-03-14 21:48:26'),
(8, 'Bank Charges', 117, 22, NULL, '2018-12-27 12:39:35', '2019-02-25 19:30:03'),
(9, 'Travel and Accommodation Expenses', 120, 44, NULL, '2018-12-27 12:41:48', '2019-03-14 21:49:40'),
(10, 'Staff Welfare/Refreshments Expenses', 120, 45, NULL, '2019-01-09 11:37:05', '2019-03-14 21:48:05'),
(11, 'Rent & Rates', 117, 25, NULL, '2019-02-12 19:26:17', '2019-04-11 23:51:00'),
(12, 'Miscelleneous expense', 120, 53, 'Payment of miscellaneous expenses', '2019-03-16 20:52:29', '2019-03-16 20:52:29'),
(13, 'Fixture,Fittings and Property Expenses', 120, 19, NULL, '2019-03-16 21:53:18', '2019-03-16 21:53:18'),
(14, 'R & M computers', 120, 48, NULL, '2019-03-16 21:58:05', '2019-03-16 21:58:05'),
(15, 'Insurance  on Motor Vehicles /Cycles', 120, 58, NULL, '2019-04-27 20:51:10', '2019-04-27 20:51:10'),
(16, 'R & M Motor vehicles / Cycles Expenses', 120, 49, NULL, '2019-04-28 17:45:45', '2019-04-28 17:45:45'),
(17, 'Corporate Social Responsibility', 120, 64, NULL, '2019-04-28 21:18:42', '2019-04-28 21:18:42'),
(18, 'Commission Expense', 120, NULL, NULL, '2019-06-26 18:30:01', '2019-06-26 18:30:01');

-- --------------------------------------------------------

--
-- Table structure for table `funds`
--

CREATE TABLE `funds` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `funds`
--

INSERT INTO `funds` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Bank', '2018-09-15 09:17:57', '2018-09-15 09:17:57'),
(2, 'Cash', '2018-12-13 10:10:53', '2018-12-13 10:10:53');

-- --------------------------------------------------------

--
-- Table structure for table `gl_accounts`
--

CREATE TABLE `gl_accounts` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `gl_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_type` enum('asset','liability','equity','income','expense') COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `manual_entries` tinyint(4) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gl_accounts`
--

INSERT INTO `gl_accounts` (`id`, `name`, `parent_id`, `gl_code`, `account_type`, `active`, `manual_entries`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'STANBIC', NULL, 'MV-1000', 'asset', 1, 1, 'NULL', '2020-04-19 07:00:00', '2020-06-30 17:02:17'),
(2, 'FNB', 0, 'MV-1001', 'asset', 1, 1, 'NULL', '2020-04-19 07:00:00', '2020-04-20 07:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `gl_closures`
--

CREATE TABLE `gl_closures` (
  `id` int(10) UNSIGNED NOT NULL,
  `office_id` int(11) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `closing_date` date NOT NULL,
  `modified_by_id` int(11) DEFAULT NULL,
  `gl_reference` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gl_journal_entries`
--

CREATE TABLE `gl_journal_entries` (
  `id` int(10) UNSIGNED NOT NULL,
  `office_id` int(11) DEFAULT NULL,
  `gl_account_id` int(11) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `account_type` enum('asset','liability','equity','income','expense') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_type` enum('disbursement','accrual','deposit','withdrawal','manual_entry','pay_charge','transfer_fund','expense','payroll','income','fee','penalty','interest','dividend','guarantee','write_off','repayment','repayment_disbursement','repayment_recovery','interest_accrual','fee_accrual','savings','shares','asset','asset_income','asset_expense','asset_depreciation') COLLATE utf8mb4_unicode_ci DEFAULT 'repayment',
  `transaction_sub_type` enum('overpayment','repayment_interest','repayment_principal','repayment_fees','repayment_penalty') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `op_balance_dr` decimal(65,4) DEFAULT NULL,
  `op_balance_cr` decimal(65,4) DEFAULT NULL,
  `debit` decimal(65,4) DEFAULT NULL,
  `credit` decimal(65,4) DEFAULT NULL,
  `reversed` tinyint(4) NOT NULL DEFAULT '0',
  `name` text COLLATE utf8mb4_unicode_ci,
  `reference` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loan_id` int(11) DEFAULT NULL,
  `loan_transaction_id` int(11) DEFAULT NULL,
  `savings_transaction_id` int(11) DEFAULT NULL,
  `savings_id` int(11) DEFAULT NULL,
  `shares_transaction_id` int(11) DEFAULT NULL,
  `payroll_transaction_id` int(11) DEFAULT NULL,
  `payment_detail_id` int(11) DEFAULT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `gl_closure_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `month` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by_id` int(11) DEFAULT NULL,
  `modified_by_id` int(11) DEFAULT NULL,
  `reconciled` tinyint(4) NOT NULL DEFAULT '0',
  `manual_entry` tinyint(4) NOT NULL DEFAULT '0',
  `approved` tinyint(4) NOT NULL DEFAULT '1',
  `approved_by_id` int(11) DEFAULT NULL,
  `approved_date` date DEFAULT NULL,
  `approved_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gl_journal_entries`
--

INSERT INTO `gl_journal_entries` (`id`, `office_id`, `gl_account_id`, `currency_id`, `account_type`, `transaction_type`, `transaction_sub_type`, `op_balance_dr`, `op_balance_cr`, `debit`, `credit`, `reversed`, `name`, `reference`, `loan_id`, `loan_transaction_id`, `savings_transaction_id`, `savings_id`, `shares_transaction_id`, `payroll_transaction_id`, `payment_detail_id`, `transaction_id`, `gl_closure_id`, `date`, `month`, `year`, `notes`, `created_by_id`, `modified_by_id`, `reconciled`, `manual_entry`, `approved`, `approved_by_id`, `approved_date`, `approved_notes`, `created_at`, `updated_at`) VALUES
(1068, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '100000.0000', 0, 'Loan Disbursement', NULL, 372, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-10', '08', '2020', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:05:26', '2020-09-01 22:05:26'),
(1069, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '100000.0000', NULL, 0, 'Loan Disbursement', NULL, 372, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-10', '08', '2020', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:05:26', '2020-09-01 22:05:26'),
(1070, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '40425.0000', 0, 'Loan Disbursement', NULL, 373, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-05', '09', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:07:33', '2020-09-01 22:07:33'),
(1071, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '40425.0000', NULL, 0, 'Loan Disbursement', NULL, 373, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-05', '09', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:07:33', '2020-09-01 22:07:33'),
(1072, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '53021.3500', 0, 'Loan Disbursement', NULL, 374, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-07-16', '07', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:11:28', '2020-09-01 22:11:28'),
(1073, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '53021.3500', NULL, 0, 'Loan Disbursement', NULL, 374, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-07-16', '07', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:11:28', '2020-09-01 22:11:28'),
(1074, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '70000.0000', 0, 'Loan Disbursement', NULL, 375, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-06', '11', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:12:16', '2020-09-01 22:12:16'),
(1075, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '70000.0000', NULL, 0, 'Loan Disbursement', NULL, 375, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-06', '11', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:12:16', '2020-09-01 22:12:16'),
(1076, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '40000.0000', 0, 'Loan Disbursement', NULL, 376, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-04-26', '04', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:15:01', '2020-09-01 22:15:01'),
(1077, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '40000.0000', NULL, 0, 'Loan Disbursement', NULL, 376, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-04-26', '04', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:15:01', '2020-09-01 22:15:01'),
(1078, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '130000.0000', 0, 'Loan Disbursement', NULL, 377, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-26', '08', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:24:09', '2020-09-01 22:24:09'),
(1079, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '130000.0000', NULL, 0, 'Loan Disbursement', NULL, 377, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-26', '08', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:24:09', '2020-09-01 22:24:09'),
(1080, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '72000.0000', 0, 'Loan Disbursement', NULL, 378, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-07', '11', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:26:30', '2020-09-01 22:26:30'),
(1081, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '72000.0000', NULL, 0, 'Loan Disbursement', NULL, 378, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-07', '11', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:26:30', '2020-09-01 22:26:30'),
(1082, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '250000.0000', 0, 'Loan Disbursement', NULL, 379, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-12', '08', '2020', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:29:03', '2020-09-01 22:29:03'),
(1083, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '250000.0000', NULL, 0, 'Loan Disbursement', NULL, 379, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-12', '08', '2020', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:29:03', '2020-09-01 22:29:03'),
(1084, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '70750.0000', 0, 'Loan Disbursement', NULL, 380, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-25', '09', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:36:22', '2020-09-01 22:36:22'),
(1085, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '70750.0000', NULL, 0, 'Loan Disbursement', NULL, 380, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-25', '09', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:36:22', '2020-09-01 22:36:22'),
(1086, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '55075.8000', 0, 'Loan Disbursement', NULL, 381, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-06-20', '06', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:38:30', '2020-09-01 22:38:30'),
(1087, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '55075.8000', NULL, 0, 'Loan Disbursement', NULL, 381, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-06-20', '06', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:38:30', '2020-09-01 22:38:30'),
(1088, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '70750.0000', 0, 'Loan Disbursement', NULL, 382, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-25', '09', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:42:17', '2020-09-01 22:42:17'),
(1089, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '70750.0000', NULL, 0, 'Loan Disbursement', NULL, 382, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-25', '09', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:42:17', '2020-09-01 22:42:17'),
(1090, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '13446.0000', 0, 'Loan Disbursement', NULL, 383, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-07-04', '07', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:43:01', '2020-09-01 22:43:01'),
(1091, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '13446.0000', NULL, 0, 'Loan Disbursement', NULL, 383, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-07-04', '07', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:43:01', '2020-09-01 22:43:01'),
(1092, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '220675.0000', 0, 'Loan Disbursement', NULL, 384, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-04-03', '04', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:43:26', '2020-09-01 22:43:26'),
(1093, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '220675.0000', NULL, 0, 'Loan Disbursement', NULL, 384, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-04-03', '04', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:43:26', '2020-09-01 22:43:26'),
(1094, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '35000.0000', 0, 'Loan Disbursement', NULL, 385, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-01', '02', '2020', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:43:59', '2020-09-01 22:43:59'),
(1095, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '35000.0000', NULL, 0, 'Loan Disbursement', NULL, 385, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-01', '02', '2020', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:43:59', '2020-09-01 22:43:59'),
(1098, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '50000.0000', 0, 'Loan Disbursement', NULL, 388, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-02-22', '02', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:45:15', '2020-09-01 22:45:15'),
(1099, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '50000.0000', NULL, 0, 'Loan Disbursement', NULL, 388, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-02-22', '02', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:45:15', '2020-09-01 22:45:15'),
(1100, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '34185.0000', 0, 'Loan Disbursement', NULL, 389, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-05', '02', '2020', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:45:58', '2020-09-01 22:45:58'),
(1101, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '34185.0000', NULL, 0, 'Loan Disbursement', NULL, 389, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-05', '02', '2020', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:45:58', '2020-09-01 22:45:58'),
(1102, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '105000.0000', 0, 'Loan Disbursement', NULL, 403, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2018-10-02', '10', '2018', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:46:40', '2020-09-01 22:46:40'),
(1103, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '105000.0000', NULL, 0, 'Loan Disbursement', NULL, 403, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2018-10-02', '10', '2018', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:46:40', '2020-09-01 22:46:40'),
(1104, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '96300.0000', 0, 'Loan Disbursement', NULL, 405, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-03-14', '03', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:50:52', '2020-09-01 22:50:52'),
(1105, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '96300.0000', NULL, 0, 'Loan Disbursement', NULL, 405, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-03-14', '03', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:50:52', '2020-09-01 22:50:52'),
(1106, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '19200.0000', 0, 'Loan Disbursement', NULL, 404, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-03', '03', '2020', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:51:26', '2020-09-01 22:51:26'),
(1107, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '19200.0000', NULL, 0, 'Loan Disbursement', NULL, 404, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-03', '03', '2020', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:51:26', '2020-09-01 22:51:26'),
(1108, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '39325.0000', 0, 'Loan Disbursement', NULL, 406, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-26', '08', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:52:06', '2020-09-01 22:52:06'),
(1109, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '39325.0000', NULL, 0, 'Loan Disbursement', NULL, 406, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-26', '08', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:52:06', '2020-09-01 22:52:06'),
(1110, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '67000.0000', 0, 'Loan Disbursement', NULL, 390, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-01', '08', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:52:32', '2020-09-01 22:52:32'),
(1111, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '67000.0000', NULL, 0, 'Loan Disbursement', NULL, 390, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-01', '08', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:52:32', '2020-09-01 22:52:32'),
(1112, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '31500.0000', 0, 'Loan Disbursement', NULL, 401, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-11', '12', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:52:58', '2020-09-01 22:52:58'),
(1113, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '31500.0000', NULL, 0, 'Loan Disbursement', NULL, 401, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-11', '12', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:52:58', '2020-09-01 22:52:58'),
(1114, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '14000.0000', 0, 'Loan Disbursement', NULL, 398, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-09', '04', '2020', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:53:26', '2020-09-01 22:53:26'),
(1115, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '14000.0000', NULL, 0, 'Loan Disbursement', NULL, 398, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-09', '04', '2020', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:53:26', '2020-09-01 22:53:26'),
(1116, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '15450.0000', 0, 'Loan Disbursement', NULL, 397, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-03', '03', '2020', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:54:01', '2020-09-01 22:54:01'),
(1117, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '15450.0000', NULL, 0, 'Loan Disbursement', NULL, 397, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-03', '03', '2020', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:54:01', '2020-09-01 22:54:01'),
(1118, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '35000.0000', 0, 'Loan Disbursement', NULL, 400, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-07-19', '07', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:54:26', '2020-09-01 22:54:26'),
(1119, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '35000.0000', NULL, 0, 'Loan Disbursement', NULL, 400, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-07-19', '07', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:54:26', '2020-09-01 22:54:26'),
(1120, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '140260.8000', 0, 'Loan Disbursement', NULL, 396, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-26', '11', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:54:49', '2020-09-01 22:54:49'),
(1121, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '140260.8000', NULL, 0, 'Loan Disbursement', NULL, 396, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-26', '11', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:54:49', '2020-09-01 22:54:49'),
(1122, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '31200.0000', 0, 'Loan Disbursement', NULL, 395, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-22', '01', '2020', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:55:17', '2020-09-01 22:55:17'),
(1123, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '31200.0000', NULL, 0, 'Loan Disbursement', NULL, 395, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-22', '01', '2020', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:55:17', '2020-09-01 22:55:17'),
(1124, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '2563.0000', 0, 'Loan Disbursement', NULL, 399, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-06-27', '06', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:55:40', '2020-09-01 22:55:40'),
(1125, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '2563.0000', NULL, 0, 'Loan Disbursement', NULL, 399, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-06-27', '06', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:55:40', '2020-09-01 22:55:40'),
(1126, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '23000.0000', 0, 'Loan Disbursement', NULL, 392, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-01', '11', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:56:03', '2020-09-01 22:56:03'),
(1127, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '23000.0000', NULL, 0, 'Loan Disbursement', NULL, 392, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-01', '11', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:56:03', '2020-09-01 22:56:03'),
(1128, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '70000.0000', 0, 'Loan Disbursement', NULL, 402, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-16', '03', '2020', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:56:25', '2020-09-01 22:56:25'),
(1129, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '70000.0000', NULL, 0, 'Loan Disbursement', NULL, 402, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-16', '03', '2020', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:56:25', '2020-09-01 22:56:25'),
(1130, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '19000.0000', 0, 'Loan Disbursement', NULL, 394, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-05', '08', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:56:47', '2020-09-01 22:56:47'),
(1131, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '19000.0000', NULL, 0, 'Loan Disbursement', NULL, 394, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-05', '08', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:56:47', '2020-09-01 22:56:47'),
(1132, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '20750.0000', 0, 'Loan Disbursement', NULL, 393, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-17', '07', '2020', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:57:08', '2020-09-01 22:57:08'),
(1133, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '20750.0000', NULL, 0, 'Loan Disbursement', NULL, 393, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-17', '07', '2020', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:57:08', '2020-09-01 22:57:08'),
(1134, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '30000.0000', 0, 'Loan Disbursement', NULL, 391, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-07-24', '07', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:58:10', '2020-09-01 22:58:10'),
(1135, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '30000.0000', NULL, 0, 'Loan Disbursement', NULL, 391, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-07-24', '07', '2019', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 22:58:10', '2020-09-01 22:58:10'),
(1136, 1, 1, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '71200.0000', 0, 'Loan Disbursement', NULL, 387, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-03', '04', '2020', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 23:11:35', '2020-09-01 23:11:35'),
(1137, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '71200.0000', NULL, 0, 'Loan Disbursement', NULL, 387, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-03', '04', '2020', NULL, 8, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-01 23:11:35', '2020-09-01 23:11:35'),
(1138, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '2300.0000', 0, 'Interest Repayment', '873', 392, 873, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 21:48:25', '2020-09-03 21:48:25'),
(1139, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2300.0000', NULL, 0, 'Interest Repayment', '873', 392, 873, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 21:48:25', '2020-09-03 21:48:25'),
(1140, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '700.0000', 0, 'Principal Repayment', '874', 392, 874, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-31', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:01:45', '2020-09-03 22:01:45'),
(1141, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '700.0000', NULL, 0, 'Principal Repayment', '874', 392, 874, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-31', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:01:45', '2020-09-03 22:01:45'),
(1142, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '2300.0000', 0, 'Interest Repayment', '875', 392, 875, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-31', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:03:13', '2020-09-03 22:03:13'),
(1143, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2300.0000', NULL, 0, 'Interest Repayment', '875', 392, 875, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-31', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:03:13', '2020-09-03 22:03:13'),
(1144, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '7770.0000', 0, 'Principal Repayment', '876', 392, 876, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:06:36', '2020-09-03 22:06:36'),
(1145, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7770.0000', NULL, 0, 'Principal Repayment', '876', 392, 876, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:06:37', '2020-09-03 22:06:37'),
(1146, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '2230.0000', 0, 'Interest Repayment', '877', 392, 877, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:08:16', '2020-09-03 22:08:16'),
(1147, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2230.0000', NULL, 0, 'Interest Repayment', '877', 392, 877, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:08:16', '2020-09-03 22:08:16'),
(1148, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '547.0000', 0, 'Principal Repayment', '878', 392, 878, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:11:02', '2020-09-03 22:11:02'),
(1149, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '547.0000', NULL, 0, 'Principal Repayment', '878', 392, 878, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:11:02', '2020-09-03 22:11:02'),
(1150, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1453.0000', 0, 'Interest Repayment', '879', 392, 879, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:12:42', '2020-09-03 22:12:42'),
(1151, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1453.0000', NULL, 0, 'Interest Repayment', '879', 392, 879, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:12:42', '2020-09-03 22:12:42'),
(1152, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '601.7000', 0, 'Principal Repayment', '880', 392, 880, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:16:44', '2020-09-03 22:16:44'),
(1153, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '601.7000', NULL, 0, 'Principal Repayment', '880', 392, 880, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:16:44', '2020-09-03 22:16:44'),
(1154, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1398.1300', 0, 'Interest Repayment', '881', 392, 881, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:18:45', '2020-09-03 22:18:45'),
(1155, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1398.1300', NULL, 0, 'Interest Repayment', '881', 392, 881, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:18:45', '2020-09-03 22:18:45'),
(1156, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '31.8700', 0, 'Principal Repayment', '882', 392, 882, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:22:16', '2020-09-03 22:22:16'),
(1157, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '31.8700', NULL, 0, 'Principal Repayment', '882', 392, 882, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:22:16', '2020-09-03 22:22:16'),
(1158, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1338.1300', 0, 'Interest Repayment', '883', 392, 883, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:23:55', '2020-09-03 22:23:55'),
(1159, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1338.1300', NULL, 0, 'Interest Repayment', '883', 392, 883, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:23:55', '2020-09-03 22:23:55'),
(1160, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '165.0600', 0, 'Principal Repayment', '884', 392, 884, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:30:55', '2020-09-03 22:30:55'),
(1161, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '165.0600', NULL, 0, 'Principal Repayment', '884', 392, 884, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:30:55', '2020-09-03 22:30:55'),
(1162, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1334.9400', 0, 'Interest Repayment', '885', 392, 885, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:32:20', '2020-09-03 22:32:20'),
(1163, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1334.9400', NULL, 0, 'Interest Repayment', '885', 392, 885, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:32:20', '2020-09-03 22:32:20'),
(1164, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '27500.0000', 0, 'Interest Repayment', '886', 379, 886, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-09-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:33:20', '2020-09-03 22:33:20'),
(1165, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '27500.0000', NULL, 0, 'Interest Repayment', '886', 379, 886, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-09-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:33:20', '2020-09-03 22:33:20'),
(1166, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '250035.0000', 0, 'Principal Repayment', '887', 379, 887, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-09-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:34:18', '2020-09-03 22:34:18'),
(1167, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '250035.0000', NULL, 0, 'Principal Repayment', '887', 379, 887, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-09-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:34:18', '2020-09-03 22:34:18'),
(1168, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '81.5500', 0, 'Principal Repayment', '888', 392, 888, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-09-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:34:34', '2020-09-03 22:34:34'),
(1169, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '81.5500', NULL, 0, 'Principal Repayment', '888', 392, 888, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-09-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:34:34', '2020-09-03 22:34:34'),
(1170, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1318.4500', 0, 'Interest Repayment', '889', 392, 889, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-30', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:36:23', '2020-09-03 22:36:23'),
(1171, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1318.4500', NULL, 0, 'Interest Repayment', '889', 392, 889, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-30', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:36:23', '2020-09-03 22:36:23'),
(1172, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1641.6000', 0, 'Interest Repayment', '890', 398, 890, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-17', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:38:27', '2020-09-03 22:38:27'),
(1173, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1641.6000', NULL, 0, 'Interest Repayment', '890', 398, 890, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-17', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:38:27', '2020-09-03 22:38:27'),
(1174, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '8689.7200', 0, 'Principal Repayment', '891', 392, 891, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-20', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:39:29', '2020-09-03 22:39:29'),
(1175, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '8689.7200', NULL, 0, 'Principal Repayment', '891', 392, 891, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-20', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:39:29', '2020-09-03 22:39:29'),
(1176, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '575.2000', 0, 'Principal Repayment', '892', 398, 892, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-17', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:39:58', '2020-09-03 22:39:58'),
(1177, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '575.2000', NULL, 0, 'Principal Repayment', '892', 398, 892, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-17', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:39:58', '2020-09-03 22:39:58'),
(1178, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1310.2800', 0, 'Interest Repayment', '893', 392, 893, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-20', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:41:26', '2020-09-03 22:41:26'),
(1179, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1310.2800', NULL, 0, 'Interest Repayment', '893', 392, 893, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-20', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:41:26', '2020-09-03 22:41:26'),
(1180, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '5696.0000', 0, 'Interest Repayment', '894', 387, 894, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-22', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:42:36', '2020-09-03 22:42:36'),
(1181, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '5696.0000', NULL, 0, 'Interest Repayment', '894', 387, 894, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-22', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:42:36', '2020-09-03 22:42:36'),
(1182, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '58.6900', 0, 'Principal Repayment', '895', 392, 895, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-09-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:43:54', '2020-09-03 22:43:54'),
(1183, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '58.6900', NULL, 0, 'Principal Repayment', '895', 392, 895, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-09-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:43:54', '2020-09-03 22:43:54'),
(1184, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '4304.0000', 0, 'Principal Repayment', '896', 387, 896, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-22', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:43:55', '2020-09-03 22:43:55'),
(1185, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '4304.0000', NULL, 0, 'Principal Repayment', '896', 387, 896, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-22', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:43:55', '2020-09-03 22:43:55'),
(1186, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '5351.6800', 0, 'Interest Repayment', '897', 387, 897, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:45:12', '2020-09-03 22:45:12'),
(1187, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '5351.6800', NULL, 0, 'Interest Repayment', '897', 387, 897, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:45:12', '2020-09-03 22:45:12'),
(1188, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '441.3100', 0, 'Interest Repayment', '898', 392, 898, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-09-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:45:44', '2020-09-03 22:45:44'),
(1189, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '441.3100', NULL, 0, 'Interest Repayment', '898', 392, 898, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-09-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:45:44', '2020-09-03 22:45:44'),
(1190, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '4648.3200', 0, 'Principal Repayment', '899', 387, 899, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:46:44', '2020-09-03 22:46:44'),
(1191, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '4648.3200', NULL, 0, 'Principal Repayment', '899', 387, 899, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:46:44', '2020-09-03 22:46:44'),
(1192, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '10000.0000', 0, 'Principal Repayment', '900', 387, 900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:48:24', '2020-09-03 22:48:24'),
(1193, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '10000.0000', NULL, 0, 'Principal Repayment', '900', 387, 900, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:48:24', '2020-09-03 22:48:24'),
(1194, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '10000.0000', 0, 'Principal Repayment', '901', 387, 901, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:49:28', '2020-09-03 22:49:28'),
(1195, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '10000.0000', NULL, 0, 'Principal Repayment', '901', 387, 901, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:49:28', '2020-09-03 22:49:28'),
(1196, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3379.8100', 0, 'Interest Repayment', '902', 387, 902, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-16', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:50:44', '2020-09-03 22:50:44'),
(1197, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3379.8100', NULL, 0, 'Interest Repayment', '902', 387, 902, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-16', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:50:44', '2020-09-03 22:50:44'),
(1198, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '6620.1900', 0, 'Principal Repayment', '903', 387, 903, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-16', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:52:00', '2020-09-03 22:52:00'),
(1199, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '6620.1900', NULL, 0, 'Principal Repayment', '903', 387, 903, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-16', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:52:00', '2020-09-03 22:52:00'),
(1200, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '2850.2000', 0, 'Interest Repayment', '904', 387, 904, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:53:23', '2020-09-03 22:53:23'),
(1201, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2850.2000', NULL, 0, 'Interest Repayment', '904', 387, 904, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:53:23', '2020-09-03 22:53:23'),
(1202, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7075.0000', 0, 'Interest Repayment', '905', 380, 905, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-31', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:55:05', '2020-09-03 22:55:05'),
(1203, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7075.0000', NULL, 0, 'Interest Repayment', '905', 380, 905, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-31', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:55:05', '2020-09-03 22:55:05'),
(1204, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '4299.6200', 0, 'Principal Repayment', '906', 387, 906, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:55:34', '2020-09-03 22:55:34'),
(1205, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '4299.6200', NULL, 0, 'Principal Repayment', '906', 387, 906, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:55:34', '2020-09-03 22:55:34'),
(1206, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7075.0000', 0, 'Interest Repayment', '907', 380, 907, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:57:07', '2020-09-03 22:57:07'),
(1207, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7075.0000', NULL, 0, 'Interest Repayment', '907', 380, 907, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:57:07', '2020-09-03 22:57:07'),
(1208, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7700.0000', 0, 'Interest Repayment', '908', 402, 908, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-17', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:57:54', '2020-09-03 22:57:54'),
(1209, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7700.0000', NULL, 0, 'Interest Repayment', '908', 402, 908, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-17', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:57:54', '2020-09-03 22:57:54'),
(1210, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7700.0000', 0, 'Interest Repayment', '909', 402, 909, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-19', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:58:41', '2020-09-03 22:58:41'),
(1211, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7700.0000', NULL, 0, 'Interest Repayment', '909', 402, 909, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-19', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:58:41', '2020-09-03 22:58:41'),
(1212, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7075.0000', 0, 'Interest Repayment', '910', 380, 910, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:59:22', '2020-09-03 22:59:22'),
(1213, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7075.0000', NULL, 0, 'Interest Repayment', '910', 380, 910, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:59:22', '2020-09-03 22:59:22'),
(1214, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7700.0000', 0, 'Interest Repayment', '911', 402, 911, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:59:37', '2020-09-03 22:59:37'),
(1215, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7700.0000', NULL, 0, 'Interest Repayment', '911', 402, 911, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 22:59:37', '2020-09-03 22:59:37'),
(1216, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '4900.1100', 0, 'Interest Repayment', '912', 402, 912, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-22', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:00:25', '2020-09-03 23:00:25'),
(1217, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '4900.1100', NULL, 0, 'Interest Repayment', '912', 402, 912, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-22', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:00:25', '2020-09-03 23:00:25'),
(1218, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7075.0000', 0, 'Interest Repayment', '914', 380, 914, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:02:28', '2020-09-03 23:02:28'),
(1219, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7075.0000', NULL, 0, 'Interest Repayment', '914', 380, 914, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:02:28', '2020-09-03 23:02:28'),
(1220, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7075.0000', 0, 'Interest Repayment', '915', 380, 915, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:03:50', '2020-09-03 23:03:50'),
(1221, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7075.0000', NULL, 0, 'Interest Repayment', '915', 380, 915, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:03:50', '2020-09-03 23:03:50'),
(1222, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7075.0000', 0, 'Interest Repayment', '916', 380, 916, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:05:02', '2020-09-03 23:05:02'),
(1223, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7075.0000', NULL, 0, 'Interest Repayment', '916', 380, 916, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:05:02', '2020-09-03 23:05:02'),
(1224, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7075.0000', 0, 'Interest Repayment', '917', 380, 917, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:06:31', '2020-09-03 23:06:31'),
(1225, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7075.0000', NULL, 0, 'Interest Repayment', '917', 380, 917, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:06:31', '2020-09-03 23:06:31'),
(1226, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '2304.0000', 0, 'Interest Repayment', '918', 404, 918, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:06:39', '2020-09-03 23:06:39'),
(1227, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2304.0000', NULL, 0, 'Interest Repayment', '918', 404, 918, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:06:39', '2020-09-03 23:06:39'),
(1228, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '196.0000', 0, 'Principal Repayment', '919', 404, 919, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:08:01', '2020-09-03 23:08:01'),
(1229, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '196.0000', NULL, 0, 'Principal Repayment', '919', 404, 919, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:08:01', '2020-09-03 23:08:01'),
(1230, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7074.9000', 0, 'Interest Repayment', '920', 380, 920, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:08:02', '2020-09-03 23:08:02'),
(1231, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7074.9000', NULL, 0, 'Interest Repayment', '920', 380, 920, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:08:02', '2020-09-03 23:08:02'),
(1232, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '2280.4800', 0, 'Interest Repayment', '921', 404, 921, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:09:14', '2020-09-03 23:09:14'),
(1233, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2280.4800', NULL, 0, 'Interest Repayment', '921', 404, 921, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:09:14', '2020-09-03 23:09:14'),
(1234, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '419.5200', 0, 'Principal Repayment', '923', 404, 923, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:10:27', '2020-09-03 23:10:27'),
(1235, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '419.5200', NULL, 0, 'Principal Repayment', '923', 404, 923, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:10:27', '2020-09-03 23:10:27'),
(1236, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '2230.1400', 0, 'Interest Repayment', '924', 404, 924, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:11:32', '2020-09-03 23:11:32'),
(1237, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2230.1400', NULL, 0, 'Interest Repayment', '924', 404, 924, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:11:32', '2020-09-03 23:11:32'),
(1238, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '769.8600', 0, 'Principal Repayment', '926', 404, 926, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:13:09', '2020-09-03 23:13:09'),
(1239, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '769.8600', NULL, 0, 'Principal Repayment', '926', 404, 926, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:13:09', '2020-09-03 23:13:09'),
(1240, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '2137.7500', 0, 'Interest Repayment', '928', 404, 928, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-15', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:15:05', '2020-09-03 23:15:05'),
(1241, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2137.7500', NULL, 0, 'Interest Repayment', '928', 404, 928, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-15', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:15:05', '2020-09-03 23:15:05'),
(1242, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '17814.6200', 0, 'Principal Repayment', '929', 404, 929, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-15', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:16:52', '2020-09-03 23:16:52'),
(1243, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '17814.6200', NULL, 0, 'Principal Repayment', '929', 404, 929, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-15', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:16:52', '2020-09-03 23:16:52'),
(1244, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '2445.9000', 0, 'Interest Repayment', '930', 389, 930, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-19', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:20:02', '2020-09-03 23:20:02'),
(1245, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2445.9000', NULL, 0, 'Interest Repayment', '930', 389, 930, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-19', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:20:02', '2020-09-03 23:20:02'),
(1246, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '108.2000', 0, 'Principal Repayment', '931', 389, 931, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-19', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:21:18', '2020-09-03 23:21:18');
INSERT INTO `gl_journal_entries` (`id`, `office_id`, `gl_account_id`, `currency_id`, `account_type`, `transaction_type`, `transaction_sub_type`, `op_balance_dr`, `op_balance_cr`, `debit`, `credit`, `reversed`, `name`, `reference`, `loan_id`, `loan_transaction_id`, `savings_transaction_id`, `savings_id`, `shares_transaction_id`, `payroll_transaction_id`, `payment_detail_id`, `transaction_id`, `gl_closure_id`, `date`, `month`, `year`, `notes`, `created_by_id`, `modified_by_id`, `reconciled`, `manual_entry`, `approved`, `approved_by_id`, `approved_date`, `approved_notes`, `created_at`, `updated_at`) VALUES
(1247, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '108.2000', NULL, 0, 'Principal Repayment', '931', 389, 931, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-19', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:21:18', '2020-09-03 23:21:18'),
(1248, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1750.0000', 0, 'Interest Repayment', '932', 389, 932, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-05', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:24:02', '2020-09-03 23:24:02'),
(1249, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1750.0000', NULL, 0, 'Interest Repayment', '932', 389, 932, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-05', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:24:02', '2020-09-03 23:24:02'),
(1250, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3435.0000', 0, 'Interest Repayment', '933', 389, 933, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-23', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:25:34', '2020-09-03 23:25:34'),
(1251, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3435.0000', NULL, 0, 'Interest Repayment', '933', 389, 933, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-23', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:25:34', '2020-09-03 23:25:34'),
(1252, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '165.0000', 0, 'Principal Repayment', '934', 389, 934, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-23', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:26:41', '2020-09-03 23:26:41'),
(1253, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '165.0000', NULL, 0, 'Principal Repayment', '934', 389, 934, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-23', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:26:41', '2020-09-03 23:26:41'),
(1254, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3418.5000', 0, 'Interest Repayment', '935', 389, 935, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-20', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:27:46', '2020-09-03 23:27:46'),
(1255, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3418.5000', NULL, 0, 'Interest Repayment', '935', 389, 935, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-20', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:27:46', '2020-09-03 23:27:46'),
(1256, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3418.5000', 0, 'Interest Repayment', '936', 389, 936, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:29:02', '2020-09-03 23:29:02'),
(1257, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3418.5000', NULL, 0, 'Interest Repayment', '936', 389, 936, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:29:02', '2020-09-03 23:29:02'),
(1258, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3418.5000', 0, 'Interest Repayment', '937', 389, 937, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-14', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:31:09', '2020-09-03 23:31:09'),
(1259, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3418.5000', NULL, 0, 'Interest Repayment', '937', 389, 937, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-14', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:31:09', '2020-09-03 23:31:09'),
(1260, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3418.5000', 0, 'Interest Repayment', '938', 389, 938, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-08', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:32:19', '2020-09-03 23:32:19'),
(1261, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3418.5000', NULL, 0, 'Interest Repayment', '938', 389, 938, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-08', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:32:19', '2020-09-03 23:32:19'),
(1262, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7075.0000', 0, 'Interest Repayment', '939', 382, 939, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-31', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:32:53', '2020-09-03 23:32:53'),
(1263, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7075.0000', NULL, 0, 'Interest Repayment', '939', 382, 939, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-31', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:32:53', '2020-09-03 23:32:53'),
(1264, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3418.5000', 0, 'Interest Repayment', '940', 389, 940, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:33:19', '2020-09-03 23:33:19'),
(1265, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3418.5000', NULL, 0, 'Interest Repayment', '940', 389, 940, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:33:19', '2020-09-03 23:33:19'),
(1266, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '2624.7800', 0, 'Interest Repayment', '941', 389, 941, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-24', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:34:31', '2020-09-03 23:34:31'),
(1267, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2624.7800', NULL, 0, 'Interest Repayment', '941', 389, 941, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-24', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:34:31', '2020-09-03 23:34:31'),
(1268, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '152.0000', 0, 'Principal Repayment', '942', 389, 942, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-24', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:36:02', '2020-09-03 23:36:02'),
(1269, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '152.0000', NULL, 0, 'Principal Repayment', '942', 389, 942, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-24', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:36:02', '2020-09-03 23:36:02'),
(1270, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7075.0000', 0, 'Interest Repayment', '943', 382, 943, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:36:50', '2020-09-03 23:36:50'),
(1271, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7075.0000', NULL, 0, 'Interest Repayment', '943', 382, 943, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:36:50', '2020-09-03 23:36:50'),
(1272, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7075.0000', 0, 'Interest Repayment', '944', 382, 944, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:39:44', '2020-09-03 23:39:44'),
(1273, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7075.0000', NULL, 0, 'Interest Repayment', '944', 382, 944, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:39:44', '2020-09-03 23:39:44'),
(1274, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3403.3000', 0, 'Interest Repayment', '945', 389, 945, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:41:44', '2020-09-03 23:41:44'),
(1275, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3403.3000', NULL, 0, 'Interest Repayment', '945', 389, 945, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:41:44', '2020-09-03 23:41:44'),
(1276, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7075.0000', 0, 'Interest Repayment', '946', 382, 946, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:42:43', '2020-09-03 23:42:43'),
(1277, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7075.0000', NULL, 0, 'Interest Repayment', '946', 382, 946, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:42:43', '2020-09-03 23:42:43'),
(1278, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7075.0000', 0, 'Interest Repayment', '948', 382, 948, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:43:46', '2020-09-03 23:43:46'),
(1279, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7075.0000', NULL, 0, 'Interest Repayment', '948', 382, 948, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:43:46', '2020-09-03 23:43:46'),
(1280, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7075.0000', 0, 'Interest Repayment', '949', 382, 949, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:44:47', '2020-09-03 23:44:47'),
(1281, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7075.0000', NULL, 0, 'Interest Repayment', '949', 382, 949, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:44:47', '2020-09-03 23:44:47'),
(1282, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7075.0000', 0, 'Interest Repayment', '950', 382, 950, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:45:42', '2020-09-03 23:45:42'),
(1283, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7075.0000', NULL, 0, 'Interest Repayment', '950', 382, 950, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:45:42', '2020-09-03 23:45:42'),
(1284, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '2800.0000', 0, 'Interest Repayment', '951', 385, 951, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-30', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:46:13', '2020-09-03 23:46:13'),
(1285, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2800.0000', NULL, 0, 'Interest Repayment', '951', 385, 951, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-30', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:46:13', '2020-09-03 23:46:13'),
(1286, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7074.9000', 0, 'Interest Repayment', '952', 382, 952, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:46:49', '2020-09-03 23:46:49'),
(1287, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7074.9000', NULL, 0, 'Interest Repayment', '952', 382, 952, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-03', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:46:49', '2020-09-03 23:46:49'),
(1288, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3360.0000', 0, 'Interest Repayment', '954', 385, 954, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:50:24', '2020-09-03 23:50:24'),
(1289, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3360.0000', NULL, 0, 'Interest Repayment', '954', 385, 954, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:50:24', '2020-09-03 23:50:24'),
(1290, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3436.0000', 0, 'Interest Repayment', '955', 385, 955, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:51:27', '2020-09-03 23:51:27'),
(1291, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3436.0000', NULL, 0, 'Interest Repayment', '955', 385, 955, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:51:27', '2020-09-03 23:51:27'),
(1292, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3480.0000', 0, 'Interest Repayment', '956', 385, 956, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:52:53', '2020-09-03 23:52:53'),
(1293, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3480.0000', NULL, 0, 'Interest Repayment', '956', 385, 956, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:52:53', '2020-09-03 23:52:53'),
(1294, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3882.6700', 0, 'Interest Repayment', '957', 385, 957, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:54:01', '2020-09-03 23:54:01'),
(1295, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3882.6700', NULL, 0, 'Interest Repayment', '957', 385, 957, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:54:01', '2020-09-03 23:54:01'),
(1296, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '117.3300', 0, 'Principal Repayment', '958', 385, 958, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:55:09', '2020-09-03 23:55:09'),
(1297, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '117.3300', NULL, 0, 'Principal Repayment', '958', 385, 958, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:55:09', '2020-09-03 23:55:09'),
(1298, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3990.6100', 0, 'Interest Repayment', '959', 385, 959, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:56:33', '2020-09-03 23:56:33'),
(1299, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3990.6100', NULL, 0, 'Interest Repayment', '959', 385, 959, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:56:33', '2020-09-03 23:56:33'),
(1300, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '9.3900', 0, 'Principal Repayment', '960', 385, 960, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:57:34', '2020-09-03 23:57:34'),
(1301, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '9.3900', NULL, 0, 'Principal Repayment', '960', 385, 960, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:57:34', '2020-09-03 23:57:34'),
(1302, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1450.6100', 0, 'Interest Repayment', '961', 385, 961, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:58:37', '2020-09-03 23:58:37'),
(1303, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1450.6100', NULL, 0, 'Interest Repayment', '961', 385, 961, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:58:37', '2020-09-03 23:58:37'),
(1304, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '10.1400', 0, 'Principal Repayment', '962', 385, 962, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:59:44', '2020-09-03 23:59:44'),
(1305, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '10.1400', NULL, 0, 'Principal Repayment', '962', 385, 962, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-03 23:59:44', '2020-09-03 23:59:44'),
(1306, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '57.5000', 0, 'Principal Repayment', '963', 373, 963, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:15:22', '2020-09-04 15:15:22'),
(1307, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '57.5000', NULL, 0, 'Principal Repayment', '963', 373, 963, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:15:22', '2020-09-04 15:15:22'),
(1308, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '4042.5000', 0, 'Interest Repayment', '964', 373, 964, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:16:53', '2020-09-04 15:16:53'),
(1309, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '4042.5000', NULL, 0, 'Interest Repayment', '964', 373, 964, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:16:53', '2020-09-04 15:16:53'),
(1310, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3120.0000', 0, 'Interest Repayment', '965', 395, 965, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:18:05', '2020-09-04 15:18:05'),
(1311, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3120.0000', NULL, 0, 'Interest Repayment', '965', 395, 965, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:18:05', '2020-09-04 15:18:05'),
(1312, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '2880.0000', 0, 'Principal Repayment', '966', 395, 966, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-09-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:19:06', '2020-09-04 15:19:06'),
(1313, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2880.0000', NULL, 0, 'Principal Repayment', '966', 395, 966, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-09-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:19:06', '2020-09-04 15:19:06'),
(1314, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '4036.7500', 0, 'Interest Repayment', '967', 373, 967, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-21', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:19:37', '2020-09-04 15:19:37'),
(1315, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '4036.7500', NULL, 0, 'Interest Repayment', '967', 373, 967, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-21', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:19:37', '2020-09-04 15:19:37'),
(1316, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '2832.0000', 0, 'Interest Repayment', '968', 395, 968, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:20:29', '2020-09-04 15:20:29'),
(1317, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2832.0000', NULL, 0, 'Interest Repayment', '968', 395, 968, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:20:29', '2020-09-04 15:20:29'),
(1318, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '336.0000', 0, 'Principal Repayment', '969', 395, 969, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-09-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:21:14', '2020-09-04 15:21:14'),
(1319, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '336.0000', NULL, 0, 'Principal Repayment', '969', 395, 969, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-09-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:21:14', '2020-09-04 15:21:14'),
(1320, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '2798.0000', 0, 'Interest Repayment', '970', 395, 970, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:22:05', '2020-09-04 15:22:05'),
(1321, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2798.0000', NULL, 0, 'Interest Repayment', '970', 395, 970, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:22:05', '2020-09-04 15:22:05'),
(1322, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '402.0000', 0, 'Interest Repayment', '971', 395, 971, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:23:09', '2020-09-04 15:23:09'),
(1323, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '402.0000', NULL, 0, 'Interest Repayment', '971', 395, 971, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:23:09', '2020-09-04 15:23:09'),
(1324, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '4036.7500', 0, 'Interest Repayment', '972', 373, 972, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:23:44', '2020-09-04 15:23:44'),
(1325, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '4036.7500', NULL, 0, 'Interest Repayment', '972', 373, 972, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:23:44', '2020-09-04 15:23:44'),
(1326, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '4036.7500', 0, 'Interest Repayment', '973', 373, 973, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:25:26', '2020-09-04 15:25:26'),
(1327, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '4036.7500', NULL, 0, 'Interest Repayment', '973', 373, 973, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:25:26', '2020-09-04 15:25:26'),
(1328, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3150.0000', 0, 'Interest Repayment', '974', 401, 974, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-30', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:25:43', '2020-09-04 15:25:43'),
(1329, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3150.0000', NULL, 0, 'Interest Repayment', '974', 401, 974, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-30', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:25:43', '2020-09-04 15:25:43'),
(1330, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3150.0000', 0, 'Interest Repayment', '975', 401, 975, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-30', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:27:23', '2020-09-04 15:27:23'),
(1331, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3150.0000', NULL, 0, 'Interest Repayment', '975', 401, 975, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-30', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:27:23', '2020-09-04 15:27:23'),
(1332, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '89.7500', 0, 'Interest Repayment', '976', 373, 976, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:27:41', '2020-09-04 15:27:41'),
(1333, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '89.7500', NULL, 0, 'Interest Repayment', '976', 373, 976, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:27:41', '2020-09-04 15:27:41'),
(1334, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3150.0000', 0, 'Interest Repayment', '977', 401, 977, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:28:54', '2020-09-04 15:28:54'),
(1335, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3150.0000', NULL, 0, 'Interest Repayment', '977', 401, 977, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:28:54', '2020-09-04 15:28:54'),
(1336, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3150.0000', 0, 'Interest Repayment', '978', 401, 978, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:29:59', '2020-09-04 15:29:59'),
(1337, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3150.0000', NULL, 0, 'Interest Repayment', '978', 401, 978, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:29:59', '2020-09-04 15:29:59'),
(1338, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '13000.0000', 0, 'Interest Repayment', '979', 377, 979, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-13', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:34:55', '2020-09-04 15:34:55'),
(1339, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '13000.0000', NULL, 0, 'Interest Repayment', '979', 377, 979, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-13', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:34:55', '2020-09-04 15:34:55'),
(1340, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '13000.0000', 0, 'Interest Repayment', '980', 377, 980, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-30', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:37:30', '2020-09-04 15:37:30'),
(1341, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '13000.0000', NULL, 0, 'Interest Repayment', '980', 377, 980, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-30', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:37:30', '2020-09-04 15:37:30'),
(1342, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '13000.0000', 0, 'Interest Repayment', '981', 377, 981, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-30', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:39:15', '2020-09-04 15:39:15'),
(1343, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '13000.0000', NULL, 0, 'Interest Repayment', '981', 377, 981, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-30', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:39:15', '2020-09-04 15:39:15'),
(1344, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '13000.0000', 0, 'Interest Repayment', '982', 377, 982, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:43:17', '2020-09-04 15:43:17'),
(1345, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '13000.0000', NULL, 0, 'Interest Repayment', '982', 377, 982, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:43:17', '2020-09-04 15:43:17'),
(1346, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '13000.0000', 0, 'Interest Repayment', '983', 377, 983, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:45:00', '2020-09-04 15:45:00'),
(1347, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '13000.0000', NULL, 0, 'Interest Repayment', '983', 377, 983, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:45:00', '2020-09-04 15:45:00'),
(1348, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '13000.0000', 0, 'Interest Repayment', '984', 377, 984, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-16', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:47:59', '2020-09-04 15:47:59'),
(1349, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '13000.0000', NULL, 0, 'Interest Repayment', '984', 377, 984, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-16', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:47:59', '2020-09-04 15:47:59'),
(1350, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '11000.0000', 0, 'Interest Repayment', '985', 377, 985, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-16', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:49:57', '2020-09-04 15:49:57'),
(1351, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '11000.0000', NULL, 0, 'Interest Repayment', '985', 377, 985, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-16', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:49:57', '2020-09-04 15:49:57'),
(1352, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '5507.5800', 0, 'Interest Repayment', '986', 381, 986, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-09', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:57:16', '2020-09-04 15:57:16'),
(1353, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '5507.5800', NULL, 0, 'Interest Repayment', '986', 381, 986, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-09', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:57:16', '2020-09-04 15:57:16'),
(1354, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '5507.5800', 0, 'Interest Repayment', '987', 381, 987, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:59:13', '2020-09-04 15:59:13'),
(1355, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '5507.5800', NULL, 0, 'Interest Repayment', '987', 381, 987, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 15:59:13', '2020-09-04 15:59:13'),
(1356, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '5507.5800', 0, 'Interest Repayment', '988', 381, 988, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:01:22', '2020-09-04 16:01:22'),
(1357, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '5507.5800', NULL, 0, 'Interest Repayment', '988', 381, 988, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:01:22', '2020-09-04 16:01:22'),
(1358, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '5507.5800', 0, 'Interest Repayment', '989', 381, 989, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-12', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:04:00', '2020-09-04 16:04:00'),
(1359, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '5507.5800', NULL, 0, 'Interest Repayment', '989', 381, 989, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-12', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:04:00', '2020-09-04 16:04:00'),
(1360, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '5507.5800', 0, 'Interest Repayment', '990', 381, 990, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:06:05', '2020-09-04 16:06:05'),
(1361, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '5507.5800', NULL, 0, 'Interest Repayment', '990', 381, 990, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:06:05', '2020-09-04 16:06:05'),
(1362, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '5507.5800', 0, 'Interest Repayment', '991', 381, 991, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:10:42', '2020-09-04 16:10:42'),
(1363, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '5507.5800', NULL, 0, 'Interest Repayment', '991', 381, 991, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:10:42', '2020-09-04 16:10:42'),
(1364, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '5507.5800', 0, 'Interest Repayment', '992', 381, 992, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:15:09', '2020-09-04 16:15:09'),
(1365, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '5507.5800', NULL, 0, 'Interest Repayment', '992', 381, 992, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:15:09', '2020-09-04 16:15:09'),
(1366, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '5507.5800', 0, 'Interest Repayment', '993', 381, 993, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:16:16', '2020-09-04 16:16:16'),
(1367, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '5507.5800', NULL, 0, 'Interest Repayment', '993', 381, 993, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:16:16', '2020-09-04 16:16:16'),
(1368, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '3431.7800', 0, 'Principal Repayment', '994', 381, 994, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-21', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:21:04', '2020-09-04 16:21:04'),
(1369, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3431.7800', NULL, 0, 'Principal Repayment', '994', 381, 994, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-21', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:21:04', '2020-09-04 16:21:04'),
(1370, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '0.0600', 0, 'Interest Repayment', '995', 381, 995, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:24:00', '2020-09-04 16:24:00'),
(1371, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '0.0600', NULL, 0, 'Interest Repayment', '995', 381, 995, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:24:00', '2020-09-04 16:24:00'),
(1372, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '9835.6000', 0, 'Principal Repayment', '996', 381, 996, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-21', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:26:15', '2020-09-04 16:26:15'),
(1373, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '9835.6000', NULL, 0, 'Principal Repayment', '996', 381, 996, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-21', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:26:15', '2020-09-04 16:26:15'),
(1374, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '5819.1600', 0, 'Principal Repayment', '998', 381, 998, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:30:38', '2020-09-04 16:30:38'),
(1375, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '5819.1600', NULL, 0, 'Principal Repayment', '998', 381, 998, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:30:38', '2020-09-04 16:30:38'),
(1376, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '10000.0000', 0, 'Principal Repayment', '1000', 381, 1000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-15', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:34:24', '2020-09-04 16:34:24'),
(1377, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '10000.0000', NULL, 0, 'Principal Repayment', '1000', 381, 1000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-15', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:34:24', '2020-09-04 16:34:24'),
(1378, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '4802.1400', 0, 'Principal Repayment', '1002', 381, 1002, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-17', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:39:04', '2020-09-04 16:39:04'),
(1379, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '4802.1400', NULL, 0, 'Principal Repayment', '1002', 381, 1002, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-17', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:39:04', '2020-09-04 16:39:04'),
(1380, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '2881.2900', 0, 'Principal Repayment', '1004', 381, 1004, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-11', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:42:07', '2020-09-04 16:42:07'),
(1381, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2881.2900', NULL, 0, 'Principal Repayment', '1004', 381, 1004, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-11', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 16:42:07', '2020-09-04 16:42:07'),
(1382, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '5000.0000', 0, 'Interest Repayment', '1006', 376, 1006, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:01:01', '2020-09-04 17:01:01'),
(1383, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '5000.0000', NULL, 0, 'Interest Repayment', '1006', 376, 1006, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:01:01', '2020-09-04 17:01:01'),
(1384, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '6000.0000', 0, 'Interest Repayment', '1007', 376, 1007, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-22', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:03:02', '2020-09-04 17:03:02'),
(1385, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '6000.0000', NULL, 0, 'Interest Repayment', '1007', 376, 1007, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-22', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:03:02', '2020-09-04 17:03:02'),
(1386, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '6000.0000', 0, 'Interest Repayment', '1008', 376, 1008, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-22', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:04:11', '2020-09-04 17:04:11'),
(1387, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '6000.0000', NULL, 0, 'Interest Repayment', '1008', 376, 1008, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-22', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:04:11', '2020-09-04 17:04:11'),
(1388, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '6000.0000', 0, 'Interest Repayment', '1009', 376, 1009, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-09-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:06:06', '2020-09-04 17:06:06'),
(1389, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '6000.0000', NULL, 0, 'Interest Repayment', '1009', 376, 1009, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-09-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:06:06', '2020-09-04 17:06:06'),
(1390, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '6000.0000', 0, 'Interest Repayment', '1010', 376, 1010, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-22', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:07:24', '2020-09-04 17:07:24'),
(1391, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '6000.0000', NULL, 0, 'Interest Repayment', '1010', 376, 1010, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-22', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:07:24', '2020-09-04 17:07:24'),
(1392, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '6000.0000', 0, 'Interest Repayment', '1011', 376, 1011, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-22', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:09:29', '2020-09-04 17:09:29'),
(1393, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '6000.0000', NULL, 0, 'Interest Repayment', '1011', 376, 1011, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-22', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:09:29', '2020-09-04 17:09:29'),
(1394, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '2999.5700', 0, 'Interest Repayment', '1012', 376, 1012, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-22', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:10:47', '2020-09-04 17:10:47'),
(1395, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2999.5700', NULL, 0, 'Interest Repayment', '1012', 376, 1012, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-22', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:10:47', '2020-09-04 17:10:47'),
(1396, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '30000.0000', 0, 'Interest Repayment', '1013', 405, 1013, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:18:48', '2020-09-04 17:18:48'),
(1397, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '30000.0000', NULL, 0, 'Interest Repayment', '1013', 405, 1013, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:18:48', '2020-09-04 17:18:48'),
(1398, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3500.0000', 0, 'Interest Repayment', '1014', 375, 1014, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-05', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:19:08', '2020-09-04 17:19:08'),
(1399, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3500.0000', NULL, 0, 'Interest Repayment', '1014', 375, 1014, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-05', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:19:08', '2020-09-04 17:19:08'),
(1400, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3500.0000', 0, 'Interest Repayment', '1015', 375, 1015, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:21:38', '2020-09-04 17:21:38'),
(1401, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3500.0000', NULL, 0, 'Interest Repayment', '1015', 375, 1015, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:21:38', '2020-09-04 17:21:38'),
(1402, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '8500.0000', 0, 'Principal Repayment', '1016', 375, 1016, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:22:30', '2020-09-04 17:22:30'),
(1403, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '8500.0000', NULL, 0, 'Principal Repayment', '1016', 375, 1016, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:22:30', '2020-09-04 17:22:30'),
(1404, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '5000.0000', 0, 'Principal Repayment', '1017', 388, 1017, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:23:17', '2020-09-04 17:23:17'),
(1405, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '5000.0000', NULL, 0, 'Principal Repayment', '1017', 388, 1017, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:23:17', '2020-09-04 17:23:17'),
(1406, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '25000.0000', 0, 'Interest Repayment', '1018', 388, 1018, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:24:56', '2020-09-04 17:24:56'),
(1407, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '25000.0000', NULL, 0, 'Interest Repayment', '1018', 388, 1018, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:24:56', '2020-09-04 17:24:56'),
(1408, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3075.0000', 0, 'Interest Repayment', '1019', 375, 1019, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:26:43', '2020-09-04 17:26:43'),
(1409, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3075.0000', NULL, 0, 'Interest Repayment', '1019', 375, 1019, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:26:43', '2020-09-04 17:26:43'),
(1410, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '400.0000', 0, 'Principal Repayment', '1020', 388, 1020, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-07-11', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:27:01', '2020-09-04 17:27:01'),
(1411, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '400.0000', NULL, 0, 'Principal Repayment', '1020', 388, 1020, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-07-11', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:27:01', '2020-09-04 17:27:01'),
(1412, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '11925.0000', 0, 'Principal Repayment', '1021', 375, 1021, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:27:43', '2020-09-04 17:27:43'),
(1413, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '11925.0000', NULL, 0, 'Principal Repayment', '1021', 375, 1021, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:27:43', '2020-09-04 17:27:43'),
(1414, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3600.0000', 0, 'Interest Repayment', '1022', 388, 1022, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-07-11', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:29:18', '2020-09-04 17:29:18'),
(1415, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3600.0000', NULL, 0, 'Interest Repayment', '1022', 388, 1022, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-07-11', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:29:18', '2020-09-04 17:29:18'),
(1416, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '2478.5000', 0, 'Interest Repayment', '1023', 375, 1023, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:29:30', '2020-09-04 17:29:30'),
(1417, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2478.5000', NULL, 0, 'Interest Repayment', '1023', 375, 1023, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:29:30', '2020-09-04 17:29:30'),
(1418, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '10021.2500', 0, 'Principal Repayment', '1024', 375, 1024, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:30:24', '2020-09-04 17:30:24'),
(1419, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '10021.2500', NULL, 0, 'Principal Repayment', '1024', 375, 1024, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:30:24', '2020-09-04 17:30:24'),
(1420, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '1432.0000', 0, 'Principal Repayment', '1025', 388, 1025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:32:23', '2020-09-04 17:32:23'),
(1421, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1432.0000', NULL, 0, 'Principal Repayment', '1025', 388, 1025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:32:23', '2020-09-04 17:32:23'),
(1422, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '2977.6000', 0, 'Interest Repayment', '1026', 375, 1026, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:33:19', '2020-09-04 17:33:19');
INSERT INTO `gl_journal_entries` (`id`, `office_id`, `gl_account_id`, `currency_id`, `account_type`, `transaction_type`, `transaction_sub_type`, `op_balance_dr`, `op_balance_cr`, `debit`, `credit`, `reversed`, `name`, `reference`, `loan_id`, `loan_transaction_id`, `savings_transaction_id`, `savings_id`, `shares_transaction_id`, `payroll_transaction_id`, `payment_detail_id`, `transaction_id`, `gl_closure_id`, `date`, `month`, `year`, `notes`, `created_by_id`, `modified_by_id`, `reconciled`, `manual_entry`, `approved`, `approved_by_id`, `approved_date`, `approved_notes`, `created_at`, `updated_at`) VALUES
(1423, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2977.6000', NULL, 0, 'Interest Repayment', '1026', 375, 1026, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:33:19', '2020-09-04 17:33:19'),
(1424, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3568.0000', 0, 'Interest Repayment', '1027', 388, 1027, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:34:13', '2020-09-04 17:34:13'),
(1425, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3568.0000', NULL, 0, 'Interest Repayment', '1027', 388, 1027, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:34:13', '2020-09-04 17:34:13'),
(1426, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '10022.4000', 0, 'Principal Repayment', '1028', 375, 1028, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:34:27', '2020-09-04 17:34:27'),
(1427, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '10022.4000', NULL, 0, 'Principal Repayment', '1028', 375, 1028, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:34:27', '2020-09-04 17:34:27'),
(1428, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '4476.4300', 0, 'Interest Repayment', '1029', 375, 1029, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:35:45', '2020-09-04 17:35:45'),
(1429, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '4476.4300', NULL, 0, 'Interest Repayment', '1029', 375, 1029, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:35:45', '2020-09-04 17:35:45'),
(1430, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '22046.5600', 0, 'Principal Repayment', '1030', 388, 1030, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-19', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:36:20', '2020-09-04 17:36:20'),
(1431, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '22046.5600', NULL, 0, 'Principal Repayment', '1030', 388, 1030, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-19', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:36:20', '2020-09-04 17:36:20'),
(1432, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '10523.4700', 0, 'Principal Repayment', '1031', 375, 1031, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:36:40', '2020-09-04 17:36:40'),
(1433, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '10523.4700', NULL, 0, 'Principal Repayment', '1031', 375, 1031, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:36:40', '2020-09-04 17:36:40'),
(1434, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3950.3600', 0, 'Interest Repayment', '1032', 375, 1032, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:38:21', '2020-09-04 17:38:21'),
(1435, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3950.3600', NULL, 0, 'Interest Repayment', '1032', 375, 1032, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:38:21', '2020-09-04 17:38:21'),
(1436, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3453.4400', 0, 'Interest Repayment', '1033', 388, 1033, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-19', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:38:28', '2020-09-04 17:38:28'),
(1437, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3453.4400', NULL, 0, 'Interest Repayment', '1033', 388, 1033, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-19', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:38:28', '2020-09-04 17:38:28'),
(1438, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '11049.6400', 0, 'Principal Repayment', '1034', 375, 1034, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:39:43', '2020-09-04 17:39:43'),
(1439, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '11049.6400', NULL, 0, 'Principal Repayment', '1034', 375, 1034, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:39:43', '2020-09-04 17:39:43'),
(1440, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '4042.2200', 0, 'Interest Repayment', '1035', 375, 1035, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:41:34', '2020-09-04 17:41:34'),
(1441, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '4042.2200', NULL, 0, 'Interest Repayment', '1035', 375, 1035, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:41:34', '2020-09-04 17:41:34'),
(1442, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '6348.4800', 0, 'Principal Repayment', '1036', 388, 1036, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-17', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:41:58', '2020-09-04 17:41:58'),
(1443, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '6348.4800', NULL, 0, 'Principal Repayment', '1036', 388, 1036, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-17', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:41:58', '2020-09-04 17:41:58'),
(1444, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '26814.4700', 0, 'Principal Repayment', '1037', 375, 1037, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:42:54', '2020-09-04 17:42:54'),
(1445, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '26814.4700', NULL, 0, 'Principal Repayment', '1037', 375, 1037, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:42:54', '2020-09-04 17:42:54'),
(1446, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '4378.9900', 0, 'Interest Repayment', '1038', 388, 1038, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-17', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:44:04', '2020-09-04 17:44:04'),
(1447, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '4378.9900', NULL, 0, 'Interest Repayment', '1038', 388, 1038, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-17', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:44:04', '2020-09-04 17:44:04'),
(1448, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '38.0000', 0, 'Principal Repayment', '1039', 388, 1039, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-23', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:47:33', '2020-09-04 17:47:33'),
(1449, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '38.0000', NULL, 0, 'Principal Repayment', '1039', 388, 1039, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-23', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:47:33', '2020-09-04 17:47:33'),
(1450, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7200.0000', 0, 'Interest Repayment', '1041', 378, 1041, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-06-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:49:16', '2020-09-04 17:49:16'),
(1451, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7200.0000', NULL, 0, 'Interest Repayment', '1041', 378, 1041, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-06-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:49:16', '2020-09-04 17:49:16'),
(1452, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7200.0000', 0, 'Interest Repayment', '1043', 378, 1043, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-11', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:53:29', '2020-09-04 17:53:29'),
(1453, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7200.0000', NULL, 0, 'Interest Repayment', '1043', 378, 1043, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-11', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:53:29', '2020-09-04 17:53:29'),
(1454, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '4920.0000', 0, 'Interest Repayment', '1045', 378, 1045, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-24', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:59:14', '2020-09-04 17:59:14'),
(1455, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '4920.0000', NULL, 0, 'Interest Repayment', '1045', 378, 1045, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-24', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 17:59:14', '2020-09-04 17:59:14'),
(1456, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '4920.0000', 0, 'Interest Repayment', '1047', 378, 1047, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-24', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:01:09', '2020-09-04 18:01:09'),
(1457, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '4920.0000', NULL, 0, 'Interest Repayment', '1047', 378, 1047, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-24', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:01:09', '2020-09-04 18:01:09'),
(1458, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '15160.0000', 0, 'Principal Repayment', '1048', 378, 1048, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-24', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:02:42', '2020-09-04 18:02:42'),
(1459, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '15160.0000', NULL, 0, 'Principal Repayment', '1048', 378, 1048, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-24', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:02:42', '2020-09-04 18:02:42'),
(1460, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '84.1400', 0, 'Principal Repayment', '1049', 388, 1049, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-25', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:04:01', '2020-09-04 18:04:01'),
(1461, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '84.1400', NULL, 0, 'Principal Repayment', '1049', 388, 1049, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-25', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:04:01', '2020-09-04 18:04:01'),
(1462, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3404.0000', 0, 'Interest Repayment', '1050', 378, 1050, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-15', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:04:52', '2020-09-04 18:04:52'),
(1463, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3404.0000', NULL, 0, 'Interest Repayment', '1050', 378, 1050, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-15', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:04:52', '2020-09-04 18:04:52'),
(1464, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '16596.0000', 0, 'Principal Repayment', '1052', 378, 1052, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-15', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:06:35', '2020-09-04 18:06:35'),
(1465, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '16596.0000', NULL, 0, 'Principal Repayment', '1052', 378, 1052, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-15', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:06:35', '2020-09-04 18:06:35'),
(1466, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '50.4900', 0, 'Principal Repayment', '1053', 388, 1053, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-25', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:08:17', '2020-09-04 18:08:17'),
(1467, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '50.4900', NULL, 0, 'Principal Repayment', '1053', 388, 1053, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-25', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:08:17', '2020-09-04 18:08:17'),
(1468, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '10500.0000', 0, 'Interest Repayment', '1055', 403, 1055, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-17', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:16:48', '2020-09-04 18:16:48'),
(1469, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '10500.0000', NULL, 0, 'Interest Repayment', '1055', 403, 1055, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-17', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:16:48', '2020-09-04 18:16:48'),
(1470, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '10500.0000', 0, 'Interest Repayment', '1056', 403, 1056, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-18', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:18:47', '2020-09-04 18:18:47'),
(1471, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '10500.0000', NULL, 0, 'Interest Repayment', '1056', 403, 1056, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-18', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:18:47', '2020-09-04 18:18:47'),
(1472, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1744.0000', 0, 'Interest Repayment', '1057', 378, 1057, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:20:10', '2020-09-04 18:20:10'),
(1473, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1744.0000', NULL, 0, 'Interest Repayment', '1057', 378, 1057, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:20:10', '2020-09-04 18:20:10'),
(1474, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '10500.0000', 0, 'Interest Repayment', '1058', 403, 1058, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-18', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:20:41', '2020-09-04 18:20:41'),
(1475, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '10500.0000', NULL, 0, 'Interest Repayment', '1058', 403, 1058, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-18', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:20:41', '2020-09-04 18:20:41'),
(1476, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '15699.6000', 0, 'Principal Repayment', '1059', 378, 1059, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:21:15', '2020-09-04 18:21:15'),
(1477, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '15699.6000', NULL, 0, 'Principal Repayment', '1059', 378, 1059, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:21:15', '2020-09-04 18:21:15'),
(1478, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '10500.0000', 0, 'Interest Repayment', '1060', 403, 1060, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-18', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:22:13', '2020-09-04 18:22:13'),
(1479, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '10500.0000', NULL, 0, 'Interest Repayment', '1060', 403, 1060, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-18', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:22:13', '2020-09-04 18:22:13'),
(1480, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '46.5000', 0, 'Interest Repayment', '1061', 378, 1061, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-14', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:23:17', '2020-09-04 18:23:17'),
(1481, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '46.5000', NULL, 0, 'Interest Repayment', '1061', 378, 1061, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-14', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:23:17', '2020-09-04 18:23:17'),
(1482, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '10500.0000', 0, 'Interest Repayment', '1062', 403, 1062, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-18', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:23:40', '2020-09-04 18:23:40'),
(1483, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '10500.0000', NULL, 0, 'Interest Repayment', '1062', 403, 1062, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-18', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:23:40', '2020-09-04 18:23:40'),
(1484, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '1744.0000', 0, 'Principal Repayment', '1063', 378, 1063, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-14', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:24:38', '2020-09-04 18:24:38'),
(1485, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1744.0000', NULL, 0, 'Principal Repayment', '1063', 378, 1063, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-14', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:24:38', '2020-09-04 18:24:38'),
(1486, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '10500.0000', 0, 'Interest Repayment', '1064', 403, 1064, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-18', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:25:00', '2020-09-04 18:25:00'),
(1487, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '10500.0000', NULL, 0, 'Interest Repayment', '1064', 403, 1064, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-18', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:25:00', '2020-09-04 18:25:00'),
(1488, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '10500.0000', 0, 'Interest Repayment', '1065', 403, 1065, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-18', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:27:24', '2020-09-04 18:27:24'),
(1489, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '10500.0000', NULL, 0, 'Interest Repayment', '1065', 403, 1065, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-18', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:27:24', '2020-09-04 18:27:24'),
(1490, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '10500.0000', 0, 'Interest Repayment', '1066', 403, 1066, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-18', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:29:11', '2020-09-04 18:29:11'),
(1491, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '10500.0000', NULL, 0, 'Interest Repayment', '1066', 403, 1066, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-18', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:29:11', '2020-09-04 18:29:11'),
(1492, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '0.3300', 0, 'Interest Repayment', '1067', 403, 1067, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-18', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:32:06', '2020-09-04 18:32:06'),
(1493, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '0.3300', NULL, 0, 'Interest Repayment', '1067', 403, 1067, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-18', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:32:06', '2020-09-04 18:32:06'),
(1494, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '12000.0000', 0, 'Interest Repayment', '1076', 396, 1076, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-24', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:41:28', '2020-09-04 18:41:28'),
(1495, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '12000.0000', NULL, 0, 'Interest Repayment', '1076', 396, 1076, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-24', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:41:28', '2020-09-04 18:41:28'),
(1496, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '12000.0000', 0, 'Interest Repayment', '1077', 396, 1077, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-06-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:44:03', '2020-09-04 18:44:03'),
(1497, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '12000.0000', NULL, 0, 'Interest Repayment', '1077', 396, 1077, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-06-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:44:03', '2020-09-04 18:44:03'),
(1498, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '12000.0000', 0, 'Interest Repayment', '1078', 396, 1078, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-07-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:47:59', '2020-09-04 18:47:59'),
(1499, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '12000.0000', NULL, 0, 'Interest Repayment', '1078', 396, 1078, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-07-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:47:59', '2020-09-04 18:47:59'),
(1500, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '12000.0000', 0, 'Interest Repayment', '1079', 396, 1079, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:50:45', '2020-09-04 18:50:45'),
(1501, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '12000.0000', NULL, 0, 'Interest Repayment', '1079', 396, 1079, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:50:45', '2020-09-04 18:50:45'),
(1502, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '12000.0000', 0, 'Interest Repayment', '1080', 396, 1080, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:52:36', '2020-09-04 18:52:36'),
(1503, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '12000.0000', NULL, 0, 'Interest Repayment', '1080', 396, 1080, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:52:36', '2020-09-04 18:52:36'),
(1504, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '3000.0000', 0, 'Principal Repayment', '1081', 396, 1081, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:53:45', '2020-09-04 18:53:45'),
(1505, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3000.0000', NULL, 0, 'Principal Repayment', '1081', 396, 1081, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:53:45', '2020-09-04 18:53:45'),
(1506, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, '11760.0000', '11760.0000', 1, 'Interest Repayment', '1082', 396, 1082, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:54:54', '2020-09-04 19:51:29'),
(1507, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '11760.0000', '11760.0000', 1, 'Interest Repayment', '1082', 396, 1082, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:54:54', '2020-09-04 19:51:29'),
(1508, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, '3240.0000', '3240.0000', 1, 'Principal Repayment', '1083', 396, 1083, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:55:56', '2020-09-04 19:52:38'),
(1509, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3240.0000', '3240.0000', 1, 'Principal Repayment', '1083', 396, 1083, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:55:56', '2020-09-04 19:52:38'),
(1510, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, '11500.0000', '11500.0000', 1, 'Interest Repayment', '1084', 396, 1084, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-31', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:57:24', '2020-09-04 19:53:52'),
(1511, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '11500.0000', '11500.0000', 1, 'Interest Repayment', '1084', 396, 1084, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-31', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:57:24', '2020-09-04 19:53:52'),
(1512, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, '3499.2000', '3499.2000', 1, 'Interest Repayment', '1085', 396, 1085, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-31', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:59:46', '2020-09-04 19:54:56'),
(1513, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3499.2000', '3499.2000', 1, 'Interest Repayment', '1085', 396, 1085, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-31', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 18:59:46', '2020-09-04 19:54:56'),
(1514, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, '11545.5700', '11545.5700', 1, 'Interest Repayment', '1086', 396, 1086, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 19:01:16', '2020-09-04 19:57:00'),
(1515, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '11545.5700', '11545.5700', 1, 'Interest Repayment', '1086', 396, 1086, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 19:01:16', '2020-09-04 19:57:00'),
(1516, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '3454.4300', 0, 'Principal Repayment', '1087', 396, 1087, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 19:06:57', '2020-09-04 19:06:57'),
(1517, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3454.4300', NULL, 0, 'Principal Repayment', '1087', 396, 1087, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 19:06:57', '2020-09-04 19:06:57'),
(1518, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3462.3700', 0, 'Interest Repayment', '1088', 396, 1088, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-18', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 19:08:23', '2020-09-04 19:08:23'),
(1519, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3462.3700', NULL, 0, 'Interest Repayment', '1088', 396, 1088, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-18', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 19:08:23', '2020-09-04 19:08:23'),
(1520, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '1055.4900', 0, 'Principal Repayment', '1089', 396, 1089, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-18', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 19:09:37', '2020-09-04 19:09:37'),
(1521, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1055.4900', NULL, 0, 'Principal Repayment', '1089', 396, 1089, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-18', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 19:09:37', '2020-09-04 19:09:37'),
(1522, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '12000.0000', 0, 'Interest Repayment', '1090', 396, 1090, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-05-25', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 19:41:47', '2020-09-04 19:41:47'),
(1523, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '12000.0000', NULL, 0, 'Interest Repayment', '1090', 396, 1090, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-05-25', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 19:41:47', '2020-09-04 19:41:47'),
(1524, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '11760.0000', 0, 'Interest Repayment', '1091', 396, 1091, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 19:51:30', '2020-09-04 19:51:30'),
(1525, 1, 4, 37, NULL, 'repayment', NULL, NULL, NULL, '11760.0000', NULL, 0, 'Interest Repayment', '1091', 396, 1091, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 19:51:30', '2020-09-04 19:51:30'),
(1526, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '3240.0000', 0, 'Principal Repayment', '1092', 396, 1092, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 19:52:38', '2020-09-04 19:52:38'),
(1527, 1, 4, 37, NULL, 'repayment', NULL, NULL, NULL, '3240.0000', NULL, 0, 'Principal Repayment', '1092', 396, 1092, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 19:52:38', '2020-09-04 19:52:38'),
(1528, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '11500.0000', 0, 'Interest Repayment', '1093', 396, 1093, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 19:53:52', '2020-09-04 19:53:52'),
(1529, 1, 4, 37, NULL, 'repayment', NULL, NULL, NULL, '11500.0000', NULL, 0, 'Interest Repayment', '1093', 396, 1093, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 19:53:52', '2020-09-04 19:53:52'),
(1530, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3499.2000', 0, 'Interest Repayment', '1094', 396, 1094, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 19:54:56', '2020-09-04 19:54:56'),
(1531, 1, 4, 37, NULL, 'repayment', NULL, NULL, NULL, '3499.2000', NULL, 0, 'Interest Repayment', '1094', 396, 1094, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 19:54:56', '2020-09-04 19:54:56'),
(1532, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3007.9400', 0, 'Interest Repayment', '1095', 396, 1095, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-31', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 19:57:00', '2020-09-04 19:57:00'),
(1533, 1, 4, 37, NULL, 'repayment', NULL, NULL, NULL, '3007.9400', NULL, 0, 'Interest Repayment', '1095', 396, 1095, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-31', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 19:57:00', '2020-09-04 19:57:00'),
(1534, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3932.5000', 0, 'Interest Repayment', '1096', 406, 1096, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:27:17', '2020-09-04 21:27:17'),
(1535, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3932.5000', NULL, 0, 'Interest Repayment', '1096', 406, 1096, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:27:17', '2020-09-04 21:27:17'),
(1536, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '4067.5000', 0, 'Principal Repayment', '1097', 406, 1097, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:29:09', '2020-09-04 21:29:09'),
(1537, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '4067.5000', NULL, 0, 'Principal Repayment', '1097', 406, 1097, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:29:09', '2020-09-04 21:29:09'),
(1538, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3525.7500', 0, 'Interest Repayment', '1098', 406, 1098, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-09', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:30:39', '2020-09-04 21:30:39'),
(1539, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3525.7500', NULL, 0, 'Interest Repayment', '1098', 406, 1098, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-09', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:30:39', '2020-09-04 21:30:39'),
(1540, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3525.7500', 0, 'Interest Repayment', '1099', 406, 1099, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:32:53', '2020-09-04 21:32:53'),
(1541, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3525.7500', NULL, 0, 'Interest Repayment', '1099', 406, 1099, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:32:53', '2020-09-04 21:32:53'),
(1542, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3525.7500', 0, 'Interest Repayment', '1100', 406, 1100, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:35:15', '2020-09-04 21:35:15'),
(1543, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3525.7500', NULL, 0, 'Interest Repayment', '1100', 406, 1100, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:35:15', '2020-09-04 21:35:15'),
(1544, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3525.0000', 0, 'Interest Repayment', '1101', 406, 1101, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:36:23', '2020-09-04 21:36:23'),
(1545, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3525.0000', NULL, 0, 'Interest Repayment', '1101', 406, 1101, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:36:23', '2020-09-04 21:36:23'),
(1546, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3525.7500', 0, 'Interest Repayment', '1102', 406, 1102, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:37:41', '2020-09-04 21:37:41'),
(1547, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3525.7500', NULL, 0, 'Interest Repayment', '1102', 406, 1102, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:37:41', '2020-09-04 21:37:41'),
(1548, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '71.2400', 0, 'Principal Repayment', '1103', 406, 1103, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:39:03', '2020-09-04 21:39:03'),
(1549, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '71.2400', NULL, 0, 'Principal Repayment', '1103', 406, 1103, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-02', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:39:03', '2020-09-04 21:39:03'),
(1550, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3518.6000', 0, 'Interest Repayment', '1104', 406, 1104, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:40:10', '2020-09-04 21:40:10'),
(1551, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3518.6000', NULL, 0, 'Interest Repayment', '1104', 406, 1104, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:40:10', '2020-09-04 21:40:10'),
(1552, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '1481.4000', 0, 'Principal Repayment', '1105', 406, 1105, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:41:26', '2020-09-04 21:41:26'),
(1553, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1481.4000', NULL, 0, 'Principal Repayment', '1105', 406, 1105, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:41:26', '2020-09-04 21:41:26'),
(1554, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3370.4900', 0, 'Interest Repayment', '1106', 406, 1106, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:42:44', '2020-09-04 21:42:44'),
(1555, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3370.4900', NULL, 0, 'Interest Repayment', '1106', 406, 1106, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:42:44', '2020-09-04 21:42:44'),
(1556, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '2129.5100', 0, 'Principal Repayment', '1107', 406, 1107, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:43:45', '2020-09-04 21:43:45'),
(1557, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2129.5100', NULL, 0, 'Principal Repayment', '1107', 406, 1107, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:43:45', '2020-09-04 21:43:45'),
(1558, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3010.4800', 0, 'Interest Repayment', '1108', 406, 1108, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:45:06', '2020-09-04 21:45:06'),
(1559, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3010.4800', NULL, 0, 'Interest Repayment', '1108', 406, 1108, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:45:06', '2020-09-04 21:45:06'),
(1560, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '392.4700', 0, 'Principal Repayment', '1109', 406, 1109, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:47:53', '2020-09-04 21:47:53'),
(1561, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '392.4700', NULL, 0, 'Principal Repayment', '1109', 406, 1109, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:47:53', '2020-09-04 21:47:53'),
(1562, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '381.7100', 0, 'Principal Repayment', '1111', 406, 1111, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-30', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:50:02', '2020-09-04 21:50:02'),
(1563, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '381.7100', NULL, 0, 'Principal Repayment', '1111', 406, 1111, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-30', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:50:02', '2020-09-04 21:50:02'),
(1564, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1900.0000', 0, 'Interest Repayment', '1112', 394, 1112, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:57:43', '2020-09-04 21:57:43'),
(1565, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1900.0000', NULL, 0, 'Interest Repayment', '1112', 394, 1112, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 21:57:43', '2020-09-04 21:57:43'),
(1566, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '1100.0000', 0, 'Principal Repayment', '1113', 394, 1113, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:00:13', '2020-09-04 22:00:13'),
(1567, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1100.0000', NULL, 0, 'Principal Repayment', '1113', 394, 1113, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:00:13', '2020-09-04 22:00:13'),
(1568, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1790.0000', 0, 'Interest Repayment', '1114', 394, 1114, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:01:38', '2020-09-04 22:01:38'),
(1569, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1790.0000', NULL, 0, 'Interest Repayment', '1114', 394, 1114, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:01:38', '2020-09-04 22:01:38'),
(1570, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '108.0100', 0, 'Principal Repayment', '1115', 394, 1115, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:04:10', '2020-09-04 22:04:10'),
(1571, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '108.0100', NULL, 0, 'Principal Repayment', '1115', 394, 1115, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:04:10', '2020-09-04 22:04:10'),
(1572, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1779.9900', 0, 'Interest Repayment', '1116', 394, 1116, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-22', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:05:17', '2020-09-04 22:05:17'),
(1573, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1779.9900', NULL, 0, 'Interest Repayment', '1116', 394, 1116, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-22', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:05:17', '2020-09-04 22:05:17'),
(1574, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '118.8100', 0, 'Principal Repayment', '1117', 394, 1117, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-22', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:07:47', '2020-09-04 22:07:47'),
(1575, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '118.8100', NULL, 0, 'Principal Repayment', '1117', 394, 1117, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-22', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:07:47', '2020-09-04 22:07:47'),
(1576, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1767.3200', 0, 'Interest Repayment', '1118', 394, 1118, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-12', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:09:33', '2020-09-04 22:09:33'),
(1577, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1767.3200', NULL, 0, 'Interest Repayment', '1118', 394, 1118, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-12', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:09:33', '2020-09-04 22:09:33'),
(1578, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '125.6900', 0, 'Principal Repayment', '1119', 394, 1119, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-12', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:10:40', '2020-09-04 22:10:40'),
(1579, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '125.6900', NULL, 0, 'Principal Repayment', '1119', 394, 1119, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-12', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:10:40', '2020-09-04 22:10:40'),
(1580, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1754.7500', 0, 'Interest Repayment', '1120', 394, 1120, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-08', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:12:33', '2020-09-04 22:12:33'),
(1581, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1754.7500', NULL, 0, 'Interest Repayment', '1120', 394, 1120, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-08', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:12:33', '2020-09-04 22:12:33'),
(1582, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1754.7500', 0, 'Interest Repayment', '1121', 394, 1121, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:13:45', '2020-09-04 22:13:45'),
(1583, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1754.7500', NULL, 0, 'Interest Repayment', '1121', 394, 1121, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:13:45', '2020-09-04 22:13:45'),
(1584, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1754.7500', 0, 'Interest Repayment', '1122', 394, 1122, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-16', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:15:52', '2020-09-04 22:15:52'),
(1585, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1754.7500', NULL, 0, 'Interest Repayment', '1122', 394, 1122, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-16', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:15:52', '2020-09-04 22:15:52'),
(1586, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1754.7500', 0, 'Interest Repayment', '1123', 394, 1123, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-16', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:17:05', '2020-09-04 22:17:05'),
(1587, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1754.7500', NULL, 0, 'Interest Repayment', '1123', 394, 1123, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-16', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:17:05', '2020-09-04 22:17:05'),
(1588, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '45.2500', 0, 'Principal Repayment', '1124', 394, 1124, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-16', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:17:52', '2020-09-04 22:17:52'),
(1589, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '45.2500', NULL, 0, 'Principal Repayment', '1124', 394, 1124, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-16', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:17:52', '2020-09-04 22:17:52'),
(1590, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '943.5900', 0, 'Interest Repayment', '1125', 394, 1125, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-15', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:19:00', '2020-09-04 22:19:00'),
(1591, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '943.5900', NULL, 0, 'Interest Repayment', '1125', 394, 1125, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-15', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:19:00', '2020-09-04 22:19:00'),
(1592, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '49.7800', 0, 'Principal Repayment', '1126', 394, 1126, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-15', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:19:49', '2020-09-04 22:19:49'),
(1593, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '49.7800', NULL, 0, 'Principal Repayment', '1126', 394, 1126, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-15', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:19:49', '2020-09-04 22:19:49'),
(1594, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '54.7500', 0, 'Principal Repayment', '1128', 394, 1128, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-16', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:21:59', '2020-09-04 22:21:59'),
(1595, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '54.7500', NULL, 0, 'Principal Repayment', '1128', 394, 1128, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-16', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:21:59', '2020-09-04 22:21:59'),
(1596, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '6700.0000', 0, 'Interest Repayment', '1129', 390, 1129, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-15', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:47:08', '2020-09-04 22:47:08');
INSERT INTO `gl_journal_entries` (`id`, `office_id`, `gl_account_id`, `currency_id`, `account_type`, `transaction_type`, `transaction_sub_type`, `op_balance_dr`, `op_balance_cr`, `debit`, `credit`, `reversed`, `name`, `reference`, `loan_id`, `loan_transaction_id`, `savings_transaction_id`, `savings_id`, `shares_transaction_id`, `payroll_transaction_id`, `payment_detail_id`, `transaction_id`, `gl_closure_id`, `date`, `month`, `year`, `notes`, `created_by_id`, `modified_by_id`, `reconciled`, `manual_entry`, `approved`, `approved_by_id`, `approved_date`, `approved_notes`, `created_at`, `updated_at`) VALUES
(1597, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '6700.0000', NULL, 0, 'Interest Repayment', '1129', 390, 1129, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-15', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:47:08', '2020-09-04 22:47:08'),
(1598, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '6700.0000', 0, 'Interest Repayment', '1130', 390, 1130, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-15', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:48:02', '2020-09-04 22:48:02'),
(1599, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '6700.0000', NULL, 0, 'Interest Repayment', '1130', 390, 1130, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-15', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:48:02', '2020-09-04 22:48:02'),
(1600, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '6700.0000', 0, 'Interest Repayment', '1131', 390, 1131, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-15', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:50:27', '2020-09-04 22:50:27'),
(1601, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '6700.0000', NULL, 0, 'Interest Repayment', '1131', 390, 1131, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-15', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:50:27', '2020-09-04 22:50:27'),
(1602, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '6600.0000', 0, 'Principal Repayment', '1132', 390, 1132, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-15', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:51:47', '2020-09-04 22:51:47'),
(1603, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '6600.0000', NULL, 0, 'Principal Repayment', '1132', 390, 1132, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-15', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:51:47', '2020-09-04 22:51:47'),
(1604, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '6040.0000', 0, 'Interest Repayment', '1133', 390, 1133, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:53:18', '2020-09-04 22:53:18'),
(1605, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '6040.0000', NULL, 0, 'Interest Repayment', '1133', 390, 1133, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:53:18', '2020-09-04 22:53:18'),
(1606, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '360.0000', 0, 'Principal Repayment', '1134', 390, 1134, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:54:13', '2020-09-04 22:54:13'),
(1607, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '360.0000', NULL, 0, 'Principal Repayment', '1134', 390, 1134, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:54:13', '2020-09-04 22:54:13'),
(1608, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '6040.0000', 0, 'Interest Repayment', '1135', 390, 1135, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:55:09', '2020-09-04 22:55:09'),
(1609, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '6040.0000', NULL, 0, 'Interest Repayment', '1135', 390, 1135, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:55:09', '2020-09-04 22:55:09'),
(1610, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '360.0000', 0, 'Principal Repayment', '1136', 390, 1136, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:56:43', '2020-09-04 22:56:43'),
(1611, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '360.0000', NULL, 0, 'Principal Repayment', '1136', 390, 1136, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:56:43', '2020-09-04 22:56:43'),
(1612, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '5968.0000', 0, 'Interest Repayment', '1137', 390, 1137, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:57:50', '2020-09-04 22:57:50'),
(1613, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '5968.0000', NULL, 0, 'Interest Repayment', '1137', 390, 1137, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:57:50', '2020-09-04 22:57:50'),
(1614, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '1732.0000', 0, 'Principal Repayment', '1138', 390, 1138, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:58:41', '2020-09-04 22:58:41'),
(1615, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1732.0000', NULL, 0, 'Principal Repayment', '1138', 390, 1138, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 22:58:41', '2020-09-04 22:58:41'),
(1616, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '5794.8000', 0, 'Interest Repayment', '1139', 390, 1139, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:01:24', '2020-09-04 23:01:24'),
(1617, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '5794.8000', NULL, 0, 'Interest Repayment', '1139', 390, 1139, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:01:24', '2020-09-04 23:01:24'),
(1618, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '5794.8000', 0, 'Interest Repayment', '1140', 390, 1140, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:02:15', '2020-09-04 23:02:15'),
(1619, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '5794.8000', NULL, 0, 'Interest Repayment', '1140', 390, 1140, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-01', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:02:15', '2020-09-04 23:02:15'),
(1620, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3861.9700', 0, 'Interest Repayment', '1141', 390, 1141, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-30', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:03:02', '2020-09-04 23:03:02'),
(1621, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3861.9700', NULL, 0, 'Interest Repayment', '1141', 390, 1141, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-30', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:03:02', '2020-09-04 23:03:02'),
(1622, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '2400.0000', 0, 'Interest Repayment', '1142', 391, 1142, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:11:13', '2020-09-04 23:11:13'),
(1623, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2400.0000', NULL, 0, 'Interest Repayment', '1142', 391, 1142, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:11:13', '2020-09-04 23:11:13'),
(1624, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '2400.0000', 0, 'Interest Repayment', '1143', 391, 1143, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-18', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:12:19', '2020-09-04 23:12:19'),
(1625, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2400.0000', NULL, 0, 'Interest Repayment', '1143', 391, 1143, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-18', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:12:19', '2020-09-04 23:12:19'),
(1626, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '2400.0000', 0, 'Interest Repayment', '1144', 391, 1144, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-31', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:12:57', '2020-09-04 23:12:57'),
(1627, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2400.0000', NULL, 0, 'Interest Repayment', '1144', 391, 1144, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-31', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:12:57', '2020-09-04 23:12:57'),
(1628, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '2400.0000', 0, 'Interest Repayment', '1145', 391, 1145, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-13', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:14:03', '2020-09-04 23:14:03'),
(1629, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2400.0000', NULL, 0, 'Interest Repayment', '1145', 391, 1145, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-13', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:14:03', '2020-09-04 23:14:03'),
(1630, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '600.0000', 0, 'Principal Repayment', '1146', 391, 1146, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-13', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:15:49', '2020-09-04 23:15:49'),
(1631, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '600.0000', NULL, 0, 'Principal Repayment', '1146', 391, 1146, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-13', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:15:49', '2020-09-04 23:15:49'),
(1632, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '2352.0000', 0, 'Interest Repayment', '1147', 391, 1147, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:18:23', '2020-09-04 23:18:23'),
(1633, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2352.0000', NULL, 0, 'Interest Repayment', '1147', 391, 1147, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:18:23', '2020-09-04 23:18:23'),
(1634, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '5000.0000', 0, 'Principal Repayment', '1148', 391, 1148, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:19:32', '2020-09-04 23:19:32'),
(1635, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '5000.0000', NULL, 0, 'Principal Repayment', '1148', 391, 1148, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:19:32', '2020-09-04 23:19:32'),
(1636, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1952.0000', 0, 'Interest Repayment', '1149', 391, 1149, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:20:25', '2020-09-04 23:20:25'),
(1637, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1952.0000', NULL, 0, 'Interest Repayment', '1149', 391, 1149, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:20:25', '2020-09-04 23:20:25'),
(1638, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '3400.0000', 0, 'Principal Repayment', '1150', 391, 1150, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:21:41', '2020-09-04 23:21:41'),
(1639, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3400.0000', NULL, 0, 'Principal Repayment', '1150', 391, 1150, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-04', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:21:41', '2020-09-04 23:21:41'),
(1640, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1680.0000', 0, 'Interest Repayment', '1151', 391, 1151, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-19', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:22:39', '2020-09-04 23:22:39'),
(1641, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1680.0000', NULL, 0, 'Interest Repayment', '1151', 391, 1151, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-19', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:22:39', '2020-09-04 23:22:39'),
(1642, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1680.0000', 0, 'Interest Repayment', '1152', 391, 1152, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-19', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:23:22', '2020-09-04 23:23:22'),
(1643, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1680.0000', NULL, 0, 'Interest Repayment', '1152', 391, 1152, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-19', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:23:22', '2020-09-04 23:23:22'),
(1644, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '320.0000', 0, 'Principal Repayment', '1153', 391, 1153, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-19', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:24:32', '2020-09-04 23:24:32'),
(1645, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '320.0000', NULL, 0, 'Principal Repayment', '1153', 391, 1153, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-19', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:24:32', '2020-09-04 23:24:32'),
(1646, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1654.0000', 0, 'Interest Repayment', '1154', 391, 1154, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-08', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:25:24', '2020-09-04 23:25:24'),
(1647, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1654.0000', NULL, 0, 'Interest Repayment', '1154', 391, 1154, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-08', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:25:24', '2020-09-04 23:25:24'),
(1648, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '282.3400', 0, 'Interest Repayment', '1155', 391, 1155, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-08', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:25:25', '2020-09-04 23:25:25'),
(1649, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '282.3400', NULL, 0, 'Interest Repayment', '1155', 391, 1155, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-08', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:25:25', '2020-09-04 23:25:25'),
(1650, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '679.9900', 0, 'Principal Repayment', '1156', 391, 1156, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-08', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:26:22', '2020-09-04 23:26:22'),
(1651, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '679.9900', NULL, 0, 'Principal Repayment', '1156', 391, 1156, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-08', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:26:22', '2020-09-04 23:26:22'),
(1652, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '745.6000', 0, 'Principal Repayment', '1157', 391, 1157, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-08', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:27:42', '2020-09-04 23:27:42'),
(1653, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '745.6000', NULL, 0, 'Principal Repayment', '1157', 391, 1157, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-08', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-04 23:27:42', '2020-09-04 23:27:42'),
(1654, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3500.0000', 0, 'Interest Repayment', '1158', 400, 1158, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:34:32', '2020-09-05 16:34:32'),
(1655, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3500.0000', NULL, 0, 'Interest Repayment', '1158', 400, 1158, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:34:32', '2020-09-05 16:34:32'),
(1656, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3500.0000', 0, 'Interest Repayment', '1159', 400, 1159, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:35:34', '2020-09-05 16:35:34'),
(1657, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3500.0000', NULL, 0, 'Interest Repayment', '1159', 400, 1159, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:35:34', '2020-09-05 16:35:34'),
(1658, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3500.0000', 0, 'Interest Repayment', '1160', 400, 1160, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:36:16', '2020-09-05 16:36:16'),
(1659, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3500.0000', NULL, 0, 'Interest Repayment', '1160', 400, 1160, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:36:16', '2020-09-05 16:36:16'),
(1660, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3500.0000', 0, 'Interest Repayment', '1161', 400, 1161, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:36:58', '2020-09-05 16:36:58'),
(1661, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3500.0000', NULL, 0, 'Interest Repayment', '1161', 400, 1161, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:36:58', '2020-09-05 16:36:58'),
(1662, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3500.0000', 0, 'Interest Repayment', '1162', 400, 1162, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:37:50', '2020-09-05 16:37:50'),
(1663, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3500.0000', NULL, 0, 'Interest Repayment', '1162', 400, 1162, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:37:50', '2020-09-05 16:37:50'),
(1664, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '1000.0000', 0, 'Principal Repayment', '1163', 400, 1163, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:38:47', '2020-09-05 16:38:47'),
(1665, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1000.0000', NULL, 0, 'Principal Repayment', '1163', 400, 1163, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:38:47', '2020-09-05 16:38:47'),
(1666, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3400.0000', 0, 'Interest Repayment', '1164', 400, 1164, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-23', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:39:28', '2020-09-05 16:39:28'),
(1667, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3400.0000', NULL, 0, 'Interest Repayment', '1164', 400, 1164, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-23', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:39:28', '2020-09-05 16:39:28'),
(1668, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3400.0000', 0, 'Interest Repayment', '1165', 400, 1165, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-23', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:40:06', '2020-09-05 16:40:06'),
(1669, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3400.0000', NULL, 0, 'Interest Repayment', '1165', 400, 1165, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-23', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:40:06', '2020-09-05 16:40:06'),
(1670, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3500.0000', 0, 'Interest Repayment', '1166', 400, 1166, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-23', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:40:41', '2020-09-05 16:40:41'),
(1671, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3500.0000', NULL, 0, 'Interest Repayment', '1166', 400, 1166, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-23', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:40:41', '2020-09-05 16:40:41'),
(1672, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '200.1100', 0, 'Interest Repayment', '1167', 400, 1167, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-24', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:42:29', '2020-09-05 16:42:29'),
(1673, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '200.1100', NULL, 0, 'Interest Repayment', '1167', 400, 1167, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-24', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:42:29', '2020-09-05 16:42:29'),
(1674, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '5302.1400', 0, 'Interest Repayment', '1172', 374, 1172, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:48:59', '2020-09-05 16:48:59'),
(1675, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '5302.1400', NULL, 0, 'Interest Repayment', '1172', 374, 1172, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:48:59', '2020-09-05 16:48:59'),
(1676, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '5697.8700', 0, 'Principal Repayment', '1173', 374, 1173, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:49:50', '2020-09-05 16:49:50'),
(1677, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '5697.8700', NULL, 0, 'Principal Repayment', '1173', 374, 1173, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:49:50', '2020-09-05 16:49:50'),
(1678, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '4732.3500', 0, 'Interest Repayment', '1174', 374, 1174, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:50:34', '2020-09-05 16:50:34'),
(1679, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '4732.3500', NULL, 0, 'Interest Repayment', '1174', 374, 1174, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:50:34', '2020-09-05 16:50:34'),
(1680, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '3267.6500', 0, 'Principal Repayment', '1175', 374, 1175, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:51:29', '2020-09-05 16:51:29'),
(1681, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3267.6500', NULL, 0, 'Principal Repayment', '1175', 374, 1175, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:51:29', '2020-09-05 16:51:29'),
(1682, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '4405.5800', 0, 'Interest Repayment', '1176', 374, 1176, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:52:51', '2020-09-05 16:52:51'),
(1683, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '4405.5800', NULL, 0, 'Interest Repayment', '1176', 374, 1176, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:52:51', '2020-09-05 16:52:51'),
(1684, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '3594.4200', 0, 'Principal Repayment', '1177', 374, 1177, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:53:38', '2020-09-05 16:53:38'),
(1685, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3594.4200', NULL, 0, 'Principal Repayment', '1177', 374, 1177, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:53:38', '2020-09-05 16:53:38'),
(1686, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '4046.1400', 0, 'Interest Repayment', '1178', 374, 1178, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-25', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:54:22', '2020-09-05 16:54:22'),
(1687, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '4046.1400', NULL, 0, 'Interest Repayment', '1178', 374, 1178, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-25', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:54:22', '2020-09-05 16:54:22'),
(1688, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '3953.8600', 0, 'Principal Repayment', '1179', 374, 1179, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-25', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:55:22', '2020-09-05 16:55:22'),
(1689, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3953.8600', NULL, 0, 'Principal Repayment', '1179', 374, 1179, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-25', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:55:22', '2020-09-05 16:55:22'),
(1690, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3650.7600', 0, 'Interest Repayment', '1180', 374, 1180, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-25', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:56:25', '2020-09-05 16:56:25'),
(1691, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3650.7600', NULL, 0, 'Interest Repayment', '1180', 374, 1180, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-25', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:56:25', '2020-09-05 16:56:25'),
(1692, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '5349.2400', 0, 'Principal Repayment', '1181', 374, 1181, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-25', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:58:02', '2020-09-05 16:58:02'),
(1693, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '5349.2400', NULL, 0, 'Principal Repayment', '1181', 374, 1181, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-12-25', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:58:02', '2020-09-05 16:58:02'),
(1694, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '3115.8000', 0, 'Interest Repayment', '1182', 374, 1182, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-13', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:58:43', '2020-09-05 16:58:43'),
(1695, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3115.8000', NULL, 0, 'Interest Repayment', '1182', 374, 1182, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-13', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:58:43', '2020-09-05 16:58:43'),
(1696, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '12384.2000', 0, 'Principal Repayment', '1183', 374, 1183, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-13', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:59:33', '2020-09-05 16:59:33'),
(1697, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '12384.2000', NULL, 0, 'Principal Repayment', '1183', 374, 1183, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-13', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 16:59:33', '2020-09-05 16:59:33'),
(1698, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7877.4500', 0, 'Interest Repayment', '1184', 374, 1184, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:00:19', '2020-09-05 17:00:19'),
(1699, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7877.4500', NULL, 0, 'Interest Repayment', '1184', 374, 1184, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:00:19', '2020-09-05 17:00:19'),
(1700, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '2422.5500', 0, 'Principal Repayment', '1185', 374, 1185, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:01:16', '2020-09-05 17:01:16'),
(1701, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2422.5500', NULL, 0, 'Principal Repayment', '1185', 374, 1185, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-28', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:01:16', '2020-09-05 17:01:16'),
(1702, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '7635.1600', 0, 'Interest Repayment', '1186', 374, 1186, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:01:55', '2020-09-05 17:01:55'),
(1703, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '7635.1600', NULL, 0, 'Interest Repayment', '1186', 374, 1186, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:01:55', '2020-09-05 17:01:55'),
(1704, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '2364.8400', 0, 'Principal Repayment', '1187', 374, 1187, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:02:52', '2020-09-05 17:02:52'),
(1705, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2364.8400', NULL, 0, 'Principal Repayment', '1187', 374, 1187, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:02:52', '2020-09-05 17:02:52'),
(1706, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1651.6000', 0, 'Interest Repayment', '1188', 374, 1188, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:03:31', '2020-09-05 17:03:31'),
(1707, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1651.6000', NULL, 0, 'Interest Repayment', '1188', 374, 1188, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:03:31', '2020-09-05 17:03:31'),
(1708, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '2601.3200', 0, 'Principal Repayment', '1189', 374, 1189, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:04:18', '2020-09-05 17:04:18'),
(1709, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2601.3200', NULL, 0, 'Principal Repayment', '1189', 374, 1189, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-04-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:04:18', '2020-09-05 17:04:18'),
(1710, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '3861.4600', 0, 'Principal Repayment', '1191', 374, 1191, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:05:58', '2020-09-05 17:05:58'),
(1711, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '3861.4600', NULL, 0, 'Principal Repayment', '1191', 374, 1191, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:05:58', '2020-09-05 17:05:58'),
(1712, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '2247.6000', 0, 'Principal Repayment', '1193', 374, 1193, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:07:24', '2020-09-05 17:07:24'),
(1713, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2247.6000', NULL, 0, 'Principal Repayment', '1193', 374, 1193, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-29', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:07:24', '2020-09-05 17:07:24'),
(1714, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '2472.3600', 0, 'Principal Repayment', '1195', 374, 1195, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-30', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:08:51', '2020-09-05 17:08:51'),
(1715, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '2472.3600', NULL, 0, 'Principal Repayment', '1195', 374, 1195, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-30', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:08:51', '2020-09-05 17:08:51'),
(1716, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '1719.6000', 0, 'Principal Repayment', '1197', 374, 1197, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-31', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:10:12', '2020-09-05 17:10:12'),
(1717, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1719.6000', NULL, 0, 'Principal Repayment', '1197', 374, 1197, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-08-31', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:10:12', '2020-09-05 17:10:12'),
(1718, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1344.0000', 0, 'Interest Repayment', '1198', 383, 1198, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:35:38', '2020-09-05 17:35:38'),
(1719, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1344.0000', NULL, 0, 'Interest Repayment', '1198', 383, 1198, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:35:38', '2020-09-05 17:35:38'),
(1720, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '55.4000', 0, 'Principal Repayment', '1199', 383, 1199, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:36:28', '2020-09-05 17:36:28'),
(1721, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '55.4000', NULL, 0, 'Principal Repayment', '1199', 383, 1199, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-07', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:36:28', '2020-09-05 17:36:28'),
(1722, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '60.9400', 0, 'Principal Repayment', '1200', 383, 1200, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-12', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:43:06', '2020-09-05 17:43:06'),
(1723, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '60.9400', NULL, 0, 'Principal Repayment', '1200', 383, 1200, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-12', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:43:06', '2020-09-05 17:43:06'),
(1724, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1339.0600', 0, 'Interest Repayment', '1201', 383, 1201, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-12', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:46:07', '2020-09-05 17:46:07'),
(1725, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1339.0600', NULL, 0, 'Interest Repayment', '1201', 383, 1201, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-12', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:46:07', '2020-09-05 17:46:07'),
(1726, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1326.8700', 0, 'Interest Repayment', '1202', 383, 1202, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:52:18', '2020-09-05 17:52:18'),
(1727, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1326.8700', NULL, 0, 'Interest Repayment', '1202', 383, 1202, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:52:18', '2020-09-05 17:52:18'),
(1728, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '1723.1300', 0, 'Principal Repayment', '1203', 383, 1203, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:54:57', '2020-09-05 17:54:57'),
(1729, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1723.1300', NULL, 0, 'Principal Repayment', '1203', 383, 1203, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-11-26', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:54:57', '2020-09-05 17:54:57'),
(1730, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1154.5600', 0, 'Interest Repayment', '1204', 383, 1204, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:56:20', '2020-09-05 17:56:20'),
(1731, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1154.5600', NULL, 0, 'Interest Repayment', '1204', 383, 1204, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-01-27', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:56:20', '2020-09-05 17:56:20'),
(1732, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1154.5600', 0, 'Interest Repayment', '1205', 383, 1205, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:57:09', '2020-09-05 17:57:09'),
(1733, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1154.5600', NULL, 0, 'Interest Repayment', '1205', 383, 1205, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:57:09', '2020-09-05 17:57:09'),
(1734, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1154.5600', 0, 'Interest Repayment', '1206', 383, 1206, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:58:09', '2020-09-05 17:58:09'),
(1735, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1154.5600', NULL, 0, 'Interest Repayment', '1206', 383, 1206, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:58:09', '2020-09-05 17:58:09'),
(1736, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '136.3200', 0, 'Principal Repayment', '1207', 383, 1207, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:59:05', '2020-09-05 17:59:05'),
(1737, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '136.3200', NULL, 0, 'Principal Repayment', '1207', 383, 1207, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:59:05', '2020-09-05 17:59:05'),
(1738, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1140.9300', 0, 'Interest Repayment', '1208', 383, 1208, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:59:52', '2020-09-05 17:59:52'),
(1739, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1140.9300', NULL, 0, 'Interest Repayment', '1208', 383, 1208, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 17:59:52', '2020-09-05 17:59:52'),
(1740, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '1359.0800', 0, 'Principal Repayment', '1209', 383, 1209, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 18:00:48', '2020-09-05 18:00:48'),
(1741, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1359.0800', NULL, 0, 'Principal Repayment', '1209', 383, 1209, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-03-10', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 18:00:48', '2020-09-05 18:00:48'),
(1742, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1000.5200', 0, 'Interest Repayment', '1210', 383, 1210, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 18:01:40', '2020-09-05 18:01:40'),
(1743, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1000.5200', NULL, 0, 'Interest Repayment', '1210', 383, 1210, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 18:01:40', '2020-09-05 18:01:40'),
(1744, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1000.5200', 0, 'Interest Repayment', '1211', 383, 1211, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 18:02:20', '2020-09-05 18:02:20'),
(1745, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1000.5200', NULL, 0, 'Interest Repayment', '1211', 383, 1211, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 18:02:20', '2020-09-05 18:02:20'),
(1746, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '89.9700', 0, 'Principal Repayment', '1212', 383, 1212, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 18:03:09', '2020-09-05 18:03:09'),
(1747, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '89.9700', NULL, 0, 'Principal Repayment', '1212', 383, 1212, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-05-06', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 18:03:09', '2020-09-05 18:03:09'),
(1748, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '140.8200', 0, 'Interest Repayment', '1213', 383, 1213, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-23', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 18:04:10', '2020-09-05 18:04:10'),
(1749, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '140.8200', NULL, 0, 'Interest Repayment', '1213', 383, 1213, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-23', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 18:04:10', '2020-09-05 18:04:10'),
(1750, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '503.9800', 0, 'Principal Repayment', '1214', 383, 1214, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-23', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 18:04:51', '2020-09-05 18:04:51'),
(1751, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '503.9800', NULL, 0, 'Principal Repayment', '1214', 383, 1214, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-23', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 18:04:51', '2020-09-05 18:04:51'),
(1752, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '9456.2200', 0, 'Principal Repayment', '1217', 383, 1217, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-31', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 18:07:34', '2020-09-05 18:07:34'),
(1753, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '9456.2200', NULL, 0, 'Principal Repayment', '1217', 383, 1217, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-07-31', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 18:07:34', '2020-09-05 18:07:34'),
(1754, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '1756.3000', 0, 'Interest Repayment', '1218', 399, 1218, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-16', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 18:08:52', '2020-09-05 18:08:52'),
(1755, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '1756.3000', NULL, 0, 'Interest Repayment', '1218', 399, 1218, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-16', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 18:08:52', '2020-09-05 18:08:52'),
(1756, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '243.7000', 0, 'Principal Repayment', '1219', 399, 1219, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-16', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 18:09:34', '2020-09-05 18:09:34'),
(1757, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '243.7000', NULL, 0, 'Principal Repayment', '1219', 399, 1219, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-09-16', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 18:09:34', '2020-09-05 18:09:34'),
(1758, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '293.6100', 0, 'Interest Repayment', '1220', 399, 1220, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-14', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 18:10:16', '2020-09-05 18:10:16'),
(1759, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '293.6100', NULL, 0, 'Interest Repayment', '1220', 399, 1220, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-14', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 18:10:16', '2020-09-05 18:10:16'),
(1760, 1, 7, 37, NULL, 'repayment', 'repayment_principal', NULL, NULL, NULL, '268.0700', 0, 'Principal Repayment', '1221', 399, 1221, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-14', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 18:11:00', '2020-09-05 18:11:00'),
(1761, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '268.0700', NULL, 0, 'Principal Repayment', '1221', 399, 1221, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-10-14', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-05 18:11:00', '2020-09-05 18:11:00'),
(1768, 1, NULL, 37, NULL, 'disbursement', NULL, NULL, NULL, NULL, '100000.0000', 0, 'Loan Disbursement', NULL, 407, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-24', '02', '2020', NULL, 1, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-08 17:56:36', '2020-09-08 17:56:36'),
(1769, 1, 7, 37, NULL, 'disbursement', NULL, NULL, NULL, '100000.0000', NULL, 0, 'Loan Disbursement', NULL, 407, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-02-24', '02', '2020', NULL, 1, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-08 17:56:36', '2020-09-08 17:56:36'),
(1770, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '50000.0000', 0, 'Interest Repayment', '1245', 403, 1245, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-01-05', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-09 01:47:05', '2020-09-09 01:47:05'),
(1771, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '50000.0000', NULL, 0, 'Interest Repayment', '1245', 403, 1245, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-01-05', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-09 01:47:05', '2020-09-09 01:47:05'),
(1772, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '27500.0000', 0, 'Interest Repayment', '1246', 379, 1246, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-09-14', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-14 14:46:22', '2020-09-14 14:46:22'),
(1773, 1, 1, 37, NULL, 'repayment', NULL, NULL, NULL, '27500.0000', NULL, 0, 'Interest Repayment', '1246', 379, 1246, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-09-14', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2020-09-14 14:46:22', '2020-09-14 14:46:22'),
(1774, 1, 77, 37, NULL, 'repayment', 'repayment_interest', NULL, NULL, NULL, '27500.0000', 0, 'Interest Repayment', '1247', 379, 1247, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-05', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2022-10-05 15:19:13', '2022-10-05 15:19:13'),
(1775, 1, 4, 37, NULL, 'repayment', NULL, NULL, NULL, '27500.0000', NULL, 0, 'Interest Repayment', '1247', 379, 1247, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-05', '0', '2', NULL, NULL, NULL, 0, 0, 1, NULL, NULL, NULL, '2022-10-05 15:19:13', '2022-10-05 15:19:13');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `office_id` int(11) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `external_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `joined_date` date DEFAULT NULL,
  `activated_date` date DEFAULT NULL,
  `reactivated_date` date DEFAULT NULL,
  `declined_date` date DEFAULT NULL,
  `declined_reason` text COLLATE utf8mb4_unicode_ci,
  `closed_reason` text COLLATE utf8mb4_unicode_ci,
  `closed_date` date DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `activated_by_id` int(11) DEFAULT NULL,
  `reactivated_by_id` int(11) DEFAULT NULL,
  `declined_by_id` int(11) DEFAULT NULL,
  `closed_by_id` int(11) DEFAULT NULL,
  `mobile` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ward` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','active','inactive','declined','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_clients`
--

CREATE TABLE `group_clients` (
  `id` int(10) UNSIGNED NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_loan_allocation`
--

CREATE TABLE `group_loan_allocation` (
  `id` int(10) UNSIGNED NOT NULL,
  `loan_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `amount` decimal(65,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_users`
--

CREATE TABLE `group_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guarantors`
--

CREATE TABLE `guarantors` (
  `id` int(10) UNSIGNED NOT NULL,
  `country_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `savings_id` int(11) DEFAULT NULL,
  `loan_id` int(11) DEFAULT NULL,
  `loan_application_id` int(11) DEFAULT NULL,
  `is_client` tinyint(4) NOT NULL DEFAULT '0',
  `client_relationship_id` int(11) DEFAULT NULL,
  `amount` decimal(65,4) DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `middle_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('male','female','other','unspecified') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `street` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `mobile` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `picture` text COLLATE utf8mb4_unicode_ci,
  `work` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `work_address` text COLLATE utf8mb4_unicode_ci,
  `lock_funds` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guarantors`
--

INSERT INTO `guarantors` (`id`, `country_id`, `client_id`, `savings_id`, `loan_id`, `loan_application_id`, `is_client`, `client_relationship_id`, `amount`, `title`, `first_name`, `middle_name`, `last_name`, `gender`, `dob`, `street`, `address`, `mobile`, `phone`, `email`, `picture`, `work`, `work_address`, `lock_funds`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, NULL, 944, NULL, 0, 4, '8000.0000', NULL, 'tisiyenje', NULL, 'Phiri', NULL, NULL, NULL, NULL, '0978246508', NULL, NULL, NULL, NULL, NULL, 0, '2019-06-17 23:38:58', '2019-06-17 23:38:58'),
(2, NULL, NULL, NULL, 1154, NULL, 0, 14, '7000.0000', NULL, 'clara', NULL, 'Khondowe', NULL, NULL, NULL, NULL, '0979784706', NULL, NULL, NULL, NULL, NULL, 0, '2019-10-11 19:47:23', '2019-10-11 19:47:23'),
(3, NULL, NULL, NULL, 1175, NULL, 0, 14, '6500.0000', NULL, 'Stephen', NULL, 'Band', NULL, NULL, NULL, NULL, '0977703895', NULL, NULL, NULL, NULL, NULL, 0, '2019-10-11 22:06:48', '2019-10-11 22:06:48'),
(4, NULL, NULL, NULL, 13, NULL, 0, 16, '14000.0000', NULL, 'CHABALA', 'NACHELA', 'YAMBA', NULL, NULL, NULL, NULL, '0978110912', NULL, NULL, NULL, NULL, NULL, 0, '2022-10-14 16:37:13', '2022-10-14 16:37:13'),
(5, NULL, NULL, NULL, 14, NULL, 0, 16, '40000.0000', NULL, 'CHABALA', 'MUTINTA', 'YAMBA', NULL, NULL, NULL, NULL, '0960957632', NULL, NULL, NULL, NULL, NULL, 0, '2022-10-14 16:54:02', '2022-10-14 16:54:02');

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_type` enum('client','group') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'client',
  `loan_product_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `office_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `fund_id` int(11) DEFAULT NULL,
  `loan_purpose_id` int(11) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `decimals` int(11) NOT NULL DEFAULT '2',
  `account_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `external_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loan_officer_id` int(11) DEFAULT NULL,
  `principal` decimal(65,4) DEFAULT NULL,
  `applied_amount` decimal(65,4) DEFAULT NULL,
  `approved_amount` decimal(65,4) DEFAULT NULL,
  `principal_derived` decimal(65,4) DEFAULT NULL,
  `interest_derived` decimal(65,4) DEFAULT NULL,
  `fees_derived` decimal(65,4) DEFAULT NULL,
  `penalty_derived` decimal(65,4) DEFAULT NULL,
  `disbursement_fees` decimal(65,4) DEFAULT NULL,
  `processing_fee` decimal(65,4) DEFAULT NULL,
  `loan_term` int(11) DEFAULT NULL,
  `loan_term_type` enum('days','weeks','months','years') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `repayment_frequency` int(11) DEFAULT NULL,
  `repayment_frequency_type` enum('days','weeks','months','years') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `override_interest` tinyint(4) DEFAULT '0',
  `interest_rate` decimal(65,4) DEFAULT NULL,
  `override_interest_rate` decimal(65,4) DEFAULT NULL,
  `interest_rate_type` enum('day','week','month','year') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expected_disbursement_date` date DEFAULT NULL,
  `disbursement_date` date DEFAULT NULL,
  `expected_maturity_date` date DEFAULT NULL,
  `expected_first_repayment_date` date DEFAULT NULL,
  `repayments_number` int(11) DEFAULT NULL,
  `first_repayment_date` date DEFAULT NULL,
  `interest_method` enum('flat','declining_balance') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `armotization_method` enum('equal_installment','equal_principal') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grace_on_interest_charged` int(11) DEFAULT NULL,
  `grace_on_principal` int(11) DEFAULT NULL,
  `grace_on_interest_payment` int(11) DEFAULT NULL,
  `status` enum('new','pending','approved','need_changes','disbursed','declined','rejected','withdrawn','written_off','closed','pending_reschedule','rescheduled','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_by_id` int(11) DEFAULT NULL,
  `modified_by_id` int(11) DEFAULT NULL,
  `approved_by_id` int(11) DEFAULT NULL,
  `need_changes_by_id` int(11) DEFAULT NULL,
  `withdrawn_by_id` int(11) DEFAULT NULL,
  `declined_by_id` int(11) DEFAULT NULL,
  `written_off_by_id` int(11) DEFAULT NULL,
  `disbursed_by_id` int(11) DEFAULT NULL,
  `rescheduled_by_id` int(11) DEFAULT NULL,
  `closed_by_id` int(11) DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  `modified_date` date DEFAULT NULL,
  `approved_date` date DEFAULT NULL,
  `need_changes_date` date DEFAULT NULL,
  `withdrawn_date` date DEFAULT NULL,
  `declined_date` date DEFAULT NULL,
  `written_off_date` date DEFAULT NULL,
  `rescheduled_date` date DEFAULT NULL,
  `closed_date` date DEFAULT NULL,
  `month` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `approved_notes` text COLLATE utf8mb4_unicode_ci,
  `declined_notes` text COLLATE utf8mb4_unicode_ci,
  `written_off_notes` text COLLATE utf8mb4_unicode_ci,
  `disbursed_notes` text COLLATE utf8mb4_unicode_ci,
  `withdrawn_notes` text COLLATE utf8mb4_unicode_ci,
  `rescheduled_notes` text COLLATE utf8mb4_unicode_ci,
  `closed_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`id`, `client_type`, `loan_product_id`, `client_id`, `office_id`, `group_id`, `fund_id`, `loan_purpose_id`, `currency_id`, `decimals`, `account_number`, `external_id`, `loan_officer_id`, `principal`, `applied_amount`, `approved_amount`, `principal_derived`, `interest_derived`, `fees_derived`, `penalty_derived`, `disbursement_fees`, `processing_fee`, `loan_term`, `loan_term_type`, `repayment_frequency`, `repayment_frequency_type`, `override_interest`, `interest_rate`, `override_interest_rate`, `interest_rate_type`, `expected_disbursement_date`, `disbursement_date`, `expected_maturity_date`, `expected_first_repayment_date`, `repayments_number`, `first_repayment_date`, `interest_method`, `armotization_method`, `grace_on_interest_charged`, `grace_on_principal`, `grace_on_interest_payment`, `status`, `created_by_id`, `modified_by_id`, `approved_by_id`, `need_changes_by_id`, `withdrawn_by_id`, `declined_by_id`, `written_off_by_id`, `disbursed_by_id`, `rescheduled_by_id`, `closed_by_id`, `created_date`, `modified_date`, `approved_date`, `need_changes_date`, `withdrawn_date`, `declined_date`, `written_off_date`, `rescheduled_date`, `closed_date`, `month`, `year`, `notes`, `approved_notes`, `declined_notes`, `written_off_notes`, `disbursed_notes`, `withdrawn_notes`, `rescheduled_notes`, `closed_notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'client', 2, 1, 1, NULL, 2, 2, 37, 1, NULL, '255876/66/1', 22, '2200.0000', '2200.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'months', 1, 'months', 0, '25.0000', NULL, 'month', '2020-11-10', NULL, NULL, '2020-11-27', NULL, NULL, 'declining_balance', 'equal_installment', 0, 0, 0, 'withdrawn', 26, NULL, NULL, NULL, 26, NULL, NULL, NULL, NULL, NULL, '2020-10-17', NULL, NULL, NULL, '2022-10-10', NULL, NULL, NULL, NULL, '10', '2020', NULL, NULL, NULL, NULL, NULL, 'wrong detailes', NULL, NULL, '2022-10-11 01:41:57', '2022-10-11 02:24:48', NULL),
(2, 'client', 2, 1, 1, NULL, NULL, 2, 37, 2, NULL, '255876/66/1', 22, '2200.0000', '2200.0000', '2200.0000', NULL, NULL, NULL, NULL, NULL, NULL, 1, 'months', 1, 'months', 0, '20.0000', NULL, 'month', '2020-11-10', '2020-11-10', '2020-12-27', '2020-11-27', NULL, '2020-11-27', 'flat', 'equal_installment', 0, 0, 0, 'closed', 26, NULL, 26, NULL, NULL, NULL, NULL, 26, NULL, NULL, '2022-10-10', NULL, '2020-11-10', NULL, NULL, NULL, NULL, NULL, NULL, '10', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-11 02:10:46', '2022-10-11 16:08:02', NULL),
(3, 'client', 2, 3, 1, NULL, 2, 2, 37, 2, NULL, '413706/74/1', 24, '6000.0000', '6000.0000', '6000.0000', NULL, NULL, NULL, NULL, NULL, NULL, 4, 'months', 1, 'months', 0, '15.0000', NULL, 'month', '2022-06-15', '2022-06-15', '2022-10-30', '2022-06-30', NULL, '2022-06-30', 'flat', 'equal_installment', 0, 0, 0, 'disbursed', 26, NULL, 26, NULL, NULL, NULL, NULL, 26, NULL, NULL, '2022-10-11', NULL, '2022-06-14', NULL, NULL, NULL, NULL, NULL, NULL, '10', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-11 16:20:52', '2022-10-11 16:27:14', NULL),
(4, 'client', 2, 4, 1, NULL, NULL, 2, 37, 2, NULL, '135220/10/1', 23, '10000.0000', '10000.0000', '10000.0000', NULL, NULL, NULL, NULL, NULL, NULL, 6, 'months', 1, 'months', 0, '10.8334', NULL, 'month', '2022-08-15', '2022-08-15', '2023-03-27', '2022-09-27', NULL, '2022-09-27', 'flat', 'equal_installment', 0, 0, 0, 'disbursed', 26, NULL, 26, NULL, NULL, NULL, NULL, 26, NULL, NULL, '2022-08-15', NULL, '2022-10-11', NULL, NULL, NULL, NULL, NULL, NULL, '08', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-11 16:58:02', '2022-10-11 19:21:53', NULL),
(5, 'client', 2, 5, 1, NULL, NULL, 3, 37, 2, NULL, '758224/11/1', 23, '15000.0000', '15000.0000', '15000.0000', NULL, NULL, NULL, NULL, NULL, NULL, 1, 'months', 1, 'months', 0, '20.0000', NULL, 'month', '2021-04-13', '2021-04-15', '2021-05-30', '2021-04-30', NULL, '2021-04-30', 'flat', 'equal_installment', 0, 0, 0, 'closed', 26, NULL, 26, NULL, NULL, NULL, NULL, 26, NULL, NULL, '2021-04-13', NULL, '2021-04-13', NULL, NULL, NULL, NULL, NULL, NULL, '04', '2021', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-11 19:57:21', '2022-10-11 20:04:18', NULL),
(6, 'client', 2, 5, 1, NULL, 2, 3, 37, 2, NULL, '758224/11/1', 23, '20000.0000', '20000.0000', '20000.0000', NULL, NULL, NULL, NULL, NULL, NULL, 2, 'months', 1, 'months', 0, '17.5000', NULL, 'month', '2021-09-06', '2021-09-06', '2021-11-30', '2021-09-30', NULL, '2021-09-30', 'flat', 'equal_installment', 0, 0, 0, 'closed', 26, NULL, 26, NULL, NULL, NULL, NULL, 26, NULL, NULL, '2022-10-11', NULL, '2021-09-06', NULL, NULL, NULL, NULL, NULL, NULL, '10', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-11 20:08:15', '2022-10-11 20:20:56', NULL),
(7, 'client', 2, 6, 1, NULL, NULL, NULL, 37, 2, NULL, '199035/47/1', 22, '2500.0000', '2500.0000', '2500.0000', NULL, NULL, NULL, NULL, NULL, NULL, 2, 'months', 1, 'months', 0, '20.0000', NULL, 'month', '2020-10-22', '2020-10-22', '2021-01-23', '2020-11-23', NULL, '2020-11-23', 'flat', 'equal_installment', 0, 0, 0, 'written_off', 26, NULL, 26, NULL, NULL, NULL, 26, 26, NULL, NULL, '2022-10-13', NULL, '2020-10-22', NULL, NULL, NULL, '2022-10-13', NULL, NULL, '10', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-13 14:37:44', '2022-10-13 14:54:20', NULL),
(8, 'client', 7, 5, 1, NULL, 2, 3, 37, 2, NULL, '758224/11/1', 23, '20000.0000', '20000.0000', '20000.0000', NULL, NULL, NULL, NULL, NULL, NULL, 2, 'months', 1, 'months', 0, '20.0000', NULL, 'month', '2022-07-25', '2022-07-25', '2022-11-30', '2022-09-30', NULL, '2022-09-30', 'flat', 'equal_installment', 0, 0, 0, 'closed', 26, NULL, 26, NULL, NULL, NULL, NULL, 26, NULL, NULL, '2022-07-25', NULL, '2022-07-25', NULL, NULL, NULL, NULL, NULL, NULL, '07', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-13 15:30:58', '2022-10-13 15:57:22', NULL),
(9, 'client', 2, 7, 1, NULL, 2, 2, 37, 2, NULL, '253360/68/1', 1, '10000.0000', '10000.0000', '10000.0000', NULL, NULL, NULL, NULL, NULL, NULL, 1, 'months', 1, 'months', 0, '25.0000', NULL, 'month', '2020-10-17', '2020-10-17', '2020-12-17', '2020-11-17', NULL, '2020-11-17', 'flat', 'equal_installment', 0, 0, 0, 'closed', 26, NULL, 26, NULL, NULL, NULL, NULL, 26, NULL, NULL, '2020-10-17', NULL, '2020-10-17', NULL, NULL, NULL, NULL, NULL, NULL, '10', '2020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-13 20:04:38', '2022-10-13 20:06:37', NULL),
(10, 'client', 2, 7, 1, NULL, NULL, 2, 37, 2, NULL, '253360/68/1', 1, '25000.0000', '25000.0000', '25000.0000', NULL, NULL, NULL, NULL, NULL, NULL, 2, 'months', 1, 'months', 0, '20.0000', NULL, 'month', '2020-10-22', '2020-10-22', '2021-01-20', '2020-11-20', NULL, '2020-11-20', 'flat', 'equal_installment', 0, 0, 0, 'written_off', 26, NULL, 26, NULL, NULL, NULL, 26, 26, NULL, NULL, '2020-10-23', NULL, '2020-10-22', NULL, NULL, NULL, '2022-10-13', NULL, NULL, '10', '2020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-13 20:09:35', '2022-10-13 20:13:05', NULL),
(11, 'client', 3, 8, 1, NULL, 2, 2, 37, 2, NULL, '741755/11/1', 22, '50000.0000', '50000.0000', '50000.0000', NULL, NULL, NULL, NULL, NULL, NULL, 6, 'months', 1, 'months', 0, '10.0000', NULL, 'month', '2021-06-08', '2021-08-18', '2022-03-17', '2021-09-17', NULL, '2021-09-17', 'flat', 'equal_installment', 0, 0, 0, 'disbursed', 26, NULL, 26, NULL, NULL, NULL, NULL, 26, NULL, NULL, '2021-06-08', NULL, '2021-08-18', NULL, NULL, NULL, NULL, NULL, NULL, '06', '2021', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-14 14:53:51', '2022-10-14 15:21:52', NULL),
(12, 'client', 9, 9, 1, NULL, 2, 1, 37, 2, NULL, '878578/11/1', 22, '300000.0000', '300000.0000', '300000.0000', NULL, NULL, NULL, NULL, NULL, NULL, 3, 'months', 1, 'months', 0, '10.0000', NULL, 'month', '2020-10-28', '2020-10-28', '2021-02-27', '2022-10-27', NULL, '2020-11-27', 'flat', 'equal_installment', 0, 6, 0, 'disbursed', 26, NULL, 26, NULL, NULL, NULL, NULL, 26, NULL, NULL, '2020-10-27', NULL, '2020-10-28', NULL, NULL, NULL, NULL, NULL, NULL, '10', '2020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-14 16:00:23', '2022-10-14 16:04:44', NULL),
(13, 'client', 7, 10, 1, NULL, NULL, 1, 37, 2, NULL, '320170002357', 24, '10000.0000', '10000.0000', '10000.0000', NULL, NULL, NULL, NULL, NULL, NULL, 2, 'months', 1, 'months', 0, '20.0000', NULL, 'month', '2021-02-17', '2021-02-17', '2021-05-30', '2021-03-30', NULL, '2021-03-30', 'flat', 'equal_installment', 0, 0, 0, 'closed', 26, NULL, 26, NULL, NULL, NULL, NULL, 26, NULL, NULL, '2021-02-13', NULL, '2020-02-17', NULL, NULL, NULL, NULL, NULL, NULL, '02', '2021', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-14 16:35:14', '2022-10-14 16:44:14', NULL),
(14, 'client', 7, 10, 1, NULL, NULL, 1, 37, 2, NULL, '320170002357', 24, '25000.0000', '25000.0000', '25000.0000', NULL, NULL, NULL, NULL, NULL, NULL, 6, 'months', 1, 'months', 0, '10.0000', NULL, 'month', '2021-05-07', '2021-05-07', '2021-12-11', '2021-06-11', NULL, '2021-06-11', 'flat', 'equal_installment', 0, 0, 0, 'closed', 26, NULL, 26, NULL, NULL, NULL, NULL, 26, NULL, NULL, '2021-05-07', NULL, '2021-05-07', NULL, NULL, NULL, NULL, NULL, NULL, '05', '2021', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-14 16:51:32', '2022-10-17 14:00:24', NULL),
(15, 'client', 2, 1, 1, NULL, 2, 2, 37, 2, NULL, '255876/66/1', 22, '1000.0000', '1000.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'months', 1, 'months', 0, '25.0000', NULL, 'month', '2022-10-18', NULL, NULL, '2022-11-18', NULL, NULL, 'flat', 'equal_installment', 0, 0, 0, 'declined', 26, NULL, NULL, NULL, NULL, 26, NULL, NULL, NULL, NULL, '2022-10-17', NULL, NULL, NULL, NULL, '2022-10-17', NULL, NULL, NULL, '10', '2022', NULL, NULL, 'client was not able to pay previous loan', NULL, NULL, NULL, NULL, NULL, '2022-10-17 14:56:26', '2022-10-17 14:57:33', NULL),
(16, 'client', 2, 11, 1, NULL, NULL, 2, 37, 2, NULL, '329380/61/1', 23, '10000.0000', '10000.0000', '10000.0000', NULL, NULL, NULL, NULL, NULL, NULL, 6, 'months', 1, 'months', 0, '10.0000', NULL, 'month', '2022-07-21', '2022-07-21', '2023-03-02', '2022-08-30', NULL, '2022-08-30', 'flat', 'equal_installment', 0, 0, 0, 'disbursed', 26, NULL, 26, NULL, NULL, NULL, NULL, 26, NULL, NULL, '2022-06-10', NULL, '2022-07-21', NULL, NULL, NULL, NULL, NULL, NULL, '06', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-19 14:24:40', '2022-10-19 14:42:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `loan_applications`
--

CREATE TABLE `loan_applications` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_type` enum('client','group') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'client',
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `loan_id` int(10) UNSIGNED DEFAULT NULL,
  `loan_purpose_id` int(11) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `office_id` int(10) UNSIGNED DEFAULT NULL,
  `client_id` int(10) UNSIGNED DEFAULT NULL,
  `group_id` int(10) UNSIGNED DEFAULT NULL,
  `loan_product_id` int(11) NOT NULL,
  `amount` decimal(65,4) NOT NULL DEFAULT '0.0000',
  `status` enum('approved','pending','declined') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `guarantor_ids` text COLLATE utf8mb4_unicode_ci,
  `loan_term` int(11) DEFAULT NULL,
  `loan_term_type` enum('days','weeks','months','years') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by_id` int(11) DEFAULT NULL,
  `declined_by_id` int(11) DEFAULT NULL,
  `approved_notes` text COLLATE utf8mb4_unicode_ci,
  `declined_notes` text COLLATE utf8mb4_unicode_ci,
  `declined_date` date DEFAULT NULL,
  `approved_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_charges`
--

CREATE TABLE `loan_charges` (
  `id` int(10) UNSIGNED NOT NULL,
  `loan_id` int(11) DEFAULT NULL,
  `charge_id` int(11) DEFAULT NULL,
  `penalty` tinyint(4) NOT NULL DEFAULT '0',
  `waived` tinyint(4) NOT NULL DEFAULT '0',
  `charge_type` enum('disbursement','disbursement_repayment','specified_due_date','installment_fee','overdue_installment_fee','loan_rescheduling_fee','overdue_maturity') COLLATE utf8mb4_unicode_ci NOT NULL,
  `charge_option` enum('flat','percentage','installment_principal_due','installment_principal_interest_due','installment_interest_due','installment_total_due','total_due','original_principal') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(65,2) DEFAULT NULL,
  `amount_paid` decimal(65,2) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `grace_period` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loan_charges`
--

INSERT INTO `loan_charges` (`id`, `loan_id`, `charge_id`, `penalty`, `waived`, `charge_type`, `charge_option`, `amount`, `amount_paid`, `due_date`, `grace_period`, `created_at`, `updated_at`) VALUES
(2, 16, 3, 0, 0, 'disbursement', 'flat', '150.00', NULL, NULL, 0, '2022-10-19 14:41:59', '2022-10-19 14:41:59');

-- --------------------------------------------------------

--
-- Table structure for table `loan_products`
--

CREATE TABLE `loan_products` (
  `id` int(10) UNSIGNED NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `fund_id` int(11) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `decimals` int(11) NOT NULL DEFAULT '2',
  `minimum_principal` decimal(65,4) DEFAULT NULL,
  `default_principal` decimal(65,4) DEFAULT NULL,
  `maximum_principal` decimal(65,4) DEFAULT NULL,
  `minimum_loan_term` int(11) DEFAULT NULL,
  `default_loan_term` int(11) DEFAULT NULL,
  `maximum_loan_term` int(11) DEFAULT NULL,
  `repayment_frequency` int(11) DEFAULT NULL,
  `repayment_frequency_type` enum('days','weeks','months','years') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `minimum_interest_rate` decimal(65,4) DEFAULT NULL,
  `default_interest_rate` decimal(65,4) DEFAULT NULL,
  `maximum_interest_rate` decimal(65,4) DEFAULT NULL,
  `interest_rate_type` enum('day','week','month','year') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grace_on_interest_charged` int(11) DEFAULT NULL,
  `grace_on_principal` int(11) DEFAULT NULL,
  `grace_on_interest_payment` int(11) DEFAULT NULL,
  `allow_custom_grace` tinyint(4) NOT NULL DEFAULT '0',
  `allow_standing_instuctions` tinyint(4) NOT NULL DEFAULT '0',
  `interest_method` enum('flat','declining_balance') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `armotization_method` enum('equal_installment','equal_principal') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interest_calculation_period_type` enum('daily','same') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'same',
  `year_days` enum('actual','360','364','365') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '365',
  `month_days` enum('actual','30','31') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '30',
  `loan_transaction_strategy` enum('penalty_fees_interest_principal','principal_interest_penalty_fees','interest_principal_penalty_fees') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'interest_principal_penalty_fees',
  `include_in_cycle` tinyint(4) NOT NULL DEFAULT '0',
  `lock_guarantee` tinyint(4) NOT NULL DEFAULT '0',
  `allocate_overpayments` tinyint(4) NOT NULL DEFAULT '0',
  `allow_additional_charges` tinyint(4) NOT NULL DEFAULT '0',
  `accounting_rule` enum('none','cash','accrual_periodic','accrual_upfront') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `npa_days` int(11) DEFAULT NULL,
  `arrears_grace_days` int(11) DEFAULT NULL,
  `npa_suspend_income` tinyint(4) NOT NULL DEFAULT '0',
  `gl_account_fund_source_id` int(11) DEFAULT NULL,
  `gl_account_loan_portfolio_id` int(11) DEFAULT NULL,
  `gl_account_receivable_interest_id` int(11) DEFAULT NULL,
  `gl_account_receivable_fee_id` int(11) DEFAULT NULL,
  `gl_account_receivable_penalty_id` int(11) DEFAULT NULL,
  `gl_account_loan_over_payments_id` int(11) DEFAULT NULL,
  `gl_account_suspended_income_id` int(11) DEFAULT NULL,
  `gl_account_income_interest_id` int(11) DEFAULT NULL,
  `gl_account_income_fee_id` int(11) DEFAULT NULL,
  `gl_account_income_penalty_id` int(11) DEFAULT NULL,
  `gl_account_income_recovery_id` int(11) DEFAULT NULL,
  `gl_account_loans_written_off_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loan_products`
--

INSERT INTO `loan_products` (`id`, `created_by_id`, `name`, `short_name`, `description`, `fund_id`, `currency_id`, `decimals`, `minimum_principal`, `default_principal`, `maximum_principal`, `minimum_loan_term`, `default_loan_term`, `maximum_loan_term`, `repayment_frequency`, `repayment_frequency_type`, `minimum_interest_rate`, `default_interest_rate`, `maximum_interest_rate`, `interest_rate_type`, `grace_on_interest_charged`, `grace_on_principal`, `grace_on_interest_payment`, `allow_custom_grace`, `allow_standing_instuctions`, `interest_method`, `armotization_method`, `interest_calculation_period_type`, `year_days`, `month_days`, `loan_transaction_strategy`, `include_in_cycle`, `lock_guarantee`, `allocate_overpayments`, `allow_additional_charges`, `accounting_rule`, `npa_days`, `arrears_grace_days`, `npa_suspend_income`, `gl_account_fund_source_id`, `gl_account_loan_portfolio_id`, `gl_account_receivable_interest_id`, `gl_account_receivable_fee_id`, `gl_account_receivable_penalty_id`, `gl_account_loan_over_payments_id`, `gl_account_suspended_income_id`, `gl_account_income_interest_id`, `gl_account_income_fee_id`, `gl_account_income_penalty_id`, `gl_account_income_recovery_id`, `gl_account_loans_written_off_id`, `created_at`, `updated_at`) VALUES
(2, 1, 'Salary advance', 'Salary backed', 'loans tied to the net and salary day', 1, 37, 2, '800.0000', '0.0000', '30000.0000', 0, 1, 18, 1, 'months', '5.0000', '0.0000', '25.0000', 'month', 0, 0, 0, 0, 0, 'flat', 'equal_installment', 'same', 'actual', 'actual', 'interest_principal_penalty_fees', 0, 1, 0, 1, 'none', NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 1, 'Collateral- Flat', 'Collateral', 'Collateral', 1, 37, 2, '15000.0000', '0.0000', '300000.0000', 1, 0, 12, 1, 'months', '5.0000', '0.0000', '25.0000', 'month', 0, 0, 0, 0, 0, 'flat', 'equal_installment', 'same', 'actual', 'actual', 'penalty_fees_interest_principal', 0, 1, 0, 1, 'none', NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 26, 'BUSINESS LOAN - FLAT RATE', 'BUSINESS', 'To finance SME, stock, invoices and contracts', 1, 37, 2, '5000.0000', '0.0000', '100000.0000', 1, 0, 12, 1, 'months', '10.0000', '0.0000', '25.0000', 'month', 0, 0, 0, 0, 0, 'flat', 'equal_installment', 'same', 'actual', 'actual', 'interest_principal_penalty_fees', 0, 1, 0, 1, 'none', NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 26, 'SCHEME LOAN - FLAT RATE', 'SCHEME', 'MOU LOANS', 1, 37, 2, '500.0000', '0.0000', '20000.0000', 1, 0, 18, 1, 'months', '5.0000', '0.0000', '20.0000', 'month', 0, 0, 0, 0, 0, 'flat', 'equal_installment', 'same', 'actual', 'actual', 'penalty_fees_interest_principal', 0, 1, 0, 1, 'none', NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 26, 'BUSINESS LOAN - INTEREST PAID FIRST', 'BUSINESS', 'INTEREST FIRST', 1, 37, 2, '10000.0000', '0.0000', '300000.0000', 1, 0, 10, 1, 'months', '5.0000', '0.0000', '25.0000', 'month', 0, 6, 0, 0, 0, 'flat', 'equal_installment', 'same', 'actual', 'actual', 'interest_principal_penalty_fees', 0, 1, 0, 0, 'none', NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `loan_product_charges`
--

CREATE TABLE `loan_product_charges` (
  `id` int(10) UNSIGNED NOT NULL,
  `loan_product_id` int(11) DEFAULT NULL,
  `charge_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loan_product_charges`
--

INSERT INTO `loan_product_charges` (`id`, `loan_product_id`, `charge_id`, `created_at`, `updated_at`) VALUES
(7, 2, 3, '2022-10-19 14:21:56', '2022-10-19 14:21:56');

-- --------------------------------------------------------

--
-- Table structure for table `loan_provisioning_criteria`
--

CREATE TABLE `loan_provisioning_criteria` (
  `id` int(10) UNSIGNED NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `name` text COLLATE utf8mb4_unicode_ci,
  `min` int(11) DEFAULT NULL,
  `max` int(11) DEFAULT NULL,
  `percentage` int(11) DEFAULT NULL,
  `gl_account_liability_id` int(11) DEFAULT NULL,
  `gl_account_expense_id` int(11) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loan_provisioning_criteria`
--

INSERT INTO `loan_provisioning_criteria` (`id`, `created_by_id`, `name`, `min`, `max`, `percentage`, `gl_account_liability_id`, `gl_account_expense_id`, `notes`, `active`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Current', 0, 30, 0, NULL, NULL, NULL, 1, NULL, NULL),
(2, NULL, 'Especially Mentioned', 31, 60, 5, NULL, NULL, NULL, 1, NULL, NULL),
(3, NULL, 'Substandard', 61, 90, 10, NULL, NULL, NULL, 1, NULL, NULL),
(4, NULL, 'Doubtful', 91, 180, 50, NULL, NULL, NULL, 1, NULL, NULL),
(5, NULL, 'Loss', 181, 5000, 100, NULL, NULL, NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `loan_purposes`
--

CREATE TABLE `loan_purposes` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loan_purposes`
--

INSERT INTO `loan_purposes` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Business', '2018-12-13 11:00:22', '2018-12-13 11:00:22'),
(2, 'Personal', '2019-04-28 18:46:12', '2019-04-28 18:46:12'),
(3, 'School Fees', '2019-04-28 18:46:32', '2019-04-28 18:46:32'),
(4, 'Education', '2019-04-28 18:47:31', '2019-04-28 18:47:31');

-- --------------------------------------------------------

--
-- Table structure for table `loan_repayment_schedules`
--

CREATE TABLE `loan_repayment_schedules` (
  `id` int(10) UNSIGNED NOT NULL,
  `loan_id` int(11) DEFAULT NULL,
  `installment` int(11) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `month` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `principal` decimal(65,4) DEFAULT NULL,
  `principal_waived` decimal(65,4) DEFAULT NULL,
  `principal_written_off` decimal(65,4) DEFAULT NULL,
  `principal_paid` decimal(65,4) DEFAULT NULL,
  `interest` decimal(65,4) DEFAULT NULL,
  `interest_waived` decimal(65,4) DEFAULT NULL,
  `interest_written_off` decimal(65,4) DEFAULT NULL,
  `interest_paid` decimal(65,4) DEFAULT NULL,
  `fees` decimal(65,4) DEFAULT NULL,
  `fees_waived` decimal(65,4) DEFAULT NULL,
  `fees_written_off` decimal(65,4) DEFAULT NULL,
  `fees_paid` decimal(65,4) DEFAULT NULL,
  `penalty` decimal(65,4) DEFAULT NULL,
  `penalty_waived` decimal(65,4) DEFAULT NULL,
  `penalty_written_off` decimal(65,4) DEFAULT NULL,
  `penalty_paid` decimal(65,4) DEFAULT NULL,
  `total_due` decimal(65,4) DEFAULT NULL,
  `total_paid_advance` decimal(65,4) DEFAULT NULL,
  `total_paid_late` decimal(65,4) DEFAULT NULL,
  `paid` tinyint(4) NOT NULL DEFAULT '0',
  `modified_by_id` int(11) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loan_repayment_schedules`
--

INSERT INTO `loan_repayment_schedules` (`id`, `loan_id`, `installment`, `due_date`, `from_date`, `month`, `year`, `principal`, `principal_waived`, `principal_written_off`, `principal_paid`, `interest`, `interest_waived`, `interest_written_off`, `interest_paid`, `fees`, `fees_waived`, `fees_written_off`, `fees_paid`, `penalty`, `penalty_waived`, `penalty_written_off`, `penalty_paid`, `total_due`, `total_paid_advance`, `total_paid_late`, `paid`, `modified_by_id`, `created_by_id`, `created_at`, `updated_at`) VALUES
(792, 372, NULL, '2020-09-10', NULL, '09', '2020', '0.0000', NULL, NULL, NULL, '11000.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:05:26', '2020-09-01 22:05:26'),
(793, 372, NULL, '2020-10-10', NULL, '10', '2020', '0.0000', NULL, NULL, NULL, '11000.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:05:26', '2020-09-01 22:05:26'),
(794, 372, NULL, '2020-11-10', NULL, '11', '2020', '0.0000', NULL, NULL, NULL, '11000.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:05:26', '2020-09-01 22:05:26'),
(795, 372, NULL, '2020-12-10', NULL, '12', '2020', '11111.1100', NULL, NULL, NULL, '11000.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:05:26', '2020-09-01 22:05:26'),
(796, 372, NULL, '2021-01-10', NULL, '01', '2021', '11111.1100', NULL, NULL, NULL, '9777.7800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:05:26', '2020-09-01 22:05:26'),
(797, 372, NULL, '2021-02-10', NULL, '02', '2021', '11111.1100', NULL, NULL, NULL, '8555.5600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:05:26', '2020-09-01 22:05:26'),
(798, 372, NULL, '2021-03-10', NULL, '03', '2021', '11111.1100', NULL, NULL, NULL, '7333.3300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:05:26', '2020-09-01 22:05:26'),
(799, 372, NULL, '2021-04-10', NULL, '04', '2021', '11111.1100', NULL, NULL, NULL, '6111.1100', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:05:26', '2020-09-01 22:05:26'),
(800, 372, NULL, '2021-05-10', NULL, '05', '2021', '11111.1100', NULL, NULL, NULL, '4888.8900', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:05:26', '2020-09-01 22:05:26'),
(801, 372, NULL, '2021-06-10', NULL, '06', '2021', '11111.1100', NULL, NULL, NULL, '3666.6700', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:05:26', '2020-09-01 22:05:26'),
(802, 372, NULL, '2021-07-10', NULL, '07', '2021', '11111.1100', NULL, NULL, NULL, '2444.4500', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:05:26', '2020-09-01 22:05:26'),
(803, 372, NULL, '2021-08-10', NULL, '08', '2021', '11111.1200', NULL, NULL, NULL, '1222.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:05:26', '2020-09-01 22:05:26'),
(804, 373, NULL, '2019-10-05', NULL, '10', '2019', '0.0000', NULL, NULL, '0.0000', '4042.5000', NULL, NULL, '4042.5000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:07:33', '2020-09-04 15:27:40'),
(805, 373, NULL, '2019-11-05', NULL, '11', '2019', '0.0000', NULL, NULL, '0.0000', '4042.5000', NULL, NULL, '4042.5000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:07:33', '2020-09-04 15:27:40'),
(806, 373, NULL, '2019-12-05', NULL, '12', '2019', '0.0000', NULL, NULL, '0.0000', '4042.5000', NULL, NULL, '4042.5000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:07:33', '2020-09-04 15:27:40'),
(807, 373, NULL, '2020-01-05', NULL, '01', '2020', '4491.6700', NULL, NULL, '57.5000', '4042.5000', NULL, NULL, '4042.5000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:07:33', '2020-09-04 15:27:40'),
(808, 373, NULL, '2020-02-05', NULL, '02', '2020', '4491.6700', NULL, NULL, '0.0000', '3593.3300', NULL, NULL, '72.5000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:07:33', '2020-09-04 15:27:41'),
(809, 373, NULL, '2020-03-05', NULL, '03', '2020', '4491.6700', NULL, NULL, '0.0000', '3144.1700', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:07:33', '2020-09-04 15:27:40'),
(810, 373, NULL, '2020-04-05', NULL, '04', '2020', '4491.6700', NULL, NULL, '0.0000', '2695.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:07:33', '2020-09-04 15:27:40'),
(811, 373, NULL, '2020-05-05', NULL, '05', '2020', '4491.6700', NULL, NULL, '0.0000', '2245.8300', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:07:33', '2020-09-04 15:27:40'),
(812, 373, NULL, '2020-06-05', NULL, '06', '2020', '4491.6700', NULL, NULL, '0.0000', '1796.6700', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:07:33', '2020-09-04 15:27:40'),
(813, 373, NULL, '2020-07-05', NULL, '07', '2020', '4491.6700', NULL, NULL, '0.0000', '1347.5000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:07:33', '2020-09-04 15:27:40'),
(814, 373, NULL, '2020-08-05', NULL, '08', '2020', '4491.6700', NULL, NULL, '0.0000', '898.3300', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:07:33', '2020-09-04 15:27:40'),
(815, 373, NULL, '2020-09-05', NULL, '09', '2020', '4491.6400', NULL, NULL, '0.0000', '449.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:07:33', '2020-09-04 15:27:40'),
(816, 374, NULL, '2019-08-16', NULL, '08', '2019', '0.0000', NULL, NULL, '0.0000', '5302.1400', NULL, NULL, '5302.1400', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:11:28', '2020-09-05 17:10:12'),
(817, 374, NULL, '2019-09-16', NULL, '09', '2019', '0.0000', NULL, NULL, '0.0000', '5302.1400', NULL, NULL, '5302.1400', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:11:28', '2020-09-05 17:10:12'),
(818, 374, NULL, '2019-10-16', NULL, '10', '2019', '0.0000', NULL, NULL, '0.0000', '5302.1400', NULL, NULL, '5302.1400', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:11:28', '2020-09-05 17:10:12'),
(819, 374, NULL, '2019-11-16', NULL, '11', '2019', '5891.2600', NULL, NULL, '5891.2600', '5302.1400', NULL, NULL, '5302.1400', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:11:28', '2020-09-05 17:10:12'),
(820, 374, NULL, '2019-12-16', NULL, '12', '2019', '5891.2600', NULL, NULL, '5891.2600', '4713.0100', NULL, NULL, '4713.0100', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:11:28', '2020-09-05 17:10:12'),
(821, 374, NULL, '2020-01-16', NULL, '01', '2020', '5891.2600', NULL, NULL, '5891.2600', '4123.8800', NULL, NULL, '4123.8800', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:11:28', '2020-09-05 17:10:12'),
(822, 374, NULL, '2020-02-16', NULL, '02', '2020', '5891.2600', NULL, NULL, '5891.2600', '3534.7600', NULL, NULL, '3534.7600', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:11:28', '2020-09-05 17:10:12'),
(823, 374, NULL, '2020-03-16', NULL, '03', '2020', '5891.2600', NULL, NULL, '5891.2600', '2945.6300', NULL, NULL, '2945.6300', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:11:28', '2020-09-05 17:10:12'),
(824, 374, NULL, '2020-04-16', NULL, '04', '2020', '5891.2600', NULL, NULL, '5891.2600', '2356.5100', NULL, NULL, '2356.5100', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:11:28', '2020-09-05 17:10:12'),
(825, 374, NULL, '2020-05-16', NULL, '05', '2020', '5891.2600', NULL, NULL, '5891.2600', '1767.3800', NULL, NULL, '1767.3800', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:11:28', '2020-09-05 17:10:12'),
(826, 374, NULL, '2020-06-16', NULL, '06', '2020', '5891.2600', NULL, NULL, '5891.2600', '1178.2500', NULL, NULL, '1178.2500', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:11:28', '2020-09-05 17:10:12'),
(827, 374, NULL, '2020-07-16', NULL, '07', '2020', '5891.2700', NULL, NULL, '4806.8900', '589.0000', NULL, NULL, '589.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:11:28', '2020-09-05 17:10:12'),
(828, 375, NULL, '2019-12-06', NULL, '12', '2019', '0.0000', NULL, NULL, '0.0000', '3500.0000', NULL, NULL, '3500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:12:16', '2020-09-04 17:42:54'),
(829, 375, NULL, '2020-01-06', NULL, '01', '2020', '0.0000', NULL, NULL, '0.0000', '3500.0000', NULL, NULL, '3500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:12:16', '2020-09-04 17:42:54'),
(830, 375, NULL, '2020-02-06', NULL, '02', '2020', '0.0000', NULL, NULL, '0.0000', '3500.0000', NULL, NULL, '3500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:12:16', '2020-09-04 17:42:54'),
(831, 375, NULL, '2020-03-06', NULL, '03', '2020', '7777.7800', NULL, NULL, '7777.7800', '3500.0000', NULL, NULL, '3500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:12:16', '2020-09-04 17:42:54'),
(832, 375, NULL, '2020-04-06', NULL, '04', '2020', '7777.7800', NULL, NULL, '7777.7800', '3111.1100', NULL, NULL, '3111.1100', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:12:16', '2020-09-04 17:42:54'),
(833, 375, NULL, '2020-05-06', NULL, '05', '2020', '7777.7800', NULL, NULL, '7777.7800', '2722.2200', NULL, NULL, '2722.2200', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:12:16', '2020-09-04 17:42:54'),
(834, 375, NULL, '2020-06-06', NULL, '06', '2020', '7777.7800', NULL, NULL, '7777.7800', '2333.3300', NULL, NULL, '2333.3300', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:12:16', '2020-09-04 17:42:54'),
(835, 375, NULL, '2020-07-06', NULL, '07', '2020', '7777.7800', NULL, NULL, '7777.7800', '1944.4400', NULL, NULL, '1944.4400', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:12:16', '2020-09-04 17:42:54'),
(836, 375, NULL, '2020-08-06', NULL, '08', '2020', '7777.7800', NULL, NULL, '7777.7800', '1555.5600', NULL, NULL, '1555.5600', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:12:16', '2020-09-04 17:42:54'),
(837, 375, NULL, '2020-09-06', NULL, '09', '2020', '7777.7800', NULL, NULL, '7777.7800', '1166.6700', NULL, NULL, '1166.6700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:12:16', '2020-09-04 17:42:54'),
(838, 375, NULL, '2020-10-06', NULL, '10', '2020', '7777.7800', NULL, NULL, '7777.7800', '777.7800', NULL, NULL, '777.7800', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:12:16', '2020-09-04 17:42:54'),
(839, 375, NULL, '2020-11-06', NULL, '11', '2020', '7777.7600', NULL, NULL, '7777.7600', '389.0000', NULL, NULL, '389.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:12:16', '2020-09-04 17:42:54'),
(840, 376, NULL, '2019-04-26', NULL, '04', '2019', '0.0000', NULL, NULL, '0.0000', '4000.0000', NULL, NULL, '4000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:15:01', '2020-09-04 17:10:47'),
(841, 376, NULL, '2019-05-26', NULL, '05', '2019', '0.0000', NULL, NULL, '0.0000', '4000.0000', NULL, NULL, '4000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:15:01', '2020-09-04 17:10:47'),
(842, 376, NULL, '2019-06-26', NULL, '06', '2019', '0.0000', NULL, NULL, '0.0000', '4000.0000', NULL, NULL, '4000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:15:01', '2020-09-04 17:10:47'),
(843, 376, NULL, '2019-07-26', NULL, '07', '2019', '4444.4400', NULL, NULL, '0.0000', '4000.0000', NULL, NULL, '4000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:15:01', '2020-09-04 17:10:47'),
(844, 376, NULL, '2019-08-26', NULL, '08', '2019', '4444.4400', NULL, NULL, '0.0000', '3555.5600', NULL, NULL, '3555.5600', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:15:01', '2020-09-04 17:10:47'),
(845, 376, NULL, '2019-09-26', NULL, '09', '2019', '4444.4400', NULL, NULL, '0.0000', '3111.1100', NULL, NULL, '3111.1100', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:15:01', '2020-09-04 17:10:47'),
(846, 376, NULL, '2019-10-26', NULL, '10', '2019', '4444.4400', NULL, NULL, '0.0000', '2666.6700', NULL, NULL, '2666.6700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:15:01', '2020-09-04 17:10:47'),
(847, 376, NULL, '2019-11-26', NULL, '11', '2019', '4444.4400', NULL, NULL, '0.0000', '2222.2200', NULL, NULL, '2222.2200', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:15:01', '2020-09-04 17:10:47'),
(848, 376, NULL, '2019-12-26', NULL, '12', '2019', '4444.4400', NULL, NULL, '0.0000', '1777.7800', NULL, NULL, '1777.7800', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:15:01', '2020-09-04 17:10:47'),
(849, 376, NULL, '2020-01-26', NULL, '01', '2020', '4444.4400', NULL, NULL, '0.0000', '1333.3400', NULL, NULL, '1333.3400', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:15:01', '2020-09-04 17:10:47'),
(850, 376, NULL, '2020-02-26', NULL, '02', '2020', '4444.4400', NULL, NULL, '0.0000', '888.8900', NULL, NULL, '888.8900', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:15:01', '2020-09-04 17:10:47'),
(851, 376, NULL, '2020-03-26', NULL, '03', '2020', '4444.4800', NULL, NULL, '0.0000', '444.0000', NULL, NULL, '444.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:15:01', '2020-09-04 17:10:47'),
(852, 377, NULL, '2019-10-26', NULL, '10', '2019', '0.0000', NULL, NULL, '0.0000', '13000.0000', NULL, NULL, '13000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:24:09', '2020-09-04 15:49:57'),
(853, 377, NULL, '2019-11-26', NULL, '11', '2019', '0.0000', NULL, NULL, '0.0000', '13000.0000', NULL, NULL, '13000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:24:09', '2020-09-04 15:49:57'),
(854, 377, NULL, '2019-12-26', NULL, '12', '2019', '0.0000', NULL, NULL, '0.0000', '13000.0000', NULL, NULL, '13000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:24:09', '2020-09-04 15:49:57'),
(855, 377, NULL, '2020-01-26', NULL, '01', '2020', '14444.4400', NULL, NULL, '0.0000', '13000.0000', NULL, NULL, '13000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:24:09', '2020-09-04 15:49:57'),
(856, 377, NULL, '2020-02-26', NULL, '02', '2020', '14444.4400', NULL, NULL, '0.0000', '11555.5600', NULL, NULL, '11555.5600', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:24:09', '2020-09-04 15:49:57'),
(857, 377, NULL, '2020-03-26', NULL, '03', '2020', '14444.4400', NULL, NULL, '0.0000', '10111.1100', NULL, NULL, '10111.1100', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:24:09', '2020-09-04 15:49:57'),
(858, 377, NULL, '2020-04-26', NULL, '04', '2020', '14444.4400', NULL, NULL, '0.0000', '8666.6700', NULL, NULL, '8666.6700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:24:09', '2020-09-04 15:49:57'),
(859, 377, NULL, '2020-05-26', NULL, '05', '2020', '14444.4400', NULL, NULL, '0.0000', '7222.2200', NULL, NULL, '6666.6600', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:24:09', '2020-09-04 15:49:57'),
(860, 377, NULL, '2020-06-26', NULL, '06', '2020', '14444.4400', NULL, NULL, '0.0000', '5777.7800', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:24:09', '2020-09-04 15:49:57'),
(861, 377, NULL, '2020-07-26', NULL, '07', '2020', '14444.4400', NULL, NULL, '0.0000', '4333.3400', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:24:09', '2020-09-04 15:49:57'),
(862, 377, NULL, '2020-08-26', NULL, '08', '2020', '14444.4400', NULL, NULL, '0.0000', '2888.8900', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:24:09', '2020-09-04 15:49:57'),
(863, 377, NULL, '2020-09-26', NULL, '09', '2020', '14444.4800', NULL, NULL, '0.0000', '1444.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:24:09', '2020-09-04 15:49:57'),
(864, 378, NULL, '2019-12-07', NULL, '12', '2019', '0.0000', NULL, NULL, '0.0000', '7200.0000', NULL, NULL, '7200.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:26:30', '2020-09-04 18:24:38'),
(865, 378, NULL, '2020-01-07', NULL, '01', '2020', '0.0000', NULL, NULL, '0.0000', '7200.0000', NULL, NULL, '7200.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:26:30', '2020-09-04 18:24:38'),
(866, 378, NULL, '2020-02-07', NULL, '02', '2020', '0.0000', NULL, NULL, '0.0000', '7200.0000', NULL, NULL, '7200.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:26:30', '2020-09-04 18:24:38'),
(867, 378, NULL, '2020-03-07', NULL, '03', '2020', '8000.0000', NULL, NULL, '8000.0000', '7200.0000', NULL, NULL, '7200.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:26:30', '2020-09-04 18:24:38'),
(868, 378, NULL, '2020-04-07', NULL, '04', '2020', '8000.0000', NULL, NULL, '8000.0000', '6400.0000', NULL, NULL, '634.5000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:26:30', '2020-09-04 18:24:38'),
(869, 378, NULL, '2020-05-07', NULL, '05', '2020', '8000.0000', NULL, NULL, '8000.0000', '5600.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:26:30', '2020-09-04 18:24:38'),
(870, 378, NULL, '2020-06-07', NULL, '06', '2020', '8000.0000', NULL, NULL, '8000.0000', '4800.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:26:30', '2020-09-04 18:24:38'),
(871, 378, NULL, '2020-07-07', NULL, '07', '2020', '8000.0000', NULL, NULL, '8000.0000', '4000.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:26:30', '2020-09-04 18:24:38'),
(872, 378, NULL, '2020-08-07', NULL, '08', '2020', '8000.0000', NULL, NULL, '8000.0000', '3200.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:26:30', '2020-09-04 18:24:38'),
(873, 378, NULL, '2020-09-07', NULL, '09', '2020', '8000.0000', NULL, NULL, '1199.6000', '2400.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:26:30', '2020-09-04 18:24:38'),
(874, 378, NULL, '2020-10-07', NULL, '10', '2020', '8000.0000', NULL, NULL, '0.0000', '1600.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:26:30', '2020-09-04 18:24:38'),
(875, 378, NULL, '2020-11-07', NULL, '11', '2020', '8000.0000', NULL, NULL, '0.0000', '800.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:26:30', '2020-09-04 18:24:38'),
(876, 379, NULL, '2020-09-12', NULL, '09', '2020', '0.0000', NULL, NULL, '0.0000', '27500.0000', NULL, NULL, '27500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:29:03', '2022-10-05 15:19:13'),
(877, 379, NULL, '2020-10-12', NULL, '10', '2020', '0.0000', NULL, NULL, '0.0000', '27500.0000', NULL, NULL, '27500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:29:03', '2022-10-05 15:19:13'),
(878, 379, NULL, '2020-11-12', NULL, '11', '2020', '0.0000', NULL, NULL, '0.0000', '27500.0000', NULL, NULL, '27500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:29:03', '2022-10-05 15:19:13'),
(879, 379, NULL, '2020-12-12', NULL, '12', '2020', '27777.7800', NULL, NULL, '27777.7800', '27500.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:29:03', '2022-10-05 15:19:13'),
(880, 379, NULL, '2021-01-12', NULL, '01', '2021', '27777.7800', NULL, NULL, '27777.7800', '24444.4400', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:29:03', '2022-10-05 15:19:13'),
(881, 379, NULL, '2021-02-12', NULL, '02', '2021', '27777.7800', NULL, NULL, '27777.7800', '21388.8900', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:29:03', '2022-10-05 15:19:13'),
(882, 379, NULL, '2021-03-12', NULL, '03', '2021', '27777.7800', NULL, NULL, '27777.7800', '18333.3300', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:29:03', '2022-10-05 15:19:13'),
(883, 379, NULL, '2021-04-12', NULL, '04', '2021', '27777.7800', NULL, NULL, '27777.7800', '15277.7800', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:29:03', '2022-10-05 15:19:13'),
(884, 379, NULL, '2021-05-12', NULL, '05', '2021', '27777.7800', NULL, NULL, '27777.7800', '12222.2200', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:29:03', '2022-10-05 15:19:13'),
(885, 379, NULL, '2021-06-12', NULL, '06', '2021', '27777.7800', NULL, NULL, '27777.7800', '9166.6700', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:29:03', '2022-10-05 15:19:13'),
(886, 379, NULL, '2021-07-12', NULL, '07', '2021', '27777.7800', NULL, NULL, '27777.7800', '6111.1100', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:29:03', '2022-10-05 15:19:13'),
(887, 379, NULL, '2021-08-12', NULL, '08', '2021', '27777.7600', NULL, NULL, '27777.7600', '3056.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:29:03', '2022-10-05 15:19:13'),
(888, 380, NULL, '2019-10-25', NULL, '10', '2019', '0.0000', NULL, NULL, '0.0000', '7075.0000', NULL, NULL, '7075.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:36:22', '2020-09-03 23:14:46'),
(889, 380, NULL, '2019-11-25', NULL, '11', '2019', '0.0000', NULL, NULL, '0.0000', '7075.0000', NULL, NULL, '7075.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:36:22', '2020-09-03 23:14:46'),
(890, 380, NULL, '2019-12-25', NULL, '12', '2019', '0.0000', NULL, NULL, '0.0000', '7075.0000', NULL, NULL, '7075.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:36:22', '2020-09-03 23:14:46'),
(891, 380, NULL, '2020-01-25', NULL, '01', '2020', '7861.1100', NULL, NULL, '0.0000', '7075.0000', NULL, NULL, '7075.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:36:22', '2020-09-03 23:14:46'),
(892, 380, NULL, '2020-02-25', NULL, '02', '2020', '7861.1100', NULL, NULL, '0.0000', '6288.8900', NULL, NULL, '6288.8900', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:36:22', '2020-09-03 23:14:46'),
(893, 380, NULL, '2020-03-25', NULL, '03', '2020', '7861.1100', NULL, NULL, '0.0000', '5502.7800', NULL, NULL, '5502.7800', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:36:22', '2020-09-03 23:14:46'),
(894, 380, NULL, '2020-04-25', NULL, '04', '2020', '7861.1100', NULL, NULL, '0.0000', '4716.6700', NULL, NULL, '4716.6700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:36:22', '2020-09-03 23:14:46'),
(895, 380, NULL, '2020-05-25', NULL, '05', '2020', '7861.1100', NULL, NULL, '0.0000', '3930.5600', NULL, NULL, '3930.5600', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:36:22', '2020-09-03 23:14:46'),
(896, 380, NULL, '2020-06-25', NULL, '06', '2020', '7861.1100', NULL, NULL, '0.0000', '3144.4500', NULL, NULL, '3144.4500', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:36:22', '2020-09-03 23:14:46'),
(897, 380, NULL, '2020-07-25', NULL, '07', '2020', '7861.1100', NULL, NULL, '0.0000', '2358.3300', NULL, NULL, '2358.3300', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:36:22', '2020-09-03 23:14:46'),
(898, 380, NULL, '2020-08-25', NULL, '08', '2020', '7861.1100', NULL, NULL, '0.0000', '1572.2200', NULL, NULL, '1572.2200', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:36:22', '2020-09-03 23:14:46'),
(899, 380, NULL, '2020-09-25', NULL, '09', '2020', '7861.1200', NULL, NULL, '0.0000', '786.0000', NULL, NULL, '786.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:36:22', '2020-09-03 23:14:46'),
(900, 381, NULL, '2019-07-20', NULL, '07', '2019', '0.0000', NULL, NULL, '0.0000', '5507.5800', NULL, NULL, '5507.5800', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:38:30', '2020-09-04 16:43:56'),
(901, 381, NULL, '2019-08-20', NULL, '08', '2019', '0.0000', NULL, NULL, '0.0000', '5507.5800', NULL, NULL, '5507.5800', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:38:30', '2020-09-04 16:43:57'),
(902, 381, NULL, '2019-09-20', NULL, '09', '2019', '0.0000', NULL, NULL, '0.0000', '5507.5800', NULL, NULL, '5507.5800', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:38:30', '2020-09-04 16:43:57'),
(903, 381, NULL, '2019-10-20', NULL, '10', '2019', '6119.5300', NULL, NULL, '6119.5300', '5507.5800', NULL, NULL, '5507.5800', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:38:30', '2020-09-04 16:43:57'),
(904, 381, NULL, '2019-11-20', NULL, '11', '2019', '6119.5300', NULL, NULL, '6119.5300', '4895.6300', NULL, NULL, '4895.6300', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:38:30', '2020-09-04 16:43:57'),
(905, 381, NULL, '2019-12-20', NULL, '12', '2019', '6119.5300', NULL, NULL, '6119.5300', '4283.6700', NULL, NULL, '4283.6700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:38:30', '2020-09-04 16:43:57'),
(906, 381, NULL, '2020-01-20', NULL, '01', '2020', '6119.5300', NULL, NULL, '6119.5300', '3671.7200', NULL, NULL, '3671.7200', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:38:30', '2020-09-04 16:43:57'),
(907, 381, NULL, '2020-02-20', NULL, '02', '2020', '6119.5300', NULL, NULL, '6119.5300', '3059.7700', NULL, NULL, '3059.7700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:38:30', '2020-09-04 16:43:57'),
(908, 381, NULL, '2020-03-20', NULL, '03', '2020', '6119.5300', NULL, NULL, '6119.5300', '2447.8200', NULL, NULL, '2447.8200', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:38:30', '2020-09-04 16:43:57'),
(909, 381, NULL, '2020-04-20', NULL, '04', '2020', '6119.5300', NULL, NULL, '52.7900', '1835.8600', NULL, NULL, '1835.8600', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:38:30', '2020-09-04 16:43:57'),
(910, 381, NULL, '2020-05-20', NULL, '05', '2020', '6119.5300', NULL, NULL, '0.0000', '1223.9100', NULL, NULL, '1223.9100', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:38:30', '2020-09-04 16:43:57'),
(911, 381, NULL, '2020-06-20', NULL, '06', '2020', '6119.5600', NULL, NULL, '0.0000', '612.0000', NULL, NULL, '612.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:38:30', '2020-09-04 16:43:57'),
(912, 382, NULL, '2019-10-25', NULL, '10', '2019', '0.0000', NULL, NULL, '0.0000', '7075.0000', NULL, NULL, '7075.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:42:17', '2020-09-03 23:48:11'),
(913, 382, NULL, '2019-11-25', NULL, '11', '2019', '0.0000', NULL, NULL, '0.0000', '7075.0000', NULL, NULL, '7075.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:42:17', '2020-09-03 23:48:11'),
(914, 382, NULL, '2019-12-25', NULL, '12', '2019', '0.0000', NULL, NULL, '0.0000', '7075.0000', NULL, NULL, '7075.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:42:17', '2020-09-03 23:48:11'),
(915, 382, NULL, '2020-01-25', NULL, '01', '2020', '7861.1100', NULL, NULL, '0.0000', '7075.0000', NULL, NULL, '7075.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:42:17', '2020-09-03 23:48:11'),
(916, 382, NULL, '2020-02-25', NULL, '02', '2020', '7861.1100', NULL, NULL, '0.0000', '6288.8900', NULL, NULL, '6288.8900', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:42:17', '2020-09-03 23:48:11'),
(917, 382, NULL, '2020-03-25', NULL, '03', '2020', '7861.1100', NULL, NULL, '0.0000', '5502.7800', NULL, NULL, '5502.7800', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:42:17', '2020-09-03 23:48:11'),
(918, 382, NULL, '2020-04-25', NULL, '04', '2020', '7861.1100', NULL, NULL, '0.0000', '4716.6700', NULL, NULL, '4716.6700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:42:17', '2020-09-03 23:48:11'),
(919, 382, NULL, '2020-05-25', NULL, '05', '2020', '7861.1100', NULL, NULL, '0.0000', '3930.5600', NULL, NULL, '3930.5600', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:42:17', '2020-09-03 23:48:11'),
(920, 382, NULL, '2020-06-25', NULL, '06', '2020', '7861.1100', NULL, NULL, '0.0000', '3144.4500', NULL, NULL, '3144.4500', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:42:17', '2020-09-03 23:48:11'),
(921, 382, NULL, '2020-07-25', NULL, '07', '2020', '7861.1100', NULL, NULL, '0.0000', '2358.3300', NULL, NULL, '2358.3300', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:42:17', '2020-09-03 23:48:11'),
(922, 382, NULL, '2020-08-25', NULL, '08', '2020', '7861.1100', NULL, NULL, '0.0000', '1572.2200', NULL, NULL, '1572.2200', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:42:17', '2020-09-03 23:48:11'),
(923, 382, NULL, '2020-09-25', NULL, '09', '2020', '7861.1200', NULL, NULL, '0.0000', '786.0000', NULL, NULL, '786.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:42:17', '2020-09-03 23:48:11'),
(924, 383, NULL, '2019-08-04', NULL, '08', '2019', '0.0000', NULL, NULL, '0.0000', '1344.6000', NULL, NULL, '1344.6000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:01', '2020-09-05 18:07:34'),
(925, 383, NULL, '2019-09-04', NULL, '09', '2019', '0.0000', NULL, NULL, '0.0000', '1344.6000', NULL, NULL, '1344.6000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:01', '2020-09-05 18:07:34'),
(926, 383, NULL, '2019-10-04', NULL, '10', '2019', '0.0000', NULL, NULL, '0.0000', '1344.6000', NULL, NULL, '1344.6000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:01', '2020-09-05 18:07:34'),
(927, 383, NULL, '2019-11-04', NULL, '11', '2019', '1494.0000', NULL, NULL, '1494.0000', '1344.6000', NULL, NULL, '1344.6000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:01', '2020-09-05 18:07:34'),
(928, 383, NULL, '2019-12-04', NULL, '12', '2019', '1494.0000', NULL, NULL, '1494.0000', '1195.2000', NULL, NULL, '1195.2000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:01', '2020-09-05 18:07:34'),
(929, 383, NULL, '2020-01-04', NULL, '01', '2020', '1494.0000', NULL, NULL, '1494.0000', '1045.8000', NULL, NULL, '1045.8000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:01', '2020-09-05 18:07:34'),
(930, 383, NULL, '2020-02-04', NULL, '02', '2020', '1494.0000', NULL, NULL, '1494.0000', '896.4000', NULL, NULL, '896.4000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:01', '2020-09-05 18:07:34'),
(931, 383, NULL, '2020-03-04', NULL, '03', '2020', '1494.0000', NULL, NULL, '1494.0000', '747.0000', NULL, NULL, '747.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:01', '2020-09-05 18:07:34'),
(932, 383, NULL, '2020-04-04', NULL, '04', '2020', '1494.0000', NULL, NULL, '1494.0000', '597.6000', NULL, NULL, '597.6000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:01', '2020-09-05 18:07:34'),
(933, 383, NULL, '2020-05-04', NULL, '05', '2020', '1494.0000', NULL, NULL, '1494.0000', '448.2000', NULL, NULL, '448.2000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:01', '2020-09-05 18:07:34'),
(934, 383, NULL, '2020-06-04', NULL, '06', '2020', '1494.0000', NULL, NULL, '1494.0000', '298.8000', NULL, NULL, '298.8000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:01', '2020-09-05 18:07:34'),
(935, 383, NULL, '2020-07-04', NULL, '07', '2020', '1494.0000', NULL, NULL, '1433.0400', '149.0000', NULL, NULL, '149.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:01', '2020-09-05 18:07:34'),
(936, 384, NULL, '2019-05-03', NULL, '05', '2019', '0.0000', NULL, NULL, NULL, '22067.5000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:26', '2020-09-01 22:43:26'),
(937, 384, NULL, '2019-06-03', NULL, '06', '2019', '0.0000', NULL, NULL, NULL, '22067.5000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:26', '2020-09-01 22:43:26'),
(938, 384, NULL, '2019-07-03', NULL, '07', '2019', '0.0000', NULL, NULL, NULL, '22067.5000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:26', '2020-09-01 22:43:26'),
(939, 384, NULL, '2019-08-03', NULL, '08', '2019', '24519.4400', NULL, NULL, NULL, '22067.5000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:26', '2020-09-01 22:43:26'),
(940, 384, NULL, '2019-09-03', NULL, '09', '2019', '24519.4400', NULL, NULL, NULL, '19615.5600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:26', '2020-09-01 22:43:26'),
(941, 384, NULL, '2019-10-03', NULL, '10', '2019', '24519.4400', NULL, NULL, NULL, '17163.6100', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:26', '2020-09-01 22:43:26'),
(942, 384, NULL, '2019-11-03', NULL, '11', '2019', '24519.4400', NULL, NULL, NULL, '14711.6700', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:26', '2020-09-01 22:43:26'),
(943, 384, NULL, '2019-12-03', NULL, '12', '2019', '24519.4400', NULL, NULL, NULL, '12259.7200', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:26', '2020-09-01 22:43:26'),
(944, 384, NULL, '2020-01-03', NULL, '01', '2020', '24519.4400', NULL, NULL, NULL, '9807.7800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:26', '2020-09-01 22:43:26'),
(945, 384, NULL, '2020-02-03', NULL, '02', '2020', '24519.4400', NULL, NULL, NULL, '7355.8400', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:26', '2020-09-01 22:43:26'),
(946, 384, NULL, '2020-03-03', NULL, '03', '2020', '24519.4400', NULL, NULL, NULL, '4903.8900', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:26', '2020-09-01 22:43:26'),
(947, 384, NULL, '2020-04-03', NULL, '04', '2020', '24519.4800', NULL, NULL, NULL, '2452.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:26', '2020-09-01 22:43:26'),
(948, 385, NULL, '2020-02-02', NULL, '02', '2020', '0.0000', NULL, NULL, '0.0000', '2800.0000', NULL, NULL, '2800.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:59', '2020-09-03 23:59:43'),
(949, 385, NULL, '2020-03-02', NULL, '03', '2020', '0.0000', NULL, NULL, '0.0000', '2800.0000', NULL, NULL, '2800.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:59', '2020-09-03 23:59:43'),
(950, 385, NULL, '2020-04-02', NULL, '04', '2020', '0.0000', NULL, NULL, '0.0000', '2800.0000', NULL, NULL, '2800.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:59', '2020-09-03 23:59:43'),
(951, 385, NULL, '2020-05-02', NULL, '05', '2020', '3888.8900', NULL, NULL, '136.8600', '2800.0000', NULL, NULL, '2800.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:59', '2020-09-03 23:59:44'),
(952, 385, NULL, '2020-06-02', NULL, '06', '2020', '3888.8900', NULL, NULL, '0.0000', '2488.8900', NULL, NULL, '2488.8900', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:59', '2020-09-03 23:59:43'),
(953, 385, NULL, '2020-07-02', NULL, '07', '2020', '3888.8900', NULL, NULL, '0.0000', '2177.7800', NULL, NULL, '2177.7800', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:59', '2020-09-03 23:59:43'),
(954, 385, NULL, '2020-08-02', NULL, '08', '2020', '3888.8900', NULL, NULL, '0.0000', '1866.6700', NULL, NULL, '1866.6700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:59', '2020-09-03 23:59:43'),
(955, 385, NULL, '2020-09-02', NULL, '09', '2020', '3888.8900', NULL, NULL, '0.0000', '1555.5600', NULL, NULL, '1555.5600', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:59', '2020-09-03 23:59:43'),
(956, 385, NULL, '2020-10-02', NULL, '10', '2020', '3888.8900', NULL, NULL, '0.0000', '1244.4400', NULL, NULL, '1244.4400', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:59', '2020-09-03 23:59:43'),
(957, 385, NULL, '2020-11-02', NULL, '11', '2020', '3888.8900', NULL, NULL, '0.0000', '933.3300', NULL, NULL, '933.3300', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:59', '2020-09-03 23:59:44'),
(958, 385, NULL, '2020-12-02', NULL, '12', '2020', '3888.8900', NULL, NULL, '0.0000', '622.2200', NULL, NULL, '622.2200', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:59', '2020-09-03 23:59:44'),
(959, 385, NULL, '2021-01-02', NULL, '01', '2021', '3888.8800', NULL, NULL, '0.0000', '311.0000', NULL, NULL, '311.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:43:59', '2020-09-03 23:59:44'),
(972, 388, NULL, '2019-03-22', NULL, '03', '2019', '0.0000', NULL, NULL, '0.0000', '5000.0000', NULL, NULL, '5000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:15', '2020-09-04 18:10:16'),
(973, 388, NULL, '2019-04-22', NULL, '04', '2019', '0.0000', NULL, NULL, '0.0000', '5000.0000', NULL, NULL, '5000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:15', '2020-09-04 18:10:16'),
(974, 388, NULL, '2019-05-22', NULL, '05', '2019', '0.0000', NULL, NULL, '0.0000', '5000.0000', NULL, NULL, '5000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:15', '2020-09-04 18:10:16'),
(975, 388, NULL, '2019-06-22', NULL, '06', '2019', '5555.5600', NULL, NULL, '5555.5600', '5000.0000', NULL, NULL, '5000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:15', '2020-09-04 18:10:16'),
(976, 388, NULL, '2019-07-22', NULL, '07', '2019', '5555.5600', NULL, NULL, '5555.5600', '4444.4400', NULL, NULL, '4444.4400', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:15', '2020-09-04 18:10:16'),
(977, 388, NULL, '2019-08-22', NULL, '08', '2019', '5555.5600', NULL, NULL, '5555.5600', '3888.8900', NULL, NULL, '3888.8900', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:15', '2020-09-04 18:10:16'),
(978, 388, NULL, '2019-09-22', NULL, '09', '2019', '5555.5600', NULL, NULL, '5555.5600', '3333.3300', NULL, NULL, '3333.3300', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:15', '2020-09-04 18:10:16'),
(979, 388, NULL, '2019-10-22', NULL, '10', '2019', '5555.5600', NULL, NULL, '5555.5600', '2777.7800', NULL, NULL, '2777.7800', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:15', '2020-09-04 18:10:16'),
(980, 388, NULL, '2019-11-22', NULL, '11', '2019', '5555.5600', NULL, NULL, '5555.5600', '2222.2200', NULL, NULL, '2222.2200', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:15', '2020-09-04 18:10:17'),
(981, 388, NULL, '2019-12-22', NULL, '12', '2019', '5555.5600', NULL, NULL, '2066.3100', '1666.6600', NULL, NULL, '1666.6600', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:15', '2020-09-04 18:10:17'),
(982, 388, NULL, '2020-01-22', NULL, '01', '2020', '5555.5600', NULL, NULL, '0.0000', '1111.1100', NULL, NULL, '1111.1100', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:15', '2020-09-04 18:10:17'),
(983, 388, NULL, '2020-02-22', NULL, '02', '2020', '5555.5200', NULL, NULL, '0.0000', '556.0000', NULL, NULL, '556.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:15', '2020-09-04 18:10:17'),
(984, 389, NULL, '2020-03-05', NULL, '03', '2020', '0.0000', NULL, NULL, '0.0000', '3418.5000', NULL, NULL, '3418.5000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:58', '2020-09-03 23:43:27'),
(985, 389, NULL, '2020-04-05', NULL, '04', '2020', '0.0000', NULL, NULL, '0.0000', '3418.5000', NULL, NULL, '3418.5000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:58', '2020-09-03 23:43:27'),
(986, 389, NULL, '2020-05-05', NULL, '05', '2020', '0.0000', NULL, NULL, '0.0000', '3418.5000', NULL, NULL, '3418.5000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:58', '2020-09-03 23:43:27'),
(987, 389, NULL, '2020-06-05', NULL, '06', '2020', '3798.3300', NULL, NULL, '425.2000', '3418.5000', NULL, NULL, '3418.5000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:58', '2020-09-03 23:43:27'),
(988, 389, NULL, '2020-07-05', NULL, '07', '2020', '3798.3300', NULL, NULL, '0.0000', '3038.6700', NULL, NULL, '3038.6700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:58', '2020-09-03 23:43:27'),
(989, 389, NULL, '2020-08-05', NULL, '08', '2020', '3798.3300', NULL, NULL, '0.0000', '2658.8300', NULL, NULL, '2658.8300', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:58', '2020-09-03 23:43:27'),
(990, 389, NULL, '2020-09-05', NULL, '09', '2020', '3798.3300', NULL, NULL, '0.0000', '2279.0000', NULL, NULL, '2279.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:58', '2020-09-03 23:43:27'),
(991, 389, NULL, '2020-10-05', NULL, '10', '2020', '3798.3300', NULL, NULL, '0.0000', '1899.1700', NULL, NULL, '1899.1700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:58', '2020-09-03 23:43:27'),
(992, 389, NULL, '2020-11-05', NULL, '11', '2020', '3798.3300', NULL, NULL, '0.0000', '1519.3400', NULL, NULL, '1519.3400', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:58', '2020-09-03 23:43:27'),
(993, 389, NULL, '2020-12-05', NULL, '12', '2020', '3798.3300', NULL, NULL, '0.0000', '1139.5000', NULL, NULL, '1139.5000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:58', '2020-09-03 23:43:27'),
(994, 389, NULL, '2021-01-05', NULL, '01', '2021', '3798.3300', NULL, NULL, '0.0000', '759.6700', NULL, NULL, '759.6700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:58', '2020-09-03 23:43:27'),
(995, 389, NULL, '2021-02-05', NULL, '02', '2021', '3798.3600', NULL, NULL, '0.0000', '380.0000', NULL, NULL, '380.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:45:58', '2020-09-03 23:43:27'),
(996, 403, NULL, '2018-11-02', '2019-01-05', '11', '2018', '0.0000', NULL, NULL, '0.0000', '10500.0000', NULL, NULL, '10500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, '10500.0000', 0, NULL, NULL, '2020-09-01 22:46:40', '2020-09-09 01:47:04');
INSERT INTO `loan_repayment_schedules` (`id`, `loan_id`, `installment`, `due_date`, `from_date`, `month`, `year`, `principal`, `principal_waived`, `principal_written_off`, `principal_paid`, `interest`, `interest_waived`, `interest_written_off`, `interest_paid`, `fees`, `fees_waived`, `fees_written_off`, `fees_paid`, `penalty`, `penalty_waived`, `penalty_written_off`, `penalty_paid`, `total_due`, `total_paid_advance`, `total_paid_late`, `paid`, `modified_by_id`, `created_by_id`, `created_at`, `updated_at`) VALUES
(997, 403, NULL, '2018-12-02', '2019-01-05', '12', '2018', '0.0000', NULL, NULL, '0.0000', '10500.0000', NULL, NULL, '10500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, '10500.0000', 0, NULL, NULL, '2020-09-01 22:46:40', '2020-09-09 01:47:04'),
(998, 403, NULL, '2019-01-02', NULL, '01', '2019', '0.0000', NULL, NULL, '0.0000', '10500.0000', NULL, NULL, '10500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:46:40', '2020-09-09 01:47:05'),
(999, 403, NULL, '2019-02-02', NULL, '02', '2019', '11666.6700', NULL, NULL, '0.0000', '10500.0000', NULL, NULL, '10500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:46:40', '2020-09-09 01:47:05'),
(1000, 403, NULL, '2019-03-02', NULL, '03', '2019', '11666.6700', NULL, NULL, '0.0000', '9333.3300', NULL, NULL, '9333.3300', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:46:40', '2020-09-09 01:47:05'),
(1001, 403, NULL, '2019-04-02', NULL, '04', '2019', '11666.6700', NULL, NULL, '0.0000', '8166.6700', NULL, NULL, '8166.6700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:46:40', '2020-09-09 01:47:05'),
(1002, 403, NULL, '2019-05-02', NULL, '05', '2019', '11666.6700', NULL, NULL, '0.0000', '7000.0000', NULL, NULL, '7000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:46:40', '2020-09-09 01:47:05'),
(1003, 403, NULL, '2019-06-02', NULL, '06', '2019', '11666.6700', NULL, NULL, '0.0000', '5833.3300', NULL, NULL, '5833.3300', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:46:40', '2020-09-09 01:47:05'),
(1004, 403, NULL, '2019-07-02', NULL, '07', '2019', '11666.6700', NULL, NULL, '0.0000', '4666.6700', NULL, NULL, '4666.6700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:46:40', '2020-09-09 01:47:05'),
(1005, 403, NULL, '2019-08-02', NULL, '08', '2019', '11666.6700', NULL, NULL, '0.0000', '3500.0000', NULL, NULL, '3500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:46:40', '2020-09-09 01:47:05'),
(1006, 403, NULL, '2019-09-02', NULL, '09', '2019', '11666.6700', NULL, NULL, '0.0000', '2333.3300', NULL, NULL, '2333.3300', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:46:40', '2020-09-09 01:47:05'),
(1007, 403, NULL, '2019-10-02', NULL, '10', '2019', '11666.6400', NULL, NULL, '0.0000', '1167.0000', NULL, NULL, '1167.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:46:40', '2020-09-09 01:47:05'),
(1008, 405, NULL, '2019-04-14', NULL, '04', '2019', '0.0000', NULL, NULL, '0.0000', '9630.0000', NULL, NULL, '9630.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:50:52', '2020-09-04 17:18:48'),
(1009, 405, NULL, '2019-05-14', NULL, '05', '2019', '0.0000', NULL, NULL, '0.0000', '9630.0000', NULL, NULL, '9630.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:50:52', '2020-09-04 17:18:48'),
(1010, 405, NULL, '2019-06-14', NULL, '06', '2019', '0.0000', NULL, NULL, '0.0000', '9630.0000', NULL, NULL, '9630.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:50:52', '2020-09-04 17:18:48'),
(1011, 405, NULL, '2019-07-14', NULL, '07', '2019', '10700.0000', NULL, NULL, '0.0000', '9630.0000', NULL, NULL, '1110.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:50:52', '2020-09-04 17:18:48'),
(1012, 405, NULL, '2019-08-14', NULL, '08', '2019', '10700.0000', NULL, NULL, '0.0000', '8560.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:50:52', '2020-09-04 17:18:48'),
(1013, 405, NULL, '2019-09-14', NULL, '09', '2019', '10700.0000', NULL, NULL, '0.0000', '7490.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:50:52', '2020-09-04 17:18:48'),
(1014, 405, NULL, '2019-10-14', NULL, '10', '2019', '10700.0000', NULL, NULL, '0.0000', '6420.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:50:52', '2020-09-04 17:18:48'),
(1015, 405, NULL, '2019-11-14', NULL, '11', '2019', '10700.0000', NULL, NULL, '0.0000', '5350.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:50:52', '2020-09-04 17:18:48'),
(1016, 405, NULL, '2019-12-14', NULL, '12', '2019', '10700.0000', NULL, NULL, '0.0000', '4280.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:50:52', '2020-09-04 17:18:48'),
(1017, 405, NULL, '2020-01-14', NULL, '01', '2020', '10700.0000', NULL, NULL, '0.0000', '3210.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:50:52', '2020-09-04 17:18:48'),
(1018, 405, NULL, '2020-02-14', NULL, '02', '2020', '10700.0000', NULL, NULL, '0.0000', '2140.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:50:52', '2020-09-04 17:18:48'),
(1019, 405, NULL, '2020-03-14', NULL, '03', '2020', '10700.0000', NULL, NULL, '0.0000', '1070.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:50:52', '2020-09-04 17:18:48'),
(1020, 404, NULL, '2020-04-03', NULL, '04', '2020', '0.0000', NULL, NULL, '0.0000', '2304.0000', NULL, NULL, '2304.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:51:26', '2020-09-03 23:16:52'),
(1021, 404, NULL, '2020-05-03', NULL, '05', '2020', '0.0000', NULL, NULL, '0.0000', '2304.0000', NULL, NULL, '2304.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:51:26', '2020-09-03 23:16:52'),
(1022, 404, NULL, '2020-06-03', NULL, '06', '2020', '0.0000', NULL, NULL, '0.0000', '2304.0000', NULL, NULL, '2304.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:51:26', '2020-09-03 23:16:52'),
(1023, 404, NULL, '2020-07-03', NULL, '07', '2020', '2133.3300', NULL, NULL, '2133.3300', '2304.0000', NULL, NULL, '2040.3700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:51:26', '2020-09-03 23:16:52'),
(1024, 404, NULL, '2020-08-03', NULL, '08', '2020', '2133.3300', NULL, NULL, '2133.3300', '2048.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:51:26', '2020-09-03 23:16:52'),
(1025, 404, NULL, '2020-09-03', NULL, '09', '2020', '2133.3300', NULL, NULL, '2133.3300', '1792.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:51:26', '2020-09-03 23:16:52'),
(1026, 404, NULL, '2020-10-03', NULL, '10', '2020', '2133.3300', NULL, NULL, '2133.3300', '1536.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:51:26', '2020-09-03 23:16:52'),
(1027, 404, NULL, '2020-11-03', NULL, '11', '2020', '2133.3300', NULL, NULL, '2133.3300', '1280.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:51:26', '2020-09-03 23:16:52'),
(1028, 404, NULL, '2020-12-03', NULL, '12', '2020', '2133.3300', NULL, NULL, '2133.3300', '1024.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:51:26', '2020-09-03 23:16:52'),
(1029, 404, NULL, '2021-01-03', NULL, '01', '2021', '2133.3300', NULL, NULL, '2133.3300', '768.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:51:26', '2020-09-03 23:16:52'),
(1030, 404, NULL, '2021-02-03', NULL, '02', '2021', '2133.3300', NULL, NULL, '2133.3300', '512.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:51:26', '2020-09-03 23:16:52'),
(1031, 404, NULL, '2021-03-03', NULL, '03', '2021', '2133.3600', NULL, NULL, '2133.3600', '256.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:51:26', '2020-09-03 23:16:52'),
(1032, 406, NULL, '2025-03-11', NULL, '03', '2025', '0.0000', NULL, NULL, '0.0000', '3932.5000', NULL, NULL, '3932.5000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:06', '2020-09-04 21:50:01'),
(1033, 406, NULL, '2025-04-11', NULL, '04', '2025', '0.0000', NULL, NULL, '0.0000', '3932.5000', NULL, NULL, '3932.5000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:06', '2020-09-04 21:50:01'),
(1034, 406, NULL, '2025-05-11', NULL, '05', '2025', '0.0000', NULL, NULL, '0.0000', '3932.5000', NULL, NULL, '3932.5000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:06', '2020-09-04 21:50:02'),
(1035, 406, NULL, '2025-06-11', NULL, '06', '2025', '4369.4400', NULL, NULL, '4369.4400', '3932.5000', NULL, NULL, '3932.5000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:06', '2020-09-04 21:50:02'),
(1036, 406, NULL, '2025-07-11', NULL, '07', '2025', '4369.4400', NULL, NULL, '4154.3900', '3495.5600', NULL, NULL, '3495.5600', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:06', '2020-09-04 21:50:02'),
(1037, 406, NULL, '2025-08-11', NULL, '08', '2025', '4369.4400', NULL, NULL, '0.0000', '3058.6100', NULL, NULL, '3058.6100', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:06', '2020-09-04 21:50:02'),
(1038, 406, NULL, '2025-09-11', NULL, '09', '2025', '4369.4400', NULL, NULL, '0.0000', '2621.6700', NULL, NULL, '2621.6700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:06', '2020-09-04 21:50:02'),
(1039, 406, NULL, '2025-10-11', NULL, '10', '2025', '4369.4400', NULL, NULL, '0.0000', '2184.7200', NULL, NULL, '2184.7200', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:06', '2020-09-04 21:50:02'),
(1040, 406, NULL, '2025-11-11', NULL, '11', '2025', '4369.4400', NULL, NULL, '0.0000', '1747.7800', NULL, NULL, '1747.7800', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:06', '2020-09-04 21:50:02'),
(1041, 406, NULL, '2025-12-11', NULL, '12', '2025', '4369.4400', NULL, NULL, '0.0000', '1310.8400', NULL, NULL, '1310.8400', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:06', '2020-09-04 21:50:02'),
(1042, 406, NULL, '2026-01-11', NULL, '01', '2026', '4369.4400', NULL, NULL, '0.0000', '873.8900', NULL, NULL, '873.8900', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:06', '2020-09-04 21:50:02'),
(1043, 406, NULL, '2026-02-11', NULL, '02', '2026', '4369.4800', NULL, NULL, '0.0000', '437.0000', NULL, NULL, '437.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:06', '2020-09-04 21:50:02'),
(1044, 390, NULL, '2019-09-01', NULL, '09', '2019', '0.0000', NULL, NULL, '0.0000', '6700.0000', NULL, NULL, '6700.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:32', '2020-09-04 23:03:02'),
(1045, 390, NULL, '2019-10-01', NULL, '10', '2019', '0.0000', NULL, NULL, '0.0000', '6700.0000', NULL, NULL, '6700.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:32', '2020-09-04 23:03:02'),
(1046, 390, NULL, '2019-11-01', NULL, '11', '2019', '0.0000', NULL, NULL, '0.0000', '6700.0000', NULL, NULL, '6700.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:32', '2020-09-04 23:03:02'),
(1047, 390, NULL, '2019-12-01', NULL, '12', '2019', '7444.4400', NULL, NULL, '7444.4400', '6700.0000', NULL, NULL, '6700.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:32', '2020-09-04 23:03:02'),
(1048, 390, NULL, '2020-01-01', NULL, '01', '2020', '7444.4400', NULL, NULL, '1607.5600', '5955.5600', NULL, NULL, '5955.5600', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:32', '2020-09-04 23:03:02'),
(1049, 390, NULL, '2020-02-01', NULL, '02', '2020', '7444.4400', NULL, NULL, '0.0000', '5211.1100', NULL, NULL, '5211.1100', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:32', '2020-09-04 23:03:02'),
(1050, 390, NULL, '2020-03-01', NULL, '03', '2020', '7444.4400', NULL, NULL, '0.0000', '4466.6700', NULL, NULL, '4466.6700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:32', '2020-09-04 23:03:02'),
(1051, 390, NULL, '2020-04-01', NULL, '04', '2020', '7444.4400', NULL, NULL, '0.0000', '3722.2200', NULL, NULL, '3722.2200', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:32', '2020-09-04 23:03:02'),
(1052, 390, NULL, '2020-05-01', NULL, '05', '2020', '7444.4400', NULL, NULL, '0.0000', '2977.7800', NULL, NULL, '2977.7800', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:32', '2020-09-04 23:03:02'),
(1053, 390, NULL, '2020-06-01', NULL, '06', '2020', '7444.4400', NULL, NULL, '0.0000', '2233.3400', NULL, NULL, '2233.3400', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:32', '2020-09-04 23:03:02'),
(1054, 390, NULL, '2020-07-01', NULL, '07', '2020', '7444.4400', NULL, NULL, '0.0000', '1488.8900', NULL, NULL, '1488.8900', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:32', '2020-09-04 23:03:02'),
(1055, 390, NULL, '2020-08-01', NULL, '08', '2020', '7444.4800', NULL, NULL, '0.0000', '744.0000', NULL, NULL, '744.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:32', '2020-09-04 23:03:02'),
(1056, 401, NULL, '2020-01-11', NULL, '01', '2020', '0.0000', NULL, NULL, '0.0000', '3150.0000', NULL, NULL, '3150.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:58', '2020-09-04 15:29:59'),
(1057, 401, NULL, '2020-02-11', NULL, '02', '2020', '0.0000', NULL, NULL, '0.0000', '3150.0000', NULL, NULL, '3150.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:58', '2020-09-04 15:29:59'),
(1058, 401, NULL, '2020-03-11', NULL, '03', '2020', '0.0000', NULL, NULL, '0.0000', '3150.0000', NULL, NULL, '3150.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:58', '2020-09-04 15:29:59'),
(1059, 401, NULL, '2020-04-11', NULL, '04', '2020', '3500.0000', NULL, NULL, '0.0000', '3150.0000', NULL, NULL, '3150.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:58', '2020-09-04 15:29:59'),
(1060, 401, NULL, '2020-05-11', NULL, '05', '2020', '3500.0000', NULL, NULL, '0.0000', '2800.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:58', '2020-09-04 15:29:59'),
(1061, 401, NULL, '2020-06-11', NULL, '06', '2020', '3500.0000', NULL, NULL, '0.0000', '2450.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:58', '2020-09-04 15:29:59'),
(1062, 401, NULL, '2020-07-11', NULL, '07', '2020', '3500.0000', NULL, NULL, '0.0000', '2100.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:58', '2020-09-04 15:29:59'),
(1063, 401, NULL, '2020-08-11', NULL, '08', '2020', '3500.0000', NULL, NULL, '0.0000', '1750.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:58', '2020-09-04 15:29:59'),
(1064, 401, NULL, '2020-09-11', NULL, '09', '2020', '3500.0000', NULL, NULL, '0.0000', '1400.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:58', '2020-09-04 15:29:59'),
(1065, 401, NULL, '2020-10-11', NULL, '10', '2020', '3500.0000', NULL, NULL, '0.0000', '1050.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:58', '2020-09-04 15:29:59'),
(1066, 401, NULL, '2020-11-11', NULL, '11', '2020', '3500.0000', NULL, NULL, '0.0000', '700.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:58', '2020-09-04 15:29:59'),
(1067, 401, NULL, '2020-12-11', NULL, '12', '2020', '3500.0000', NULL, NULL, '0.0000', '350.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:52:58', '2020-09-04 15:29:59'),
(1068, 398, NULL, '2020-05-09', NULL, '05', '2020', '0.0000', NULL, NULL, '0.0000', '1680.0000', NULL, NULL, '1641.6000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:53:26', '2020-09-03 22:39:58'),
(1069, 398, NULL, '2020-06-09', NULL, '06', '2020', '0.0000', NULL, NULL, '0.0000', '1680.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:53:26', '2020-09-03 22:39:58'),
(1070, 398, NULL, '2020-07-09', NULL, '07', '2020', '0.0000', NULL, NULL, '0.0000', '1680.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:53:26', '2020-09-03 22:39:58'),
(1071, 398, NULL, '2020-08-09', NULL, '08', '2020', '1555.5600', NULL, NULL, '575.2000', '1680.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:53:26', '2020-09-03 22:39:58'),
(1072, 398, NULL, '2020-09-09', NULL, '09', '2020', '1555.5600', NULL, NULL, '0.0000', '1493.3300', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:53:26', '2020-09-03 22:39:58'),
(1073, 398, NULL, '2020-10-09', NULL, '10', '2020', '1555.5600', NULL, NULL, '0.0000', '1306.6700', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:53:26', '2020-09-03 22:39:58'),
(1074, 398, NULL, '2020-11-09', NULL, '11', '2020', '1555.5600', NULL, NULL, '0.0000', '1120.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:53:26', '2020-09-03 22:39:58'),
(1075, 398, NULL, '2020-12-09', NULL, '12', '2020', '1555.5600', NULL, NULL, '0.0000', '933.3300', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:53:26', '2020-09-03 22:39:58'),
(1076, 398, NULL, '2021-01-09', NULL, '01', '2021', '1555.5600', NULL, NULL, '0.0000', '746.6600', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:53:26', '2020-09-03 22:39:58'),
(1077, 398, NULL, '2021-02-09', NULL, '02', '2021', '1555.5600', NULL, NULL, '0.0000', '560.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:53:26', '2020-09-03 22:39:58'),
(1078, 398, NULL, '2021-03-09', NULL, '03', '2021', '1555.5600', NULL, NULL, '0.0000', '373.3300', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:53:26', '2020-09-03 22:39:58'),
(1079, 398, NULL, '2021-04-09', NULL, '04', '2021', '1555.5200', NULL, NULL, '0.0000', '187.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:53:26', '2020-09-03 22:39:58'),
(1080, 397, NULL, '2020-04-03', NULL, '04', '2020', '0.0000', NULL, NULL, NULL, '1545.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:01', '2020-09-01 22:54:01'),
(1081, 397, NULL, '2020-05-03', NULL, '05', '2020', '0.0000', NULL, NULL, NULL, '1545.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:01', '2020-09-01 22:54:01'),
(1082, 397, NULL, '2020-06-03', NULL, '06', '2020', '0.0000', NULL, NULL, NULL, '1545.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:01', '2020-09-01 22:54:01'),
(1083, 397, NULL, '2020-07-03', NULL, '07', '2020', '1716.6700', NULL, NULL, NULL, '1545.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:01', '2020-09-01 22:54:01'),
(1084, 397, NULL, '2020-08-03', NULL, '08', '2020', '1716.6700', NULL, NULL, NULL, '1373.3300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:01', '2020-09-01 22:54:01'),
(1085, 397, NULL, '2020-09-03', NULL, '09', '2020', '1716.6700', NULL, NULL, NULL, '1201.6700', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:01', '2020-09-01 22:54:01'),
(1086, 397, NULL, '2020-10-03', NULL, '10', '2020', '1716.6700', NULL, NULL, NULL, '1030.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:01', '2020-09-01 22:54:01'),
(1087, 397, NULL, '2020-11-03', NULL, '11', '2020', '1716.6700', NULL, NULL, NULL, '858.3300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:01', '2020-09-01 22:54:01'),
(1088, 397, NULL, '2020-12-03', NULL, '12', '2020', '1716.6700', NULL, NULL, NULL, '686.6700', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:01', '2020-09-01 22:54:01'),
(1089, 397, NULL, '2021-01-03', NULL, '01', '2021', '1716.6700', NULL, NULL, NULL, '515.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:01', '2020-09-01 22:54:01'),
(1090, 397, NULL, '2021-02-03', NULL, '02', '2021', '1716.6700', NULL, NULL, NULL, '343.3300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:01', '2020-09-01 22:54:01'),
(1091, 397, NULL, '2021-03-03', NULL, '03', '2021', '1716.6400', NULL, NULL, NULL, '172.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:01', '2020-09-01 22:54:01'),
(1092, 400, NULL, '2020-08-19', NULL, '08', '2020', '0.0000', NULL, NULL, '0.0000', '3500.0000', NULL, NULL, '3500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:26', '2020-09-05 16:45:26'),
(1093, 400, NULL, '2020-09-19', NULL, '09', '2020', '0.0000', NULL, NULL, '0.0000', '3500.0000', NULL, NULL, '3500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:26', '2020-09-05 16:45:26'),
(1094, 400, NULL, '2020-10-19', NULL, '10', '2020', '0.0000', NULL, NULL, '0.0000', '3500.0000', NULL, NULL, '3500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:26', '2020-09-05 16:45:26'),
(1095, 400, NULL, '2020-11-19', NULL, '11', '2020', '3888.8900', NULL, NULL, '1000.0000', '3500.0000', NULL, NULL, '3500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:26', '2020-09-05 16:45:26'),
(1096, 400, NULL, '2020-12-19', NULL, '12', '2020', '3888.8900', NULL, NULL, '0.0000', '3111.1100', NULL, NULL, '3111.1100', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:26', '2020-09-05 16:45:26'),
(1097, 400, NULL, '2021-01-19', NULL, '01', '2021', '3888.8900', NULL, NULL, '0.0000', '2722.2200', NULL, NULL, '2722.2200', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:26', '2020-09-05 16:45:26'),
(1098, 400, NULL, '2021-02-19', NULL, '02', '2021', '3888.8900', NULL, NULL, '0.0000', '2333.3300', NULL, NULL, '2333.3300', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:26', '2020-09-05 16:45:26'),
(1099, 400, NULL, '2021-03-19', NULL, '03', '2021', '3888.8900', NULL, NULL, '0.0000', '1944.4400', NULL, NULL, '1944.4400', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:26', '2020-09-05 16:45:26'),
(1100, 400, NULL, '2021-04-19', NULL, '04', '2021', '3888.8900', NULL, NULL, '0.0000', '1555.5600', NULL, NULL, '1555.5600', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:26', '2020-09-05 16:45:26'),
(1101, 400, NULL, '2021-05-19', NULL, '05', '2021', '3888.8900', NULL, NULL, '0.0000', '1166.6700', NULL, NULL, '1166.6700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:26', '2020-09-05 16:45:26'),
(1102, 400, NULL, '2021-06-19', NULL, '06', '2021', '3888.8900', NULL, NULL, '0.0000', '777.7800', NULL, NULL, '777.7800', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:26', '2020-09-05 16:45:26'),
(1103, 400, NULL, '2021-07-19', NULL, '07', '2021', '3888.8800', NULL, NULL, '0.0000', '389.0000', NULL, NULL, '389.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:26', '2020-09-05 16:45:26'),
(1104, 396, NULL, '2019-12-26', NULL, '12', '2019', '0.0000', NULL, NULL, '0.0000', '11220.8600', NULL, NULL, '11220.8600', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:49', '2020-09-04 19:57:00'),
(1105, 396, NULL, '2020-01-26', NULL, '01', '2020', '0.0000', NULL, NULL, '0.0000', '11220.8600', NULL, NULL, '11220.8600', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:49', '2020-09-04 19:57:00'),
(1106, 396, NULL, '2020-02-26', NULL, '02', '2020', '0.0000', NULL, NULL, '0.0000', '11220.8600', NULL, NULL, '11220.8600', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:49', '2020-09-04 19:57:00'),
(1107, 396, NULL, '2020-03-26', NULL, '03', '2020', '15584.5300', NULL, NULL, '10749.9200', '11220.8600', NULL, NULL, '11220.8600', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:49', '2020-09-04 19:57:00'),
(1108, 396, NULL, '2020-04-26', NULL, '04', '2020', '15584.5300', NULL, NULL, '0.0000', '9974.1000', NULL, NULL, '9974.1000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:49', '2020-09-04 19:57:00'),
(1109, 396, NULL, '2020-05-26', NULL, '05', '2020', '15584.5300', NULL, NULL, '0.0000', '8727.3400', NULL, NULL, '8727.3400', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:49', '2020-09-04 19:57:00'),
(1110, 396, NULL, '2020-06-26', NULL, '06', '2020', '15584.5300', NULL, NULL, '0.0000', '7480.5800', NULL, NULL, '7480.5800', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:49', '2020-09-04 19:57:00'),
(1111, 396, NULL, '2020-07-26', NULL, '07', '2020', '15584.5300', NULL, NULL, '0.0000', '6233.8100', NULL, NULL, '6233.8100', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:49', '2020-09-04 19:57:00'),
(1112, 396, NULL, '2020-08-26', NULL, '08', '2020', '15584.5300', NULL, NULL, '0.0000', '4987.0500', NULL, NULL, '4987.0500', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:49', '2020-09-04 19:57:00'),
(1113, 396, NULL, '2020-09-26', NULL, '09', '2020', '15584.5300', NULL, NULL, '0.0000', '3740.2900', NULL, NULL, '3740.2900', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:49', '2020-09-04 19:57:00'),
(1114, 396, NULL, '2020-10-26', NULL, '10', '2020', '15584.5300', NULL, NULL, '0.0000', '2493.5300', NULL, NULL, '2493.5300', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:49', '2020-09-04 19:57:00'),
(1115, 396, NULL, '2020-11-26', NULL, '11', '2020', '15584.5600', NULL, NULL, '0.0000', '1247.0000', NULL, NULL, '1247.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:54:49', '2020-09-04 19:57:00'),
(1116, 395, NULL, '2020-02-22', NULL, '02', '2020', '0.0000', NULL, NULL, '0.0000', '3120.0000', NULL, NULL, '3120.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:17', '2020-09-04 15:23:09'),
(1117, 395, NULL, '2020-03-22', NULL, '03', '2020', '0.0000', NULL, NULL, '0.0000', '3120.0000', NULL, NULL, '3120.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:17', '2020-09-04 15:23:09'),
(1118, 395, NULL, '2020-04-22', NULL, '04', '2020', '0.0000', NULL, NULL, '0.0000', '3120.0000', NULL, NULL, '2912.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:17', '2020-09-04 15:23:09'),
(1119, 395, NULL, '2020-05-22', NULL, '05', '2020', '3466.6700', NULL, NULL, '3216.0000', '3120.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:17', '2020-09-04 15:23:09'),
(1120, 395, NULL, '2020-06-22', NULL, '06', '2020', '3466.6700', NULL, NULL, '0.0000', '2773.3300', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:17', '2020-09-04 15:23:09'),
(1121, 395, NULL, '2020-07-22', NULL, '07', '2020', '3466.6700', NULL, NULL, '0.0000', '2426.6700', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:17', '2020-09-04 15:23:09'),
(1122, 395, NULL, '2020-08-22', NULL, '08', '2020', '3466.6700', NULL, NULL, '0.0000', '2080.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:17', '2020-09-04 15:23:09'),
(1123, 395, NULL, '2020-09-22', NULL, '09', '2020', '3466.6700', NULL, NULL, '0.0000', '1733.3300', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:17', '2020-09-04 15:23:09'),
(1124, 395, NULL, '2020-10-22', NULL, '10', '2020', '3466.6700', NULL, NULL, '0.0000', '1386.6700', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:17', '2020-09-04 15:23:09'),
(1125, 395, NULL, '2020-11-22', NULL, '11', '2020', '3466.6700', NULL, NULL, '0.0000', '1040.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:17', '2020-09-04 15:23:09'),
(1126, 395, NULL, '2020-12-22', NULL, '12', '2020', '3466.6700', NULL, NULL, '0.0000', '693.3300', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:17', '2020-09-04 15:23:09'),
(1127, 395, NULL, '2021-01-22', NULL, '01', '2021', '3466.6400', NULL, NULL, '0.0000', '347.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:17', '2020-09-04 15:23:09'),
(1128, 399, NULL, '2019-07-27', NULL, '07', '2019', '0.0000', NULL, NULL, '0.0000', '256.3000', NULL, NULL, '256.3000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:40', '2020-09-05 18:14:27'),
(1129, 399, NULL, '2019-08-27', NULL, '08', '2019', '0.0000', NULL, NULL, '0.0000', '256.3000', NULL, NULL, '256.3000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:40', '2020-09-05 18:14:27'),
(1130, 399, NULL, '2019-09-27', NULL, '09', '2019', '0.0000', NULL, NULL, '0.0000', '256.3000', NULL, NULL, '256.3000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:40', '2020-09-05 18:14:27'),
(1131, 399, NULL, '2019-10-27', NULL, '10', '2019', '284.7800', NULL, NULL, '284.7800', '256.3000', NULL, NULL, '256.3000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:40', '2020-09-05 18:14:27'),
(1132, 399, NULL, '2019-11-27', NULL, '11', '2019', '284.7800', NULL, NULL, '226.9900', '227.8200', NULL, NULL, '227.8200', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:40', '2020-09-05 18:14:27'),
(1133, 399, NULL, '2019-12-27', NULL, '12', '2019', '284.7800', NULL, NULL, '0.0000', '199.3400', NULL, NULL, '199.3400', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:40', '2020-09-05 18:14:27'),
(1134, 399, NULL, '2020-01-27', NULL, '01', '2020', '284.7800', NULL, NULL, '0.0000', '170.8700', NULL, NULL, '170.8700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:40', '2020-09-05 18:14:27'),
(1135, 399, NULL, '2020-02-27', NULL, '02', '2020', '284.7800', NULL, NULL, '0.0000', '142.3900', NULL, NULL, '142.3900', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:40', '2020-09-05 18:14:27'),
(1136, 399, NULL, '2020-03-27', NULL, '03', '2020', '284.7800', NULL, NULL, '0.0000', '113.9100', NULL, NULL, '113.9100', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:40', '2020-09-05 18:14:27'),
(1137, 399, NULL, '2020-04-27', NULL, '04', '2020', '284.7800', NULL, NULL, '0.0000', '85.4300', NULL, NULL, '85.4300', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:40', '2020-09-05 18:14:27'),
(1138, 399, NULL, '2020-05-27', NULL, '05', '2020', '284.7800', NULL, NULL, '0.0000', '56.9500', NULL, NULL, '56.9500', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:40', '2020-09-05 18:14:27'),
(1139, 399, NULL, '2020-06-27', NULL, '06', '2020', '284.7600', NULL, NULL, '0.0000', '28.0000', NULL, NULL, '28.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:55:40', '2020-09-05 18:14:27'),
(1140, 392, NULL, '2019-12-04', NULL, '12', '2019', '0.0000', NULL, NULL, '0.0000', '2300.0000', NULL, NULL, '2300.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:03', '2020-09-03 22:45:44'),
(1141, 392, NULL, '2020-01-04', NULL, '01', '2020', '0.0000', NULL, NULL, '0.0000', '2300.0000', NULL, NULL, '2300.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:03', '2020-09-03 22:45:44'),
(1142, 392, NULL, '2020-02-04', NULL, '02', '2020', '0.0000', NULL, NULL, '0.0000', '2300.0000', NULL, NULL, '2300.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:03', '2020-09-03 22:45:44'),
(1143, 392, NULL, '2020-03-04', NULL, '03', '2020', '2555.5600', NULL, NULL, '2555.5600', '2300.0000', NULL, NULL, '2300.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:03', '2020-09-03 22:45:44'),
(1144, 392, NULL, '2020-04-04', NULL, '04', '2020', '2555.5600', NULL, NULL, '2555.5600', '2044.4400', NULL, NULL, '2044.4400', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:03', '2020-09-03 22:45:44'),
(1145, 392, NULL, '2020-05-04', NULL, '05', '2020', '2555.5600', NULL, NULL, '2555.5600', '1788.8900', NULL, NULL, '1788.8900', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:03', '2020-09-03 22:45:44'),
(1146, 392, NULL, '2020-06-04', NULL, '06', '2020', '2555.5600', NULL, NULL, '2555.5600', '1533.3300', NULL, NULL, '1533.3300', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:03', '2020-09-03 22:45:44'),
(1147, 392, NULL, '2020-07-04', NULL, '07', '2020', '2555.5600', NULL, NULL, '2555.5600', '1277.7800', NULL, NULL, '857.5800', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:03', '2020-09-03 22:45:44'),
(1148, 392, NULL, '2020-08-04', NULL, '08', '2020', '2555.5600', NULL, NULL, '2555.5600', '1022.2200', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:03', '2020-09-03 22:45:44'),
(1149, 392, NULL, '2020-09-04', NULL, '09', '2020', '2555.5600', NULL, NULL, '2555.5600', '766.6600', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:03', '2020-09-03 22:45:44'),
(1150, 392, NULL, '2020-10-04', NULL, '10', '2020', '2555.5600', NULL, NULL, '756.6700', '511.1100', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:03', '2020-09-03 22:45:44'),
(1151, 392, NULL, '2020-11-04', NULL, '11', '2020', '2555.5200', NULL, NULL, '0.0000', '256.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:03', '2020-09-03 22:45:44'),
(1152, 402, NULL, '2020-04-16', NULL, '04', '2020', '0.0000', NULL, NULL, '0.0000', '3500.0000', NULL, NULL, '3500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:25', '2020-09-03 23:01:35'),
(1153, 402, NULL, '2020-05-16', NULL, '05', '2020', '0.0000', NULL, NULL, '0.0000', '3500.0000', NULL, NULL, '3500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:25', '2020-09-03 23:01:35'),
(1154, 402, NULL, '2020-06-16', NULL, '06', '2020', '0.0000', NULL, NULL, '0.0000', '3500.0000', NULL, NULL, '3500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:25', '2020-09-03 23:01:35'),
(1155, 402, NULL, '2020-07-16', NULL, '07', '2020', '7777.7800', NULL, NULL, '0.0000', '3500.0000', NULL, NULL, '3500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:25', '2020-09-03 23:01:35'),
(1156, 402, NULL, '2020-08-16', NULL, '08', '2020', '7777.7800', NULL, NULL, '0.0000', '3111.1100', NULL, NULL, '3111.1100', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:25', '2020-09-03 23:01:35'),
(1157, 402, NULL, '2020-09-16', NULL, '09', '2020', '7777.7800', NULL, NULL, '0.0000', '2722.2200', NULL, NULL, '2722.2200', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:25', '2020-09-03 23:01:35'),
(1158, 402, NULL, '2020-10-16', NULL, '10', '2020', '7777.7800', NULL, NULL, '0.0000', '2333.3300', NULL, NULL, '2333.3300', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:25', '2020-09-03 23:01:35'),
(1159, 402, NULL, '2020-11-16', NULL, '11', '2020', '7777.7800', NULL, NULL, '0.0000', '1944.4400', NULL, NULL, '1944.4400', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:25', '2020-09-03 23:01:35'),
(1160, 402, NULL, '2020-12-16', NULL, '12', '2020', '7777.7800', NULL, NULL, '0.0000', '1555.5600', NULL, NULL, '1555.5600', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:25', '2020-09-03 23:01:35'),
(1161, 402, NULL, '2021-01-16', NULL, '01', '2021', '7777.7800', NULL, NULL, '0.0000', '1166.6700', NULL, NULL, '1166.6700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:25', '2020-09-03 23:01:35'),
(1162, 402, NULL, '2021-02-16', NULL, '02', '2021', '7777.7800', NULL, NULL, '0.0000', '777.7800', NULL, NULL, '777.7800', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:25', '2020-09-03 23:01:35'),
(1163, 402, NULL, '2021-03-16', NULL, '03', '2021', '7777.7600', NULL, NULL, '0.0000', '389.0000', NULL, NULL, '389.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:25', '2020-09-03 23:01:35'),
(1164, 394, NULL, '2019-09-05', NULL, '09', '2019', '0.0000', NULL, NULL, '0.0000', '1900.0000', NULL, NULL, '1900.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:46', '2020-09-04 22:21:58'),
(1165, 394, NULL, '2019-10-05', NULL, '10', '2019', '0.0000', NULL, NULL, '0.0000', '1900.0000', NULL, NULL, '1900.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:47', '2020-09-04 22:21:58'),
(1166, 394, NULL, '2019-11-05', NULL, '11', '2019', '0.0000', NULL, NULL, '0.0000', '1900.0000', NULL, NULL, '1900.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:47', '2020-09-04 22:21:58'),
(1167, 394, NULL, '2019-12-05', NULL, '12', '2019', '2111.1100', NULL, NULL, '1602.2900', '1900.0000', NULL, NULL, '1900.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:47', '2020-09-04 22:21:59'),
(1168, 394, NULL, '2020-01-05', NULL, '01', '2020', '2111.1100', NULL, NULL, '0.0000', '1688.8900', NULL, NULL, '1688.8900', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:47', '2020-09-04 22:21:59'),
(1169, 394, NULL, '2020-02-05', NULL, '02', '2020', '2111.1100', NULL, NULL, '0.0000', '1477.7800', NULL, NULL, '1477.7800', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:47', '2020-09-04 22:21:59'),
(1170, 394, NULL, '2020-03-05', NULL, '03', '2020', '2111.1100', NULL, NULL, '0.0000', '1266.6700', NULL, NULL, '1266.6700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:47', '2020-09-04 22:21:59'),
(1171, 394, NULL, '2020-04-05', NULL, '04', '2020', '2111.1100', NULL, NULL, '0.0000', '1055.5600', NULL, NULL, '1055.5600', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:47', '2020-09-04 22:21:59'),
(1172, 394, NULL, '2020-05-05', NULL, '05', '2020', '2111.1100', NULL, NULL, '0.0000', '844.4500', NULL, NULL, '844.4500', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:47', '2020-09-04 22:21:59'),
(1173, 394, NULL, '2020-06-05', NULL, '06', '2020', '2111.1100', NULL, NULL, '0.0000', '633.3300', NULL, NULL, '633.3300', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:47', '2020-09-04 22:21:59'),
(1174, 394, NULL, '2020-07-05', NULL, '07', '2020', '2111.1100', NULL, NULL, '0.0000', '422.2200', NULL, NULL, '422.2200', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:47', '2020-09-04 22:21:59'),
(1175, 394, NULL, '2020-08-05', NULL, '08', '2020', '2111.1200', NULL, NULL, '0.0000', '211.0000', NULL, NULL, '211.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:56:47', '2020-09-04 22:21:59'),
(1176, 393, NULL, '2020-08-17', NULL, '08', '2020', '0.0000', NULL, NULL, NULL, '2490.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:57:08', '2020-09-01 22:57:08'),
(1177, 393, NULL, '2020-09-17', NULL, '09', '2020', '0.0000', NULL, NULL, NULL, '2490.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:57:08', '2020-09-01 22:57:08'),
(1178, 393, NULL, '2020-10-17', NULL, '10', '2020', '0.0000', NULL, NULL, NULL, '2490.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:57:08', '2020-09-01 22:57:08'),
(1179, 393, NULL, '2020-11-17', NULL, '11', '2020', '2305.5600', NULL, NULL, NULL, '2490.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:57:08', '2020-09-01 22:57:08'),
(1180, 393, NULL, '2020-12-17', NULL, '12', '2020', '2305.5600', NULL, NULL, NULL, '2213.3300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:57:08', '2020-09-01 22:57:08'),
(1181, 393, NULL, '2021-01-17', NULL, '01', '2021', '2305.5600', NULL, NULL, NULL, '1936.6700', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:57:08', '2020-09-01 22:57:08'),
(1182, 393, NULL, '2021-02-17', NULL, '02', '2021', '2305.5600', NULL, NULL, NULL, '1660.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:57:08', '2020-09-01 22:57:08'),
(1183, 393, NULL, '2021-03-17', NULL, '03', '2021', '2305.5600', NULL, NULL, NULL, '1383.3300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:57:08', '2020-09-01 22:57:08'),
(1184, 393, NULL, '2021-04-17', NULL, '04', '2021', '2305.5600', NULL, NULL, NULL, '1106.6600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:57:08', '2020-09-01 22:57:08'),
(1185, 393, NULL, '2021-05-17', NULL, '05', '2021', '2305.5600', NULL, NULL, NULL, '830.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:57:08', '2020-09-01 22:57:08'),
(1186, 393, NULL, '2021-06-17', NULL, '06', '2021', '2305.5600', NULL, NULL, NULL, '553.3300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:57:08', '2020-09-01 22:57:08'),
(1187, 393, NULL, '2021-07-17', NULL, '07', '2021', '2305.5200', NULL, NULL, NULL, '277.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:57:08', '2020-09-01 22:57:08'),
(1188, 391, NULL, '2019-08-24', NULL, '08', '2019', '0.0000', NULL, NULL, '0.0000', '2400.0000', NULL, NULL, '2400.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:58:10', '2020-09-04 23:27:42'),
(1189, 391, NULL, '2019-09-24', NULL, '09', '2019', '0.0000', NULL, NULL, '0.0000', '2400.0000', NULL, NULL, '2400.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:58:10', '2020-09-04 23:27:42');
INSERT INTO `loan_repayment_schedules` (`id`, `loan_id`, `installment`, `due_date`, `from_date`, `month`, `year`, `principal`, `principal_waived`, `principal_written_off`, `principal_paid`, `interest`, `interest_waived`, `interest_written_off`, `interest_paid`, `fees`, `fees_waived`, `fees_written_off`, `fees_paid`, `penalty`, `penalty_waived`, `penalty_written_off`, `penalty_paid`, `total_due`, `total_paid_advance`, `total_paid_late`, `paid`, `modified_by_id`, `created_by_id`, `created_at`, `updated_at`) VALUES
(1190, 391, NULL, '2019-10-24', NULL, '10', '2019', '0.0000', NULL, NULL, '0.0000', '2400.0000', NULL, NULL, '2400.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:58:10', '2020-09-04 23:27:42'),
(1191, 391, NULL, '2019-11-24', NULL, '11', '2019', '3333.3300', NULL, NULL, '3333.3300', '2400.0000', NULL, NULL, '2400.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:58:10', '2020-09-04 23:27:42'),
(1192, 391, NULL, '2019-12-24', NULL, '12', '2019', '3333.3300', NULL, NULL, '3333.3300', '2133.3300', NULL, NULL, '2133.3300', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:58:10', '2020-09-04 23:27:42'),
(1193, 391, NULL, '2020-01-24', NULL, '01', '2020', '3333.3300', NULL, NULL, '3333.3300', '1866.6700', NULL, NULL, '1866.6700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:58:10', '2020-09-04 23:27:42'),
(1194, 391, NULL, '2020-02-24', NULL, '02', '2020', '3333.3300', NULL, NULL, '745.6000', '1600.0000', NULL, NULL, '1600.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:58:10', '2020-09-04 23:27:42'),
(1195, 391, NULL, '2020-03-24', NULL, '03', '2020', '3333.3300', NULL, NULL, '0.0000', '1333.3300', NULL, NULL, '1333.3300', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:58:10', '2020-09-04 23:27:42'),
(1196, 391, NULL, '2020-04-24', NULL, '04', '2020', '3333.3300', NULL, NULL, '0.0000', '1066.6700', NULL, NULL, '1066.6700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:58:10', '2020-09-04 23:27:42'),
(1197, 391, NULL, '2020-05-24', NULL, '05', '2020', '3333.3300', NULL, NULL, '0.0000', '800.0000', NULL, NULL, '800.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:58:10', '2020-09-04 23:27:42'),
(1198, 391, NULL, '2020-06-24', NULL, '06', '2020', '3333.3300', NULL, NULL, '0.0000', '533.3400', NULL, NULL, '533.3400', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:58:10', '2020-09-04 23:27:42'),
(1199, 391, NULL, '2020-07-24', NULL, '07', '2020', '3333.3600', NULL, NULL, '0.0000', '267.0000', NULL, NULL, '267.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 22:58:10', '2020-09-04 23:27:42'),
(1201, 387, NULL, '2020-05-03', NULL, '05', '2020', '0.0000', NULL, NULL, '0.0000', '5696.0000', NULL, NULL, '5696.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 23:11:35', '2020-09-03 22:55:33'),
(1202, 387, NULL, '2020-06-03', NULL, '06', '2020', '0.0000', NULL, NULL, '0.0000', '5696.0000', NULL, NULL, '5696.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 23:11:35', '2020-09-03 22:55:34'),
(1203, 387, NULL, '2020-07-03', NULL, '07', '2020', '0.0000', NULL, NULL, '0.0000', '5696.0000', NULL, NULL, '189.6800', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 23:11:35', '2020-09-03 22:55:34'),
(1204, 387, NULL, '2020-08-03', NULL, '08', '2020', '7911.1100', NULL, NULL, '7911.1100', '5696.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 23:11:35', '2020-09-03 22:55:33'),
(1205, 387, NULL, '2020-09-03', NULL, '09', '2020', '7911.1100', NULL, NULL, '7911.1100', '5063.1100', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 23:11:35', '2020-09-03 22:55:33'),
(1206, 387, NULL, '2020-10-03', NULL, '10', '2020', '7911.1100', NULL, NULL, '7911.1100', '4430.2200', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 23:11:35', '2020-09-03 22:55:33'),
(1207, 387, NULL, '2020-11-03', NULL, '11', '2020', '7911.1100', NULL, NULL, '7911.1100', '3797.3300', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 23:11:35', '2020-09-03 22:55:34'),
(1208, 387, NULL, '2020-12-03', NULL, '12', '2020', '7911.1100', NULL, NULL, '7911.1100', '3164.4400', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 23:11:35', '2020-09-03 22:55:34'),
(1209, 387, NULL, '2021-01-03', NULL, '01', '2021', '7911.1100', NULL, NULL, '316.5800', '2531.5600', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 23:11:35', '2020-09-03 22:55:34'),
(1210, 387, NULL, '2021-02-03', NULL, '02', '2021', '7911.1100', NULL, NULL, '0.0000', '1898.6700', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 23:11:35', '2020-09-03 22:55:33'),
(1211, 387, NULL, '2021-03-03', NULL, '03', '2021', '7911.1100', NULL, NULL, '0.0000', '1265.7800', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 23:11:35', '2020-09-03 22:55:33'),
(1212, 387, NULL, '2021-04-03', NULL, '04', '2021', '7911.1200', NULL, NULL, '0.0000', '633.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 23:11:35', '2020-09-03 22:55:33'),
(1213, 387, NULL, '2020-06-03', NULL, '06', '2020', '0.0000', NULL, NULL, '0.0000', '5696.0100', NULL, NULL, '5696.0100', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-01 23:12:12', '2020-09-03 22:55:34'),
(1221, 407, NULL, '2020-03-24', NULL, '03', '2020', '100000.0000', NULL, NULL, NULL, '12000.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-08 17:56:36', '2020-09-08 17:56:36'),
(1222, 407, NULL, '2020-09-08', NULL, '09', '2020', '0.0000', NULL, NULL, NULL, '13080.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-08 18:00:58', '2020-09-08 18:00:58'),
(1223, 407, NULL, '2020-03-24', NULL, '03', '2020', '0.0000', NULL, NULL, NULL, '12000.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-08 18:32:02', '2020-09-08 18:32:02'),
(1224, 403, NULL, '2020-09-08', NULL, '09', '2020', '0.0000', NULL, NULL, '0.0000', '1166.6700', NULL, NULL, '1166.6700', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2020-09-08 21:11:51', '2020-09-09 01:47:05'),
(1225, 407, NULL, '2020-09-08', NULL, '09', '2020', '0.0000', NULL, NULL, NULL, '12000.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-08 21:39:24', '2020-09-08 21:39:24'),
(1226, 407, NULL, '2020-10-08', NULL, '10', '2020', '0.0000', NULL, NULL, NULL, '12000.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2020-09-08 21:40:09', '2020-09-08 21:40:09'),
(1227, 403, NULL, '2018-12-02', '2019-01-05', '12', '2018', '0.0000', NULL, NULL, '0.0000', '10500.0000', NULL, NULL, '10500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, '10500.0000', 0, NULL, NULL, '2020-09-09 01:39:11', '2020-09-09 01:47:04'),
(1228, 403, NULL, '2019-01-02', '2019-01-05', '01', '2019', '0.0000', NULL, NULL, '0.0000', '10500.0000', NULL, NULL, '10500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, '10500.0000', 0, NULL, NULL, '2020-09-09 01:40:48', '2020-09-09 01:47:04'),
(1231, 2, NULL, '2020-11-27', '2020-11-27', '11', '2020', '2200.0000', NULL, NULL, '2200.0000', '440.0000', NULL, NULL, '440.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2022-10-11 16:06:04', '2022-10-11 16:08:02'),
(1232, 3, NULL, '2022-06-30', '2022-07-01', '06', '2022', '1500.0000', NULL, NULL, '1500.0000', '900.0000', NULL, NULL, '900.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, '2400.0000', 0, NULL, NULL, '2022-10-11 16:27:14', '2022-10-11 19:33:39'),
(1233, 3, NULL, '2022-07-30', '2022-07-19', '07', '2022', '1500.0000', NULL, NULL, '1500.0000', '900.0000', NULL, NULL, '900.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, '2400.0000', NULL, 0, NULL, NULL, '2022-10-11 16:27:14', '2022-10-11 19:33:39'),
(1234, 3, NULL, '2022-08-30', '2022-07-19', '08', '2022', '1500.0000', NULL, NULL, '1500.0000', '900.0000', NULL, NULL, '900.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, '2400.0000', NULL, 0, NULL, NULL, '2022-10-11 16:27:14', '2022-10-11 19:33:39'),
(1235, 3, NULL, '2022-09-30', NULL, '09', '2022', '1500.0000', NULL, NULL, '0.0000', '900.0000', '300.0000', NULL, '600.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2022-10-11 16:27:14', '2022-10-11 19:33:39'),
(1236, 4, NULL, '2022-09-27', NULL, '09', '2022', '1666.6700', NULL, NULL, '1666.6600', '1083.3400', NULL, NULL, '1083.3400', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2022-10-11 19:21:53', '2022-10-11 19:27:33'),
(1237, 4, NULL, '2022-10-27', NULL, '10', '2022', '1666.6700', NULL, NULL, '0.0000', '1083.3400', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2022-10-11 19:21:53', '2022-10-11 19:27:33'),
(1238, 4, NULL, '2022-11-27', NULL, '11', '2022', '1666.6700', NULL, NULL, '0.0000', '1083.3400', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2022-10-11 19:21:53', '2022-10-11 19:27:33'),
(1239, 4, NULL, '2022-12-27', NULL, '12', '2022', '1666.6700', NULL, NULL, '0.0000', '1083.3400', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2022-10-11 19:21:53', '2022-10-11 19:27:33'),
(1240, 4, NULL, '2023-01-27', NULL, '01', '2023', '1666.6700', NULL, NULL, '0.0000', '1083.3400', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2022-10-11 19:21:53', '2022-10-11 19:27:33'),
(1241, 4, NULL, '2023-02-27', NULL, '02', '2023', '1666.6500', NULL, NULL, '0.0000', '1083.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2022-10-11 19:21:53', '2022-10-11 19:27:33'),
(1242, 5, NULL, '2021-04-30', '2021-05-04', '04', '2021', '15000.0000', NULL, NULL, '15000.0000', '3000.0000', NULL, NULL, '3000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, '18000.0000', 0, NULL, NULL, '2022-10-11 20:03:13', '2022-10-11 20:04:18'),
(1243, 6, NULL, '2021-09-30', '2021-11-12', '09', '2021', '10000.0000', NULL, NULL, '10000.0000', '3500.0000', NULL, NULL, '3500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, '1500.0000', 0, NULL, NULL, '2022-10-11 20:09:53', '2022-10-11 20:20:56'),
(1244, 6, NULL, '2021-10-30', '2021-12-03', '10', '2021', '10000.0000', NULL, NULL, '10000.0000', '3500.0000', NULL, NULL, '3500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, '3000.0000', 0, NULL, NULL, '2022-10-11 20:09:53', '2022-10-11 20:20:56'),
(1246, 7, NULL, '2020-11-23', NULL, '11', '2020', '1250.0000', NULL, NULL, '310.0000', '500.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2022-10-13 14:46:31', '2022-10-13 14:49:14'),
(1247, 7, NULL, '2020-12-23', NULL, '12', '2020', '1250.0000', NULL, NULL, '0.0000', '500.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2022-10-13 14:46:31', '2022-10-13 14:49:14'),
(1248, 8, NULL, '2022-09-30', '2022-09-08', '09', '2022', '10000.0000', NULL, NULL, '10000.0000', '4000.0000', NULL, NULL, '4000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, '8020.0000', NULL, 0, NULL, NULL, '2022-10-13 15:32:06', '2022-10-13 15:57:21'),
(1249, 8, NULL, '2022-10-30', '2022-10-08', '10', '2022', '10000.0000', NULL, NULL, '10000.0000', '4000.0000', NULL, NULL, '4000.0000', '1126.0000', NULL, NULL, '1126.0000', NULL, NULL, NULL, '0.0000', NULL, '2070.0000', NULL, 0, NULL, NULL, '2022-10-13 15:32:06', '2022-10-13 15:57:22'),
(1250, 9, NULL, '2020-11-17', '2020-10-21', '11', '2020', '10000.0000', NULL, NULL, '10000.0000', '2500.0000', NULL, NULL, '2500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, '12500.0000', NULL, 0, NULL, NULL, '2022-10-13 20:05:28', '2022-10-13 20:06:37'),
(1251, 10, NULL, '2020-11-20', NULL, '11', '2020', '12500.0000', NULL, NULL, NULL, '5000.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2022-10-13 20:11:21', '2022-10-13 20:11:21'),
(1252, 10, NULL, '2020-12-20', NULL, '12', '2020', '12500.0000', NULL, NULL, NULL, '5000.0000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2022-10-13 20:11:21', '2022-10-13 20:11:21'),
(1259, 11, NULL, '2021-09-17', '2021-12-13', '09', '2021', '8333.3300', NULL, NULL, '8333.3300', '5000.0000', NULL, NULL, '5000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, '8333.3300', 0, NULL, NULL, '2022-10-14 15:21:52', '2022-10-14 15:29:57'),
(1260, 11, NULL, '2021-10-17', '2021-12-13', '10', '2021', '8333.3300', NULL, NULL, '8333.3300', '5000.0000', NULL, NULL, '5000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, '8333.3300', 0, NULL, NULL, '2022-10-14 15:21:52', '2022-10-14 15:29:57'),
(1261, 11, NULL, '2021-11-17', '2021-12-13', '11', '2021', '8333.3300', NULL, NULL, '8333.3300', '5000.0000', NULL, NULL, '5000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, '8333.3300', 0, NULL, NULL, '2022-10-14 15:21:52', '2022-10-14 15:29:57'),
(1262, 11, NULL, '2021-12-17', '2021-12-13', '12', '2021', '8333.3300', NULL, NULL, '8333.3300', '5000.0000', NULL, NULL, '5000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, '8333.3300', NULL, 0, NULL, NULL, '2022-10-14 15:21:52', '2022-10-14 15:29:57'),
(1263, 11, NULL, '2022-01-17', '2021-12-13', '01', '2022', '8333.3300', NULL, NULL, '8333.3300', '5000.0000', '5000.0000', NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, '8333.3300', NULL, 0, NULL, NULL, '2022-10-14 15:21:52', '2022-10-14 15:29:57'),
(1264, 11, NULL, '2022-02-17', NULL, '02', '2022', '8333.3500', NULL, NULL, '8333.3500', '5000.0000', '5000.0000', NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2022-10-14 15:21:52', '2022-10-14 15:29:57'),
(1265, 12, NULL, '2020-11-27', NULL, '11', '2020', '0.0000', NULL, NULL, '0.0000', '30000.0000', NULL, NULL, '30000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2022-10-14 16:04:44', '2022-10-14 16:09:01'),
(1266, 12, NULL, '2020-12-27', NULL, '12', '2020', '0.0000', NULL, NULL, '0.0000', '30000.0000', NULL, NULL, '30000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2022-10-14 16:04:44', '2022-10-14 16:09:01'),
(1267, 12, NULL, '2021-01-27', NULL, '01', '2021', '300000.0000', NULL, NULL, '0.0000', '30000.0000', NULL, NULL, '30000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2022-10-14 16:04:44', '2022-10-14 16:09:01'),
(1270, 13, NULL, '2021-03-30', '2021-03-26', '03', '2021', '5000.0000', NULL, NULL, '5000.0000', '2000.0000', NULL, NULL, '2000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, '500.0000', NULL, 0, NULL, NULL, '2022-10-14 16:39:58', '2022-10-14 16:44:14'),
(1271, 13, NULL, '2021-04-30', '2021-04-30', '04', '2021', '5000.0000', NULL, NULL, '5000.0000', '2000.0000', NULL, NULL, '2000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2022-10-14 16:39:58', '2022-10-14 16:44:14'),
(1280, 14, NULL, '2021-06-11', '2021-07-20', '06', '2021', '4166.6700', NULL, NULL, '4166.6700', '2500.0000', NULL, NULL, '2500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, '166.6700', 0, NULL, NULL, '2022-10-17 13:14:36', '2022-10-17 14:00:24'),
(1281, 14, NULL, '2021-07-11', '2021-08-31', '07', '2021', '4166.6700', NULL, NULL, '4166.6700', '2500.0000', NULL, NULL, '2500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, '133.3400', 0, NULL, NULL, '2022-10-17 13:14:36', '2022-10-17 14:00:24'),
(1282, 14, NULL, '2021-08-11', '2021-09-30', '08', '2021', '4166.6700', NULL, NULL, '4166.6700', '2500.0000', NULL, NULL, '2500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, '800.0100', 0, NULL, NULL, '2022-10-17 13:14:36', '2022-10-17 14:00:24'),
(1283, 14, NULL, '2021-09-11', '2021-10-28', '09', '2021', '4166.6700', NULL, NULL, '4166.6700', '2500.0000', NULL, NULL, '2500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, '2866.6800', 0, NULL, NULL, '2022-10-17 13:14:36', '2022-10-17 14:00:24'),
(1284, 14, NULL, '2021-10-11', '2021-11-30', '10', '2021', '4166.6700', NULL, NULL, '4166.6700', '2500.0000', NULL, NULL, '2500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, '533.3500', 0, NULL, NULL, '2022-10-17 13:14:36', '2022-10-17 14:00:24'),
(1285, 14, NULL, '2021-11-11', '2022-01-07', '11', '2021', '4166.6500', NULL, NULL, '4166.6500', '2500.0000', NULL, NULL, '2500.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, '200.0000', 0, NULL, NULL, '2022-10-17 13:14:36', '2022-10-17 14:00:24'),
(1298, 16, NULL, '2022-08-30', '2022-09-01', '08', '2022', '1666.6700', NULL, NULL, '1666.6700', '1000.0000', NULL, NULL, '1000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, '2666.6700', 0, NULL, NULL, '2022-10-19 14:42:50', '2022-10-19 16:14:59'),
(1299, 16, NULL, '2022-09-30', '2022-09-30', '09', '2022', '1666.6700', NULL, NULL, '1666.6700', '1000.0000', NULL, NULL, '1000.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2022-10-19 14:42:50', '2022-10-19 16:14:59'),
(1300, 16, NULL, '2022-10-30', NULL, '10', '2022', '1666.6700', NULL, NULL, '0.0000', '1000.0000', NULL, NULL, '0.6600', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2022-10-19 14:42:50', '2022-10-19 16:14:59'),
(1301, 16, NULL, '2022-11-30', NULL, '11', '2022', '1666.6700', NULL, NULL, '0.0000', '1000.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2022-10-19 14:42:50', '2022-10-19 16:14:59'),
(1302, 16, NULL, '2022-12-30', NULL, '12', '2022', '1666.6700', NULL, NULL, '0.0000', '1000.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2022-10-19 14:42:50', '2022-10-19 16:14:59'),
(1303, 16, NULL, '2023-01-30', NULL, '01', '2023', '1666.6500', NULL, NULL, '0.0000', '1000.0000', NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, '0.0000', NULL, NULL, NULL, 0, NULL, NULL, '2022-10-19 14:42:50', '2022-10-19 16:14:59');

-- --------------------------------------------------------

--
-- Table structure for table `loan_reschedule_requests`
--

CREATE TABLE `loan_reschedule_requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `loan_id` int(11) DEFAULT NULL,
  `principal` decimal(65,4) DEFAULT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_by_id` int(11) DEFAULT NULL,
  `modified_by_id` int(11) DEFAULT NULL,
  `approved_by_id` int(11) DEFAULT NULL,
  `rejected_by_id` int(11) DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  `modified_date` date DEFAULT NULL,
  `approved_date` date DEFAULT NULL,
  `rejected_date` date DEFAULT NULL,
  `reschedule_from_date` date DEFAULT NULL,
  `recalculate_interest` int(11) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_transactions`
--

CREATE TABLE `loan_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `loan_id` int(11) DEFAULT NULL,
  `office_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `payment_type_id` int(11) DEFAULT NULL,
  `gl_account_fund_source_id` int(11) NOT NULL,
  `transaction_type` enum('repayment','repayment_disbursement','write_off','write_off_recovery','disbursement','interest_accrual','fee_accrual','penalty_accrual','deposit','withdrawal','manual_entry','pay_charge','transfer_fund','interest','income','fee','disbursement_fee','installment_fee','specified_due_date_fee','overdue_maturity','overdue_installment_fee','loan_rescheduling_fee','penalty','interest_waiver','charge_waiver','rollover','premium') COLLATE utf8mb4_unicode_ci DEFAULT 'repayment',
  `created_by_id` int(11) DEFAULT NULL,
  `modified_by_id` int(11) DEFAULT NULL,
  `payment_detail_id` int(11) DEFAULT NULL,
  `charge_id` int(11) DEFAULT NULL,
  `loan_repayment_schedule_id` int(11) DEFAULT NULL,
  `debit` decimal(65,4) DEFAULT NULL,
  `credit` decimal(65,4) DEFAULT NULL,
  `balance` decimal(65,4) DEFAULT NULL,
  `amount` decimal(65,4) DEFAULT NULL,
  `reversible` tinyint(4) NOT NULL DEFAULT '0',
  `reversed` tinyint(4) NOT NULL DEFAULT '0',
  `reversal_type` enum('system','user','none') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `payment_apply_to` enum('interest','principal','fees','penalty','regular') COLLATE utf8mb4_unicode_ci DEFAULT 'regular',
  `status` enum('pending','approved','declined') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `approved_by_id` int(11) DEFAULT NULL,
  `approved_date` date DEFAULT NULL,
  `interest` decimal(65,4) DEFAULT NULL,
  `principal` decimal(65,4) DEFAULT NULL,
  `fee` decimal(65,4) DEFAULT NULL,
  `penalty` decimal(65,4) DEFAULT NULL,
  `overpayment` decimal(65,4) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `month` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt` text COLLATE utf8mb4_unicode_ci,
  `principal_derived` decimal(65,4) DEFAULT NULL,
  `interest_derived` decimal(65,4) DEFAULT NULL,
  `fees_derived` decimal(65,4) DEFAULT NULL,
  `penalty_derived` decimal(65,4) DEFAULT NULL,
  `overpayment_derived` decimal(65,4) DEFAULT NULL,
  `unrecognized_income_derived` decimal(65,4) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loan_transactions`
--

INSERT INTO `loan_transactions` (`id`, `loan_id`, `office_id`, `client_id`, `payment_type_id`, `gl_account_fund_source_id`, `transaction_type`, `created_by_id`, `modified_by_id`, `payment_detail_id`, `charge_id`, `loan_repayment_schedule_id`, `debit`, `credit`, `balance`, `amount`, `reversible`, `reversed`, `reversal_type`, `payment_apply_to`, `status`, `approved_by_id`, `approved_date`, `interest`, `principal`, `fee`, `penalty`, `overpayment`, `date`, `month`, `year`, `receipt`, `principal_derived`, `interest_derived`, `fees_derived`, `penalty_derived`, `overpayment_derived`, `unrecognized_income_derived`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(10, 2, 1, NULL, NULL, 0, 'disbursement', 26, NULL, 4, NULL, NULL, '2200.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-11-10', '11', '2020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-11 16:06:04', '2022-10-11 16:06:04', NULL),
(11, 2, 1, NULL, NULL, 0, 'interest', 26, NULL, NULL, NULL, NULL, '440.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-11-10', '11', '2020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-11 16:06:04', '2022-10-11 16:06:04', NULL),
(12, 2, 1, NULL, NULL, 0, 'repayment', 26, NULL, 5, NULL, NULL, NULL, '2640.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-11-27', '11', '2020', NULL, '2200.0000', '440.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-11 16:08:02', '2022-10-11 16:08:02', NULL),
(13, 3, 1, NULL, NULL, 0, 'disbursement', 26, NULL, 6, NULL, NULL, '6000.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-06-15', '06', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-11 16:27:14', '2022-10-11 16:27:14', NULL),
(14, 3, 1, NULL, NULL, 0, 'interest', 26, NULL, NULL, NULL, NULL, '3600.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-06-15', '06', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-11 16:27:14', '2022-10-11 16:27:14', NULL),
(15, 3, 1, NULL, NULL, 0, 'repayment', 26, NULL, 7, NULL, NULL, NULL, '2400.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-07-01', '07', '2022', NULL, '1500.0000', '900.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-11 16:28:37', '2022-10-11 19:33:39', NULL),
(16, 3, 1, NULL, NULL, 0, 'repayment', 26, NULL, 8, NULL, NULL, NULL, '5400.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-07-19', '07', '2022', NULL, '3000.0000', '2400.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-11 16:30:53', '2022-10-11 19:33:39', NULL),
(17, 3, 1, NULL, NULL, 0, 'interest_waiver', 26, NULL, NULL, NULL, NULL, NULL, '1800.0000', NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-07-19', '07', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'EARLY SETTLEMENT. PAID OFF BEFORE TIME AFTER 34 DAYS UNDER 30% INTEREST', '2022-10-11 16:34:21', '2022-10-11 16:34:21', NULL),
(18, 3, 1, NULL, NULL, 0, 'interest_waiver', 26, NULL, NULL, NULL, NULL, NULL, '1800.0000', NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-07-19', '07', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-11 16:35:29', '2022-10-11 16:35:29', NULL),
(19, 3, 1, NULL, NULL, 0, 'interest_waiver', 26, NULL, NULL, NULL, NULL, NULL, '1500.0000', NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-11', '10', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-11 16:36:26', '2022-10-11 16:36:26', NULL),
(20, 3, 1, NULL, NULL, 0, 'interest_waiver', 26, NULL, NULL, NULL, NULL, NULL, '300.0000', NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-07-07', '07', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'PAID OFF LOAN ERLIER', '2022-10-11 16:37:21', '2022-10-11 16:37:21', NULL),
(21, 3, 1, NULL, NULL, 0, 'interest_waiver', 26, NULL, NULL, NULL, NULL, NULL, '1500.0000', NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-07-21', '07', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-11 16:42:23', '2022-10-11 16:42:23', NULL),
(22, 4, 1, NULL, NULL, 0, 'disbursement', 26, NULL, 9, NULL, NULL, '10000.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-08-15', '08', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-11 19:21:53', '2022-10-11 19:21:53', NULL),
(23, 4, 1, NULL, NULL, 0, 'interest', 26, NULL, NULL, NULL, NULL, '6499.7000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-08-15', '08', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-11 19:21:53', '2022-10-11 19:21:53', NULL),
(24, 4, 1, NULL, NULL, 0, 'repayment', 26, NULL, 10, NULL, NULL, NULL, '2750.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-09-27', '09', '2022', NULL, '1666.6600', '1083.3400', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-11 19:27:33', '2022-10-11 19:27:33', NULL),
(25, 3, 1, NULL, NULL, 0, 'interest_waiver', 26, NULL, NULL, NULL, NULL, NULL, '1200.0000', NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-07-19', '07', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-11 19:33:39', '2022-10-11 19:33:39', NULL),
(26, 5, 1, NULL, NULL, 0, 'disbursement', 26, NULL, 11, NULL, NULL, '15000.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-04-15', '04', '2021', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-11 20:03:13', '2022-10-11 20:03:13', NULL),
(27, 5, 1, NULL, NULL, 0, 'interest', 26, NULL, NULL, NULL, NULL, '3000.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-04-15', '04', '2021', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-11 20:03:13', '2022-10-11 20:03:13', NULL),
(28, 5, 1, NULL, NULL, 0, 'repayment', 26, NULL, 12, NULL, NULL, NULL, '18000.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-05-04', '05', '2021', NULL, '15000.0000', '3000.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-11 20:04:18', '2022-10-11 20:04:18', NULL),
(29, 6, 1, NULL, NULL, 0, 'disbursement', 26, NULL, 13, NULL, NULL, '20000.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-09-06', '09', '2021', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-11 20:09:53', '2022-10-11 20:09:53', NULL),
(30, 6, 1, NULL, NULL, 0, 'interest', 26, NULL, NULL, NULL, NULL, '7000.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-09-06', '09', '2021', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-11 20:09:53', '2022-10-11 20:09:53', NULL),
(31, 6, 1, NULL, NULL, 0, 'repayment', 26, NULL, 14, NULL, NULL, NULL, '5000.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-10-04', '10', '2021', NULL, '1500.0000', '3500.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-11 20:15:20', '2022-10-11 20:20:56', NULL),
(32, 6, 1, NULL, NULL, 0, 'repayment', 26, NULL, 15, NULL, NULL, NULL, '7000.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-11-04', '11', '2021', NULL, '7000.0000', '0.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-11 20:16:47', '2022-10-11 20:20:56', NULL),
(33, 6, 1, NULL, NULL, 0, 'repayment', 26, NULL, 16, NULL, NULL, NULL, '7000.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-11-12', '11', '2021', NULL, '3500.0000', '3500.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-11 20:17:56', '2022-10-11 20:20:56', NULL),
(34, 6, 1, NULL, NULL, 0, 'repayment', 26, NULL, 17, NULL, NULL, NULL, '5000.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-11-23', '11', '2021', NULL, '5000.0000', '0.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-11 20:19:01', '2022-10-11 20:20:56', NULL),
(35, 6, 1, NULL, NULL, 0, 'repayment', 26, NULL, 18, NULL, NULL, NULL, '3000.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-03', '12', '2021', NULL, '3000.0000', '0.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-11 20:20:56', '2022-10-11 20:20:56', NULL),
(38, 7, 1, NULL, NULL, 0, 'disbursement', 26, NULL, 20, NULL, NULL, '2500.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-10-22', '10', '2020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-13 14:46:31', '2022-10-13 14:46:31', NULL),
(39, 7, 1, NULL, NULL, 0, 'interest', 26, NULL, NULL, NULL, NULL, '1000.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-10-22', '10', '2020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-13 14:46:31', '2022-10-13 14:46:31', NULL),
(40, 7, 1, NULL, NULL, 0, 'repayment', 26, NULL, 21, NULL, NULL, NULL, '310.0000', NULL, NULL, 1, 0, 'none', 'principal', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-01-07', '01', '2021', NULL, '310.0000', '0.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-13 14:49:14', '2022-10-13 14:49:14', NULL),
(41, 7, 1, NULL, NULL, 0, 'write_off', 26, NULL, NULL, NULL, NULL, NULL, '3500.0000', NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-30', '12', '2021', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-13 14:54:20', '2022-10-13 14:54:20', NULL),
(42, 8, 1, NULL, NULL, 0, 'disbursement', 26, NULL, 22, NULL, NULL, '20000.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-07-25', '07', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-13 15:32:06', '2022-10-13 15:32:06', NULL),
(43, 8, 1, NULL, NULL, 0, 'interest', 26, NULL, NULL, NULL, NULL, '8000.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-07-25', '07', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-13 15:32:06', '2022-10-13 15:32:06', NULL),
(44, 8, 1, NULL, NULL, 0, 'repayment', 26, NULL, 23, NULL, NULL, NULL, '2000.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-08-30', '08', '2022', NULL, '0.0000', '2000.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-13 15:33:50', '2022-10-13 15:57:21', NULL),
(45, 8, 1, NULL, NULL, 0, 'repayment', 26, NULL, 24, NULL, NULL, NULL, '3980.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-09-02', '09', '2022', NULL, '1980.0000', '2000.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-13 15:39:09', '2022-10-13 15:57:21', NULL),
(46, 8, 1, NULL, NULL, 0, 'repayment', 26, NULL, 25, NULL, NULL, NULL, '8050.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-09-08', '09', '2022', NULL, '8020.0000', '30.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-13 15:39:47', '2022-10-13 15:57:21', NULL),
(47, 8, 1, NULL, NULL, 0, 'repayment', 26, NULL, 26, NULL, NULL, NULL, '4900.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-03', '10', '2022', NULL, '930.0000', '3970.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-13 15:41:39', '2022-10-13 15:57:22', NULL),
(48, 8, 1, NULL, NULL, 0, 'repayment', 26, NULL, 27, NULL, NULL, NULL, '7000.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-07', '10', '2022', NULL, '7000.0000', '0.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-13 15:43:06', '2022-10-13 15:57:22', NULL),
(49, 8, 1, NULL, NULL, 0, 'repayment', 26, NULL, 28, NULL, NULL, '1126.0000', '1126.0000', NULL, NULL, 0, 1, 'user', 'penalty', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-13', '10', '2022', NULL, '0.0000', '0.0000', '0.0000', '0.0000', NULL, '1126.0000', NULL, '2022-10-13 15:45:52', '2022-10-13 15:55:37', NULL),
(50, 8, 1, NULL, NULL, 0, 'repayment', 26, NULL, 29, NULL, NULL, NULL, '2070.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-08', '10', '2022', NULL, '2070.0000', '0.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-13 15:46:51', '2022-10-13 15:57:22', NULL),
(51, 8, 1, NULL, NULL, 0, 'specified_due_date_fee', 26, NULL, NULL, NULL, 1249, '1126.0000', NULL, NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-30', '10', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-13 15:53:32', '2022-10-13 15:53:32', NULL),
(52, 8, 1, NULL, NULL, 0, 'repayment', 26, NULL, 30, NULL, NULL, NULL, '1126.0000', NULL, NULL, 1, 0, 'none', 'fees', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-09', '10', '2022', NULL, '0.0000', '0.0000', '1126.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-13 15:57:21', '2022-10-13 15:57:22', NULL),
(53, 9, 1, NULL, NULL, 0, 'disbursement', 26, NULL, 31, NULL, NULL, '10000.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-10-17', '10', '2020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-13 20:05:28', '2022-10-13 20:05:28', NULL),
(54, 9, 1, NULL, NULL, 0, 'interest', 26, NULL, NULL, NULL, NULL, '2500.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-10-17', '10', '2020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-13 20:05:28', '2022-10-13 20:05:28', NULL),
(55, 9, 1, NULL, NULL, 0, 'repayment', 26, NULL, 32, NULL, NULL, NULL, '12500.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-10-21', '10', '2020', NULL, '10000.0000', '2500.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-13 20:06:37', '2022-10-13 20:06:37', NULL),
(56, 10, 1, NULL, NULL, 0, 'disbursement', 26, NULL, 33, NULL, NULL, '25000.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-10-22', '10', '2020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-13 20:11:21', '2022-10-13 20:11:21', NULL),
(57, 10, 1, NULL, NULL, 0, 'interest', 26, NULL, NULL, NULL, NULL, '10000.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-10-22', '10', '2020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-13 20:11:21', '2022-10-13 20:11:21', NULL),
(58, 10, 1, NULL, NULL, 0, 'write_off', 26, NULL, NULL, NULL, NULL, NULL, '35000.0000', NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-30', '12', '2021', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-13 20:13:05', '2022-10-13 20:13:05', NULL),
(71, 11, 1, NULL, NULL, 0, 'disbursement', 26, NULL, 41, NULL, NULL, '50000.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-08-18', '08', '2021', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-14 15:21:52', '2022-10-14 15:21:52', NULL),
(72, 11, 1, NULL, NULL, 0, 'interest', 26, NULL, NULL, NULL, NULL, '30000.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-08-18', '08', '2021', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-14 15:21:52', '2022-10-14 15:21:52', NULL),
(73, 11, 1, NULL, NULL, 0, 'repayment', 26, NULL, 42, NULL, NULL, NULL, '5000.0000', NULL, NULL, 1, 0, 'none', 'interest', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-09-16', '09', '2021', NULL, '0.0000', '5000.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-14 15:22:38', '2022-10-14 15:29:57', NULL),
(74, 11, 1, NULL, NULL, 0, 'repayment', 26, NULL, 43, NULL, NULL, NULL, '5000.0000', NULL, NULL, 1, 0, 'none', 'interest', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-10-13', '10', '2021', NULL, '0.0000', '5000.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-14 15:23:23', '2022-10-14 15:29:57', NULL),
(75, 11, 1, NULL, NULL, 0, 'repayment', 26, NULL, 44, NULL, NULL, NULL, '5000.0000', NULL, NULL, 1, 0, 'none', 'interest', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-11-19', '11', '2021', NULL, '0.0000', '5000.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-14 15:24:04', '2022-10-14 15:29:57', NULL),
(76, 11, 1, NULL, NULL, 0, 'repayment', 26, NULL, 45, NULL, NULL, NULL, '5000.0000', NULL, NULL, 1, 0, 'none', 'interest', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-13', '12', '2021', NULL, '0.0000', '5000.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-14 15:24:46', '2022-10-14 15:29:57', NULL),
(77, 11, 1, NULL, NULL, 0, 'repayment', 26, NULL, 46, NULL, NULL, '50000.0000', '50000.0000', NULL, NULL, 0, 1, 'user', 'principal', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-13', '12', '2021', NULL, '50000.0000', '0.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-14 15:25:46', '2022-10-14 15:29:57', NULL),
(78, 11, 1, NULL, NULL, 0, 'interest_waiver', 26, NULL, NULL, NULL, NULL, NULL, '10000.0000', NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-13', '12', '2021', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'LOAN PAID OFF BEFORE DUE DATE ON 13/12/2021 AFTER 4 MONTHS, RECALCULATED UNDER 40%', '2022-10-14 15:27:39', '2022-10-14 15:27:39', NULL),
(79, 11, 1, NULL, NULL, 0, 'repayment', 26, NULL, 47, NULL, NULL, NULL, '50000.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-13', '12', '2021', NULL, '50000.0000', '0.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-14 15:29:57', '2022-10-14 15:29:57', NULL),
(80, 12, 1, NULL, NULL, 0, 'disbursement', 26, NULL, 48, NULL, NULL, '300000.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-10-28', '10', '2020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-14 16:04:44', '2022-10-14 16:04:44', NULL),
(81, 12, 1, NULL, NULL, 0, 'interest', 26, NULL, NULL, NULL, NULL, '90000.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-10-28', '10', '2020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-14 16:04:44', '2022-10-14 16:04:44', NULL),
(82, 12, 1, NULL, NULL, 0, 'repayment', 26, NULL, 49, NULL, NULL, NULL, '30000.0000', NULL, NULL, 1, 0, 'none', 'interest', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-11-30', '11', '2020', NULL, '0.0000', '30000.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-14 16:05:34', '2022-10-14 16:09:01', NULL),
(83, 12, 1, NULL, NULL, 0, 'repayment', 26, NULL, 50, NULL, NULL, NULL, '15000.0000', NULL, NULL, 1, 0, 'none', 'interest', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-12-31', '12', '2020', NULL, '0.0000', '15000.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-14 16:06:49', '2022-10-14 16:09:01', NULL),
(84, 12, 1, NULL, NULL, 0, 'repayment', 26, NULL, 51, NULL, NULL, NULL, '15000.0000', NULL, NULL, 1, 0, 'none', 'interest', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-01-08', '01', '2021', NULL, '0.0000', '15000.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-14 16:07:25', '2022-10-14 16:09:01', NULL),
(85, 12, 1, NULL, NULL, 0, 'repayment', 26, NULL, 52, NULL, NULL, NULL, '20000.0000', NULL, NULL, 1, 0, 'none', 'interest', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-01-27', '01', '2021', NULL, '0.0000', '20000.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-14 16:08:18', '2022-10-14 16:09:01', NULL),
(86, 12, 1, NULL, NULL, 0, 'repayment', 26, NULL, 53, NULL, NULL, NULL, '10000.0000', NULL, NULL, 1, 0, 'none', 'interest', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-02-11', '02', '2021', NULL, '0.0000', '10000.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-14 16:09:01', '2022-10-14 16:09:01', NULL),
(89, 13, 1, NULL, NULL, 0, 'disbursement', 26, NULL, 55, NULL, NULL, '10000.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-02-17', '02', '2021', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-14 16:39:58', '2022-10-14 16:39:58', NULL),
(90, 13, 1, NULL, NULL, 0, 'interest', 26, NULL, NULL, NULL, NULL, '4000.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-02-17', '02', '2021', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-14 16:39:58', '2022-10-14 16:39:58', NULL),
(91, 13, 1, NULL, NULL, 0, 'repayment', 26, NULL, 56, NULL, NULL, NULL, '6000.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-03-22', '03', '2021', NULL, '4000.0000', '2000.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-14 16:40:55', '2022-10-14 16:44:14', NULL),
(92, 13, 1, NULL, NULL, 0, 'repayment', 26, NULL, 57, NULL, NULL, NULL, '500.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-03-24', '03', '2021', NULL, '500.0000', '0.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-14 16:41:38', '2022-10-14 16:44:14', NULL),
(93, 13, 1, NULL, NULL, 0, 'repayment', 26, NULL, 58, NULL, NULL, NULL, '500.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-03-26', '03', '2021', NULL, '500.0000', '0.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-14 16:42:04', '2022-10-14 16:44:14', NULL),
(94, 13, 1, NULL, NULL, 0, 'repayment', 26, NULL, 59, NULL, NULL, NULL, '1250.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-04-30', '04', '2021', NULL, '1250.0000', '0.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-14 16:43:40', '2022-10-14 16:44:14', NULL),
(95, 13, 1, NULL, NULL, 0, 'repayment', 26, NULL, 60, NULL, NULL, NULL, '5750.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-04-03', '04', '2021', NULL, '3750.0000', '2000.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-14 16:44:14', '2022-10-14 16:44:14', NULL),
(119, 14, 1, NULL, NULL, 0, 'disbursement', 26, NULL, 81, NULL, NULL, '25000.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-05-07', '05', '2021', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-17 13:14:36', '2022-10-17 13:14:36', NULL),
(120, 14, 1, NULL, NULL, 0, 'interest', 26, NULL, NULL, NULL, NULL, '15000.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-05-07', '05', '2021', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-17 13:14:36', '2022-10-17 13:14:36', NULL),
(121, 14, 1, NULL, NULL, 0, 'repayment', 26, NULL, 82, NULL, NULL, NULL, '6500.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-06-11', '06', '2021', NULL, '4000.0000', '2500.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-17 13:50:50', '2022-10-17 14:00:24', NULL),
(122, 14, 1, NULL, NULL, 0, 'repayment', 26, NULL, 83, NULL, NULL, NULL, '6700.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-07-20', '07', '2021', NULL, '4200.0000', '2500.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-17 13:51:25', '2022-10-17 14:00:24', NULL),
(123, 14, 1, NULL, NULL, 0, 'repayment', 26, NULL, 84, NULL, NULL, NULL, '2000.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-08-31', '08', '2021', NULL, '133.3400', '1866.6600', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-17 13:52:08', '2022-10-17 14:00:24', NULL),
(124, 14, 1, NULL, NULL, 0, 'repayment', 26, NULL, 85, NULL, NULL, NULL, '1500.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-09-11', '09', '2021', NULL, '866.6600', '633.3400', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-17 13:52:37', '2022-10-17 14:00:24', NULL),
(125, 14, 1, NULL, NULL, 0, 'repayment', 26, NULL, 86, NULL, NULL, NULL, '1500.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-09-21', '09', '2021', NULL, '1500.0000', '0.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-17 13:53:31', '2022-10-17 14:00:24', NULL),
(126, 14, 1, NULL, NULL, 0, 'repayment', 26, NULL, 87, NULL, NULL, NULL, '1000.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-09-29', '09', '2021', NULL, '1000.0000', '0.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-17 13:54:00', '2022-10-17 14:00:24', NULL),
(127, 14, 1, NULL, NULL, 0, 'repayment', 26, NULL, 88, NULL, NULL, NULL, '3000.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-09-30', '09', '2021', NULL, '800.0100', '2199.9900', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-17 13:55:07', '2022-10-17 14:00:24', NULL),
(128, 14, 1, NULL, NULL, 0, 'repayment', 26, NULL, 89, NULL, NULL, NULL, '1600.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-10-19', '10', '2021', NULL, '1299.9900', '300.0100', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-17 13:55:40', '2022-10-17 14:00:24', NULL),
(129, 14, 1, NULL, NULL, 0, 'repayment', 26, NULL, 90, NULL, NULL, NULL, '4000.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-10-28', '10', '2021', NULL, '2866.6800', '1133.3200', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-17 13:56:14', '2022-10-17 14:00:24', NULL),
(130, 14, 1, NULL, NULL, 0, 'repayment', 26, NULL, 91, NULL, NULL, NULL, '1000.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-11-15', '11', '2021', NULL, '0.0000', '1000.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-17 13:57:10', '2022-10-17 14:00:24', NULL),
(131, 14, 1, NULL, NULL, 0, 'repayment', 26, NULL, 92, NULL, NULL, NULL, '4000.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-11-22', '11', '2021', NULL, '3633.3200', '366.6800', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-17 13:58:05', '2022-10-17 14:00:24', NULL),
(132, 14, 1, NULL, NULL, 0, 'repayment', 26, NULL, 93, NULL, NULL, NULL, '4000.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-11-30', '11', '2021', NULL, '1500.0000', '2500.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-17 13:58:43', '2022-10-17 14:00:24', NULL),
(133, 14, 1, NULL, NULL, 0, 'repayment', 26, NULL, 94, NULL, NULL, NULL, '1500.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-24', '12', '2021', NULL, '1500.0000', '0.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-17 13:59:26', '2022-10-17 14:00:24', NULL),
(134, 14, 1, NULL, NULL, 0, 'repayment', 26, NULL, 95, NULL, NULL, NULL, '1500.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2021-12-31', '12', '2021', NULL, '1500.0000', '0.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-17 13:59:54', '2022-10-17 14:00:24', NULL),
(135, 14, 1, NULL, NULL, 0, 'repayment', 26, NULL, 96, NULL, NULL, NULL, '200.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-01-07', '01', '2022', NULL, '200.0000', '0.0000', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-17 14:00:24', '2022-10-17 14:00:24', NULL),
(146, 16, 1, NULL, NULL, 0, 'disbursement', 26, NULL, 99, NULL, NULL, '10000.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-07-21', '07', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-19 14:42:50', '2022-10-19 14:42:50', NULL),
(147, 16, 1, NULL, NULL, 0, 'interest', 26, NULL, NULL, NULL, NULL, '6000.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-07-21', '07', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-19 14:42:50', '2022-10-19 14:42:50', NULL),
(148, 16, 1, NULL, NULL, 0, 'disbursement_fee', 26, NULL, NULL, NULL, NULL, '150.0000', NULL, NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-07-21', '07', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-19 14:42:50', '2022-10-19 14:42:50', NULL),
(149, 16, 1, NULL, NULL, 0, 'repayment_disbursement', 26, NULL, NULL, NULL, NULL, NULL, '150.0000', NULL, NULL, 0, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-07-21', '07', '2022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-19 14:42:50', '2022-10-19 14:42:50', NULL),
(150, 16, 1, NULL, NULL, 0, 'repayment', 26, NULL, 100, NULL, NULL, NULL, '2667.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-09-01', '09', '2022', NULL, '1666.6700', '1000.3300', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-19 16:11:41', '2022-10-19 16:14:59', NULL),
(151, 16, 1, NULL, NULL, 0, 'repayment', 26, NULL, 101, NULL, NULL, NULL, '2667.0000', NULL, NULL, 1, 0, 'none', 'regular', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-09-30', '09', '2022', NULL, '1666.6700', '1000.3300', '0.0000', '0.0000', NULL, '0.0000', NULL, '2022-10-19 16:14:59', '2022-10-19 16:14:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `loan_transaction_repayment_schedule_mappings`
--

CREATE TABLE `loan_transaction_repayment_schedule_mappings` (
  `id` int(10) UNSIGNED NOT NULL,
  `loan_repayment_schedule_id` int(11) DEFAULT NULL,
  `loan_transaction_id` int(11) DEFAULT NULL,
  `interest` decimal(65,4) DEFAULT NULL,
  `principal` decimal(65,4) DEFAULT NULL,
  `fee` decimal(65,4) DEFAULT NULL,
  `penalty` decimal(65,4) DEFAULT NULL,
  `overpayment` decimal(65,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(67, '2014_07_02_230147_migration_cartalyst_sentinel', 1),
(68, '2018_04_17_142054_create_clients_table', 1),
(69, '2018_04_17_143536_create_offices_table', 1),
(70, '2018_04_17_143618_create_office_transactions_table', 1),
(71, '2018_04_17_145018_create_group_clients_table', 1),
(72, '2018_04_17_145018_create_groups_table', 1),
(73, '2018_04_17_145135_create_gl_accounts_table', 1),
(74, '2018_04_17_145228_create_gl_closures_table', 1),
(75, '2018_04_17_145702_create_loans_table', 1),
(76, '2018_04_17_145803_create_loan_repayment_schedules_table', 1),
(77, '2018_04_17_154317_create_gl_journal_entries_table', 1),
(78, '2018_04_17_155148_create_funds_table', 1),
(79, '2018_04_17_155538_create_documents_table', 1),
(80, '2018_04_17_155601_create_loan_products_table', 1),
(81, '2018_04_17_160526_create_payment_type_details_table', 1),
(82, '2018_04_17_160526_create_payment_types_table', 1),
(83, '2018_04_17_160706_create_currencies_table', 1),
(84, '2018_04_17_161015_create_charges_table', 1),
(85, '2018_04_17_161955_create_loan_charges_table', 1),
(86, '2018_04_17_162008_create_loan_product_charges_table', 1),
(87, '2018_04_17_162337_create_loan_reschedule_requests_table', 1),
(88, '2018_04_17_162446_create_loan_transactions_table', 1),
(89, '2018_04_17_162650_create_loan_transaction_repayment_schedule_mappings_table', 1),
(90, '2018_04_17_162728_create_notes_table', 1),
(91, '2018_04_17_173153_create_client_next_of_kin_table', 1),
(92, '2018_04_17_174617_create_client_identifications_table', 1),
(93, '2018_04_17_175118_create_client_identification_types_table', 1),
(94, '2018_04_17_175504_create_client_relationships_table', 1),
(95, '2018_04_17_175600_create_client_profession_table', 1),
(96, '2018_04_22_230823_create_settings_table', 1),
(97, '2018_04_22_230940_create_countries_table', 1),
(98, '2018_04_22_233825_create_permissions_table', 1),
(99, '2018_04_26_230414_create_loan_purposes_table', 1),
(100, '2018_04_27_230233_create_collateral_table', 1),
(101, '2018_04_27_230233_create_collateral_types_table', 1),
(102, '2018_04_28_111043_create_guarantors_table', 1),
(103, '2018_04_29_140913_create_payment_details_table', 1),
(104, '2018_04_30_172023_create_sms_gateways_table', 1),
(105, '2018_05_03_012752_create_loan_provisioning_criteria_table', 1),
(106, '2018_05_07_141744_create_group_loan_allocation_table', 1),
(107, '2018_05_08_232948_create_savings_products_table', 1),
(108, '2018_05_08_233244_create_savings_table', 1),
(109, '2018_05_08_233314_create_savings_charges_table', 1),
(110, '2018_05_08_233328_create_savings_transactions_table', 1),
(111, '2018_05_08_233440_create_savings_product_charges_table', 1),
(112, '2018_05_11_030728_create_report_scheduler_table', 1),
(113, '2018_05_11_041541_create_report_scheduler_run_history_table', 1),
(114, '2018_05_11_042659_create_communication_campaigns_table', 1),
(115, '2018_05_11_155900_create_audit_trail_table', 1),
(116, '2018_05_21_224207_create_custom_fields_table', 1),
(117, '2018_05_21_224603_create_custom_fields_meta_table', 1),
(118, '2018_05_24_121405_create_asset_types_table', 1),
(119, '2018_05_24_122537_create_assets_table', 1),
(120, '2018_05_24_122554_create_asset_depreciation_table', 1),
(121, '2018_05_24_162316_create_expense_types_table', 1),
(122, '2018_05_24_162910_create_expenses_table', 1),
(123, '2018_05_24_163811_create_expense_budgets_table', 1),
(124, '2018_05_24_165943_create_other_income_table', 1),
(125, '2018_05_24_165945_create_other_income_types_table', 1),
(126, '2018_05_27_182007_create_client_users_table', 1),
(127, '2018_05_27_182033_create_group_users_table', 1),
(128, '2018_05_28_154830_create_payroll_templates_table', 1),
(129, '2018_05_28_161903_create_payroll_template_meta_table', 1),
(130, '2018_05_28_163251_create_payroll_table', 1),
(131, '2018_05_28_163322_create_payroll_meta_table', 1),
(132, '2018_06_05_092031_create_loan_applications_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(10) UNSIGNED NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `type` enum('client','loan','group','savings','identification','shares','repayment') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `modified_by_id` int(11) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `offices`
--

CREATE TABLE `offices` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `external_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opening_date` date DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `phone` text COLLATE utf8mb4_unicode_ci,
  `email` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `manager_id` int(11) DEFAULT NULL,
  `active` tinyint(4) DEFAULT '1',
  `default_office` tinyint(4) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `offices`
--

INSERT INTO `offices` (`id`, `name`, `parent_id`, `external_id`, `opening_date`, `address`, `phone`, `email`, `notes`, `manager_id`, `active`, `default_office`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'CHURCH ROAD HQ LUSAKA', NULL, 'BML-HQ-000', '2020-04-30', NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, '2022-10-19 14:53:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `office_transactions`
--

CREATE TABLE `office_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `from_office_id` int(11) DEFAULT NULL,
  `to_office_id` int(11) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `amount` decimal(65,8) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `other_income`
--

CREATE TABLE `other_income` (
  `id` int(10) UNSIGNED NOT NULL,
  `office_id` int(10) UNSIGNED DEFAULT NULL,
  `created_by_id` int(10) UNSIGNED DEFAULT NULL,
  `other_income_type_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(65,2) NOT NULL DEFAULT '0.00',
  `date` date DEFAULT NULL,
  `year` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `month` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `files` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','declined') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `approved_date` date DEFAULT NULL,
  `approved_by_id` int(10) UNSIGNED DEFAULT NULL,
  `declined_date` date DEFAULT NULL,
  `declined_by_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `other_income_types`
--

CREATE TABLE `other_income_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gl_account_asset_id` int(11) DEFAULT NULL,
  `gl_account_income_id` int(11) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `other_income_types`
--

INSERT INTO `other_income_types` (`id`, `name`, `gl_account_asset_id`, `gl_account_income_id`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Admin Fees', 120, 69, 'notes', '2018-11-20 19:53:28', '2019-05-31 16:55:52'),
(2, 'Others', 120, 81, NULL, '2019-01-05 05:46:53', '2019-03-14 21:34:40'),
(3, 'Inconvenience fees', 120, 74, 'Payment for inconvenience fee', '2019-01-09 11:34:26', '2019-03-16 18:41:50'),
(4, 'Handling fees', 120, 73, 'Payment for Handling fees', '2019-02-10 18:23:58', '2019-05-31 16:56:20'),
(5, 'Application Form', 120, 70, 'Purchase of Application form', '2019-03-15 23:56:16', '2019-03-16 00:08:17'),
(6, 'Appraisal fee', 120, 71, 'Payment for Appraisal fee', '2019-03-16 18:39:58', '2019-05-31 16:56:09');

-- --------------------------------------------------------

--
-- Table structure for table `payment_details`
--

CREATE TABLE `payment_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `payment_type_id` int(11) DEFAULT NULL,
  `account_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cheque_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `routing_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_details`
--

INSERT INTO `payment_details` (`id`, `payment_type_id`, `account_number`, `cheque_number`, `routing_code`, `receipt_number`, `bank`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-11 02:12:54', '2022-10-11 02:12:54'),
(2, 1, NULL, NULL, NULL, '053', NULL, NULL, '2022-10-11 02:15:44', '2022-10-11 02:15:44'),
(3, 1, NULL, NULL, NULL, '064', 'FNB', NULL, '2022-10-11 02:34:37', '2022-10-11 02:34:37'),
(4, 1, NULL, NULL, NULL, '064', 'FNB', NULL, '2022-10-11 16:06:04', '2022-10-11 16:06:04'),
(5, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-11 16:08:02', '2022-10-11 16:08:02'),
(6, 1, NULL, NULL, NULL, '380', 'ZANACO', NULL, '2022-10-11 16:27:14', '2022-10-11 16:27:14'),
(7, 1, NULL, NULL, NULL, '1216', NULL, NULL, '2022-10-11 16:28:37', '2022-10-11 16:28:37'),
(8, 1, NULL, NULL, NULL, '1228', NULL, NULL, '2022-10-11 16:30:53', '2022-10-11 16:30:53'),
(9, 1, NULL, NULL, NULL, '428', 'FNB', NULL, '2022-10-11 19:21:53', '2022-10-11 19:21:53'),
(10, 1, NULL, NULL, NULL, '1370', NULL, NULL, '2022-10-11 19:27:33', '2022-10-11 19:27:33'),
(11, 1, NULL, NULL, NULL, '151', 'STANDART CHATTERED BANK', NULL, '2022-10-11 20:03:13', '2022-10-11 20:03:13'),
(12, 1, NULL, NULL, NULL, '308', NULL, NULL, '2022-10-11 20:04:18', '2022-10-11 20:04:18'),
(13, 1, NULL, NULL, NULL, '242', 'STANDART  BANK', NULL, '2022-10-11 20:09:53', '2022-10-11 20:09:53'),
(14, 1, NULL, NULL, NULL, '599', NULL, NULL, '2022-10-11 20:15:20', '2022-10-11 20:15:20'),
(15, 1, NULL, NULL, NULL, '674', NULL, NULL, '2022-10-11 20:16:47', '2022-10-11 20:16:47'),
(16, 1, NULL, NULL, NULL, '683', NULL, NULL, '2022-10-11 20:17:56', '2022-10-11 20:17:56'),
(17, 1, NULL, NULL, NULL, '696', NULL, NULL, '2022-10-11 20:19:01', '2022-10-11 20:19:01'),
(18, 1, NULL, NULL, NULL, '741', NULL, NULL, '2022-10-11 20:20:56', '2022-10-11 20:20:56'),
(19, 1, NULL, NULL, NULL, '036', NULL, NULL, '2022-10-13 14:44:38', '2022-10-13 14:44:38'),
(20, 1, NULL, NULL, NULL, '036', NULL, NULL, '2022-10-13 14:46:31', '2022-10-13 14:46:31'),
(21, 1, NULL, NULL, NULL, '127', NULL, NULL, '2022-10-13 14:49:14', '2022-10-13 14:49:14'),
(22, 1, NULL, NULL, NULL, '410', 'STANDART  BANK', NULL, '2022-10-13 15:32:06', '2022-10-13 15:32:06'),
(23, 1, NULL, NULL, NULL, '1319', NULL, NULL, '2022-10-13 15:33:50', '2022-10-13 15:33:50'),
(24, 1, NULL, NULL, NULL, '1335', NULL, NULL, '2022-10-13 15:39:09', '2022-10-13 15:39:09'),
(25, 1, NULL, NULL, NULL, '1344', NULL, NULL, '2022-10-13 15:39:47', '2022-10-13 15:39:47'),
(26, 1, NULL, NULL, NULL, '1397', NULL, NULL, '2022-10-13 15:41:39', '2022-10-13 15:41:39'),
(27, 1, NULL, NULL, NULL, '1409', NULL, NULL, '2022-10-13 15:43:06', '2022-10-13 15:43:06'),
(28, 1, NULL, NULL, NULL, '1411', NULL, NULL, '2022-10-13 15:45:52', '2022-10-13 15:45:52'),
(29, 1, NULL, NULL, NULL, NULL, '1411', NULL, '2022-10-13 15:46:51', '2022-10-13 15:46:51'),
(30, 1, NULL, NULL, NULL, NULL, '1411', NULL, '2022-10-13 15:57:21', '2022-10-13 15:57:21'),
(31, 1, NULL, NULL, NULL, '018', NULL, NULL, '2022-10-13 20:05:28', '2022-10-13 20:05:28'),
(32, 1, NULL, NULL, NULL, '001', NULL, NULL, '2022-10-13 20:06:37', '2022-10-13 20:06:37'),
(33, 1, NULL, NULL, NULL, '016', NULL, NULL, '2022-10-13 20:11:21', '2022-10-13 20:11:21'),
(34, 1, NULL, NULL, NULL, '233', NULL, NULL, '2022-10-14 14:55:54', '2022-10-14 14:55:54'),
(35, 1, NULL, NULL, NULL, '551', NULL, NULL, '2022-10-14 14:57:56', '2022-10-14 14:57:56'),
(36, 1, NULL, NULL, NULL, '624', NULL, NULL, '2022-10-14 14:58:54', '2022-10-14 14:58:54'),
(37, 1, NULL, NULL, NULL, '691', NULL, NULL, '2022-10-14 15:06:36', '2022-10-14 15:06:36'),
(38, 1, NULL, NULL, NULL, '767', NULL, NULL, '2022-10-14 15:08:30', '2022-10-14 15:08:30'),
(39, 1, NULL, NULL, NULL, '767', NULL, NULL, '2022-10-14 15:14:44', '2022-10-14 15:14:44'),
(40, 1, NULL, NULL, NULL, '767', NULL, NULL, '2022-10-14 15:16:07', '2022-10-14 15:16:07'),
(41, 1, NULL, NULL, NULL, '233', NULL, NULL, '2022-10-14 15:21:52', '2022-10-14 15:21:52'),
(42, 1, NULL, NULL, NULL, '551', NULL, NULL, '2022-10-14 15:22:38', '2022-10-14 15:22:38'),
(43, 1, NULL, NULL, NULL, '624', NULL, NULL, '2022-10-14 15:23:23', '2022-10-14 15:23:23'),
(44, 1, NULL, NULL, NULL, '691', NULL, NULL, '2022-10-14 15:24:04', '2022-10-14 15:24:04'),
(45, 1, NULL, NULL, NULL, '767', NULL, NULL, '2022-10-14 15:24:46', '2022-10-14 15:24:46'),
(46, 1, NULL, NULL, NULL, '767', NULL, NULL, '2022-10-14 15:25:46', '2022-10-14 15:25:46'),
(47, 1, NULL, NULL, NULL, '767', NULL, NULL, '2022-10-14 15:29:57', '2022-10-14 15:29:57'),
(48, 1, NULL, NULL, NULL, '068', NULL, NULL, '2022-10-14 16:04:44', '2022-10-14 16:04:44'),
(49, 1, NULL, NULL, NULL, '059', NULL, NULL, '2022-10-14 16:05:34', '2022-10-14 16:05:34'),
(50, 1, NULL, NULL, NULL, '118', NULL, NULL, '2022-10-14 16:06:49', '2022-10-14 16:06:49'),
(51, 1, NULL, NULL, NULL, '130', NULL, NULL, '2022-10-14 16:07:25', '2022-10-14 16:07:25'),
(52, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-14 16:08:18', '2022-10-14 16:08:18'),
(53, 1, NULL, NULL, NULL, '189', NULL, NULL, '2022-10-14 16:09:01', '2022-10-14 16:09:01'),
(54, 1, NULL, NULL, NULL, '114', NULL, NULL, '2022-10-14 16:38:16', '2022-10-14 16:38:16'),
(55, 1, NULL, NULL, NULL, '114', NULL, NULL, '2022-10-14 16:39:58', '2022-10-14 16:39:58'),
(56, 1, NULL, NULL, NULL, '230', NULL, NULL, '2022-10-14 16:40:55', '2022-10-14 16:40:55'),
(57, 1, NULL, NULL, NULL, '235', NULL, NULL, '2022-10-14 16:41:38', '2022-10-14 16:41:38'),
(58, 1, NULL, NULL, NULL, '244', NULL, NULL, '2022-10-14 16:42:04', '2022-10-14 16:42:04'),
(59, 1, NULL, NULL, NULL, '301', NULL, NULL, '2022-10-14 16:43:40', '2022-10-14 16:43:40'),
(60, 1, NULL, NULL, NULL, '303', NULL, NULL, '2022-10-14 16:44:14', '2022-10-14 16:44:14'),
(61, 1, NULL, NULL, NULL, '172', NULL, NULL, '2022-10-14 16:54:41', '2022-10-14 16:54:41'),
(62, 1, NULL, NULL, NULL, '172', 'FNB', NULL, '2022-10-14 16:55:49', '2022-10-14 16:55:49'),
(63, 1, NULL, NULL, NULL, '172', NULL, NULL, '2022-10-14 16:57:50', '2022-10-14 16:57:50'),
(64, 1, NULL, NULL, NULL, '369', NULL, NULL, '2022-10-14 16:58:27', '2022-10-14 16:58:27'),
(65, 1, NULL, NULL, NULL, '431', NULL, NULL, '2022-10-14 16:59:00', '2022-10-14 16:59:00'),
(66, 1, NULL, NULL, NULL, '524', NULL, NULL, '2022-10-14 16:59:25', '2022-10-14 16:59:25'),
(67, 1, NULL, NULL, NULL, '549', NULL, NULL, '2022-10-14 16:59:53', '2022-10-14 16:59:53'),
(68, 1, NULL, NULL, NULL, '554', NULL, NULL, '2022-10-14 17:00:22', '2022-10-14 17:00:22'),
(69, 1, NULL, NULL, NULL, '579', NULL, NULL, '2022-10-14 17:00:48', '2022-10-14 17:00:48'),
(70, 1, NULL, NULL, NULL, '592', NULL, NULL, '2022-10-14 17:02:07', '2022-10-14 17:02:07'),
(71, 1, NULL, NULL, NULL, '627', NULL, NULL, '2022-10-14 17:02:42', '2022-10-14 17:02:42'),
(72, 1, NULL, NULL, NULL, '649', NULL, NULL, '2022-10-14 17:03:19', '2022-10-14 17:03:19'),
(73, 1, NULL, NULL, NULL, '684', NULL, NULL, '2022-10-14 17:04:56', '2022-10-14 17:04:56'),
(74, 1, NULL, NULL, NULL, '693', NULL, NULL, '2022-10-14 17:05:37', '2022-10-14 17:05:37'),
(75, 1, NULL, NULL, NULL, '722', NULL, NULL, '2022-10-14 17:06:16', '2022-10-14 17:06:16'),
(76, 1, NULL, NULL, NULL, '722', NULL, NULL, '2022-10-14 17:06:19', '2022-10-14 17:06:19'),
(77, 1, NULL, NULL, NULL, '722', NULL, NULL, '2022-10-14 19:51:56', '2022-10-14 19:51:56'),
(78, 1, NULL, NULL, NULL, '820', NULL, NULL, '2022-10-14 19:55:59', '2022-10-14 19:55:59'),
(79, 1, NULL, NULL, NULL, '839', NULL, NULL, '2022-10-14 19:56:41', '2022-10-14 19:56:41'),
(80, 1, NULL, NULL, NULL, '820', NULL, NULL, '2022-10-14 19:58:14', '2022-10-14 19:58:14'),
(81, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2022-10-17 13:14:36', '2022-10-17 13:14:36'),
(82, 1, NULL, NULL, NULL, '369', NULL, NULL, '2022-10-17 13:50:50', '2022-10-17 13:50:50'),
(83, 1, NULL, NULL, NULL, '431', NULL, NULL, '2022-10-17 13:51:25', '2022-10-17 13:51:25'),
(84, 1, NULL, NULL, NULL, '524', NULL, NULL, '2022-10-17 13:52:08', '2022-10-17 13:52:08'),
(85, 1, NULL, NULL, NULL, '549', NULL, NULL, '2022-10-17 13:52:37', '2022-10-17 13:52:37'),
(86, 1, NULL, NULL, NULL, '554', NULL, NULL, '2022-10-17 13:53:31', '2022-10-17 13:53:31'),
(87, 1, NULL, NULL, NULL, '579', NULL, NULL, '2022-10-17 13:54:00', '2022-10-17 13:54:00'),
(88, 1, NULL, NULL, NULL, '592', NULL, NULL, '2022-10-17 13:55:07', '2022-10-17 13:55:07'),
(89, 1, NULL, NULL, NULL, '627', NULL, NULL, '2022-10-17 13:55:40', '2022-10-17 13:55:40'),
(90, 1, NULL, NULL, NULL, '649', NULL, NULL, '2022-10-17 13:56:14', '2022-10-17 13:56:14'),
(91, 1, NULL, NULL, NULL, '684', NULL, NULL, '2022-10-17 13:57:10', '2022-10-17 13:57:10'),
(92, 1, NULL, NULL, NULL, '693', NULL, NULL, '2022-10-17 13:58:05', '2022-10-17 13:58:05'),
(93, 1, NULL, NULL, NULL, '722', NULL, NULL, '2022-10-17 13:58:43', '2022-10-17 13:58:43'),
(94, 1, NULL, NULL, NULL, '789', NULL, NULL, '2022-10-17 13:59:26', '2022-10-17 13:59:26'),
(95, 1, NULL, NULL, NULL, '820', NULL, NULL, '2022-10-17 13:59:54', '2022-10-17 13:59:54'),
(96, 1, NULL, NULL, NULL, '839', NULL, NULL, '2022-10-17 14:00:24', '2022-10-17 14:00:24'),
(97, 3, NULL, NULL, NULL, '405', NULL, NULL, '2022-10-19 14:25:53', '2022-10-19 14:25:53'),
(98, 3, NULL, NULL, NULL, '405', NULL, NULL, '2022-10-19 14:27:42', '2022-10-19 14:27:42'),
(99, 1, NULL, NULL, NULL, '405', NULL, NULL, '2022-10-19 14:42:50', '2022-10-19 14:42:50'),
(100, 1, NULL, NULL, NULL, '1331', NULL, NULL, '2022-10-19 16:11:41', '2022-10-19 16:11:41'),
(101, 1, NULL, NULL, NULL, '1389', NULL, NULL, '2022-10-19 16:14:59', '2022-10-19 16:14:59');

-- --------------------------------------------------------

--
-- Table structure for table `payment_types`
--

CREATE TABLE `payment_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_cash` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_types`
--

INSERT INTO `payment_types` (`id`, `name`, `notes`, `is_cash`, `created_at`, `updated_at`) VALUES
(1, 'Cash', NULL, 0, '2018-09-15 09:28:29', '2018-09-15 09:28:29'),
(2, 'Cheque', NULL, 0, '2018-09-15 09:28:50', '2018-09-15 09:28:50'),
(3, 'bank deposit', NULL, 0, '2022-10-17 19:37:34', '2022-10-17 19:37:34'),
(4, 'BANK TRANSFER', NULL, 0, '2022-10-19 14:56:17', '2022-10-19 14:56:17'),
(5, 'EWALLET', NULL, 0, '2022-10-19 14:56:31', '2022-10-19 14:56:31');

-- --------------------------------------------------------

--
-- Table structure for table `payment_type_details`
--

CREATE TABLE `payment_type_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` enum('loan','savings','share','client','journal') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` int(11) NOT NULL,
  `account_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cheque_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `routing_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(10) UNSIGNED NOT NULL,
  `payroll_template_id` int(10) UNSIGNED DEFAULT NULL,
  `gl_account_expense_id` int(10) UNSIGNED DEFAULT NULL,
  `gl_account_asset_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `office_id` int(10) UNSIGNED DEFAULT NULL,
  `employee_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_type_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `date` date DEFAULT NULL,
  `year` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `month` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recurring` tinyint(4) NOT NULL DEFAULT '0',
  `recur_frequency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '31',
  `recur_start_date` date DEFAULT NULL,
  `recur_end_date` date DEFAULT NULL,
  `recur_next_date` date DEFAULT NULL,
  `recur_type` enum('days','weeks','months','years') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'months',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`id`, `payroll_template_id`, `gl_account_expense_id`, `gl_account_asset_id`, `user_id`, `office_id`, `employee_name`, `business_name`, `payment_method`, `payment_type_id`, `bank_name`, `account_number`, `description`, `comments`, `paid_amount`, `date`, `year`, `month`, `recurring`, `recur_frequency`, `recur_start_date`, `recur_end_date`, `recur_next_date`, `recur_type`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, NULL, 2, NULL, 'Tim Banda', 'Nyali Cashline', 'Cash', NULL, 'FNB', '5677777', 'PAID', 'nil', '4594.88', '2018-10-31', '2018', '10', 1, '1', '2018-01-01', '2018-12-31', '2018-01-01', 'months', '2018-11-22 20:52:58', '2018-11-22 20:52:58');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_meta`
--

CREATE TABLE `payroll_meta` (
  `id` int(10) UNSIGNED NOT NULL,
  `payroll_id` int(10) UNSIGNED NOT NULL,
  `payroll_template_meta_id` int(10) UNSIGNED DEFAULT NULL,
  `value` decimal(65,2) DEFAULT NULL,
  `is_tax` tinyint(4) DEFAULT '0',
  `is_percentage` tinyint(4) DEFAULT '0',
  `position` enum('top_left','top_right','bottom_left','bottom_right') COLLATE utf8mb4_unicode_ci DEFAULT 'bottom_left',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payroll_meta`
--

INSERT INTO `payroll_meta` (`id`, `payroll_id`, `payroll_template_meta_id`, `value`, `is_tax`, `is_percentage`, `position`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '0.45', 0, 0, 'bottom_right', '2018-11-22 20:52:58', '2018-11-22 20:52:58'),
(2, 1, 3, '4500.00', 0, 0, 'bottom_left', '2018-11-22 20:52:58', '2018-11-22 20:52:58'),
(3, 1, 4, '24.67', 0, 0, 'bottom_right', '2018-11-22 20:52:58', '2018-11-22 20:52:58'),
(4, 1, 5, '120.00', 0, 0, 'bottom_left', '2018-11-22 20:52:58', '2018-11-22 20:52:58');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_templates`
--

CREATE TABLE `payroll_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `picture` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(4) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payroll_templates`
--

INSERT INTO `payroll_templates` (`id`, `name`, `notes`, `picture`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Default', 'Default Payroll Template', 'default_payroll_template.jpg', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payroll_template_meta`
--

CREATE TABLE `payroll_template_meta` (
  `id` int(10) UNSIGNED NOT NULL,
  `payroll_template_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` enum('top_left','top_right','bottom_left','bottom_right','none') COLLATE utf8mb4_unicode_ci DEFAULT 'bottom_left',
  `type` enum('addition','deduction') COLLATE utf8mb4_unicode_ci DEFAULT 'addition',
  `is_default` tinyint(4) NOT NULL DEFAULT '0',
  `is_tax` tinyint(4) NOT NULL DEFAULT '0',
  `is_percentage` tinyint(4) NOT NULL DEFAULT '0',
  `tax_on` enum('net','gross') COLLATE utf8mb4_unicode_ci DEFAULT 'net',
  `default_value` decimal(65,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payroll_template_meta`
--

INSERT INTO `payroll_template_meta` (`id`, `payroll_template_id`, `name`, `position`, `type`, `is_default`, `is_tax`, `is_percentage`, `tax_on`, `default_value`, `created_at`, `updated_at`) VALUES
(2, 1, 'NAPSA', 'bottom_right', 'addition', 0, 0, 0, 'net', NULL, '2018-11-22 20:48:25', '2018-11-22 20:48:25'),
(3, 1, 'WAGE', 'bottom_left', 'addition', 0, 0, 0, 'net', NULL, '2018-11-22 20:48:40', '2018-11-22 20:48:40'),
(4, 1, 'PAYE', 'bottom_right', 'addition', 0, 0, 0, 'net', NULL, '2018-11-22 20:48:56', '2018-11-22 20:48:56'),
(5, 1, 'Allowance', 'bottom_left', 'addition', 0, 0, 0, 'net', NULL, '2018-11-22 20:49:16', '2018-11-22 20:49:16'),
(6, 1, 'Salary advance', 'bottom_right', 'addition', 0, 0, 0, 'net', NULL, '2022-10-19 15:02:47', '2022-10-19 15:02:47'),
(7, 1, 'NHIMA', 'bottom_right', 'addition', 0, 0, 0, 'net', NULL, '2022-10-19 15:03:01', '2022-10-19 15:03:01'),
(8, 1, 'Bonus', 'bottom_left', 'addition', 0, 0, 0, 'net', NULL, '2022-10-19 15:03:16', '2022-10-19 15:03:16'),
(9, 1, 'HOUSING ALLOWANCE', 'bottom_left', 'addition', 0, 0, 0, 'net', NULL, '2022-10-19 15:04:06', '2022-10-19 15:04:06'),
(10, 1, 'ALLOWANCE', 'bottom_left', 'addition', 0, 0, 0, 'net', NULL, '2022-10-19 15:05:04', '2022-10-19 15:05:04');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(11) DEFAULT '0',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `parent_id`, `name`, `slug`, `description`) VALUES
(1, 0, 'Users', 'users', 'Access Users Module'),
(2, 1, 'View Users', 'users.view', 'View Users'),
(3, 1, 'Create Users', 'users.create', 'Create Users'),
(4, 1, 'Update Users', 'users.update', 'Update Users'),
(5, 1, 'Delete Users', 'users.delete', 'Delete users'),
(7, 1, 'View Roles', 'users.roles.view', 'View Roles'),
(8, 1, 'Create Roles', 'users.roles.create', 'Create Roles'),
(9, 1, 'Update Roles', 'users.roles.update', 'Update Roles'),
(10, 1, 'Delete Roles', 'users.roles.delete', 'Delete Roles'),
(11, 1, 'Assign Roles', 'users.roles.assign', 'Assign User Roles'),
(12, 0, 'Settings', 'settings', 'Access Settings Module'),
(13, 12, 'Update Settings', 'settings.update', 'Update Settings'),
(14, 12, 'View General Settings', 'settings.general.view', 'View General Settings'),
(15, 12, 'View Organisation Settings', 'settings.organisation.view', 'View Organisation Settings'),
(16, 0, 'Accounting', 'accounting', 'Access Accounting Module'),
(17, 16, 'View Chart of Accounts', 'accounting.gl_accounts.view', 'View Chart of Accounts'),
(18, 16, 'Create Chart of Accounts', 'accounting.gl_accounts.create', 'Create Chart of Accounts'),
(19, 16, 'Update Chart of Accounts', 'accounting.gl_accounts.update', 'Update Chart of Accounts'),
(20, 16, 'Delete Chart of Accounts', 'accounting.gl_accounts.delete', 'Delete Chart of Accounts'),
(21, 16, 'View Journals', 'accounting.journals.view', 'View Journals'),
(22, 16, 'Create Manual Journals', 'accounting.journals.create', 'Create Manual Journals'),
(23, 16, 'Delete Manual Journals', 'accounting.journals.delete', 'Delete Manual Journals'),
(24, 16, 'Approve Manual Journals', 'accounting.journals.approve', 'Approve Manual Journals'),
(25, 16, 'View Reconciliations', 'accounting.journals.reconciliation.view', 'View Reconciliations'),
(26, 16, 'Reconcile Journal Entries', 'accounting.journals.reconciliation.create', 'Reconcile Journal Entries'),
(27, 16, 'View Accounting Close Period', 'accounting.period.view', 'View  Accounting Close Period'),
(28, 16, 'Create Accounting Close Period', 'accounting.period.create', 'Create Accounting Close Period'),
(29, 16, 'Delete Accounting Close Period', 'accounting.period.delete', 'Delete Accounting Close Period'),
(30, 0, 'Audit Trail', 'audit_trail', 'Access Audit Trail Module'),
(31, 0, 'Branches', 'offices', 'Access Branches Module'),
(32, 31, 'View Branches', 'offices.view', 'View Branches'),
(33, 31, 'Create Branches', 'offices.create', 'Create Branches'),
(34, 31, 'Update Branches', 'offices.update', 'Update Branches'),
(35, 31, 'Delete Branches', 'offices.delete', 'Delete Branches'),
(36, 0, 'Clients', 'clients', 'Access Clients Module'),
(37, 36, 'View Clients', 'clients.view', 'View Clients'),
(38, 36, 'Create Clients', 'clients.create', 'Create Clients'),
(39, 36, 'Update Clients', 'clients.update', 'Update Clients'),
(40, 36, 'Delete Clients', 'clients.delete', 'Delete Clients'),
(41, 36, 'Approve Clients', 'clients.approve', 'Approve Clients'),
(42, 36, 'Close Client', 'clients.close', 'Close Client'),
(43, 36, 'View  Client Pending approval', 'clients.pending_approval', 'View  Client Pending approval'),
(44, 36, 'View Closed Clients', 'clients.closed', 'View Closed Clients'),
(45, 36, 'Create Client Document', 'clients.documents.create', 'Create Client Document'),
(46, 36, 'Read Client Document', 'clients.documents.view', 'Read Client Document'),
(47, 36, 'Delete Client Document', 'clients.documents.delete', 'Delete Client Document'),
(48, 36, 'Update Client Document', 'clients.documents.update', 'Update Client Document'),
(49, 36, 'Read Next Of Kin', 'clients.next_of_kin.view', 'Read Next Of Kin'),
(50, 36, 'Create Next Of Kin', 'clients.next_of_kin.create', 'Create Next Of Kin'),
(51, 36, 'Update Next Of Kin', 'clients.next_of_kin.update', 'Update Next Of Kin'),
(52, 36, 'Delete Next Of Kin', 'clients.next_of_kin.delete', 'Delete Next Of Kin'),
(53, 36, 'Read Client Identifiers', 'clients.identification.view', 'Read Client Identifiers'),
(54, 36, 'Create Client Identifiers', 'clients.identification.create', 'Create Client Identifiers'),
(55, 36, 'Update Client Identifiers', 'clients.identification.update', 'Update Client Identifiers'),
(56, 36, 'Delete Client identifier', 'clients.identification.delete', 'Delete Client identifier'),
(57, 36, 'Read Client notes', 'clients.notes.view', 'Read Client notes'),
(58, 36, 'Create Client notes', 'clients.notes.create', 'Create Client notes'),
(59, 36, 'Update Client notes', 'clients.notes.update', 'Update Client notes'),
(60, 36, 'Delete Client notes', 'clients.notes.delete', 'Delete Client notes'),
(61, 36, 'Read Client Accounts', 'clients.accounts.view', 'Read Client Accounts'),
(62, 36, 'Transfer Client', 'clients.transfer.client', 'Transfer Client'),
(63, 36, 'Approve Client Transfer', 'clients.transfer.approve', 'Approve Client Transfer'),
(64, 0, 'Groups', 'groups', 'Access Groups Module'),
(65, 64, 'View Groups', 'groups.view', 'View Groups'),
(66, 64, 'Create Group', 'groups.create', 'Create Group'),
(67, 64, 'Approve Group', 'groups.approve', 'Approve Group'),
(68, 64, 'Update Groups', 'groups.update', 'Update Groups'),
(69, 64, 'Add Client to Group', 'groups.client.create', 'Add Client to Group'),
(70, 64, 'Remove Client', 'groups.client.delete', 'Remove Client'),
(71, 64, 'View Group Document', 'groups.documents.view', 'View  Group Document'),
(72, 64, 'Add Group Document', 'groups.documents.create', 'Add Group Document'),
(73, 64, 'Update Group Document', 'groups.documents.update', 'Update Group Document'),
(74, 64, 'Delete Group Document', 'groups.documents.delete', 'Delete Group Document'),
(75, 64, 'View Group Note', 'groups.notes.view', 'View Group Note'),
(76, 64, 'Create Group Note', 'groups.notes.create', 'Create Group Note'),
(77, 64, 'Update Group Note', 'groups.notes.update', 'Update Group Note'),
(78, 64, 'Delete Group Note', 'groups.notes.delete', 'Delete Group Note'),
(79, 64, 'View Assigned Groups', 'groups.view_assigned', 'View Assigned Groups'),
(80, 64, 'View Created', 'groups.view_created', 'View Created'),
(81, 36, 'View Assigned', 'clients.view_assigned', 'View Assigned'),
(82, 36, 'View Created', 'clients.view_created', 'View Created'),
(83, 0, 'Loans', 'loans', 'Access Loans Module'),
(84, 83, 'View Loans', 'loans.view', 'View Loans'),
(85, 83, 'View Pending Loans', 'loans.pending_approval', 'View Pending Loans'),
(86, 64, 'View Groups Pending Approval', 'groups.pending_approval', 'View Groups Pending Approval'),
(87, 83, 'Awaiting Disbursement', 'loans.awaiting_disbursement', 'Loans Awaiting Disbursement'),
(88, 83, 'Loans Declined', 'loans.declined', 'View Loans Declined'),
(89, 83, 'View Loans Written Off', 'loans.written_off', 'View Loans Written Off'),
(90, 83, 'View Loans Closed', 'loans.closed', 'View Loans Closed'),
(91, 83, 'View Loans Rescheduled', 'loans.rescheduled', 'View Loans Rescheduled'),
(92, 83, 'View Loans Evaluated', 'loans.evaluated', 'View Loans Evaluated'),
(93, 83, 'Create Loans', 'loans.create', 'Create Loans'),
(94, 83, 'Update Loans', 'loans.update', 'Update Loans'),
(95, 83, 'Approve Loan', 'loans.approve', 'Approve Loan'),
(96, 83, 'Disburse Loans', 'loans.disburse', 'Disburse Loans'),
(97, 83, 'Undo Approval', 'loans.undo_approval', 'Disburse Loans'),
(98, 83, 'Undo Disbursement', 'loans.undo_disbursement', 'Undo Disbursement'),
(99, 83, 'Write off loan', 'loans.write_off', 'Write off loan'),
(100, 83, 'Undo Write off', 'loans.undo_write_off', 'Undo Write off'),
(101, 83, 'Waive Loan Interest', 'loans.waive_interest', 'Waive Loan Interest'),
(102, 83, 'Apply charge to loan', 'loans.charge.create', 'Apply charge to loan'),
(103, 83, 'Waive Loan Charge', 'loans.charge.waive', 'Waive Loan Charge'),
(104, 83, 'View Assigned Loans', 'loans.view_assigned', 'View Assigned Loans'),
(105, 83, 'Create Loan Reschedule', 'loans.reschedule.create', 'Create Loan Reschedule'),
(106, 83, 'Make Repayment', 'loans.transactions.create', 'Make Repayment'),
(107, 83, 'View Transactions', 'loans.transactions.view', 'View Transactions'),
(108, 83, 'Approve Loan Repayment', 'loans.transactions.approve', 'Approve Loan Repayment'),
(109, 83, 'Adjust Loan Transaction', 'loans.transactions.update', 'Adjust Loan Transaction'),
(110, 83, 'View System Reversed Transactions', 'loans.transactions.system_reversed', 'View System Reversed Transactions'),
(111, 83, 'View Loan Repayment Schedule', 'loans.view_repayment_schedule', 'View Loan Repayment Schedule'),
(112, 83, 'View Loan Documents', 'loans.documents.view', 'View Loan Documents'),
(113, 83, 'Create Loan Documents', 'loans.documents.create', 'Create Loan Documents'),
(114, 83, 'Update Loan Documents', 'loans.documents.update', 'Update Loan Documents'),
(115, 83, 'Delete Loan Documents', 'loans.documents.delete', 'Delete Loan Documents'),
(116, 83, 'View Collateral', 'loans.collateral.view', 'View Collateral'),
(117, 83, 'Create Collateral', 'loans.collateral.create', 'Create Collateral'),
(118, 83, 'Update Collateral', 'loans.collateral.update', 'Update Collateral'),
(119, 83, 'Delete Collateral', 'loans.collateral.delete', 'Delete Collateral'),
(120, 83, 'View Guarantors', 'loans.guarantors.view', 'View Guarantors'),
(121, 83, 'Create Guarantors', 'loans.guarantors.create', 'Create Guarantors'),
(122, 83, 'Update Guarantors', 'loans.guarantors.update', 'Update Guarantors'),
(123, 83, 'Delete Guarantors', 'loans.guarantors.delete', 'Delete Guarantors'),
(124, 83, 'View Loan Notes', 'loans.notes.view', 'View Loan Notes'),
(125, 83, 'Create Loan Notes', 'loans.notes.create', 'Create Loan Notes'),
(126, 83, 'Update Loan Notes', 'loans.notes.update', 'Update Loan Notes'),
(127, 83, 'Delete Loan Notes', 'loans.notes.delete', 'Delete Loan Notes'),
(128, 83, 'View Group Allocation', 'loans.view_group_allocation', 'View Group Allocation'),
(129, 83, 'View Client Details', 'loans.view_client_details', 'View Client Details'),
(130, 83, 'Email Schedule', 'loans.email_schedule', 'Email Schedule'),
(131, 83, 'Pdf Schedule', 'loans.pdf_schedule', 'Pdf Schedule'),
(132, 0, 'Savings', 'savings', 'Access Savings Module'),
(133, 132, 'View Savings', 'savings.view', 'View Savings'),
(134, 132, 'View Savings Pending Approval', 'savings.pending_approval', 'View Savings Pending Approval'),
(135, 132, 'View Approved Savings Accounts', 'savings.approved', 'View Approved Savings Accounts'),
(136, 132, 'View Savings Closed', 'savings.closed', 'View Savings Closed'),
(137, 132, 'Create Savings', 'savings.create', 'Create Savings'),
(138, 132, 'Update Savings', 'savings.update', 'Update Savings'),
(139, 132, 'Delete Savings', 'savings.delete', 'Delete Savings'),
(140, 132, 'Approve Savings', 'savings.approve', 'Approve Savings'),
(141, 132, 'Undo Approval', 'savings.undo_approval', 'Undo Approval'),
(142, 132, 'Close Savings Account', 'savings.close', 'Close Savings Account'),
(143, 132, 'View Transactions', 'savings.transactions.view', 'View Transactions'),
(144, 132, 'Create Transactions', 'savings.transactions.create', 'Create Transactions'),
(145, 132, 'Update Transactions', 'savings.transactions.update', 'Update Transactions'),
(146, 132, 'Delete Transactions', 'savings.transactions.delete', 'Delete Transaction'),
(147, 132, 'View Documents', 'savings.documents.view', 'View Documents'),
(148, 132, 'Create Savings Documents', 'savings.documents.create', 'Create Savings Documents'),
(149, 132, 'Update Savings Documents', 'savings.documents.update', 'Update Savings Documents'),
(150, 132, 'Delete Savings Documents', 'savings.documents.delete', 'Delete Savings Documents'),
(151, 132, 'View Savings Notes', 'savings.notes.view', 'View Savings Notes'),
(152, 132, 'Create Savings Notes', 'savings.notes.create', 'Create Savings Notes'),
(153, 132, 'Update Savings Notes', 'savings.notes.update', 'Update Savings Notes'),
(154, 132, 'Delete Savings Notes', 'savings.notes.delete', 'Delete Savings Notes'),
(155, 132, 'Post Interest', 'savings.post_interest', 'Post Interest'),
(156, 132, 'Email Statement', 'savings.email_statement', 'Email Statement'),
(157, 132, 'Pdf Statement', 'savings.pdf_statement', 'Pdf Statement'),
(158, 132, 'Add Charge To Savings Account', 'savings.charge.create', 'Add Charge To Savings Account'),
(159, 132, 'Waive Saving Account Charge', 'savings.charge.waive', 'Waive Saving Account Charge'),
(160, 132, 'Approve Savings Transaction', 'savings.transactions.approve', 'Approve Savings Transaction'),
(161, 132, 'Make Deposit', 'savings.transactions.deposit', 'Make Deposit'),
(162, 132, 'Make Withdrawal', 'savings.transactions.withdrawal', 'Make Withdrawal'),
(163, 0, 'Products', 'products', 'Access Savings & Loan Products and related modules'),
(164, 163, 'View Charges', 'products.charges.view', 'View Charges'),
(165, 163, 'Create Charge', 'products.charges.create', 'Create Charge'),
(166, 163, 'Update Charge', 'products.charges.update', 'Update Charge'),
(167, 163, 'Delete Charge', 'products.charges.delete', 'Delete Charge'),
(168, 163, 'View Currencies', 'products.currencies.view', 'View Currencies'),
(169, 163, 'Create Currency', 'products.currencies.create', 'Create Currency'),
(170, 163, 'Update Currency', 'products.currencies.update', 'Update Currency'),
(171, 163, 'Delete Currencies', 'products.currencies.delete', 'Delete Currencies'),
(172, 163, 'Funds', 'products.funds.view', 'Funds'),
(173, 163, 'Create Funds', 'products.funds.create', 'Create Funds'),
(174, 163, 'Update Funds', 'products.funds.update', 'Update Funds'),
(175, 163, 'Delete Funds', 'products.funds.delete', 'Delete Funds'),
(176, 163, 'View Payment Types', 'products.payment_types.view', 'View Payment Types'),
(177, 163, 'Create Payment types', 'products.payment_types.create', 'Create Payment types'),
(178, 163, 'Update Payment Types', 'products.payment_types.update', 'Update Payment Types'),
(179, 163, 'Delete Payment Types', 'products.payment_types.delete', 'Delete Payment Types'),
(180, 163, 'View Loan Purpose', 'products.loan_purposes.view', 'View Loan Purpose'),
(181, 163, 'Create Loan Purpose', 'products.loan_purposes.create', 'Create Loan Purpose'),
(182, 163, 'Delete Loan Purpose', 'products.loan_purposes.delete', 'Delete Loan Purpose'),
(183, 163, 'Update Loan Purpose', 'products.loan_purposes.update', 'Update Loan Purpose'),
(184, 163, 'View Collateral Types', 'products.collateral_types.view', 'View Collateral Types'),
(185, 163, 'Create Collateral Types', 'products.collateral_types.create', 'Create Collateral Types'),
(186, 163, 'Update Collateral Types', 'products.collateral_types.update', 'Update Collateral Types'),
(187, 163, 'Delete Collateral Types', 'products.collateral_types.delete', 'Delete Collateral Types'),
(188, 163, 'View Client Relationship', 'products.client_relationships.view', 'View Client Relationship'),
(189, 163, 'Create Client Relationship', 'products.client_relationships.create', 'Create Client Relationship'),
(190, 163, 'Update Client Relationship', 'products.client_relationships.update', 'Update Client Relationship'),
(191, 163, 'Delete Client Relationship', 'products.client_relationships.delete', 'Delete Client Relationship'),
(192, 163, 'View Client Identification Type', 'products.client_identification_types.view', 'View Client Identification Type'),
(193, 163, 'Create Client Identification Type', 'products.client_identification_types.create', 'Create Client Identification Type'),
(194, 163, 'Update Client Identification Type', 'products.client_identification_types.update', 'Update Client Identification Type'),
(195, 163, 'Delete Client Identification Type', 'products.client_identification_types.delete', 'Delete Client Identification Type'),
(196, 163, 'Manage Loan Provisioning Criteria', 'products.loan_provisioning_criteria.update', 'Manage Loan Provisioning Criteria'),
(197, 163, 'View Loan Products', 'products.loan_products.view', 'View Loan Products'),
(198, 163, 'Create Loan Products', 'products.loan_products.create', 'Create Loan Products'),
(199, 163, 'Update Loan Products', 'products.loan_products.update', 'Update Loan Products'),
(200, 163, 'Delete Loan Products', 'products.loan_products.delete', 'Delete Loan Products'),
(201, 163, 'View Savings Products', 'products.savings_products.view', 'View Savings Products'),
(202, 163, 'Create Savings Products', 'products.savings_products.create', 'Create View Savings Products'),
(203, 163, 'Update Savings Products', 'products.savings_products.update', 'Update Savings Products'),
(204, 163, 'Delete Savings Products', 'products.savings_products.delete', 'Delete Savings Products'),
(205, 0, 'Reports', 'reports', 'Access Reports Module'),
(206, 205, 'Downloading/Exporting of Reports', 'reports.downloading_exporting_of_reports', 'Downloading/Exporting of Reports'),
(207, 205, 'Client Reports', 'reports.client_reports', 'Access Client Reports Menu'),
(208, 205, 'Loan Reports', 'reports.loan_reports', 'Access Loan Reports Menu'),
(209, 205, 'Financial Reports', 'reports.financial_reports', 'Financial Reports'),
(210, 205, 'Savings Reports', 'reports.savings_reports', 'Access Savings Reports Menu'),
(211, 205, 'Reports Scheduler', 'reports.reports_scheduler', 'Access Reports Scheduler Menu'),
(212, 205, 'Client Numbers Report', 'reports.client_numbers_report', 'Client Numbers Report'),
(213, 205, 'Collection Sheet', 'reports.collection_sheet', 'Collection Sheet'),
(214, 205, 'Repayments Report', 'reports.repayments_report', 'Repayments Report'),
(215, 205, 'Expected Repayment', 'reports.expected_repayment', 'Expected Repayment'),
(216, 205, 'Arrears Report', 'reports.arrears_report', 'Arrears Report'),
(217, 205, 'Disbursed Loans', 'reports.disbursed_loans', 'Disbursed Loans'),
(218, 205, 'Loan Portfolio', 'reports.loan_portfolio', 'Loan Portfolio'),
(219, 205, 'Balance Sheet', 'reports.balance_sheet', 'Balance Sheet'),
(220, 205, 'Trial Balance', 'reports.trial_balance', 'Trial Balance'),
(221, 205, 'Income Statement', 'reports.income_statement', 'Income Statement'),
(222, 205, 'Provisioning', 'reports.provisioning', 'Provisioning'),
(223, 205, 'Journals Report', 'reports.journals_report', 'Journals Report'),
(224, 205, 'Savings Transactions', 'reports.savings_transactions', 'Savings Transactions'),
(225, 205, 'Savings Accounts Report', 'reports.savings_accounts_report', 'Savings Accounts Report'),
(226, 205, 'View Report Scheduler', 'reports.reports_scheduler.view', 'View Report Scheduler'),
(227, 205, 'Create Report Scheduler', 'reports.reports_scheduler.create', 'Create Report Scheduler'),
(228, 205, 'Update Report Scheduler', 'reports.reports_scheduler.update', 'Update Report Scheduler'),
(229, 205, 'Delete Report Scheduler', 'reports.reports_scheduler.delete', 'Delete Report Scheduler'),
(230, 0, 'Communication', 'communication', 'Access Communication Module'),
(231, 230, 'View Campaigns', 'communication.view', 'View Campaigns'),
(232, 230, 'Create Campaign', 'communication.create', 'Create Campaign'),
(233, 230, 'Update Campaign', 'communication.update', 'Update Campaign'),
(234, 230, 'Delete Campaign', 'communication.delete', 'Delete Campaign'),
(235, 230, 'Approve Campaign', 'communication.approve', 'Approve Campaign'),
(236, 0, 'Dashboard', 'dashboard', 'Dashboard'),
(237, 236, 'Loans Disbursed', 'dashboard.loans_disbursed', 'View Loans Disbursed'),
(238, 236, 'Total Repayments', 'dashboard.total_repayments', 'Total Repayments'),
(239, 236, 'Total Outstanding', 'dashboard.total_outstanding', 'Total Outstanding'),
(240, 236, 'Amount in Arrears', 'dashboard.amount_in_arrears', 'Amount in Arrears'),
(241, 236, 'Fees Earned', 'dashboard.fees_earned', 'Fees Earned'),
(242, 236, 'Fees Paid', 'dashboard.fees_paid', 'Fees Paid'),
(243, 236, 'Penalties Paid', 'dashboard.penalties_paid', 'Penalties Paid'),
(244, 236, 'Penalties Earned', 'dashboard.penalties_earned', 'Penalties Earned'),
(245, 236, 'Loans Status Overview', 'dashboard.loans_status_overview', 'Loans Status Overview'),
(246, 236, 'Clients Overview', 'dashboard.clients_overview', 'Clients Overview'),
(247, 236, 'Savings Balances Overview', 'dashboard.savings_balances_overview', 'Savings Balances Overview'),
(248, 236, 'My Loan Repayments', 'dashboard.my_loan_repayments', 'My Loan Repayments'),
(249, 236, 'My Disbursed loans', 'dashboard.my_disbursed_loans', 'My Disbursed loans'),
(250, 236, 'My Number of outstanding loans', 'dashboard.my_number_of_outstanding_loans', 'My Number of outstanding loans'),
(251, 236, 'My Outstanding Loan balance', 'dashboard.my_outstanding_loan_balance', 'My Outstanding Loan balance'),
(252, 236, 'My Clients', 'dashboard.my_clients', 'My Clients'),
(253, 236, 'My written off Amount', 'dashboard.my_written_off_amount', 'My written off Amount'),
(254, 236, 'Collection Statistics', 'dashboard.collection_statistics', 'Collection Statistics'),
(255, 0, 'Custom Fields', 'custom_fields', 'Access Custom Fields'),
(256, 255, 'View Custom Fields', 'custom_fields.view', 'View Custom Fields'),
(257, 255, 'Create Custom Fields', 'custom_fields.create', 'Create Custom Fields'),
(258, 255, 'Update Custom Fields', 'custom_fields.update', 'Update Custom Fields'),
(259, 255, 'Delete Custom Fields', 'custom_fields.delete', 'Delete Custom Fields'),
(260, 0, 'Assets', 'assets', 'Access assets module'),
(261, 260, 'View Assets', 'assets.view', 'View Assets'),
(262, 260, 'Create Assets', 'assets.create', 'Create Assets'),
(263, 260, 'Update Assets', 'assets.update', 'Update Assets'),
(264, 260, 'Delete Assets', 'assets.delete', 'Delete Assets'),
(265, 260, 'View Asset Types', 'assets.types.view', 'View Asset Types'),
(266, 260, 'Create  Asset Types', 'assets.types.create', 'Create  Asset Types'),
(267, 260, 'Update Asset Types', 'assets.types.update', 'Update Asset Types'),
(268, 260, 'Delete Asset Types', 'assets.types.delete', 'Delete Asset Types'),
(269, 0, 'Expenses', 'expenses', 'View Expenses Module'),
(270, 269, 'View Expenses Module', 'expenses.view', 'View Expenses Module'),
(271, 269, 'Create Expenses', 'expenses.create', 'Create Expenses Module'),
(272, 269, 'Update Expenses', 'expenses.update', 'Update Expenses'),
(273, 269, 'Delete Expenses', 'expenses.delete', 'Delete Expenses'),
(274, 269, 'View Expense Types', 'expenses.types.view', 'View Expense Types'),
(275, 269, 'Create Expenses Types', 'expenses.types.create', 'Create Expenses Types'),
(276, 269, 'Update Expenses Types', 'expenses.types.update', 'Update Expenses Types'),
(277, 269, 'Delete Expense Types', 'expenses.types.delete', 'Delete Expense Types'),
(278, 0, 'Other Income', 'other_income', 'View Other Income'),
(279, 278, 'View Other Income', 'other_income.view', 'View Other Income'),
(280, 278, 'Create Other Income', 'other_income.create', 'Create Other Income'),
(281, 278, 'Update Other Income', 'other_income.update', 'Update Other Income'),
(282, 278, 'Delete Other Income', 'other_income.delete', 'Delete Other Income'),
(283, 278, 'View Other Income types', 'other_income.types.view', 'View Other Income types'),
(284, 278, 'Create Other Income types', 'other_income.types.create', 'Create Other Income types'),
(285, 278, 'Update Other Income types', 'other_income.types.update', 'Update Other Income types'),
(286, 278, 'Delete Other Income types', 'other_income.types.delete', 'Delete Other Income types'),
(287, 269, 'View Budget', 'expenses.budget.view', 'View Budget'),
(288, 269, 'Create Budget', 'expenses.budget.create', 'Create Budget'),
(289, 269, 'Update Budget', 'expenses.budget.update', 'Update Budget'),
(290, 269, 'Delete Budget', 'expenses.budget.delete', 'Delete Budget'),
(291, 0, 'Payroll', 'payroll', 'Access Payroll templates'),
(292, 291, 'View Payroll', 'payroll.view', 'View Payroll'),
(293, 291, 'Create Payroll', 'payroll.create', NULL),
(294, 291, 'Update Payroll', 'payroll.update', NULL),
(295, 291, 'Delete Payroll', 'payroll.delete', NULL),
(297, 296, 'View Grants', 'grants.view', 'View Grants'),
(298, 296, 'Create Grant', 'grants.create', NULL),
(299, 296, 'Update Grant', 'grants.update', NULL),
(300, 296, 'Delete Grant', 'grants.delete', NULL),
(301, 296, 'Approve Grants', 'grants.approve', NULL),
(302, 296, 'Undo approval', 'grants.undo_approval', NULL),
(303, 296, 'Disburse Grants', 'grants.disburse', NULL),
(304, 296, 'Undo disbursement', 'grants.undo_disbursement', NULL),
(305, 296, 'View Grants Pending Approval', 'grants.pending_approval', NULL),
(306, 296, 'View Grants Awaiting Disbursement', 'grants.awaiting_disbursement', NULL),
(307, 296, 'View Grants Declined', 'grants.declined', NULL),
(308, 296, 'View Documents', 'grants.documents.view', NULL),
(309, 296, 'Create Documents', 'grants.documents.create', NULL),
(310, 296, 'Update Documents', 'grants.documents.update', NULL),
(311, 296, 'Delete Documents', 'grants.documents.delete', NULL),
(312, 296, 'View Notes', 'grants.notes.view', NULL),
(313, 296, 'Create Notes', 'grants.notes.create', NULL),
(314, 296, 'Update Notes', 'grants.notes.update', NULL),
(315, 296, 'Delete Notes', 'grants.notes.delete', NULL),
(316, 236, 'Grants Status Overview', 'dashboard.grants_status_overview', NULL),
(317, 236, 'Grants Disbursement Overview', 'dashboard.grants_disbursement_overview', NULL),
(318, 205, 'Grant Reports', 'reports.grant_reports', NULL),
(319, 205, 'Age Analysis', 'reports.age_analysis_reports', NULL),
(320, 205, 'Client Listing', 'reports.client_list_reports', NULL),
(321, 16, 'Create Opening Balnce', 'accounting.journals.create_op', 'Create Opening Balnce'),
(322, 205, 'Consolidated Trial Balance', 'reports.consolidated_trial_balance', ' Consolidated Trial Balance'),
(323, 205, 'Daily Transaction', 'reports.daily_transactions_reports', NULL),
(324, 205, 'Loan Book', 'reports.loan_book', NULL),
(325, 236, 'my_disbursed_loans', 'dashboard.my_disbursed', 'My disbursed Loans'),
(326, 236, 'my_repayments_loans', 'dashboard.my_repayments_loans', NULL),
(327, 236, 'my_outstanding_loans', 'dashboard.my_outstanding_loans', NULL),
(328, 236, 'my_loan_arrears', 'dashboard.my_loan_arrears', NULL),
(329, 236, 'my_branch_disbursed', 'dashboard.my_branch_disbursed', NULL),
(330, 236, 'my_branch_repayments', 'dashboard.my_branch_repayments', NULL),
(331, 236, 'my_branch_outstanding', 'dashboard.my_branch_outstanding', NULL),
(332, 236, 'my_branch_arrears', 'dashboard.my_branch_arrears', NULL),
(333, 83, 'my_loans', 'loans.my_loans', NULL),
(334, 36, 'my_clients', 'clients.my_clients', NULL),
(335, 36, 'branch_clients', 'clients.branch_clients', NULL),
(336, 83, 'branch_loans', 'loans.branch_loans', NULL),
(337, 205, 'my_collection_sheet', 'reports.my_collection_sheet', NULL),
(338, 205, 'my_repayment_report', 'reports.my_repayment_report', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `persistences`
--

CREATE TABLE `persistences` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `persistences`
--

INSERT INTO `persistences` (`id`, `user_id`, `code`, `created_at`, `updated_at`) VALUES
(0, 1, 'Hc6oidFb9WTiRmFbg4NEpgeoqwpxRsxF', '2020-05-08 07:27:02', '2020-05-08 07:27:02'),
(1, 1, 'W6PheLwRTBxrISCkTKE8i11uzdZaL4lz', '2018-09-10 10:01:44', '2018-09-10 10:01:44'),
(2, 1, '2j4c7d67mpIPs6fOUpFGT0MuW9OaCTDv', '2018-09-12 07:04:30', '2018-09-12 07:04:30'),
(3, 1, 'f1qsclwXEAKvFWEiwzoGuSL0tPZZzkVQ', '2018-09-15 09:14:42', '2018-09-15 09:14:42'),
(4, 1, 'i9gCj4Ul3ZHt9HfjiJXY4wMuxWYxjaGd', '2018-11-20 19:42:43', '2018-11-20 19:42:43'),
(5, 1, 'NaLMqS5pEoIOxYMK4qbJVgH7QDZDgans', '2018-11-21 02:11:50', '2018-11-21 02:11:50'),
(6, 1, 'zJLf6UALlAlsAj4Xbh2dynXOleKpJTyP', '2018-11-22 04:56:45', '2018-11-22 04:56:45'),
(7, 1, 'VXZIdZ5erl3skD4wT02ZyCwb59nvRbtb', '2018-11-22 12:03:58', '2018-11-22 12:03:58'),
(9, 1, 'wdu9bH3D0EZKtsyM13KeV5kbWYbpj2j5', '2018-11-22 12:10:19', '2018-11-22 12:10:19'),
(10, 1, 'gMHq7H4CS3DJFrkP7slOvYhPI1V9dJhO', '2018-11-22 18:55:40', '2018-11-22 18:55:40'),
(11, 1, 'HwEi773A7r18vasLeGydKubsY1Ay3s9P', '2018-11-23 04:44:00', '2018-11-23 04:44:00'),
(12, 1, 'Oz1I2zhkbJEjUyJ9VzGqi1yftLZ6Kvn9', '2018-11-24 09:48:48', '2018-11-24 09:48:48'),
(13, 1, 'oeT7dn0ErdBASzYOiCc5zuAmXkt5rmXK', '2018-11-30 13:02:18', '2018-11-30 13:02:18'),
(14, 1, 'UuJkZsQTLEGUDOV1BTDhUM2JMtKKP1gN', '2018-11-30 19:00:47', '2018-11-30 19:00:47'),
(15, 1, 'TcAhPoWSx8LVwMdOl5P05jQ0eX8L9qWs', '2018-12-01 05:03:51', '2018-12-01 05:03:51'),
(16, 1, 'AidIQR8QvBtnCkCwVffsRcIamFYxJsdD', '2018-12-01 08:42:16', '2018-12-01 08:42:16'),
(17, 1, 'nuXSPPRmvfhiXX6rziezOYWw2fr6tT6n', '2018-12-01 08:43:20', '2018-12-01 08:43:20'),
(21, 1, '9EDSYAW9u2aHVbn5ScDH92217oBCo3w4', '2018-12-01 09:10:58', '2018-12-01 09:10:58'),
(23, 1, 'lp2ZkWKtY8CkNf9jZUKFlXC8gEQ0YnBs', '2018-12-01 11:38:55', '2018-12-01 11:38:55'),
(25, 1, 'L0FgIf7DhSsgGV37Y3UtS9DXXfY5HjSs', '2018-12-03 10:33:24', '2018-12-03 10:33:24'),
(28, 1, 'zg5fTQ0WQkCRLd4v26MJyen06OAkLOXg', '2018-12-04 07:15:11', '2018-12-04 07:15:11'),
(29, 1, 'leAYAB27bsZKNJjYqmMgVp6PLaA5ZSUY', '2018-12-05 06:29:30', '2018-12-05 06:29:30'),
(30, 1, 'cFhHDzLkn3WCxjq62iTjsWBZ2YhTqv6f', '2018-12-12 11:27:45', '2018-12-12 11:27:45'),
(31, 1, 'pf4KWvBEMUWBWBSfYt10c3VEBmTCORrs', '2018-12-13 10:09:01', '2018-12-13 10:09:01'),
(32, 1, 'sPWSeFoSd69k1SitGGWRl92BIAgkTwVX', '2018-12-13 12:24:03', '2018-12-13 12:24:03'),
(33, 1, '3wDLVkNtFPrZjPeGTs0VWDbcGYnnhe8z', '2018-12-14 09:27:03', '2018-12-14 09:27:03'),
(35, 1, 'kFYGOnYAszH516IpJIbBKY7vMNNmMyMp', '2018-12-14 13:16:18', '2018-12-14 13:16:18'),
(36, 1, 'CAdKLIj2RXyJgMcvjN8CZTVRf7DAgRji', '2018-12-15 06:56:18', '2018-12-15 06:56:18'),
(37, 1, 'vY4rS5uD5x9kTPMtqw7vkxYgqA3QABw6', '2018-12-15 12:14:05', '2018-12-15 12:14:05'),
(38, 1, 'UY5hHbctpOUUkfkSPnZlr6lSxLKOJc9r', '2018-12-15 12:45:00', '2018-12-15 12:45:00'),
(39, 1, 'ippcxN1oAEPv11yNzrfxSeipWpxAF1FH', '2018-12-16 21:18:10', '2018-12-16 21:18:10'),
(40, 1, 'NsjsWpoPRBtmKW39JcRtLxghYJy8dBmy', '2018-12-17 06:50:13', '2018-12-17 06:50:13'),
(41, 1, 'fdZkjmCyBxu2Pj9ZI4q23NrwVLQwazYb', '2018-12-17 06:56:53', '2018-12-17 06:56:53'),
(42, 1, 'E69iE8mEUTtT1POHGVOMj1Pij9mhp3a4', '2018-12-18 07:43:00', '2018-12-18 07:43:00'),
(43, 1, 'IddZA2mMx9HncTucMBP11s9kmlvKS1QN', '2018-12-18 20:29:48', '2018-12-18 20:29:48'),
(44, 1, '2Xc3pTluvksAD2TARhnOuCKSYGT0AnDd', '2018-12-19 04:43:13', '2018-12-19 04:43:13'),
(45, 1, '05PMjzGTPWdN3vCkmndpxJJj7HBu2ddy', '2018-12-19 11:45:59', '2018-12-19 11:45:59'),
(46, 1, 'l7h9TUq3xjFrh9QnR0RCaqoEX6nrBvnn', '2018-12-27 05:43:26', '2018-12-27 05:43:26'),
(47, 1, 'OMaD2fk3cnCzjKcnx7nUALaXRRiktBfV', '2018-12-27 18:50:11', '2018-12-27 18:50:11'),
(48, 1, 'jPB3SfhEj46MicacFfxAwkAvmTraVCRi', '2018-12-27 21:31:35', '2018-12-27 21:31:35'),
(49, 1, '3WaufviobDlE27gBloRm9cPwPZn2qkBD', '2018-12-28 06:32:03', '2018-12-28 06:32:03'),
(50, 1, 'lEq5Xugjf1GJR0K7AR2n8HMHcngbLDIa', '2018-12-28 10:49:29', '2018-12-28 10:49:29'),
(51, 1, 'AP1kvsT2vYq9r4G0kXoHkwGGab3exeZT', '2018-12-29 05:25:20', '2018-12-29 05:25:20'),
(52, 1, 'MFlZxxMqDMffgnkdNolYZtoQxdvZaZ8p', '2018-12-30 06:43:29', '2018-12-30 06:43:29'),
(53, 1, '5N72e8yvsiVJyL3uVslpoqZOCe251t72', '2018-12-30 19:33:01', '2018-12-30 19:33:01'),
(54, 1, 'CC4szIIhE6mX8eU1nWE2ppY5m4SStfp1', '2019-01-02 12:39:33', '2019-01-02 12:39:33'),
(55, 1, 'DkROUFP8e8fU16HBLhx9H7EJLwS78rto', '2019-01-03 08:18:00', '2019-01-03 08:18:00'),
(56, 1, 'EIvlEHiR864fJxzRmkMINqSdw2C1UMYs', '2019-01-03 16:58:58', '2019-01-03 16:58:58'),
(57, 1, '7SvUY0XfLEA3zwZCrc260zk8hMgDN8wK', '2019-01-04 08:44:08', '2019-01-04 08:44:08'),
(58, 1, '4NJzaNYrKcXWBDa59PyNDkeCAdBuO50D', '2019-01-05 00:21:22', '2019-01-05 00:21:22'),
(59, 1, 'cxcdSPezICOMd10apRe88cxiyv3izx49', '2019-01-05 05:36:20', '2019-01-05 05:36:20'),
(60, 1, 'GAYrx4DijXTmSzfKc5O3N9egxJ9OsJEo', '2019-01-05 16:01:12', '2019-01-05 16:01:12'),
(61, 1, 'BUHvaQ5Cew15AqG9LGi2Djk3UVjl2ODr', '2019-01-06 13:26:45', '2019-01-06 13:26:45'),
(62, 1, 'hAnM7ZbuCdUyUC1TRlkTS2y2lD2Dn5Q8', '2019-01-07 09:04:37', '2019-01-07 09:04:37'),
(63, 1, 'Kkdj3TOR7OCWScygOEEmvKAW9nYuLbyo', '2019-01-07 18:13:05', '2019-01-07 18:13:05'),
(64, 1, 'JZO6U7pcEzfsFSRLEfmZGmUJadGGEqva', '2019-01-07 23:18:11', '2019-01-07 23:18:11'),
(65, 1, 'O9schxxVFJ1qqVnDm98AyunHtda2oryT', '2019-01-08 06:25:57', '2019-01-08 06:25:57'),
(66, 1, 'WVlT8AMH4yBQRRijZS3mU4AZZz8TyDo2', '2019-01-08 09:58:37', '2019-01-08 09:58:37'),
(67, 1, 'jTanAVIUGD1Qfy0th8UQYs9Vv0WqrNVs', '2019-01-08 14:46:22', '2019-01-08 14:46:22'),
(68, 1, '43fE5s9iLOyLWyEZeb4m1gtiaJZJQcBb', '2019-01-09 06:42:59', '2019-01-09 06:42:59'),
(69, 1, 'Fd8t79FRFaC5VyFFEnVjKXygrdm16mRe', '2019-01-09 10:56:02', '2019-01-09 10:56:02'),
(70, 1, 'Z1ALYzTk7sCkZSFweUZb89cnwJErlpcT', '2019-01-09 20:15:24', '2019-01-09 20:15:24'),
(71, 1, '17GEQBHVVsVuQ9L5GkSBXx8AuHL9BIDm', '2019-01-10 05:24:36', '2019-01-10 05:24:36'),
(72, 1, 'jffJWzPOvB9jUl5Lq70qltA4QsJVr6yc', '2019-01-16 06:13:39', '2019-01-16 06:13:39'),
(73, 1, 'qDSuwm6afya7FVXsjBrIIZJT8yuauD4m', '2019-01-16 21:52:16', '2019-01-16 21:52:16'),
(74, 1, 'FlVNOs0LhSoZT48N31JdKk5qqDeivn5Y', '2019-01-17 19:57:26', '2019-01-17 19:57:26'),
(75, 1, 'peImS7F7ky8soyOR0tEIiw4xou4csbZe', '2019-01-18 07:54:29', '2019-01-18 07:54:29'),
(76, 1, 'Hw5tcdhaJJWerCO788kRnsh3euQ2mZ4g', '2019-01-20 13:45:43', '2019-01-20 13:45:43'),
(77, 1, 'this72iwU8pphuqNYHLERKsFmOFcr6gH', '2019-01-25 21:13:31', '2019-01-25 21:13:31'),
(78, 1, 'jmViDjCKUMkqGScgYYT9LCqiVNr393qA', '2019-01-28 04:52:00', '2019-01-28 04:52:00'),
(79, 1, 'JOvVaxX9KGZ6r4RM4eOpo8VJjeT4H3ED', '2019-01-29 01:49:55', '2019-01-29 01:49:55'),
(80, 1, 'fo1RVBfp7wM1mzq88Hv9T8iZQHGt6zqq', '2019-01-29 07:25:53', '2019-01-29 07:25:53'),
(81, 1, 'Ehr6CB5rx9uHnqm2UphbTq1eKGVVJcIJ', '2019-01-29 14:27:41', '2019-01-29 14:27:41'),
(82, 1, 'BcBkKndcJToi7Tg97NyPrhvjjhaFh4v5', '2019-01-29 19:57:49', '2019-01-29 19:57:49'),
(83, 1, 'jOp9rFFSal9hMUU1AFuAamahMiB4JMq5', '2019-02-01 17:40:16', '2019-02-01 17:40:16'),
(84, 1, 's01YYsUrFuDaxgPWZ8UDhibMD1FAGEDT', '2019-02-02 02:00:44', '2019-02-02 02:00:44'),
(85, 1, 'LyvKaQsTNpGqaSvy5zNXMud4eJ2tmDnb', '2019-02-04 17:14:36', '2019-02-04 17:14:36'),
(87, 1, 'OTIo56lRj4KwymFDfsGdlPd38QzFyWcs', '2019-02-05 06:42:13', '2019-02-05 06:42:13'),
(90, 1, 'mL74YX67xb1TbbZd3A47ybvhiXFtcigu', '2019-02-05 20:58:30', '2019-02-05 20:58:30'),
(92, 1, 'MnOurDW2kElBCJqRIAP0c0pYHKftyebH', '2019-02-06 17:33:34', '2019-02-06 17:33:34'),
(93, 1, 'EJKBXhAQ1sOCnhnSBdFYJnCfGl9VC65Y', '2019-02-06 22:28:19', '2019-02-06 22:28:19'),
(94, 1, 'K4iBxcs3UuTELRoYVtaDE97zSsSFgjec', '2019-02-07 16:59:05', '2019-02-07 16:59:05'),
(96, 1, 'AzYpVTQGUPpKw9hqwtRecC8olqRkN41O', '2019-02-07 17:54:25', '2019-02-07 17:54:25'),
(98, 1, 'Wq5bK6Ef2yjL7lyJj7C9DqUgj98tTGnU', '2019-02-08 16:39:36', '2019-02-08 16:39:36'),
(99, 1, 'uY1TzxaRBNIj6tfEutvKahUvliAOmYvy', '2019-02-08 21:07:54', '2019-02-08 21:07:54'),
(102, 1, 'dcn3fT8xH3kwAmdYPAaRuMWFHXdqxGz7', '2019-02-09 18:48:47', '2019-02-09 18:48:47'),
(103, 1, 'duQfu7eX3Vasbji2ugB6FRQaojwDK21L', '2019-02-09 21:12:50', '2019-02-09 21:12:50'),
(105, 1, 'PZEjj36OY5OQp4XB6bqFLQrncV9dUoG3', '2019-02-09 21:58:33', '2019-02-09 21:58:33'),
(106, 1, 'CminPbAhQ1N3pqNMQXMIR07Y7DJtKx1o', '2019-02-10 16:24:09', '2019-02-10 16:24:09'),
(110, 1, 'yDNvhZy5dNi361GXnJOJl2C7tw8cBHiV', '2019-02-11 14:52:58', '2019-02-11 14:52:58'),
(111, 1, 'OE5wSOIM9XEivYQLuIQPEdgKJGuyvuhG', '2019-02-11 17:17:38', '2019-02-11 17:17:38'),
(112, 1, 'u5bX3NZef9I3sjBs5WMo7F4YDLnmAKVp', '2019-02-12 06:48:44', '2019-02-12 06:48:44'),
(113, 1, 'X25Y33tYuyxijhPP8MNxSQHARKHbYXst', '2019-02-12 15:54:45', '2019-02-12 15:54:45'),
(114, 1, 'HJFGS8B4puqYxRIWujLkIopGm2AEcQL9', '2019-02-12 16:39:51', '2019-02-12 16:39:51'),
(115, 1, 'ihob3O565UkEicMTRlWDxGJTraNUFSph', '2019-02-13 15:38:53', '2019-02-13 15:38:53'),
(116, 1, '6HvZE1IoSjJTiEvugpcc0H66Mntv0rMd', '2019-02-13 18:20:39', '2019-02-13 18:20:39'),
(117, 1, 'ZlDsrQ43J58bDz3pVsIFYdp7pknbNW8W', '2019-02-13 21:42:55', '2019-02-13 21:42:55'),
(118, 1, 'JUMt2C4rPa22cEYARSZgnpEdSFuTHF49', '2019-02-14 15:10:58', '2019-02-14 15:10:58'),
(119, 1, 'aK9OxwZb0pgZZEJ0qiejZF6cM6h82nqe', '2019-02-14 17:50:20', '2019-02-14 17:50:20'),
(120, 1, 'qlQx0TcR5aGTtYeOhZTeetkEWcawqkb0', '2019-02-14 18:10:16', '2019-02-14 18:10:16'),
(121, 1, '4j3aQnUy94ElrobcfePovzrOlkfLvW9v', '2019-02-14 23:01:24', '2019-02-14 23:01:24'),
(122, 1, 'IFmWNqVv6P89o5sl1K2Qm0H2q1HXicru', '2019-02-19 15:06:51', '2019-02-19 15:06:51'),
(123, 1, 'N2V2lvemuervjbHrg6oqCE8tOyLPd4mo', '2019-02-19 19:00:56', '2019-02-19 19:00:56'),
(124, 1, 'UK6t9ZLTWt766SzxbP5Czz3F72nSXLIM', '2019-02-20 14:42:31', '2019-02-20 14:42:31'),
(125, 1, 'ih025iboV5ommXbSSyUV08une6wpoqb5', '2019-02-20 21:58:29', '2019-02-20 21:58:29'),
(127, 1, 'kFLDcHueSUfFUe5vCiuKv8C3ADB7JxxU', '2019-02-20 22:02:51', '2019-02-20 22:02:51'),
(128, 1, 'NvgPYghKdt23bdBpi5r2eIYiLDSaqlXC', '2019-02-21 17:08:57', '2019-02-21 17:08:57'),
(129, 1, 'teBa1CySkB8qcvKQm53pKdnb9NHxMZt0', '2019-02-21 18:12:49', '2019-02-21 18:12:49'),
(131, 1, '15w7eR1XiBv7JVvGpffVPDXmSyZEqVTJ', '2019-02-21 22:45:25', '2019-02-21 22:45:25'),
(132, 1, 'Hp5PDdRb4a0gWdyPnnbmA4ObFzUZJVrp', '2019-02-22 17:03:50', '2019-02-22 17:03:50'),
(133, 1, 'YJjx7gJbvrtKYXQsTqjcFrpAN7Cy7XoQ', '2019-02-24 16:17:32', '2019-02-24 16:17:32'),
(139, 1, 'lWU68oWIDZRaMF275WipBOTSeM6qypRs', '2019-02-24 20:54:50', '2019-02-24 20:54:50'),
(140, 1, 'fp3iVyzPBmRgzYjuBWtNEqYI0qtjp16Z', '2019-02-25 14:19:48', '2019-02-25 14:19:48'),
(143, 1, 'JudXXQgTdtDm93PPja5L1iuV2MCdbWxV', '2019-02-25 15:30:51', '2019-02-25 15:30:51'),
(146, 1, 'HY0QbK17a0e98q0qLPkh4LxxqCfUS1CZ', '2019-02-25 16:18:41', '2019-02-25 16:18:41'),
(147, 1, 'pINB1sBAn7mnNmAIImHAVmvHBUhicket', '2019-02-25 19:24:39', '2019-02-25 19:24:39'),
(148, 1, 'xxgKwLE3iGXKFPeQGq8soBuUcd900jhG', '2019-02-25 20:42:48', '2019-02-25 20:42:48'),
(150, 1, 'yy2vZvriVFqUTlriRKivPsCq3P1xWElm', '2019-02-25 23:02:45', '2019-02-25 23:02:45'),
(152, 1, 'p8XVHbfdJgBznK1cgkjnLeqB1wArOlZ7', '2019-02-26 20:35:16', '2019-02-26 20:35:16'),
(153, 1, 'B5yFn9dkejgGq8k1dxDSzAara8OHTFuo', '2019-02-26 22:57:30', '2019-02-26 22:57:30'),
(154, 1, 'gqRJzdY1YzSFY1LQJ9bf1VmHwY09xpSX', '2019-02-27 07:19:12', '2019-02-27 07:19:12'),
(155, 1, 'V5FmY5HLpcBGkKbb9gZmmMsuU5uJsS46', '2019-02-27 15:17:03', '2019-02-27 15:17:03'),
(156, 1, 'arGSzqnAYmPCxHY0iuOnioobnOHvJtlr', '2019-02-27 15:42:36', '2019-02-27 15:42:36'),
(157, 1, 'd5GE6NG9G978ERzezVXt6apNeLozR7cG', '2019-02-27 17:20:43', '2019-02-27 17:20:43'),
(158, 0, 'zwWDtMZx9wGKKU92TKYH7KS49SesMhVD', '2019-03-01 14:58:41', '2019-03-01 14:58:41'),
(159, 1, '3tGkfpgMvn5FnPkLRdy7Ly3gk3yS2Mcc', '2019-03-02 15:43:30', '2019-03-02 15:43:30'),
(160, 1, 'S9d7WOF8aZMcWC93BizhoqiS4Ac3ixAO', '2019-03-06 06:38:34', '2019-03-06 06:38:34'),
(161, 1, 'Tasw5X5FbeY7cIsuq2mujVsUFrptPfTI', '2019-03-06 20:41:36', '2019-03-06 20:41:36'),
(162, 1, 'H4egdHVOR0FgWbewLNbe8MYy0QlMpBR0', '2019-03-07 21:50:57', '2019-03-07 21:50:57'),
(163, 1, 'ynvP4ND9I7OMxmzGAgbEO772WthsuE6z', '2019-03-08 16:50:57', '2019-03-08 16:50:57'),
(164, 1, 'm7ZbUJhsJd82s6cFT7eRANgYLHlwfShH', '2019-03-10 22:34:49', '2019-03-10 22:34:49'),
(165, 1, '5L0Iv7iUWFgjWKQCVJDLQihFhYgoD4vu', '2019-03-11 03:58:50', '2019-03-11 03:58:50'),
(166, 1, 'AS3NhGTlkyFCMLLIjicAEM9UFcES4JLB', '2019-03-11 14:05:57', '2019-03-11 14:05:57'),
(167, 1, 'roPOHZnTY8nEGQ9ADj5Rr0NkX6ZmJ5he', '2019-03-11 18:21:00', '2019-03-11 18:21:00'),
(168, 1, 'Fbyn2wZpCk1Uoztzz9MT23rQG1fC0EtK', '2019-03-11 23:48:36', '2019-03-11 23:48:36'),
(169, 1, 'myiqo8iPyLtzU4BRgJBgTOOsvokCLXTG', '2019-03-12 02:26:46', '2019-03-12 02:26:46'),
(170, 1, '3FcKLFznIyG2jwkGwWKFQ7FRCKV0BqnX', '2019-03-12 16:26:44', '2019-03-12 16:26:44'),
(171, 1, 'V79B7NNFklKFnd8Zdics5HJKpHdrx1DL', '2019-03-12 16:40:35', '2019-03-12 16:40:35'),
(173, 1, 'oDWwel3M5PWkm6SWg5V3FSvjphUFXtsN', '2019-03-12 16:43:16', '2019-03-12 16:43:16'),
(175, 1, '0rym4eWPu9qXYdw3rJuGG2Pi9Gpy0AFJ', '2019-03-12 16:45:28', '2019-03-12 16:45:28'),
(188, 1, 'gmuQItWaFrahdbr94tRjI4Q9egtLjDLK', '2019-03-12 17:38:01', '2019-03-12 17:38:01'),
(189, 1, 'dWyFYGmtD6WbOwCOGQKiLOv1wQcCXw6h', '2019-03-12 17:38:36', '2019-03-12 17:38:36'),
(195, 1, 'kj9FfIt4FYPaOm4vourjHpkd9YxAzpAZ', '2019-03-12 21:43:44', '2019-03-12 21:43:44'),
(197, 1, 'TUeu7fVodNkbZFhIY74hTNoaJFa6GssE', '2019-03-12 22:11:46', '2019-03-12 22:11:46'),
(199, 1, 'zTOVfJJK1XfgKcbPo5rQlL4DNpSCIHXw', '2019-03-13 13:06:18', '2019-03-13 13:06:18'),
(207, 1, '08GIfhB9al5n6j5qTxXgzb9Fypacbute', '2019-03-13 19:47:28', '2019-03-13 19:47:28'),
(210, 1, '95fs0ikhpaioNpYhCOaBFop8HkOKXMeQ', '2019-03-13 23:38:49', '2019-03-13 23:38:49'),
(216, 1, 'mjNIHOES39d6qASYnc1Ll1Rp0i6wgTsf', '2019-03-14 15:58:19', '2019-03-14 15:58:19'),
(217, 1, 'ffAZJcmtwT4UvvdDfjFXdRo7zQBqXf7l', '2019-03-14 16:03:52', '2019-03-14 16:03:52'),
(220, 1, 'NPKqgJF80Ma4p9vcEOr631NjN96oZjfQ', '2019-03-14 18:45:18', '2019-03-14 18:45:18'),
(232, 1, 'k8UrNIznw7b2zTV0bcswPAl6e7kkX4QG', '2019-03-15 16:09:10', '2019-03-15 16:09:10'),
(235, 1, 'WlJQtGL3LGDm9IqyfCy56t67Wo0zlHCF', '2019-03-15 16:54:28', '2019-03-15 16:54:28'),
(245, 1, 'uqSRNIAiim9xQLJhRPOGBRi3K4bBViZk', '2019-03-16 21:43:54', '2019-03-16 21:43:54'),
(253, 1, '3r6umMJlbOfD5b0DTH1pdHCIMpyu2kAI', '2019-03-23 07:10:31', '2019-03-23 07:10:31'),
(257, 1, 'zatBsrU6rXAznD36mQ6W0iaytTiAVSyz', '2019-04-08 16:24:01', '2019-04-08 16:24:01'),
(258, 1, '18JsBD0fQsUTv4RVKYW6ZYx9P9BznudP', '2019-04-09 14:42:06', '2019-04-09 14:42:06'),
(259, 1, '0er3kV8t0NCuTsJpsNFElFeURgMKUBot', '2019-04-09 14:56:56', '2019-04-09 14:56:56'),
(260, 1, 'wBTlbM1yVa2Ye9Kf7mjBsuqGKdItXJoW', '2019-04-09 19:40:15', '2019-04-09 19:40:15'),
(261, 1, 'E2TuZctk9sUFE3XE3hpQjnlEKDL0t9Lr', '2019-04-09 19:48:55', '2019-04-09 19:48:55'),
(264, 1, 'SfGEzCFFulxKEFCK883twhRI7sQNIlw2', '2019-04-10 16:35:22', '2019-04-10 16:35:22'),
(267, 1, '5ymX2aIahqUTCoilPFHTcKb3m5LYZlc0', '2019-04-10 22:40:09', '2019-04-10 22:40:09'),
(268, 1, 'cQtLriHufkDIJ2AcRxtnRWY8h4wCz3vC', '2019-04-11 15:18:21', '2019-04-11 15:18:21'),
(269, 1, 'ad8YgetSi5xR4cR78U8IpukZh8s9qJ7h', '2019-04-11 17:43:21', '2019-04-11 17:43:21'),
(270, 1, 'tKgqoUqpzwtGTwwWGMS6ak4coxezXqxn', '2019-04-11 17:53:07', '2019-04-11 17:53:07'),
(271, 1, 'BY7BNxky8Z8sEUkHl0kLVOVSJYnGU8uo', '2019-04-11 18:08:07', '2019-04-11 18:08:07'),
(272, 1, 'a3bLETU9DXzNrMtt57vkdVqAagnUi1ND', '2019-04-11 21:03:06', '2019-04-11 21:03:06'),
(273, 1, 'hcw7CUfryP5afYNjfRtPL8mvzIhtKc5Z', '2019-04-11 23:07:22', '2019-04-11 23:07:22'),
(275, 1, 'huf1D1bG19hHHZUcXstFoj6cY0FW3QzF', '2019-04-12 03:24:19', '2019-04-12 03:24:19'),
(276, 1, 'ifUUdf5znN4sR7AGxfXO88DT2YmbYTYM', '2019-04-12 13:59:38', '2019-04-12 13:59:38'),
(277, 1, '7hLsAJoq03pekkMAiiJOW6Brn1CWfDHR', '2019-04-12 15:58:35', '2019-04-12 15:58:35'),
(278, 1, 'MNQB7YlB4rYIqN7KUtg973Xzu3DU9mXm', '2019-04-12 17:55:29', '2019-04-12 17:55:29'),
(279, 1, 'u18NxCZHHdoqjDBfIT9ZZ0q6qfqCUHRJ', '2019-04-12 21:25:28', '2019-04-12 21:25:28'),
(280, 1, 'zZokXeZTv6Tw9smg4ShmUZo4YC898XVT', '2019-04-12 22:33:56', '2019-04-12 22:33:56'),
(282, 1, 'fcXjsdHTann5VUTJSMS3P0yzYOIR7mTK', '2019-04-14 11:40:23', '2019-04-14 11:40:23'),
(283, 1, 'EUhVxd416Qu5JYGaasEL4g0VcvYAS2ur', '2019-04-14 18:41:15', '2019-04-14 18:41:15'),
(284, 1, 'gRlKDuA8lmE6GX5LQuoPQ71s0nbKMWj4', '2019-04-14 22:24:56', '2019-04-14 22:24:56'),
(285, 1, 'uoPd20h3vWmF4ruiU35dr4JS4SsC9qQz', '2019-04-14 22:28:47', '2019-04-14 22:28:47'),
(287, 1, '8EX4kyuptOP8LpUU7ecqfnrjXMhB9ZKk', '2019-04-14 22:37:43', '2019-04-14 22:37:43'),
(288, 1, 'puIPkG9XdQPi7GB4LIny4hSL4vSDGLgd', '2019-04-26 09:55:39', '2019-04-26 09:55:39'),
(289, 1, 'sU2ih691JG2ZSQO1RmavQHJOOQa2DBrB', '2019-04-26 15:12:00', '2019-04-26 15:12:00'),
(290, 1, 'tMSYa44oBX78VZ4AuyXJqr2eRZG0iag0', '2019-04-26 21:54:21', '2019-04-26 21:54:21'),
(291, 1, '7hxzRAm4wJEXKmY6Xnhwj2FK5OrYodBs', '2019-04-27 17:07:30', '2019-04-27 17:07:30'),
(292, 1, 's10c5mb49a9DdYjrZYtsNMPfSfCFuLCR', '2019-04-27 17:11:26', '2019-04-27 17:11:26'),
(294, 1, 'ypRVhqJV4Hh1LFeU4sqYQra0b5K91RUi', '2019-04-27 18:52:02', '2019-04-27 18:52:02'),
(302, 1, 'cVKqMWg8zZAMULtmXeuqu80pOyRrlDvZ', '2019-04-27 20:26:05', '2019-04-27 20:26:05'),
(303, 1, 'qxIbNJabMZNBvNag1YMfib3465TP6SCr', '2019-04-27 21:04:30', '2019-04-27 21:04:30'),
(306, 1, 'EvcOlkrKBfkieO9JR99mEOQetONypG4P', '2019-04-27 23:37:05', '2019-04-27 23:37:05'),
(307, 1, 'GzFQZ7ceYN7vTOTlbcHtvbYCsmUeHJm0', '2019-04-28 12:33:44', '2019-04-28 12:33:44'),
(319, 1, 'zyKHadxHQkxk8bEerzdL1czw1V3gr7dM', '2019-04-28 21:24:22', '2019-04-28 21:24:22'),
(320, 1, 'I4hM4hhfeuLZgfhj66fryIz4sDaziNsK', '2019-04-29 00:31:30', '2019-04-29 00:31:30'),
(329, 1, 'CBVHGytnbPPh8Ce24NIAIzY5UZqLg6Ch', '2019-04-29 16:08:06', '2019-04-29 16:08:06'),
(331, 1, 'BdNgj0prahIQBV1ZWtcIUSoB7UaqohjH', '2019-04-29 19:12:56', '2019-04-29 19:12:56'),
(333, 1, 'epDZb39OU25ZF5HPR8qb6Frwjbe6Af2n', '2019-04-29 22:35:01', '2019-04-29 22:35:01'),
(334, 1, 'AuJTQ5DJPmI5Up093JR2kUmto2KJJ0Yo', '2019-04-30 11:59:00', '2019-04-30 11:59:00'),
(338, 1, 'zfn1szC80gWvnF16SOVkhXpFMz6M5i5d', '2019-04-30 23:18:21', '2019-04-30 23:18:21'),
(345, 1, 'f6fa12gEb1lLmJNvUUjefnrUm6mykfNn', '2019-05-16 18:05:41', '2019-05-16 18:05:41'),
(346, 1, '3bMXIzZfa4tFZ6ILiFny0bLK0vciEr3o', '2019-05-16 21:53:25', '2019-05-16 21:53:25'),
(348, 1, '3fCrItqtk8GVJFAK1xsm8D1S2A0yRemC', '2019-05-17 15:20:34', '2019-05-17 15:20:34'),
(352, 1, 'gxq3s4tylbyhEZoNbjugXrHuOzdv12pA', '2019-05-18 16:47:29', '2019-05-18 16:47:29'),
(357, 1, 'J2y670rSxdNE895pCdEyVd8dw81wzvWI', '2019-05-24 19:43:08', '2019-05-24 19:43:08'),
(359, 1, '39aiTXuN0JJGGQTrhie9KLg6EB1BlgVY', '2019-05-24 19:49:20', '2019-05-24 19:49:20'),
(360, 1, 'ODeucpSRmOqhlLvvIbMci87SIfAaEV67', '2019-05-25 17:28:46', '2019-05-25 17:28:46'),
(363, 1, 'fvSIKZWwme05EQvbjpJKzhGJyt4ZRRTA', '2019-05-26 00:32:21', '2019-05-26 00:32:21'),
(367, 1, 'Z6CkGkVAkuxv3GKN7lnnFn6KfpCYDmQg', '2019-05-26 20:25:18', '2019-05-26 20:25:18'),
(420, 1, 'WmTlupmxFp8Rp3TWcY8h0y7oXvid97ek', '2019-06-28 07:01:01', '2019-06-28 07:01:01'),
(422, 1, 'CktqTQ9lz8PJBvkdZ86Kr0l6mHw7FHvV', '2019-06-28 18:45:52', '2019-06-28 18:45:52'),
(427, 1, 'PESOavXeuLGnglPYgM959TqQwVbFWm7a', '2019-07-04 14:53:29', '2019-07-04 14:53:29'),
(440, 1, 'djeenRWbp3hkE5CzYLjpJGECYIqRoxOb', '2019-07-11 19:30:26', '2019-07-11 19:30:26'),
(495, 1, 'v3TS9AIrYmvcGOgtWakMCsPy385HHloY', '2019-08-27 15:34:43', '2019-08-27 15:34:43'),
(497, 1, 'AbdhDViXL3A0hq80AXfX5nicO4Utm1vI', '2019-08-30 06:52:49', '2019-08-30 06:52:49'),
(498, 1, 'SdrL2pXG77xu5MZN827z3wjOOHZdfaTC', '2019-08-31 15:07:08', '2019-08-31 15:07:08'),
(518, 1, '5fU5jV9guDY0yNpCA0tZKhc0YV7SFRTB', '2019-09-17 14:48:51', '2019-09-17 14:48:51'),
(520, 1, 'E89EUF4GU8pBchdPN08z8giHFRu2I2CM', '2019-09-17 15:07:35', '2019-09-17 15:07:35'),
(526, 1, 'FYM6bZygFhlfSSihNkHw9oKzb5eVui0f', '2019-09-18 22:56:35', '2019-09-18 22:56:35'),
(533, 1, '8DT2aGoHy7CeGTxFBTUG5GDNB6WyR3t8', '2019-09-18 23:42:15', '2019-09-18 23:42:15'),
(535, 1, 'zcOBK4UbwMGW3wJj5wVmf1j9VZybw4SV', '2019-09-18 23:52:27', '2019-09-18 23:52:27'),
(541, 1, 'CGi3VH0ZPhX0YaM9uRWPMeEMTZYYePBs', '2019-09-19 19:35:51', '2019-09-19 19:35:51'),
(542, 1, 'dxZhCe18qbPZf3yL0RUxdxfXYJcRU8tN', '2019-09-19 23:05:18', '2019-09-19 23:05:18'),
(552, 1, 'JoKq67du6BYRoxA7XGit4xpwHc8rYH7X', '2019-09-25 04:00:07', '2019-09-25 04:00:07'),
(554, 1, 'TneOnGtlzGh67zxVTZL2G5kYUVoH1qC3', '2019-10-02 05:43:33', '2019-10-02 05:43:33'),
(561, 1, 'vooSFCvFgX2lwSNgVWiXogrfln4ayngk', '2019-10-10 00:19:58', '2019-10-10 00:19:58'),
(567, 1, '5uIiKeifDmfcBa32qxcAntWhxXaAuX2a', '2019-10-22 21:58:04', '2019-10-22 21:58:04'),
(569, 1, 'TWR3wURQ0HN4STMK7Zt7RokcLdCGYW8v', '2019-10-25 16:47:21', '2019-10-25 16:47:21'),
(572, 1, 'JWO8I7orlSyqsIZYlRCx1yub2BVsSV0S', '2019-10-26 19:43:29', '2019-10-26 19:43:29'),
(575, 1, '3rfrS17B8nvMdcmjsbCXlPvlwl1jr0yb', '2019-10-26 21:18:15', '2019-10-26 21:18:15'),
(576, 1, 'wlS2wcZoVYGqLvB8iajU914hv9SFWcyg', '2019-10-27 05:09:37', '2019-10-27 05:09:37'),
(579, 1, '2IAHL1pgG04t810G9hVxzGDc3bh6l4Qn', '2019-10-28 16:46:10', '2019-10-28 16:46:10'),
(582, 1, 'IMG8VCayJE54I3PjzVHoDb4uNaRg9swm', '2019-10-28 21:42:20', '2019-10-28 21:42:20'),
(584, 1, 'Vgv9dknxNSbJdQm3aPHninIKrqAklZ0a', '2019-10-30 17:15:08', '2019-10-30 17:15:08'),
(585, 1, 'Uo7goNIlXtvbaoBMj7q5LWqx8Amj2VOy', '2019-10-31 15:31:37', '2019-10-31 15:31:37'),
(586, 1, 'ePNL7ROuOFr6ITNrIDHverjWqkKp8yh4', '2019-12-06 19:20:49', '2019-12-06 19:20:49'),
(587, 1, '9MW4nnwA13rznbsz59ajnGjhk1LqL45G', '2019-12-09 20:57:45', '2019-12-09 20:57:45'),
(588, 1, '1SWBFOM9KBTmucF9X0DVwRQ0IXePXvR4', '2019-12-19 13:14:50', '2019-12-19 13:14:50'),
(589, 1, 'dxsl4lOyTR1lHgjHkjfY2zEPGY3MuPh5', '2020-01-15 04:23:43', '2020-01-15 04:23:43'),
(590, 1, 'Yu029G4sS5AvzqYXnBLbowj4LhRqCWHM', '2020-01-20 23:10:26', '2020-01-20 23:10:26'),
(591, 1, 'VUxpKlB8BSFFI5ku1odFTX2Wr4I97DRJ', '2020-01-29 19:51:14', '2020-01-29 19:51:14'),
(592, 1, 'GGAI3wsnziZ3kNGdHviJZ9Bmit6jBccR', '2020-02-03 20:38:52', '2020-02-03 20:38:52'),
(593, 1, 'VcRgi6DDJCI1bTQ7JcGyLPumQm3vrg0L', '2020-03-04 18:29:54', '2020-03-04 18:29:54'),
(594, 1, 'aQXYBEapKPuWrEOrFa1IdrL7A5FvuEYE', '2020-03-06 16:23:04', '2020-03-06 16:23:04'),
(595, 1, 'F0Mxt0cANCLWggaIb2bFJDzEWvWXviwg', '2020-03-09 05:26:29', '2020-03-09 05:26:29'),
(596, 1, 'zWwzjTWsGwE3z7l3ld70YgMbLLnmQyR1', '2020-03-17 02:08:55', '2020-03-17 02:08:55'),
(597, 1, '10gnaDNIZ93xhVETnrtt0nfNK7xy5BV8', '2020-03-27 19:59:52', '2020-03-27 19:59:52'),
(598, 1, 'Mag6beBf5foQhXa3EvCT0RaZyNlryswg', '2020-03-27 20:34:35', '2020-03-27 20:34:35'),
(599, 1, 'EFNDKs9sI5Viykz7MbJAMRSCx6YFpnDP', '2020-03-28 06:04:29', '2020-03-28 06:04:29'),
(600, 1, 'CgPIKIJtKfZw5L3B67Oxu4BupjOayDal', '2020-03-29 17:12:25', '2020-03-29 17:12:25'),
(601, 1, 'Fblm2je0AiH9GfM8omPdphoLj3qi8lvX', '2020-03-29 22:32:12', '2020-03-29 22:32:12'),
(602, 1, 'fyN1f3WXKkBvSMIBNOiBACkgsL5uQAAZ', '2020-03-30 19:22:56', '2020-03-30 19:22:56'),
(603, 1, 'OzvTBkMulx8IE17VXdQfVPT6MDzB3vhv', '2020-03-30 20:49:05', '2020-03-30 20:49:05'),
(604, 1, 'aetlcI3Q3FHgUZUAoX5qDwhoM0bkHxg7', '2020-04-01 23:46:15', '2020-04-01 23:46:15'),
(605, 1, 'u3OxpFgrz2EPBezSxue2pn1k1DqsBON6', '2020-04-02 16:58:24', '2020-04-02 16:58:24'),
(606, 1, 'WAJzWAnD65cBSpWcl9DtE7qWRg1t8vOu', '2020-04-07 16:59:23', '2020-04-07 16:59:23'),
(607, 1, 'mKqF7gvTi88wtylIqFfVMH5fE6qA4Xwq', '2020-04-07 17:12:49', '2020-04-07 17:12:49'),
(608, 1, '3J7HhB84m6ziKskWJSGjZqvxzCMfALjx', '2020-04-07 17:36:00', '2020-04-07 17:36:00'),
(609, 1, 'ohdsVsngtltIpBsMLTcAtRykYxBLNveh', '2020-04-07 20:26:56', '2020-04-07 20:26:56'),
(610, 1, 'lrMkwqQEmQ1C4iRZddsycgNH0L4Of3Vd', '2020-04-08 00:52:49', '2020-04-08 00:52:49'),
(611, 1, '955zXhCvpDP1M4x9t2rfCEZrmUDJPQsL', '2020-04-08 18:57:37', '2020-04-08 18:57:37'),
(612, 1, 'k5QYgK8ox6eSzCJzQ4AiJ6sQVGe9cUd9', '2020-04-08 22:55:27', '2020-04-08 22:55:27'),
(613, 1, 'DbIRLJUzVCduAhetMo1Q3uKWhJrZQCBA', '2020-04-09 10:24:40', '2020-04-09 10:24:40'),
(614, 1, '0snpegxsrbaJ2rKls1SdC4KE7L6ER0qe', '2020-04-12 07:15:48', '2020-04-12 07:15:48'),
(615, 1, 'jFxQMrA5vHSWik3UY14L75OuRS7Mpw3B', '2020-04-15 22:28:55', '2020-04-15 22:28:55'),
(616, 1, 'ukNzs7ytW7AAVCYr7pNMSvFHSrv4NGIr', '2020-04-19 14:13:33', '2020-04-19 14:13:33'),
(617, 1, 'uod9BwPs5eAy6UTw0Py0MYe5K5njrtKu', '2020-04-19 14:39:36', '2020-04-19 14:39:36'),
(618, 1, 'CydLif8RlLkOW3sEHW9drmk9zDHyWbjr', '2020-04-19 14:44:04', '2020-04-19 14:44:04'),
(619, 1, 'XaQG2XOi73cvl4MpMg06FVxUYj8y5QlF', '2020-04-19 17:31:38', '2020-04-19 17:31:38'),
(620, 1, 'LQ4LEKSvtiguXarTAyZmBwpfPa5zRzWZ', '2020-04-19 17:50:35', '2020-04-19 17:50:35'),
(621, 1, 'ZnkphvDAjrokAF4pxtP5gjcZVADD07TF', '2020-04-19 19:35:29', '2020-04-19 19:35:29'),
(622, 1, 'nwOM8xrGdE4HXLaIvO7MtCEx4Bu3OcEx', '2020-04-20 05:45:39', '2020-04-20 05:45:39'),
(623, 1, 'bTGLrV8FZQimlbMOK1w6lDkHZqCeMsJW', '2020-04-20 19:52:40', '2020-04-20 19:52:40'),
(624, 1, 'KwMJusxKg66RaaS58oE80Ng9AeQeoDnR', '2020-04-21 18:44:44', '2020-04-21 18:44:44'),
(626, 1, 'sGBuPL0XWp37nHncqoAoeorcz6JNoYuQ', '2020-04-21 22:59:17', '2020-04-21 22:59:17'),
(628, 1, 'KNdmuXYfzPPgRHmTF6bxJA0vGoDP9ISX', '2020-04-22 17:42:38', '2020-04-22 17:42:38'),
(630, 1, 'uinJvY0vKOgGvt178ATixfPTbYTW69c7', '2020-04-22 23:42:52', '2020-04-22 23:42:52'),
(634, 1, 'gxpN88m4csZiTIjSenTygWDWs4fVAbmq', '2020-04-23 17:48:26', '2020-04-23 17:48:26'),
(635, 1, 'RySPJT4diVLuzmof7K9iqbY0cNwkt20W', '2020-04-23 21:52:24', '2020-04-23 21:52:24'),
(648, 1, 'xIXMpuFbdRcaPTGVeIubojToZuzTiV6p', '2020-04-28 21:15:45', '2020-04-28 21:15:45'),
(652, 1, 'KcOw40VbB6R11BdNwuiAuQ6lCc49jvY8', '2020-04-29 07:09:04', '2020-04-29 07:09:04'),
(655, 1, 'EUigl2HO1zmsG2tDvHgD7JiNSUOCf4nY', '2020-04-30 04:06:21', '2020-04-30 04:06:21'),
(660, 1, 'paT8Wg2uvHrNr7itzgQPZHzn9AN8I4Wr', '2020-05-01 18:02:09', '2020-05-01 18:02:09'),
(664, 1, 'Uy17cdnRY5FHiyNeWuUXtvNOYctFkGTh', '2020-05-04 20:41:12', '2020-05-04 20:41:12'),
(676, 1, 'JOCjYThjIiK2tDlC58vSIoVbFzaT5p59', '2020-05-06 18:19:19', '2020-05-06 18:19:19'),
(679, 1, 'ipYDTIiUtMo4rTptNDWP8gqgnNcTg75w', '2020-05-06 23:25:57', '2020-05-06 23:25:57'),
(683, 1, 'o89LiJbThWHFfz41C9NSYoIZ9wKwyRVf', '2020-05-07 21:01:17', '2020-05-07 21:01:17'),
(684, 1, '7ARZT2AJwbO0xgwkBEF4dvDco0pDsAWV', '2020-05-09 15:19:34', '2020-05-09 15:19:34'),
(685, 1, 'ubHKI4nTxMI9MDA3AcoklTJmhDNGh1cP', '2020-05-21 03:39:50', '2020-05-21 03:39:50'),
(686, 1, 'vtR4lfFc639Fs03n44s94W1QYlizBmfG', '2020-05-26 16:17:39', '2020-05-26 16:17:39'),
(699, 1, 'IPJ6FwQytuQkP6e1l9OA1Ow8zapDC1uY', '2020-05-29 19:50:28', '2020-05-29 19:50:28'),
(713, 1, 'NlX8sEpxrtkD1eDG84vlFH0ABnhppq5e', '2020-06-09 21:57:07', '2020-06-09 21:57:07'),
(721, 1, 'mFJGbMx4p6jl6XXCOfcWflBwEg40ILrF', '2020-06-17 22:26:48', '2020-06-17 22:26:48'),
(722, 1, '7ABGOUM5MpSjlxwMaUfC4WYUN3VsryZj', '2020-06-19 18:01:11', '2020-06-19 18:01:11'),
(743, 1, 'xEcsQdw0aRzdl7iL9Pq2AhCWposPNFsZ', '2020-06-25 08:03:10', '2020-06-25 08:03:10'),
(746, 1, '1FrNMb4rch2z4gj8d74THzmIUDtOMYV4', '2020-06-26 15:23:15', '2020-06-26 15:23:15'),
(754, 1, 'kMAssLhaTx74RtdD2d8laVat2942kCcK', '2020-07-21 00:17:36', '2020-07-21 00:17:36'),
(755, 1, 'agn1adpODNZO9dTn2jmyWT5d2lpHBpe1', '2020-07-27 17:56:13', '2020-07-27 17:56:13'),
(756, 1, 'nX2svxIrKqzChziyTiR1l0m2Aogb6U73', '2020-07-29 01:00:35', '2020-07-29 01:00:35'),
(757, 1, 'e07oWAtPv9oYNwKg13jNA2VtFGgKqt6r', '2020-08-01 06:13:07', '2020-08-01 06:13:07'),
(760, 1, 'HNW2hQTxGCIREZd1B3C2qfyO43SMsJTR', '2020-08-10 05:06:41', '2020-08-10 05:06:41'),
(761, 1, 'zX4Gu7LPUTHxfARctTTjMMi5UQFmL3X5', '2020-08-10 05:09:48', '2020-08-10 05:09:48'),
(763, 1, 'mPULpQL5G2Fot59zF6FuBSsPewr9yYYW', '2020-08-12 18:00:36', '2020-08-12 18:00:36'),
(764, 1, 'znfuXwEG26GNORJxmG235iMxt0UqoOwo', '2020-08-15 06:10:13', '2020-08-15 06:10:13'),
(768, 1, 'upQMrcDWPs9vsWo9hsHyh44VMUmhHqUI', '2020-08-19 23:49:18', '2020-08-19 23:49:18'),
(773, 1, 'X2KdUsegPyRpurwBS8SBdaK8m6tEilce', '2020-08-27 16:56:32', '2020-08-27 16:56:32'),
(777, 1, 'RpIGYbIqI2c8VM1kOW6oAXWSySEvkYd8', '2020-09-07 23:45:48', '2020-09-07 23:45:48'),
(780, 1, '22BPn3DBsVq6P07y8TzcyoG78LzNrnS8', '2020-09-08 17:50:47', '2020-09-08 17:50:47'),
(781, 1, 'B9CcXJhjHK4MbglMWGDdxvs5y85KP81o', '2020-09-08 19:47:33', '2020-09-08 19:47:33'),
(782, 1, 'DQBkva5gp00otLXCkUUEwVMWOx1Z7Ilb', '2020-09-08 21:17:02', '2020-09-08 21:17:02'),
(784, 1, 'k1lwYI1KSQykfHEpnahzgV75oqml6sMF', '2020-09-14 14:42:45', '2020-09-14 14:42:45'),
(785, 1, 'sAkMuHQraXczFjc4aCDKCmQaYHNdGZOW', '2020-09-14 14:50:08', '2020-09-14 14:50:08'),
(786, 1, '4YEkXoARyYkTCPYI5iOy1ibbKEKldeoC', '2020-09-21 18:40:42', '2020-09-21 18:40:42'),
(787, 1, 'uVcd2BazFSEnxhDKt9HYT3iEClDbVd9U', '2020-09-23 14:38:50', '2020-09-23 14:38:50'),
(788, 1, '7QDShE33jOTy6bDeFHhRQFMS3fLCFBmH', '2020-10-06 13:29:52', '2020-10-06 13:29:52'),
(789, 1, 'gBX4HJ6PQWH5pgQOpUvms0eXdeItxQJ4', '2020-10-15 15:56:41', '2020-10-15 15:56:41'),
(790, 1, '5TtquFoSMU1WjbWKG1f8c6FDLkIayxy2', '2020-10-16 12:20:35', '2020-10-16 12:20:35'),
(791, 1, 'uoP5F7EvRtPcC1Kz91JmTMZLhkWyPCa1', '2020-10-21 20:32:15', '2020-10-21 20:32:15'),
(792, 1, 'n3JFzPdrMCbcjuhvtBmQBdpI55qr81gB', '2020-10-23 14:09:20', '2020-10-23 14:09:20'),
(793, 1, 'wbhv9dMNbW6q252rbtS9TshI0tY03nyO', '2022-10-05 13:54:11', '2022-10-05 13:54:11'),
(794, 1, 'z6MkFyhLU2mwzb5o8f822j3DYULn4R4U', '2022-10-06 14:44:10', '2022-10-06 14:44:10'),
(795, 1, '5iRFvU7O6rvJHBBcihrsmdCreQgo2Ylw', '2022-10-07 18:46:45', '2022-10-07 18:46:45'),
(796, 1, '01VTL4go3bJZ7V5Afk3ciYZ961bTJRWn', '2022-10-07 19:21:53', '2022-10-07 19:21:53'),
(797, 1, '3Mih9fcNc1YCnjd2QP6U2G0AYHyfo4S8', '2022-10-07 19:27:39', '2022-10-07 19:27:39'),
(798, 1, 'qa9W6xUmjclHMv69mmkChxATLFKpGEjm', '2022-10-08 11:46:30', '2022-10-08 11:46:30'),
(799, 22, 'DiCOiiVWYdDbLDQCPoELtGBenhqwqouK', '2022-10-08 11:56:45', '2022-10-08 11:56:45'),
(800, 1, 'Tod4NXfZ8pHD7roMgWi2RTtR0JnYHGjP', '2022-10-08 11:57:30', '2022-10-08 11:57:30'),
(801, 22, 'RopuXEh3pMe1ui9GBcLLyMkXEhEr4Pvi', '2022-10-08 12:06:49', '2022-10-08 12:06:49'),
(802, 1, 'jxB7nT7lW20pWbZuH3U7qOUBYtF3OIgU', '2022-10-08 12:07:57', '2022-10-08 12:07:57'),
(803, 22, 'qLvm2XWArQP3pDyfUIDJ8qMs9rW1SWpD', '2022-10-08 12:10:26', '2022-10-08 12:10:26'),
(804, 1, 'JkMN8124vtuFzPhOkpTTZVSh4VAgkIPe', '2022-10-08 12:12:24', '2022-10-08 12:12:24'),
(805, 25, '5uZyPhqUPtFnZrQxH3S16aRoRlfBbN2f', '2022-10-08 15:19:17', '2022-10-08 15:19:17'),
(806, 1, '7vXo1U0r1On37n1Z8TEU9OYtVlOwBh8j', '2022-10-08 18:33:08', '2022-10-08 18:33:08'),
(807, 25, 'Ja3JTQOpSpVSPlLqEgRmbT3JatXGJiAl', '2022-10-08 19:29:36', '2022-10-08 19:29:36'),
(808, 25, 'mRUofhAEFqZFtlwRp3NLXQtzlO16MKGb', '2022-10-08 22:32:21', '2022-10-08 22:32:21'),
(809, 1, 'gGUbzlbkfTLqPhSF9AMkssxPEvtlqZYY', '2022-10-09 15:19:03', '2022-10-09 15:19:03'),
(810, 25, '0Mr4xE3sGwwtCB4OrEZZpKRT1wkmSt00', '2022-10-09 22:02:01', '2022-10-09 22:02:01'),
(811, 25, 'NLKu5evC5CMPjNmfDefSf1Xcqf630VrX', '2022-10-09 23:52:56', '2022-10-09 23:52:56'),
(812, 24, '5Tl7UY3KQ6isYHNF4fuCeIfzPlCyO4zj', '2022-10-10 12:06:33', '2022-10-10 12:06:33'),
(813, 25, 'JVTAymZ1U03vkxVbKNPlZ7skx3rRdyRK', '2022-10-10 14:34:16', '2022-10-10 14:34:16'),
(814, 25, 'dOTuxt07RbO9LT0P5klFCf11jToGOnt1', '2022-10-10 16:36:56', '2022-10-10 16:36:56'),
(815, 26, 'QuKwrjLOFOTEev2dod26sGyiowVg4oFw', '2022-10-10 19:25:35', '2022-10-10 19:25:35'),
(816, 25, 'dmBWJp3WOdlENQutZOZrIE56qjTgA3w5', '2022-10-10 19:56:58', '2022-10-10 19:56:58'),
(817, 26, 'nEOHq3NFfalw1zgfP8O2MEswawEWA3nQ', '2022-10-11 00:58:49', '2022-10-11 00:58:49'),
(818, 25, 'tb3eRv6gqHimRecJIRIrPjFWUMKIpgvX', '2022-10-11 01:21:47', '2022-10-11 01:21:47'),
(819, 25, 'aNJBDj1zIPjNTLDZltIlIRtMivcFcl7P', '2022-10-11 01:21:47', '2022-10-11 01:21:47'),
(820, 25, 'WL16hgk4Qdvnt2HMv0wZHn2zQdltjXZh', '2022-10-11 13:09:03', '2022-10-11 13:09:03'),
(821, 26, 'hksMzLrUdEUbqCJG3ueLSWfrcDsUZUhS', '2022-10-11 14:39:48', '2022-10-11 14:39:48'),
(822, 26, 'QZScgSUyVDl58Dfd6f5rPkDQklWA4M0C', '2022-10-11 19:11:24', '2022-10-11 19:11:24'),
(823, 25, 'nKiErs3wb0FZb981y7i4kurpEE3YsTlg', '2022-10-11 19:44:51', '2022-10-11 19:44:51'),
(824, 1, 'BQLKTugLWJ4qFtLw9N1vzXmMe91BnmpO', '2022-10-11 22:11:19', '2022-10-11 22:11:19'),
(825, 26, 'Ko2OSz0nv2D9b5ud7cEXQRGsRISAPgHQ', '2022-10-11 23:29:32', '2022-10-11 23:29:32'),
(826, 25, 'yHkwBTG9OkryzphBbTLpfmBE83gpy6eV', '2022-10-12 00:47:14', '2022-10-12 00:47:14'),
(827, 1, '5xtVRa4CPP88u4PoGoK2A3XN2Fn6zhUu', '2022-10-12 07:19:08', '2022-10-12 07:19:08'),
(828, 25, 'xocXFBoLtUD53dy1E0tVpfAPAzg7zT5y', '2022-10-12 10:06:57', '2022-10-12 10:06:57'),
(829, 26, 'gVUDovMBOyugYCIcr7xdBgaHKDpLdJgf', '2022-10-12 11:55:36', '2022-10-12 11:55:36'),
(830, 25, 'LOMMuzCZ5jNMXlkfmRETSYhz2HYtLxzo', '2022-10-12 16:50:35', '2022-10-12 16:50:35'),
(831, 24, '9vndzAAfOy3TI7pk1UgAAdrnCUkNhZre', '2022-10-13 13:18:27', '2022-10-13 13:18:27'),
(832, 26, 'MZWjXJ37jEfezdYn9P9CTGSQyYHCYbZF', '2022-10-13 13:40:29', '2022-10-13 13:40:29'),
(833, 25, '1R7C7x73OqsUPuDHFZoXnkaJC1cbVI74', '2022-10-13 19:13:46', '2022-10-13 19:13:46'),
(834, 26, '5SOCV6i2xNrZ5DFt1mTwNEJtiQUvA2Or', '2022-10-14 14:10:59', '2022-10-14 14:10:59'),
(835, 25, 'bIbRViwhd7uot5f0ercToW3LgZKm22YL', '2022-10-14 16:33:29', '2022-10-14 16:33:29'),
(836, 24, 'lYWpe1AR2rGB3Bgoz9Q2HNdVhkAhhT2h', '2022-10-14 16:41:59', '2022-10-14 16:41:59'),
(837, 26, '3ocwG7gaTExM6h2Wq1o1k3MAoWuf2DJs', '2022-10-14 19:48:05', '2022-10-14 19:48:05'),
(838, 26, 'w62XVpXDxu5xzaAA5foqvtwF6jFiObdU', '2022-10-17 12:11:24', '2022-10-17 12:11:24'),
(839, 25, '9qaVmmdG0WsRjLvcCDn56VmcyOkBTMoc', '2022-10-17 18:05:54', '2022-10-17 18:05:54'),
(840, 24, 'gxEKZmq82kZpiqztZJ0ueHCo06mevTTO', '2022-10-17 19:03:44', '2022-10-17 19:03:44'),
(841, 26, 'pfJO4zWxT0jFfjAEp6DngNfRFCvsMkLO', '2022-10-17 19:07:39', '2022-10-17 19:07:39'),
(842, 26, 'SKZzcIBOerKRwiQiPLb7nmfGGs5VcI7G', '2022-10-19 13:12:27', '2022-10-19 13:12:27'),
(843, 26, 'AibCp7EcEz78BXf7Ou6NMltfvlPZWrfc', '2022-10-20 14:59:45', '2022-10-20 14:59:45'),
(844, 26, 'iY57caJ0yq7z0Vb8esuR7OVsQnsxF4C5', '2022-10-21 14:27:13', '2022-10-21 14:27:13'),
(845, 1, 'ZmJ1gUrvOdMXbW97HDQMH6O9ZaSqfwwB', '2022-10-24 17:03:10', '2022-10-24 17:03:10'),
(846, 26, 'p6T6JhD7VfuVVqJpGjTdjlOYm1vNAMPl', '2022-10-25 17:53:42', '2022-10-25 17:53:42'),
(847, 25, '5BtMrqnZjLHWEega6wrJERrmlTurWoo7', '2022-10-25 18:31:33', '2022-10-25 18:31:33'),
(848, 25, '54Qu5N96pp4HJK8CzGXjNqKuwqLlbNAy', '2022-10-25 20:46:16', '2022-10-25 20:46:16'),
(849, 1, 'WINLmyE157qeKLbFLRkqNwbTK7XdfsZp', '2022-10-26 14:16:36', '2022-10-26 14:16:36');

-- --------------------------------------------------------

--
-- Table structure for table `reminders`
--

CREATE TABLE `reminders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_scheduler`
--

CREATE TABLE `report_scheduler` (
  `id` int(10) UNSIGNED NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `report_start_date` date DEFAULT NULL,
  `report_start_time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recurrence_type` enum('none','schedule') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recur_frequency` enum('daily','monthly','weekly','yearly') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recur_interval` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_recipients` text COLLATE utf8mb4_unicode_ci,
  `email_subject` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_message` text COLLATE utf8mb4_unicode_ci,
  `email_attachment_file_format` enum('pdf','csv','xls') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `report_category` enum('client_report','loan_report','financial_report','group_report','savings_report','organisation_report') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `report_name` enum('disbursed_loans_report','loan_portfolio_report','expected_repayments_report','repayments_report','collection_report','arrears_report','balance_sheet','trial_balance','profit_and_loss','cash_flow','provisioning','historical_income_statement','journals_report','accrued_interest','client_numbers_report','clients_overview','top_clients_report','loan_sizes_report','group_report','group_breakdown','savings_account_report','savings_balance_report','savings_transaction_report','fixed_term_maturity_report','products_summary','individual_indicator_report','loan_officer_performance_report','audit_report','group_indicator_report') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date_type` enum('date_picker','today','yesterday','tomorrow') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date_type` enum('date_picker','today','yesterday','tomorrow') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `office_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loan_officer_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gl_account_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manual_entries` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loan_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loan_product_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_run_date` date DEFAULT NULL,
  `next_run_date` date DEFAULT NULL,
  `last_run_time` date DEFAULT NULL,
  `next_run_time` date DEFAULT NULL,
  `number_of_runs` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `status` enum('pending','approved','declined') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_scheduler_run_history`
--

CREATE TABLE `report_scheduler_run_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `report_schedule_id` int(11) DEFAULT NULL,
  `report_start_date` date DEFAULT NULL,
  `report_start_time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time_limit` tinyint(4) NOT NULL DEFAULT '0',
  `from_time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_days` text COLLATE utf8mb4_unicode_ci,
  `permissions` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `slug`, `name`, `time_limit`, `from_time`, `to_time`, `access_days`, `permissions`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Admin', 0, NULL, NULL, '[]', '{\"users\":true,\"users.view\":true,\"users.create\":true,\"users.update\":true,\"users.delete\":true,\"users.roles.view\":true,\"users.roles.create\":true,\"users.roles.update\":true,\"users.roles.delete\":true,\"users.roles.assign\":true,\"settings\":true,\"settings.update\":true,\"settings.general.view\":true,\"settings.organisation.view\":true,\"accounting\":true,\"accounting.gl_accounts.view\":true,\"accounting.gl_accounts.create\":true,\"accounting.gl_accounts.update\":true,\"accounting.gl_accounts.delete\":true,\"accounting.journals.view\":true,\"accounting.journals.create\":true,\"accounting.journals.delete\":true,\"accounting.journals.approve\":true,\"accounting.journals.reconciliation.view\":true,\"accounting.journals.reconciliation.create\":true,\"accounting.period.view\":true,\"accounting.period.create\":true,\"accounting.period.delete\":true,\"accounting.journals.create_op\":true,\"audit_trail\":true,\"offices\":true,\"offices.view\":true,\"offices.create\":true,\"offices.update\":true,\"offices.delete\":true,\"clients\":true,\"clients.view\":true,\"clients.create\":true,\"clients.update\":true,\"clients.delete\":true,\"clients.approve\":true,\"clients.close\":true,\"clients.pending_approval\":true,\"clients.closed\":true,\"clients.documents.create\":true,\"clients.documents.view\":true,\"clients.documents.delete\":true,\"clients.documents.update\":true,\"clients.next_of_kin.view\":true,\"clients.next_of_kin.create\":true,\"clients.next_of_kin.update\":true,\"clients.next_of_kin.delete\":true,\"clients.identification.view\":true,\"clients.identification.create\":true,\"clients.identification.update\":true,\"clients.identification.delete\":true,\"clients.notes.view\":true,\"clients.notes.create\":true,\"clients.notes.update\":true,\"clients.notes.delete\":true,\"clients.accounts.view\":true,\"clients.transfer.client\":true,\"clients.transfer.approve\":true,\"clients.view_assigned\":true,\"clients.view_created\":true,\"clients.my_clients\":true,\"clients.branch_clients\":true,\"groups\":true,\"groups.view\":true,\"groups.create\":true,\"groups.approve\":true,\"groups.update\":true,\"groups.client.create\":true,\"groups.client.delete\":true,\"groups.documents.view\":true,\"groups.documents.create\":true,\"groups.documents.update\":true,\"groups.documents.delete\":true,\"groups.notes.view\":true,\"groups.notes.create\":true,\"groups.notes.update\":true,\"groups.notes.delete\":true,\"groups.view_assigned\":true,\"groups.view_created\":true,\"groups.pending_approval\":true,\"loans\":true,\"loans.view\":true,\"loans.pending_approval\":true,\"loans.awaiting_disbursement\":true,\"loans.declined\":true,\"loans.written_off\":true,\"loans.closed\":true,\"loans.rescheduled\":true,\"loans.evaluated\":true,\"loans.create\":true,\"loans.update\":true,\"loans.approve\":true,\"loans.disburse\":true,\"loans.undo_approval\":true,\"loans.undo_disbursement\":true,\"loans.write_off\":true,\"loans.undo_write_off\":true,\"loans.waive_interest\":true,\"loans.charge.create\":true,\"loans.charge.waive\":true,\"loans.view_assigned\":true,\"loans.reschedule.create\":true,\"loans.transactions.create\":true,\"loans.transactions.view\":true,\"loans.transactions.approve\":true,\"loans.transactions.update\":true,\"loans.transactions.system_reversed\":true,\"loans.view_repayment_schedule\":true,\"loans.documents.view\":true,\"loans.documents.create\":true,\"loans.documents.update\":true,\"loans.documents.delete\":true,\"loans.collateral.view\":true,\"loans.collateral.create\":true,\"loans.collateral.update\":true,\"loans.collateral.delete\":true,\"loans.guarantors.view\":true,\"loans.guarantors.create\":true,\"loans.guarantors.update\":true,\"loans.guarantors.delete\":true,\"loans.notes.view\":true,\"loans.notes.create\":true,\"loans.notes.update\":true,\"loans.notes.delete\":true,\"loans.view_group_allocation\":true,\"loans.view_client_details\":true,\"loans.email_schedule\":true,\"loans.pdf_schedule\":true,\"loans.my_loans\":true,\"loans.branch_loans\":true,\"savings\":true,\"savings.view\":true,\"savings.pending_approval\":true,\"savings.approved\":true,\"savings.closed\":true,\"savings.create\":true,\"savings.update\":true,\"savings.delete\":true,\"savings.approve\":true,\"savings.undo_approval\":true,\"savings.close\":true,\"savings.transactions.view\":true,\"savings.transactions.create\":true,\"savings.transactions.update\":true,\"savings.transactions.delete\":true,\"savings.documents.view\":true,\"savings.documents.create\":true,\"savings.documents.update\":true,\"savings.documents.delete\":true,\"savings.notes.view\":true,\"savings.notes.create\":true,\"savings.notes.update\":true,\"savings.notes.delete\":true,\"savings.post_interest\":true,\"savings.email_statement\":true,\"savings.pdf_statement\":true,\"savings.charge.create\":true,\"savings.charge.waive\":true,\"savings.transactions.approve\":true,\"savings.transactions.deposit\":true,\"savings.transactions.withdrawal\":true,\"products\":true,\"products.charges.view\":true,\"products.charges.create\":true,\"products.charges.update\":true,\"products.charges.delete\":true,\"products.currencies.view\":true,\"products.currencies.create\":true,\"products.currencies.update\":true,\"products.currencies.delete\":true,\"products.funds.view\":true,\"products.funds.create\":true,\"products.funds.update\":true,\"products.funds.delete\":true,\"products.payment_types.view\":true,\"products.payment_types.create\":true,\"products.payment_types.update\":true,\"products.payment_types.delete\":true,\"products.loan_purposes.view\":true,\"products.loan_purposes.create\":true,\"products.loan_purposes.delete\":true,\"products.loan_purposes.update\":true,\"products.collateral_types.view\":true,\"products.collateral_types.create\":true,\"products.collateral_types.update\":true,\"products.collateral_types.delete\":true,\"products.client_relationships.view\":true,\"products.client_relationships.create\":true,\"products.client_relationships.update\":true,\"products.client_relationships.delete\":true,\"products.client_identification_types.view\":true,\"products.client_identification_types.create\":true,\"products.client_identification_types.update\":true,\"products.client_identification_types.delete\":true,\"products.loan_provisioning_criteria.update\":true,\"products.loan_products.view\":true,\"products.loan_products.create\":true,\"products.loan_products.update\":true,\"products.loan_products.delete\":true,\"products.savings_products.view\":true,\"products.savings_products.create\":true,\"products.savings_products.update\":true,\"products.savings_products.delete\":true,\"reports\":true,\"reports.downloading_exporting_of_reports\":true,\"reports.client_reports\":true,\"reports.loan_reports\":true,\"reports.financial_reports\":true,\"reports.savings_reports\":true,\"reports.reports_scheduler\":true,\"reports.client_numbers_report\":true,\"reports.collection_sheet\":true,\"reports.repayments_report\":true,\"reports.expected_repayment\":true,\"reports.arrears_report\":true,\"reports.disbursed_loans\":true,\"reports.loan_portfolio\":true,\"reports.balance_sheet\":true,\"reports.trial_balance\":true,\"reports.income_statement\":true,\"reports.provisioning\":true,\"reports.journals_report\":true,\"reports.savings_transactions\":true,\"reports.savings_accounts_report\":true,\"reports.reports_scheduler.view\":true,\"reports.reports_scheduler.create\":true,\"reports.reports_scheduler.update\":true,\"reports.reports_scheduler.delete\":true,\"reports.age_analysis_reports\":true,\"reports.client_list_reports\":true,\"reports.daily_transactions_reports\":true,\"reports.loan_book\":true,\"communication\":true,\"communication.view\":true,\"communication.create\":true,\"communication.update\":true,\"communication.delete\":true,\"communication.approve\":true,\"dashboard\":true,\"dashboard.loans_disbursed\":true,\"dashboard.total_repayments\":true,\"dashboard.total_outstanding\":true,\"dashboard.amount_in_arrears\":true,\"dashboard.fees_earned\":true,\"dashboard.fees_paid\":true,\"dashboard.penalties_paid\":true,\"dashboard.penalties_earned\":true,\"dashboard.loans_status_overview\":true,\"dashboard.clients_overview\":true,\"dashboard.savings_balances_overview\":true,\"dashboard.my_loan_repayments\":true,\"dashboard.my_disbursed_loans\":true,\"dashboard.my_number_of_outstanding_loans\":true,\"dashboard.my_outstanding_loan_balance\":true,\"dashboard.my_clients\":true,\"dashboard.my_written_off_amount\":true,\"dashboard.collection_statistics\":true,\"dashboard.my_disbursed\":true,\"dashboard.my_repayments_loans\":true,\"dashboard.my_outstanding_loans\":true,\"dashboard.my_loan_arrears\":true,\"dashboard.my_branch_disbursed\":true,\"dashboard.my_branch_repayments\":true,\"dashboard.my_branch_outstanding\":true,\"dashboard.my_branch_arrears\":true,\"custom_fields\":true,\"custom_fields.view\":true,\"custom_fields.create\":true,\"custom_fields.update\":true,\"custom_fields.delete\":true,\"assets\":true,\"assets.view\":true,\"assets.create\":true,\"assets.update\":true,\"assets.delete\":true,\"assets.types.view\":true,\"assets.types.create\":true,\"assets.types.update\":true,\"assets.types.delete\":true,\"expenses\":true,\"expenses.view\":true,\"expenses.create\":true,\"expenses.update\":true,\"expenses.delete\":true,\"expenses.types.view\":true,\"expenses.types.create\":true,\"expenses.types.update\":true,\"expenses.types.delete\":true,\"expenses.budget.view\":true,\"expenses.budget.create\":true,\"expenses.budget.update\":true,\"expenses.budget.delete\":true,\"other_income\":true,\"other_income.view\":true,\"other_income.create\":true,\"other_income.update\":true,\"other_income.delete\":true,\"other_income.types.view\":true,\"other_income.types.create\":true,\"other_income.types.update\":true,\"other_income.types.delete\":true,\"payroll\":true,\"payroll.view\":true,\"payroll.create\":true,\"payroll.update\":true,\"payroll.delete\":true}', NULL, '2019-09-17 15:04:55'),
(2, 'client', 'Client', 0, NULL, NULL, '[]', '{}', NULL, NULL),
(4, 'client-service-executive', 'Client Service Executive', 0, NULL, NULL, '[]', '{\"users\":true,\"settings.update\":true,\"accounting.gl_accounts.view\":true,\"accounting.gl_accounts.create\":true,\"accounting.gl_accounts.update\":true,\"offices\":true,\"offices.view\":true,\"clients\":true,\"clients.view\":true,\"clients.create\":true,\"clients.update\":true,\"clients.pending_approval\":true,\"clients.closed\":true,\"clients.documents.create\":true,\"clients.documents.view\":true,\"clients.documents.delete\":true,\"clients.documents.update\":true,\"clients.next_of_kin.view\":true,\"clients.next_of_kin.create\":true,\"clients.next_of_kin.update\":true,\"clients.next_of_kin.delete\":true,\"clients.identification.view\":true,\"clients.identification.create\":true,\"clients.identification.update\":true,\"clients.identification.delete\":true,\"clients.notes.view\":true,\"clients.notes.create\":true,\"clients.notes.update\":true,\"clients.notes.delete\":true,\"clients.accounts.view\":true,\"clients.transfer.client\":true,\"clients.transfer.approve\":true,\"clients.view_assigned\":true,\"clients.view_created\":true,\"clients.my_clients\":true,\"clients.branch_clients\":true,\"groups\":true,\"groups.view\":true,\"groups.create\":true,\"groups.update\":true,\"groups.client.create\":true,\"groups.client.delete\":true,\"groups.documents.view\":true,\"groups.documents.create\":true,\"groups.documents.update\":true,\"groups.documents.delete\":true,\"groups.notes.view\":true,\"groups.notes.create\":true,\"groups.notes.update\":true,\"groups.notes.delete\":true,\"groups.view_assigned\":true,\"groups.view_created\":true,\"groups.pending_approval\":true,\"loans\":true,\"loans.view\":true,\"loans.pending_approval\":true,\"loans.awaiting_disbursement\":true,\"loans.declined\":true,\"loans.written_off\":true,\"loans.closed\":true,\"loans.rescheduled\":true,\"loans.evaluated\":true,\"loans.create\":true,\"loans.update\":true,\"loans.charge.waive\":true,\"loans.view_assigned\":true,\"loans.reschedule.create\":true,\"loans.transactions.create\":true,\"loans.transactions.view\":true,\"loans.transactions.update\":true,\"loans.transactions.system_reversed\":true,\"loans.view_repayment_schedule\":true,\"loans.documents.view\":true,\"loans.documents.create\":true,\"loans.documents.update\":true,\"loans.documents.delete\":true,\"loans.collateral.view\":true,\"loans.collateral.create\":true,\"loans.collateral.update\":true,\"loans.collateral.delete\":true,\"loans.guarantors.view\":true,\"loans.guarantors.create\":true,\"loans.guarantors.update\":true,\"loans.guarantors.delete\":true,\"loans.notes.view\":true,\"loans.notes.create\":true,\"loans.notes.update\":true,\"loans.notes.delete\":true,\"loans.view_group_allocation\":true,\"loans.view_client_details\":true,\"loans.email_schedule\":true,\"loans.pdf_schedule\":true,\"loans.my_loans\":true,\"loans.branch_loans\":true,\"reports\":true,\"reports.downloading_exporting_of_reports\":true,\"reports.client_reports\":true,\"reports.loan_reports\":true,\"reports.reports_scheduler\":true,\"reports.client_numbers_report\":true,\"reports.repayments_report\":true,\"reports.expected_repayment\":true,\"reports.arrears_report\":true,\"reports.disbursed_loans\":true,\"reports.loan_portfolio\":true,\"reports.balance_sheet\":true,\"reports.trial_balance\":true,\"reports.income_statement\":true,\"reports.provisioning\":true,\"reports.journals_report\":true,\"reports.savings_transactions\":true,\"reports.savings_accounts_report\":true,\"reports.reports_scheduler.view\":true,\"reports.reports_scheduler.create\":true,\"reports.reports_scheduler.update\":true,\"reports.reports_scheduler.delete\":true,\"reports.grant_reports\":true,\"reports.age_analysis_reports\":true,\"reports.client_list_reports\":true,\"reports.consolidated_trial_balance\":true,\"reports.daily_transactions_reports\":true,\"reports.loan_book\":true,\"reports.my_collection_sheet\":true,\"reports.my_repayment_report\":true,\"communication\":true,\"communication.view\":true,\"communication.create\":true,\"communication.update\":true,\"communication.delete\":true,\"communication.approve\":true,\"dashboard\":true,\"dashboard.fees_earned\":true,\"dashboard.fees_paid\":true,\"dashboard.penalties_paid\":true,\"dashboard.penalties_earned\":true,\"dashboard.loans_status_overview\":true,\"dashboard.clients_overview\":true,\"dashboard.savings_balances_overview\":true,\"dashboard.my_loan_repayments\":true,\"dashboard.my_disbursed_loans\":true,\"dashboard.my_number_of_outstanding_loans\":true,\"dashboard.my_outstanding_loan_balance\":true,\"dashboard.my_clients\":true,\"dashboard.my_written_off_amount\":true,\"dashboard.collection_statistics\":true,\"dashboard.grants_status_overview\":true,\"dashboard.grants_disbursement_overview\":true,\"dashboard.my_disbursed\":true,\"dashboard.my_repayments_loans\":true,\"dashboard.my_outstanding_loans\":true,\"dashboard.my_loan_arrears\":true,\"dashboard.my_branch_disbursed\":true,\"dashboard.my_branch_repayments\":true,\"dashboard.my_branch_outstanding\":true,\"dashboard.my_branch_arrears\":true,\"expenses\":true,\"expenses.view\":true,\"expenses.create\":true,\"expenses.update\":true,\"expenses.delete\":true,\"expenses.types.view\":true,\"expenses.types.create\":true,\"expenses.types.update\":true,\"expenses.types.delete\":true,\"expenses.budget.view\":true,\"expenses.budget.create\":true,\"expenses.budget.update\":true,\"expenses.budget.delete\":true,\"other_income\":true,\"other_income.view\":true,\"other_income.create\":true,\"other_income.update\":true,\"other_income.delete\":true,\"other_income.types.view\":true,\"other_income.types.create\":true,\"other_income.types.update\":true,\"other_income.types.delete\":true}', '2018-12-01 08:46:50', '2022-10-08 12:17:05'),
(5, 'accountant', 'Accountant', 0, NULL, NULL, '[]', '{\"users\":true,\"users.view\":true,\"accounting\":true,\"accounting.gl_accounts.view\":true,\"accounting.gl_accounts.create\":true,\"accounting.gl_accounts.update\":true,\"accounting.gl_accounts.delete\":true,\"accounting.journals.view\":true,\"accounting.journals.create\":true,\"accounting.journals.delete\":true,\"accounting.journals.approve\":true,\"accounting.journals.reconciliation.view\":true,\"accounting.journals.reconciliation.create\":true,\"accounting.period.view\":true,\"accounting.period.create\":true,\"accounting.period.delete\":true,\"accounting.journals.create_op\":true,\"audit_trail\":true,\"offices\":true,\"offices.view\":true,\"clients\":true,\"clients.view\":true,\"groups.view\":true,\"groups.documents.view\":true,\"groups.notes.view\":true,\"groups.view_assigned\":true,\"groups.view_created\":true,\"groups.pending_approval\":true,\"loans\":true,\"loans.view\":true,\"loans.pending_approval\":true,\"loans.awaiting_disbursement\":true,\"loans.declined\":true,\"loans.written_off\":true,\"loans.closed\":true,\"loans.rescheduled\":true,\"loans.evaluated\":true,\"loans.write_off\":true,\"loans.undo_write_off\":true,\"loans.waive_interest\":true,\"loans.charge.create\":true,\"loans.charge.waive\":true,\"loans.view_assigned\":true,\"loans.transactions.create\":true,\"loans.transactions.view\":true,\"loans.transactions.system_reversed\":true,\"loans.view_repayment_schedule\":true,\"loans.documents.view\":true,\"loans.collateral.view\":true,\"loans.guarantors.view\":true,\"loans.notes.view\":true,\"loans.view_group_allocation\":true,\"loans.view_client_details\":true,\"loans.email_schedule\":true,\"loans.pdf_schedule\":true,\"products\":true,\"products.charges.view\":true,\"products.currencies.view\":true,\"products.funds.view\":true,\"products.payment_types.view\":true,\"products.payment_types.create\":true,\"products.payment_types.update\":true,\"products.payment_types.delete\":true,\"products.loan_purposes.view\":true,\"products.collateral_types.view\":true,\"products.client_relationships.view\":true,\"products.client_identification_types.view\":true,\"products.loan_provisioning_criteria.update\":true,\"products.loan_products.view\":true,\"products.savings_products.view\":true,\"reports\":true,\"reports.downloading_exporting_of_reports\":true,\"reports.client_reports\":true,\"reports.loan_reports\":true,\"reports.financial_reports\":true,\"reports.savings_reports\":true,\"reports.reports_scheduler\":true,\"reports.client_numbers_report\":true,\"reports.collection_sheet\":true,\"reports.repayments_report\":true,\"reports.expected_repayment\":true,\"reports.arrears_report\":true,\"reports.disbursed_loans\":true,\"reports.loan_portfolio\":true,\"reports.balance_sheet\":true,\"reports.trial_balance\":true,\"reports.income_statement\":true,\"reports.provisioning\":true,\"reports.journals_report\":true,\"reports.savings_transactions\":true,\"reports.savings_accounts_report\":true,\"reports.reports_scheduler.view\":true,\"reports.reports_scheduler.create\":true,\"reports.reports_scheduler.update\":true,\"reports.reports_scheduler.delete\":true,\"reports.grant_reports\":true,\"reports.age_analysis_reports\":true,\"reports.client_list_reports\":true,\"reports.consolidated_trial_balance\":true,\"reports.daily_transactions_reports\":true,\"reports.loan_book\":true,\"reports.my_collection_sheet\":true,\"reports.my_repayment_report\":true,\"communication.view\":true,\"dashboard\":true,\"dashboard.loans_disbursed\":true,\"dashboard.total_repayments\":true,\"dashboard.total_outstanding\":true,\"dashboard.amount_in_arrears\":true,\"dashboard.fees_earned\":true,\"dashboard.fees_paid\":true,\"dashboard.penalties_paid\":true,\"dashboard.penalties_earned\":true,\"dashboard.loans_status_overview\":true,\"dashboard.clients_overview\":true,\"dashboard.savings_balances_overview\":true,\"dashboard.my_loan_repayments\":true,\"dashboard.my_disbursed_loans\":true,\"dashboard.my_number_of_outstanding_loans\":true,\"dashboard.my_outstanding_loan_balance\":true,\"dashboard.my_clients\":true,\"dashboard.my_written_off_amount\":true,\"dashboard.collection_statistics\":true,\"dashboard.grants_status_overview\":true,\"dashboard.grants_disbursement_overview\":true,\"dashboard.my_disbursed\":true,\"dashboard.my_repayments_loans\":true,\"dashboard.my_outstanding_loans\":true,\"dashboard.my_loan_arrears\":true,\"dashboard.my_branch_disbursed\":true,\"dashboard.my_branch_repayments\":true,\"dashboard.my_branch_outstanding\":true,\"dashboard.my_branch_arrears\":true,\"assets\":true,\"assets.view\":true,\"assets.create\":true,\"assets.update\":true,\"assets.delete\":true,\"assets.types.view\":true,\"assets.types.create\":true,\"assets.types.update\":true,\"assets.types.delete\":true,\"expenses\":true,\"expenses.view\":true,\"expenses.create\":true,\"expenses.update\":true,\"expenses.delete\":true,\"expenses.types.view\":true,\"expenses.types.create\":true,\"expenses.types.update\":true,\"expenses.types.delete\":true,\"expenses.budget.view\":true,\"expenses.budget.create\":true,\"expenses.budget.update\":true,\"expenses.budget.delete\":true,\"other_income\":true,\"other_income.view\":true,\"other_income.create\":true,\"other_income.update\":true,\"other_income.delete\":true,\"other_income.types.view\":true,\"other_income.types.create\":true,\"other_income.types.update\":true,\"other_income.types.delete\":true,\"payroll\":true,\"payroll.view\":true,\"payroll.create\":true,\"payroll.update\":true,\"payroll.delete\":true}', '2020-05-26 22:48:57', '2020-06-19 18:10:59');

-- --------------------------------------------------------

--
-- Table structure for table `role_users`
--

CREATE TABLE `role_users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_users`
--

INSERT INTO `role_users` (`user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2019-03-12 07:34:14', '2019-03-12 07:34:14'),
(22, 4, '2022-10-08 12:09:54', '2022-10-08 12:09:54'),
(23, 4, '2022-10-08 12:18:58', '2022-10-08 12:18:58'),
(24, 4, '2022-10-08 12:20:14', '2022-10-08 12:20:14'),
(25, 1, '2022-10-08 12:21:26', '2022-10-08 12:21:26'),
(26, 1, '2022-10-08 12:22:52', '2022-10-08 12:22:52'),
(27, 1, '2022-10-08 12:25:51', '2022-10-08 12:25:51');

-- --------------------------------------------------------

--
-- Table structure for table `saved_queries`
--

CREATE TABLE `saved_queries` (
  `name` varchar(30) DEFAULT NULL,
  `description` varchar(300) DEFAULT NULL,
  `query` varchar(3000) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `savings`
--

CREATE TABLE `savings` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_type` enum('client','group') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'client',
  `client_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `office_id` int(11) DEFAULT NULL,
  `field_officer_id` int(11) DEFAULT NULL,
  `savings_product_id` int(11) DEFAULT NULL,
  `external_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `decimals` int(11) NOT NULL DEFAULT '2',
  `interest_rate` decimal(65,4) DEFAULT NULL,
  `allow_overdraft` tinyint(4) NOT NULL DEFAULT '0',
  `minimum_balance` decimal(65,4) DEFAULT NULL,
  `overdraft_limit` decimal(65,4) DEFAULT NULL,
  `interest_compounding_period` enum('daily','monthly','quarterly','biannual','annually') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interest_posting_period` enum('monthly','quarterly','biannual','annually') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `allow_transfer_withdrawal_fee` tinyint(4) NOT NULL DEFAULT '0',
  `opening_balance` decimal(65,4) DEFAULT NULL,
  `allow_additional_charges` tinyint(4) NOT NULL DEFAULT '0',
  `year_days` enum('360','365') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '365',
  `status` enum('pending','approved','closed','declined','withdrawn') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_by_id` int(11) DEFAULT NULL,
  `modified_by_id` int(11) DEFAULT NULL,
  `approved_by_id` int(11) DEFAULT NULL,
  `closed_by_id` int(11) DEFAULT NULL,
  `declined_by_id` int(11) DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  `modified_date` date DEFAULT NULL,
  `approved_date` date DEFAULT NULL,
  `declined_date` date DEFAULT NULL,
  `closed_date` date DEFAULT NULL,
  `month` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `approved_notes` text COLLATE utf8mb4_unicode_ci,
  `declined_notes` text COLLATE utf8mb4_unicode_ci,
  `closed_notes` text COLLATE utf8mb4_unicode_ci,
  `balance` decimal(65,4) DEFAULT NULL,
  `deposits` decimal(65,4) DEFAULT NULL,
  `interest_earned` decimal(65,4) DEFAULT NULL,
  `interest_posted` decimal(65,4) DEFAULT NULL,
  `interest_overdraft` decimal(65,4) DEFAULT NULL,
  `withdrawals` decimal(65,4) DEFAULT NULL,
  `fees` decimal(65,4) DEFAULT NULL,
  `penalty` decimal(65,4) DEFAULT NULL,
  `start_interest_calculation_date` date DEFAULT NULL,
  `last_interest_calculation_date` date DEFAULT NULL,
  `next_interest_calculation_date` date DEFAULT NULL,
  `next_interest_posting_date` date DEFAULT NULL,
  `last_interest_posting_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `savings_charges`
--

CREATE TABLE `savings_charges` (
  `id` int(10) UNSIGNED NOT NULL,
  `savings_id` int(11) DEFAULT NULL,
  `charge_id` int(11) DEFAULT NULL,
  `penalty` tinyint(4) NOT NULL DEFAULT '0',
  `waived` tinyint(4) NOT NULL DEFAULT '0',
  `charge_type` enum('savings_activation','withdrawal_fee','annual_fee','monthly_fee','specified_due_date') COLLATE utf8mb4_unicode_ci NOT NULL,
  `charge_option` enum('flat','percentage') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(65,2) DEFAULT NULL,
  `amount_paid` decimal(65,2) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `grace_period` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `savings_products`
--

CREATE TABLE `savings_products` (
  `id` int(10) UNSIGNED NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `currency_id` int(11) DEFAULT NULL,
  `decimals` int(11) NOT NULL DEFAULT '2',
  `interest_rate` decimal(65,4) DEFAULT NULL,
  `allow_overdraft` tinyint(4) NOT NULL DEFAULT '0',
  `minimum_balance` decimal(65,4) DEFAULT NULL,
  `interest_compounding_period` enum('daily','monthly','quarterly','biannual','annually') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interest_posting_period` enum('monthly','quarterly','biannual','annually') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interest_calculation_type` enum('daily','average') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `allow_transfer_withdrawal_fee` tinyint(4) NOT NULL DEFAULT '0',
  `opening_balance` decimal(65,4) DEFAULT NULL,
  `allow_additional_charges` tinyint(4) NOT NULL DEFAULT '0',
  `year_days` enum('360','365') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '365',
  `accounting_rule` enum('none','cash') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `gl_account_savings_reference_id` int(11) DEFAULT NULL,
  `gl_account_overdraft_portfolio_id` int(11) DEFAULT NULL,
  `gl_account_savings_control_id` int(11) DEFAULT NULL,
  `gl_account_interest_on_savings_id` int(11) DEFAULT NULL,
  `gl_account_savings_written_off_id` int(11) DEFAULT NULL,
  `gl_account_income_interest_id` int(11) DEFAULT NULL,
  `gl_account_income_fee_id` int(11) DEFAULT NULL,
  `gl_account_income_penalty_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `savings_product_charges`
--

CREATE TABLE `savings_product_charges` (
  `id` int(10) UNSIGNED NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `charge_id` int(11) DEFAULT NULL,
  `savings_product_id` int(11) DEFAULT NULL,
  `amount` decimal(65,2) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `grace_period` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `savings_transactions`
--

CREATE TABLE `savings_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `office_id` int(11) DEFAULT NULL,
  `modified_by_id` int(11) DEFAULT NULL,
  `payment_detail_id` int(11) DEFAULT NULL,
  `savings_id` int(10) UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT '0.00',
  `debit` decimal(65,4) DEFAULT NULL,
  `credit` decimal(65,4) DEFAULT NULL,
  `balance` decimal(65,4) DEFAULT NULL,
  `transaction_type` enum('deposit','withdrawal','bank_fees','interest','dividend','guarantee','guarantee_restored','fees_payment','transfer_loan','transfer_savings','specified_due_date_fee') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reversible` tinyint(4) NOT NULL DEFAULT '0',
  `reversed` tinyint(4) NOT NULL DEFAULT '0',
  `reversal_type` enum('system','user','none') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `status` enum('pending','approved','declined') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `approved_by_id` int(11) DEFAULT NULL,
  `approved_date` date DEFAULT NULL,
  `system_interest` tinyint(4) NOT NULL DEFAULT '0',
  `date` date DEFAULT NULL,
  `time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `month` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `balance_date` date DEFAULT NULL,
  `balance_days` int(11) DEFAULT NULL,
  `cumulative_balance_days` int(11) DEFAULT NULL,
  `cumulative_balance` decimal(65,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `setting_key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'company_name', 'BML'),
(2, 'company_address', 'Suite 608'),
(3, 'company_currency', '37'),
(4, 'company_website', 'http://www.bmlbanking.com'),
(5, 'company_country', '245'),
(6, 'system_version', '1.0'),
(7, 'sms_enabled', '1'),
(8, 'active_sms', '1'),
(9, 'portal_address', 'http://www.'),
(10, 'company_email', 'info@bedfordmicrofinance.com'),
(11, 'currency_symbol', '$'),
(12, 'currency_position', 'left'),
(13, 'company_logo', 'cc51948e-acbc-4c9b-8eb0-d9f171faee5f.JPG'),
(14, 'paypal_email', ''),
(15, 'currency', 'USD'),
(16, 'password_reset_subject', 'Password reset instructions'),
(17, 'password_reset_template', '<div class=\"WordSection1\">\r\n<p>{companyLogo}</p>\r\n<p>Hello {firstName} {lastName},</p>\r\n<p>Your password has been reset. Please click the link below to reset it. If you did not request a password reset just ignore this ms</p>\r\n<p>Click <a href=\"{resetLink}\">{resetLink}</a></p>\r\n<p>Best Wishes, <br /> Webstudio Support Team<br /> The Web Specialists</p>\r\n</div>'),
(18, 'payment_received_sms_template', 'Dear {clientName}, we have received your payment of ${paymentAmount} for loan {loanNumber}. New loan balance:${loanBalance}. Thank you'),
(19, 'payment_received_email_template', '<p>Dear {clientName}, we have received your payment of ${paymentAmount} for loan {loanNumber}. New loan balance:${loanBalance}. Thank you</p>'),
(20, 'payment_received_email_subject', 'Payment Received'),
(21, 'savings_transaction_sms_template', 'Dear {clientName}, a {transactionType}   of ${transactionAmount} was made on your savings {savingsNumber}. New Savings balance:${savingsBalance}. Thank you'),
(22, 'savings_transaction_email_template', '<p>Dear {clientName}, a {transactionType} of ${transactionAmount} was made on your savings {savingsNumber}. New Savings balance:${savingsBalance}. Thank you</p>'),
(23, 'savings_transaction_email_subject', 'New Transaction'),
(24, 'payment_email_subject', 'Payment Receipt'),
(25, 'payment_email_template', '<p>Dear {clientName}, find attached receipt of your payment of ${paymentAmount} for loan {loanNumber} on {paymentDate}. New loan balance:${loanBalance}. Thank you</p>'),
(26, 'client_statement_email_subject', 'Client Statement'),
(27, 'client_statement_email_template', '<p>Dear {clientName}, find attached statement of your loans with us. Thank you</p>'),
(28, 'loan_statement_email_subject', 'Loan Statement'),
(29, 'loan_statement_email_template', '<p>Dear {clientName}, find attached loan statement for loan {loanNumber}. Thank you</p>'),
(30, 'loan_schedule_email_subject', 'Loan Schedule'),
(31, 'loan_schedule_email_template', '<p>Dear {clientName}, find attached loan schedule for loan {loanNumber}. Thank you</p>'),
(32, 'cron_last_run', '2019-05-16 15:35:38'),
(33, 'auto_apply_penalty', NULL),
(34, 'auto_payment_receipt_sms', '0'),
(35, 'auto_payment_receipt_email', '0'),
(36, 'auto_repayment_sms_reminder', '0'),
(37, 'auto_repayment_email_reminder', '0'),
(38, 'auto_repayment_days', '4'),
(39, 'auto_overdue_repayment_sms_reminder', '0'),
(40, 'auto_overdue_repayment_email_reminder', '0'),
(41, 'auto_overdue_repayment_days', '2'),
(42, 'auto_overdue_loan_sms_reminder', '0'),
(43, 'auto_overdue_loan_email_reminder', '0'),
(44, 'auto_overdue_loan_days', '2'),
(45, 'loan_overdue_email_subject', 'Loan Overdue'),
(46, 'loan_overdue_email_template', '<p>Dear {clientName}, Your loan {loanNumber} is overdue. Please make your payment. Thank you</p>'),
(47, 'loan_overdue_sms_template', 'Dear {clientName}, Your loan {loanNumber} is overdue. Please make your payment. Thank you'),
(48, 'loan_payment_reminder_subject', 'Upcoming Payment Reminder'),
(49, 'loan_payment_reminder_email_template', '<p>Dear {clientName},You have an upcoming payment of {paymentAmount} due on {paymentDate} for loan {loanNumber}. Please make your payment. Thank you</p>'),
(50, 'loan_payment_reminder_sms_template', 'Dear {clientName},You have an upcoming payment of {paymentAmount} due on {paymentDate} for loan {loanNumber}. Please make your payment. Thank you'),
(51, 'missed_payment_email_subject', 'Missed Payment'),
(52, 'missed_payment_email_template', '<p>Dear {clientName},You missed payment of {paymentAmount} which was due on {paymentDate} for loan {loanNumber}. Please make your payment. Thank you</p>'),
(53, 'missed_payment_sms_template', 'Dear {clientName},You missed  payment of {paymentAmount} which was due on {paymentDate} for loan {loanNumber}. Please make your payment. Thank you'),
(54, 'enable_cron', '1'),
(55, 'loan_approved_auto_email', '0'),
(56, 'loan_approved_auto_sms', '0'),
(57, 'loan_disbursed_auto_email', '0'),
(58, 'loan_disbursed_auto_sms', '0'),
(59, 'loan_approved_email_subject', 'Loan Approved'),
(60, 'loan_approved_email_template', '<p>Dear {clientName},Your loan {loanNumber} has been approved, amount {approvedAmount}. Thank you</p>'),
(61, 'loan_approved_sms_template', 'Dear {clientName},Your loan {loanNumber} has been approved, amount {approvedAmount}. Thank you'),
(62, 'loan_disbursed_email_subject', 'Loan Disbursed'),
(63, 'loan_disbursed_email_template', '<p>Dear {clientName},Your loan {loanNumber} has been disbursed.First payment:{firstPaymentDate},amount {firstPaymentAmount}. Thank you</p>'),
(64, 'loan_disbursed_sms_template', 'Dear {clientName},Your loan {loanNumber} has been disbursed.First payment:{firstPaymentDate},amount {firstPaymentAmount}. Thank you'),
(65, 'savings_statement_email_template', 'Dear {clientName},Find your statement for {savingsNumber} attached. Thank you'),
(66, 'allow_self_registration', '0'),
(67, 'allow_client_apply', '0'),
(68, 'enable_google_recaptcha', '0'),
(69, 'google_recaptcha_site_key', NULL),
(70, 'google_recaptcha_secret_key', NULL),
(71, 'update_url', 'http://webstudio.co.zw'),
(72, 'enable_custom_fields', '1'),
(73, 'login_details_email_subject', 'Login Details'),
(74, 'login_details_email_template', 'Dear {clientName},Your login details have been created. Your username is: {email}<br>Password  {password}.You can login <a href=\"{loginUrl}\">here</a> Thank you'),
(75, 'payroll_gl_account_expense_id', NULL),
(76, 'payroll_gl_account_asset_id', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sms_gateways`
--

CREATE TABLE `sms_gateways` (
  `id` int(10) UNSIGNED NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `name` text COLLATE utf8mb4_unicode_ci,
  `from_name` text COLLATE utf8mb4_unicode_ci,
  `to_name` text COLLATE utf8mb4_unicode_ci,
  `url` text COLLATE utf8mb4_unicode_ci,
  `msg_name` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sms_gateways`
--

INSERT INTO `sms_gateways` (`id`, `created_by_id`, `name`, `from_name`, `to_name`, `url`, `msg_name`, `notes`, `created_at`, `updated_at`) VALUES
(1, NULL, 'sms', NULL, 'GIPO', 'www.lightwinscreations.com', 'Gipo', 'notes', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `throttle`
--

CREATE TABLE `throttle` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `throttle`
--

INSERT INTO `throttle` (`id`, `user_id`, `type`, `ip`, `created_at`, `updated_at`) VALUES
(1, NULL, 'global', NULL, '2019-03-12 17:12:56', '2019-03-12 17:12:56'),
(2, NULL, 'ip', '197.220.200.180', '2019-03-12 17:12:56', '2019-03-12 17:12:56'),
(3, NULL, 'global', NULL, '2019-03-12 17:14:11', '2019-03-12 17:14:11'),
(4, NULL, 'ip', '197.220.200.180', '2019-03-12 17:14:11', '2019-03-12 17:14:11'),
(5, NULL, 'global', NULL, '2019-03-12 17:36:32', '2019-03-12 17:36:32'),
(6, NULL, 'ip', '197.220.200.180', '2019-03-12 17:36:32', '2019-03-12 17:36:32'),
(7, NULL, 'global', NULL, '2019-03-12 21:57:25', '2019-03-12 21:57:25'),
(8, NULL, 'ip', '102.148.178.243', '2019-03-12 21:57:25', '2019-03-12 21:57:25'),
(9, NULL, 'global', NULL, '2019-03-13 13:06:03', '2019-03-13 13:06:03'),
(10, NULL, 'ip', '102.145.156.88', '2019-03-13 13:06:03', '2019-03-13 13:06:03'),
(11, NULL, 'global', NULL, '2019-03-13 16:02:28', '2019-03-13 16:02:28'),
(12, NULL, 'ip', '197.220.200.180', '2019-03-13 16:02:28', '2019-03-13 16:02:28'),
(13, NULL, 'global', NULL, '2019-03-13 23:38:26', '2019-03-13 23:38:26'),
(14, NULL, 'ip', '197.220.200.180', '2019-03-13 23:38:26', '2019-03-13 23:38:26'),
(15, NULL, 'global', NULL, '2019-03-14 15:51:28', '2019-03-14 15:51:28'),
(16, NULL, 'ip', '197.220.200.180', '2019-03-14 15:51:28', '2019-03-14 15:51:28'),
(17, NULL, 'global', NULL, '2019-03-14 15:57:49', '2019-03-14 15:57:49'),
(18, NULL, 'ip', '197.220.200.180', '2019-03-14 15:57:49', '2019-03-14 15:57:49'),
(19, NULL, 'global', NULL, '2019-03-14 18:58:17', '2019-03-14 18:58:17'),
(20, NULL, 'ip', '197.220.200.180', '2019-03-14 18:58:17', '2019-03-14 18:58:17'),
(21, NULL, 'global', NULL, '2019-03-14 19:11:06', '2019-03-14 19:11:06'),
(22, NULL, 'ip', '197.220.200.180', '2019-03-14 19:11:06', '2019-03-14 19:11:06'),
(23, NULL, 'global', NULL, '2019-03-14 19:12:27', '2019-03-14 19:12:27'),
(24, NULL, 'ip', '197.220.200.180', '2019-03-14 19:12:27', '2019-03-14 19:12:27'),
(25, NULL, 'global', NULL, '2019-03-14 19:13:39', '2019-03-14 19:13:39'),
(26, NULL, 'ip', '197.220.200.180', '2019-03-14 19:13:39', '2019-03-14 19:13:39'),
(27, NULL, 'global', NULL, '2019-03-14 20:23:38', '2019-03-14 20:23:38'),
(28, NULL, 'ip', '197.220.200.180', '2019-03-14 20:23:38', '2019-03-14 20:23:38'),
(29, NULL, 'global', NULL, '2019-03-14 20:24:17', '2019-03-14 20:24:17'),
(30, NULL, 'ip', '197.220.200.180', '2019-03-14 20:24:17', '2019-03-14 20:24:17'),
(31, NULL, 'global', NULL, '2019-03-16 16:32:22', '2019-03-16 16:32:22'),
(32, NULL, 'ip', '197.220.200.180', '2019-03-16 16:32:22', '2019-03-16 16:32:22'),
(33, NULL, 'global', NULL, '2019-03-19 16:24:10', '2019-03-19 16:24:10'),
(34, NULL, 'ip', '41.223.119.37', '2019-03-19 16:24:10', '2019-03-19 16:24:10'),
(35, NULL, 'global', NULL, '2019-03-19 23:13:27', '2019-03-19 23:13:27'),
(36, NULL, 'ip', '197.220.200.180', '2019-03-19 23:13:27', '2019-03-19 23:13:27'),
(37, NULL, 'global', NULL, '2019-03-23 07:09:30', '2019-03-23 07:09:30'),
(38, NULL, 'ip', '41.223.117.70', '2019-03-23 07:09:30', '2019-03-23 07:09:30'),
(39, NULL, 'global', NULL, '2019-03-23 07:09:54', '2019-03-23 07:09:54'),
(40, NULL, 'ip', '41.223.117.70', '2019-03-23 07:09:54', '2019-03-23 07:09:54'),
(41, NULL, 'global', NULL, '2019-04-06 16:42:20', '2019-04-06 16:42:20'),
(42, NULL, 'ip', '197.220.200.180', '2019-04-06 16:42:20', '2019-04-06 16:42:20'),
(43, NULL, 'global', NULL, '2019-04-08 16:23:54', '2019-04-08 16:23:54'),
(44, NULL, 'ip', '102.146.192.126', '2019-04-08 16:23:54', '2019-04-08 16:23:54'),
(45, NULL, 'global', NULL, '2019-04-09 14:27:32', '2019-04-09 14:27:32'),
(46, NULL, 'ip', '41.72.120.226', '2019-04-09 14:27:32', '2019-04-09 14:27:32'),
(47, NULL, 'global', NULL, '2019-04-09 14:28:06', '2019-04-09 14:28:06'),
(48, NULL, 'ip', '41.72.120.226', '2019-04-09 14:28:06', '2019-04-09 14:28:06'),
(49, NULL, 'global', NULL, '2019-04-09 14:32:51', '2019-04-09 14:32:51'),
(50, NULL, 'ip', '41.72.120.226', '2019-04-09 14:32:51', '2019-04-09 14:32:51'),
(51, NULL, 'global', NULL, '2019-04-09 14:34:14', '2019-04-09 14:34:14'),
(52, NULL, 'ip', '41.72.120.226', '2019-04-09 14:34:14', '2019-04-09 14:34:14'),
(53, NULL, 'global', NULL, '2019-04-09 14:34:36', '2019-04-09 14:34:36'),
(54, NULL, 'ip', '41.72.120.226', '2019-04-09 14:34:36', '2019-04-09 14:34:36'),
(55, NULL, 'global', NULL, '2019-04-09 14:35:00', '2019-04-09 14:35:00'),
(56, NULL, 'ip', '41.72.120.226', '2019-04-09 14:35:00', '2019-04-09 14:35:00'),
(57, NULL, 'global', NULL, '2019-04-09 14:41:42', '2019-04-09 14:41:42'),
(58, NULL, 'ip', '102.148.210.69', '2019-04-09 14:41:42', '2019-04-09 14:41:42'),
(59, NULL, 'global', NULL, '2019-04-09 14:51:07', '2019-04-09 14:51:07'),
(60, NULL, 'ip', '41.72.120.226', '2019-04-09 14:51:07', '2019-04-09 14:51:07'),
(61, NULL, 'global', NULL, '2019-04-09 14:52:20', '2019-04-09 14:52:20'),
(62, NULL, 'ip', '41.72.120.226', '2019-04-09 14:52:20', '2019-04-09 14:52:20'),
(63, NULL, 'global', NULL, '2019-04-09 14:53:29', '2019-04-09 14:53:29'),
(64, NULL, 'ip', '41.72.120.226', '2019-04-09 14:53:29', '2019-04-09 14:53:29'),
(65, NULL, 'global', NULL, '2019-04-09 14:54:15', '2019-04-09 14:54:15'),
(66, NULL, 'ip', '41.72.120.226', '2019-04-09 14:54:15', '2019-04-09 14:54:15'),
(67, NULL, 'global', NULL, '2019-04-10 16:35:08', '2019-04-10 16:35:08'),
(68, NULL, 'ip', '102.145.49.241', '2019-04-10 16:35:08', '2019-04-10 16:35:08'),
(69, NULL, 'global', NULL, '2019-04-10 22:40:02', '2019-04-10 22:40:02'),
(70, NULL, 'ip', '102.149.153.223', '2019-04-10 22:40:02', '2019-04-10 22:40:02'),
(71, NULL, 'global', NULL, '2019-04-11 15:18:13', '2019-04-11 15:18:13'),
(72, NULL, 'ip', '102.144.3.61', '2019-04-11 15:18:13', '2019-04-11 15:18:13'),
(73, NULL, 'global', NULL, '2019-04-11 18:07:54', '2019-04-11 18:07:54'),
(74, NULL, 'ip', '41.223.117.77', '2019-04-11 18:07:54', '2019-04-11 18:07:54'),
(75, NULL, 'global', NULL, '2019-04-14 22:31:31', '2019-04-14 22:31:31'),
(76, NULL, 'ip', '197.220.200.180', '2019-04-14 22:31:31', '2019-04-14 22:31:31'),
(78, NULL, 'global', NULL, '2019-04-27 19:52:24', '2019-04-27 19:52:24'),
(79, NULL, 'ip', '197.220.200.180', '2019-04-27 19:52:24', '2019-04-27 19:52:24'),
(81, NULL, 'global', NULL, '2019-04-27 19:52:25', '2019-04-27 19:52:25'),
(82, NULL, 'ip', '197.220.200.180', '2019-04-27 19:52:25', '2019-04-27 19:52:25'),
(84, NULL, 'global', NULL, '2019-04-27 20:03:27', '2019-04-27 20:03:27'),
(85, NULL, 'ip', '197.220.200.180', '2019-04-27 20:03:27', '2019-04-27 20:03:27'),
(86, NULL, 'global', NULL, '2019-04-27 22:25:51', '2019-04-27 22:25:51'),
(87, NULL, 'ip', '197.220.200.180', '2019-04-27 22:25:51', '2019-04-27 22:25:51'),
(88, NULL, 'global', NULL, '2019-04-28 16:46:49', '2019-04-28 16:46:49'),
(89, NULL, 'ip', '197.220.200.180', '2019-04-28 16:46:49', '2019-04-28 16:46:49'),
(90, NULL, 'global', NULL, '2019-04-29 00:31:13', '2019-04-29 00:31:13'),
(91, NULL, 'ip', '197.220.200.180', '2019-04-29 00:31:13', '2019-04-29 00:31:13'),
(92, 1, 'user', NULL, '2019-04-29 00:31:13', '2019-04-29 00:31:13'),
(93, NULL, 'global', NULL, '2019-05-15 17:41:39', '2019-05-15 17:41:39'),
(94, NULL, 'ip', '197.220.200.180', '2019-05-15 17:41:39', '2019-05-15 17:41:39'),
(95, NULL, 'global', NULL, '2019-05-15 17:44:57', '2019-05-15 17:44:57'),
(96, NULL, 'ip', '197.220.200.180', '2019-05-15 17:44:57', '2019-05-15 17:44:57'),
(97, NULL, 'global', NULL, '2019-05-15 17:45:36', '2019-05-15 17:45:36'),
(98, NULL, 'ip', '197.220.200.180', '2019-05-15 17:45:36', '2019-05-15 17:45:36'),
(100, NULL, 'global', NULL, '2019-05-15 17:50:20', '2019-05-15 17:50:20'),
(101, NULL, 'ip', '197.220.200.180', '2019-05-15 17:50:20', '2019-05-15 17:50:20'),
(102, NULL, 'global', NULL, '2019-05-15 17:52:22', '2019-05-15 17:52:22'),
(103, NULL, 'ip', '197.220.200.180', '2019-05-15 17:52:22', '2019-05-15 17:52:22'),
(105, NULL, 'global', NULL, '2019-05-15 17:53:20', '2019-05-15 17:53:20'),
(106, NULL, 'ip', '197.220.200.180', '2019-05-15 17:53:20', '2019-05-15 17:53:20'),
(107, NULL, 'global', NULL, '2019-05-16 19:04:51', '2019-05-16 19:04:51'),
(108, NULL, 'ip', '41.223.118.69', '2019-05-16 19:04:51', '2019-05-16 19:04:51'),
(109, NULL, 'global', NULL, '2019-05-20 15:46:04', '2019-05-20 15:46:04'),
(110, NULL, 'ip', '197.220.200.180', '2019-05-20 15:46:04', '2019-05-20 15:46:04'),
(112, NULL, 'global', NULL, '2019-06-20 18:53:16', '2019-06-20 18:53:16'),
(113, NULL, 'ip', '41.223.118.74', '2019-06-20 18:53:16', '2019-06-20 18:53:16'),
(115, NULL, 'global', NULL, '2019-07-11 19:30:14', '2019-07-11 19:30:14'),
(116, NULL, 'ip', '102.149.111.104', '2019-07-11 19:30:14', '2019-07-11 19:30:14'),
(117, NULL, 'global', NULL, '2019-07-17 15:51:48', '2019-07-17 15:51:48'),
(118, NULL, 'ip', '41.223.119.41', '2019-07-17 15:51:48', '2019-07-17 15:51:48'),
(119, NULL, 'global', NULL, '2019-07-17 15:53:19', '2019-07-17 15:53:19'),
(120, NULL, 'ip', '41.223.119.41', '2019-07-17 15:53:19', '2019-07-17 15:53:19'),
(121, NULL, 'global', NULL, '2019-07-17 15:56:17', '2019-07-17 15:56:17'),
(122, NULL, 'ip', '41.223.119.41', '2019-07-17 15:56:17', '2019-07-17 15:56:17'),
(123, NULL, 'global', NULL, '2019-07-17 16:30:08', '2019-07-17 16:30:08'),
(124, NULL, 'ip', '41.223.119.41', '2019-07-17 16:30:08', '2019-07-17 16:30:08'),
(125, NULL, 'global', NULL, '2019-07-17 16:30:51', '2019-07-17 16:30:51'),
(126, NULL, 'ip', '41.223.119.41', '2019-07-17 16:30:51', '2019-07-17 16:30:51'),
(127, NULL, 'global', NULL, '2019-07-17 16:31:57', '2019-07-17 16:31:57'),
(128, NULL, 'ip', '41.223.119.41', '2019-07-17 16:31:57', '2019-07-17 16:31:57'),
(129, NULL, 'global', NULL, '2019-08-23 15:44:55', '2019-08-23 15:44:55'),
(130, NULL, 'ip', '197.220.200.180', '2019-08-23 15:44:55', '2019-08-23 15:44:55'),
(131, NULL, 'global', NULL, '2019-08-23 15:45:12', '2019-08-23 15:45:12'),
(132, NULL, 'ip', '197.220.200.180', '2019-08-23 15:45:12', '2019-08-23 15:45:12'),
(133, NULL, 'global', NULL, '2019-09-23 16:17:15', '2019-09-23 16:17:15'),
(134, NULL, 'ip', '197.220.200.180', '2019-09-23 16:17:15', '2019-09-23 16:17:15'),
(136, NULL, 'global', NULL, '2019-10-25 16:47:13', '2019-10-25 16:47:13'),
(137, NULL, 'ip', '165.56.62.134', '2019-10-25 16:47:13', '2019-10-25 16:47:13'),
(138, 1, 'user', NULL, '2019-10-25 16:47:13', '2019-10-25 16:47:13'),
(139, NULL, 'global', NULL, '2019-10-25 17:23:32', '2019-10-25 17:23:32'),
(140, NULL, 'ip', '197.220.200.180', '2019-10-25 17:23:32', '2019-10-25 17:23:32'),
(141, NULL, 'global', NULL, '2020-03-04 18:28:54', '2020-03-04 18:28:54'),
(142, NULL, 'ip', '41.223.117.76', '2020-03-04 18:28:54', '2020-03-04 18:28:54'),
(143, NULL, 'global', NULL, '2020-03-09 05:26:16', '2020-03-09 05:26:16'),
(144, NULL, 'ip', '102.148.104.34', '2020-03-09 05:26:16', '2020-03-09 05:26:16'),
(145, NULL, 'global', NULL, '2020-03-17 02:06:45', '2020-03-17 02:06:45'),
(146, NULL, 'ip', '45.214.92.79', '2020-03-17 02:06:45', '2020-03-17 02:06:45'),
(147, NULL, 'global', NULL, '2020-03-17 02:08:40', '2020-03-17 02:08:40'),
(148, NULL, 'ip', '45.214.92.79', '2020-03-17 02:08:40', '2020-03-17 02:08:40'),
(149, NULL, 'global', NULL, '2020-03-29 22:32:00', '2020-03-29 22:32:00'),
(150, NULL, 'ip', '45.213.75.215', '2020-03-29 22:32:00', '2020-03-29 22:32:00'),
(151, 1, 'user', NULL, '2020-03-29 22:32:00', '2020-03-29 22:32:00'),
(152, NULL, 'global', NULL, '2020-05-05 22:18:45', '2020-05-05 22:18:45'),
(153, NULL, 'ip', '41.223.116.254', '2020-05-05 22:18:45', '2020-05-05 22:18:45'),
(154, NULL, 'global', NULL, '2020-05-05 22:19:25', '2020-05-05 22:19:25'),
(155, NULL, 'ip', '41.223.116.254', '2020-05-05 22:19:25', '2020-05-05 22:19:25'),
(156, NULL, 'global', NULL, '2020-05-05 22:20:27', '2020-05-05 22:20:27'),
(157, NULL, 'ip', '41.223.116.254', '2020-05-05 22:20:27', '2020-05-05 22:20:27'),
(158, NULL, 'global', NULL, '2020-05-05 22:23:00', '2020-05-05 22:23:00'),
(159, NULL, 'ip', '41.223.116.254', '2020-05-05 22:23:00', '2020-05-05 22:23:00'),
(161, NULL, 'global', NULL, '2020-05-05 22:23:28', '2020-05-05 22:23:28'),
(162, NULL, 'ip', '41.223.116.254', '2020-05-05 22:23:28', '2020-05-05 22:23:28'),
(164, NULL, 'global', NULL, '2020-05-09 15:19:08', '2020-05-09 15:19:08'),
(165, NULL, 'ip', '::1', '2020-05-09 15:19:08', '2020-05-09 15:19:08'),
(166, NULL, 'global', NULL, '2020-06-09 15:24:21', '2020-06-09 15:24:21'),
(167, NULL, 'ip', '41.223.117.74', '2020-06-09 15:24:21', '2020-06-09 15:24:21'),
(169, NULL, 'global', NULL, '2020-06-09 21:53:02', '2020-06-09 21:53:02'),
(170, NULL, 'ip', '197.212.166.42', '2020-06-09 21:53:02', '2020-06-09 21:53:02'),
(171, 1, 'user', NULL, '2020-06-09 21:53:02', '2020-06-09 21:53:02'),
(172, NULL, 'global', NULL, '2020-06-09 21:53:55', '2020-06-09 21:53:55'),
(173, NULL, 'ip', '197.212.166.42', '2020-06-09 21:53:55', '2020-06-09 21:53:55'),
(174, 1, 'user', NULL, '2020-06-09 21:53:55', '2020-06-09 21:53:55'),
(175, NULL, 'global', NULL, '2020-06-09 21:54:09', '2020-06-09 21:54:09'),
(176, NULL, 'ip', '197.212.166.42', '2020-06-09 21:54:09', '2020-06-09 21:54:09'),
(177, NULL, 'global', NULL, '2020-06-09 21:56:31', '2020-06-09 21:56:31'),
(178, NULL, 'ip', '196.46.212.104', '2020-06-09 21:56:31', '2020-06-09 21:56:31'),
(179, 1, 'user', NULL, '2020-06-09 21:56:31', '2020-06-09 21:56:31'),
(180, NULL, 'global', NULL, '2020-06-10 14:44:32', '2020-06-10 14:44:32'),
(181, NULL, 'ip', '41.223.117.74', '2020-06-10 14:44:32', '2020-06-10 14:44:32'),
(183, NULL, 'global', NULL, '2020-06-10 14:46:09', '2020-06-10 14:46:09'),
(184, NULL, 'ip', '41.223.117.74', '2020-06-10 14:46:09', '2020-06-10 14:46:09'),
(186, NULL, 'global', NULL, '2020-06-22 15:49:54', '2020-06-22 15:49:54'),
(187, NULL, 'ip', '41.223.117.74', '2020-06-22 15:49:54', '2020-06-22 15:49:54'),
(189, NULL, 'global', NULL, '2020-06-22 15:50:24', '2020-06-22 15:50:24'),
(190, NULL, 'ip', '41.223.117.74', '2020-06-22 15:50:24', '2020-06-22 15:50:24'),
(192, NULL, 'global', NULL, '2020-07-06 08:03:40', '2020-07-06 08:03:40'),
(193, NULL, 'ip', '102.148.251.198', '2020-07-06 08:03:40', '2020-07-06 08:03:40'),
(195, NULL, 'global', NULL, '2020-07-06 08:04:40', '2020-07-06 08:04:40'),
(196, NULL, 'ip', '102.148.251.198', '2020-07-06 08:04:40', '2020-07-06 08:04:40'),
(198, NULL, 'global', NULL, '2020-07-06 08:06:52', '2020-07-06 08:06:52'),
(199, NULL, 'ip', '102.148.251.198', '2020-07-06 08:06:52', '2020-07-06 08:06:52'),
(201, NULL, 'global', NULL, '2020-07-11 17:25:58', '2020-07-11 17:25:58'),
(202, NULL, 'ip', '41.223.117.73', '2020-07-11 17:25:58', '2020-07-11 17:25:58'),
(203, 1, 'user', NULL, '2020-07-11 17:25:58', '2020-07-11 17:25:58'),
(204, NULL, 'global', NULL, '2020-07-21 00:17:02', '2020-07-21 00:17:02'),
(205, NULL, 'ip', '102.149.78.105', '2020-07-21 00:17:02', '2020-07-21 00:17:02'),
(206, 1, 'user', NULL, '2020-07-21 00:17:02', '2020-07-21 00:17:02'),
(207, NULL, 'global', NULL, '2020-08-19 23:48:59', '2020-08-19 23:48:59'),
(208, NULL, 'ip', '102.145.13.110', '2020-08-19 23:48:59', '2020-08-19 23:48:59'),
(209, 1, 'user', NULL, '2020-08-19 23:48:59', '2020-08-19 23:48:59'),
(210, NULL, 'global', NULL, '2020-08-23 14:36:51', '2020-08-23 14:36:51'),
(211, NULL, 'ip', '102.148.218.14', '2020-08-23 14:36:51', '2020-08-23 14:36:51'),
(212, 1, 'user', NULL, '2020-08-23 14:36:51', '2020-08-23 14:36:51'),
(213, NULL, 'global', NULL, '2020-08-23 14:37:37', '2020-08-23 14:37:37'),
(214, NULL, 'ip', '102.148.218.14', '2020-08-23 14:37:37', '2020-08-23 14:37:37'),
(215, 1, 'user', NULL, '2020-08-23 14:37:37', '2020-08-23 14:37:37'),
(216, NULL, 'global', NULL, '2020-08-27 16:56:07', '2020-08-27 16:56:07'),
(217, NULL, 'ip', '102.146.198.60', '2020-08-27 16:56:07', '2020-08-27 16:56:07'),
(218, 1, 'user', NULL, '2020-08-27 16:56:07', '2020-08-27 16:56:07'),
(219, NULL, 'global', NULL, '2022-10-09 20:03:28', '2022-10-09 20:03:28'),
(220, NULL, 'ip', '45.215.255.7', '2022-10-09 20:03:28', '2022-10-09 20:03:28'),
(221, NULL, 'global', NULL, '2022-10-09 20:03:31', '2022-10-09 20:03:31'),
(222, NULL, 'ip', '45.215.255.7', '2022-10-09 20:03:31', '2022-10-09 20:03:31'),
(223, NULL, 'global', NULL, '2022-10-17 12:11:02', '2022-10-17 12:11:02'),
(224, NULL, 'ip', '165.56.184.140', '2022-10-17 12:11:02', '2022-10-17 12:11:02'),
(225, 26, 'user', NULL, '2022-10-17 12:11:02', '2022-10-17 12:11:02'),
(226, NULL, 'global', NULL, '2022-10-17 18:58:59', '2022-10-17 18:58:59'),
(227, NULL, 'ip', '165.56.184.140', '2022-10-17 18:58:59', '2022-10-17 18:58:59'),
(228, 26, 'user', NULL, '2022-10-17 18:58:59', '2022-10-17 18:58:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `office_id` int(11) DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permissions` text COLLATE utf8mb4_unicode_ci,
  `last_login` timestamp NULL DEFAULT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('male','female','other','unspecified') COLLATE utf8mb4_unicode_ci DEFAULT 'unspecified',
  `enable_google2fa` tinyint(4) NOT NULL DEFAULT '0',
  `blocked` tinyint(4) NOT NULL DEFAULT '0',
  `google2fa_secret` text COLLATE utf8mb4_unicode_ci,
  `address` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `time_limit` tinyint(4) NOT NULL DEFAULT '0',
  `from_time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_days` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `office_id`, `email`, `password`, `permissions`, `last_login`, `first_name`, `last_name`, `phone`, `gender`, `enable_google2fa`, `blocked`, `google2fa_secret`, `address`, `notes`, `time_limit`, `from_time`, `to_time`, `access_days`, `created_at`, `updated_at`) VALUES
(1, 1, 'smwale82@gmail.com', '$2y$10$Ak3Yq.VoK28fTL8nLaw3YOle1wL1m6bsM3ie6HgnEh71T3ZlVReIy', NULL, '2022-10-26 14:16:36', 'Admin', 'Admin', NULL, 'male', 0, 0, NULL, '<p>123<br></p>', '<p>123<br></p>', 0, NULL, NULL, '[]', '2019-03-12 07:34:14', '2022-10-26 14:16:36'),
(22, 1, 'evans.sakala@bedfordmicrofinance.com', '$2y$10$cSFEJuVMHIfL/0pKAlD7Jez1OY5v8vlyi24i3XPXfR95yyRg1LyF2', NULL, '2022-10-08 12:10:26', 'Evans', 'Sakala', NULL, 'male', 0, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2022-10-08 11:55:58', '2022-10-08 12:10:26'),
(23, 1, 'choolwe.nampindi@bedfordmicrofinance.com', '$2y$10$3LyBDX2HcmB/lRrCJ6FQKu8/0ddUvpUAg.llwJl8dfpJMYDbm29Lm', NULL, NULL, 'Choolwe', 'Nampindi', NULL, 'female', 0, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2022-10-08 12:18:58', '2022-10-19 15:09:32'),
(24, 1, 'kaluba.mulele@bedfordmicrofinance.com', '$2y$10$VD1b1RUVhZgjsrXt6Y6LBuRoOCZGrSnMl5B0sX9QL5N/48BjJBwdO', NULL, '2022-10-17 19:03:44', 'Kaluba', 'Mulele', NULL, 'female', 0, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2022-10-08 12:20:14', '2022-10-19 15:09:56'),
(25, 1, 'lhabazoka@yahoo.com', '$2y$10$Qak0IGQjP8yi0bYQZEH6XOI9pzJl4OpEde0js5gPfqegtW4LeTYca', NULL, '2022-10-25 20:46:16', 'Lubinda', 'Haabazoka', NULL, 'male', 0, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2022-10-08 12:21:26', '2022-10-25 20:46:16'),
(26, 1, 'maryhaba@icloud.com', '$2y$10$lNxUgvlzCwZoSMAaCVVZReGSBgC9kheTbzU7hMrX2c0S75JzDGpXW', NULL, '2022-10-25 17:53:42', 'Maria', 'Haabazoka', NULL, 'female', 0, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2022-10-08 12:22:52', '2022-10-25 17:53:42'),
(27, 1, 'nachilima4@gmail.com', '$2y$10$//GljjUtshDpQb9Ff4DiV.LRjenyZWaKCMaR5I79KIUGhIPriSsb.', NULL, NULL, 'Miriam', 'Nachilima', NULL, 'female', 0, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2022-10-08 12:25:51', '2022-10-08 12:25:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activations`
--
ALTER TABLE `activations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `asset_depreciation`
--
ALTER TABLE `asset_depreciation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `asset_types`
--
ALTER TABLE `asset_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_trail`
--
ALTER TABLE `audit_trail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `charges`
--
ALTER TABLE `charges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client_identifications`
--
ALTER TABLE `client_identifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client_identification_types`
--
ALTER TABLE `client_identification_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client_next_of_kin`
--
ALTER TABLE `client_next_of_kin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client_profession`
--
ALTER TABLE `client_profession`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client_relationships`
--
ALTER TABLE `client_relationships`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client_users`
--
ALTER TABLE `client_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `collateral`
--
ALTER TABLE `collateral`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `collateral_types`
--
ALTER TABLE `collateral_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `communication_campaigns`
--
ALTER TABLE `communication_campaigns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `custom_fields`
--
ALTER TABLE `custom_fields`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `custom_fields_meta`
--
ALTER TABLE `custom_fields_meta`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_budgets`
--
ALTER TABLE `expense_budgets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_types`
--
ALTER TABLE `expense_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `funds`
--
ALTER TABLE `funds`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gl_accounts`
--
ALTER TABLE `gl_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gl_closures`
--
ALTER TABLE `gl_closures`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gl_journal_entries`
--
ALTER TABLE `gl_journal_entries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_clients`
--
ALTER TABLE `group_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_loan_allocation`
--
ALTER TABLE `group_loan_allocation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_users`
--
ALTER TABLE `group_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guarantors`
--
ALTER TABLE `guarantors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_applications`
--
ALTER TABLE `loan_applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_charges`
--
ALTER TABLE `loan_charges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_products`
--
ALTER TABLE `loan_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_product_charges`
--
ALTER TABLE `loan_product_charges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_provisioning_criteria`
--
ALTER TABLE `loan_provisioning_criteria`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_purposes`
--
ALTER TABLE `loan_purposes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_repayment_schedules`
--
ALTER TABLE `loan_repayment_schedules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_reschedule_requests`
--
ALTER TABLE `loan_reschedule_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_transactions`
--
ALTER TABLE `loan_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_transaction_repayment_schedule_mappings`
--
ALTER TABLE `loan_transaction_repayment_schedule_mappings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offices`
--
ALTER TABLE `offices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `office_transactions`
--
ALTER TABLE `office_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `other_income`
--
ALTER TABLE `other_income`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `other_income_types`
--
ALTER TABLE `other_income_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_details`
--
ALTER TABLE `payment_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_types`
--
ALTER TABLE `payment_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_type_details`
--
ALTER TABLE `payment_type_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payroll_meta`
--
ALTER TABLE `payroll_meta`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payroll_templates`
--
ALTER TABLE `payroll_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payroll_template_meta`
--
ALTER TABLE `payroll_template_meta`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `persistences`
--
ALTER TABLE `persistences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `persistences_code_unique` (`code`);

--
-- Indexes for table `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `report_scheduler`
--
ALTER TABLE `report_scheduler`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `report_scheduler_run_history`
--
ALTER TABLE `report_scheduler_run_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indexes for table `role_users`
--
ALTER TABLE `role_users`
  ADD PRIMARY KEY (`user_id`,`role_id`);

--
-- Indexes for table `savings`
--
ALTER TABLE `savings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `savings_charges`
--
ALTER TABLE `savings_charges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `savings_products`
--
ALTER TABLE `savings_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `savings_product_charges`
--
ALTER TABLE `savings_product_charges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `savings_transactions`
--
ALTER TABLE `savings_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sms_gateways`
--
ALTER TABLE `sms_gateways`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `throttle`
--
ALTER TABLE `throttle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `throttle_user_id_index` (`user_id`);

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
-- AUTO_INCREMENT for table `activations`
--
ALTER TABLE `activations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `asset_depreciation`
--
ALTER TABLE `asset_depreciation`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_types`
--
ALTER TABLE `asset_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `audit_trail`
--
ALTER TABLE `audit_trail`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=281;

--
-- AUTO_INCREMENT for table `charges`
--
ALTER TABLE `charges`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `client_identifications`
--
ALTER TABLE `client_identifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `client_identification_types`
--
ALTER TABLE `client_identification_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `client_next_of_kin`
--
ALTER TABLE `client_next_of_kin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=180;

--
-- AUTO_INCREMENT for table `client_profession`
--
ALTER TABLE `client_profession`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `client_relationships`
--
ALTER TABLE `client_relationships`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `client_users`
--
ALTER TABLE `client_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `collateral`
--
ALTER TABLE `collateral`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `collateral_types`
--
ALTER TABLE `collateral_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `communication_campaigns`
--
ALTER TABLE `communication_campaigns`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `custom_fields`
--
ALTER TABLE `custom_fields`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `custom_fields_meta`
--
ALTER TABLE `custom_fields_meta`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=361;

--
-- AUTO_INCREMENT for table `expense_budgets`
--
ALTER TABLE `expense_budgets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_types`
--
ALTER TABLE `expense_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `funds`
--
ALTER TABLE `funds`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gl_accounts`
--
ALTER TABLE `gl_accounts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `gl_closures`
--
ALTER TABLE `gl_closures`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gl_journal_entries`
--
ALTER TABLE `gl_journal_entries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1776;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_clients`
--
ALTER TABLE `group_clients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_loan_allocation`
--
ALTER TABLE `group_loan_allocation`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_users`
--
ALTER TABLE `group_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guarantors`
--
ALTER TABLE `guarantors`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `loan_applications`
--
ALTER TABLE `loan_applications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_charges`
--
ALTER TABLE `loan_charges`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `loan_products`
--
ALTER TABLE `loan_products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `loan_product_charges`
--
ALTER TABLE `loan_product_charges`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `loan_provisioning_criteria`
--
ALTER TABLE `loan_provisioning_criteria`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `loan_purposes`
--
ALTER TABLE `loan_purposes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `loan_repayment_schedules`
--
ALTER TABLE `loan_repayment_schedules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1304;

--
-- AUTO_INCREMENT for table `loan_reschedule_requests`
--
ALTER TABLE `loan_reschedule_requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_transactions`
--
ALTER TABLE `loan_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT for table `loan_transaction_repayment_schedule_mappings`
--
ALTER TABLE `loan_transaction_repayment_schedule_mappings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `offices`
--
ALTER TABLE `offices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `office_transactions`
--
ALTER TABLE `office_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `other_income`
--
ALTER TABLE `other_income`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `other_income_types`
--
ALTER TABLE `other_income_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payment_details`
--
ALTER TABLE `payment_details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `payment_types`
--
ALTER TABLE `payment_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payment_type_details`
--
ALTER TABLE `payment_type_details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payroll_meta`
--
ALTER TABLE `payroll_meta`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payroll_templates`
--
ALTER TABLE `payroll_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payroll_template_meta`
--
ALTER TABLE `payroll_template_meta`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=339;

--
-- AUTO_INCREMENT for table `persistences`
--
ALTER TABLE `persistences`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=850;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `throttle`
--
ALTER TABLE `throttle`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=229;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
