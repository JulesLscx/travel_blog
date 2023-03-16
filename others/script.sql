-- --------------------------------------------------------
-- Hôte:                         15.188.174.107
-- Version du serveur:           5.5.68-MariaDB - MariaDB Server
-- SE du serveur:                Linux
-- HeidiSQL Version:             12.3.0.6589
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;

/*!40101 SET NAMES utf8 */;

/*!50503 SET NAMES utf8mb4 */;

/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;

/*!40103 SET TIME_ZONE='+00:00' */;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Listage de la structure de la base pour travel
CREATE DATABASE IF NOT EXISTS `TRAVEL` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_bin */;

USE `TRAVEL`;

-- Listage de la structure de table travel. ARTICLE
CREATE TABLE IF NOT EXISTS `ARTICLE` (
    `ID` INT(11) NOT NULL AUTO_INCREMENT,
    `CONTENU` TEXT NOT NULL,
    `DATEP` DATE DEFAULT NULL,
    `DATEM` DATE DEFAULT NULL,
    `DATES` DATE DEFAULT NULL,
    `LOGIN` VARCHAR(50) DEFAULT NULL,
    `TITRE` VARCHAR(100) DEFAULT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=INNODB AUTO_INCREMENT=13 DEFAULT CHARSET=UTF8;

-- Listage des données de la table travel.ARTICLE : ~1 rows (environ)
INSERT INTO `ARTICLE` (
    `ID`,
    `CONTENU`,
    `DATEP`,
    `DATEM`,
    `DATES`,
    `LOGIN`,
    `TITRE`
) VALUES (
    2,
    'J adore le voyage',
    '2023-03-15',
    NULL,
    NULL,
    'user1',
    NULL
),
(
    3,
    'Vraiment adoré ce voyage en Israel',
    '2023-03-16',
    NULL,
    NULL,
    'user2',
    'Road trip'
),
(
    4,
    'Découvrez la beauté époustouflante de la Patagonie',
    '2023-03-16',
    NULL,
    NULL,
    'user3',
    'Les merveilles de la Patagonie'
),
(
    5,
    'Découvrez la ville de l AMOUR EN GONDOLE',
    '2023-03-16',
    NULL,
    NULL,
    'USER2',
    'ESCAPADE ROMANTIQUE À VENISE'
),
(
    6,
    'UNE LISTE DE RESTAURANTS INCONTOURNABLES À BARCELONE',
    '2023-03-16',
    NULL,
    NULL,
    'USER3',
    'LES MEILLEURS RESTAURANTS DE BARCELONE'
),
(
    7,
    'EXPLOREZ LES INCROYABLES TEMPLES D Angkor au Cambodge',
    '2023-03-16',
    NULL,
    NULL,
    'user2',
    'A la découverte des temples d ANGKOR'
),
(
    8,
    'DÉCOUVREZ LES PLAGES LES PLUS PITTORESQUES DE BALI',
    '2023-03-16',
    NULL,
    NULL,
    'USER3',
    'LES PLUS BELLES PLAGES DE BALI'
),
(
    9,
    'UNE LISTE DES ACTIVITÉS INCONTOURNABLES À FAIRE À NEW YORK',
    '2023-03-16',
    NULL,
    NULL,
    'USER2',
    'LES MEILLEURES ACTIVITÉS À FAIRE À NEW YORK'
),
(
    10,
    'EXPLOREZ LA CULTURE FASCINANTE DU JAPON',
    '2023-03-16',
    NULL,
    NULL,
    'USER3',
    'A LA DÉCOUVERTE DE LA CULTURE JAPONAISE'
),
(
    11,
    'DÉCOUVREZ LA GASTRONOMIE FRANÇAISE DANS LA VILLE DE LA GASTRONOMIE',
    '2023-03-16',
    NULL,
    NULL,
    'USER2',
    'VOYAGE CULINAIRE À PARIS'
),
(
    12,
    'DÉCOUVREZ LES PLUS BELLES RANDONNÉES EN MONTAGNE',
    '2023-03-16',
    NULL,
    NULL,
    'USER3',
    'LES MEILLEURES RANDONNÉES EN MONTAGNE'
);

-- Listage de la structure de table travel. REAGIR
CREATE TABLE IF NOT EXISTS `REAGIR` (
    `LOGIN` VARCHAR(50) NOT NULL,
    `ID` INT(11) NOT NULL,
    `LIKES` TINYINT(1) NOT NULL,
    PRIMARY KEY (`LOGIN`, `ID`)
) ENGINE=INNODB DEFAULT CHARSET=UTF8;

-- Listage des données de la table travel.REAGIR : ~0 rows (environ)

-- Listage de la structure de table travel. REPONSE
CREATE TABLE IF NOT EXISTS `REPONSE` (
    `ID` INT(11) NOT NULL AUTO_INCREMENT,
    `CONTENU` TEXT NOT NULL,
    `DATEP` DATE NOT NULL,
    `ID_ARTICLE` INT(11) NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=INNODB DEFAULT CHARSET=UTF8;

-- Listage des données de la table travel.REPONSE : ~0 rows (environ)

-- Listage de la structure de table travel. TAG
CREATE TABLE IF NOT EXISTS `TAG` (
    `TAG` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`TAG`)
) ENGINE=INNODB DEFAULT CHARSET=UTF8;

INSERT INTO `TAG` (
    `TAG`
) VALUES (
    'affaire'
),
(
    'agence'
),
(
    'agence de voyages'
),
(
    'aiguade'
),
(
    'Angkor'
),
(
    'bagage'
),
(
    'Bali'
),
(
    'Barcelone'
),
(
    'biscuit'
),
(
    'Bordeaux'
),
(
    'campagne'
),
(
    'cange'
),
(
    'carnet'
),
(
    'carnet de voyage'
),
(
    'Chine'
),
(
    'citytrip'
),
(
    'compagnon'
),
(
    'couch surfing'
),
(
    'cyclotourisme'
),
(
    'déplacement'
),
(
    'dînée'
),
(
    'errance'
),
(
    'escale'
),
(
    'étape'
),
(
    'expédition'
),
(
    'explorateur'
),
(
    'exploration'
),
(
    'géographique'
),
(
    'Inde'
),
(
    'Israël'
),
(
    'Japon'
),
(
    'naufrage'
),
(
    'navigation'
),
(
    'New York'
),
(
    'noce'
),
(
    'nuit'
),
(
    'Paris'
),
(
    'passage'
),
(
    'Patagonie'
),
(
    'pèlerin'
),
(
    'pèlerinage'
),
(
    'périple'
),
(
    'Randonnée'
),
(
    'relais'
),
(
    'retour'
),
(
    'sortie'
),
(
    'souvenir'
),
(
    'tourisme'
),
(
    'touriste'
),
(
    'valise'
),
(
    'Venise'
),
(
    'voyager'
),
(
    'voyageur'
),
(
    'voyagiste'
);

-- Listage de la structure de table travel. TAGS
CREATE TABLE IF NOT EXISTS `TAGS` (
    `ID` INT(11) NOT NULL,
    `TAG` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`ID`, `TAG`)
) ENGINE=INNODB DEFAULT CHARSET=UTF8;

