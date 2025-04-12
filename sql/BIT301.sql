-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 12, 2025 at 06:18 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `BIT301`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id`, `createdAt`, `updatedAT`, `fullName`, `birthday`, `phoneNumber`, `email`, `userId`) VALUES
('1485788c-726b-11ee-9678-00ff02f405b2', '2023-10-24 12:44:32', '2023-10-24 12:44:32', 'Gerald Lee Jia', '2001-11-14', '0186689133', 'geraldlee168168@gmail.com', '11ffb377-726b-11ee-9678-00ff02f405b2');

-- --------------------------------------------------------

--
-- Table structure for table `document`
--

CREATE TABLE `document` (
  `id` varchar(36) NOT NULL,
  `fileName` varchar(255) NOT NULL,
  `fileUrl` varchar(9999) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document`
--

INSERT INTO `document` (`id`, `fileName`, `fileUrl`) VALUES
('017c8850-729b-47ae-9b41-0a195d828b01', 'Confirmation_for_Booking_ID_#_1085857893.pdf', 'uploads/Confirmation_for_Booking_ID.pdf'),
('0da1279f-4c1a-4ef4-a9fc-277149a74069', 'Confirmation_for_Booking_ID_#_1085857893.pdf', 'uploads/Confirmation_for_Booking_ID.pdf'),
('1f1b5f6e-bea2-4150-bc5b-111fd13e06ed', 'Materials needed for dance team.pdf', 'uploads/Materials needed for dance team.pdf'),
('2ab0892a-0b2b-4a4e-bfb9-c106cad3aade', 'Informational Interview Questions (1).pdf', 'uploads/Informational Interview Questions (1).pdf'),
('30e3082f-d6c6-40d0-b00c-bc2d2c80efde', 'Confirmation_for_Booking_ID_#_1085857893.pdf', 'uploads/Confirmation_for_Booking_ID.pdf'),
('42abcb7c-26af-418e-9008-22a56d0b5b78', 'Confirmation_for_Booking_ID_#_1085857893.pdf', 'uploads/Confirmation_for_Booking_ID.pdf'),
('71d7e4fd-8bb1-4e5f-ad9c-2985f5bb6c06', 'Confirmation_for_Booking_ID_#_1085857893.pdf', 'uploads/Confirmation_for_Booking_ID.pdf'),
('8375adb3-a18f-4777-b607-00bc2beedea7', 'receipt-6770af5e7e21697.pdf', 'uploads/receipt-6770af5e7e21697.pdf'),
('886cfa9a-7d3f-11ee-801f-00ff02f405b2', 'test', 'https://www.google.com/url?sa=t&rct=j&q=&esrc=s&source=web&cd=&cad=rja&uact=8&ved=2ahUKEwjIiuGCr7GCAxWSR2cHHbAiAiIQFnoECAgQAQ&url=https%3A%2F%2Fwww.w3.org%2FWAI%2FER%2Ftests%2Fxhtml%2Ftestfiles%2Fresources%2Fpdf%2Fdummy.pdf&usg=AOvVaw1yfXcABf-Bej4cjTs8tPJn&opi=89978449'),
('b25eb92f-4f6e-409a-9e17-e3ee594ec2ad', 'Profile.pdf', 'uploads/Profile.pdf'),
('d4ea07a5-3038-4743-a5c8-2e15dadb8b9f', 'Materials needed for dance team.pdf', 'uploads/Materials needed for dance team.pdf'),
('fb4aa54e-dfba-4d0c-84bc-7cc8e2280b3a', 'BCS302_Assignment1 Tasks (1).pdf', 'uploads/BCS302_Assignment1 Tasks (1).pdf');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `merchants`
--

