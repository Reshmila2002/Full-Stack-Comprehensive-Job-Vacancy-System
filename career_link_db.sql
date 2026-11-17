-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 23, 2026 at 03:06 PM
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
-- Database: `career_link_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `seeker_id` int(11) NOT NULL,
  `cover_letter` text DEFAULT NULL,
  `cv_path` varchar(255) NOT NULL,
  `status` enum('applied','reviewed','rejected','hired') DEFAULT 'applied',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `job_id`, `seeker_id`, `cover_letter`, `cv_path`, `status`, `applied_at`) VALUES
(2, 72, 39, '6a38e30427690_cl_39.pdf', '6a38e3042767d_cv_39.pdf', 'applied', '2026-06-22 07:23:48'),
(3, 68, 44, '6a38ed9c6427f_cl_44.pdf', '6a38ed7dcfcdc_cv_44.pdf', 'applied', '2026-06-22 08:08:29');

-- --------------------------------------------------------

--
-- Table structure for table `company_profiles`
--

CREATE TABLE `company_profiles` (
  `id` int(11) NOT NULL,
  `employer_id` int(11) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `industry` varchar(100) NOT NULL,
  `bio` text NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','resolved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'sinari', 'sinari@gmail.com', 'hdh', 'gtsdu', 'pending', '2026-06-22 07:04:21'),
(2, 'ann', 'ann@gmail.com', 'Can\'t log into my account', 'cant find job', 'pending', '2026-06-22 08:11:34');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `employer_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `salary` varchar(100) NOT NULL,
  `job_type` enum('Full-Time','Part-Time','Internship','Contract') NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `employer_id`, `title`, `category`, `location`, `salary`, `job_type`, `description`, `status`, `created_at`) VALUES
