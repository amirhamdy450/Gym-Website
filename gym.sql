-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 08, 2026 at 01:41 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gym`
--

-- --------------------------------------------------------

--
-- Table structure for table `groupprograms`
--

CREATE TABLE `groupprograms` (
  `id` int NOT NULL,
  `Title` varchar(100) NOT NULL,
  `Description` text,
  `Capacity` int NOT NULL DEFAULT '20',
  `Room` varchar(100) DEFAULT 'Main Studio',
  `Image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `groupprograms`
--

INSERT INTO `groupprograms` (`id`, `Title`, `Description`, `Capacity`, `Room`, `Image`) VALUES
(1, 'HIIT Cardio', NULL, 25, 'Studio A', NULL),
(2, 'Yoga Flow', NULL, 15, 'Studio B', NULL),
(3, 'CrossFit Challenge', NULL, 20, 'Main Hall', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Address` varchar(255) NOT NULL,
  `City` varchar(100) NOT NULL,
  `Phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `Name`, `Address`, `City`, `Phone`) VALUES
(1, 'Downtown Studio', '123 Fitness Blvd', 'Metro City', '555-0101'),
(2, 'Uptown Elite', '456 Heights Ave', 'Metro City', '555-0102');

-- --------------------------------------------------------

--
-- Table structure for table `memberships`
--

CREATE TABLE `memberships` (
  `id` int NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `DurationMonths` int NOT NULL DEFAULT '1',
  `Description` text,
  `FeaturesJson` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin
) ;

--
-- Dumping data for table `memberships`
--

INSERT INTO `memberships` (`id`, `Name`, `Price`, `DurationMonths`, `Description`, `FeaturesJson`) VALUES
(1, 'BRONZE', '800.00', 1, 'STARTER PLAN', '{\"Core\": [\"4 Guest Invites\", \"1 Body Checkup\", \"Gym Access\"], \"Premium\": [], \"Locked\": [\"Private Sessions\", \"Group Classes\", \"Spa Access\"]}'),
(2, 'SILVER', '1200.00', 1, 'MOST POPULAR', '{\"Core\": [\"8 Guest Invites\", \"3 Group Classes\", \"2 Body Checkups\"], \"Premium\": [\"2 Private Sessions\", \"Spa Access\"], \"Locked\": []}'),
(3, 'GOLD', '1650.00', 1, 'VIP EXPERIENCE', '{\"Core\": [\"16 Guest Invites\", \"5 Group Classes\", \"4 Body Checkups\"], \"Premium\": [\"4 Private Sessions\", \"Spa Access\", \"Priority Booking\"], \"Locked\": []}');

-- --------------------------------------------------------

--
-- Table structure for table `orderitems`
--