INSERT INTO `merchants` (`ID`, `createdAt`, `updatedAt`, `merchantName`, `contactNumber`, `email`, `userId`) VALUES
('073d4927-d4a1-40a9-9997-ea6f93edd0b6', '2025-03-17 03:49:37', '2025-03-17 03:49:37', 'organizer 5', '01812312313', 'geraldlee1688@gmail.com', '97036cc7-ee6c-495b-a1b0-a0ba5e6be827'),
('2e6066c3-8d6f-4223-b8d9-f1f855cff182', '2025-03-16 14:18:52', '2025-03-16 14:18:52', 'organizer 4', '01812312313', 'geraldlee1114+1@gmail.com', '685523cf-21f7-4e7f-9ac3-e889d8f2e95d'),
('9a88aef0-c09a-478d-a73c-87e6fd85936f', '2025-03-15 08:25:27', '2025-03-15 08:25:27', 'organizer 2', '01812312313', 'geraldlee168168+1@gmail.com', '23003a03-1857-4bb0-a29a-927d20e2b2c6'),
('a2d46c1f-82be-46ce-81c0-44279a12942b', '2025-03-16 13:57:37', '2025-03-16 13:57:37', 'organizer 3', '01812312313', 'geraldlee1114@gmail.com', 'd78b79da-a9bf-4ff0-9a01-cdaf5d2fae10'),
('f87939bf-5431-4137-9d2d-dbbf5fe670e9', '2025-03-13 09:54:29', '2025-03-13 09:54:29', 'organizer 1', '01812312313', 'geraldlee168168@gmail.com', '5967a9f4-220e-4354-b273-0589e4aa1504');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`ID`, `createdAt`, `updatedAt`, `name`, `date`, `time`, `productCode`, `description`, `imageUrl`, `merchantID`) VALUES
('c182d594-8e76-4eb4-9c80-71c3cf43c498', '2025-04-09 04:48:39', '2025-04-09 04:48:39', 'test123123', '2025-05-03', '20:00:00', 'test123123', 'test123123', '../uploads/product_67f5fc2743d8f.jpg', 'f87939bf-5431-4137-9d2d-dbbf5fe670e9'),
('f58e668b-f0b9-4c20-ba8e-585f1974acb0', '2025-03-11 04:14:19', '2025-03-11 04:14:19', 'Testing', '2025-03-28', '20:00:00', 'testing-1', 'testing', 'uploads/background.jpg', 'f87939bf-5431-4137-9d2d-dbbf5fe670e9');

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
  `type` enum('CUSTOMER','MERCHANT','ADMIN','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `createdAt`, `updatedAt`, `userName`, `password`, `isFirstTimeLogin`, `type`) VALUES
('11ffb377-726b-11ee-9678-00ff02f405b2', '2023-10-24 12:44:28', '2023-10-24 12:44:28', 'geraldlee2001', '$2y$10$no44VdmTisDWFJTm3gplku8nXxqmybKr2.ps44xjUnLeiegGSqHga', 0, 'CUSTOMER'),
('1485901b-7553-4a57-add9-cbd6bf30be6b', '2023-11-13 18:35:35', '2023-11-13 18:35:35', 'test2', '$2y$10$loTHPyH472Gtfyxa2nZwpuQxoBHFfHw7gIlnhuFmo.vmjMpYOxMrK', 0, 'MERCHANT'),
('23003a03-1857-4bb0-a29a-927d20e2b2c6', '2025-03-15 08:25:27', '2025-03-15 08:25:27', 'organizer2', '$2y$12$Wdmx.TmIiY4g.g3Me3vL0.sj3e20sSuGBy7w29f84TRtY46rO/yT6', 0, 'MERCHANT'),
('298abba3-e56b-4d1b-9170-803ddb9edbce', '2023-11-13 18:42:59', '2023-11-13 18:42:59', 't4', '$2y$10$llOz0NTgqwwnke9JQ0NIsOBlg8Sgq188kYjSvSMTSWbLQSwz98n3i', 0, 'MERCHANT'),
('5861289f-41aa-443e-8c23-15bda430f630', '2023-11-17 03:32:14', '2023-11-17 03:32:14', 'mer12', '$2y$10$CLA4Tv5E17MupOXDz/v/TeDdopXGv8bFidi8w3f/dTxYT2RGde1Me', 0, 'MERCHANT'),
('5967a9f4-220e-4354-b273-0589e4aa1504', '2025-03-11 04:10:41', '2025-03-11 04:10:41', 'organizer1', '$2y$12$Wdmx.TmIiY4g.g3Me3vL0.sj3e20sSuGBy7w29f84TRtY46rO/yT6', 0, 'MERCHANT'),
('6195f8fd-8bab-4406-8e6f-39178d6da650', '2025-03-13 09:54:29', '2025-03-13 09:54:29', 'geraldlee1111', '$2y$12$TXDWwhzJ7W/Dgl6QrGYEGOchH5/HfVasX2HRx8zs.zk4aikc1TiZ.', 0, 'MERCHANT'),
('685523cf-21f7-4e7f-9ac3-e889d8f2e95d', '2025-03-16 14:18:52', '2025-03-16 14:18:52', 'organizer4', '$2y$12$wpgruN9wRpLbfW5lE7vpWOwJ1dI1O3CiSXBsPDNPreew.HXsIyDoO', 0, 'MERCHANT'),
('740660a2-7237-11ee-9795-3c9c0f64ed48', '2023-10-24 06:34:59', '2023-10-24 06:34:59', 'admin', '$2y$10$sEdYf/3s8P2M4kndcs.zGOPIB.lRSG30LDCc2.lbDuV4iGCcOPmBu', 0, 'ADMIN'),
('74227764-1883-426a-abc0-dfeb2b05cd6f', '2023-11-14 04:49:25', '2023-11-14 04:49:25', 'florence', '$2y$10$3seGJtef8k3e9lLER8xwUeufAtWuH3y3FjIsmVX8tcYGDHYooNSqW', 0, 'CUSTOMER'),
('784e3544-0cce-497b-beaa-1675537720b6', '2023-11-07 07:28:15', '2023-11-07 07:28:15', 'merchant1', '$2y$10$w2zbBkdtsi3vcoNjzBeeB.FUZzBbtWE4btNdBnrmZcriTS9K2EE1y', 0, 'MERCHANT'),
('81755d9f-aad2-4312-aa5a-b1e069df682c', '2023-11-13 18:09:35', '2023-11-13 18:09:35', 'merchant2', '$2y$10$w2zbBkdtsi3vcoNjzBeeB.FUZzBbtWE4btNdBnrmZcriTS9K2EE1y', 0, 'MERCHANT'),
('8630e8a0-731a-4ea1-aeec-c377be625c91', '2023-11-13 18:39:46', '2023-11-13 18:39:46', 'test3', '$2y$10$oYuEtJrQkCn4lgs32KiiQexuneH/8xaDfzFmDlcX2w8e9r1FdBOdG', 0, 'MERCHANT'),
('8a056ad8-ef22-48c4-9b49-a30c95a2ae50', '2023-11-14 05:40:18', '2023-11-14 05:40:18', 't12', '$2y$10$vZFU.S9agozoMq9Tb1HXRu/FL01cXZzDyjLaG8wL4fXHb0MAJAoam', 0, 'MERCHANT'),
('97036cc7-ee6c-495b-a1b0-a0ba5e6be827', '2025-03-17 03:49:37', '2025-03-17 03:49:37', 'organizer5', '$2y$12$A10Fidjwh/2gTIiPz3d1JuUb3xDFc8B42l1CrMG8AFLiExXHPUdzS', 1, 'MERCHANT'),
('ac3d499a-7c38-4701-be2d-ac23e5f9bc33', '2023-11-14 04:29:23', '2023-11-14 04:29:23', 't2', '$2y$10$XEtDxJ6e1bUN/r2w.f8EqOoM2OHGQ4XzIsSLClHtAWQY3Dc2UssJi', 0, 'MERCHANT'),
('c63a6e98-be6e-43e7-8361-cf1de904e4ec', '2023-11-14 04:48:03', '2023-11-14 04:48:03', 'florencetan', '$2y$10$UdFaWSaDxFbWkyE/p3g/juXNtEMHlaYxLB9nG1FlJ2PJavjHHUsye', 0, 'CUSTOMER'),
('d0d96d89-219f-4302-8655-08f47a9d1090', '2023-11-13 18:35:09', '2023-11-13 18:35:09', 'test', '$2y$10$EczmalqBX1KAwFzRLDS6xuEdKMMGcRVMaIYMUb5st3ZMoF4Mq.h5m', 0, 'MERCHANT'),
('d78b79da-a9bf-4ff0-9a01-cdaf5d2fae10', '2025-03-16 13:57:37', '2025-03-16 13:57:37', 'organizer3', '$2y$12$Xf/8kg8SeQR91VETS07.oef3lBFHGOaGZQ/SmrpISsyZ1CxE3hw86', 1, 'MERCHANT'),
('f30d08a7-4d3d-461b-a065-23540e7af5b4', '2023-11-12 17:40:22', '2023-11-12 17:40:22', '1234', '$2y$10$VOUmZTqKi7XAV6t.Ee79DumC8KPObQBN4yyIjrj9Za8Wjc6yK1vVa', 0, 'CUSTOMER'),
('f7876ea1-909e-425a-8cf7-1000d4c24d65', '2023-11-17 03:22:53', '2023-11-17 03:22:53', 'customer', '$2y$10$6UDuBbg.CHYND7AvR4w7E.iRvtuIOwVBsfBfUMfh1b6OAmHMbr9R.', 0, 'CUSTOMER');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `document`
--
ALTER TABLE `document`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `merchants`
--
ALTER TABLE `merchants`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `FK_MerchantUser` (`userId`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `merchantID` (`merchantID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
