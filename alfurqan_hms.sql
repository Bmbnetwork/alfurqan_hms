-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2026 at 02:55 PM
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
-- Database: `alfurqan_hms`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `username`, `action`, `description`, `ip_address`, `created_at`) VALUES
(1, 4, 'Hamza', 'LOGIN', 'User logged in successfully', '::1', '2026-06-16 10:38:53'),
(2, 4, 'Hamza', 'LOGOUT', 'DOCTOR user logged out successfully', '::1', '2026-06-16 10:39:52'),
(3, 1, 'admin', 'LOGIN', 'User logged in successfully', '::1', '2026-06-16 10:40:16'),
(4, 1, 'admin', 'LOGOUT', 'ADMIN user logged out successfully', '::1', '2026-06-16 10:41:25'),
(5, 1, 'admin', 'LOGIN', 'User logged in successfully', '::1', '2026-06-16 10:47:48'),
(6, 1, 'admin', 'LOGOUT', 'ADMIN user logged out successfully', '::1', '2026-06-16 10:48:27'),
(7, NULL, 'aisha@email.com', 'LOGOUT', 'Patient logged out successfully', '::1', '2026-06-16 11:05:05'),
(8, NULL, 'aisha@email.com', 'LOGOUT', 'Patient logged out successfully', '::1', '2026-06-16 13:18:14'),
(9, 4, 'Hamza', 'LOGIN', 'User logged in successfully', '::1', '2026-06-16 13:18:34'),
(10, 4, 'Hamza', 'LOGOUT', 'DOCTOR user logged out successfully', '::1', '2026-06-16 13:18:47'),
(11, 1, 'admin', 'LOGIN', 'User logged in successfully', '::1', '2026-06-16 13:19:03'),
(12, 1, 'admin', 'LOGOUT', 'ADMIN user logged out successfully', '::1', '2026-06-16 13:19:28'),
(13, 4, 'Hamza', 'LOGIN', 'User logged in successfully', '::1', '2026-06-16 13:19:50'),
(14, 4, 'Hamza', 'ADD_CONSULTATION', 'Consultation for patient ID: 1', '::1', '2026-06-16 13:20:58'),
(15, 4, 'Hamza', 'REQUEST_LAB_TEST', 'Lab test requested for patient ID: 1', '::1', '2026-06-16 13:21:35'),
(16, NULL, 'aisha@email.com', 'LOGOUT', 'Patient logged out successfully', '::1', '2026-06-17 09:10:39'),
(17, 1, 'admin', 'LOGIN', 'User logged in successfully', '::1', '2026-06-17 09:11:14'),
(18, 1, 'admin', 'LOGOUT', 'ADMIN user logged out successfully', '::1', '2026-06-17 09:11:24'),
(19, 4, 'Hamza', 'LOGIN', 'User logged in successfully', '::1', '2026-06-17 09:11:51'),
(20, 4, 'Hamza', 'LOGOUT', 'DOCTOR user logged out successfully', '::1', '2026-06-17 09:20:00'),
(21, NULL, 'aisha@email.com', 'LOGOUT', 'Patient logged out successfully', '::1', '2026-06-17 09:20:38'),
(22, 7, 'lab_technician', 'LOGIN', 'User logged in successfully', '::1', '2026-06-17 09:21:00'),
(23, 7, 'lab_technician', 'RECORD_LAB_RESULT', 'Recorded result for request ID: 1', '::1', '2026-06-17 09:21:53'),
(24, 7, 'lab_technician', 'LOGOUT', 'LAB_TECHNICIAN user logged out successfully', '::1', '2026-06-17 09:24:51'),
(25, 6, 'pharmacist', 'LOGIN', 'User logged in successfully', '::1', '2026-06-17 09:25:08'),
(26, 6, 'pharmacist', 'DISPENSE_DRUG', 'Dispensed Paracetamol 500mg for Rx #1', '::1', '2026-06-17 09:25:22'),
(27, 6, 'pharmacist', 'LOGOUT', 'PHARMACIST user logged out successfully', '::1', '2026-06-17 09:25:47'),
(28, 5, 'nurse', 'LOGIN', 'User logged in successfully', '::1', '2026-06-17 09:26:01'),
(29, 5, 'nurse', 'LOGOUT', 'NURSE user logged out successfully', '::1', '2026-06-17 09:26:38'),
(30, 1, 'admin', 'LOGIN', 'User logged in successfully', '::1', '2026-06-17 09:27:05'),
(31, 1, 'admin', 'ADD_BILL', 'Created bill of ₦250 for patient ID: 1', '::1', '2026-06-17 09:27:50'),
(32, 1, 'admin', 'EDIT_PATIENT', 'Updated patient: Fatima Abdullahi', '::1', '2026-06-17 09:28:50'),
(33, 1, 'admin', 'EDIT_PATIENT', 'Updated patient: Aisha Mohammed', '::1', '2026-06-17 09:29:07'),
(34, 1, 'admin', 'EDIT_PATIENT', 'Updated patient: Ibrahim Suleiman', '::1', '2026-06-17 09:29:40'),
(35, 1, 'admin', 'LOGOUT', 'ADMIN user logged out successfully', '::1', '2026-06-17 09:30:01'),
(36, 1, 'admin', 'LOGIN', 'User logged in successfully', '::1', '2026-06-17 14:18:19'),
(37, 1, 'admin', 'LOGOUT', 'ADMIN user logged out successfully', '::1', '2026-06-18 12:44:47'),
(38, 4, 'Hamza', 'LOGIN', 'User logged in successfully', '::1', '2026-06-18 12:49:41'),
(39, 4, 'Hamza', 'ADD_CONSULTATION', 'Consultation for patient ID: 1', '::1', '2026-06-18 12:50:35'),
(40, 4, 'Hamza', 'LOGOUT', 'DOCTOR user logged out successfully', '::1', '2026-06-18 12:54:13');

