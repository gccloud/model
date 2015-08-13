-- phpMyAdmin SQL Dump
-- version 4.4.0
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Jeu 13 Août 2015 à 12:21
-- Version du serveur :  5.6.25-1~dotdeb+7.1
-- Version de PHP :  5.4.43-1~dotdeb+7.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `mymodelexample`
--

-- --------------------------------------------------------

--
-- Structure de la table `address`
--

CREATE TABLE IF NOT EXISTS `address` (
  `id` int(10) unsigned NOT NULL,
  `city_id` int(10) unsigned NOT NULL,
  `label` varchar(255) NOT NULL,
  `dateinsert` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `address`
--

INSERT INTO `address` (`id`, `city_id`, `label`, `dateinsert`) VALUES
(1, 1, '11 Traverse Joint', '2015-08-13 00:00:00'),
(2, 1, '22 Traverse Joint', '2015-08-13 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `city`
--

CREATE TABLE IF NOT EXISTS `city` (
  `id` int(10) unsigned NOT NULL,
  `country_id` int(10) unsigned NOT NULL,
  `label` varchar(255) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `dateinsert` datetime NOT NULL,
  `dateupdate` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `city`
--

INSERT INTO `city` (`id`, `country_id`, `label`, `zipcode`, `dateinsert`, `dateupdate`) VALUES
(1, 1, 'Test City', '98765', '2015-08-13 00:00:00', '2015-08-13 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `country`
--

CREATE TABLE IF NOT EXISTS `country` (
  `id` int(10) unsigned NOT NULL,
  `label` varchar(255) NOT NULL,
  `iso` varchar(255) NOT NULL,
  `dateinsert` datetime NOT NULL,
  `dateupdate` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `country`
--

INSERT INTO `country` (`id`, `label`, `iso`, `dateinsert`, `dateupdate`) VALUES
(1, 'Test Country', 'TC', '2015-08-13 00:00:00', '2015-08-13 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `enumusergroup_id`
--

CREATE TABLE IF NOT EXISTS `enumusergroup_id` (
  `id` int(10) unsigned NOT NULL,
  `label` varchar(255) NOT NULL,
  `constant` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `enumusergroup_id`
--

INSERT INTO `enumusergroup_id` (`id`, `label`, `constant`) VALUES
(1, 'Super Administrator', 'SUPERADMIN'),
(2, 'Administrator', 'ADMIN');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL,
  `enumusergroup_id` int(10) unsigned NOT NULL,
  `address_id` int(10) unsigned NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `dateinsert` datetime NOT NULL,
  `dateupdate` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `user`
--

INSERT INTO `user` (`id`, `enumusergroup_id`, `address_id`, `lastname`, `firstname`, `dateinsert`, `dateupdate`) VALUES
(1, 1, 1, 'Test', 'User', '2015-08-13 00:00:00', '2015-08-13 00:00:00'),
(2, 2, 2, 'Test 2', 'Test 2', '2015-08-13 00:00:00', '2015-08-13 00:00:00');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `city_id` (`city_id`);

--
-- Index pour la table `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`id`),
  ADD KEY `country_id` (`country_id`);

--
-- Index pour la table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `enumusergroup_id`
--
ALTER TABLE `enumusergroup_id`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `address_id` (`address_id`),
  ADD KEY `enumusergroup_id` (`enumusergroup_id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `address`
--
ALTER TABLE `address`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `city`
--
ALTER TABLE `city`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `country`
--
ALTER TABLE `country`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `enumusergroup_id`
--
ALTER TABLE `enumusergroup_id`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `city_id` FOREIGN KEY (`city_id`) REFERENCES `city` (`id`);

--
-- Contraintes pour la table `city`
--
ALTER TABLE `city`
  ADD CONSTRAINT `country_id` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`);

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `address_id` FOREIGN KEY (`address_id`) REFERENCES `address` (`id`),
  ADD CONSTRAINT `enumusergroup_id` FOREIGN KEY (`enumusergroup_id`) REFERENCES `enumusergroup_id` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
