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
    `LOGIN` VARCHAR(50) DEFAULT NULL,
    `TITRE` VARCHAR(100) DEFAULT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=INNODB AUTO_INCREMENT=15 DEFAULT CHARSET=UTF8;

-- Listage des données de la table travel.ARTICLE : ~9 rows (environ)
INSERT INTO `ARTICLE` (
    `ID`,
    `CONTENU`,
    `DATEP`,
    `DATEM`,
    `LOGIN`,
    `TITRE`
) VALUES (
    3,
    'Vreeuument ?!',
    '2023-03-16',
    '2023-03-31',
    'user2',
    'Je viens dÊTRE MODIFIÉ'
),
(
    4,
    'DÉCOUVREZ LA BEAUTÉ ÉPOUSTOUFLANTE DE LA PATAGONIE',
    '2023-03-16',
    NULL,
    'USER3',
    'LES MERVEILLES DE LA PATAGONIE'
),
(
    5,
    'DÉCOUVREZ LA VILLE DE Lamour en gondole',
    '2023-03-16',
    NULL,
    'user2',
    'Escapade romantique à Venise'
),
(
    6,
    'Une liste de restaurants incontournables à Barcelone',
    '2023-03-16',
    NULL,
    'user3',
    'Les meilleurs restaurants de Barcelone'
),
(
    7,
    'Explorez les incroyables temples d\'ANGKOR AU CAMBODGE', '2023-03-16', NULL, 'USER2', 'A LA DÉCOUVERTE DES TEMPLES D\'Angkor'
),
(
    8,
    'Découvrez les plages les plus pittoresques de Bali',
    '2023-03-16',
    NULL,
    'user3',
    'Les plus belles plages de Bali'
),
(
    9,
    'Une liste des activités incontournables à faire à New York',
    '2023-03-16',
    NULL,
    'user2',
    'Les meilleures activités à faire à New York'
),
(
    10,
    'Explorez la culture fascinante du Japon',
    '2023-03-16',
    NULL,
    'user3',
    'A la découverte de la culture japonaise'
),
(
    11,
    'Découvrez la gastronomie française dans la ville de la gastronomie',
    '2023-03-16',
    NULL,
    'user2',
    'Voyage culinaire à Paris'
),
(
    15,
    'Le restaurant XYZ à New York est un must-see pour tous les gourmets en visite dans la ville. Leurs plats sont délicieux et leur service est excellent. Nous avons adoré notre expérience ici !',
    '2023-03-31',
    NULL,
    'Alice',
    'Découverte du restaurant XYZ à New York'
),
(
    16,
    'Découvrez les plus belles plages de Bali',
    '2023-03-31',
    NULL,
    'Alice',
    'Les plages de Bali'
),
(
    17,
    'Les meilleures adresses pour manger à New York',
    '2023-03-31',
    NULL,
    'Bob',
    'Les restaurants à New York'
),
(
    18,
    'Les activités incontournables à faire à Paris',
    '2023-03-31',
    NULL,
    'Charlie',
    'Que faire à Paris'
),
(
    19,
    'Comment préparer un voyage en Thaïlande',
    '2023-03-31',
    NULL,
    'David',
    'Voyage en Thaïlande'
),
(
    20,
    'Les plus beaux sites archéologiques en Grèce',
    '2023-03-31',
    NULL,
    'Eve',
    'Grèce antique'
),
(
    21,
    'Top 10 des endroits à visiter à Tokyo',
    '2023-03-31',
    NULL,
    'Frank',
    'Tokyo'
),
(
    22,
    'Les meilleures randonnées à faire en Corse',
    '2023-03-31',
    NULL,
    'Grace',
    'Randonnées en Corse'
),
(
    23,
    'Les plus belles plages de la côte d\'AZUR', '2023-03-31', NULL, 'HENRY', 'LA CÔTE D\'Azur'
),
(
    24,
    'Comment visiter les monuments de Rome',
    '2023-03-31',
    NULL,
    'Isabelle',
    'Rome'
),
(
    25,
    'Les activités à ne pas manquer à Londres',
    '2023-03-31',
    NULL,
    'John',
    'Londres'
),
(
    26,
    'Les spécialités culinaires à découvrir en Italie',
    '2023-03-31',
    NULL,
    'Kate',
    'Cuisine italienne'
),
(
    27,
    'Les sites naturels à visiter en Islande',
    '2023-03-31',
    NULL,
    'Lucas',
    'Islande'
),
(
    28,
    'Comment organiser un road-trip en Californie',
    '2023-03-31',
    NULL,
    'Marie',
    'Californie'
),
(
    29,
    'Les activités à faire en famille à Barcelone',
    '2023-03-31',
    NULL,
    'Nathan',
    'Barcelone en famille'
),
(
    30,
    'Les endroits les plus romantiques de Venise',
    '2023-03-31',
    NULL,
    'Olivia',
    'Venise romantique'
),
(
    31,
    'Les plus beaux marchés de Noël en Allemagne',
    '2023-03-31',
    NULL,
    'Alice',
    'Marchés de Noël allemands'
),
(
    32,
    'Les plus beaux paysages du Canada',
    '2023-03-31',
    NULL,
    'Bob',
    'Canada'
),
(
    33,
    'Les activités à faire à Amsterdam',
    '2023-03-31',
    NULL,
    'Charlie',
    'Amsterdam'
),
(
    34,
    'Les endroits à visiter à Séoul',
    '2023-03-31',
    NULL,
    'David',
    'Séoul'
),
(
    35,
    'Comment organiser un voyage en Australie',
    '2023-03-31',
    NULL,
    'Eve',
    'Australie'
),
(
    36,
    'Les meilleurs spots de surf à Hawaii',
    '2023-03-31',
    NULL,
    'Frank',
    'Surf à Hawaii'
),
(
    37,
    'Les meilleures adresses pour manger à Barcelone',
    '2023-03-31',
    NULL,
    'Grace',
    'Manger à Barcelone'
),
(
    38,
    'Les parcs dATTRACTIONS À DÉCOUVRIR EN FLORIDE',
    '2023-03-31',
    NULL,
    'HENRY',
    'FLORIDE'
),
(
    39,
    'LES SITES À VISITER EN CHINE',
    '2023-03-31',
    NULL,
    'ISABELLE',
    'CHINE'
),
(
    40,
    'COMMENT PRÉPARER UN VOYAGE EN INDE',
    '2023-03-31',
    NULL,
    'JOHN',
    'VOYAGE EN INDE'
),
(
    41,
    'LES ENDROITS À VISITER EN SUISSE',
    '2023-03-31',
    NULL,
    'KATE',
    'SUISSE'
),
(
    42,
    'LES MEILLEURS HÔTELS POUR UN SÉJOUR EN THAÏLANDE',
    '2023-03-31',
    NULL,
    'LUCAS',
    'HÔTELS EN THAÏLANDE'
),
(
    43,
    'LES ACTIVITÉS À FAIRE À SYDNEY',
    '2023-03-31',
    NULL,
    'MARIE',
    'SYDNEY'
),
(
    44,
    'LES RANDONNÉES À FAIRE DANS LES ALPES',
    '2023-03-31',
    NULL,
    'NATHAN',
    'ALPES'
),
(
    45,
    'LES MEILLEURS RESTAURANTS DE FRUITS DE MER À MARSEILLE',
    '2023-03-31',
    NULL,
    'OLIVIA',
    'FRUITS DE MER À MARSEILLE'
),
(
    46,
    '10 RAISONS DE VISITER KYOTO',
    '2023-03-31',
    NULL,
    'ALICE',
    'KYOTO,
    JAPON'
),
(
    47,
    'LES MEILLEURS RESTAURANTS À SÉOUL',
    '2023-03-31',
    NULL,
    'BOB',
    'SÉOUL,
    CORÉE DU SUD'
),
(
    48,
    'LES ACTIVITÉS INCONTOURNABLES À MARRAKECH',
    '2023-03-31',
    NULL,
    'CHARLIE',
    'MARRAKECH,
    MAROC'
),
(
    49,
    'LES PLUS BELLES PLAGES DE BALI',
    '2023-03-31',
    NULL,
    'DAVID',
    'BALI,
    INDONÉSIE'
),
(
    50,
    'UN WEEK-END À LISBONNE : QUE VOIR ET FAIRE ?',
    '2023-03-31',
    NULL,
    'ALICE',
    'LISBONNE,
    PORTUGAL'
),
(
    51,
    'LES TRÉSORS CACHÉS DE NEW YORK',
    '2023-03-31',
    NULL,
    'BOB',
    'NEW YORK,
    ÉTATS-UNIS'
),
(
    52,
    'QUE FAIRE À BERLIN EN 3 JOURS ?',
    '2023-03-31',
    NULL,
    'CHARLIE',
    'BERLIN,
    ALLEMAGNE'
),
(
    53,
    'LES MEILLEURS SPOTS DE SURF À HAWAÏ',
    '2023-03-31',
    NULL,
    'DAVID',
    'HAWAÏ,
    ÉTATS-UNIS'
),
(
    54,
    'UN ROAD TRIP EN TOSCANE : ITINÉRAIRE ET CONSEILS',
    '2023-03-31',
    NULL,
    'ALICE',
    'TOSCANE,
    ITALIE'
),
(
    55,
    'LES PARCS NATIONAUX À NE PAS MANQUER EN AUSTRALIE',
    '2023-03-31',
    NULL,
    'BOB',
    'AUSTRALIE'
);

