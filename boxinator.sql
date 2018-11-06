-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Värd: 127.0.0.1
-- Tid vid skapande: 06 nov 2018 kl 22:05
-- Serverversion: 10.1.36-MariaDB
-- PHP-version: 7.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databas: `boxinator`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `countries`
--

CREATE TABLE `countries` (
  `id_countries` int(11) NOT NULL,
  `country` varchar(255) NOT NULL,
  `multiplier` decimal(10,5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `countries`
--

INSERT INTO `countries` (`id_countries`, `country`, `multiplier`) VALUES
(1, 'Sweden', '1.30000'),
(2, 'China', '4.00000'),
(3, 'Brazil', '8.60000'),
(4, 'Australia', '7.20000');

-- --------------------------------------------------------

--
-- Tabellstruktur `package`
--

CREATE TABLE `package` (
  `id_package` int(11) NOT NULL,
  `shipment_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `weight` decimal(20,2) NOT NULL,
  `cost` decimal(20,2) NOT NULL,
  `red` int(3) NOT NULL,
  `green` int(3) NOT NULL,
  `blue` int(3) NOT NULL,
  `country_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `package`
--

INSERT INTO `package` (`id_package`, `shipment_id`, `name`, `weight`, `cost`, `red`, `green`, `blue`, `country_id`) VALUES
(106, 1, 'Test testsson', '25.00', '32.50', 255, 255, 255, 1),
(107, 1, 'Prov Provsdottir', '19.00', '163.40', 128, 0, 0, 3),
(108, 56, 'Dr. Vem', '34.50', '296.70', 0, 255, 0, 3),
(109, 56, 'Fru Fri', '2.50', '18.00', 255, 128, 128, 4),
(110, 56, 'St. Nyfiken', '44.00', '57.20', 0, 128, 0, 1);

-- --------------------------------------------------------

--
-- Tabellstruktur `shipment`
--

CREATE TABLE `shipment` (
  `id_shipment` int(11) NOT NULL,
  `shipdate` date NOT NULL,
  `total_weight` decimal(20,2) NOT NULL,
  `total_cost` decimal(20,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `shipment`
--

INSERT INTO `shipment` (`id_shipment`, `shipdate`, `total_weight`, `total_cost`) VALUES
(1, '2018-11-06', '44.00', '195.90'),
(56, '2018-11-06', '81.00', '371.90');

--
-- Index för dumpade tabeller
--

--
-- Index för tabell `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id_countries`);

--
-- Index för tabell `package`
--
ALTER TABLE `package`
  ADD PRIMARY KEY (`id_package`),
  ADD KEY `country_id` (`country_id`);

--
-- Index för tabell `shipment`
--
ALTER TABLE `shipment`
  ADD PRIMARY KEY (`id_shipment`);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell `countries`
--
ALTER TABLE `countries`
  MODIFY `id_countries` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT för tabell `package`
--
ALTER TABLE `package`
  MODIFY `id_package` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT för tabell `shipment`
--
ALTER TABLE `shipment`
  MODIFY `id_shipment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- Restriktioner för dumpade tabeller
--

--
-- Restriktioner för tabell `package`
--
ALTER TABLE `package`
  ADD CONSTRAINT `package_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id_countries`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