-- --------------------------------------------------------

--
-- Table structure for table `ai_insights`
--

CREATE TABLE `ai_insights` (
  `id` int(11) NOT NULL,
  `insight_type` enum('Disease Trend','Risk Alert','Department Load','Drug Interaction','Forecast','Executive Summary') NOT NULL,
  `insight_title` varchar(255) NOT NULL,
  `insight_content` text NOT NULL,
  `recommendation` text DEFAULT NULL,
  `confidence_level` decimal(5,2) DEFAULT 0.00,
  `severity` enum('Low','Medium','High','Critical') DEFAULT 'Medium',
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_questions`
--

CREATE TABLE `ai_questions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `intent` varchar(100) DEFAULT NULL,
  `entities` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ai_questions`
--

INSERT INTO `ai_questions` (`id`, `user_id`, `question`, `intent`, `entities`, `created_at`) VALUES
(1, 1, 'Which disease is increasing the fastest?', 'fastest_growing_disease', '{\"period\":\"month\"}', '2026-06-17 14:27:31'),
(2, 1, 'Which department is overloaded?', 'department_overload', '{\"period\":\"month\"}', '2026-06-17 14:27:33'),
(3, 1, 'What is the most common disease this month?', 'most_common_disease', '{\"period\":\"month\"}', '2026-06-17 14:27:47');

-- --------------------------------------------------------

--
-- Table structure for table `ai_responses`
--

CREATE TABLE `ai_responses` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `response` text NOT NULL,
  `statistics` text DEFAULT NULL,
  `recommendation` text DEFAULT NULL,
  `confidence` decimal(5,2) DEFAULT NULL,
  `response_time_ms` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ai_responses`
--

INSERT INTO `ai_responses` (`id`, `question_id`, `response`, `statistics`, `recommendation`, `confidence`, `response_time_ms`, `created_at`) VALUES
(1, 2, 'I can help you analyze hospital data. Try asking about disease trends, patient risks, department performance, or drug interactions.', '{}', 'Use specific questions like \'What is the most common disease this month?\' for detailed insights.', 70.00, 138, '2026-06-17 14:27:34'),
(2, 3, 'Tramadol is the most common disease this month with 1 confirmed cases, representing 100.00% of all diagnoses.', '[{\"name\":\"Tramadol\",\"cases\":\"1\",\"percentage\":\"100.00\"}]', 'Ensure adequate stock of Tramadol medications and prepare additional staff for treatment.', 92.00, 99, '2026-06-17 14:27:47');

-- --------------------------------------------------------

--
-- Table structure for table `analytics_logs`
--

CREATE TABLE `analytics_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `antenatal`
--

CREATE TABLE `antenatal` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `gestation_age_weeks` int(11) DEFAULT NULL,
  `blood_pressure` varchar(10) DEFAULT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `visit_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('Pending','Confirmed','Rescheduled','Cancelled','Completed','No-Show') DEFAULT 'Pending',
  `notes` text DEFAULT NULL,
  `confirmed_by` int(11) DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `appointment_time`, `reason`, `status`, `notes`, `confirmed_by`, `confirmed_at`, `created_at`) VALUES