-- Listage de la structure de table travel. REAGIR
CREATE TABLE IF NOT EXISTS `REAGIR` (
    `LOGIN` VARCHAR(50) NOT NULL,
    `ID` INT(11) NOT NULL,
    `LIKES` TINYINT(1) NOT NULL,
    PRIMARY KEY (`LOGIN`, `ID`)
) ENGINE=INNODB DEFAULT CHARSET=UTF8;

-- Listage des données de la table travel.REAGIR : ~5 rows (environ)
INSERT INTO `REAGIR` (
    `LOGIN`,
    `ID`,
    `LIKES`
) VALUES (
    'alice',
    3,
    1
),
(
    'alice',
    10,
    1
),
(
    'alice',
    19,
    1
),
(
    'alice',
    22,
    1
),
(
    'alice',
    25,
    1
),
(
    'alice',
    32,
    1
),
(
    'alice',
    37,
    1
),
(
    'alice',
    38,
    1
),
(
    'alice',
    46,
    1
),
(
    'alice',
    47,
    1
),
(
    'alice',
    48,
    1
),
(
    'bob',
    5,
    -1
),
(
    'bob',
    9,
    -1
),
(
    'bob',
    11,
    -1
),
(
    'bob',
    15,
    -1
),
(
    'bob',
    26,
    -1
),
(
    'bob',
    28,
    -1
),
(
    'bob',
    30,
    -1
),
(
    'bob',
    39,
    -1
),
(
    'bob',
    40,
    -1
),
(
    'bob',
    50,
    -1
),
(
    'charlie',
    7,
    1
),
(
    'charlie',
    12,
    1
),
(
    'charlie',
    16,
    1
),
(
    'charlie',
    17,
    1
),
(
    'charlie',
    20,
    1
),
(
    'charlie',
    33,
    1
),
(
    'charlie',
    34,
    1
),
(
    'charlie',
    36,
    1
),
(
    'charlie',
    42,
    1
),
(
    'charlie',
    49,
    1
),
(
    'david',
    8,
    -1
),
(
    'david',
    14,
    -1
),
(
    'david',
    18,
    -1
),
(
    'david',
    23,
    -1
),
(
    'david',
    24,
    -1
),
(
    'david',
    27,
    -1
),
(
    'david',
    35,
    -1
),
(
    'david',
    41,
    -1
),
(
    'david',
    44,
    -1
),
(
    'david',
    45,
    -1
),
(
    'user1',
    2,
    1
),
(
    'user2',
    2,
    -1
),
(
    'user2',
    4,
    -1
),
(
    'user2',
    6,
    1
),
(
    'user2',
    8,
    -1
);