-- Listage des données de la table travel.TAGS : ~0 rows (environ)
INSERT INTO `TAGS` (
    `ID`,
    `TAG`
) VALUES (
    3,
    'Israël'
),
(
    4,
    'Patagonie'
),
(
    5,
    'Venise'
),
(
    6,
    'Barcelone'
),
(
    7,
    'Angkor'
),
(
    8,
    'Bali'
),
(
    9,
    'New York'
),
(
    10,
    'Japon'
),
(
    11,
    'Paris'
),
(
    12,
    'Randonnée'
);

-- Listage de la structure de table travel. users
CREATE TABLE IF NOT EXISTS `USERS` (
    `LOGIN` VARCHAR(50) NOT NULL,
    `MDP` VARCHAR(100) NOT NULL,
    `ROLE` INT(11) NOT NULL,
    PRIMARY KEY (`LOGIN`)
) ENGINE=INNODB DEFAULT CHARSET=UTF8;

-- Listage des données de la table travel.users : ~2 rows (environ)
INSERT INTO `USERS` (
    `LOGIN`,
    `MDP`,
    `ROLE`
) VALUES (
    'user1',
    'mdp1',
    0
),
(
    'user2',
    'mdp2',
    0
),
(
    'user3',
    'mdp3',
    0
);

-- Listage de la structure de déclencheur travel. ARTICLE_AFTER_INSERT
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';

DELIMITER

/

/

CREATE TRIGGER `ARTICLE_AFTER_INSERT` BEFORE
    INSERT ON `ARTICLE` FOR EACH ROW SET NEW.DATEP = NOW()// DELIMITER;
    SET SQL_MODE=@OLDTMP_SQL_MODE;
 -- Listage de la structure de déclencheur travel. ARTICLE_AFTER_UPDATE
    SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
    DELIMITER // CREATE TRIGGER `ARTICLE_AFTER_UPDATE` AFTER
        UPDATE ON `ARTICLE` FOR EACH ROW
            UPDATE ARTICLE SET DATEM = NOW() WHERE ID = NEW.ID// DELIMITER;
            SET SQL_MODE=@OLDTMP_SQL_MODE;
 /*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
 /*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
 /*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
 /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
 /*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;