-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 21, 2026 at 12:39 AM
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
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `Key` varchar(50) NOT NULL,
  `Value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `Role` enum('Admin','Instructor','Trainee') NOT NULL DEFAULT 'Trainee',
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `ProfileImage` varchar(255) DEFAULT NULL,
  `CreatedAt` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `FullName`, `Email`, `PasswordHash`, `Role`, `PhoneNumber`, `ProfileImage`, `CreatedAt`) VALUES
(1, 'Amir Amgad', 'amirhamdy450@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trainee', NULL, NULL, '2024-10-09 00:00:00'),
(2, 'first name', 'firstname@epixgym.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Instructor', NULL, NULL, '2026-01-19 19:40:49'),
(3, 'as sd', 'assd@epixgym.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Instructor', NULL, NULL, '2026-01-19 19:40:49');

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

-- --------------------------------------------------------
-- TOUR BOOKING SYSTEM TABLES
-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Address` varchar(255) NOT NULL,
  `City` varchar(100) NOT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `locations`
--
INSERT INTO `locations` (`Name`, `Address`, `City`, `Phone`) VALUES
('Downtown Studio', '123 Fitness Blvd', 'Metro City', '555-0101'),
('Uptown Elite', '456 Heights Ave', 'Metro City', '555-0102');

-- --------------------------------------------------------

--
-- Table structure for table `tourtimeslots`
--

CREATE TABLE `tourtimeslots` (
  `id` int NOT NULL AUTO_INCREMENT,
  `SlotTime` time NOT NULL,
  `IsActive` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tourtimeslots`
--
INSERT INTO `tourtimeslots` (`SlotTime`) VALUES
('09:00:00'), ('11:00:00'), ('13:00:00'), ('15:00:00'), ('17:30:00'), ('19:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `specialoffers`
--

CREATE TABLE `specialoffers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Title` varchar(100) NOT NULL,
  `Description` text,
  `DiscountPercentage` int DEFAULT 0,
  `IsActive` tinyint(1) DEFAULT 1,
  `Image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `specialoffers`
--
INSERT INTO `specialoffers` (`Title`, `Description`, `DiscountPercentage`) VALUES
('Limited Time Offer', 'Join today and get 50% OFF your first month.', 50);

-- --------------------------------------------------------

--
-- Table structure for table `tourbookings`
--

CREATE TABLE `tourbookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `FullName` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `PhoneNumber` varchar(20) NOT NULL,
  `LocationId` int NOT NULL,
  `TourDate` date NOT NULL,
  `SlotId` int NOT NULL,
  `Status` enum('Scheduled','Cancelled','Completed') NOT NULL DEFAULT 'Scheduled',
  `CreatedAt` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `LocationId` (`LocationId`),
  KEY `SlotId` (`SlotId`),
  CONSTRAINT `tourbookings_ibfk_1` FOREIGN KEY (`LocationId`) REFERENCES `locations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tourbookings_ibfk_2` FOREIGN KEY (`SlotId`) REFERENCES `tourtimeslots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

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
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Key` (`Key`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `MembershipId` (`MembershipId`);

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
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- Constraints for table `programenrollments`
--
ALTER TABLE `programenrollments`
  ADD CONSTRAINT `programenrollments_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `programenrollments_ibfk_2` FOREIGN KEY (`ProgramId`) REFERENCES `programs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subscriptions_ibfk_2` FOREIGN KEY (`MembershipId`) REFERENCES `memberships` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
