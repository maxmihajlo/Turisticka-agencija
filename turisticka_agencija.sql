-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 13, 2026 at 10:46 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `turisticka_agencija`
--

-- --------------------------------------------------------

--
-- Table structure for table `aranzman`
--

CREATE TABLE `aranzman` (
  `ID_aranzmana` int(11) NOT NULL,
  `naziv` varchar(100) NOT NULL,
  `opis` text NOT NULL,
  `datum_polaska` date NOT NULL,
  `datum_povratka` date NOT NULL,
  `cena_po_osobi` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `aranzman`
--

INSERT INTO `aranzman` (`ID_aranzmana`, `naziv`, `opis`, `datum_polaska`, `datum_povratka`, `cena_po_osobi`) VALUES
(7, 'Letovanje Grčka', '7 noćenja, apartmani, autobus', '2026-06-15', '2026-06-22', 450.00),
(8, 'Pariz vikend', 'Avion + hotel 3 noći', '2026-04-10', '2026-04-13', 720.00),
(9, 'Italija tura', 'Rim – Firenca – Venecija', '2026-05-05', '2026-05-12', 680.00),
(10, 'Barcelona city break', 'Avion + hotel 4 noći, slobodno vreme', '2026-04-18', '2026-04-22', 650.00),
(11, 'Turska Antalija', 'All inclusive hotel, 7 noći, avion', '2026-06-20', '2026-06-27', 780.00),
(12, 'Prag vikend', 'Autobus + hotel 3 noći', '2026-05-01', '2026-05-04', 320.00),
(13, 'Beč i Bratislava', 'Dvodnevni izlet, autobus', '2026-04-05', '2026-04-06', 180.00),
(14, 'Španija Andaluzija', 'Sevilja – Granada – Malaga, avion', '2026-09-10', '2026-09-18', 920.00),
(15, 'Egipat Hurgada', 'Resort 5*, all inclusive, avion', '2026-10-05', '2026-10-12', 890.00),
(16, 'Budimpešta Nova godina', 'Autobus + hotel 2 noći', '2025-12-30', '2026-01-02', 260.00),
(17, 'Švajcarska Alpi', 'Cirih – Lucern – Interlaken', '2026-07-10', '2026-07-17', 1100.00),
(18, 'London tura', 'Avion + hotel 4 noći + razgledanje', '2026-05-15', '2026-05-20', 950.00),
(19, 'Malta letovanje', 'Avion + apartmani 7 noći', '2026-08-01', '2026-08-08', 700.00);

-- --------------------------------------------------------

--
-- Table structure for table `klijent`
--

CREATE TABLE `klijent` (
  `ID_klijent` int(11) NOT NULL,
  `ime` varchar(50) NOT NULL,
  `prezime` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `klijent`
--

INSERT INTO `klijent` (`ID_klijent`, `ime`, `prezime`, `email`) VALUES
(6, 'Milica', 'Ilić', 'milica@gmail.com'),
(7, 'Nikola', 'Stojanović', 'nikola@gmail.com'),
(8, 'Jelena', 'Mitić', 'jelena@gmail.com'),
(10, 'Ana', 'Jovanović', 'ana.jovanovic@gmail.com'),
(11, 'Nikola', 'Ilić', 'nikola.ilic@gmail.com'),
(12, 'Milica', 'Stojanović', 'milica.stojanovic@gmail.com'),
(13, 'Stefan', 'Nikolić', 'stefan.nikolic@gmail.com'),
(14, 'Jelena', 'Marković', 'jelena.markovic@gmail.com'),
(15, 'Luka', 'Pavlović', 'luka.pavlovic@gmail.com'),
(16, 'Ivana', 'Đorđević', 'ivana@gmail.com'),
(18, 'Sara', 'Ristić', 'sara.ristic@gmail.com'),
(19, 'Petar', 'Petrovic', 'petar@gmail.com'),
(20, 'Sara', 'Despic', 'sarad@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `partner`
--

CREATE TABLE `partner` (
  `ID_partner` int(11) NOT NULL,
  `naziv` varchar(100) NOT NULL,
  `tip` enum('hotel','prevoznik','vodic','') NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `partner`
--

INSERT INTO `partner` (`ID_partner`, `naziv`, `tip`, `email`) VALUES
(7, 'Hotel Sun', 'hotel', 'hotel@sun.com'),
(8, 'SkyTravel', 'prevoznik', 'info@skytravel.com'),
(9, 'City Guide', 'vodic', 'guide@city.com'),
(10, 'Hotel Adriatic', 'hotel', 'kontakt@hoteladriatic.rs'),
(11, 'Hotel Panorama', 'hotel', 'info@panorama.rs'),
(12, 'SkyTravel Prevoz', 'prevoznik', 'office@skytravel.rs'),
(13, 'Balkan Bus', 'prevoznik', 'kontakt@balkanbus.rs'),
(14, 'Guide Plus', 'vodic', 'info@guideplus.rs'),
(15, 'City Tours', 'vodic', 'citytours@gmail.com'),
(16, 'Luxury Stay', 'hotel', 'luxurystay@hotel.rs'),
(17, 'FastLine Transport', 'prevoznik', 'fastline@transport.rs');

-- --------------------------------------------------------

--
-- Table structure for table `partner_isplata`
--

CREATE TABLE `partner_isplata` (
  `ID_isplata` int(11) NOT NULL,
  `ID_provizija` int(11) NOT NULL,
  `ID_partner` int(11) NOT NULL,
  `datum_isplate` date NOT NULL,
  `status` enum('nije_placeno','u_obradi','placeno') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `partner_isplata`
--

INSERT INTO `partner_isplata` (`ID_isplata`, `ID_provizija`, `ID_partner`, `datum_isplate`, `status`) VALUES
(11, 18, 8, '2026-01-11', 'u_obradi'),
(12, 17, 10, '2026-01-11', 'placeno'),
(13, 19, 13, '2026-01-11', 'placeno'),
(14, 20, 10, '2026-01-11', 'u_obradi'),
(15, 21, 9, '2026-01-12', 'u_obradi'),
(16, 22, 16, '2026-01-12', 'placeno'),
(17, 24, 16, '2026-01-12', 'u_obradi'),
(18, 23, 9, '2026-01-12', 'placeno');

-- --------------------------------------------------------

--
-- Table structure for table `provizija`
--

CREATE TABLE `provizija` (
  `ID_provizija` int(11) NOT NULL,
  `ID_partner` int(11) NOT NULL,
  `ID_racun` int(11) NOT NULL,
  `iznos_partner` decimal(10,2) NOT NULL,
  `iznos_agencija` decimal(10,2) NOT NULL,
  `datum` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `provizija`
--

INSERT INTO `provizija` (`ID_provizija`, `ID_partner`, `ID_racun`, `iznos_partner`, `iznos_agencija`, `datum`) VALUES
(17, 10, 21, 120.00, 360.00, '2026-01-11'),
(18, 8, 21, 40.00, 360.00, '2026-01-11'),
(19, 13, 23, 20.00, 1300.00, '2026-01-11'),
(20, 10, 23, 120.00, 1300.00, '2026-01-11'),
(21, 9, 25, 100.00, 1660.00, '2026-01-12'),
(22, 16, 25, 140.00, 1660.00, '2026-01-12'),
(23, 9, 26, 150.00, 990.00, '2026-01-12'),
(24, 16, 26, 210.00, 990.00, '2026-01-12');

-- --------------------------------------------------------

--
-- Table structure for table `racun`
--

CREATE TABLE `racun` (
  `ID_racun` int(11) NOT NULL,
  `ID_rezervacija` int(11) NOT NULL,
  `datum` date NOT NULL,
  `status` varchar(30) NOT NULL,
  `ukupna_cena` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `racun`
--

INSERT INTO `racun` (`ID_racun`, `ID_rezervacija`, `datum`, `status`, `ukupna_cena`) VALUES
(8, 21, '2026-01-11', 'placen', 520.00),
(9, 22, '2026-01-11', 'kreiran', 2600.00),
(10, 23, '2026-01-11', 'placen', 1440.00),
(11, 24, '2026-01-11', 'otkazan', 1400.00),
(12, 25, '2026-01-12', 'placen', 1900.00),
(13, 26, '2026-01-12', 'placen', 1350.00);

-- --------------------------------------------------------

--
-- Table structure for table `rezervacija`
--

CREATE TABLE `rezervacija` (
  `ID_rezervacija` int(11) NOT NULL,
  `ID_klijent` int(11) NOT NULL,
  `ID_zaposleni` int(11) NOT NULL,
  `ID_aranzmana` int(11) NOT NULL,
  `datum_rezervacije` date NOT NULL,
  `broj_osoba` int(11) NOT NULL,
  `status` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rezervacija`
--

INSERT INTO `rezervacija` (`ID_rezervacija`, `ID_klijent`, `ID_zaposleni`, `ID_aranzmana`, `datum_rezervacije`, `broj_osoba`, `status`) VALUES
(21, 6, 1, 16, '2025-12-27', 2, 'nova'),
(22, 10, 1, 10, '2026-01-09', 4, 'nova'),
(23, 12, 1, 8, '2026-01-10', 2, 'potvrdjena'),
(24, 8, 1, 19, '2026-01-22', 2, 'otkazana'),
(25, 19, 1, 18, '2026-01-12', 2, 'nova'),
(26, 20, 6, 7, '2026-01-12', 3, 'nova');

-- --------------------------------------------------------

--
-- Table structure for table `ugovor_partner`
--

CREATE TABLE `ugovor_partner` (
  `ID_ugovor` int(11) NOT NULL,
  `ID_partner` int(11) NOT NULL,
  `ID_zaposleni` int(11) NOT NULL,
  `pocetak_ugovora` date NOT NULL,
  `kraj_ugovora` date NOT NULL,
  `tip_usluge` varchar(50) NOT NULL,
  `tarifa` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ugovor_partner`
--

INSERT INTO `ugovor_partner` (`ID_ugovor`, `ID_partner`, `ID_zaposleni`, `pocetak_ugovora`, `kraj_ugovora`, `tip_usluge`, `tarifa`) VALUES
(8, 9, 1, '2026-01-10', '2026-01-31', 'vodic', 50.00),
(9, 7, 1, '2026-01-10', '2026-01-30', 'smestaj', 100.00),
(10, 8, 1, '2025-12-30', '2027-02-03', 'prevoz', 20.00),
(11, 13, 1, '2026-01-23', '2026-03-14', 'prevoz', 10.00),
(12, 15, 1, '2026-01-16', '2026-08-21', 'vodic', 5.00),
(13, 17, 1, '2026-01-04', '2033-11-18', 'prevoz', 30.00),
(14, 10, 1, '2026-01-11', '2027-11-18', 'smestah', 60.00),
(15, 11, 1, '2026-01-25', '2026-08-15', 'smestaj', 70.00),
(16, 16, 1, '2026-01-31', '2026-09-24', 'smestaj', 70.00);

-- --------------------------------------------------------

--
-- Table structure for table `zaposleni`
--

CREATE TABLE `zaposleni` (
  `ID_zaposleni` int(11) NOT NULL,
  `ime` varchar(50) NOT NULL,
  `prezime` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pol` enum('muski','zenski','','') NOT NULL,
  `lozinka` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zaposleni`
--

INSERT INTO `zaposleni` (`ID_zaposleni`, `ime`, `prezime`, `email`, `pol`, `lozinka`) VALUES
(1, 'Tea', 'Krstic', 'tea@gmail.com', 'zenski', '123'),
(4, 'Ana', 'Petrović', 'ana@agencija.rs', 'zenski', '123'),
(5, 'Marko', 'Jovanović', 'marko@agencija.rs', 'muski', '123'),
(6, 'Mihajlo', 'Maksimovic', 'mihajlo@gmail.com', 'muski', '123'),
(7, 'Milica', 'Petkovic', 'milica@agencija.com', 'zenski', '12345');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aranzman`
--
ALTER TABLE `aranzman`
  ADD PRIMARY KEY (`ID_aranzmana`);

--
-- Indexes for table `klijent`
--
ALTER TABLE `klijent`
  ADD PRIMARY KEY (`ID_klijent`);

--
-- Indexes for table `partner`
--
ALTER TABLE `partner`
  ADD PRIMARY KEY (`ID_partner`);

--
-- Indexes for table `partner_isplata`
--
ALTER TABLE `partner_isplata`
  ADD PRIMARY KEY (`ID_isplata`),
  ADD KEY `ID_provizija` (`ID_provizija`),
  ADD KEY `ID_partner` (`ID_partner`);

--
-- Indexes for table `provizija`
--
ALTER TABLE `provizija`
  ADD PRIMARY KEY (`ID_provizija`),
  ADD KEY `ID_partner` (`ID_partner`),
  ADD KEY `ID_rezervacija` (`ID_racun`);

--
-- Indexes for table `racun`
--
ALTER TABLE `racun`
  ADD PRIMARY KEY (`ID_racun`),
  ADD KEY `ID_rezervacija` (`ID_rezervacija`);

--
-- Indexes for table `rezervacija`
--
ALTER TABLE `rezervacija`
  ADD PRIMARY KEY (`ID_rezervacija`),
  ADD KEY `ID_klijent` (`ID_klijent`),
  ADD KEY `ID_aranzmana` (`ID_aranzmana`);

--
-- Indexes for table `ugovor_partner`
--
ALTER TABLE `ugovor_partner`
  ADD PRIMARY KEY (`ID_ugovor`);

--
-- Indexes for table `zaposleni`
--
ALTER TABLE `zaposleni`
  ADD PRIMARY KEY (`ID_zaposleni`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aranzman`
--
ALTER TABLE `aranzman`
  MODIFY `ID_aranzmana` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `klijent`
--
ALTER TABLE `klijent`
  MODIFY `ID_klijent` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `partner`
--
ALTER TABLE `partner`
  MODIFY `ID_partner` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `partner_isplata`
--
ALTER TABLE `partner_isplata`
  MODIFY `ID_isplata` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `provizija`
--
ALTER TABLE `provizija`
  MODIFY `ID_provizija` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `racun`
--
ALTER TABLE `racun`
  MODIFY `ID_racun` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `rezervacija`
--
ALTER TABLE `rezervacija`
  MODIFY `ID_rezervacija` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `ugovor_partner`
--
ALTER TABLE `ugovor_partner`
  MODIFY `ID_ugovor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `zaposleni`
--
ALTER TABLE `zaposleni`
  MODIFY `ID_zaposleni` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `partner_isplata`
--
ALTER TABLE `partner_isplata`
  ADD CONSTRAINT `partner_isplata_ibfk_1` FOREIGN KEY (`ID_provizija`) REFERENCES `provizija` (`ID_provizija`),
  ADD CONSTRAINT `partner_isplata_ibfk_2` FOREIGN KEY (`ID_partner`) REFERENCES `partner` (`ID_partner`);

--
-- Constraints for table `provizija`
--
ALTER TABLE `provizija`
  ADD CONSTRAINT `provizija_ibfk_1` FOREIGN KEY (`ID_partner`) REFERENCES `partner` (`ID_partner`),
  ADD CONSTRAINT `provizija_ibfk_2` FOREIGN KEY (`ID_racun`) REFERENCES `rezervacija` (`ID_rezervacija`);

--
-- Constraints for table `racun`
--
ALTER TABLE `racun`
  ADD CONSTRAINT `racun_ibfk_1` FOREIGN KEY (`ID_rezervacija`) REFERENCES `rezervacija` (`ID_rezervacija`);

--
-- Constraints for table `rezervacija`
--
ALTER TABLE `rezervacija`
  ADD CONSTRAINT `rezervacija_ibfk_1` FOREIGN KEY (`ID_klijent`) REFERENCES `klijent` (`ID_klijent`),
  ADD CONSTRAINT `rezervacija_ibfk_2` FOREIGN KEY (`ID_aranzmana`) REFERENCES `aranzman` (`ID_aranzmana`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