(68, 1, 'Software Engineer', 'IT & Technology', 'Remote', '$85,000/year', 'Full-Time', 'Looking for a skilled Full Stack Developer proficient in React and Node.js.', 'approved', '2026-06-22 07:21:58'),
(69, 1, 'Data Analyst', 'Data Science', 'New York, NY', '$75,000/year', 'Full-Time', 'Analyze complex datasets to provide actionable business insights.', 'approved', '2026-06-22 07:21:58'),
(70, 1, 'Digital Marketing Specialist', 'Marketing', 'Remote', '$60,000/year', 'Full-Time', 'Manage SEO, SEM, and social media advertising campaigns.', 'approved', '2026-06-22 07:21:58'),
(71, 1, 'UI/UX Designer', 'Design', 'San Francisco, CA', '$90,000/year', 'Full-Time', 'Design user-centric interfaces and build interactive prototypes.', 'approved', '2026-06-22 07:21:58'),
(72, 1, 'Project Manager', 'Management', 'Chicago, IL', '$95,000/year', 'Full-Time', 'Lead cross-functional teams to deliver software projects on time.', 'approved', '2026-06-22 07:21:58'),
(73, 1, 'Content Writer', 'Writing & Editing', 'Remote', '$35/hour', 'Part-Time', 'Create engaging blog posts, articles, and website copy.', 'approved', '2026-06-22 07:21:58'),
(74, 1, 'Sales Development Representative', 'Sales', 'Austin, TX', '$55,000/year', 'Full-Time', 'Outbound prospecting and qualifying leads for the enterprise sales team.', 'approved', '2026-06-22 07:21:58'),
(75, 1, 'Customer Support Specialist', 'Customer Service', 'Remote', '$40,000/year', 'Full-Time', 'Provide technical support and help resolve customer issues via chat and email.', 'approved', '2026-06-22 07:21:58'),
(76, 1, 'DevOps Engineer', 'IT & Technology', 'Seattle, WA', '$110,000/year', 'Full-Time', 'Maintain CI/CD pipelines, AWS infrastructure, and system monitoring.', 'approved', '2026-06-22 07:21:58'),
(77, 1, 'Financial Analyst', 'Finance', 'Boston, MA', '$80,000/year', 'Full-Time', 'Prepare financial reports, budgeting models, and forecasting.', 'approved', '2026-06-22 07:21:58'),
(78, 1, 'HR Generalist', 'Human Resources', 'Denver, CO', '$65,000/year', 'Full-Time', 'Manage employee onboarding, relations, benefits, and compliance.', 'approved', '2026-06-22 07:21:58'),
(79, 1, 'Web Developer Intern', 'IT & Technology', 'Remote', '$25/hour', 'Internship', 'Gain hands-on experience building web applications with PHP and MySQL.', 'approved', '2026-06-22 07:21:58'),
(80, 1, 'QA Automation Engineer', 'IT & Technology', 'Los Angeles, CA', '$85,000/year', 'Full-Time', 'Write automated test scripts using Selenium and Python.', 'approved', '2026-06-22 07:21:58'),
(81, 1, 'Product Manager', 'Management', 'New York, NY', '$120,000/year', 'Full-Time', 'Define product vision, roadmap, and collaborate with engineering teams.', 'approved', '2026-06-22 07:21:58'),
(82, 1, 'Graphic Designer', 'Design', 'Remote', '$30/hour', 'Part-Time', 'Create visual assets for branding, social media, and print media.', 'approved', '2026-06-22 07:21:58'),
(83, 1, 'Cybersecurity Analyst', 'IT & Technology', 'Washington, DC', '$105,000/year', 'Full-Time', 'Monitor networks for security breaches and implement protective measures.', 'approved', '2026-06-22 07:21:58'),
(84, 1, 'SEO Specialist', 'Marketing', 'Remote', '$50/hour', 'Part-Time', 'Optimize website structure and content to improve organic search rankings.', 'approved', '2026-06-22 07:21:58'),
(85, 1, 'Social Media Manager', 'Marketing', 'Miami, FL', '$48,000/year', 'Full-Time', 'Curate and schedule daily content across TikTok, Instagram, and LinkedIn.', 'approved', '2026-06-22 07:21:58'),
(86, 1, 'IT Support Technician', 'IT & Technology', 'Phoenix, AZ', '$50,000/year', 'Full-Time', 'Troubleshoot hardware, software, and local networking issues for staff.', 'approved', '2026-06-22 07:21:58'),
(87, 1, 'Database Administrator', 'Data Science', 'Dallas, TX', '$90,000/year', 'Full-Time', 'Ensure database security, integrity, performance, and regular backups.', 'approved', '2026-06-22 07:21:58'),
(88, 43, 'Sinhala Typing', 'Document', 'Remote', '20000', 'Part-Time', 'Responsibilities:\r\n\r\nTranscribe audio/video content from Sinhala documentaries accurately.\r\n\r\nEnsure proper grammar, spelling, and punctuation in Sinhala.\r\n\r\nMeet daily/weekly transcription targets.\r\n\r\nMaintain confidentiality of all content.\r\n\r\nRequirements:\r\n\r\nFluent in Sinhala reading and writing.\r\n\r\nFast and accurate Sinhala typing skills (minimum 40 WPM).\r\n\r\nBasic computer knowledge.\r\n\r\nGood listening skills.\r\n\r\nPrevious transcription experience is a plus.\r\n\r\nBenefits:\r\n\r\n100% Remote Work – Work from anywhere.\r\n\r\nFlexible working hours.\r\n\r\nMonthly salary of Rs. 20,000.', 'approved', '2026-06-22 08:03:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('seeker','employer','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'John doe', 'johndoe@gmail.com', '$2y$10$dA6NTHkEAinHqF1N7euW4OnqI5CJrNVUEwlFUFh3O3Vs7l.eUF.q2', 'seeker', '2026-06-22 07:21:13'),
(29, 'MASS', 'mass@gmail.com', '$2y$10$p1SBZCl5Mun8RW5ikvKSTeTZ4bj3r5zt5lQ5mrJdHtKjX/WlKAwsO', 'employer', '2026-06-22 05:36:07'),
(30, 'Hemass', 'hemass@gmail.com', '$2y$10$MegOKkadBBwzMPlJVwnRceXmCOi0N.N0w9bSG4T1kC3jd4cU8Jy7e', 'employer', '2026-06-22 05:38:18'),
(33, 'max', 'max@gmail.com', '$2y$10$O.FyWjtY.ObDFeC1hOP3cuBwbu2NGLyyAc0lxcfXcVmG61ELHBT7i', 'employer', '2026-06-22 07:05:24'),
(37, 'dog', 'dog@gmail.com', '$2y$10$QgVw3yA8LYPpnhxdDMLlF.oU40e06enzbplZztZZawGvaOFo28x1i', 'employer', '2026-06-22 07:08:28'),
(38, 'tharushi', 'tharushi@gmail.com', '$2y$10$FB/AgiDBQQg9f/u9qsRLneBCxg2aUJLJTu4lzFBr/Oqdt5hPw1Pk6', 'admin', '2026-06-22 07:11:09'),
(39, 'john doe', 'johndoe1@gmail.com', '$2y$10$TTOTFXRMAMGfRQXZtbnK9uXqTJ.kExxZ05r7yB0RBQWFLH9ZLKrIa', 'seeker', '2026-06-22 07:17:29'),
(43, 'HEMASS', 'hemasss@gmail.com', '$2y$10$fgfaBvLS1e8vQQi0P1UV.O1OaqJ8LBKc3b.ZklP4z1m5UjsXQtcjC', 'employer', '2026-06-22 07:59:16'),
(44, 'John Alex', 'johnalex@gmail.com', '$2y$10$7MQqmOHIABYn28P7gXrOWefXC8BM3IXa1pQN7p8831gcCaEFH39Ku', 'seeker', '2026-06-22 08:06:48'),
(45, 'big', 'big@gmail.com', '$2y$10$KImnE7cuk8TBpGCqV5soeu02T3fztSQBAoq3sm2WH.tmVz.Wq8hA6', 'employer', '2026-06-22 08:12:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `seeker_id` (`seeker_id`);

--
-- Indexes for table `company_profiles`
--
ALTER TABLE `company_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employer_id` (`employer_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employer_id` (`employer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `company_profiles`
--
ALTER TABLE `company_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`seeker_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `company_profiles`
--
ALTER TABLE `company_profiles`
  ADD CONSTRAINT `company_profiles_ibfk_1` FOREIGN KEY (`employer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`employer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