(1, 1, 4, '2026-06-18', '10:00:00', 'Malaria', 'Confirmed', '', 1, '2026-06-16 13:19:21', '2026-06-16 13:18:07');

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('Paid','Pending') DEFAULT 'Pending',
  `bill_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`id`, `patient_id`, `amount`, `description`, `status`, `bill_date`, `created_by`) VALUES
(1, 1, 250.00, 'bought drugs', 'Paid', '2026-06-17 09:27:49', 1);

-- --------------------------------------------------------

--
-- Table structure for table `consultations`
--

CREATE TABLE `consultations` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `symptoms` text DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `prescription` text DEFAULT NULL,
  `visit_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consultations`
--

INSERT INTO `consultations` (`id`, `patient_id`, `doctor_id`, `symptoms`, `diagnosis`, `prescription`, `visit_date`) VALUES
(1, 1, 4, 'Malaria', 'Tramadol', 'Tramadol', '2026-06-16 13:20:58'),
(2, 1, 4, 'Malaria', 'Malaria', 'Tramadol', '2026-06-18 12:50:34');

-- --------------------------------------------------------

--
-- Table structure for table `diseases`
--

CREATE TABLE `diseases` (
  `id` int(11) NOT NULL,
  `disease_name` varchar(150) NOT NULL,
  `disease_category` varchar(50) DEFAULT NULL,
  `severity_level` enum('Low','Medium','High','Critical') DEFAULT 'Medium',
  `recommended_department` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `diseases`
--

INSERT INTO `diseases` (`id`, `disease_name`, `disease_category`, `severity_level`, `recommended_department`, `description`, `created_at`) VALUES
(1, 'Malaria', 'Infectious Disease', 'High', 'General Outpatient', 'Mosquito-borne infectious disease causing fever and flu-like symptoms', '2026-06-16 12:53:52'),
(2, 'Typhoid Fever', 'Infectious Disease', 'High', 'General Outpatient', 'Bacterial infection causing high fever and gastrointestinal symptoms', '2026-06-16 12:53:52'),
(3, 'Dengue Fever', 'Infectious Disease', 'High', 'General Outpatient', 'Mosquito-borne viral infection causing severe flu-like symptoms', '2026-06-16 12:53:52'),
(4, 'Common Cold', 'Respiratory', 'Low', 'General Outpatient', 'Viral infection of the upper respiratory tract', '2026-06-16 12:53:52'),
(5, 'Influenza', 'Respiratory', 'Medium', 'General Outpatient', 'Viral infection causing fever, body aches, and respiratory symptoms', '2026-06-16 12:53:52'),
(6, 'Gastroenteritis', 'Gastrointestinal', 'Medium', 'General Outpatient', 'Inflammation of stomach and intestines causing diarrhea and vomiting', '2026-06-16 12:53:52'),
(7, 'Urinary Tract Infection', 'Urological', 'Medium', 'Urology', 'Bacterial infection of the urinary system', '2026-06-16 12:53:52'),
(8, 'Pneumonia', 'Respiratory', 'High', 'Pulmonology', 'Infection causing inflammation in the lungs', '2026-06-16 12:53:52'),
(9, 'Tuberculosis', 'Infectious Disease', 'Critical', 'Pulmonology', 'Bacterial infection primarily affecting the lungs', '2026-06-16 12:53:52'),
(10, 'Hypertension', 'Cardiovascular', 'Medium', 'Cardiology', 'High blood pressure condition', '2026-06-16 12:53:52'),
(11, 'Diabetes Mellitus', 'Endocrine', 'Medium', 'Endocrinology', 'Metabolic disease causing high blood sugar', '2026-06-16 12:53:52'),
(12, 'Asthma', 'Respiratory', 'Medium', 'Pulmonology', 'Chronic respiratory condition causing breathing difficulties', '2026-06-16 12:53:52');

-- --------------------------------------------------------

--
-- Table structure for table `disease_symptom_mapping`
--

CREATE TABLE `disease_symptom_mapping` (
  `id` int(11) NOT NULL,
  `disease_id` int(11) NOT NULL,
  `symptom_id` int(11) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `weight` decimal(3,2) DEFAULT 1.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `disease_symptom_mapping`
--

INSERT INTO `disease_symptom_mapping` (`id`, `disease_id`, `symptom_id`, `is_primary`, `weight`) VALUES
(1, 1, 1, 1, 1.50),
(2, 1, 8, 1, 1.20),
(3, 1, 10, 1, 1.30),
(4, 1, 9, 0, 0.80),
(5, 1, 17, 0, 0.90),
(6, 2, 1, 1, 1.50),
(7, 2, 11, 1, 1.50),
(8, 2, 6, 0, 1.10),
(9, 2, 9, 0, 0.80),
(10, 2, 17, 0, 0.90),
(11, 3, 1, 1, 1.50),
(12, 3, 2, 1, 1.20),
(13, 3, 12, 1, 1.10),
(14, 3, 13, 1, 1.30),
(15, 3, 8, 0, 1.00),
(16, 4, 6, 1, 1.10),
(17, 4, 7, 1, 0.90),
(18, 4, 1, 0, 0.80),
(19, 4, 9, 0, 0.80),
(20, 5, 1, 1, 1.50),
(21, 5, 8, 1, 1.00),
(22, 5, 6, 1, 1.10),
(23, 5, 9, 1, 0.80),
(24, 5, 2, 0, 1.20);

-- --------------------------------------------------------

--
-- Table structure for table `doctor_overrides`
--

CREATE TABLE `doctor_overrides` (
  `id` int(11) NOT NULL,
  `warning_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `override_reason` text NOT NULL,
  `override_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `drugs`
--

CREATE TABLE `drugs` (
  `id` int(11) NOT NULL,
  `drug_name` varchar(150) NOT NULL,
  `generic_name` varchar(150) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `dosage_form` varchar(50) DEFAULT NULL,
  `strength` varchar(50) DEFAULT NULL,
  `quantity_in_stock` int(11) DEFAULT 0,
  `reorder_level` int(11) DEFAULT 10,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `supplier` varchar(100) DEFAULT NULL,
  `batch_number` varchar(50) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `added_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drugs`
--

INSERT INTO `drugs` (`id`, `drug_name`, `generic_name`, `category`, `dosage_form`, `strength`, `quantity_in_stock`, `reorder_level`, `unit_price`, `supplier`, `batch_number`, `expiry_date`, `date_added`, `added_by`) VALUES
(1, 'Paracetamol 500mg', 'Acetaminophen', 'Analgesics', 'Tablet', '500mg', 495, 10, 50.00, NULL, NULL, NULL, '2026-06-16 10:19:31', NULL),
(2, 'Amoxicillin 500mg', 'Amoxicillin', 'Antibiotics', 'Capsule', '500mg', 300, 10, 150.00, NULL, NULL, NULL, '2026-06-16 10:19:31', NULL),
(3, 'Coartem', 'Artemether', 'Antimalarials', 'Tablet', '80mg', 200, 10, 500.00, NULL, NULL, NULL, '2026-06-16 10:19:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `drug_allergies`
--

CREATE TABLE `drug_allergies` (
  `id` int(11) NOT NULL,
  `drug_id` int(11) NOT NULL,
  `allergy_name` varchar(100) NOT NULL,
  `severity` enum('Mild','Moderate','Severe') DEFAULT 'Moderate'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drug_allergies`
--

INSERT INTO `drug_allergies` (`id`, `drug_id`, `allergy_name`, `severity`) VALUES
(1, 1, 'Penicillin', 'Severe'),
(2, 2, 'Sulfa', 'Moderate'),
(3, 3, 'Aspirin', 'Mild');

-- --------------------------------------------------------

--
-- Table structure for table `drug_interactions`
--

