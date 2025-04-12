-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 11, 2025 at 06:03 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4
SET
  SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET
  time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;

/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;

/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;

/*!40101 SET NAMES utf8mb4 */
;

--
-- Database: `BIT301`
--
-- --------------------------------------------------------
--
-- Table structure for table `bookings`
--
CREATE TABLE `bookings` (
  `id` char(36) NOT NULL,
  `userId` char(36) DEFAULT NULL,
  `productId` char(36) DEFAULT NULL,
  `totalPrice` decimal(10, 2) DEFAULT NULL,
  `promoCode` varchar(50) DEFAULT NULL,
  `status` enum('CONFIRMED', 'CANCELLED', 'PENDING') DEFAULT NULL,
  `paymentIntentId` varchar(255) DEFAULT NULL,
  `createdAt` datetime DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `booking_seats`
--
CREATE TABLE `booking_seats` (
  `bookingId` char(36) NOT NULL,
  `seatId` char(36) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `customer`
--
CREATE TABLE `customer` (
  `id` varchar(36) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAT` timestamp NOT NULL DEFAULT current_timestamp(),
  `fullName` char(50) NOT NULL,
  `birthday` date NOT NULL,
  `phoneNumber` varchar(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `userId` varchar(36) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `document`
--
CREATE TABLE `document` (
  `id` varchar(36) NOT NULL,
  `fileName` varchar(255) NOT NULL,
  `fileUrl` varchar(9999) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `merchants`
--
CREATE TABLE `merchants` (
  `ID` varchar(36) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `merchantName` varchar(255) NOT NULL,
  `contactNumber` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `userId` varchar(36) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `product`
--
CREATE TABLE `product` (
  `ID` varchar(36) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `productCode` varchar(20) NOT NULL,
  `description` varchar(9999) NOT NULL,
  `imageUrl` varchar(1000) DEFAULT NULL,
  `merchantID` varchar(36) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `promo_codes`
--
CREATE TABLE `promo_codes` (
  `id` varchar(36) NOT NULL,
  `merchantId` varchar(36) NOT NULL,
  `productId` varchar(36) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_amount` decimal(10, 2) NOT NULL,
  `discount_type` enum('percentage', 'fixed') NOT NULL,
  `start_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `usage_limit` int(11) NOT NULL,
  `current_usage` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `seats`
--
CREATE TABLE `seats` (
  `id` char(36) NOT NULL,
  `eventId` char(36) DEFAULT NULL,
  `seatRow` varchar(10) DEFAULT NULL,
  `seatNumber` int(11) DEFAULT NULL,
  `isBooked` tinyint(1) DEFAULT 0,
  `ticketTypeId` char(36) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `ticket_types`
--
CREATE TABLE `ticket_types` (
  `id` char(36) NOT NULL,
  `eventId` char(36) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10, 2) DEFAULT NULL,
  `maxQuantity` int(11) DEFAULT NULL,
  `restrictions` text DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `user`
--
CREATE TABLE `user` (
  `id` varchar(36) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `userName` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `isFirstTimeLogin` tinyint(1) NOT NULL DEFAULT 0,
  `type` enum('CUSTOMER', 'MERCHANT', 'ADMIN', '') NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Indexes for dumped tables
--
--
-- Indexes for table `bookings`
--
ALTER TABLE
  `bookings`
ADD
  PRIMARY KEY (`id`),
ADD
  KEY `userId` (`userId`),
ADD
  KEY `productId` (`productId`);

--
-- Indexes for table `booking_seats`
--
ALTER TABLE
  `booking_seats`
ADD
  PRIMARY KEY (`bookingId`, `seatId`),
ADD
  KEY `seatId` (`seatId`);

--
-- Indexes for table `customer`
--
ALTER TABLE
  `customer`
ADD
  PRIMARY KEY (`id`);

--
-- Indexes for table `document`
--
ALTER TABLE
  `document`
ADD
  PRIMARY KEY (`id`);

--
-- Indexes for table `merchants`
--
ALTER TABLE
  `merchants`
ADD
  PRIMARY KEY (`ID`),
ADD
  KEY `FK_MerchantUser` (`userId`);

--
-- Indexes for table `product`
--
ALTER TABLE
  `product`
ADD
  PRIMARY KEY (`ID`),
ADD
  KEY `merchantID` (`merchantID`);

--
-- Indexes for table `promo_codes`
--
ALTER TABLE
  `promo_codes`
ADD
  PRIMARY KEY (`id`),
ADD
  UNIQUE KEY `code` (`code`),
ADD
  KEY `merchantId` (`merchantId`),
ADD
  KEY `productId` (`productId`);

--
-- Indexes for table `seats`
--
ALTER TABLE
  `seats`
ADD
  PRIMARY KEY (`id`);

--
-- Indexes for table `ticket_types`
--
ALTER TABLE
  `ticket_types`
ADD
  PRIMARY KEY (`id`),
ADD
  KEY `eventId` (`eventId`);

--
-- Indexes for table `user`
--
ALTER TABLE
  `user`
ADD
  PRIMARY KEY (`id`);

--
-- Constraints for dumped tables
--
--
-- Constraints for table `bookings`
--
ALTER TABLE
  `bookings`
ADD
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`id`),
ADD
  CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`productId`) REFERENCES `product` (`ID`);

--
-- Constraints for table `booking_seats`
--
ALTER TABLE
  `booking_seats`
ADD
  CONSTRAINT `booking_seats_ibfk_1` FOREIGN KEY (`bookingId`) REFERENCES `bookings` (`id`),
ADD
  CONSTRAINT `booking_seats_ibfk_2` FOREIGN KEY (`seatId`) REFERENCES `seats` (`id`);

--
-- Constraints for table `promo_codes`
--
ALTER TABLE
  `promo_codes`
ADD
  CONSTRAINT `promo_codes_ibfk_1` FOREIGN KEY (`merchantId`) REFERENCES `merchants` (`ID`),
ADD
  CONSTRAINT `promo_codes_ibfk_2` FOREIGN KEY (`productId`) REFERENCES `product` (`ID`);

--
-- Constraints for table `ticket_types`
--
ALTER TABLE
  `ticket_types`
ADD
  CONSTRAINT `ticket_types_ibfk_1` FOREIGN KEY (`eventId`) REFERENCES `product` (`ID`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;

/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;