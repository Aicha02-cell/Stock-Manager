-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 18 juil. 2024 à 20:17
-- Version du serveur : 5.7.40
-- Version de PHP : 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_stock`
--

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

DROP TABLE IF EXISTS `article`;
CREATE TABLE IF NOT EXISTS `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_article` varchar(50) NOT NULL,
  `id_categorie` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix_unitaire` int(11) NOT NULL,
  `date_fabrication` datetime NOT NULL,
  `date_expiration` datetime NOT NULL,
  `images` varchar(255) NOT NULL,
  `nom_fournisseur` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle_categorie` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `libelle_categorie`) VALUES
(7, 'AB'),
(6, 'AC');

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_commande` int(11) DEFAULT NULL,
  `nom_client` varchar(100) DEFAULT NULL,
  `adresse_client` varchar(255) DEFAULT NULL,
  `telephone_client` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_commande` (`id_commande`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

DROP TABLE IF EXISTS `commande`;
CREATE TABLE IF NOT EXISTS `commande` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prenom` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `adresse` text NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `date_commande` date NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `statut` varchar(100) DEFAULT NULL,
  `date_acceptation` date DEFAULT NULL,
  `date_livraison` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=85 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `commande`
--

INSERT INTO `commande` (`id`, `prenom`, `nom`, `adresse`, `telephone`, `date_commande`, `total`, `statut`, `date_acceptation`, `date_livraison`) VALUES
(84, 'Mamou', 'GAMB', 'Dougoucoro', '58455552', '2024-07-18', '9025000.00', 'acceptee', NULL, NULL),
(83, 'Nounours', 'Bl', 'ATTBOUGOU', '77455065', '2024-07-18', '3111000.00', 'acceptee', NULL, NULL),
(82, 'Nounours', 'Bl', 'ATTBOUGOU', '77455065', '2024-07-17', '1503000.00', 'acceptee', NULL, NULL),
(81, 'kadi', 'Doumbia', 'ATTBOUGOU', '77455065', '2024-07-16', '1503000.00', 'acceptee', NULL, NULL),
(80, 'kdi', 'konÃ©', 'niamana', '77455065', '2024-07-16', '8000.00', 'acceptee', NULL, NULL),
(79, 'aichata ', 'Sanogo', 'ATC', '72721226', '2024-07-16', '548000.00', 'acceptee', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

DROP TABLE IF EXISTS `commandes`;
CREATE TABLE IF NOT EXISTS `commandes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_article` int(11) NOT NULL,
  `id_fournisseur` int(11) NOT NULL,
  `nom_client` varchar(100) NOT NULL,
  `prenom_client` varchar(100) NOT NULL,
  `telephone_client` varchar(20) NOT NULL,
  `adresse_client` text,
  `quantite` int(11) NOT NULL,
  `prix_total` decimal(10,2) NOT NULL,
  `date_commande` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `statut` varchar(100) NOT NULL,
  `delai_livraison` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `commande_admin`
--

DROP TABLE IF EXISTS `commande_admin`;
CREATE TABLE IF NOT EXISTS `commande_admin` (
  `id_commande` int(11) NOT NULL AUTO_INCREMENT,
  `nom_article` varchar(100) DEFAULT NULL,
  `id_fournisseur` int(11) DEFAULT NULL,
  `prenom` varchar(50) DEFAULT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` varchar(100) DEFAULT NULL,
  `quantite` int(11) DEFAULT NULL,
  `prix` decimal(10,2) DEFAULT NULL,
  `date_commande` varchar(100) DEFAULT NULL,
  `date_livraison` varchar(100) NOT NULL,
  PRIMARY KEY (`id_commande`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `commande_article`
--

DROP TABLE IF EXISTS `commande_article`;
CREATE TABLE IF NOT EXISTS `commande_article` (
  `id_commande` int(11) NOT NULL,
  `id_article` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_commande`,`id_article`),
  KEY `id_article` (`id_article`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `commande_article`
--

INSERT INTO `commande_article` (`id_commande`, `id_article`, `quantite`, `prix`) VALUES
(82, 16, 300, '5000.00'),
(78, 16, 80, '5000.00'),
(78, 15, 32, '3000.00'),
(77, 15, 2, '3000.00'),
(77, 16, 50, '5000.00'),
(76, 15, 2, '3000.00'),
(76, 16, 20, '5000.00'),
(75, 15, 2, '3000.00'),
(75, 16, 20, '5000.00'),
(74, 15, 1, '3000.00'),
(74, 16, 12, '5000.00'),
(82, 15, 1, '3000.00'),
(81, 16, 300, '5000.00'),
(81, 15, 1, '3000.00'),
(80, 15, 1, '3000.00'),
(80, 16, 1, '5000.00'),
(79, 16, 109, '5000.00'),
(79, 15, 1, '3000.00'),
(83, 15, 1037, '3000.00'),
(84, 16, 5, '5000.00'),
(84, 15, 3000, '3000.00');

-- --------------------------------------------------------

--
-- Structure de la table `commande_clients`
--

DROP TABLE IF EXISTS `commande_clients`;
CREATE TABLE IF NOT EXISTS `commande_clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_article` int(11) DEFAULT NULL,
  `id_fournisseur` int(11) DEFAULT NULL,
  `id_client` int(11) DEFAULT NULL,
  `quantite` int(11) DEFAULT NULL,
  `prix_total` decimal(10,2) DEFAULT NULL,
  `date_commande` date DEFAULT NULL,
  `statut` enum('attente','confirmee','annulee') DEFAULT 'attente',
  PRIMARY KEY (`id`),
  KEY `id_article` (`id_article`),
  KEY `id_fournisseur` (`id_fournisseur`),
  KEY `id_client` (`id_client`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `details_commandes`
--

DROP TABLE IF EXISTS `details_commandes`;
CREATE TABLE IF NOT EXISTS `details_commandes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_commande` int(11) NOT NULL,
  `id_article` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_commande` (`id_commande`),
  KEY `id_article` (`id_article`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `detail_reception`
--

DROP TABLE IF EXISTS `detail_reception`;
CREATE TABLE IF NOT EXISTS `detail_reception` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reception_id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `reception_id` (`reception_id`),
  KEY `article_id` (`article_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `detail_reception`
--

INSERT INTO `detail_reception` (`id`, `reception_id`, `article_id`, `quantite`) VALUES
(1, 6, 3, 100);

-- --------------------------------------------------------

--
-- Structure de la table `expedition`
--

DROP TABLE IF EXISTS `expedition`;
CREATE TABLE IF NOT EXISTS `expedition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_exp` int(11) NOT NULL,
  `client` varchar(255) NOT NULL,
  `produit` varchar(255) NOT NULL,
  `quantite` int(11) NOT NULL,
  `date_exp` date NOT NULL,
  `statut` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `expedition`
--

INSERT INTO `expedition` (`id`, `id_exp`, `client`, `produit`, `quantite`, `date_exp`, `statut`) VALUES
(1, 0, 'client1', '', 0, '0000-00-00', 'En Attente'),
(2, 0, 'client1', '', 0, '0000-00-00', 'En Attente'),
(3, 0, 'client1', 'boisson', 100, '2024-06-02', 'En Attente'),
(4, 0, 'client1', 'boisson', 10, '2024-06-02', 'En Attente'),
(5, 0, 'client1', 'boisson', 10, '2024-06-02', 'En Attente'),
(6, 0, 'client1', 'boisson', 10, '2024-06-02', 'En Attente'),
(7, 0, 'client1', 'boisson', 200, '2024-06-02', ''),
(8, 0, 'client1', 'boisson', 200, '2024-06-02', ''),
(9, 0, 'client1', 'boisson', 200, '2024-06-02', ''),
(10, 0, 'client1', 'boisson', 200, '2024-06-02', ''),
(11, 0, 'client1', 'boisson', 200, '2024-06-01', ''),
(12, 0, 'client1', 'boisson', 200, '2024-06-01', ''),
(13, 0, 'client1', 'boisson', 200, '2024-06-01', ''),
(14, 0, 'client1', '', 0, '0000-00-00', 'En cours');

-- --------------------------------------------------------

--
-- Structure de la table `fournisseurs`
--

DROP TABLE IF EXISTS `fournisseurs`;
CREATE TABLE IF NOT EXISTS `fournisseurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `adresse` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `fournisseurs`
--

INSERT INTO `fournisseurs` (`id`, `nom`, `contact`, `adresse`, `email`) VALUES
(14, 'Hawa ', '58545756', 'ATC', 'hawa@gmail.com'),
(15, 'Sanogo', '77455065', 'ATC', 'Sanogo1234@gmail.com'),
(16, 'kadi Doumbia', '72722216', 'ATTBOUGOU', 'jojo12@gmail.com');

-- --------------------------------------------------------

--
-- Structure de la table `fournisseur_article`
--

DROP TABLE IF EXISTS `fournisseur_article`;
CREATE TABLE IF NOT EXISTS `fournisseur_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_fournisseur` int(11) DEFAULT NULL,
  `id_article` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_fournisseur` (`id_fournisseur`),
  KEY `id_article` (`id_article`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `fournisseur_article`
--

INSERT INTO `fournisseur_article` (`id`, `id_fournisseur`, `id_article`) VALUES
(1, 9, 9),
(2, 10, 10);

-- --------------------------------------------------------

--
-- Structure de la table `mouvements_stock`
--

DROP TABLE IF EXISTS `mouvements_stock`;
CREATE TABLE IF NOT EXISTS `mouvements_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_article` int(11) NOT NULL,
  `type` enum('entree','sortie') NOT NULL,
  `quantite` int(11) NOT NULL,
  `date_mouvement` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_article` (`id_article`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `mouvements_stock`
--

INSERT INTO `mouvements_stock` (`id`, `id_article`, `type`, `quantite`, `date_mouvement`) VALUES
(1, 1, 'entree', 50, '2024-06-29 18:22:05'),
(2, 1, 'sortie', 10, '2024-06-29 18:22:05'),
(3, 1, 'entree', 50, '2024-06-29 18:22:07'),
(4, 1, 'sortie', 10, '2024-06-29 18:22:07'),
(5, 1, 'entree', 50, '2024-06-29 18:29:42'),
(6, 1, 'sortie', 10, '2024-06-29 18:29:42'),
(7, 1, 'entree', 50, '2024-06-29 18:29:43'),
(8, 1, 'sortie', 10, '2024-06-29 18:29:43'),
(9, 1, 'entree', 50, '2024-06-29 18:29:45'),
(10, 1, 'sortie', 10, '2024-06-29 18:29:45'),
(11, 1, 'entree', 50, '2024-06-29 18:29:49'),
(12, 1, 'sortie', 10, '2024-06-29 18:29:49'),
(13, 1, 'entree', 50, '2024-06-29 18:30:55'),
(14, 1, 'sortie', 10, '2024-06-29 18:30:55'),
(15, 1, 'entree', 50, '2024-06-29 18:30:58'),
(16, 1, 'sortie', 10, '2024-06-29 18:30:58');

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

DROP TABLE IF EXISTS `produit`;
CREATE TABLE IF NOT EXISTS `produit` (
  `id_produit` int(11) NOT NULL AUTO_INCREMENT,
  `code_produit` varchar(100) DEFAULT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `prix` varchar(100) DEFAULT NULL,
  `unite_mesure` varchar(100) DEFAULT NULL,
  `code_barre` varchar(100) DEFAULT NULL,
  `quantite` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_produit`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `produit`
--

INSERT INTO `produit` (`id_produit`, `code_produit`, `nom`, `description`, `categorie`, `prix`, `unite_mesure`, `code_barre`, `quantite`) VALUES
(45, 'c1963', 'Lait en poudre', 'cest du lait', 'B', '10000', '10litre', '5', '462'),
(44, 'B10', 'chaussure', 'porte', 'vetement', '100000', '10', '5', '30');

-- --------------------------------------------------------

--
-- Structure de la table `reception`
--

DROP TABLE IF EXISTS `reception`;
CREATE TABLE IF NOT EXISTS `reception` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fournisseur_id` int(11) NOT NULL,
  `date_reception` date NOT NULL,
  `statut` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fournisseur_id` (`fournisseur_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `reception`
--

INSERT INTO `reception` (`id`, `fournisseur_id`, `date_reception`, `statut`, `created_at`) VALUES
(1, 2, '2024-06-29', 'reÃ§ue', '2024-06-29 00:49:57'),
(2, 2, '2024-06-29', 'reÃ§ue', '2024-06-29 00:52:53'),
(3, 2, '2024-06-29', 'reÃ§ue', '2024-06-29 00:56:00'),
(4, 2, '2024-06-29', 'reÃ§ue', '2024-06-29 00:56:05'),
(5, 2, '2024-06-29', 'reÃ§ue', '2024-06-29 00:56:10'),
(6, 2, '2024-06-29', 'reÃ§ue', '2024-06-29 01:05:12');

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `permissions` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `stocks`
--

DROP TABLE IF EXISTS `stocks`;
CREATE TABLE IF NOT EXISTS `stocks` (
  `StockID` int(11) NOT NULL AUTO_INCREMENT,
  `ProduitID` int(11) DEFAULT NULL,
  `QuantiteEnStock` int(11) DEFAULT NULL,
  `Date_mise_a_jour` date DEFAULT NULL,
  PRIMARY KEY (`StockID`),
  KEY `ProduitID` (`ProduitID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','supplier','client') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `age`, `password`, `role`, `created_at`) VALUES
(1, 'didia', 'kiatoukone20@gmail.com', 22, '$2y$10$YbNwF5cUOewQpkSdgg0WiezphBpiJ23bDjm5TCUM/qUkdAPxNkq1u', 'admin', '2024-06-24 16:48:19'),
(2, 'didia', 'kiatoukone20@gmail.com', 18, '$2y$10$1pKqFtY..rHRlkML/TtU6e1WpiDbm22vaA4lTN0WGmyBys2Rsbb1i', 'client', '2024-06-29 00:59:18'),
(3, 'didia', 'kiatoukone20@gmail.com', 18, '$2y$10$PQSe3MB78Qem4Jl.OAdkZeqzCme3q1/SMmDd1EWi8hMKfB8Xr.GsW', 'client', '2024-07-01 11:02:26'),
(4, 'didia', 'kiatoukone20@gmail.com', 18, '$2y$10$TfFQ5I6aUTujXyNQ6YeuneAe35RG8BWuYQRtKpjILJfLAvPTWggpq', 'supplier', '2024-07-01 11:03:54'),
(5, 'didia', 'kiatoukone20@gmail.com', 18, '$2y$10$7YB79oDmFvUl.3y2XqYyd.8a/U6e4J1K3iJWseU7A/lcCHgarNZ6a', 'admin', '2024-07-01 15:20:55'),
(6, 'kadi', 'kiatoukone20@gmail.com', 18, '$2y$10$wfV6ZzrScpE2cyE7u1YyXuoOd/5Nl0MmEaBhpjZ6U5TY4F9J1No8C', 'admin', '2024-07-02 17:43:21'),
(7, 'didia', 'kiatoukone20@gmail.com', 18, '$2y$10$WJ/7HQzWF7iPqnfMkrLzeeNtNSOdcQ9SdL39MkzT07VQe3AVC0hUW', 'admin', '2024-07-02 17:44:12'),
(8, 'didia', 'kiatoukone20@gmail.com', 18, '$2y$10$GXH8EZb6F6QDyPwup/agJe6aiO1rlWAOKHCRYiuzakv4hUJY9lN.2', 'client', '2024-07-03 14:56:55'),
(9, 'didia', 'kiatoukone20@gmail.com', 19, '$2y$10$TFhLma4/jDb2vqTfOpGAx.aqihwOb7ffS1Bha0cAGkdMxKDp/lO4S', 'admin', '2024-07-09 14:53:50'),
(10, 'kadidia', 'Sanogo1234@gmail.com', 20, '$2y$10$fnSA163W99p5E0rFgVVIneSCyl.th3v5yxQjyhYEDgwjRffJ6.xbi', 'admin', '2024-07-09 14:59:57'),
(11, 'didia', 'kiatoukone20@gmail.com', 20, '$2y$10$kjHh34pVFZhbuwRmnmxLoenMuOuGDLg98czpT3AimejGyzODxh9AW', 'admin', '2024-07-09 15:15:24'),
(12, 'didia', 'kiatoukone20@gmail.com', 20, '$2y$10$iUsCntYzeKLM9rHbVbRU0OsSQI3Mfpx.B3r.mTkpfQPWso4s6KWUi', 'admin', '2024-07-09 15:15:39'),
(13, 'AICHA', 'AICHA@gmail.com', 25, '$2y$10$yAGfM8BM15W6iIyZo0FSzuSJExU6btLsIPXDvUrOfJMoGj4Qy0lla', 'admin', '2024-07-14 20:08:26'),
(14, 'aicha', 'aicha@gmail.com', 23, '$2y$10$J05LKmDQRuslsQe1VLgzuOlazi53fjSwDLJt4aRTAS04m.8e6Qjbm', 'client', '2024-07-14 20:54:13');

-- --------------------------------------------------------

--
-- Structure de la table `ventes`
--

DROP TABLE IF EXISTS `ventes`;
CREATE TABLE IF NOT EXISTS `ventes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_article` int(11) DEFAULT NULL,
  `id_client` int(11) DEFAULT NULL,
  `quantite` int(11) DEFAULT NULL,
  `prix` decimal(10,2) DEFAULT NULL,
  `date_vente` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_article` (`id_article`),
  KEY `id_client` (`id_client`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `ventes`
--

INSERT INTO `ventes` (`id`, `id_article`, `id_client`, `quantite`, `prix`, `date_vente`) VALUES
(1, 3, 1, 2, '2000.00', '2024-06-29 02:59:44'),
(2, 3, 1, 2, '2000.00', '2024-06-29 03:07:10'),
(3, 4, 3, 20, '10000.00', '2024-06-30 22:05:01'),
(4, 8, 1, 20, '6000.00', '2024-07-01 14:14:38'),
(5, 9, 6, 100, '500000.00', '2024-07-02 17:58:33');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