CREATE TABLE `drug_interactions` (
  `id` int(11) NOT NULL,
  `drug_id_1` int(11) NOT NULL,
  `drug_id_2` int(11) NOT NULL,
  `interaction_severity` enum('Mild','Moderate','Severe','Contraindicated') NOT NULL,
  `interaction_description` text DEFAULT NULL,
  `recommendation` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drug_interactions`
--

INSERT INTO `drug_interactions` (`id`, `drug_id_1`, `drug_id_2`, `interaction_severity`, `interaction_description`, `recommendation`) VALUES
(1, 1, 2, 'Moderate', 'Increased risk of bleeding', 'Monitor closely or use alternative'),
(2, 2, 3, 'Severe', 'Reduced effectiveness of both drugs', 'Avoid concurrent use'),
(3, 1, 3, 'Mild', 'Minor interaction, usually safe', 'Monitor patient response');

-- --------------------------------------------------------

--
-- Table structure for table `drug_sales`
--

CREATE TABLE `drug_sales` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `prescription_id` int(11) DEFAULT NULL,
  `drug_id` int(11) NOT NULL,
  `quantity_dispensed` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `pharmacist_id` int(11) DEFAULT NULL,
  `sale_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Dispensed','Pending','Cancelled') DEFAULT 'Dispensed',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drug_sales`
--

INSERT INTO `drug_sales` (`id`, `patient_id`, `prescription_id`, `drug_id`, `quantity_dispensed`, `unit_price`, `total_amount`, `pharmacist_id`, `sale_date`, `status`, `notes`) VALUES
(1, NULL, 1, 1, 5, 50.00, 250.00, 6, '2026-06-17 09:25:20', 'Dispensed', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `executive_summaries`
--

CREATE TABLE `executive_summaries` (
  `id` int(11) NOT NULL,
  `summary_date` date NOT NULL,
  `total_patients` int(11) DEFAULT NULL,
  `most_common_disease` varchar(150) DEFAULT NULL,
  `fastest_growing_disease` varchar(150) DEFAULT NULL,
  `critical_patients` int(11) DEFAULT NULL,
  `drug_interactions_prevented` int(11) DEFAULT NULL,
  `ai_recommendation` text DEFAULT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `executive_summaries`
--

INSERT INTO `executive_summaries` (`id`, `summary_date`, `total_patients`, `most_common_disease`, `fastest_growing_disease`, `critical_patients`, `drug_interactions_prevented`, `ai_recommendation`, `generated_at`) VALUES
(1, '2026-06-17', 3, 'Tramadol', '0', 0, 0, 'Based on current data, Tramadol is the primary health concern. Consider increasing resources for this condition and monitoring the growth of N/A.', '2026-06-17 14:24:42');

-- --------------------------------------------------------

--
-- Table structure for table `hospital_forecasts`
--

CREATE TABLE `hospital_forecasts` (
  `id` int(11) NOT NULL,
  `forecast_type` enum('Patient Volume','Disease Outbreak','Admission Rate','Drug Demand') NOT NULL,
  `forecast_date` date NOT NULL,
  `predicted_value` decimal(10,2) DEFAULT NULL,
  `actual_value` decimal(10,2) DEFAULT NULL,
  `confidence` decimal(5,2) DEFAULT NULL,
  `factors` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospital_forecasts`
--

INSERT INTO `hospital_forecasts` (`id`, `forecast_type`, `forecast_date`, `predicted_value`, `actual_value`, `confidence`, `factors`, `created_at`) VALUES
(1, 'Patient Volume', '2026-06-18', 1.00, NULL, 83.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:24:41'),
(2, 'Patient Volume', '2026-06-19', 1.00, NULL, 81.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:24:41'),
(3, 'Patient Volume', '2026-06-20', 1.00, NULL, 79.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:24:41'),
(4, 'Patient Volume', '2026-06-21', 1.00, NULL, 77.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:24:41'),
(5, 'Patient Volume', '2026-06-22', 1.00, NULL, 75.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:24:41'),
(6, 'Patient Volume', '2026-06-23', 1.00, NULL, 73.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:24:41'),
(7, 'Patient Volume', '2026-06-24', 1.00, NULL, 71.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:24:42'),
(8, 'Patient Volume', '2026-06-18', 1.00, NULL, 83.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:27:06'),
(9, 'Patient Volume', '2026-06-19', 1.00, NULL, 81.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:27:06'),
(10, 'Patient Volume', '2026-06-20', 1.00, NULL, 79.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:27:06'),
(11, 'Patient Volume', '2026-06-21', 1.00, NULL, 77.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:27:06'),
(12, 'Patient Volume', '2026-06-22', 1.00, NULL, 75.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:27:06'),
(13, 'Patient Volume', '2026-06-23', 1.00, NULL, 73.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:27:06'),
(14, 'Patient Volume', '2026-06-24', 1.00, NULL, 71.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:27:06'),
(15, 'Patient Volume', '2026-06-18', 1.00, NULL, 83.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:27:54'),
(16, 'Patient Volume', '2026-06-19', 1.00, NULL, 81.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:27:54'),
(17, 'Patient Volume', '2026-06-20', 1.00, NULL, 79.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:27:54'),
(18, 'Patient Volume', '2026-06-21', 1.00, NULL, 77.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:27:55'),
(19, 'Patient Volume', '2026-06-22', 1.00, NULL, 75.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:27:55'),
(20, 'Patient Volume', '2026-06-23', 1.00, NULL, 73.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:27:55'),
(21, 'Patient Volume', '2026-06-24', 1.00, NULL, 71.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:27:55'),
(22, 'Patient Volume', '2026-06-18', 1.00, NULL, 83.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:37:53'),
(23, 'Patient Volume', '2026-06-19', 1.00, NULL, 81.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:37:53'),
(24, 'Patient Volume', '2026-06-20', 1.00, NULL, 79.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:37:53'),
(25, 'Patient Volume', '2026-06-21', 1.00, NULL, 77.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:37:53'),
(26, 'Patient Volume', '2026-06-22', 1.00, NULL, 75.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:37:53'),
(27, 'Patient Volume', '2026-06-23', 1.00, NULL, 73.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:37:53'),
(28, 'Patient Volume', '2026-06-24', 1.00, NULL, 71.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:37:53'),
(29, 'Patient Volume', '2026-06-18', 1.00, NULL, 83.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:39:52'),
(30, 'Patient Volume', '2026-06-19', 1.00, NULL, 81.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:39:52'),
(31, 'Patient Volume', '2026-06-20', 1.00, NULL, 79.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:39:52'),
(32, 'Patient Volume', '2026-06-21', 1.00, NULL, 77.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:39:53'),
(33, 'Patient Volume', '2026-06-22', 1.00, NULL, 75.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:39:53'),
(34, 'Patient Volume', '2026-06-23', 1.00, NULL, 73.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:39:53'),
(35, 'Patient Volume', '2026-06-24', 1.00, NULL, 71.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:39:53'),
(36, 'Patient Volume', '2026-06-18', 1.00, NULL, 83.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:39:53'),
(37, 'Patient Volume', '2026-06-19', 1.00, NULL, 81.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:39:53'),
(38, 'Patient Volume', '2026-06-20', 1.00, NULL, 79.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:39:53'),
(39, 'Patient Volume', '2026-06-21', 1.00, NULL, 77.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:39:54'),
(40, 'Patient Volume', '2026-06-22', 1.00, NULL, 75.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:39:54'),
(41, 'Patient Volume', '2026-06-23', 1.00, NULL, 73.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:39:54'),
(42, 'Patient Volume', '2026-06-24', 1.00, NULL, 71.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:39:54'),
(43, 'Patient Volume', '2026-06-25', 1.00, NULL, 69.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:39:54'),
(44, 'Patient Volume', '2026-06-26', 1.00, NULL, 67.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:39:54'),
(45, 'Patient Volume', '2026-06-27', 1.00, NULL, 65.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:39:55'),
(46, 'Patient Volume', '2026-06-28', 1.00, NULL, 63.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:39:55'),
(47, 'Patient Volume', '2026-06-29', 1.00, NULL, 61.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:39:55'),
(48, 'Patient Volume', '2026-06-30', 1.00, NULL, 59.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:39:55'),
(49, 'Patient Volume', '2026-07-01', 1.00, NULL, 57.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:39:55'),
(50, 'Patient Volume', '2026-06-18', 1.00, NULL, 83.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:46:03'),
(51, 'Patient Volume', '2026-06-19', 1.00, NULL, 81.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:46:03'),
(52, 'Patient Volume', '2026-06-20', 1.00, NULL, 79.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:46:03'),
(53, 'Patient Volume', '2026-06-21', 1.00, NULL, 77.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:46:03'),
(54, 'Patient Volume', '2026-06-22', 1.00, NULL, 75.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:46:03'),
(55, 'Patient Volume', '2026-06-23', 1.00, NULL, 73.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:46:03'),
(56, 'Patient Volume', '2026-06-24', 1.00, NULL, 71.00, 'Based on 30-day historical average with 17% growth trend', '2026-06-17 14:46:03');

-- --------------------------------------------------------

--
-- Table structure for table `lab_requests`
--

CREATE TABLE `lab_requests` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `priority` enum('Routine','Urgent','Emergency') DEFAULT 'Routine',
  `clinical_notes` text DEFAULT NULL,
  `status` enum('Pending','In Progress','Completed','Cancelled') DEFAULT 'Pending',
  `collected_by` int(11) DEFAULT NULL,
  `collection_time` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_requests`
--

INSERT INTO `lab_requests` (`id`, `patient_id`, `doctor_id`, `test_id`, `request_date`, `priority`, `clinical_notes`, `status`, `collected_by`, `collection_time`) VALUES
(1, 1, 4, 2, '2026-06-16 13:21:35', 'Urgent', 'check and analyse and give me results', 'Completed', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lab_results`
--

CREATE TABLE `lab_results` (
  `id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `result_value` text DEFAULT NULL,
  `result_unit` varchar(50) DEFAULT NULL,
  `reference_min` varchar(50) DEFAULT NULL,
  `reference_max` varchar(50) DEFAULT NULL,
  `result_status` enum('Normal','Abnormal','Critical') DEFAULT 'Normal',
  `technician_id` int(11) DEFAULT NULL,
  `result_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_results`
--

INSERT INTO `lab_results` (`id`, `request_id`, `result_value`, `result_unit`, `reference_min`, `reference_max`, `result_status`, `technician_id`, `result_date`, `notes`) VALUES
(1, 1, 'High', '1.23', '12.0', '16.0', 'Abnormal', 7, '2026-06-17 09:21:53', 'Malaria & Typhod is high');

-- --------------------------------------------------------

--
-- Table structure for table `lab_tests`
--

CREATE TABLE `lab_tests` (
  `id` int(11) NOT NULL,
  `test_name` varchar(150) NOT NULL,
  `test_code` varchar(20) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `sample_type` varchar(50) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `reference_range` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_tests`
--

INSERT INTO `lab_tests` (`id`, `test_name`, `test_code`, `category`, `sample_type`, `unit_price`, `reference_range`, `is_active`, `created_at`) VALUES
(1, 'Full Blood Count', 'FBC', 'Hematology', 'Blood', 5000.00, NULL, 1, '2026-06-16 10:19:31'),
(2, 'Malaria Parasite', 'MP', 'Microbiology', 'Blood', 3000.00, NULL, 1, '2026-06-16 10:19:31'),
(3, 'Urine Microscopy', 'UM', 'Urinalysis', 'Urine', 2500.00, NULL, 1, '2026-06-16 10:19:31');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL,
  `patient_status` enum('Stable','Critical','Recovering','Discharged','Pending') DEFAULT 'Pending',
  `reg_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `name`, `age`, `gender`, `phone`, `address`, `allergies`, `medical_conditions`, `patient_status`, `reg_date`) VALUES
(1, 'Aisha Mohammed', 28, 'Female', '08012345678', 'Bauchi', NULL, NULL, 'Recovering', '2026-06-16 10:19:31'),
(2, 'Ibrahim Suleiman', 45, 'Male', '08023456789', 'Bauchi', NULL, NULL, 'Stable', '2026-06-16 10:19:31'),
(3, 'Fatima Abdullahi', 32, 'Female', '08034567890', 'Bauchi', NULL, NULL, 'Stable', '2026-06-16 10:19:31');

-- --------------------------------------------------------

--
-- Table structure for table `patient_assessments`
--

CREATE TABLE `patient_assessments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `assessment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `symptoms_text` text DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `current_medications` text DEFAULT NULL,
  `predicted_diseases` text DEFAULT NULL,
  `confidence_scores` text DEFAULT NULL,
  `risk_level` enum('Low','Medium','High','Critical') DEFAULT NULL,
  `recommended_department` varchar(100) DEFAULT NULL,
  `priority_status` enum('Normal','Urgent','Critical') DEFAULT 'Normal',
  `doctor_reviewed` tinyint(1) DEFAULT 0,
  `doctor_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_assessments`
--

INSERT INTO `patient_assessments` (`id`, `patient_id`, `assessment_date`, `symptoms_text`, `age`, `gender`, `weight_kg`, `medical_conditions`, `allergies`, `current_medications`, `predicted_diseases`, `confidence_scores`, `risk_level`, `recommended_department`, `priority_status`, `doctor_reviewed`, `doctor_notes`) VALUES
(1, 1, '2026-06-17 08:58:41', 'Diarrhea, Fever, Headache', 28, 'Female', 73.00, 'malaria', 'peanuts', 'tramadol', '[\"Dengue Fever\",\"Influenza\",\"Malaria\",\"Typhoid Fever\",\"Common Cold\"]', '[48,40,24,24,22.5]', 'High', 'General Outpatient', 'Urgent', 0, NULL),
(2, 1, '2026-06-17 09:09:32', 'Diarrhea, Fever, Headache', 28, 'Female', 73.00, 'malaria', 'peanuts', 'tramadol', '[\"Dengue Fever\",\"Influenza\",\"Malaria\",\"Typhoid Fever\",\"Common Cold\"]', '[48,40,24,24,22.5]', 'High', 'General Outpatient', 'Urgent', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `patient_users`
--

CREATE TABLE `patient_users` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_users`
--

INSERT INTO `patient_users` (`id`, `patient_id`, `email`, `password`, `phone`, `created_at`) VALUES
(1, 1, 'aisha@email.com', '$2y$10$yobYwtWAd.IJi7lTCcAENesdZ4Wo2mxHBrYEeo.RazLR27mGLVSfC', '08012345678', '2026-06-16 10:54:25'),
(2, 2, 'ibrahim@email.com', '$2y$10$yobYwtWAd.IJi7lTCcAENesdZ4Wo2mxHBrYEeo.RazLR27mGLVSfC', '08023456789', '2026-06-16 10:54:25'),
(3, 3, 'fatima@email.com', '$2y$10$yobYwtWAd.IJi7lTCcAENesdZ4Wo2mxHBrYEeo.RazLR27mGLVSfC', '08034567890', '2026-06-16 10:54:25');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `consultation_id` int(11) DEFAULT NULL,
  `drug_id` int(11) DEFAULT NULL,
  `drug_name` varchar(150) DEFAULT NULL,
  `dosage` varchar(100) DEFAULT NULL,
  `frequency` varchar(100) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `prescribed_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Dispensed','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `patient_id`, `doctor_id`, `consultation_id`, `drug_id`, `drug_name`, `dosage`, `frequency`, `duration`, `quantity`, `instructions`, `prescribed_date`, `status`) VALUES
(2, 1, 4, NULL, 1, 'Paracetamol 500mg', '200mg', 'twice', '7 days', 20, '', '2026-06-18 12:51:28', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `prescription_warnings`
--

CREATE TABLE `prescription_warnings` (
  `id` int(11) NOT NULL,
  `prescription_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `warning_type` enum('Drug Interaction','Allergy','Duplicate','Age Restriction','Disease Conflict','Dosage Warning') NOT NULL,
  `severity` enum('Low','Medium','High','Critical') NOT NULL,
  `warning_message` text DEFAULT NULL,
  `recommendation` text DEFAULT NULL,
  `is_overridden` tinyint(1) DEFAULT 0,
  `override_reason` text DEFAULT NULL,
  `overridden_by` int(11) DEFAULT NULL,
  `override_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `symptoms`
--

CREATE TABLE `symptoms` (
  `id` int(11) NOT NULL,
  `symptom_name` varchar(150) NOT NULL,
  `symptom_category` varchar(50) DEFAULT NULL,
  `severity_weight` decimal(3,2) DEFAULT 1.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `symptoms`
--

INSERT INTO `symptoms` (`id`, `symptom_name`, `symptom_category`, `severity_weight`, `created_at`) VALUES
(1, 'Fever', 'General', 1.50, '2026-06-16 12:53:51'),
(2, 'Headache', 'Neurological', 1.20, '2026-06-16 12:53:51'),
(3, 'Nausea', 'Gastrointestinal', 1.00, '2026-06-16 12:53:51'),
(4, 'Vomiting', 'Gastrointestinal', 1.30, '2026-06-16 12:53:51'),
(5, 'Diarrhea', 'Gastrointestinal', 1.40, '2026-06-16 12:53:51'),
(6, 'Cough', 'Respiratory', 1.10, '2026-06-16 12:53:51'),
(7, 'Sore Throat', 'Respiratory', 0.90, '2026-06-16 12:53:51'),
(8, 'Body Aches', 'General', 1.00, '2026-06-16 12:53:51'),
(9, 'Fatigue', 'General', 0.80, '2026-06-16 12:53:51'),
(10, 'Chills', 'General', 1.20, '2026-06-16 12:53:51'),
(11, 'Abdominal Pain', 'Gastrointestinal', 1.50, '2026-06-16 12:53:51'),
(12, 'Joint Pain', 'Musculoskeletal', 1.10, '2026-06-16 12:53:51'),
(13, 'Rash', 'Dermatological', 1.30, '2026-06-16 12:53:51'),
(14, 'Difficulty Breathing', 'Respiratory', 2.00, '2026-06-16 12:53:51'),
(15, 'Chest Pain', 'Cardiovascular', 2.50, '2026-06-16 12:53:51'),
(16, 'Dizziness', 'Neurological', 1.40, '2026-06-16 12:53:51'),
(17, 'Loss of Appetite', 'General', 0.90, '2026-06-16 12:53:51'),
(18, 'Weight Loss', 'General', 1.20, '2026-06-16 12:53:51'),
(19, 'Night Sweats', 'General', 1.30, '2026-06-16 12:53:51'),
(20, 'Swollen Lymph Nodes', 'Immune', 1.40, '2026-06-16 12:53:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','doctor','nurse','pharmacist','lab_technician') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$uIpqyCrldEIQg1Taeaw2re5h.ZR8qNbG8rApq81MK.fGmrKBZ0F7O', 'admin', '2026-06-16 10:19:31'),
(2, 'Bilal', '$2y$10$IoeKr45w0qPGPGpCuGTraO0iv/FTgcmAs256PAGO2fjp0nP9sjOQa', 'admin', '2026-06-16 10:19:31'),
(3, 'doctor', '$2y$10$Ap1VeCmlyNESGel2yhq6b.dYO9gkk7O8fAjQqa9nNL1ti1JdosgIG', 'doctor', '2026-06-16 10:19:31'),
(4, 'Hamza', '$2y$10$36cT/L8xcjvBzajVfIxvT.MhfhJia0m16AizXOMhf9yjiuG1W6n6e', 'doctor', '2026-06-16 10:19:31'),
(5, 'nurse', '$2y$10$ZmJgc4uR68i.mwn/ipaLcuNIDVMNabHpdq3/qPW2tLbe41RRmHYRW', 'nurse', '2026-06-16 10:19:31'),
(6, 'pharmacist', '$2y$10$jtF3ic1gBhZ1Fq4eex96AuEnHGrMqS347qsItMZfQq9hXdw.RXU16', 'pharmacist', '2026-06-16 10:19:31'),
(7, 'lab_technician', '$2y$10$NnRCO79HMDsy/LGbGqBkaO9wy0Zu3wa7rELSvaMyafsAppW0hUVIG', 'lab_technician', '2026-06-16 10:19:31');

-- --------------------------------------------------------

--
-- Table structure for table `vitals`
--

CREATE TABLE `vitals` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `nurse_id` int(11) NOT NULL,
  `temperature` decimal(4,2) DEFAULT NULL,
  `blood_pressure` varchar(10) DEFAULT NULL,
  `pulse_rate` int(11) DEFAULT NULL,
  `respiratory_rate` int(11) DEFAULT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `height_cm` decimal(5,2) DEFAULT NULL,
  `oxygen_saturation` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ai_insights`
--
ALTER TABLE `ai_insights`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type` (`insight_type`),
  ADD KEY `idx_severity` (`severity`),
  ADD KEY `idx_date` (`generated_at`);

--
-- Indexes for table `ai_questions`
--
ALTER TABLE `ai_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_intent` (`intent`);

--
-- Indexes for table `ai_responses`
--
ALTER TABLE `ai_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_question` (`question_id`);

--
-- Indexes for table `analytics_logs`
--
ALTER TABLE `analytics_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_action` (`action`);

--
-- Indexes for table `antenatal`
--
ALTER TABLE `antenatal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `confirmed_by` (`confirmed_by`);

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `consultations`
--
ALTER TABLE `consultations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `diseases`
--
ALTER TABLE `diseases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `disease_name` (`disease_name`),
  ADD KEY `idx_category` (`disease_category`),
  ADD KEY `idx_severity` (`severity_level`);

--
-- Indexes for table `disease_symptom_mapping`
--
ALTER TABLE `disease_symptom_mapping`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_disease_symptom` (`disease_id`,`symptom_id`),
  ADD KEY `idx_disease` (`disease_id`),
  ADD KEY `idx_symptom` (`symptom_id`);

--
-- Indexes for table `doctor_overrides`
--
ALTER TABLE `doctor_overrides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_warning` (`warning_id`),
  ADD KEY `idx_doctor` (`doctor_id`);

--
-- Indexes for table `drugs`
--
ALTER TABLE `drugs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added_by` (`added_by`);

--
-- Indexes for table `drug_allergies`
--
ALTER TABLE `drug_allergies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_drug` (`drug_id`);

--
-- Indexes for table `drug_interactions`
--
ALTER TABLE `drug_interactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_interaction` (`drug_id_1`,`drug_id_2`),
  ADD KEY `idx_drug_1` (`drug_id_1`),
  ADD KEY `idx_drug_2` (`drug_id_2`);

--
-- Indexes for table `drug_sales`
--
ALTER TABLE `drug_sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `drug_id` (`drug_id`),
  ADD KEY `pharmacist_id` (`pharmacist_id`);

--
-- Indexes for table `executive_summaries`
--
ALTER TABLE `executive_summaries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `summary_date` (`summary_date`),
  ADD KEY `idx_date` (`summary_date`);

--
-- Indexes for table `hospital_forecasts`
--
ALTER TABLE `hospital_forecasts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type` (`forecast_type`),
  ADD KEY `idx_date` (`forecast_date`);

--
-- Indexes for table `lab_requests`
--
ALTER TABLE `lab_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `test_id` (`test_id`),
  ADD KEY `collected_by` (`collected_by`);

--
-- Indexes for table `lab_results`
--
ALTER TABLE `lab_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `technician_id` (`technician_id`);

--
-- Indexes for table `lab_tests`
--
ALTER TABLE `lab_tests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `test_code` (`test_code`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient_assessments`
--
ALTER TABLE `patient_assessments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_patient` (`patient_id`),
  ADD KEY `idx_date` (`assessment_date`),
  ADD KEY `idx_risk` (`risk_level`);

--
-- Indexes for table `patient_users`
--
ALTER TABLE `patient_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `drug_id` (`drug_id`),
  ADD KEY `consultation_id` (`consultation_id`);

--
-- Indexes for table `prescription_warnings`
--
ALTER TABLE `prescription_warnings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `overridden_by` (`overridden_by`),
  ADD KEY `idx_prescription` (`prescription_id`),
  ADD KEY `idx_patient` (`patient_id`),
  ADD KEY `idx_severity` (`severity`);

--
-- Indexes for table `symptoms`
--
ALTER TABLE `symptoms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `symptom_name` (`symptom_name`),
  ADD KEY `idx_category` (`symptom_category`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `vitals`
--
ALTER TABLE `vitals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `nurse_id` (`nurse_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `ai_insights`
--
ALTER TABLE `ai_insights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_questions`
--
ALTER TABLE `ai_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ai_responses`
--
ALTER TABLE `ai_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `analytics_logs`
--
ALTER TABLE `analytics_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `antenatal`
--
ALTER TABLE `antenatal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `consultations`
--
ALTER TABLE `consultations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `diseases`
--
ALTER TABLE `diseases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `disease_symptom_mapping`
--
ALTER TABLE `disease_symptom_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `doctor_overrides`
--
ALTER TABLE `doctor_overrides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `drugs`
--
ALTER TABLE `drugs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `drug_allergies`
--
ALTER TABLE `drug_allergies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `drug_interactions`
--
ALTER TABLE `drug_interactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `drug_sales`
--
ALTER TABLE `drug_sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `executive_summaries`
--
ALTER TABLE `executive_summaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hospital_forecasts`
--
ALTER TABLE `hospital_forecasts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `lab_requests`
--
ALTER TABLE `lab_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lab_results`
--
ALTER TABLE `lab_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lab_tests`
--
ALTER TABLE `lab_tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `patient_assessments`
--
ALTER TABLE `patient_assessments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `patient_users`
--
ALTER TABLE `patient_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `prescription_warnings`
--
ALTER TABLE `prescription_warnings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `symptoms`
--
ALTER TABLE `symptoms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `vitals`
--
ALTER TABLE `vitals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ai_questions`
--
ALTER TABLE `ai_questions`
  ADD CONSTRAINT `ai_questions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ai_responses`
--
ALTER TABLE `ai_responses`
  ADD CONSTRAINT `ai_responses_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `ai_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `analytics_logs`
--
ALTER TABLE `analytics_logs`
  ADD CONSTRAINT `analytics_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `antenatal`
--
ALTER TABLE `antenatal`
  ADD CONSTRAINT `antenatal_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`confirmed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `billing`
--
ALTER TABLE `billing`
  ADD CONSTRAINT `billing_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `billing_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `consultations`
--
ALTER TABLE `consultations`
  ADD CONSTRAINT `consultations_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consultations_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `disease_symptom_mapping`
--
ALTER TABLE `disease_symptom_mapping`
  ADD CONSTRAINT `disease_symptom_mapping_ibfk_1` FOREIGN KEY (`disease_id`) REFERENCES `diseases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `disease_symptom_mapping_ibfk_2` FOREIGN KEY (`symptom_id`) REFERENCES `symptoms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_overrides`
--
ALTER TABLE `doctor_overrides`
  ADD CONSTRAINT `doctor_overrides_ibfk_1` FOREIGN KEY (`warning_id`) REFERENCES `prescription_warnings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `doctor_overrides_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `drugs`
--
ALTER TABLE `drugs`
  ADD CONSTRAINT `drugs_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `drug_allergies`
--
ALTER TABLE `drug_allergies`
  ADD CONSTRAINT `drug_allergies_ibfk_1` FOREIGN KEY (`drug_id`) REFERENCES `drugs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `drug_interactions`
--
ALTER TABLE `drug_interactions`
  ADD CONSTRAINT `drug_interactions_ibfk_1` FOREIGN KEY (`drug_id_1`) REFERENCES `drugs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `drug_interactions_ibfk_2` FOREIGN KEY (`drug_id_2`) REFERENCES `drugs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `drug_sales`
--
ALTER TABLE `drug_sales`
  ADD CONSTRAINT `drug_sales_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `drug_sales_ibfk_2` FOREIGN KEY (`drug_id`) REFERENCES `drugs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `drug_sales_ibfk_3` FOREIGN KEY (`pharmacist_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lab_requests`
--
ALTER TABLE `lab_requests`
  ADD CONSTRAINT `lab_requests_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_requests_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_requests_ibfk_3` FOREIGN KEY (`test_id`) REFERENCES `lab_tests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_requests_ibfk_4` FOREIGN KEY (`collected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lab_results`
--
ALTER TABLE `lab_results`
  ADD CONSTRAINT `lab_results_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `lab_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_results_ibfk_2` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `patient_assessments`
--
ALTER TABLE `patient_assessments`
  ADD CONSTRAINT `patient_assessments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_users`
--
ALTER TABLE `patient_users`
  ADD CONSTRAINT `patient_users_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescriptions_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescriptions_ibfk_3` FOREIGN KEY (`drug_id`) REFERENCES `drugs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `prescriptions_ibfk_4` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `prescription_warnings`
--
ALTER TABLE `prescription_warnings`
  ADD CONSTRAINT `prescription_warnings_ibfk_1` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescription_warnings_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescription_warnings_ibfk_3` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescription_warnings_ibfk_4` FOREIGN KEY (`overridden_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `vitals`
--
ALTER TABLE `vitals`
  ADD CONSTRAINT `vitals_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vitals_ibfk_2` FOREIGN KEY (`nurse_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
