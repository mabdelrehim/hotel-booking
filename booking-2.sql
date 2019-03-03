-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 21, 2018 at 02:28 PM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `brokerAccount`
--

CREATE TABLE `brokerAccount` (
  `brokerAccountId` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `checkIns`
--

CREATE TABLE `checkIns` (
  `checkInId` int(11) NOT NULL,
  `reservationId` int(11) NOT NULL,
  `customerId` int(11) NOT NULL,
  `hotelId` int(11) NOT NULL,
  `checkInDate` date NOT NULL,
  `checkOutDate` date NOT NULL,
  `amountPayed` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customerId` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `numberOfReservationsMade` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customerId`, `username`, `email`, `password`, `name`, `numberOfReservationsMade`) VALUES
(1, 'mohamed', 'mohamed741963@gmail.com', '123321', 'mohamed elsayed', 1);

-- --------------------------------------------------------

--
-- Table structure for table `hoteImages`
--

CREATE TABLE `hoteImages` (
  `imageId` int(11) NOT NULL,
  `ownerHotelId` int(11) NOT NULL,
  `imageURL` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hotel`
--

CREATE TABLE `hotel` (
  `hotelId` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `avgRating` float NOT NULL DEFAULT '0',
  `moneyDue` int(11) NOT NULL DEFAULT '0',
  `stars` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hotel`
--

INSERT INTO `hotel` (`hotelId`, `username`, `email`, `password`, `name`, `location`, `avgRating`, `moneyDue`, `stars`) VALUES
(1, 'hilton97', 'hilton@gmail.com', '123321', 'hilton', 'paris', 0, 0, 4);

-- --------------------------------------------------------

--
-- Table structure for table `hotelFacilities`
--

CREATE TABLE `hotelFacilities` (
  `hotelID` int(11) NOT NULL,
  `facility` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hotelFacilities`
--

INSERT INTO `hotelFacilities` (`hotelID`, `facility`) VALUES
(1, 'wifi'),
(1, 'pool'),
(1, 'gym');

-- --------------------------------------------------------

--
-- Table structure for table `pendingHotelAccounts`
--

CREATE TABLE `pendingHotelAccounts` (
  `pendingHotelId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pendingReservation`
--

CREATE TABLE `pendingReservation` (
  `pendingReservationId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `premiumHotelAccounts`
--

CREATE TABLE `premiumHotelAccounts` (
  `premiumHotelId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `ratedHotelId` int(11) NOT NULL,
  `ratingCustomerId` int(11) NOT NULL,
  `ratingId` int(11) NOT NULL,
  `numberOfStars` int(11) NOT NULL,
  `ratingText` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reservationId` int(11) NOT NULL,
  `customerId` int(11) NOT NULL,
  `hotelId` int(11) NOT NULL,
  `roomId` int(11) NOT NULL,
  `fromDate` date NOT NULL,
  `toDate` date NOT NULL,
  `totalPrice` int(11) NOT NULL,
  `isCancelled` tinyint(1) NOT NULL DEFAULT '0',
  `isApproved` tinyint(1) NOT NULL DEFAULT '0',
  `extensionDummy` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `roomId` int(11) NOT NULL,
  `offeringHotelId` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`roomId`, `offeringHotelId`, `type`, `price`) VALUES
(1, 1, 'single', 30),
(2, 1, 'single', 30),
(3, 1, 'double', 50),
(4, 1, 'double', 50),
(5, 1, 'triple', 100),
(6, 1, 'triple', 100),
(7, 1, 'royal', 200),
(8, 1, 'royal', 200);

-- --------------------------------------------------------

--
-- Table structure for table `suspendedCustomersAccounts`
--

CREATE TABLE `suspendedCustomersAccounts` (
  `suspendedCustomerId` int(11) NOT NULL,
  `suspensionDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `suspendedHotelAccounts`
--

CREATE TABLE `suspendedHotelAccounts` (
  `suspendedHotelId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brokerAccount`
--
ALTER TABLE `brokerAccount`
  ADD PRIMARY KEY (`brokerAccountId`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `checkIns`
--
ALTER TABLE `checkIns`
  ADD PRIMARY KEY (`checkInId`),
  ADD KEY `reservationId` (`reservationId`),
  ADD KEY `customerId` (`customerId`),
  ADD KEY `hotelId` (`hotelId`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customerId`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `hoteImages`
--
ALTER TABLE `hoteImages`
  ADD PRIMARY KEY (`imageId`),
  ADD KEY `ownerHotelId` (`ownerHotelId`);

--
-- Indexes for table `hotel`
--
ALTER TABLE `hotel`
  ADD PRIMARY KEY (`hotelId`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `hotelFacilities`
--
ALTER TABLE `hotelFacilities`
  ADD KEY `hotelID` (`hotelID`);

--
-- Indexes for table `pendingHotelAccounts`
--
ALTER TABLE `pendingHotelAccounts`
  ADD PRIMARY KEY (`pendingHotelId`);

--
-- Indexes for table `pendingReservation`
--
ALTER TABLE `pendingReservation`
  ADD PRIMARY KEY (`pendingReservationId`);

--
-- Indexes for table `premiumHotelAccounts`
--
ALTER TABLE `premiumHotelAccounts`
  ADD PRIMARY KEY (`premiumHotelId`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`ratingId`),
  ADD KEY `ratedHotelId` (`ratedHotelId`),
  ADD KEY `ratingCustomerId` (`ratingCustomerId`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservationId`),
  ADD KEY `customerId` (`customerId`),
  ADD KEY `roomId` (`roomId`),
  ADD KEY `hotelId` (`hotelId`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`roomId`),
  ADD KEY `offeringHotelId` (`offeringHotelId`);

--
-- Indexes for table `suspendedCustomersAccounts`
--
ALTER TABLE `suspendedCustomersAccounts`
  ADD PRIMARY KEY (`suspendedCustomerId`);

--
-- Indexes for table `suspendedHotelAccounts`
--
ALTER TABLE `suspendedHotelAccounts`
  ADD PRIMARY KEY (`suspendedHotelId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brokerAccount`
--
ALTER TABLE `brokerAccount`
  MODIFY `brokerAccountId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `checkIns`
--
ALTER TABLE `checkIns`
  MODIFY `checkInId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customerId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hoteImages`
--
ALTER TABLE `hoteImages`
  MODIFY `imageId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hotel`
--
ALTER TABLE `hotel`
  MODIFY `hotelId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `ratingId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservationId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `roomId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `checkIns`
--
ALTER TABLE `checkIns`
  ADD CONSTRAINT `checkIns_ibfk_1` FOREIGN KEY (`reservationId`) REFERENCES `reservations` (`reservationId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `checkIns_ibfk_2` FOREIGN KEY (`customerId`) REFERENCES `customer` (`customerId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `checkIns_ibfk_3` FOREIGN KEY (`hotelId`) REFERENCES `hotel` (`hotelId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hoteImages`
--
ALTER TABLE `hoteImages`
  ADD CONSTRAINT `hoteImages_ibfk_1` FOREIGN KEY (`ownerHotelId`) REFERENCES `hotel` (`hotelId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hotelFacilities`
--
ALTER TABLE `hotelFacilities`
  ADD CONSTRAINT `hotelFacilities_ibfk_1` FOREIGN KEY (`hotelID`) REFERENCES `hotel` (`hotelId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pendingHotelAccounts`
--
ALTER TABLE `pendingHotelAccounts`
  ADD CONSTRAINT `pendingHotelAccounts_ibfk_1` FOREIGN KEY (`pendingHotelId`) REFERENCES `hotel` (`hotelId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pendingReservation`
--
ALTER TABLE `pendingReservation`
  ADD CONSTRAINT `pendingReservation_ibfk_1` FOREIGN KEY (`pendingReservationId`) REFERENCES `reservations` (`reservationId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `premiumHotelAccounts`
--
ALTER TABLE `premiumHotelAccounts`
  ADD CONSTRAINT `premiumHotelAccounts_ibfk_1` FOREIGN KEY (`premiumHotelId`) REFERENCES `hotel` (`hotelId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`ratedHotelId`) REFERENCES `hotel` (`hotelId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`ratingCustomerId`) REFERENCES `customer` (`customerId`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`customerId`) REFERENCES `customer` (`customerId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`roomId`) REFERENCES `rooms` (`roomId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_3` FOREIGN KEY (`hotelId`) REFERENCES `hotel` (`hotelId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`offeringHotelId`) REFERENCES `hotel` (`hotelId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `suspendedCustomersAccounts`
--
ALTER TABLE `suspendedCustomersAccounts`
  ADD CONSTRAINT `suspendedCustomersAccounts_ibfk_1` FOREIGN KEY (`suspendedCustomerId`) REFERENCES `customer` (`customerId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `suspendedHotelAccounts`
--
ALTER TABLE `suspendedHotelAccounts`
  ADD CONSTRAINT `suspendedHotelAccounts_ibfk_1` FOREIGN KEY (`suspendedHotelId`) REFERENCES `hotel` (`hotelId`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
