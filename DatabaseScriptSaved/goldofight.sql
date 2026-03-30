-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 04 mai 2024 à 02:01
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `goldofight`
--

-- --------------------------------------------------------

--
-- Structure de la table `account`
--

CREATE TABLE `account` (
  `IdAccount` int(11) NOT NULL COMMENT 'Primary Key',
  `Nom` varchar(50) DEFAULT NULL,
  `Prenom` varchar(50) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Pseudo` varchar(25) DEFAULT NULL,
  `Password` varchar(32) DEFAULT NULL,
  `CreateDate` datetime DEFAULT current_timestamp(),
  `Modifyate` datetime DEFAULT NULL,
  `ActivationDate` datetime DEFAULT NULL,
  `Token` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `account`
--

INSERT INTO `account` (`IdAccount`, `Nom`, `Prenom`, `Email`, `Pseudo`, `Password`, `CreateDate`, `Modifyate`, `ActivationDate`, `Token`) VALUES
(299, 'Cantin', 'Samuel', 'NOUSEDEMAIL@tst.fr', 'Samiol999', 'A2D26ECA4B8545E146A9E75E0C25A5A3', '2023-10-06 14:47:20', NULL, NULL, 'uzZYKrokxI'),
(300, 'Dupont', 'Lajoie', 'NOUSEDEMAIL@tst.fr', 'Dupont1000', 'FE4E4E4D96AD90858E62BDE2D4C076B7', '2023-10-07 13:49:57', NULL, NULL, 'uzZYKrokxI');

-- --------------------------------------------------------

--
-- Structure de la table `activities`
--

CREATE TABLE `activities` (
  `IdActivity` int(11) NOT NULL COMMENT 'Primary Key',
  `Nom` varchar(50) NOT NULL,
  `Info` varchar(512) DEFAULT NULL,
  `CreateDate` datetime DEFAULT current_timestamp(),
  `StartDate` datetime DEFAULT NULL,
  `EndDate` datetime DEFAULT NULL,
  `Name` varchar(25) NOT NULL DEFAULT 'Activiy'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `activities`
--

INSERT INTO `activities` (`IdActivity`, `Nom`, `Info`, `CreateDate`, `StartDate`, `EndDate`, `Name`) VALUES
(1, 'Partie libre', NULL, '2023-10-01 00:00:00', '2023-10-01 00:00:00', '2100-10-01 00:00:00', 'Free'),
(2, 'Le camp de la lune noire', 'Un allé simple pour la lune', '2023-10-06 19:21:38', '2023-10-06 00:20:00', '2023-12-31 19:20:00', 'DarkMoon'),
(3, 'Le festin des loups', 'Venez gagner un super robot Hlpro', '2023-10-06 19:23:16', '2023-10-05 19:22:14', '2024-12-31 19:22:14', 'WolfFestin'),
(9, 'Grendizer Infinitism', 'Venez gagner le Grendizer Infinitism', '2023-10-06 19:24:35', '2023-10-05 19:23:42', '2024-12-31 19:23:42', 'Infinitism');

-- --------------------------------------------------------

--
-- Structure de la table `scores`
--

CREATE TABLE `scores` (
  `IdScore` int(11) NOT NULL COMMENT 'Primary Key',
  `Score` int(11) NOT NULL,
  `CreateDate` datetime DEFAULT current_timestamp(),
  `IdActivity` int(11) DEFAULT 1,
  `IdAccount` int(11) NOT NULL,
  `Minutes` int(11) DEFAULT 0,
  `Nbclick` int(11) DEFAULT 0,
  `Tour` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `scores`
--

INSERT INTO `scores` (`IdScore`, `Score`, `CreateDate`, `IdActivity`, `IdAccount`, `Minutes`, `Nbclick`, `Tour`) VALUES
(398, 142, '2023-10-06 15:01:05', 1, 299, 7155, 16, 1),
(399, 5161, '2023-10-06 19:18:41', 1, 299, 260014, 1152, 1),
(400, 518, '2023-10-06 19:26:26', 3, 299, 54053, 241, 1),
(401, 12561, '2023-10-06 20:03:27', 1, 299, 371299, 1626, 1),
(402, 1263, '2023-10-06 23:07:25', 1, 299, 103385, 464, 1),
(403, 30355, '2023-10-06 23:34:56', 1, 299, 783676, 3412, 1),
(404, 454, '2023-10-07 00:08:16', 1, 299, 48631, 211, 1),
(405, 223, '2023-10-07 00:39:57', 1, 299, 21949, 71, 1),
(406, 197, '2023-10-07 09:59:50', 9, 299, 24233, 96, 1),
(407, 270, '2023-10-07 10:47:37', 9, 299, 66107, 116, 1),
(408, 197, '2023-10-07 20:25:41', 1, 299, 205780, 90, 1),
(409, 158, '2023-10-07 20:26:27', 9, 299, 8267, 24, 1),
(410, 155, '2023-10-13 10:16:54', 1, 299, 9877, 40, 1),
(411, 140598, '2023-10-21 09:50:48', 1, 299, 1917345, 8352, 1),
(412, 3811, '2023-11-13 13:45:44', 1, 299, 168918, 688, 1),
(413, 728, '2023-11-29 19:47:07', 1, 299, 75876, 334, 1),
(414, 23320, '2023-12-14 17:14:12', 1, 299, 548614, 2422, 1),
(415, 3695, '2024-02-19 15:27:04', 1, 299, 190143, 744, 1),
(416, 33399, '2024-02-19 15:35:20', 1, 299, 388216, 1751, 1),
(417, 33015, '2024-03-08 09:16:10', 1, 299, 259275, 1014, 1),
(418, 42376, '2024-03-08 11:47:39', 1, 299, 487216, 2128, 1),
(419, 2958, '2024-03-14 12:11:48', 1, 299, 280500, 488, 1),
(420, 174536, '2024-03-14 12:37:33', 1, 299, 474632, 2075, 1),
(421, 8276, '2024-03-16 11:47:02', 1, 299, 301282, 1226, 1),
(422, 468, '2024-03-16 11:52:41', 3, 299, 37964, 172, 1),
(423, 370, '2024-03-16 11:54:12', 9, 299, 29666, 0, 1),
(424, 252, '2024-03-16 11:55:19', 1, 299, 35426, 1, 1),
(425, 5695, '2024-03-16 12:40:09', 1, 299, 232110, 915, 1),
(426, 220008, '2024-03-16 13:01:14', 9, 299, 1215808, 5035, 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`IdAccount`);

--
-- Index pour la table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`IdActivity`);

--
-- Index pour la table `scores`
--
ALTER TABLE `scores`
  ADD PRIMARY KEY (`IdScore`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `account`
--
ALTER TABLE `account`
  MODIFY `IdAccount` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=311;

--
-- AUTO_INCREMENT pour la table `activities`
--
ALTER TABLE `activities`
  MODIFY `IdActivity` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `scores`
--
ALTER TABLE `scores`
  MODIFY `IdScore` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=427;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