-- Listage de la structure de table travel. REPONSE
CREATE TABLE IF NOT EXISTS `REPONSE` (
    `ID` INT(11) NOT NULL AUTO_INCREMENT,
    `CONTENU` TEXT NOT NULL,
    `DATEP` DATE NOT NULL,
    `ID_ARTICLE` INT(11) NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=INNODB DEFAULT CHARSET=UTF8;

-- Listage des données de la table travel.REPONSE : ~0 rows (environ)

-- Listage de la structure de table travel. users
CREATE TABLE IF NOT EXISTS `USERS` (
    `LOGIN` VARCHAR(50) NOT NULL,
    `MDP` VARCHAR(100) NOT NULL,
    `ROLE` INT(11) NOT NULL,
    PRIMARY KEY (`LOGIN`)
) ENGINE=INNODB DEFAULT CHARSET=UTF8;

-- Listage des données de la table travel.users : ~3 rows (environ)
INSERT INTO `USERS` (
    `LOGIN`,
    `MDP`,
    `ROLE`
) VALUES (
    'Alice',
    'alicepwd',
    1
),
(
    'Bob',
    'bobpwd',
    0
),
(
    'Charlie',
    'charliepwd',
    1
),
(
    'David',
    'davidpwd',
    0
),
(
    'Eve',
    'evepwd',
    1
),
(
    'Frank',
    'frankpwd',
    0
),
(
    'Grace',
    'gracepwd',
    1
),
(
    'Henry',
    'henrypwd',
    0
),
(
    'Isabelle',
    'isabellepwd',
    1
),
(
    'John',
    'johnpwd',
    0
),
(
    'Kate',
    'katepwd',
    1
),
(
    'Lucas',
    'lucaspwd',
    0
),
(
    'Marie',
    'mariepwd',
    1
),
(
    'Nathan',
    'nathanpwd',
    0
),
(
    'Olivia',
    'oliviapwd',
    1
),
(
    'user1',
    'mdp1',
    1
),
(
    'user2',
    'mdp2',
    1
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
    DELIMITER // CREATE TRIGGER `ARTICLE_AFTER_UPDATE` BEFORE
        UPDATE ON `ARTICLE` FOR EACH ROW SET NEW.DATEM = NOW()// DELIMITER;
        SET SQL_MODE=@OLDTMP_SQL_MODE;
 /*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
 /*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
 /*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
 /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
 /*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;