CREATE TABLE `orderitems` (
  `id` int NOT NULL,
  `OrderId` int NOT NULL,
  `ProductId` int NOT NULL,
  `Quantity` int NOT NULL,
  `PriceAtPurchase` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `UserId` int NOT NULL,
  `TotalAmount` decimal(10,2) NOT NULL,
  `Status` enum('Pending','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `OrderDate` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `privatesessions`
--

CREATE TABLE `privatesessions` (
  `id` int NOT NULL,
  `UserId` int NOT NULL,
  `InstructorId` int NOT NULL,
  `Category` varchar(100) DEFAULT 'General Training',
  `StartTime` datetime NOT NULL,
  `DurationMinutes` int NOT NULL DEFAULT '60',
  `Status` enum('Pending','Confirmed','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `Notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `privatesessions`
--

INSERT INTO `privatesessions` (`id`, `UserId`, `InstructorId`, `Category`, `StartTime`, `DurationMinutes`, `Status`, `Notes`) VALUES
(1, 7, 2, 'Power Lifting', '2026-02-09 09:00:00', 60, 'Confirmed', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `Name` varchar(150) NOT NULL,
  `Category` enum('Supplement','Equipment','Apparel') NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `StockLevel` int NOT NULL DEFAULT '0',
  `Description` text,
  `Image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `programbookings`
--

CREATE TABLE `programbookings` (
  `id` int NOT NULL,
  `UserId` int NOT NULL,
  `SessionId` int NOT NULL,
  `Status` enum('Confirmed','Cancelled','Waitlist') NOT NULL DEFAULT 'Confirmed',
  `BookedAt` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `programbookings`
--

INSERT INTO `programbookings` (`id`, `UserId`, `SessionId`, `Status`, `BookedAt`) VALUES
(1, 7, 1, 'Confirmed', '2026-02-08 03:36:19');

-- --------------------------------------------------------

--
-- Table structure for table `programenrollments`
--

CREATE TABLE `programenrollments` (
  `id` int NOT NULL,
  `UserId` int NOT NULL,
  `ProgramId` int NOT NULL,
  `EnrolledAt` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int NOT NULL,
  `Title` varchar(150) NOT NULL,
  `Description` text,
  `Price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `IsFree` tinyint(1) NOT NULL DEFAULT '0',
  `DurationDays` int DEFAULT '30',
  `Image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `programsessions`
--

CREATE TABLE `programsessions` (
  `id` int NOT NULL,
  `ProgramId` int NOT NULL,
  `InstructorId` int DEFAULT NULL,
  `StartTime` datetime NOT NULL,
  `DurationMinutes` int NOT NULL DEFAULT '60'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `programsessions`
--

INSERT INTO `programsessions` (`id`, `ProgramId`, `InstructorId`, `StartTime`, `DurationMinutes`) VALUES
(1, 1, 3, '2026-02-10 18:00:00', 45),
(2, 2, 2, '2026-02-11 07:00:00', 60);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `Key` varchar(50) NOT NULL,
  `Value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `specialoffers`
--

CREATE TABLE `specialoffers` (
  `id` int NOT NULL,
  `Title` varchar(100) NOT NULL,
  `Description` text,
  `DiscountPercentage` int DEFAULT '0',
  `IsActive` tinyint(1) DEFAULT '1',
  `Image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `specialoffers`
--

INSERT INTO `specialoffers` (`id`, `Title`, `Description`, `DiscountPercentage`, `IsActive`, `Image`) VALUES
(1, 'Limited Time Offer', 'Join today and get 50% OFF your first month.', 50, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int NOT NULL,
  `UserId` int NOT NULL,
  `MembershipId` int NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  `Status` enum('Active','Expired','Cancelled') NOT NULL DEFAULT 'Active',
  `PaymentId` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tourbookings`
--

CREATE TABLE `tourbookings` (
  `id` int NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `PhoneNumber` varchar(20) NOT NULL,
  `LocationId` int NOT NULL,
  `TourDate` date NOT NULL,
  `SlotId` int NOT NULL,
  `Status` enum('Scheduled','Cancelled','Completed') NOT NULL DEFAULT 'Scheduled',
  `CreatedAt` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tourbookings`
--

INSERT INTO `tourbookings` (`id`, `FullName`, `Email`, `PhoneNumber`, `LocationId`, `TourDate`, `SlotId`, `Status`, `CreatedAt`) VALUES
(1, 'Kaye Ferrell', 'qokegupedu@mailinator.com', '+1 (806) 124-4964', 1, '2026-01-24', 2, 'Scheduled', '2026-01-22 02:12:38'),
(2, 'Kaye Ferrell', 'qokegupedu@mailinator.com', '+1 (806) 124-4964', 1, '2026-01-24', 2, 'Scheduled', '2026-01-22 02:12:48'),
(4, 'Ori Richard', 'wury@mailinator.com', '+1 (651) 691-1626', 1, '2026-01-24', 2, 'Scheduled', '2026-01-22 02:18:20'),
(5, 'Dieter Lawrence', 'qanygi@mailinator.com', '+1 (487) 217-2612', 1, '2026-01-24', 2, 'Scheduled', '2026-01-22 02:23:25'),
(6, 'Fallon Sargent', 'hevycuvyq@mailinator.com', '+1 (578) 443-6935', 1, '2026-02-10', 5, 'Scheduled', '2026-02-07 03:16:58');

-- --------------------------------------------------------

--
-- Table structure for table `tourtimeslots`
--

CREATE TABLE `tourtimeslots` (
  `id` int NOT NULL,
  `SlotTime` time NOT NULL,
  `IsActive` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tourtimeslots`
--

INSERT INTO `tourtimeslots` (`id`, `SlotTime`, `IsActive`) VALUES
(1, '09:00:00', 1),
(2, '11:00:00', 1),
(3, '13:00:00', 1),
(4, '15:00:00', 1),
(5, '17:30:00', 1),
(6, '19:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `userphysicalstats`
--

CREATE TABLE `userphysicalstats` (
  `id` int NOT NULL,
  `UserId` int NOT NULL,
  `Weight` decimal(5,2) NOT NULL,
  `BodyFat` decimal(4,1) NOT NULL,
  `RecordedAt` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `userphysicalstats`
--

INSERT INTO `userphysicalstats` (`id`, `UserId`, `Weight`, `BodyFat`, `RecordedAt`) VALUES
(5, 7, '80.50', '15.7', '2025-11-08 03:36:19'),
(6, 7, '79.20', '15.0', '2025-12-08 03:36:19'),
(7, 7, '77.00', '13.5', '2026-01-08 03:36:19'),
(8, 7, '75.00', '12.0', '2026-02-08 03:36:19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `Gender` enum('Male','Female') DEFAULT NULL,
  `DateOfBirth` date DEFAULT NULL,
  `Height` decimal(5,2) DEFAULT NULL,
  `Weight` decimal(5,2) DEFAULT NULL,
  `FitnessGoals` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `Role` enum('Admin','Instructor','Trainee') NOT NULL DEFAULT 'Trainee',
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `ProfileImage` varchar(255) DEFAULT NULL,
  `CreatedAt` datetime DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `FirstName`, `LastName`, `Email`, `PasswordHash`, `Gender`, `DateOfBirth`, `Height`, `Weight`, `FitnessGoals`, `Role`, `PhoneNumber`, `ProfileImage`, `CreatedAt`) VALUES
(2, '', '', 'firstname@epixgym.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, NULL, NULL, 'Instructor', NULL, NULL, '2026-01-19 19:40:49'),
(3, '', '', 'assd@epixgym.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, NULL, NULL, 'Instructor', NULL, NULL, '2026-01-19 19:40:49'),
(4, 'Melanie', 'Richard', 'guzyfo@mailinator.com', '$2y$10$3Tvme1mPVVjLcW0Y3FCNGO2eNmXPaJ/V2E.6WCX6jxlUqjyxq0fh6', 'Male', '1972-03-07', '118.00', '52.00', '[\"Build Muscle\"]', 'Trainee', NULL, NULL, '2026-01-21 02:55:03'),
(5, 'Kalia', 'Perkins', 'pufin@mailinator.com', '$2y$10$Sokd08.EZLIFs9rjtvZyQ.kp4zFKkiQSQLKgK8p2DCunct1tUya5O', 'Male', '2012-05-27', '202.00', '240.00', '[\"Lose Weight\"]', 'Trainee', NULL, NULL, '2026-01-21 02:57:56'),
(6, 'Edan', 'Schroeder', 'vucagipe@mailinator.com', '$2y$10$Efp2nk53lcf3GPbv5r4speAWsccxhnnXIhyDeHZyCmlDk5bCtkZ0i', 'Male', '2024-05-02', '107.00', '255.00', '[\"Build Muscle\"]', 'Trainee', NULL, NULL, '2026-01-21 03:21:58'),
(7, 'amir', 'amgad', 'amirhamdy450@gmail.com', '$2y$10$R9Frs17rs7llkxS46t0AO.Az3AFUCbTKil2QWWYiYZe0lJkSFAObu', 'Male', '2000-10-01', '160.00', '55.00', '[\"Build Muscle\",\"Endurance\",\"Strength\"]', 'Trainee', NULL, NULL, '2026-01-21 03:32:38'),
(8, 'Larissa', 'Meadows', 'lojasijy@mailinator.com', '$2y$10$Tt8XgGsZpJW/pOal0iGrWuz6xph2WMILWQomL35Lc3KD5DB90b6B.', 'Male', '2009-06-08', '160.00', '60.00', '[\"Build Muscle\",\"Flexibility\"]', 'Trainee', NULL, NULL, '2026-01-21 16:30:50');

-- --------------------------------------------------------

--
-- Table structure for table `_legacy_temp_instructor`
--

CREATE TABLE `_legacy_temp_instructor` (
  `id` int NOT NULL,
  `Name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `_legacy_temp_instructor`
--

INSERT INTO `_legacy_temp_instructor` (`id`, `Name`) VALUES
(1, 'first name'),
(2, 'as sd');

-- --------------------------------------------------------

--
-- Table structure for table `_legacy_temp_trainee`
--

CREATE TABLE `_legacy_temp_trainee` (
  `id` int NOT NULL,
  `Name` varchar(200) NOT NULL,
  `Email` varchar(200) NOT NULL,
  `DateJoined` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `_legacy_temp_trainee`
--

INSERT INTO `_legacy_temp_trainee` (`id`, `Name`, `Email`, `DateJoined`) VALUES
(2, 'Amir Amgad', 'amirhamdy450@gmail.com', '2024-10-09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `groupprograms`
--
ALTER TABLE `groupprograms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `memberships`
--
ALTER TABLE `memberships`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD PRIMARY KEY (`id`),
  ADD KEY `OrderId` (`OrderId`),
  ADD KEY `ProductId` (`ProductId`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UserId` (`UserId`);

--
-- Indexes for table `privatesessions`
--
ALTER TABLE `privatesessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `InstructorId` (`InstructorId`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programbookings`
--
ALTER TABLE `programbookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `SessionId` (`SessionId`);

--
-- Indexes for table `programenrollments`
--
ALTER TABLE `programenrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `ProgramId` (`ProgramId`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programsessions`
--
ALTER TABLE `programsessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ProgramId` (`ProgramId`),
  ADD KEY `InstructorId` (`InstructorId`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Key` (`Key`);

--
-- Indexes for table `specialoffers`
--
ALTER TABLE `specialoffers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `MembershipId` (`MembershipId`);

--
-- Indexes for table `tourbookings`
--
ALTER TABLE `tourbookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `LocationId` (`LocationId`),
  ADD KEY `SlotId` (`SlotId`);

--
-- Indexes for table `tourtimeslots`
--
ALTER TABLE `tourtimeslots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `userphysicalstats`
--
ALTER TABLE `userphysicalstats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UserId` (`UserId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `groupprograms`
--
ALTER TABLE `groupprograms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `memberships`
--
ALTER TABLE `memberships`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orderitems`
--
ALTER TABLE `orderitems`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `privatesessions`
--
ALTER TABLE `privatesessions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `programbookings`
--
ALTER TABLE `programbookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `programenrollments`
--
ALTER TABLE `programenrollments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `programsessions`
--
ALTER TABLE `programsessions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `specialoffers`
--
ALTER TABLE `specialoffers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tourbookings`
--
ALTER TABLE `tourbookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tourtimeslots`
--
ALTER TABLE `tourtimeslots`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `userphysicalstats`
--
ALTER TABLE `userphysicalstats`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD CONSTRAINT `orderitems_ibfk_1` FOREIGN KEY (`OrderId`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orderitems_ibfk_2` FOREIGN KEY (`ProductId`) REFERENCES `products` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`id`);

--
-- Constraints for table `privatesessions`
--
ALTER TABLE `privatesessions`
  ADD CONSTRAINT `privatesessions_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `privatesessions_ibfk_2` FOREIGN KEY (`InstructorId`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `programbookings`
--
ALTER TABLE `programbookings`
  ADD CONSTRAINT `programbookings_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `programbookings_ibfk_2` FOREIGN KEY (`SessionId`) REFERENCES `programsessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `programenrollments`
--
ALTER TABLE `programenrollments`
  ADD CONSTRAINT `programenrollments_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `programenrollments_ibfk_2` FOREIGN KEY (`ProgramId`) REFERENCES `programs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `programsessions`
--
ALTER TABLE `programsessions`
  ADD CONSTRAINT `programsessions_ibfk_1` FOREIGN KEY (`ProgramId`) REFERENCES `groupprograms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `programsessions_ibfk_2` FOREIGN KEY (`InstructorId`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subscriptions_ibfk_2` FOREIGN KEY (`MembershipId`) REFERENCES `memberships` (`id`);

--
-- Constraints for table `tourbookings`
--
ALTER TABLE `tourbookings`
  ADD CONSTRAINT `tourbookings_ibfk_1` FOREIGN KEY (`LocationId`) REFERENCES `locations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tourbookings_ibfk_2` FOREIGN KEY (`SlotId`) REFERENCES `tourtimeslots` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `userphysicalstats`
--
ALTER TABLE `userphysicalstats`
  ADD CONSTRAINT `userphysicalstats_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
