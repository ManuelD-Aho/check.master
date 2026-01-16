-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 16 jan. 2026 à 11:59
-- Version du serveur : 8.4.7
-- Version de PHP : 8.4.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `checkmaster`
--

-- --------------------------------------------------------

--
-- Structure de la table `action`
--

DROP TABLE IF EXISTS `action`;
CREATE TABLE IF NOT EXISTS `action` (
  `id_action` int NOT NULL AUTO_INCREMENT,
  `lib_action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_action`),
  UNIQUE KEY `lib_action` (`lib_action`),
  KEY `idx_lib` (`lib_action`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `action`
--

INSERT INTO `action` (`id_action`, `lib_action`, `description`) VALUES
(1, 'Lire', 'Consulter et visualiser les données'),
(2, 'Creer', 'Créer de nouvelles entrées'),
(3, 'Modifier', 'Modifier les entrées existantes'),
(4, 'Supprimer', 'Supprimer des entrées'),
(5, 'Valider', 'Valider et approuver des entrées'),
(6, 'Exporter', 'Exporter des données vers des fichiers');

-- --------------------------------------------------------

--
-- Structure de la table `annee_academique`
--

DROP TABLE IF EXISTS `annee_academique`;
CREATE TABLE IF NOT EXISTS `annee_academique` (
  `id_annee_acad` int NOT NULL AUTO_INCREMENT,
  `lib_annee_acad` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `est_active` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_annee_acad`),
  UNIQUE KEY `lib_annee_acad` (`lib_annee_acad`),
  KEY `idx_active` (`est_active`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `annee_academique`
--

INSERT INTO `annee_academique` (`id_annee_acad`, `lib_annee_acad`, `date_debut`, `date_fin`, `est_active`) VALUES
(1, '2024-2025', '2024-09-01', '2025-08-31', 1),
(2, '2025-2026', '2025-09-01', '2026-08-31', 0);

-- --------------------------------------------------------

--
-- Structure de la table `annotations_rapport`
--

DROP TABLE IF EXISTS `annotations_rapport`;
CREATE TABLE IF NOT EXISTS `annotations_rapport` (
  `id_annotation` int NOT NULL AUTO_INCREMENT,
  `rapport_id` int NOT NULL,
  `auteur_id` int NOT NULL,
  `page_numero` int DEFAULT NULL,
  `position_json` json DEFAULT NULL,
  `contenu` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_annotation` enum('Commentaire','Correction','Suggestion') COLLATE utf8mb4_unicode_ci DEFAULT 'Commentaire',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_annotation`),
  KEY `idx_rapport` (`rapport_id`),
  KEY `idx_auteur` (`auteur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `annotations_rapport`
--

INSERT INTO `annotations_rapport` (`id_annotation`, `rapport_id`, `auteur_id`, `page_numero`, `position_json`, `contenu`, `type_annotation`, `created_at`) VALUES
(1, 8, 6, 15, '{\"x\": 100, \"y\": 200}', 'Merci de préciser la source de ces données', 'Commentaire', '2026-01-16 10:27:05'),
(2, 8, 7, 22, '{\"x\": 150, \"y\": 300}', 'Formule incorrecte - vérifier le calcul', 'Correction', '2026-01-16 10:27:05'),
(3, 8, 8, 35, '{\"x\": 120, \"y\": 250}', 'Très bonne analyse, à développer davantage', 'Suggestion', '2026-01-16 10:27:05'),
(4, 9, 6, 10, '{\"x\": 80, \"y\": 180}', 'Introduction à étoffer', 'Suggestion', '2026-01-16 10:27:05'),
(5, 9, 10, 45, '{\"x\": 200, \"y\": 400}', 'Conclusion bien rédigée', 'Commentaire', '2026-01-16 10:27:05'),
(6, 10, 7, 8, '{\"x\": 90, \"y\": 150}', 'Référence bibliographique manquante', 'Correction', '2026-01-16 10:27:05'),
(7, 10, 11, 30, '{\"x\": 110, \"y\": 220}', 'Schéma à améliorer', 'Suggestion', '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `applied_sql_files`
--

DROP TABLE IF EXISTS `applied_sql_files`;
CREATE TABLE IF NOT EXISTS `applied_sql_files` (
  `filename` varchar(255) NOT NULL,
  `executed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `applied_sql_files`
--

INSERT INTO `applied_sql_files` (`filename`, `executed_at`) VALUES
('001_create_complete_database.sql', '2026-01-16 10:25:43'),
('001_referentiels_immuables.sql', '2026-01-16 10:27:04'),
('002_add_rapport_annotations.sql', '2026-01-16 10:27:02'),
('002_create_notifications_table.sql', '2026-01-16 10:27:02'),
('002_groupes_utilisateurs.sql', '2026-01-16 10:27:05'),
('003_add_commission_sessions.sql', '2026-01-16 11:56:10'),
('003_traitements_actions.sql', '2026-01-16 10:27:05'),
('004_add_exonerations.sql', '2026-01-16 10:27:02'),
('004_workflow_etats.sql', '2026-01-16 10:27:05'),
('005_add_permissions_actions.sql', '2026-01-16 10:27:02'),
('005_configuration_defaut.sql', '2026-01-16 10:27:05'),
('006_add_workflow_historique.sql', '2026-01-16 10:27:03'),
('006_notification_templates.sql', '2026-01-16 10:27:05'),
('007_add_imports_historiques.sql', '2026-01-16 11:56:10'),
('007_demo_data.sql', '2026-01-16 10:27:05'),
('008_add_maintenance_mode.sql', '2026-01-16 10:27:03'),
('008_entreprises_partenaires.sql', '2026-01-16 10:27:05'),
('009_add_stats_cache.sql', '2026-01-16 10:27:04'),
('009_enseignants.sql', '2026-01-16 10:27:05'),
('010_add_documents_generes.sql', '2026-01-16 10:27:04'),
('010_etudiants.sql', '2026-01-16 10:27:05'),
('011_add_sessions_commission_participants.sql', '2026-01-16 11:56:10'),
('011_personnel_admin.sql', '2026-01-16 10:27:05'),
('012_add_roles_temporaires.sql', '2026-01-16 10:27:04'),
('012_utilisateurs_complets.sql', '2026-01-16 10:27:05'),
('013_add_fulltext_indexes.sql', '2026-01-16 11:56:13'),
('013_ue_ecue.sql', '2026-01-16 10:27:05'),
('014_add_reclamations_audit_logs.sql', '2026-01-16 11:56:13'),
('014_dossiers_candidatures.sql', '2026-01-16 10:27:05'),
('015_rapports_commission.sql', '2026-01-16 10:27:05'),
('016_jury_soutenances.sql', '2026-01-16 10:27:05'),
('017_paiements_finances.sql', '2026-01-16 10:27:05'),
('018_workflow_historique.sql', '2026-01-16 10:27:05'),
('019_communications_messages.sql', '2026-01-16 10:27:05'),
('020_documents_archives.sql', '2026-01-16 10:27:05'),
('021_reclamations_escalades.sql', '2026-01-16 11:56:13');

-- --------------------------------------------------------

--
-- Structure de la table `archives`
--

DROP TABLE IF EXISTS `archives`;
CREATE TABLE IF NOT EXISTS `archives` (
  `id_archive` int NOT NULL AUTO_INCREMENT,
  `document_id` int NOT NULL,
  `hash_sha256` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verifie` tinyint(1) DEFAULT '1',
  `derniere_verification` datetime DEFAULT CURRENT_TIMESTAMP,
  `verrouille` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_archive`),
  KEY `document_id` (`document_id`),
  KEY `idx_verifie` (`verifie`),
  KEY `idx_verification` (`derniere_verification`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `archives`
--

INSERT INTO `archives` (`id_archive`, `document_id`, `hash_sha256`, `verifie`, `derniere_verification`, `verrouille`, `created_at`) VALUES
(1, 1, 'a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2', 1, '2024-12-01 02:00:00', 1, '2026-01-16 10:27:05'),
(2, 2, 'b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3', 1, '2024-12-01 02:00:00', 1, '2026-01-16 10:27:05'),
(3, 5, 'e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6', 1, '2024-12-01 02:00:00', 1, '2026-01-16 10:27:05'),
(4, 6, 'f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1', 1, '2024-12-01 02:00:00', 1, '2026-01-16 10:27:05'),
(5, 7, 'a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2', 1, '2024-12-01 02:00:00', 1, '2026-01-16 10:27:05'),
(6, 8, 'b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3', 1, '2024-12-15 02:00:00', 1, '2026-01-16 10:27:05'),
(7, 11, 'e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6', 1, '2024-12-15 02:00:00', 1, '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id_log` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entite_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entite_id` int DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `donnees_avant_json` json DEFAULT NULL,
  `donnees_apres_json` json DEFAULT NULL,
  `ip_adresse` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`),
  KEY `idx_utilisateur` (`utilisateur_id`),
  KEY `idx_entite` (`entite_type`,`entite_id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `audit_logs`
--

INSERT INTO `audit_logs` (`id_log`, `utilisateur_id`, `action`, `entite_type`, `entite_id`, `description`, `donnees_avant_json`, `donnees_apres_json`, `ip_adresse`, `user_agent`, `created_at`) VALUES
(1, 1, 'CONNEXION', 'session', NULL, 'Connexion réussie', NULL, '{\"remember\": false, \"session_id\": \"abc123\"}', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2026-01-16 11:56:13'),
(2, 30, 'CONNEXION', 'session', NULL, 'Connexion réussie - Service Scolarité', NULL, '{\"session_id\": \"def456\"}', '192.168.1.101', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-01-16 11:56:13'),
(3, 100, 'CONNEXION', 'session', NULL, 'Connexion étudiant', NULL, '{\"session_id\": \"ghi789\"}', '10.0.0.50', 'Mozilla/5.0 (Linux; Android 12)', '2026-01-16 11:56:13'),
(4, 1, 'CREATION', 'utilisateur', 100, 'Création compte étudiant KONE Adama', NULL, '{\"login\": \"kone.adama@etudiant.ufhb.ci\", \"groupe\": \"Étudiant\", \"nom_utilisateur\": \"KONE Adama\"}', '192.168.1.100', 'Mozilla/5.0', '2026-01-16 11:56:13'),
(5, 1, 'CREATION', 'utilisateur', 101, 'Création compte étudiant SANGARE Fatou', NULL, '{\"login\": \"sangare.fatou@etudiant.ufhb.ci\", \"nom_utilisateur\": \"SANGARE Fatou\"}', '192.168.1.100', 'Mozilla/5.0', '2026-01-16 11:56:13'),
(6, 100, 'CREATION', 'candidature', 1, 'Soumission de candidature', NULL, '{\"theme\": \"Système de gestion de stock avec ML\", \"entreprise\": \"Orange CI\"}', '10.0.0.50', 'Mozilla/5.0', '2026-01-16 11:56:13'),
(7, 30, 'VALIDATION', 'candidature', 1, 'Validation candidature par scolarité', '{\"validee_scolarite\": false}', '{\"validee_par\": 30, \"validee_scolarite\": true}', '192.168.1.101', 'Mozilla/5.0', '2026-01-16 11:56:13'),
(8, 20, 'VALIDATION', 'candidature', 1, 'Validation format par communication', '{\"validee_communication\": false}', '{\"validee_par\": 20, \"validee_communication\": true}', '192.168.1.102', 'Mozilla/5.0', '2026-01-16 11:56:13'),
(9, 100, 'CREATION', 'rapport', 1, 'Création brouillon rapport', NULL, '{\"titre\": \"Système de gestion de stock\", \"version\": 1}', '10.0.0.50', 'Mozilla/5.0', '2026-01-16 11:56:13'),
(10, 100, 'MODIFICATION', 'rapport', 1, 'Mise à jour rapport v2', '{\"statut\": \"Brouillon\", \"version\": 1}', '{\"statut\": \"Brouillon\", \"version\": 2}', '10.0.0.50', 'Mozilla/5.0', '2026-01-16 11:56:13'),
(11, 100, 'SOUMISSION', 'rapport', 1, 'Soumission rapport pour évaluation', '{\"statut\": \"Brouillon\"}', '{\"statut\": \"Soumis\", \"date_soumission\": \"2024-10-15\"}', '10.0.0.50', 'Mozilla/5.0', '2026-01-16 11:56:13'),
(12, 80, 'CREATION', 'session_commission', 1, 'Création session commission', NULL, '{\"date\": \"2024-10-28\", \"lieu\": \"Salle de conférence\"}', '192.168.1.103', 'Mozilla/5.0', '2026-01-16 11:56:13'),
(13, 60, 'VOTE', 'vote_commission', 1, 'Vote sur rapport étudiant', NULL, '{\"tour\": 1, \"decision\": \"Valider\", \"rapport_id\": 1}', '192.168.1.104', 'Mozilla/5.0', '2026-01-16 11:56:13'),
(14, 61, 'VOTE', 'vote_commission', 2, 'Vote sur rapport étudiant', NULL, '{\"tour\": 1, \"decision\": \"Valider\", \"rapport_id\": 1}', '192.168.1.105', 'Mozilla/5.0', '2026-01-16 11:56:13'),
(15, 80, 'VALIDATION', 'rapport', 1, 'Validation finale rapport par commission', '{\"statut\": \"En_evaluation\"}', '{\"statut\": \"Valide\", \"tour_validation\": 1}', '192.168.1.103', 'Mozilla/5.0', '2026-01-16 11:56:13'),
(16, 80, 'CREATION', 'jury', 1, 'Constitution du jury', NULL, '{\"membres\": [\"KOFFI\", \"KOUASSI\", \"DIABATE\"], \"dossier_id\": 1}', '192.168.1.103', 'Mozilla/5.0', '2026-01-16 11:56:13'),
(17, 30, 'PLANIFICATION', 'soutenance', 1, 'Planification soutenance', NULL, '{\"date\": \"2024-12-10\", \"heure\": \"09:00\", \"salle\": \"A102\"}', '192.168.1.101', 'Mozilla/5.0', '2026-01-16 11:56:13'),
(18, 80, 'DEMARRAGE', 'soutenance', 1, 'Démarrage soutenance - code validé', NULL, '{\"heure_debut\": \"09:00\", \"code_utilise\": true}', '192.168.1.106', 'Mozilla/5.0', '2026-01-16 11:56:13'),
(19, 80, 'SAISIE_NOTES', 'soutenance', 1, 'Saisie des notes de soutenance', NULL, '{\"mention\": \"Très Bien\", \"note_finale\": 16.83}', '192.168.1.106', 'Mozilla/5.0', '2026-01-16 11:56:13'),
(20, 30, 'CREATION', 'paiement', 1, 'Enregistrement paiement', NULL, '{\"mode\": \"Virement\", \"montant\": 550000, \"etudiant\": \"KONE Adama\"}', '192.168.1.101', 'Mozilla/5.0', '2026-01-16 11:56:13'),
(21, 30, 'GENERATION', 'document', 1, 'Génération reçu de paiement', NULL, '{\"type\": \"recu_paiement\", \"fichier\": \"recu_001.pdf\"}', '192.168.1.101', 'Mozilla/5.0', '2026-01-16 11:56:13'),
(22, 1, 'MODIFICATION', 'configuration', NULL, 'Modification paramètre système', '{\"cle\": \"commission.max_tours\", \"valeur\": \"3\"}', '{\"cle\": \"commission.max_tours\", \"valeur\": \"5\"}', '192.168.1.100', 'Mozilla/5.0', '2026-01-16 11:56:13'),
(23, 1, 'BACKUP', 'systeme', NULL, 'Lancement backup manuel', NULL, '{\"type\": \"full\", \"fichier\": \"backup_2024-12-15.sql.gz\"}', '192.168.1.100', 'Mozilla/5.0', '2026-01-16 11:56:13'),
(24, 100, 'DECONNEXION', 'session', NULL, 'Déconnexion étudiant', '{\"session_id\": \"ghi789\"}', NULL, '10.0.0.50', 'Mozilla/5.0', '2026-01-16 11:56:13'),
(25, 1, 'FORCE_DECONNEXION', 'session', NULL, 'Déconnexion forcée session suspecte', NULL, '{\"raison\": \"Activité suspecte détectée\", \"session_forcee\": \"xyz999\"}', '192.168.1.100', 'Mozilla/5.0', '2026-01-16 11:56:13');

-- --------------------------------------------------------

--
-- Structure de la table `candidatures`
--

DROP TABLE IF EXISTS `candidatures`;
CREATE TABLE IF NOT EXISTS `candidatures` (
  `id_candidature` int NOT NULL AUTO_INCREMENT,
  `dossier_id` int NOT NULL,
  `theme` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entreprise_id` int DEFAULT NULL,
  `maitre_stage_nom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maitre_stage_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maitre_stage_tel` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_debut_stage` date DEFAULT NULL,
  `date_fin_stage` date DEFAULT NULL,
  `date_soumission` datetime DEFAULT CURRENT_TIMESTAMP,
  `validee_scolarite` tinyint(1) DEFAULT '0',
  `date_valid_scolarite` datetime DEFAULT NULL,
  `validee_communication` tinyint(1) DEFAULT '0',
  `date_valid_communication` datetime DEFAULT NULL,
  PRIMARY KEY (`id_candidature`),
  KEY `idx_dossier` (`dossier_id`),
  KEY `idx_entreprise` (`entreprise_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `candidatures`
--

INSERT INTO `candidatures` (`id_candidature`, `dossier_id`, `theme`, `entreprise_id`, `maitre_stage_nom`, `maitre_stage_email`, `maitre_stage_tel`, `date_debut_stage`, `date_fin_stage`, `date_soumission`, `validee_scolarite`, `date_valid_scolarite`, `validee_communication`, `date_valid_communication`) VALUES
(1, 1, 'Développement d\'un système de gestion de stock avec prédiction de la demande par Machine Learning', 1, 'KOUAME Didier', 'd.kouame@orange.ci', '+225 07 01 02 03', '2024-03-01', '2024-08-31', '2024-10-01 00:00:00', 1, '2024-10-05 00:00:00', 1, '2024-10-08 00:00:00'),
(2, 2, 'Mise en place d\'une plateforme de e-banking sécurisée avec authentification biométrique', 4, 'DIALLO Aminata', 'a.diallo@sgci.ci', '+225 07 04 05 06', '2024-03-15', '2024-09-15', '2024-10-02 00:00:00', 1, '2024-10-06 00:00:00', 1, '2024-10-09 00:00:00'),
(3, 3, 'Conception d\'un système de suivi de flotte par GPS avec interface web et mobile', 7, 'BAMBA Youssouf', 'y.bamba@quantech.ci', '+225 07 07 08 09', '2024-04-01', '2024-09-30', '2024-10-03 00:00:00', 1, '2024-10-07 00:00:00', 1, '2024-10-10 00:00:00'),
(4, 4, 'Implémentation d\'un chatbot intelligent pour le service client utilisant NLP', 2, 'KONE Seydou', 's.kone@mtn.ci', '+225 07 10 11 12', '2024-03-01', '2024-08-31', '2024-10-04 00:00:00', 1, '2024-10-08 00:00:00', 1, '2024-10-11 00:00:00'),
(5, 5, 'Développement d\'une application de gestion de portefeuille client pour conseillers bancaires', 5, 'YAO Christelle', 'c.yao@bicici.com', '+225 07 13 14 15', '2024-04-15', '2024-10-15', '2024-10-05 00:00:00', 1, '2024-10-09 00:00:00', 1, '2024-10-12 00:00:00'),
(6, 6, 'Conception d\'un système de vote électronique sécurisé par blockchain', 21, 'TRAORE Mamadou', 'm.traore@men.gouv.ci', '+225 07 16 17 18', '2024-03-15', '2024-09-15', '2024-10-06 00:00:00', 1, '2024-10-10 00:00:00', 1, '2024-10-13 00:00:00'),
(7, 7, 'Développement d\'une plateforme d\'analyse des réseaux sociaux pour études marketing', 17, 'SORO Fatou', 'f.soro@jumia.com', '+225 07 19 20 21', '2024-04-01', '2024-09-30', '2024-10-07 00:00:00', 1, '2024-10-11 00:00:00', 1, '2024-10-14 00:00:00'),
(8, 8, 'Mise en place d\'un système de détection de fraude par analyse comportementale', 6, 'GBAGBO Eric', 'e.gbagbo@ecobank.ci', '+225 07 22 23 24', '2024-03-01', '2024-08-31', '2024-10-08 00:00:00', 1, '2024-10-12 00:00:00', 1, '2024-10-15 00:00:00'),
(9, 9, 'Conception d\'une application mobile de télémédecine pour zones rurales', 8, 'CISSE Aïcha', 'a.cisse@nsia.ci', '+225 07 25 26 27', '2024-04-15', '2024-10-15', '2024-10-09 00:00:00', 1, '2024-10-13 00:00:00', 1, '2024-10-16 00:00:00'),
(10, 10, 'Développement d\'un système de gestion documentaire avec OCR et indexation automatique', 9, 'DIABATE Moussa', 'm.diabate@deloitte.ci', '+225 07 28 29 30', '2024-03-15', '2024-09-15', '2024-10-10 00:00:00', 1, '2024-10-14 00:00:00', 1, NULL),
(11, 11, 'Implémentation d\'un ERP simplifié pour PME ivoiriennes', 10, 'KOUASSI Aya', 'a.kouassi@pwc.com', '+225 07 31 32 33', '2024-04-01', '2024-09-30', '2024-10-11 00:00:00', 1, '2024-10-15 00:00:00', 0, NULL),
(12, 12, 'Conception d\'une plateforme de crowdfunding adaptée au contexte africain', 19, 'OUATTARA Ibrahim', 'i.ouattara@afriland.ci', '+225 07 34 35 36', '2024-03-01', '2024-08-31', '2024-10-12 00:00:00', 0, NULL, 0, NULL),
(16, 16, 'Analyse prédictive de la consommation électrique par Deep Learning', 12, 'SANOGO Pierre', 'p.sanogo@cie.ci', '+225 07 40 41 42', '2024-04-01', '2024-09-30', '2024-10-16 00:00:00', 1, '2024-10-20 00:00:00', 1, '2024-10-23 00:00:00'),
(17, 17, 'Développement d\'un système de gestion de la chaîne logistique portuaire', 13, 'BROU Constant', 'c.brou@paa-ci.org', '+225 07 43 44 45', '2024-03-15', '2024-09-15', '2024-10-17 00:00:00', 1, '2024-10-21 00:00:00', 1, '2024-10-24 00:00:00'),
(18, 18, 'Implémentation d\'un système de réservation de vols avec tarification dynamique', 14, 'KONAN Estelle', 'e.konan@aircotedivoire.com', '+225 07 46 47 48', '2024-04-15', '2024-10-15', '2024-10-18 00:00:00', 1, '2024-10-22 00:00:00', 1, '2024-10-25 00:00:00'),
(19, 19, 'Conception d\'une plateforme de paiement mobile interopérable', 18, 'DIARRA Moussa', 'm.diarra@wave.com', '+225 07 49 50 51', '2024-03-01', '2024-08-31', '2024-10-19 00:00:00', 1, '2024-10-23 00:00:00', 1, '2024-10-26 00:00:00'),
(20, 20, 'Développement d\'un tableau de bord décisionnel pour dirigeants', 15, 'FOFANA Aminata', 'a.fofana@sifca.com', '+225 07 52 53 54', '2024-04-01', '2024-09-30', '2024-10-20 00:00:00', 1, '2024-10-24 00:00:00', 1, '2024-10-27 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `codes_temporaires`
--

DROP TABLE IF EXISTS `codes_temporaires`;
CREATE TABLE IF NOT EXISTS `codes_temporaires` (
  `id_code` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `soutenance_id` int DEFAULT NULL,
  `code_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('president_jury','reset_password','verification') COLLATE utf8mb4_unicode_ci NOT NULL,
  `valide_de` datetime NOT NULL,
  `valide_jusqu_a` datetime NOT NULL,
  `utilise` tinyint(1) DEFAULT '0',
  `utilise_a` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_code`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `idx_type` (`type`),
  KEY `idx_validite` (`valide_de`,`valide_jusqu_a`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `configuration_systeme`
--

DROP TABLE IF EXISTS `configuration_systeme`;
CREATE TABLE IF NOT EXISTS `configuration_systeme` (
  `id_config` int NOT NULL AUTO_INCREMENT,
  `cle_config` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valeur_config` text COLLATE utf8mb4_unicode_ci,
  `type_valeur` enum('string','int','float','boolean','json') COLLATE utf8mb4_unicode_ci DEFAULT 'string',
  `groupe_config` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `modifiable_ui` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_config`),
  UNIQUE KEY `cle_config` (`cle_config`),
  KEY `idx_cle` (`cle_config`),
  KEY `idx_groupe` (`groupe_config`)
) ENGINE=InnoDB AUTO_INCREMENT=233 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `configuration_systeme`
--

INSERT INTO `configuration_systeme` (`id_config`, `cle_config`, `valeur_config`, `type_valeur`, `groupe_config`, `description`, `modifiable_ui`, `created_at`, `updated_at`) VALUES
(1, 'workflow.escalade.enabled', 'true', 'boolean', 'workflow', 'Activer l\'escalade automatique vers le Doyen', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(2, 'workflow.sla.jours_defaut', '7', 'int', 'workflow', 'Délai SLA par défaut en jours', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(3, 'workflow.alerte.50_pourcent', 'true', 'boolean', 'workflow', 'Alerte à 50% du délai', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(4, 'workflow.alerte.80_pourcent', 'true', 'boolean', 'workflow', 'Alerte à 80% du délai', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(5, 'workflow.alerte.100_pourcent', 'true', 'boolean', 'workflow', 'Alerte à 100% du délai', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(6, 'workflow.gate.paiement_requis', 'true', 'boolean', 'workflow', 'Paiement requis avant commission', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(7, 'workflow.gate.rapport_requis', 'true', 'boolean', 'workflow', 'Rapport requis avant commission', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(8, 'workflow.notification.auto', 'true', 'boolean', 'workflow', 'Notifications automatiques sur transitions', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(9, 'commission.max_tours', '3', 'int', 'commission', 'Nombre maximum de tours de vote', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(10, 'commission.unanimite_requise', 'true', 'boolean', 'commission', 'Unanimité requise pour validation', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(11, 'commission.mediation.enabled', 'true', 'boolean', 'commission', 'Activer médiation par le Doyen', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(12, 'commission.session.duree_min', '60', 'int', 'commission', 'Durée minimum session commission (minutes)', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(13, 'commission.rapports.max_session', '15', 'int', 'commission', 'Nombre max rapports par session', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(14, 'commission.pv.auto_generation', 'true', 'boolean', 'commission', 'Génération auto PV après session', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(15, 'commission.rappel.jours_avant', '7', 'int', 'commission', 'Rappel X jours avant session', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(16, 'finance.scolarite.montant', '500000', 'int', 'finance', 'Montant scolarité annuelle (FCFA)', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(17, 'finance.scolarite.frais_inscription', '50000', 'int', 'finance', 'Frais d\'inscription (FCFA)', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(18, 'finance.penalite.taux_jour', '0.5', 'float', 'finance', 'Taux pénalité par jour de retard (%)', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(19, 'finance.penalite.plafond', '50', 'int', 'finance', 'Plafond maximum pénalité (%)', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(20, 'finance.penalite.grace_jours', '7', 'int', 'finance', 'Jours de grâce avant pénalité', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(21, 'finance.recu.auto_generation', 'true', 'boolean', 'finance', 'Génération automatique reçus', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(22, 'finance.modes_paiement', '[\"Especes\",\"Carte\",\"Virement\",\"Cheque\"]', 'json', 'finance', 'Modes de paiement acceptés', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(23, 'notifications.email.enabled', 'true', 'boolean', 'notifications', 'Activer envoi emails', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(24, 'notifications.email.from', 'noreply@checkmaster.ufhb.ci', 'string', 'notifications', 'Adresse expéditeur', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(25, 'notifications.email.from_name', 'CheckMaster UFHB', 'string', 'notifications', 'Nom expéditeur', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(26, 'notifications.sms.enabled', 'false', 'boolean', 'notifications', 'Activer envoi SMS', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(27, 'notifications.sms.provider', '', 'string', 'notifications', 'Provider SMS (orange, mtn, etc.)', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(28, 'notifications.queue.enabled', 'true', 'boolean', 'notifications', 'Utiliser file d\'attente', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(29, 'notifications.queue.batch_size', '50', 'int', 'notifications', 'Taille batch envoi', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(30, 'notifications.retry.max', '3', 'int', 'notifications', 'Tentatives max en cas d\'échec', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(31, 'notifications.retry.delay_minutes', '5', 'int', 'notifications', 'Délai entre tentatives (minutes)', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(32, 'documents.signatures.enabled', 'false', 'boolean', 'documents', 'Activer signatures électroniques', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(33, 'documents.signatures.otp_enabled', 'false', 'boolean', 'documents', 'OTP pour signatures', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(34, 'documents.archive.enabled', 'true', 'boolean', 'documents', 'Archivage automatique', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(35, 'documents.archive.duree_jours', '10950', 'int', 'documents', 'Durée conservation archives (30 ans)', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(36, 'documents.verification.enabled', 'true', 'boolean', 'documents', 'Vérification intégrité', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(37, 'documents.verification.frequence', 'weekly', 'string', 'documents', 'Fréquence vérification intégrité', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(38, 'documents.pdf.generator', 'tcpdf', 'string', 'documents', 'Générateur PDF par défaut (tcpdf/mpdf)', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(39, 'documents.storage.path', 'storage/documents', 'string', 'documents', 'Chemin stockage documents', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(40, 'documents.upload.max_size_mb', '10', 'int', 'documents', 'Taille max upload (Mo)', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(41, 'auth.session.duree_heures', '8', 'int', 'auth', 'Durée session en heures', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(42, 'auth.session.multi_device', 'true', 'boolean', 'auth', 'Autoriser sessions multi-appareils', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(43, 'auth.session.max_actives', '5', 'int', 'auth', 'Nombre max sessions actives', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(44, 'auth.password.min_length', '8', 'int', 'auth', 'Longueur minimum mot de passe', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(45, 'auth.password.require_uppercase', 'true', 'boolean', 'auth', 'Exiger majuscule', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(46, 'auth.password.require_lowercase', 'true', 'boolean', 'auth', 'Exiger minuscule', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(47, 'auth.password.require_number', 'true', 'boolean', 'auth', 'Exiger chiffre', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(48, 'auth.password.require_special', 'true', 'boolean', 'auth', 'Exiger caractère spécial', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(49, 'auth.password.expiry_days', '0', 'int', 'auth', 'Expiration mot de passe (0=jamais)', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(50, 'auth.bruteforce.enabled', 'true', 'boolean', 'auth', 'Protection brute-force activée', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(51, 'auth.bruteforce.seuil_1', '3', 'int', 'auth', 'Échecs avant délai 1 min', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(52, 'auth.bruteforce.seuil_2', '5', 'int', 'auth', 'Échecs avant délai 15 min', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(53, 'auth.bruteforce.seuil_verrouillage', '10', 'int', 'auth', 'Échecs avant verrouillage 24h', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(54, 'auth.2fa.enabled', 'false', 'boolean', 'auth', 'Double authentification activée', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(55, 'app.nom', 'CheckMaster UFHB', 'string', 'app', 'Nom de l\'application', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(56, 'app.version', '2.0.0', 'string', 'app', 'Version application', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(57, 'app.institution', 'Université Félix Houphouët-Boigny', 'string', 'app', 'Nom de l\'institution', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(58, 'app.ufr', 'Mathématiques et Informatique', 'string', 'app', 'Nom de l\'UFR', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(59, 'app.logo', '/assets/images/logo.png', 'string', 'app', 'Chemin logo', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(60, 'app.favicon', '/assets/images/favicon.ico', 'string', 'app', 'Chemin favicon', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(61, 'app.annee_academique_active', '1', 'int', 'app', 'ID année académique active', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(62, 'app.timezone', 'Africa/Abidjan', 'string', 'app', 'Fuseau horaire', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(63, 'app.locale', 'fr_CI', 'string', 'app', 'Locale', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(64, 'app.date_format', 'd/m/Y', 'string', 'app', 'Format date', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(65, 'app.datetime_format', 'd/m/Y H:i', 'string', 'app', 'Format date/heure', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(66, 'app.maintenance.enabled', 'false', 'boolean', 'app', 'Mode maintenance activé', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(67, 'app.maintenance.message', '', 'string', 'app', 'Message maintenance', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(68, 'app.debug', 'false', 'boolean', 'app', 'Mode debug', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(69, 'app.registration.open', 'false', 'boolean', 'app', 'Inscriptions ouvertes', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(70, 'jury.membres_min', '3', 'int', 'soutenance', 'Nombre minimum membres jury', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(71, 'jury.membres_max', '7', 'int', 'soutenance', 'Nombre maximum membres jury', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(72, 'jury.externes_min', '1', 'int', 'soutenance', 'Nombre minimum membres externes', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(73, 'jury.president.grade_min', '3', 'int', 'soutenance', 'Grade minimum président (3=MC)', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(74, 'jury.invitation.delai_reponse', '7', 'int', 'soutenance', 'Délai réponse invitation (jours)', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(75, 'soutenance.duree_defaut', '60', 'int', 'soutenance', 'Durée soutenance par défaut (min)', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(76, 'soutenance.duree_min', '45', 'int', 'soutenance', 'Durée minimum soutenance (min)', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(77, 'soutenance.duree_max', '90', 'int', 'soutenance', 'Durée maximum soutenance (min)', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(78, 'soutenance.code.longueur', '8', 'int', 'soutenance', 'Longueur code président', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(79, 'soutenance.code.validite_debut', '06:00', 'string', 'soutenance', 'Heure début validité code', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(80, 'soutenance.code.validite_fin', '23:59', 'string', 'soutenance', 'Heure fin validité code', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(81, 'soutenance.convocation.jours_avant', '7', 'int', 'soutenance', 'Convocation X jours avant', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(82, 'soutenance.rappel.jours_avant', '1', 'int', 'soutenance', 'Rappel X jours avant', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(83, 'rapport.format_acceptes', '[\"pdf\"]', 'json', 'rapport', 'Formats fichiers acceptés', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(84, 'rapport.taille_max_mb', '50', 'int', 'rapport', 'Taille max rapport (Mo)', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(85, 'rapport.pages_min', '30', 'int', 'rapport', 'Nombre minimum pages', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(86, 'rapport.pages_max', '100', 'int', 'rapport', 'Nombre maximum pages', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(87, 'rapport.versioning', 'true', 'boolean', 'rapport', 'Versionning activé', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(88, 'rapport.max_versions', '10', 'int', 'rapport', 'Nombre max versions', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(89, 'rapport.annotation.enabled', 'true', 'boolean', 'rapport', 'Annotations activées', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(90, 'rapport.page_garde.auto', 'true', 'boolean', 'rapport', 'Page de garde automatique', 1, '2026-01-15 13:44:39', '2026-01-15 13:44:39'),
(91, 'escalade.niveau_1.delai', '3', 'int', 'escalade', 'Délai niveau 1 (jours)', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(92, 'escalade.niveau_2.delai', '5', 'int', 'escalade', 'Délai niveau 2 (jours)', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(93, 'escalade.niveau_3.delai', '7', 'int', 'escalade', 'Délai niveau 3 (jours)', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(94, 'escalade.niveau_4.delai', '10', 'int', 'escalade', 'Délai niveau 4 - Doyen (jours)', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(95, 'escalade.auto.enabled', 'true', 'boolean', 'escalade', 'Escalade automatique', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(96, 'escalade.notification.immediate', 'true', 'boolean', 'escalade', 'Notification immédiate escalade', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(97, 'import.etudiants.enabled', 'true', 'boolean', 'import', 'Import étudiants activé', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(98, 'import.etudiants.format', 'xlsx', 'string', 'import', 'Format import étudiants', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(99, 'import.validation.strict', 'true', 'boolean', 'import', 'Validation stricte imports', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(100, 'export.format_defaut', 'xlsx', 'string', 'export', 'Format export par défaut', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(101, 'export.limite_lignes', '10000', 'int', 'export', 'Limite lignes export', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(102, 'audit.enabled', 'true', 'boolean', 'audit', 'Audit activé', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(103, 'audit.file.enabled', 'true', 'boolean', 'audit', 'Audit fichier activé', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(104, 'audit.db.enabled', 'true', 'boolean', 'audit', 'Audit base données activé', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(105, 'audit.retention_jours', '365', 'int', 'audit', 'Rétention logs (jours)', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(106, 'audit.sensitive_fields', '[\"mdp_utilisateur\",\"code_hash\"]', 'json', 'audit', 'Champs sensibles à masquer', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(107, 'cache.enabled', 'true', 'boolean', 'cache', 'Cache activé', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(108, 'cache.permissions.duree', '300', 'int', 'cache', 'Durée cache permissions (sec)', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(109, 'cache.config.duree', '3600', 'int', 'cache', 'Durée cache config (sec)', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(110, 'cache.stats.duree', '900', 'int', 'cache', 'Durée cache stats (sec)', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(111, 'backup.auto.enabled', 'true', 'boolean', 'backup', 'Backup automatique', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(112, 'backup.auto.frequence', 'daily', 'string', 'backup', 'Fréquence backup auto', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(113, 'backup.auto.heure', '02:00', 'string', 'backup', 'Heure backup auto', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(114, 'backup.retention.jours', '30', 'int', 'backup', 'Rétention backups (jours)', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(115, 'backup.compression', 'true', 'boolean', 'backup', 'Compression backups', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40'),
(116, 'backup.notification', 'true', 'boolean', 'backup', 'Notification après backup', 1, '2026-01-15 13:44:40', '2026-01-15 13:44:40');

-- --------------------------------------------------------

--
-- Structure de la table `critere_evaluation`
--

DROP TABLE IF EXISTS `critere_evaluation`;
CREATE TABLE IF NOT EXISTS `critere_evaluation` (
  `id_critere` int NOT NULL AUTO_INCREMENT,
  `code_critere` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ponderation` decimal(5,2) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_critere`),
  UNIQUE KEY `code_critere` (`code_critere`),
  KEY `idx_code` (`code_critere`),
  KEY `idx_actif` (`actif`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `critere_evaluation`
--

INSERT INTO `critere_evaluation` (`id_critere`, `code_critere`, `libelle`, `description`, `ponderation`, `actif`) VALUES
(1, 'FOND', 'Qualité du Fond', 'Pertinence du contenu, méthodologie, résultats', 40.00, 1),
(2, 'FORME', 'Qualité de la Forme', 'Rédaction, mise en page, orthographe', 20.00, 1),
(3, 'ORAL', 'Présentation Orale', 'Clarté, maîtrise, support visuel', 25.00, 1),
(4, 'REPONSES', 'Réponses aux Questions', 'Pertinence et maîtrise des réponses', 15.00, 1);

-- --------------------------------------------------------

--
-- Structure de la table `decisions_jury`
--

DROP TABLE IF EXISTS `decisions_jury`;
CREATE TABLE IF NOT EXISTS `decisions_jury` (
  `id_decision` int NOT NULL AUTO_INCREMENT,
  `soutenance_id` int NOT NULL,
  `decision` enum('Admis','Ajourné','Corrections_mineures','Corrections_majeures') COLLATE utf8mb4_unicode_ci NOT NULL,
  `delai_corrections` int DEFAULT NULL,
  `commentaires` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_decision`),
  KEY `idx_soutenance` (`soutenance_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `decisions_jury`
--

INSERT INTO `decisions_jury` (`id_decision`, `soutenance_id`, `decision`, `delai_corrections`, `commentaires`, `created_at`) VALUES
(1, 1, 'Admis', NULL, 'Admis avec mention Très Bien. Félicitations du jury pour la qualité du travail.', '2026-01-16 10:27:05'),
(2, 2, 'Corrections_mineures', 15, 'Admis sous réserve de corrections mineures à apporter dans un délai de 15 jours.', '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `delegations_actions_log`
--

DROP TABLE IF EXISTS `delegations_actions_log`;
CREATE TABLE IF NOT EXISTS `delegations_actions_log` (
  `id_log_delegation` int NOT NULL AUTO_INCREMENT,
  `delegation_id` int NOT NULL,
  `action_effectuee` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` json DEFAULT NULL,
  `effectue_par` int NOT NULL COMMENT 'Le délégat aire',
  `au_nom_de` int NOT NULL COMMENT 'Le délégant',
  `date_action` datetime DEFAULT CURRENT_TIMESTAMP,
  `ip_adresse` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_log_delegation`),
  KEY `effectue_par` (`effectue_par`),
  KEY `au_nom_de` (`au_nom_de`),
  KEY `idx_delegation` (`delegation_id`),
  KEY `idx_date` (`date_action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `delegations_fonctions`
--

DROP TABLE IF EXISTS `delegations_fonctions`;
CREATE TABLE IF NOT EXISTS `delegations_fonctions` (
  `id_delegation` int NOT NULL AUTO_INCREMENT,
  `delegant_id` int NOT NULL,
  `delegataire_id` int NOT NULL,
  `fonction` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope_delegation` enum('total','partiel','specifique') COLLATE utf8mb4_unicode_ci DEFAULT 'partiel',
  `restrictions` json DEFAULT NULL COMMENT 'Restrictions et limites',
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `motif` text COLLATE utf8mb4_unicode_ci,
  `document_officiel` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` enum('actif','suspendu','termine') COLLATE utf8mb4_unicode_ci DEFAULT 'actif',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_delegation`),
  KEY `idx_delegant` (`delegant_id`),
  KEY `idx_delegataire` (`delegataire_id`),
  KEY `idx_dates` (`date_debut`,`date_fin`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `demandes_exoneration`
--

DROP TABLE IF EXISTS `demandes_exoneration`;
CREATE TABLE IF NOT EXISTS `demandes_exoneration` (
  `id_demande_exoneration` int NOT NULL AUTO_INCREMENT,
  `etudiant_id` int NOT NULL,
  `type_exoneration_id` int NOT NULL,
  `annee_academique_id` int NOT NULL,
  `motif` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pieces_justificatives` json DEFAULT NULL,
  `montant_demande` decimal(10,2) DEFAULT NULL,
  `statut` enum('en_attente','approuve','refuse','en_revision') COLLATE utf8mb4_unicode_ci DEFAULT 'en_attente',
  `traite_par` int DEFAULT NULL,
  `date_traitement` datetime DEFAULT NULL,
  `commentaire_traitement` text COLLATE utf8mb4_unicode_ci,
  `decision_finale` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_demande_exoneration`),
  KEY `type_exoneration_id` (`type_exoneration_id`),
  KEY `traite_par` (`traite_par`),
  KEY `idx_etudiant` (`etudiant_id`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `documents_generes`
--

DROP TABLE IF EXISTS `documents_generes`;
CREATE TABLE IF NOT EXISTS `documents_generes` (
  `id_document` int NOT NULL AUTO_INCREMENT,
  `type_document` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entite_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entite_id` int DEFAULT NULL,
  `chemin_fichier` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taille_octets` bigint DEFAULT NULL,
  `hash_sha256` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `genere_par` int DEFAULT NULL,
  `genere_le` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_document`),
  KEY `genere_par` (`genere_par`),
  KEY `idx_type` (`type_document`),
  KEY `idx_entite` (`entite_type`,`entite_id`),
  KEY `idx_hash` (`hash_sha256`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `documents_generes`
--

INSERT INTO `documents_generes` (`id_document`, `type_document`, `entite_type`, `entite_id`, `chemin_fichier`, `nom_fichier`, `taille_octets`, `hash_sha256`, `genere_par`, `genere_le`) VALUES
(1, 'recu_paiement', 'paiement', 1, 'storage/recus/2024/recu_001.pdf', 'recu_paiement_KONE_Adama_001.pdf', 45678, 'a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2', 30, '2024-09-15 10:35:00'),
(2, 'recu_paiement', 'paiement', 2, 'storage/recus/2024/recu_002.pdf', 'recu_paiement_SANGARE_Fatou_002.pdf', 45890, 'b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3', 30, '2024-09-16 11:20:00'),
(3, 'recu_paiement', 'paiement', 3, 'storage/recus/2024/recu_003.pdf', 'recu_paiement_BROU_JeanPierre_003.pdf', 44567, 'c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4', 31, '2024-09-17 09:45:00'),
(4, 'recu_penalite', 'penalite', 1, 'storage/recus_penalites/2024/penalite_001.pdf', 'penalite_LAGO_Constant_001.pdf', 32456, 'd4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5', 30, '2024-10-20 14:30:00'),
(5, 'pv_commission', 'session_commission', 1, 'storage/pv/2024/pv_session_001.pdf', 'PV_Commission_2024-10-28.pdf', 156789, 'e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6', 80, '2024-10-28 17:00:00'),
(6, 'pv_commission', 'session_commission', 2, 'storage/pv/2024/pv_session_002.pdf', 'PV_Commission_2024-11-04.pdf', 167890, 'f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1', 80, '2024-11-04 17:00:00'),
(7, 'pv_commission', 'session_commission', 3, 'storage/pv/2024/pv_session_003.pdf', 'PV_Commission_2024-11-11.pdf', 178901, 'a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2', 80, '2024-11-11 17:00:00'),
(8, 'pv_soutenance', 'soutenance', 1, 'storage/pv_soutenance/2024/pv_soutenance_001.pdf', 'PV_Soutenance_KONE_Adama.pdf', 89012, 'b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3', 80, '2024-12-10 12:30:00'),
(9, 'pv_soutenance', 'soutenance', 2, 'storage/pv_soutenance/2024/pv_soutenance_002.pdf', 'PV_Soutenance_SANGARE_Fatou.pdf', 90123, 'c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4', 80, '2024-12-12 17:30:00'),
(10, 'bulletin_soutenance', 'soutenance', 1, 'storage/bulletins/2024/bulletin_soutenance_001.pdf', 'Bulletin_Soutenance_KONE_Adama.pdf', 67890, 'd4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5', 30, '2024-12-10 14:00:00'),
(11, 'attestation_diplome', 'etudiant', 1, 'storage/diplomes/2024/attestation_001.pdf', 'Attestation_Diplome_KONE_Adama.pdf', 123456, 'e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6', 30, '2024-12-15 11:00:00'),
(12, 'convocation_soutenance', 'soutenance', 3, 'storage/convocations/2024/convocation_003.pdf', 'Convocation_Soutenance_BROU_JeanPierre.pdf', 34567, 'f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1', 30, '2024-12-13 09:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `documents_generes_historique`
--

DROP TABLE IF EXISTS `documents_generes_historique`;
CREATE TABLE IF NOT EXISTS `documents_generes_historique` (
  `id_document_genere` int NOT NULL AUTO_INCREMENT,
  `template_id` int NOT NULL,
  `entite_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Type entité source (dossier, etudiant, etc)',
  `entite_id` int NOT NULL,
  `nom_fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chemin_fichier` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taille_octets` bigint DEFAULT NULL,
  `hash_sha256` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parametres_generation` json DEFAULT NULL,
  `statut` enum('genere','envoye','archive','supprime') COLLATE utf8mb4_unicode_ci DEFAULT 'genere',
  `genere_par` int NOT NULL,
  `date_generation` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_expiration` datetime DEFAULT NULL,
  `nombre_telechargements` int DEFAULT '0',
  `derniere_lecture` datetime DEFAULT NULL,
  PRIMARY KEY (`id_document_genere`),
  KEY `genere_par` (`genere_par`),
  KEY `idx_template` (`template_id`),
  KEY `idx_entite` (`entite_type`,`entite_id`),
  KEY `idx_statut` (`statut`),
  KEY `idx_hash` (`hash_sha256`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `documents_signatures_electroniques`
--

DROP TABLE IF EXISTS `documents_signatures_electroniques`;
CREATE TABLE IF NOT EXISTS `documents_signatures_electroniques` (
  `id_signature` int NOT NULL AUTO_INCREMENT,
  `document_genere_id` int NOT NULL,
  `signataire_id` int NOT NULL,
  `role_signataire` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ordre_signature` int NOT NULL,
  `statut` enum('en_attente','signe','refuse','expire') COLLATE utf8mb4_unicode_ci DEFAULT 'en_attente',
  `date_demande` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_signature` datetime DEFAULT NULL,
  `signature_data` text COLLATE utf8mb4_unicode_ci COMMENT 'Données cryptographiques',
  `certificat_data` text COLLATE utf8mb4_unicode_ci,
  `ip_signature` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_signature`),
  KEY `idx_document` (`document_genere_id`),
  KEY `idx_signataire` (`signataire_id`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `documents_templates`
--

DROP TABLE IF EXISTS `documents_templates`;
CREATE TABLE IF NOT EXISTS `documents_templates` (
  `id_template` int NOT NULL AUTO_INCREMENT,
  `code_template` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_template` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type_document` enum('pdf','word','excel','html','email') COLLATE utf8mb4_unicode_ci NOT NULL,
  `categorie` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contenu_template` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `variables_disponibles` json DEFAULT NULL,
  `engine` enum('twig','blade','tcpdf','mpdf','phpword') COLLATE utf8mb4_unicode_ci DEFAULT 'twig',
  `orientation` enum('portrait','landscape') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `format_papier` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'A4',
  `header_template` text COLLATE utf8mb4_unicode_ci,
  `footer_template` text COLLATE utf8mb4_unicode_ci,
  `styles_css` text COLLATE utf8mb4_unicode_ci,
  `version` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '1.0',
  `actif` tinyint(1) DEFAULT '1',
  `cree_par` int NOT NULL,
  `modifie_par` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_template`),
  UNIQUE KEY `code_template` (`code_template`),
  KEY `cree_par` (`cree_par`),
  KEY `modifie_par` (`modifie_par`),
  KEY `idx_code` (`code_template`),
  KEY `idx_type` (`type_document`),
  KEY `idx_categorie` (`categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `dossiers_etudiants`
--

DROP TABLE IF EXISTS `dossiers_etudiants`;
CREATE TABLE IF NOT EXISTS `dossiers_etudiants` (
  `id_dossier` int NOT NULL AUTO_INCREMENT,
  `etudiant_id` int NOT NULL,
  `annee_acad_id` int NOT NULL,
  `etat_actuel_id` int NOT NULL,
  `date_entree_etat` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_limite_etat` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_dossier`),
  UNIQUE KEY `unique_etudiant_annee` (`etudiant_id`,`annee_acad_id`),
  KEY `annee_acad_id` (`annee_acad_id`),
  KEY `idx_etat` (`etat_actuel_id`),
  KEY `idx_date_limite` (`date_limite_etat`),
  KEY `idx_composite_etudiant_annee` (`etudiant_id`,`annee_acad_id`),
  KEY `idx_composite_etat_workflow` (`etat_actuel_id`,`date_entree_etat`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `dossiers_etudiants`
--

INSERT INTO `dossiers_etudiants` (`id_dossier`, `etudiant_id`, `annee_acad_id`, `etat_actuel_id`, `date_entree_etat`, `date_limite_etat`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 14, '2024-12-15 10:00:00', NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(2, 2, 1, 13, '2024-12-10 14:30:00', NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(3, 3, 1, 11, '2024-12-05 09:00:00', '2024-12-20 09:00:00', '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(4, 4, 1, 10, '2024-11-28 11:00:00', '2024-12-12 11:00:00', '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(5, 5, 1, 9, '2024-11-25 15:00:00', NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(6, 6, 1, 8, '2024-11-20 10:30:00', '2024-12-04 10:30:00', '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(7, 7, 1, 7, '2024-11-15 14:00:00', NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(8, 8, 1, 6, '2024-11-10 16:00:00', '2024-11-11 16:00:00', '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(9, 9, 1, 5, '2024-11-08 09:30:00', NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(10, 10, 1, 4, '2024-11-05 11:00:00', '2024-11-08 11:00:00', '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(11, 11, 1, 3, '2024-11-01 10:00:00', '2024-11-06 10:00:00', '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(12, 12, 1, 2, '2024-10-28 14:00:00', '2024-11-04 14:00:00', '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(13, 13, 1, 1, '2024-10-20 09:00:00', NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(14, 14, 1, 1, '2024-10-20 09:00:00', NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(15, 15, 1, 1, '2024-10-20 09:00:00', NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(16, 16, 1, 7, '2024-11-18 10:00:00', NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(17, 17, 1, 7, '2024-11-19 11:00:00', NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(18, 18, 1, 8, '2024-11-22 14:00:00', '2024-12-06 14:00:00', '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(19, 19, 1, 9, '2024-11-26 16:00:00', NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(20, 20, 1, 10, '2024-11-30 09:00:00', '2024-12-14 09:00:00', '2026-01-16 10:27:05', '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `ecue`
--

DROP TABLE IF EXISTS `ecue`;
CREATE TABLE IF NOT EXISTS `ecue` (
  `id_ecue` int NOT NULL AUTO_INCREMENT,
  `code_ecue` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lib_ecue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ue_id` int NOT NULL,
  `credits` int DEFAULT NULL,
  PRIMARY KEY (`id_ecue`),
  UNIQUE KEY `code_ecue` (`code_ecue`),
  KEY `idx_code` (`code_ecue`),
  KEY `idx_ue` (`ue_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ecue`
--

INSERT INTO `ecue` (`id_ecue`, `code_ecue`, `lib_ecue`, `ue_id`, `credits`) VALUES
(1, 'ECUE-M1S1-01A', 'Programmation Java Avancée', 1, 3),
(2, 'ECUE-M1S1-01B', 'Design Patterns', 1, 3),
(3, 'ECUE-M1S1-02A', 'SQL Avancé et Optimisation', 2, 3),
(4, 'ECUE-M1S1-02B', 'Bases de Données NoSQL', 2, 3),
(5, 'ECUE-M1S1-03A', 'Architecture Réseaux', 3, 2),
(6, 'ECUE-M1S1-03B', 'Sécurité des Réseaux', 3, 3),
(7, 'ECUE-M1S1-04A', 'Algorithmique Avancée', 4, 3),
(8, 'ECUE-M1S1-04B', 'Probabilités et Statistiques', 4, 2),
(9, 'ECUE-M1S1-05A', 'Méthodes Agiles', 5, 2),
(10, 'ECUE-M1S1-05B', 'Outils de Gestion de Projet', 5, 2),
(11, 'ECUE-M1S1-06A', 'Anglais Technique', 6, 2),
(12, 'ECUE-M1S1-06B', 'Communication Professionnelle', 6, 2),
(13, 'ECUE-M1S2-01A', 'Architecture Microservices', 7, 3),
(14, 'ECUE-M1S2-01B', 'APIs et Web Services', 7, 3),
(15, 'ECUE-M1S2-02A', 'Machine Learning', 8, 3),
(16, 'ECUE-M1S2-02B', 'Deep Learning', 8, 3),
(17, 'ECUE-M1S2-03A', 'ERP et Progiciels', 9, 3),
(18, 'ECUE-M1S2-03B', 'Urbanisation des SI', 9, 2),
(19, 'ECUE-M1S2-04A', 'Marketing Digital', 10, 3),
(20, 'ECUE-M1S2-04B', 'E-Commerce', 10, 2),
(21, 'ECUE-M1S2-05A', 'Stage M1 en Entreprise', 11, 8),
(22, 'ECUE-M2S1-01A', 'Business Intelligence', 12, 3),
(23, 'ECUE-M2S1-01B', 'Data Visualization', 12, 3),
(24, 'ECUE-M2S1-02A', 'Cloud Computing AWS/Azure', 13, 3),
(25, 'ECUE-M2S1-02B', 'CI/CD et Containerisation', 13, 3),
(26, 'ECUE-M2S1-03A', 'Gouvernance IT', 14, 3),
(27, 'ECUE-M2S1-03B', 'ITIL et COBIT', 14, 2),
(28, 'ECUE-M2S1-04A', 'Création d\'Entreprise', 15, 3),
(29, 'ECUE-M2S1-04B', 'Innovation et Stratégie', 15, 2),
(30, 'ECUE-M2S1-05A', 'Audit des SI', 16, 2),
(31, 'ECUE-M2S1-05B', 'Cybersécurité', 16, 2),
(32, 'ECUE-M2S1-06A', 'Méthodologie de Recherche', 17, 2),
(33, 'ECUE-M2S1-06B', 'Rédaction Scientifique', 17, 2),
(34, 'ECUE-M2S2-01A', 'Stage de Fin d\'Études', 18, 15),
(35, 'ECUE-M2S2-01B', 'Mémoire et Soutenance', 18, 15);

-- --------------------------------------------------------

--
-- Structure de la table `email_bounces`
--

DROP TABLE IF EXISTS `email_bounces`;
CREATE TABLE IF NOT EXISTS `email_bounces` (
  `id_bounce` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_bounce` enum('Hard','Soft') COLLATE utf8mb4_unicode_ci NOT NULL,
  `raison` text COLLATE utf8mb4_unicode_ci,
  `compteur` int DEFAULT '1',
  `bloque` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_bounce`),
  KEY `idx_email` (`email`),
  KEY `idx_bloque` (`bloque`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `email_bounces`
--

INSERT INTO `email_bounces` (`id_bounce`, `email`, `type_bounce`, `raison`, `compteur`, `bloque`, `created_at`, `updated_at`) VALUES
(1, 'ancien.etudiant@ufhb.edu.ci', 'Hard', 'Mailbox does not exist', 3, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(2, 'temp.mail@example.com', 'Soft', 'Mailbox full', 1, 0, '2026-01-16 10:27:05', '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `enseignants`
--

DROP TABLE IF EXISTS `enseignants`;
CREATE TABLE IF NOT EXISTS `enseignants` (
  `id_enseignant` int NOT NULL AUTO_INCREMENT,
  `nom_ens` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom_ens` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_ens` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone_ens` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grade_id` int DEFAULT NULL,
  `fonction_id` int DEFAULT NULL,
  `specialite_id` int DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_enseignant`),
  UNIQUE KEY `email_ens` (`email_ens`),
  KEY `idx_nom` (`nom_ens`,`prenom_ens`),
  KEY `idx_email` (`email_ens`),
  KEY `idx_grade` (`grade_id`),
  KEY `idx_specialite` (`specialite_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `enseignants`
--

INSERT INTO `enseignants` (`id_enseignant`, `nom_ens`, `prenom_ens`, `email_ens`, `telephone_ens`, `grade_id`, `fonction_id`, `specialite_id`, `actif`, `created_at`, `updated_at`) VALUES
(1, 'KOFFI', 'Kouamé Jean', 'koffi.kouame@ufhb.edu.ci', '+225 07 08 09 01', 4, 5, 1, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(2, 'DIALLO', 'Mamadou', 'diallo.mamadou@ufhb.edu.ci', '+225 07 08 09 02', 4, 2, 2, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(3, 'TRAORE', 'Seydou', 'traore.seydou@ufhb.edu.ci', '+225 07 08 09 03', 4, 1, 3, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(4, 'OUATTARA', 'Brahima', 'ouattara.brahima@ufhb.edu.ci', '+225 07 08 09 04', 4, 1, 1, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(5, 'BAMBA', 'Amadou', 'bamba.amadou@ufhb.edu.ci', '+225 07 08 09 05', 4, 1, 4, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(6, 'KOUASSI', 'Aya Marie', 'kouassi.aya@ufhb.edu.ci', '+225 07 08 09 06', 3, 4, 1, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(7, 'DIABATE', 'Fatoumata', 'diabate.fatoumata@ufhb.edu.ci', '+225 07 08 09 07', 3, 4, 2, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(8, 'YAO', 'Konan Pierre', 'yao.konan@ufhb.edu.ci', '+225 07 08 09 08', 3, 3, 1, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(9, 'N\'GUESSAN', 'Ahou Christelle', 'nguessan.ahou@ufhb.edu.ci', '+225 07 08 09 09', 3, 4, 3, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(10, 'COULIBALY', 'Abdoulaye', 'coulibaly.abdoulaye@ufhb.edu.ci', '+225 07 08 09 10', 3, 4, 2, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(11, 'DOSSO', 'Mohamed', 'dosso.mohamed@ufhb.edu.ci', '+225 07 08 09 11', 3, 4, 5, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(12, 'SANOGO', 'Mariam', 'sanogo.mariam@ufhb.edu.ci', '+225 07 08 09 12', 3, 4, 1, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(13, 'GBAGBO', 'Eric', 'gbagbo.eric@ufhb.edu.ci', '+225 07 08 09 13', 2, 4, 1, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(14, 'SORO', 'Aminata', 'soro.aminata@ufhb.edu.ci', '+225 07 08 09 14', 2, 4, 2, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(15, 'TOURE', 'Issouf', 'toure.issouf@ufhb.edu.ci', '+225 07 08 09 15', 2, 4, 3, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(16, 'KONAN', 'Sylvie', 'konan.sylvie@ufhb.edu.ci', '+225 07 08 09 16', 2, 4, 4, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(17, 'FOFANA', 'Bakary', 'fofana.bakary@ufhb.edu.ci', '+225 07 08 09 17', 2, 4, 1, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(18, 'CISSE', 'Rokiatou', 'cisse.rokiatou@ufhb.edu.ci', '+225 07 08 09 18', 2, 4, 2, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(19, 'DEMBELE', 'Oumar', 'dembele.oumar@ufhb.edu.ci', '+225 07 08 09 19', 2, 4, 5, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(20, 'MEITE', 'Kadiatou', 'meite.kadiatou@ufhb.edu.ci', '+225 07 08 09 20', 2, 4, 1, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(21, 'CAMARA', 'Moussa', 'camara.moussa@ufhb.edu.ci', '+225 07 08 09 21', 1, 4, 1, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(22, 'KONATE', 'Aicha', 'konate.aicha@ufhb.edu.ci', '+225 07 08 09 22', 1, 4, 2, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(23, 'BERTHE', 'Souleymane', 'berthe.souleymane@ufhb.edu.ci', '+225 07 08 09 23', 1, 4, 3, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(24, 'SYLLA', 'Mariam', 'sylla.mariam@ufhb.edu.ci', '+225 07 08 09 24', 1, 4, 1, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(25, 'DIARRA', 'Ibrahima', 'diarra.ibrahima@ufhb.edu.ci', '+225 07 08 09 25', 1, 4, 4, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(26, 'OUEDRAOGO', 'Pascaline', 'ouedraogo.pascaline@ufhb.edu.ci', '+225 07 08 09 26', 1, 4, 2, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(27, 'KEITA', 'Lassana', 'keita.lassana@ufhb.edu.ci', '+225 07 08 09 27', 1, 4, 5, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(28, 'SIDIBE', 'Fatoumata', 'sidibe.fatoumata@ufhb.edu.ci', '+225 07 08 09 28', 1, 4, 1, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(29, 'GNAGNE', 'Serge', 'gnagne.serge@quantech.ci', '+225 07 08 09 29', 5, 4, 1, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(30, 'AHUI', 'Estelle', 'ahui.estelle@orange.ci', '+225 07 08 09 30', 5, 4, 2, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `entreprises`
--

DROP TABLE IF EXISTS `entreprises`;
CREATE TABLE IF NOT EXISTS `entreprises` (
  `id_entreprise` int NOT NULL AUTO_INCREMENT,
  `nom_entreprise` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secteur_activite` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse` text COLLATE utf8mb4_unicode_ci,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_web` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_entreprise`),
  KEY `idx_nom` (`nom_entreprise`),
  KEY `idx_actif` (`actif`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `entreprises`
--

INSERT INTO `entreprises` (`id_entreprise`, `nom_entreprise`, `secteur_activite`, `adresse`, `telephone`, `email`, `site_web`, `actif`, `created_at`, `updated_at`) VALUES
(1, 'Orange Côte d\'Ivoire', 'Télécommunications', 'Plateau, Abidjan, Côte d\'Ivoire', '+225 21 23 90 00', 'contact@orange.ci', 'https://www.orange.ci', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(2, 'MTN Côte d\'Ivoire', 'Télécommunications', 'Cocody, Abidjan, Côte d\'Ivoire', '+225 05 70 00 00', 'contact@mtn.ci', 'https://www.mtn.ci', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(3, 'Moov Africa', 'Télécommunications', 'Marcory, Abidjan, Côte d\'Ivoire', '+225 01 01 00 00', 'contact@moov-africa.ci', 'https://www.moov-africa.ci', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(4, 'Société Générale Côte d\'Ivoire', 'Banque et Finance', 'Plateau, Abidjan, Côte d\'Ivoire', '+225 20 20 12 00', 'contact@sgci.ci', 'https://www.sgci.ci', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(5, 'BICICI', 'Banque et Finance', 'Plateau, Abidjan, Côte d\'Ivoire', '+225 20 20 16 00', 'contact@bicici.com', 'https://www.bicici.com', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(6, 'Ecobank Côte d\'Ivoire', 'Banque et Finance', 'Plateau, Abidjan, Côte d\'Ivoire', '+225 20 31 92 00', 'contact@ecobank.ci', 'https://www.ecobank.com', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(7, 'QuanTech Solutions', 'Services Informatiques', 'Cocody Riviera, Abidjan', '+225 07 08 09 10', 'contact@quantech.ci', 'https://www.quantech.ci', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(8, 'NSIA Technologies', 'Assurance et Technologies', 'Plateau, Abidjan', '+225 20 31 88 00', 'it@nsia.ci', 'https://www.nsia.ci', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(9, 'Deloitte Côte d\'Ivoire', 'Conseil et Audit', 'Cocody, Abidjan', '+225 22 40 40 40', 'abidjan@deloitte.ci', 'https://www.deloitte.com', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(10, 'PwC Côte d\'Ivoire', 'Conseil et Audit', 'Plateau, Abidjan', '+225 20 31 54 00', 'ci_info@pwc.com', 'https://www.pwc.com', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(11, 'SODECI', 'Distribution d\'eau', 'Treichville, Abidjan', '+225 21 23 30 00', 'contact@sodeci.ci', 'https://www.sodeci.ci', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(12, 'CIE', 'Distribution d\'électricité', 'Treichville, Abidjan', '+225 21 23 33 00', 'contact@cie.ci', 'https://www.cie.ci', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(13, 'Port Autonome d\'Abidjan', 'Transport Maritime', 'Vridi, Abidjan', '+225 21 23 80 00', 'paa@paa-ci.org', 'https://www.paa-ci.org', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(14, 'Air Côte d\'Ivoire', 'Transport Aérien', 'Aéroport FHB, Abidjan', '+225 21 35 71 00', 'info@aircotedivoire.com', 'https://www.aircotedivoire.com', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(15, 'SIFCA Group', 'Agroalimentaire', 'Abidjan', '+225 21 75 33 00', 'contact@groupesifca.com', 'https://www.groupesifca.com', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(16, 'CFAO Motors', 'Automobile', 'Zone 4, Abidjan', '+225 21 21 93 00', 'contact@cfao.ci', 'https://www.cfao.com', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(17, 'Jumia Côte d\'Ivoire', 'E-commerce', 'Cocody, Abidjan', '+225 22 52 00 00', 'ci@jumia.com', 'https://www.jumia.ci', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(18, 'Wave Côte d\'Ivoire', 'Fintech / Mobile Money', 'Cocody, Abidjan', '+225 01 02 03 04', 'support@wave.com', 'https://www.wave.com', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(19, 'Afriland First Bank', 'Banque', 'Plateau, Abidjan', '+225 20 25 60 00', 'afb.ci@afrilandfirstbank.com', 'https://www.afrilandfirstbank.com', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(20, 'Koffi & Diabaté', 'Cabinet Juridique', 'Plateau, Abidjan', '+225 20 22 45 67', 'contact@kd-avocats.ci', NULL, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(21, 'Ministère de l\'Économie Numérique', 'Administration Publique', 'Plateau, Abidjan', '+225 20 21 35 00', 'info@men.gouv.ci', 'https://www.men.gouv.ci', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(22, 'ARTCI', 'Régulation Télécoms', 'Cocody, Abidjan', '+225 20 34 43 73', 'info@artci.ci', 'https://www.artci.ci', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(23, 'INS (Institut National de la Statistique)', 'Statistiques Publiques', 'Plateau, Abidjan', '+225 20 21 05 38', 'contact@ins.ci', 'https://www.ins.ci', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(24, 'DGBF (Direction Générale du Budget)', 'Finances Publiques', 'Plateau, Abidjan', '+225 20 20 09 20', 'dgbf@finances.gouv.ci', 'https://www.budget.gouv.ci', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(25, 'Banque Mondiale - Bureau Côte d\'Ivoire', 'Organisation Internationale', 'Cocody, Abidjan', '+225 22 40 04 00', 'abidjan@worldbank.org', 'https://www.worldbank.org', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(26, 'BAD (Banque Africaine de Développement)', 'Organisation Internationale', 'Plateau, Abidjan', '+225 20 26 10 20', 'afdb@afdb.org', 'https://www.afdb.org', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(27, 'PNUD Côte d\'Ivoire', 'Organisation Internationale', 'Cocody, Abidjan', '+225 22 51 10 00', 'registry.ci@undp.org', 'https://www.undp.org', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(28, 'UNESCO Bureau Abidjan', 'Organisation Internationale', 'Cocody, Abidjan', '+225 22 44 23 70', 'abidjan@unesco.org', 'https://www.unesco.org', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(29, 'PIGIER Côte d\'Ivoire', 'Formation Professionnelle', 'Cocody, Abidjan', '+225 22 44 88 88', 'contact@pigier.ci', 'https://www.pigier.ci', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(30, 'AGITEL Formation', 'Formation IT', 'Marcory, Abidjan', '+225 21 26 75 00', 'info@agitelformation.ci', 'https://www.agitelformation.ci', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `escalades`
--

DROP TABLE IF EXISTS `escalades`;
CREATE TABLE IF NOT EXISTS `escalades` (
  `id_escalade` int NOT NULL AUTO_INCREMENT,
  `dossier_id` int DEFAULT NULL,
  `type_escalade` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `niveau_escalade` int DEFAULT '1',
  `description` text COLLATE utf8mb4_unicode_ci,
  `statut` enum('Ouverte','En_cours','Resolue','Fermee') COLLATE utf8mb4_unicode_ci DEFAULT 'Ouverte',
  `cree_par` int DEFAULT NULL,
  `assignee_a` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_escalade`),
  KEY `cree_par` (`cree_par`),
  KEY `assignee_a` (`assignee_a`),
  KEY `idx_dossier` (`dossier_id`),
  KEY `idx_statut` (`statut`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `escalades`
--

INSERT INTO `escalades` (`id_escalade`, `dossier_id`, `type_escalade`, `niveau_escalade`, `description`, `statut`, `cree_par`, `assignee_a`, `created_at`, `updated_at`) VALUES
(1, 5, 'commission_blocage', 2, 'Blocage au tour 2 - pas d\'unanimité après discussion', 'Resolue', 1, 80, '2026-01-16 11:56:13', '2026-01-16 11:56:13'),
(2, 6, 'delai_depasse', 1, 'Délai de 7 jours dépassé pour l\'avis encadreur', 'En_cours', 1, 50, '2026-01-16 11:56:13', '2026-01-16 11:56:13'),
(3, 4, 'jury_incomplet', 1, 'Un membre du jury a décliné sa participation à J-5', 'Ouverte', 1, 80, '2026-01-16 11:56:13', '2026-01-16 11:56:13');

-- --------------------------------------------------------

--
-- Structure de la table `escalades_actions`
--

DROP TABLE IF EXISTS `escalades_actions`;
CREATE TABLE IF NOT EXISTS `escalades_actions` (
  `id_action` int NOT NULL AUTO_INCREMENT,
  `escalade_id` int NOT NULL,
  `utilisateur_id` int NOT NULL,
  `type_action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_action`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `idx_escalade` (`escalade_id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `escalades_actions`
--

INSERT INTO `escalades_actions` (`id_action`, `escalade_id`, `utilisateur_id`, `type_action`, `description`, `created_at`) VALUES
(1, 1, 80, 'Prise_en_charge', 'Escalade prise en charge pour médiation', '2026-01-16 11:56:13'),
(2, 1, 80, 'Communication', 'Réunion organisée avec les membres divergents', '2026-01-16 11:56:13'),
(3, 1, 80, 'Resolution', 'Après discussion, consensus trouvé. Rapport validé.', '2026-01-16 11:56:13'),
(4, 2, 50, 'Prise_en_charge', 'Escalade prise en charge - contact de l\'encadreur', '2026-01-16 11:56:13'),
(5, 2, 50, 'Communication', 'Message envoyé à Dr. SANOGO Mariam', '2026-01-16 11:56:13'),
(6, 2, 50, 'Relance', 'Relance téléphonique effectuée', '2026-01-16 11:56:13'),
(7, 3, 80, 'Prise_en_charge', 'Recherche d\'un remplaçant en cours', '2026-01-16 11:56:13');

-- --------------------------------------------------------

--
-- Structure de la table `escalade_niveaux`
--

DROP TABLE IF EXISTS `escalade_niveaux`;
CREATE TABLE IF NOT EXISTS `escalade_niveaux` (
  `id_niveau` int NOT NULL AUTO_INCREMENT,
  `niveau` int NOT NULL,
  `nom_niveau` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delai_reponse_jours` int DEFAULT NULL,
  PRIMARY KEY (`id_niveau`),
  UNIQUE KEY `niveau` (`niveau`),
  KEY `idx_niveau` (`niveau`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `escalade_niveaux`
--

INSERT INTO `escalade_niveaux` (`id_niveau`, `niveau`, `nom_niveau`, `delai_reponse_jours`) VALUES
(1, 1, 'Responsable de niveau', 3),
(2, 2, 'Responsable de filière', 5),
(3, 3, 'Directeur adjoint', 7),
(4, 4, 'Doyen', 10);

-- --------------------------------------------------------

--
-- Structure de la table `etudiants`
--

DROP TABLE IF EXISTS `etudiants`;
CREATE TABLE IF NOT EXISTS `etudiants` (
  `id_etudiant` int NOT NULL AUTO_INCREMENT,
  `num_etu` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_etu` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom_etu` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_etu` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone_etu` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_naiss_etu` date DEFAULT NULL,
  `lieu_naiss_etu` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `genre_etu` enum('Homme','Femme','Autre') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `promotion_etu` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_etudiant`),
  UNIQUE KEY `num_etu` (`num_etu`),
  UNIQUE KEY `email_etu` (`email_etu`),
  KEY `idx_num` (`num_etu`),
  KEY `idx_nom` (`nom_etu`,`prenom_etu`),
  KEY `idx_email` (`email_etu`),
  KEY `idx_actif` (`actif`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `etudiants`
--

INSERT INTO `etudiants` (`id_etudiant`, `num_etu`, `nom_etu`, `prenom_etu`, `email_etu`, `telephone_etu`, `date_naiss_etu`, `lieu_naiss_etu`, `genre_etu`, `promotion_etu`, `actif`, `created_at`, `updated_at`) VALUES
(1, 'CI01552852', 'KONE', 'Adama', 'kone.adama@etudiant.ufhb.ci', '+225 05 06 07 01', '1999-03-15', 'Abidjan', 'Homme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(2, 'CI01552853', 'SANGARE', 'Fatou', 'sangare.fatou@etudiant.ufhb.ci', '+225 05 06 07 02', '2000-07-22', 'Bouaké', 'Femme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(3, 'CI01552854', 'BROU', 'Jean-Pierre', 'brou.jeanpierre@etudiant.ufhb.ci', '+225 05 06 07 03', '1999-11-08', 'Yamoussoukro', 'Homme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(4, 'CI01552855', 'ASSI', 'Marie-Claire', 'assi.marieclaire@etudiant.ufhb.ci', '+225 05 06 07 04', '2000-01-30', 'Abidjan', 'Femme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(5, 'CI01552856', 'KONAN', 'Yves', 'konan.yves@etudiant.ufhb.ci', '+225 05 06 07 05', '1998-09-12', 'San-Pédro', 'Homme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(6, 'CI01552857', 'OUATTARA', 'Mariam', 'ouattara.mariam@etudiant.ufhb.ci', '+225 05 06 07 06', '1999-05-25', 'Korhogo', 'Femme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(7, 'CI01552858', 'ZADI', 'Emmanuel', 'zadi.emmanuel@etudiant.ufhb.ci', '+225 05 06 07 07', '2000-02-14', 'Daloa', 'Homme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(8, 'CI01552859', 'AKA', 'Cynthia', 'aka.cynthia@etudiant.ufhb.ci', '+225 05 06 07 08', '1999-08-03', 'Abidjan', 'Femme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(9, 'CI01552860', 'GNAMBA', 'Patrick', 'gnamba.patrick@etudiant.ufhb.ci', '+225 05 06 07 09', '1998-12-19', 'Man', 'Homme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(10, 'CI01552861', 'N\'DRI', 'Adjoua', 'ndri.adjoua@etudiant.ufhb.ci', '+225 05 06 07 10', '2000-04-07', 'Abidjan', 'Femme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(11, 'CI01552862', 'LAGO', 'Constant', 'lago.constant@etudiant.ufhb.ci', '+225 05 06 07 11', '1999-06-28', 'Gagnoa', 'Homme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(12, 'CI01552863', 'EHUI', 'Sandrine', 'ehui.sandrine@etudiant.ufhb.ci', '+225 05 06 07 12', '2000-10-11', 'Abidjan', 'Femme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(13, 'CI01552864', 'TAPE', 'Didier', 'tape.didier@etudiant.ufhb.ci', '+225 05 06 07 13', '1998-07-04', 'Divo', 'Homme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(14, 'CI01552865', 'GBADJE', 'Félicité', 'gbadje.felicite@etudiant.ufhb.ci', '+225 05 06 07 14', '1999-02-17', 'Abidjan', 'Femme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(15, 'CI01552866', 'YAPI', 'Serge', 'yapi.serge@etudiant.ufhb.ci', '+225 05 06 07 15', '2000-09-23', 'Abengourou', 'Homme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(16, 'CI01552867', 'DAGO', 'Estelle', 'dago.estelle@etudiant.ufhb.ci', '+225 05 06 07 16', '1999-04-01', 'Abidjan', 'Femme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(17, 'CI01552868', 'ASSEMIAN', 'Rodrigue', 'assemian.rodrigue@etudiant.ufhb.ci', '+225 05 06 07 17', '1998-11-29', 'Bondoukou', 'Homme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(18, 'CI01552869', 'ANOH', 'Prisca', 'anoh.prisca@etudiant.ufhb.ci', '+225 05 06 07 18', '2000-06-16', 'Abidjan', 'Femme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(19, 'CI01552870', 'GNAGNE', 'Martial', 'gnagne.martial@etudiant.ufhb.ci', '+225 05 06 07 19', '1999-01-09', 'Dabou', 'Homme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(20, 'CI01552871', 'AMON', 'Esther', 'amon.esther@etudiant.ufhb.ci', '+225 05 06 07 20', '2000-08-21', 'Abidjan', 'Femme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(21, 'CI01552872', 'ADJE', 'Boris', 'adje.boris@etudiant.ufhb.ci', '+225 05 06 07 21', '1998-10-05', 'Agboville', 'Homme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(22, 'CI01552873', 'BONI', 'Rachelle', 'boni.rachelle@etudiant.ufhb.ci', '+225 05 06 07 22', '1999-12-12', 'Abidjan', 'Femme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(23, 'CI01552874', 'KOUADIO', 'Franck', 'kouadio.franck@etudiant.ufhb.ci', '+225 05 06 07 23', '2000-03-27', 'Bouaké', 'Homme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(24, 'CI01552875', 'NIAMKE', 'Christiane', 'niamke.christiane@etudiant.ufhb.ci', '+225 05 06 07 24', '1999-07-08', 'Abidjan', 'Femme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(25, 'CI01552876', 'TANOH', 'Germain', 'tanoh.germain@etudiant.ufhb.ci', '+225 05 06 07 25', '1998-05-14', 'Dimbokro', 'Homme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(26, 'CI01552877', 'AHOURE', 'Vanessa', 'ahoure.vanessa@etudiant.ufhb.ci', '+225 05 06 07 26', '2000-11-02', 'Abidjan', 'Femme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(27, 'CI01552878', 'YEO', 'Ibrahim', 'yeo.ibrahim@etudiant.ufhb.ci', '+225 05 06 07 27', '1999-09-18', 'Korhogo', 'Homme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(28, 'CI01552879', 'GBAHI', 'Viviane', 'gbahi.viviane@etudiant.ufhb.ci', '+225 05 06 07 28', '2000-01-25', 'Abidjan', 'Femme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(29, 'CI01552880', 'BAMBA', 'Moussa', 'bamba.moussa@etudiant.ufhb.ci', '+225 05 06 07 29', '1998-08-07', 'Odienné', 'Homme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(30, 'CI01552881', 'FANNY', 'Mariame', 'fanny.mariame@etudiant.ufhb.ci', '+225 05 06 07 30', '1999-04-20', 'Abidjan', 'Femme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(31, 'CI01552882', 'DIOMANDE', 'Sekou', 'diomande.sekou@etudiant.ufhb.ci', '+225 05 06 07 31', '2000-07-13', 'Séguéla', 'Homme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(32, 'CI01552883', 'KOFFI', 'Ange', 'koffi.ange@etudiant.ufhb.ci', '+225 05 06 07 32', '1999-02-28', 'Abidjan', 'Femme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(33, 'CI01552884', 'EHOUMAN', 'Paterne', 'ehouman.paterne@etudiant.ufhb.ci', '+225 05 06 07 33', '1998-06-10', 'Adzopé', 'Homme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(34, 'CI01552885', 'TIA', 'Nadège', 'tia.nadege@etudiant.ufhb.ci', '+225 05 06 07 34', '2000-12-03', 'Abidjan', 'Femme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(35, 'CI01552886', 'GUEI', 'Sylvain', 'guei.sylvain@etudiant.ufhb.ci', '+225 05 06 07 35', '1999-10-22', 'Man', 'Homme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(36, 'CI01552887', 'OKOU', 'Florence', 'okou.florence@etudiant.ufhb.ci', '+225 05 06 07 36', '2000-05-08', 'Abidjan', 'Femme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(37, 'CI01552888', 'DJE', 'Wilfried', 'dje.wilfried@etudiant.ufhb.ci', '+225 05 06 07 37', '1998-03-16', 'Soubré', 'Homme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(38, 'CI01552889', 'KACOU', 'Bernadette', 'kacou.bernadette@etudiant.ufhb.ci', '+225 05 06 07 38', '1999-11-30', 'Abidjan', 'Femme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(39, 'CI01552890', 'TOURE', 'Mamadou', 'toure.mamadou@etudiant.ufhb.ci', '+225 05 06 07 39', '2000-02-06', 'Touba', 'Homme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(40, 'CI01552891', 'ALLOU', 'Pascale', 'allou.pascale@etudiant.ufhb.ci', '+225 05 06 07 40', '1999-08-19', 'Abidjan', 'Femme', '2024-2025', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `exonerations`
--

DROP TABLE IF EXISTS `exonerations`;
CREATE TABLE IF NOT EXISTS `exonerations` (
  `id_exoneration` int NOT NULL AUTO_INCREMENT,
  `etudiant_id` int NOT NULL,
  `annee_acad_id` int NOT NULL,
  `montant_exonere` decimal(10,2) NOT NULL,
  `pourcentage_exonere` decimal(5,2) DEFAULT NULL,
  `motif` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_attribution` date NOT NULL,
  `approuve_par` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_exoneration`),
  KEY `approuve_par` (`approuve_par`),
  KEY `idx_etudiant` (`etudiant_id`),
  KEY `idx_annee` (`annee_acad_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `exonerations`
--

INSERT INTO `exonerations` (`id_exoneration`, `etudiant_id`, `annee_acad_id`, `montant_exonere`, `pourcentage_exonere`, `motif`, `date_attribution`, `approuve_par`, `created_at`) VALUES
(1, 13, 1, 100000.00, 18.18, 'Bourse d\'excellence académique', '2024-09-10', 2, '2026-01-16 10:27:05'),
(2, 30, 1, 50000.00, 9.09, 'Situation sociale difficile - dossier validé', '2024-09-12', 2, '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `exonerations_appliquees`
--

DROP TABLE IF EXISTS `exonerations_appliquees`;
CREATE TABLE IF NOT EXISTS `exonerations_appliquees` (
  `id_exoneration_appliquee` int NOT NULL AUTO_INCREMENT,
  `demande_exoneration_id` int NOT NULL,
  `paiement_id` int DEFAULT NULL,
  `montant_exonere` decimal(10,2) NOT NULL,
  `date_application` datetime DEFAULT CURRENT_TIMESTAMP,
  `applique_par` int NOT NULL,
  PRIMARY KEY (`id_exoneration_appliquee`),
  KEY `paiement_id` (`paiement_id`),
  KEY `applique_par` (`applique_par`),
  KEY `idx_demande` (`demande_exoneration_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `exonerations_types`
--

DROP TABLE IF EXISTS `exonerations_types`;
CREATE TABLE IF NOT EXISTS `exonerations_types` (
  `id_type_exoneration` int NOT NULL AUTO_INCREMENT,
  `code_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `pourcentage_reduction` decimal(5,2) DEFAULT NULL,
  `montant_fixe` decimal(10,2) DEFAULT NULL,
  `conditions_eligibilite` json DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_type_exoneration`),
  UNIQUE KEY `code_type` (`code_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `fonctions`
--

DROP TABLE IF EXISTS `fonctions`;
CREATE TABLE IF NOT EXISTS `fonctions` (
  `id_fonction` int NOT NULL AUTO_INCREMENT,
  `lib_fonction` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `actif` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_fonction`),
  UNIQUE KEY `lib_fonction` (`lib_fonction`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `fonctions`
--

INSERT INTO `fonctions` (`id_fonction`, `lib_fonction`, `description`, `actif`) VALUES
(1, 'Enseignant', 'Directeur de département', 1),
(2, 'Responsable de filière', 'Responsable d\'une filière', 1),
(3, 'Responsable de niveau', 'Responsable d\'un niveau', 1),
(4, 'Directeur adjoint', 'Enseignant et recherche', 1),
(5, 'Doyen', 'Secrétariat administratif', 1),
(6, 'Secrétaire', 'Service scolarité', 1),
(7, 'Agent de scolarité', 'Agent du service scolarité', 1),
(8, 'Agent communication', 'Agent du service communication', 1);

-- --------------------------------------------------------

--
-- Structure de la table `grades`
--

DROP TABLE IF EXISTS `grades`;
CREATE TABLE IF NOT EXISTS `grades` (
  `id_grade` int NOT NULL AUTO_INCREMENT,
  `lib_grade` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `niveau_hierarchique` int DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_grade`),
  UNIQUE KEY `lib_grade` (`lib_grade`),
  KEY `idx_niveau` (`niveau_hierarchique`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `grades`
--

INSERT INTO `grades` (`id_grade`, `lib_grade`, `niveau_hierarchique`, `actif`) VALUES
(1, 'Assistant', 1, 1),
(2, 'Maître-Assistant', 2, 1),
(3, 'Maître de Conférences', 3, 1),
(4, 'Professeur Titulaire', 4, 1),
(5, 'Professeur Émérite', 5, 1);

-- --------------------------------------------------------

--
-- Structure de la table `groupes`
--

DROP TABLE IF EXISTS `groupes`;
CREATE TABLE IF NOT EXISTS `groupes` (
  `id_groupe` int NOT NULL AUTO_INCREMENT,
  `nom_groupe` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `niveau_hierarchique` int DEFAULT '0',
  `actif` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_groupe`),
  KEY `idx_actif` (`actif`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `groupes`
--

INSERT INTO `groupes` (`id_groupe`, `nom_groupe`, `description`, `niveau_hierarchique`, `actif`, `created_at`) VALUES
(1, 'Administrateur', 'Contrôle total du système, configuration, utilisateurs', 5, 1, '2026-01-15 13:43:50'),
(2, 'Secrétaire', 'Gestion documentaire, archivage', 6, 1, '2026-01-15 13:43:50'),
(3, 'Communication', 'Vérification format des rapports', 7, 1, '2026-01-15 13:43:50'),
(4, 'Scolarité', 'Paiements, candidatures, inscriptions', 8, 1, '2026-01-15 13:43:50'),
(5, 'Resp. Filière', 'Supervision filière MIAGE', 9, 1, '2026-01-15 13:43:50'),
(6, 'Resp. Niveau', 'Gestion Master 2', 10, 1, '2026-01-15 13:43:50'),
(7, 'Commission', 'Évaluation rapports, votes', 11, 1, '2026-01-15 13:43:50'),
(8, 'Enseignant', 'Supervision, participation jury', 12, 1, '2026-01-15 13:43:50'),
(9, 'Étudiant', 'Rédaction rapport, soumissions', 13, 1, '2026-01-15 13:43:50'),
(10, 'Président Commission', 'Constitution des jurys', 14, 1, '2026-01-15 13:43:50'),
(11, 'Président Jury', 'Saisie notes jour J (rôle temporaire)', 15, 1, '2026-01-15 13:43:50'),
(12, 'Directeur Mémoire', 'Direction scientifique', 16, 1, '2026-01-15 13:43:50'),
(13, 'Encadreur Pédagogique', 'Accompagnement étudiant', 17, 1, '2026-01-15 13:43:50');

-- --------------------------------------------------------

--
-- Structure de la table `groupe_utilisateur`
--

DROP TABLE IF EXISTS `groupe_utilisateur`;
CREATE TABLE IF NOT EXISTS `groupe_utilisateur` (
  `id_GU` int NOT NULL AUTO_INCREMENT,
  `lib_GU` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `niveau_hierarchique` int DEFAULT NULL,
  PRIMARY KEY (`id_GU`),
  UNIQUE KEY `lib_GU` (`lib_GU`),
  KEY `idx_niveau` (`niveau_hierarchique`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `historique_entites`
--

DROP TABLE IF EXISTS `historique_entites`;
CREATE TABLE IF NOT EXISTS `historique_entites` (
  `id_historique` int NOT NULL AUTO_INCREMENT,
  `entite_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entite_id` int NOT NULL,
  `version` int NOT NULL,
  `snapshot_json` json NOT NULL,
  `modifie_par` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_historique`),
  KEY `modifie_par` (`modifie_par`),
  KEY `idx_entite` (`entite_type`,`entite_id`),
  KEY `idx_version` (`version`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `historique_entites`
--

INSERT INTO `historique_entites` (`id_historique`, `entite_type`, `entite_id`, `version`, `snapshot_json`, `modifie_par`, `created_at`) VALUES
(1, 'etudiant', 1, 1, '{\"nom_etu\": \"KONE\", \"num_etu\": \"CI01552852\", \"email_etu\": \"kone.adama@etudiant.ufhb.ci\", \"prenom_etu\": \"Adama\"}', 30, '2026-01-16 10:27:05'),
(2, 'candidature', 1, 1, '{\"theme\": \"Système de gestion de stock avec prédiction ML\", \"entreprise_id\": 1, \"date_soumission\": \"2024-10-01\"}', 100, '2026-01-16 10:27:05'),
(3, 'candidature', 1, 2, '{\"theme\": \"Système de gestion de stock avec prédiction de la demande par Machine Learning\", \"entreprise_id\": 1, \"validee_scolarite\": true}', 30, '2026-01-16 10:27:05'),
(4, 'rapport', 1, 1, '{\"titre\": \"Système de gestion de stock ML\", \"statut\": \"Brouillon\", \"version\": 1}', 100, '2026-01-16 10:27:05'),
(5, 'rapport', 1, 2, '{\"titre\": \"Système de gestion de stock avec prédiction ML\", \"statut\": \"Soumis\", \"version\": 2}', 100, '2026-01-16 10:27:05'),
(6, 'rapport', 1, 3, '{\"titre\": \"Système de gestion de stock avec prédiction ML\", \"statut\": \"Valide\", \"version\": 3}', 80, '2026-01-16 10:27:05'),
(7, 'dossier', 1, 1, '{\"etat_actuel_id\": 1, \"date_entree_etat\": \"2024-09-15\"}', 30, '2026-01-16 10:27:05'),
(8, 'dossier', 1, 2, '{\"etat_actuel_id\": 14, \"diplome_delivre\": true, \"date_entree_etat\": \"2024-12-15\"}', 30, '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `imports_configurations`
--

DROP TABLE IF EXISTS `imports_configurations`;
CREATE TABLE IF NOT EXISTS `imports_configurations` (
  `id_config_import` int NOT NULL AUTO_INCREMENT,
  `nom_configuration` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_import` enum('etudiants','enseignants','notes','paiements','dossiers','entreprises') COLLATE utf8mb4_unicode_ci NOT NULL,
  `format_fichier` enum('csv','excel','xml','json') COLLATE utf8mb4_unicode_ci NOT NULL,
  `mapping_colonnes` json NOT NULL,
  `regles_validation` json DEFAULT NULL,
  `transformation_donnees` json DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `created_by` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_config_import`),
  UNIQUE KEY `uk_nom_type` (`nom_configuration`,`type_import`),
  KEY `created_by` (`created_by`),
  KEY `idx_type` (`type_import`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `imports_historiques`
--

DROP TABLE IF EXISTS `imports_historiques`;
CREATE TABLE IF NOT EXISTS `imports_historiques` (
  `id_import` int NOT NULL AUTO_INCREMENT,
  `type_import` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fichier_nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nb_lignes_totales` int DEFAULT NULL,
  `nb_lignes_reussies` int DEFAULT NULL,
  `nb_lignes_erreurs` int DEFAULT NULL,
  `erreurs_json` json DEFAULT NULL,
  `importe_par` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_import`),
  KEY `importe_par` (`importe_par`),
  KEY `idx_type` (`type_import`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `imports_lignes_details`
--

DROP TABLE IF EXISTS `imports_lignes_details`;
CREATE TABLE IF NOT EXISTS `imports_lignes_details` (
  `id_ligne_detail` int NOT NULL AUTO_INCREMENT,
  `session_import_id` int NOT NULL,
  `numero_ligne` int NOT NULL,
  `donnees_brutes` json DEFAULT NULL,
  `donnees_transformees` json DEFAULT NULL,
  `statut` enum('succes','erreur','avertissement','ignore') COLLATE utf8mb4_unicode_ci NOT NULL,
  `messages` json DEFAULT NULL COMMENT 'Tableau de messages d erreur/warning',
  `entite_id` int DEFAULT NULL COMMENT 'ID de l entité créée/modifiée',
  `entite_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Type d entité',
  `date_traitement` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_ligne_detail`),
  KEY `idx_session` (`session_import_id`),
  KEY `idx_statut` (`statut`),
  KEY `idx_entite` (`entite_type`,`entite_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `imports_rollback_data`
--

DROP TABLE IF EXISTS `imports_rollback_data`;
CREATE TABLE IF NOT EXISTS `imports_rollback_data` (
  `id_rollback` int NOT NULL AUTO_INCREMENT,
  `session_import_id` int NOT NULL,
  `table_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `record_id` int NOT NULL,
  `operation` enum('insert','update','delete') COLLATE utf8mb4_unicode_ci NOT NULL,
  `ancienne_valeur` json DEFAULT NULL,
  `nouvelle_valeur` json DEFAULT NULL,
  `rollback_effectue` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_rollback`),
  KEY `idx_session` (`session_import_id`),
  KEY `idx_table` (`table_name`,`record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `imports_sessions`
--

DROP TABLE IF EXISTS `imports_sessions`;
CREATE TABLE IF NOT EXISTS `imports_sessions` (
  `id_session_import` int NOT NULL AUTO_INCREMENT,
  `config_import_id` int DEFAULT NULL,
  `nom_fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chemin_fichier` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taille_fichier` bigint DEFAULT NULL,
  `hash_fichier` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_lignes_total` int DEFAULT NULL,
  `nombre_lignes_traitees` int DEFAULT '0',
  `nombre_succes` int DEFAULT '0',
  `nombre_erreurs` int DEFAULT '0',
  `nombre_avertissements` int DEFAULT '0',
  `statut` enum('en_attente','en_cours','termine','erreur','annule') COLLATE utf8mb4_unicode_ci DEFAULT 'en_attente',
  `progression_pourcent` decimal(5,2) DEFAULT '0.00',
  `date_debut` datetime DEFAULT NULL,
  `date_fin` datetime DEFAULT NULL,
  `duree_secondes` int DEFAULT NULL,
  `importe_par` int NOT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `erreur_globale` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_session_import`),
  KEY `config_import_id` (`config_import_id`),
  KEY `importe_par` (`importe_par`),
  KEY `idx_statut` (`statut`),
  KEY `idx_date` (`date_debut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jury_membres`
--

DROP TABLE IF EXISTS `jury_membres`;
CREATE TABLE IF NOT EXISTS `jury_membres` (
  `id_membre_jury` int NOT NULL AUTO_INCREMENT,
  `dossier_id` int NOT NULL,
  `enseignant_id` int NOT NULL,
  `role_jury` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut_acceptation` enum('Invite','Accepte','Refuse') COLLATE utf8mb4_unicode_ci DEFAULT 'Invite',
  `date_invitation` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_reponse` datetime DEFAULT NULL,
  PRIMARY KEY (`id_membre_jury`),
  UNIQUE KEY `unique_jury_membre` (`dossier_id`,`enseignant_id`),
  KEY `enseignant_id` (`enseignant_id`),
  KEY `idx_dossier` (`dossier_id`),
  KEY `idx_statut` (`statut_acceptation`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `jury_membres`
--

INSERT INTO `jury_membres` (`id_membre_jury`, `dossier_id`, `enseignant_id`, `role_jury`, `statut_acceptation`, `date_invitation`, `date_reponse`) VALUES
(1, 1, 1, 'PRESIDENT', 'Accepte', '2024-11-20 00:00:00', '2024-11-21 00:00:00'),
(2, 1, 6, 'DIRECTEUR', 'Accepte', '2024-11-20 00:00:00', '2024-11-21 00:00:00'),
(3, 1, 7, 'RAPPORTEUR', 'Accepte', '2024-11-20 00:00:00', '2024-11-22 00:00:00'),
(4, 1, 13, 'EXAMINATEUR', 'Accepte', '2024-11-20 00:00:00', '2024-11-21 00:00:00'),
(5, 1, 29, 'MAITRE_STAGE', 'Accepte', '2024-11-20 00:00:00', '2024-11-23 00:00:00'),
(6, 2, 2, 'PRESIDENT', 'Accepte', '2024-11-22 00:00:00', '2024-11-23 00:00:00'),
(7, 2, 8, 'DIRECTEUR', 'Accepte', '2024-11-22 00:00:00', '2024-11-23 00:00:00'),
(8, 2, 9, 'RAPPORTEUR', 'Accepte', '2024-11-22 00:00:00', '2024-11-24 00:00:00'),
(9, 2, 14, 'EXAMINATEUR', 'Accepte', '2024-11-22 00:00:00', '2024-11-23 00:00:00'),
(10, 2, 30, 'MAITRE_STAGE', 'Accepte', '2024-11-22 00:00:00', '2024-11-25 00:00:00'),
(11, 3, 3, 'PRESIDENT', 'Accepte', '2024-11-25 00:00:00', '2024-11-26 00:00:00'),
(12, 3, 10, 'DIRECTEUR', 'Accepte', '2024-11-25 00:00:00', '2024-11-26 00:00:00'),
(13, 3, 11, 'RAPPORTEUR', 'Accepte', '2024-11-25 00:00:00', '2024-11-27 00:00:00'),
(14, 3, 15, 'EXAMINATEUR', 'Accepte', '2024-11-25 00:00:00', '2024-11-26 00:00:00'),
(15, 3, 29, 'MAITRE_STAGE', 'Accepte', '2024-11-25 00:00:00', '2024-11-28 00:00:00'),
(16, 4, 4, 'PRESIDENT', 'Accepte', '2024-11-28 00:00:00', '2024-11-29 00:00:00'),
(17, 4, 12, 'DIRECTEUR', 'Accepte', '2024-11-28 00:00:00', '2024-11-29 00:00:00'),
(18, 4, 6, 'RAPPORTEUR', 'Invite', '2024-11-28 00:00:00', NULL),
(19, 4, 16, 'EXAMINATEUR', 'Accepte', '2024-11-28 00:00:00', '2024-11-30 00:00:00'),
(20, 4, 30, 'MAITRE_STAGE', 'Invite', '2024-11-28 00:00:00', NULL),
(21, 5, 5, 'PRESIDENT', 'Invite', '2024-12-01 00:00:00', NULL),
(22, 5, 7, 'DIRECTEUR', 'Accepte', '2024-12-01 00:00:00', '2024-12-02 00:00:00'),
(23, 5, 8, 'RAPPORTEUR', 'Invite', '2024-12-01 00:00:00', NULL),
(24, 5, 17, 'EXAMINATEUR', 'Invite', '2024-12-01 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `maintenance_mode`
--

DROP TABLE IF EXISTS `maintenance_mode`;
CREATE TABLE IF NOT EXISTS `maintenance_mode` (
  `id` int NOT NULL AUTO_INCREMENT,
  `actif` tinyint(1) DEFAULT '0',
  `message` text COLLATE utf8mb4_unicode_ci,
  `debut_maintenance` datetime DEFAULT NULL,
  `fin_maintenance` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `maintenance_mode`
--

INSERT INTO `maintenance_mode` (`id`, `actif`, `message`, `debut_maintenance`, `fin_maintenance`, `created_at`, `updated_at`) VALUES
(1, 0, NULL, NULL, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `maintenance_planifiee`
--

DROP TABLE IF EXISTS `maintenance_planifiee`;
CREATE TABLE IF NOT EXISTS `maintenance_planifiee` (
  `id_maintenance` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `type_maintenance` enum('complete','partielle','urgente') COLLATE utf8mb4_unicode_ci DEFAULT 'complete',
  `services_affectes` json DEFAULT NULL COMMENT 'Liste des services/modules affectés',
  `message_utilisateurs` text COLLATE utf8mb4_unicode_ci,
  `statut` enum('planifiee','en_cours','terminee','annulee') COLLATE utf8mb4_unicode_ci DEFAULT 'planifiee',
  `notification_envoyee` tinyint(1) DEFAULT '0',
  `planifie_par` int NOT NULL,
  `annule_par` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_maintenance`),
  KEY `planifie_par` (`planifie_par`),
  KEY `annule_par` (`annule_par`),
  KEY `idx_dates` (`date_debut`,`date_fin`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `mentions`
--

DROP TABLE IF EXISTS `mentions`;
CREATE TABLE IF NOT EXISTS `mentions` (
  `id_mention` int NOT NULL AUTO_INCREMENT,
  `code_mention` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle_mention` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note_min` decimal(5,2) NOT NULL,
  `note_max` decimal(5,2) NOT NULL,
  `ordre_affichage` int DEFAULT NULL,
  PRIMARY KEY (`id_mention`),
  UNIQUE KEY `code_mention` (`code_mention`),
  KEY `idx_code` (`code_mention`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `mentions`
--

INSERT INTO `mentions` (`id_mention`, `code_mention`, `libelle_mention`, `note_min`, `note_max`, `ordre_affichage`) VALUES
(1, 'AJOURNÉ', 'Ajourné', 0.00, 9.99, 1),
(2, 'PASSABLE', 'Passable', 10.00, 11.99, 2),
(3, 'ASSEZ_BIEN', 'Assez Bien', 12.00, 13.99, 3),
(4, 'BIEN', 'Bien', 14.00, 15.99, 4),
(5, 'TRES_BIEN', 'Très Bien', 16.00, 17.99, 5),
(6, 'EXCELLENT', 'Excellent', 18.00, 20.00, 6);

-- --------------------------------------------------------

--
-- Structure de la table `messages_internes`
--

DROP TABLE IF EXISTS `messages_internes`;
CREATE TABLE IF NOT EXISTS `messages_internes` (
  `id_message` int NOT NULL AUTO_INCREMENT,
  `expediteur_id` int NOT NULL,
  `destinataire_id` int NOT NULL,
  `sujet` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contenu` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `lu` tinyint(1) DEFAULT '0',
  `date_lecture` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_message`),
  KEY `expediteur_id` (`expediteur_id`),
  KEY `idx_destinataire` (`destinataire_id`),
  KEY `idx_lu` (`lu`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `messages_internes`
--

INSERT INTO `messages_internes` (`id_message`, `expediteur_id`, `destinataire_id`, `sujet`, `contenu`, `lu`, `date_lecture`, `created_at`) VALUES
(1, 1, 100, 'Bienvenue sur CheckMaster', 'Bonjour KONE Adama,\n\nBienvenue sur la plateforme CheckMaster. Votre compte a été créé avec succès.\n\nVous pouvez maintenant accéder à votre espace étudiant pour:\n- Soumettre votre candidature\n- Suivre l\'avancement de votre dossier\n- Rédiger votre rapport\n\nCordialement,\nL\'équipe CheckMaster', 1, '2024-09-16 08:30:00', '2026-01-16 10:27:05'),
(2, 1, 101, 'Bienvenue sur CheckMaster', 'Bonjour SANGARE Fatou,\n\nBienvenue sur la plateforme CheckMaster...', 1, '2024-09-16 09:15:00', '2026-01-16 10:27:05'),
(3, 1, 102, 'Bienvenue sur CheckMaster', 'Bonjour BROU Jean-Pierre,\n\nBienvenue sur la plateforme CheckMaster...', 1, '2024-09-16 10:00:00', '2026-01-16 10:27:05'),
(4, 30, 100, 'Candidature validée', 'Bonjour,\n\nVotre candidature a été validée par le service scolarité.\n\nVous pouvez maintenant accéder à la rédaction de votre rapport dans votre espace étudiant.\n\nCordialement,\nService Scolarité', 1, '2024-10-06 14:30:00', '2026-01-16 10:27:05'),
(5, 20, 100, 'Format rapport validé', 'Bonjour,\n\nLe format de votre rapport a été validé par le service communication.\n\nVotre dossier est transmis à la commission.\n\nCordialement,\nService Communication', 1, '2024-10-09 16:45:00', '2026-01-16 10:27:05'),
(6, 60, 100, 'Retour sur votre rapport', 'Bonjour Adama,\n\nJ\'ai parcouru votre rapport. Dans l\'ensemble, c\'est un bon travail.\n\nQuelques points à améliorer:\n- Approfondir la partie méthodologique\n- Ajouter plus de références récentes\n\nÀ bientôt,\nDr. KOUASSI', 1, '2024-10-20 11:00:00', '2026-01-16 10:27:05'),
(7, 100, 60, 'Re: Retour sur votre rapport', 'Bonjour Dr. KOUASSI,\n\nMerci pour votre retour. J\'ai pris note de vos remarques et je vais procéder aux modifications suggérées.\n\nCordialement,\nAdama KONE', 1, '2024-10-20 14:30:00', '2026-01-16 10:27:05'),
(8, 70, 101, 'Point sur votre avancement', 'Bonjour Fatou,\n\nPouvez-vous me faire un point sur l\'avancement de votre stage et de votre rapport?\n\nCordialement,\nDr. SANOGO', 1, '2024-10-25 09:00:00', '2026-01-16 10:27:05'),
(9, 80, 100, 'Rapport validé par la Commission', 'Bonjour,\n\nNous avons le plaisir de vous informer que votre rapport a été validé par la Commission de validation.\n\nVotre dossier passe maintenant à l\'étape suivante.\n\nFélicitations,\nLe Président de la Commission', 1, '2024-10-30 17:00:00', '2026-01-16 10:27:05'),
(10, 30, 112, 'Rappel - Documents manquants', 'Bonjour,\n\nIl manque encore les documents suivants à votre dossier:\n- Attestation d\'assurance\n- Convention de stage signée\n\nMerci de les transmettre au plus vite.\n\nService Scolarité', 0, NULL, '2026-01-16 10:27:05'),
(11, 1, 110, 'Mise à jour disponible', 'Bonjour,\n\nUne nouvelle fonctionnalité est disponible sur CheckMaster: vous pouvez maintenant télécharger vos reçus directement depuis votre espace.\n\nL\'équipe CheckMaster', 0, NULL, '2026-01-16 10:27:05'),
(12, 60, 61, 'Session commission du 18/11', 'Bonjour Fatoumata,\n\nAs-tu eu le temps de consulter les 3 rapports assignés pour la prochaine session?\n\nCordialement,\nAya', 1, '2024-11-15 10:30:00', '2026-01-16 10:27:05'),
(13, 61, 60, 'Re: Session commission du 18/11', 'Bonjour Aya,\n\nOui, j\'ai terminé la lecture. On pourra en discuter avant la session si tu veux.\n\nFatoumata', 1, '2024-11-15 11:45:00', '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `metriques_performance`
--

DROP TABLE IF EXISTS `metriques_performance`;
CREATE TABLE IF NOT EXISTS `metriques_performance` (
  `id_metrique` int NOT NULL AUTO_INCREMENT,
  `endpoint` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `methode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `temps_reponse_ms` int NOT NULL,
  `temps_db_ms` int DEFAULT NULL,
  `memoire_mb` decimal(10,2) DEFAULT NULL,
  `statut_http` int DEFAULT NULL,
  `utilisateur_id` int DEFAULT NULL,
  `ip_adresse` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `timestamp_metrique` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_metrique`),
  KEY `idx_endpoint` (`endpoint`),
  KEY `idx_timestamp` (`timestamp_metrique`),
  KEY `idx_statut` (`statut_http`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id_migration` int NOT NULL AUTO_INCREMENT,
  `migration_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_migration`),
  UNIQUE KEY `migration_name` (`migration_name`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id_migration`, `migration_name`, `executed_at`) VALUES
(1, '002_add_rapport_annotations', '2026-01-16 10:27:02'),
(2, '004_add_exonerations', '2026-01-16 10:27:02'),
(3, '005_add_permissions_actions', '2026-01-16 10:27:02'),
(4, '006_add_workflow_historique', '2026-01-16 10:27:03'),
(5, '008_add_maintenance_mode', '2026-01-16 10:27:03'),
(6, '009_add_stats_cache', '2026-01-16 10:27:04'),
(7, '010_add_documents_generes', '2026-01-16 10:27:04'),
(8, '012_add_roles_temporaires', '2026-01-16 10:27:04'),
(9, '003_add_commission_sessions', '2026-01-16 11:56:10'),
(10, '007_add_imports_historiques', '2026-01-16 11:56:10'),
(11, '011_add_sessions_commission_participants', '2026-01-16 11:56:10'),
(12, '013_add_fulltext_indexes', '2026-01-16 11:56:13'),
(13, '014_add_reclamations_audit_logs', '2026-01-16 11:56:13');

-- --------------------------------------------------------

--
-- Structure de la table `niveau_acces_donnees`
--

DROP TABLE IF EXISTS `niveau_acces_donnees`;
CREATE TABLE IF NOT EXISTS `niveau_acces_donnees` (
  `id_niv_acces_donnee` int NOT NULL AUTO_INCREMENT,
  `lib_niveau_acces` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_niv_acces_donnee`),
  UNIQUE KEY `lib_niveau_acces` (`lib_niveau_acces`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `niveau_acces_donnees`
--

INSERT INTO `niveau_acces_donnees` (`id_niv_acces_donnee`, `lib_niveau_acces`, `description`) VALUES
(1, 'Lecture seule', 'Peut uniquement consulter les données'),
(2, 'Lecture/Écriture', 'Peut consulter et modifier les données'),
(3, 'Complet', 'Accès total incluant la suppression'),
(4, 'Administrateur', 'Accès système complet');

-- --------------------------------------------------------

--
-- Structure de la table `niveau_approbation`
--

DROP TABLE IF EXISTS `niveau_approbation`;
CREATE TABLE IF NOT EXISTS `niveau_approbation` (
  `id_niveau_approbation` int NOT NULL AUTO_INCREMENT,
  `lib_niveau` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordre_niveau` int DEFAULT NULL,
  PRIMARY KEY (`id_niveau_approbation`),
  UNIQUE KEY `lib_niveau` (`lib_niveau`),
  KEY `idx_ordre` (`ordre_niveau`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `niveau_approbation`
--

INSERT INTO `niveau_approbation` (`id_niveau_approbation`, `lib_niveau`, `ordre_niveau`) VALUES
(1, 'Auto-validation', 1),
(2, 'Validation scolarité', 2),
(3, 'Validation responsable', 3),
(4, 'Validation commission', 4),
(5, 'Validation direction', 5);

-- --------------------------------------------------------

--
-- Structure de la table `niveau_etude`
--

DROP TABLE IF EXISTS `niveau_etude`;
CREATE TABLE IF NOT EXISTS `niveau_etude` (
  `id_niveau` int NOT NULL AUTO_INCREMENT,
  `lib_niveau` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ordre_niveau` int DEFAULT NULL,
  PRIMARY KEY (`id_niveau`),
  UNIQUE KEY `lib_niveau` (`lib_niveau`),
  KEY `idx_ordre` (`ordre_niveau`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `niveau_etude`
--

INSERT INTO `niveau_etude` (`id_niveau`, `lib_niveau`, `description`, `ordre_niveau`) VALUES
(1, 'Licence 1', 'Première année de licence', 1),
(2, 'Licence 2', 'Deuxième année de licence', 2),
(3, 'Licence 3', 'Troisième année de licence', 3),
(4, 'Master 1', 'Première année de master', 4),
(5, 'Master 2', 'Deuxième année de master (mémoire)', 5),
(6, 'Doctorat', 'Formation doctorale', 6);

-- --------------------------------------------------------

--
-- Structure de la table `notes_soutenance`
--

DROP TABLE IF EXISTS `notes_soutenance`;
CREATE TABLE IF NOT EXISTS `notes_soutenance` (
  `id_note` int NOT NULL AUTO_INCREMENT,
  `soutenance_id` int NOT NULL,
  `membre_jury_id` int NOT NULL,
  `note_fond` decimal(5,2) DEFAULT NULL,
  `note_forme` decimal(5,2) DEFAULT NULL,
  `note_soutenance` decimal(5,2) DEFAULT NULL,
  `note_finale` decimal(5,2) DEFAULT NULL,
  `mention` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_note`),
  KEY `membre_jury_id` (`membre_jury_id`),
  KEY `idx_soutenance` (`soutenance_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notes_soutenance`
--

INSERT INTO `notes_soutenance` (`id_note`, `soutenance_id`, `membre_jury_id`, `note_fond`, `note_forme`, `note_soutenance`, `note_finale`, `mention`, `commentaire`) VALUES
(1, 1, 1, 17.00, 16.50, 17.00, 16.83, 'Très Bien', 'Excellent travail de recherche appliquée'),
(2, 1, 2, 16.50, 16.00, 17.50, 16.67, 'Très Bien', 'Bonne maîtrise du sujet'),
(3, 1, 3, 17.50, 17.00, 16.50, 17.00, 'Très Bien', 'Analyse pertinente'),
(4, 1, 4, 16.00, 15.50, 16.00, 15.83, 'Bien', 'Présentation claire'),
(5, 1, 5, 17.00, 16.50, 17.00, 16.83, 'Très Bien', 'Bon lien théorie-pratique'),
(6, 2, 6, 15.50, 15.00, 15.50, 15.33, 'Bien', 'Travail solide'),
(7, 2, 7, 14.50, 15.00, 15.00, 14.83, 'Bien', 'Méthodologie à améliorer'),
(8, 2, 8, 15.00, 14.50, 16.00, 15.17, 'Bien', 'Bonne présentation orale'),
(9, 2, 9, 14.00, 14.50, 14.50, 14.33, 'Bien', 'Contenu pertinent'),
(10, 2, 10, 15.50, 15.50, 15.50, 15.50, 'Bien', 'Perspective professionnelle claire');

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id_notification` int NOT NULL AUTO_INCREMENT,
  `destinataire_id` int NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'info',
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contenu` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `lue` tinyint(1) DEFAULT '0',
  `lue_le` datetime DEFAULT NULL,
  `lien` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `donnees_json` json DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_notification`),
  KEY `idx_destinataire` (`destinataire_id`),
  KEY `idx_lue` (`lue`),
  KEY `idx_composite_destinataire_lue` (`destinataire_id`,`lue`),
  KEY `idx_composite_date_type` (`created_at`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notifications_historique`
--

DROP TABLE IF EXISTS `notifications_historique`;
CREATE TABLE IF NOT EXISTS `notifications_historique` (
  `id_historique` int NOT NULL AUTO_INCREMENT,
  `template_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destinataire_id` int DEFAULT NULL,
  `canal` enum('Email','SMS','Messagerie') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sujet` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` enum('Envoye','Echec','Bounce') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `erreur_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_historique`),
  KEY `idx_destinataire` (`destinataire_id`),
  KEY `idx_statut` (`statut`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notifications_historique`
--

INSERT INTO `notifications_historique` (`id_historique`, `template_code`, `destinataire_id`, `canal`, `sujet`, `statut`, `erreur_message`, `created_at`) VALUES
(1, 'AUTH_BIENVENUE', 100, 'Email', 'Bienvenue sur CheckMaster', 'Envoye', NULL, '2026-01-16 10:27:05'),
(2, 'AUTH_BIENVENUE', 101, 'Email', 'Bienvenue sur CheckMaster', 'Envoye', NULL, '2026-01-16 10:27:05'),
(3, 'AUTH_BIENVENUE', 102, 'Email', 'Bienvenue sur CheckMaster', 'Envoye', NULL, '2026-01-16 10:27:05'),
(4, 'CANDIDATURE_SOUMISE', 100, 'Email', 'Candidature soumise', 'Envoye', NULL, '2026-01-16 10:27:05'),
(5, 'CANDIDATURE_VALIDEE', 100, 'Email', 'Candidature validée', 'Envoye', NULL, '2026-01-16 10:27:05'),
(6, 'COMMISSION_RAPPORT_VALIDE', 100, 'Email', 'Rapport validé par la commission', 'Envoye', NULL, '2026-01-16 10:27:05'),
(7, 'SOUTENANCE_PLANIFIEE', 100, 'Email', 'Soutenance planifiée', 'Envoye', NULL, '2026-01-16 10:27:05'),
(8, 'RESULTAT_SOUTENANCE', 100, 'Email', 'Résultat de votre soutenance', 'Envoye', NULL, '2026-01-16 10:27:05'),
(9, 'CANDIDATURE_SOUMISE', 112, 'Email', 'Candidature soumise', 'Echec', 'Connection refused', '2026-01-16 10:27:05'),
(10, 'CANDIDATURE_SOUMISE', 112, 'Email', 'Candidature soumise (retry)', 'Echec', 'Timeout', '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `notifications_queue`
--

DROP TABLE IF EXISTS `notifications_queue`;
CREATE TABLE IF NOT EXISTS `notifications_queue` (
  `id_queue` int NOT NULL AUTO_INCREMENT,
  `template_id` int NOT NULL,
  `destinataire_id` int NOT NULL,
  `canal` enum('Email','SMS','Messagerie') COLLATE utf8mb4_unicode_ci NOT NULL,
  `variables_json` json DEFAULT NULL,
  `priorite` int DEFAULT '5',
  `statut` enum('En_attente','En_cours','Envoye','Echec') COLLATE utf8mb4_unicode_ci DEFAULT 'En_attente',
  `tentatives` int DEFAULT '0',
  `erreur_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `envoye_le` datetime DEFAULT NULL,
  PRIMARY KEY (`id_queue`),
  KEY `template_id` (`template_id`),
  KEY `destinataire_id` (`destinataire_id`),
  KEY `idx_statut` (`statut`),
  KEY `idx_priorite` (`priorite`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notifications_queue`
--

INSERT INTO `notifications_queue` (`id_queue`, `template_id`, `destinataire_id`, `canal`, `variables_json`, `priorite`, `statut`, `tentatives`, `erreur_message`, `created_at`, `envoye_le`) VALUES
(1, 1, 100, 'Email', '{\"nom\": \"KONE Adama\", \"email\": \"kone.adama@etudiant.ufhb.ci\", \"lien_activation\": \"https://checkmaster.ufhb.ci/activer/abc123\"}', 5, 'Envoye', 1, NULL, '2026-01-16 10:27:05', '2024-09-15 10:30:00'),
(2, 1, 101, 'Email', '{\"nom\": \"SANGARE Fatou\", \"email\": \"sangare.fatou@etudiant.ufhb.ci\", \"lien_activation\": \"https://checkmaster.ufhb.ci/activer/def456\"}', 5, 'Envoye', 1, NULL, '2026-01-16 10:27:05', '2024-09-15 10:31:00'),
(3, 18, 100, 'Email', '{\"nom\": \"KONE Adama\", \"theme\": \"Système de gestion de stock avec ML\"}', 5, 'Envoye', 1, NULL, '2026-01-16 10:27:05', '2024-10-01 15:00:00'),
(4, 65, 102, 'Email', '{\"nom\": \"BROU Jean-Pierre\", \"date\": \"2024-12-20\", \"heure\": \"10:00\", \"salle\": \"Amphithéâtre 1\"}', 3, 'En_attente', 0, NULL, '2026-01-16 10:27:05', NULL),
(5, 66, 103, 'Email', '{\"nom\": \"ASSI Marie-Claire\", \"date\": \"2024-12-22\", \"heure\": \"09:00\", \"salle\": \"A102\"}', 3, 'En_attente', 0, NULL, '2026-01-16 10:27:05', NULL),
(6, 19, 112, 'Email', '{\"nom\": \"TAPE Didier\"}', 5, 'Echec', 3, 'Connection timeout after 30s', '2026-01-16 10:27:05', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `notification_templates`
--

DROP TABLE IF EXISTS `notification_templates`;
CREATE TABLE IF NOT EXISTS `notification_templates` (
  `id_template` int NOT NULL AUTO_INCREMENT,
  `code_template` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `canal` enum('Email','SMS','Messagerie') COLLATE utf8mb4_unicode_ci NOT NULL,
  `sujet` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `corps` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `variables_json` json DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_template`),
  UNIQUE KEY `code_template` (`code_template`),
  KEY `idx_code` (`code_template`),
  KEY `idx_canal` (`canal`),
  KEY `idx_actif` (`actif`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notification_templates`
--

INSERT INTO `notification_templates` (`id_template`, `code_template`, `canal`, `sujet`, `corps`, `variables_json`, `actif`, `created_at`, `updated_at`) VALUES
(1, 'AUTH_BIENVENUE', 'Email', 'Bienvenue sur CheckMaster', 'Bonjour {{nom}},\n\nVotre compte CheckMaster a été créé.\n\nLogin: {{email}}\n\nCliquez sur le lien suivant pour définir votre mot de passe: {{lien_activation}}\n\nCe lien expire dans 48 heures.\n\nCordialement,\nL\'équipe CheckMaster', '{\"nom\": \"string\", \"email\": \"string\", \"lien_activation\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(2, 'AUTH_RESET_PASSWORD', 'Email', 'Réinitialisation de mot de passe', 'Bonjour {{nom}},\n\nUne demande de réinitialisation de mot de passe a été effectuée.\n\nCliquez sur ce lien pour réinitialiser: {{lien}}\n\nCe lien expire dans 24 heures.\n\nSi vous n\'êtes pas à l\'origine de cette demande, ignorez cet email.', '{\"nom\": \"string\", \"lien\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(3, 'AUTH_PASSWORD_CHANGED', 'Email', 'Mot de passe modifié', 'Bonjour {{nom}},\n\nVotre mot de passe a été modifié avec succès.\n\nSi vous n\'êtes pas à l\'origine de cette modification, contactez immédiatement l\'administrateur.', '{\"nom\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(4, 'AUTH_SESSION_FORCEE', 'Email', 'Session terminée', 'Bonjour {{nom}},\n\nVotre session a été fermée par un administrateur.\n\nRaison: {{raison}}\n\nSi vous avez des questions, contactez le support.', '{\"nom\": \"string\", \"raison\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(5, 'AUTH_TENTATIVES_ECHEC', 'Email', 'Alertes de sécurité - Tentatives échouées', 'Bonjour {{nom}},\n\nNous avons détecté {{nombre}} tentatives de connexion échouées sur votre compte.\n\nDernière tentative: {{date}} depuis {{ip}}\n\nSi ce n\'était pas vous, changez votre mot de passe immédiatement.', '{\"ip\": \"string\", \"nom\": \"string\", \"date\": \"string\", \"nombre\": \"integer\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(6, 'CANDIDATURE_SOUMISE', 'Email', 'Candidature soumise', 'Bonjour {{nom}},\n\nVotre candidature a été soumise avec succès.\n\nThème: {{theme}}\nEntreprise: {{entreprise}}\nDate de soumission: {{date}}\n\nVotre candidature sera examinée par le service scolarité.', '{\"nom\": \"string\", \"date\": \"string\", \"theme\": \"string\", \"entreprise\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(7, 'CANDIDATURE_VALIDEE', 'Email', 'Candidature validée', 'Bonjour {{nom}},\n\nVotre candidature a été validée par le service scolarité.\n\nThème: {{theme}}\n\nVous pouvez maintenant procéder au paiement des frais de scolarité.', '{\"nom\": \"string\", \"theme\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(8, 'CANDIDATURE_REJETEE', 'Email', 'Candidature rejetée', 'Bonjour {{nom}},\n\nVotre candidature a été rejetée.\n\nMotif: {{motif}}\n\nVous pouvez soumettre une nouvelle candidature après correction.', '{\"nom\": \"string\", \"motif\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(9, 'CANDIDATURE_DEMANDE_INFO', 'Email', 'Informations complémentaires requises', 'Bonjour {{nom}},\n\nDes informations complémentaires sont requises pour votre candidature.\n\n{{message}}\n\nMerci de compléter votre dossier dans les plus brefs délais.', '{\"nom\": \"string\", \"message\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(10, 'CANDIDATURE_RAPPEL', 'Email', 'Rappel - Candidature en attente', 'Bonjour {{nom}},\n\nVotre candidature est en attente depuis {{jours}} jours.\n\nÉtat actuel: {{etat}}\n\nAction requise: {{action}}', '{\"nom\": \"string\", \"etat\": \"string\", \"jours\": \"integer\", \"action\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(11, 'CANDIDATURE_VALIDATION_SCOLARITE', 'Email', 'Nouvelle candidature à valider', 'Bonjour,\n\nUne nouvelle candidature nécessite votre validation.\n\nÉtudiant: {{etudiant}}\nThème: {{theme}}\n\nConnectez-vous pour traiter cette demande.', '{\"theme\": \"string\", \"etudiant\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(12, 'CANDIDATURE_FORMAT_VALIDE', 'Email', 'Format de rapport validé', 'Bonjour {{nom}},\n\nLe format de votre rapport a été validé par le service communication.\n\nVotre dossier est maintenant en attente d\'évaluation par la commission.', '{\"nom\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(13, 'CANDIDATURE_FORMAT_REJETE', 'Email', 'Format de rapport à corriger', 'Bonjour {{nom}},\n\nLe format de votre rapport nécessite des corrections.\n\nCommentaires: {{commentaires}}\n\nMerci de corriger et resoumettre.', '{\"nom\": \"string\", \"commentaires\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(14, 'PAIEMENT_RECU', 'Email', 'Paiement reçu', 'Bonjour {{nom}},\n\nNous avons bien reçu votre paiement.\n\nMontant: {{montant}} FCFA\nRéférence: {{reference}}\nDate: {{date}}\n\nVotre reçu est disponible dans votre espace étudiant.', '{\"nom\": \"string\", \"date\": \"string\", \"montant\": \"decimal\", \"reference\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(15, 'PAIEMENT_COMPLET', 'Email', 'Paiement complet - Dossier débloqué', 'Bonjour {{nom}},\n\nVotre paiement est maintenant complet.\n\nTotal payé: {{total}} FCFA\n\nVotre dossier a été débloqué pour la suite du processus.', '{\"nom\": \"string\", \"total\": \"decimal\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(16, 'PAIEMENT_RAPPEL', 'Email', 'Rappel de paiement', 'Bonjour {{nom}},\n\nVous avez un solde impayé de {{solde}} FCFA.\n\nDate limite: {{date_limite}}\n\nMerci de régulariser votre situation.', '{\"nom\": \"string\", \"solde\": \"decimal\", \"date_limite\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(17, 'PAIEMENT_RETARD', 'Email', 'Paiement en retard - Pénalité appliquée', 'Bonjour {{nom}},\n\nVotre paiement est en retard de {{jours}} jours.\n\nUne pénalité de {{penalite}} FCFA a été appliquée.\n\nNouveau total: {{total}} FCFA', '{\"nom\": \"string\", \"jours\": \"integer\", \"total\": \"decimal\", \"penalite\": \"decimal\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(18, 'PAIEMENT_EXONERATION', 'Email', 'Exonération accordée', 'Bonjour {{nom}},\n\nUne exonération de {{montant}} FCFA ({{pourcentage}}%) vous a été accordée.\n\nMotif: {{motif}}\n\nNouveau montant dû: {{nouveau_total}} FCFA', '{\"nom\": \"string\", \"motif\": \"string\", \"montant\": \"decimal\", \"pourcentage\": \"decimal\", \"nouveau_total\": \"decimal\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(19, 'RECU_DISPONIBLE', 'Email', 'Reçu de paiement disponible', 'Bonjour {{nom}},\n\nVotre reçu de paiement est disponible.\n\nRéférence: {{reference}}\nMontant: {{montant}} FCFA\n\nTéléchargez-le depuis votre espace étudiant.', '{\"nom\": \"string\", \"montant\": \"decimal\", \"reference\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(20, 'PAIEMENT_NOTIFICATION_SCOLARITE', 'Email', 'Nouveau paiement enregistré', 'Bonjour,\n\nUn nouveau paiement a été enregistré.\n\nÉtudiant: {{etudiant}}\nMontant: {{montant}} FCFA\nMode: {{mode}}', '{\"mode\": \"string\", \"montant\": \"decimal\", \"etudiant\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(21, 'COMMISSION_SESSION_PLANIFIEE', 'Email', 'Session de commission planifiée', 'Bonjour {{nom}},\n\nUne session de commission a été planifiée.\n\nDate: {{date}}\nLieu: {{lieu}}\nNombre de rapports: {{nombre_rapports}}\n\nMerci de confirmer votre présence.', '{\"nom\": \"string\", \"date\": \"string\", \"lieu\": \"string\", \"nombre_rapports\": \"integer\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(22, 'COMMISSION_RAPPEL_SESSION', 'Email', 'Rappel - Session de commission demain', 'Bonjour {{nom}},\n\nRappel: Session de commission demain.\n\nDate: {{date}}\nLieu: {{lieu}}\n\nN\'oubliez pas de consulter les rapports à évaluer.', '{\"nom\": \"string\", \"date\": \"string\", \"lieu\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(23, 'COMMISSION_RAPPORT_ATTRIBUE', 'Email', 'Rapport attribué pour évaluation', 'Bonjour {{nom}},\n\nUn rapport vous a été attribué pour évaluation.\n\nÉtudiant: {{etudiant}}\nThème: {{theme}}\nSession: {{date_session}}\n\nConsultez le rapport dans votre espace.', '{\"nom\": \"string\", \"theme\": \"string\", \"etudiant\": \"string\", \"date_session\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(24, 'COMMISSION_VOTE_ENREGISTRE', 'Email', 'Vote enregistré', 'Bonjour {{nom}},\n\nVotre vote a été enregistré avec succès.\n\nRapport: {{rapport}}\nDécision: {{decision}}\nTour: {{tour}}', '{\"nom\": \"string\", \"tour\": \"integer\", \"rapport\": \"string\", \"decision\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(25, 'COMMISSION_RAPPORT_VALIDE', 'Email', 'Rapport validé par la commission', 'Bonjour {{nom}},\n\nVotre rapport a été validé par la commission.\n\nRésultat: Approuvé à l\'unanimité\nTour de vote: {{tour}}\n\nVotre dossier passe maintenant à l\'étape suivante.', '{\"nom\": \"string\", \"tour\": \"integer\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(26, 'COMMISSION_RAPPORT_A_REVOIR', 'Email', 'Rapport à réviser', 'Bonjour {{nom}},\n\nLa commission demande des modifications sur votre rapport.\n\nCommentaires:\n{{commentaires}}\n\nMerci de procéder aux corrections et resoumettre.', '{\"nom\": \"string\", \"commentaires\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(27, 'COMMISSION_RAPPORT_REJETE', 'Email', 'Rapport rejeté par la commission', 'Bonjour {{nom}},\n\nVotre rapport a été rejeté par la commission.\n\nMotifs:\n{{motifs}}\n\nVous pouvez soumettre un nouveau rapport après corrections majeures.', '{\"nom\": \"string\", \"motifs\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(28, 'COMMISSION_PV_DISPONIBLE', 'Email', 'PV de commission disponible', 'Bonjour,\n\nLe PV de la session de commission est disponible.\n\nDate session: {{date}}\nRapports traités: {{nombre}}\n\nConsultez-le dans la section archives.', '{\"date\": \"string\", \"nombre\": \"integer\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(29, 'COMMISSION_MEDIATION_REQUISE', 'Email', 'Médiation requise - Vote non unanime', 'Bonjour,\n\nUne médiation du Doyen est requise.\n\nRapport: {{rapport}}\nÉtudiant: {{etudiant}}\nTours effectués: {{tours}}\n\nMerci de traiter cette escalade.', '{\"tours\": \"integer\", \"rapport\": \"string\", \"etudiant\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(30, 'COMMISSION_DECISION_FINALE', 'Email', 'Décision finale de la commission', 'Bonjour {{nom}},\n\nLa décision finale concernant votre rapport:\n\nDécision: {{decision}}\nCommentaires: {{commentaires}}\n\n{{prochaines_etapes}}', '{\"nom\": \"string\", \"decision\": \"string\", \"commentaires\": \"string\", \"prochaines_etapes\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(31, 'ENCADREUR_ASSIGNATION', 'Email', 'Nouvelle assignation d\'étudiant', 'Bonjour {{nom}},\n\nVous avez été assigné comme encadreur pédagogique.\n\nÉtudiant: {{etudiant}}\nThème: {{theme}}\n\nConsultez le dossier dans votre espace.', '{\"nom\": \"string\", \"theme\": \"string\", \"etudiant\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(32, 'ENCADREUR_DEMANDE_AVIS', 'Email', 'Avis requis sur dossier étudiant', 'Bonjour {{nom}},\n\nVotre avis est requis sur le dossier suivant:\n\nÉtudiant: {{etudiant}}\nThème: {{theme}}\n\nMerci de donner votre avis dans les 7 jours.', '{\"nom\": \"string\", \"theme\": \"string\", \"etudiant\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(33, 'ENCADREUR_RAPPEL_AVIS', 'Email', 'Rappel - Avis en attente', 'Bonjour {{nom}},\n\nRappel: Un avis est en attente depuis {{jours}} jours.\n\nÉtudiant: {{etudiant}}\n\nMerci de traiter cette demande.', '{\"nom\": \"string\", \"jours\": \"integer\", \"etudiant\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(34, 'ETUDIANT_AVIS_FAVORABLE', 'Email', 'Avis favorable de l\'encadreur', 'Bonjour {{nom}},\n\nVotre encadreur pédagogique a donné un avis favorable.\n\nCommentaires: {{commentaires}}\n\nVotre dossier passe à l\'étape de constitution du jury.', '{\"nom\": \"string\", \"commentaires\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(35, 'ETUDIANT_AVIS_DEFAVORABLE', 'Email', 'Avis défavorable de l\'encadreur', 'Bonjour {{nom}},\n\nVotre encadreur pédagogique a émis des réserves.\n\nCommentaires: {{commentaires}}\n\nMerci de prendre contact avec votre encadreur.', '{\"nom\": \"string\", \"commentaires\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(36, 'DIRECTEUR_MEMOIRE_NOTIFICATION', 'Email', 'Notification de direction de mémoire', 'Bonjour {{nom}},\n\nVous êtes enregistré comme directeur de mémoire.\n\nÉtudiant: {{etudiant}}\nThème: {{theme}}\n\nConnectez-vous pour suivre l\'avancement.', '{\"nom\": \"string\", \"theme\": \"string\", \"etudiant\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(37, 'JURY_INVITATION', 'Email', 'Invitation à participer à un jury', 'Bonjour {{nom}},\n\nVous êtes invité à participer à un jury de soutenance.\n\nÉtudiant: {{etudiant}}\nThème: {{theme}}\nRôle proposé: {{role}}\n\nMerci de confirmer votre disponibilité.', '{\"nom\": \"string\", \"role\": \"string\", \"theme\": \"string\", \"etudiant\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(38, 'JURY_ACCEPTATION', 'Email', 'Participation au jury confirmée', 'Bonjour {{nom}},\n\nVotre participation au jury a été confirmée.\n\nÉtudiant: {{etudiant}}\nRôle: {{role}}\n\nVous serez notifié de la date de soutenance.', '{\"nom\": \"string\", \"role\": \"string\", \"etudiant\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(39, 'JURY_REFUS', 'Email', 'Participation au jury déclinée', 'Bonjour,\n\n{{membre}} a décliné l\'invitation au jury.\n\nÉtudiant: {{etudiant}}\nRaison: {{raison}}\n\nVeuillez trouver un remplaçant.', '{\"membre\": \"string\", \"raison\": \"string\", \"etudiant\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(40, 'JURY_COMPLET', 'Email', 'Jury constitué', 'Bonjour {{nom}},\n\nLe jury pour votre soutenance est maintenant complet.\n\nPrésident: {{president}}\nMembres: {{membres}}\n\nLa date de soutenance vous sera communiquée prochainement.', '{\"nom\": \"string\", \"membres\": \"string\", \"president\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(41, 'SOUTENANCE_PLANIFIEE', 'Email', 'Soutenance planifiée', 'Bonjour {{nom}},\n\nVotre soutenance a été planifiée.\n\nDate: {{date}}\nHeure: {{heure}}\nSalle: {{salle}}\n\nMerci de vous présenter 15 minutes avant.', '{\"nom\": \"string\", \"date\": \"string\", \"heure\": \"string\", \"salle\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(42, 'SOUTENANCE_RAPPEL_J7', 'Email', 'Rappel soutenance dans 7 jours', 'Bonjour {{nom}},\n\nRappel: Votre soutenance est dans 7 jours.\n\nDate: {{date}}\nSalle: {{salle}}\n\nPréparez votre présentation.', '{\"nom\": \"string\", \"date\": \"string\", \"salle\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(43, 'SOUTENANCE_RAPPEL_J1', 'Email', 'Rappel soutenance demain', 'Bonjour {{nom}},\n\nRappel: Votre soutenance est demain.\n\nDate: {{date}}\nHeure: {{heure}}\nSalle: {{salle}}\n\nBonne chance!', '{\"nom\": \"string\", \"date\": \"string\", \"heure\": \"string\", \"salle\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(44, 'SOUTENANCE_CONVOCATION', 'Email', 'Convocation officielle à la soutenance', 'Bonjour {{nom}},\n\nVeuillez trouver ci-joint votre convocation officielle.\n\nDate: {{date}}\nHeure: {{heure}}\nSalle: {{salle}}\n\nDocument à présenter le jour de la soutenance.', '{\"nom\": \"string\", \"date\": \"string\", \"heure\": \"string\", \"salle\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(45, 'SOUTENANCE_CODE_PRESIDENT', 'Email', 'Code de démarrage soutenance', 'Bonjour {{nom}},\n\nVoici le code de démarrage pour la soutenance:\n\nCode: {{code}}\nValide de: {{heure_debut}} à {{heure_fin}}\n\nÉtudiant: {{etudiant}}\n\nCe code est strictement personnel.', '{\"nom\": \"string\", \"code\": \"string\", \"etudiant\": \"string\", \"heure_fin\": \"string\", \"heure_debut\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(46, 'SOUTENANCE_DEMARREE', 'Email', 'Soutenance démarrée', 'Information: La soutenance de {{etudiant}} a démarré.\n\nPrésident: {{president}}\nHeure de début: {{heure}}', '{\"heure\": \"string\", \"etudiant\": \"string\", \"president\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(47, 'SOUTENANCE_TERMINEE', 'Email', 'Soutenance terminée', 'Bonjour {{nom}},\n\nVotre soutenance est terminée.\n\nLes résultats vous seront communiqués après délibération du jury.', '{\"nom\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(48, 'SOUTENANCE_REPORTEE', 'Email', 'Soutenance reportée', 'Bonjour {{nom}},\n\nVotre soutenance a été reportée.\n\nAncienne date: {{ancienne_date}}\nNouvelle date: {{nouvelle_date}}\nRaison: {{raison}}', '{\"nom\": \"string\", \"raison\": \"string\", \"ancienne_date\": \"string\", \"nouvelle_date\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(49, 'SOUTENANCE_ANNULEE', 'Email', 'Soutenance annulée', 'Bonjour {{nom}},\n\nVotre soutenance a été annulée.\n\nRaison: {{raison}}\n\nVous serez contacté pour reprogrammer.', '{\"nom\": \"string\", \"raison\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(50, 'JURY_NOTES_SAISIES', 'Email', 'Notes de soutenance enregistrées', 'Bonjour,\n\nLes notes ont été enregistrées pour la soutenance.\n\nÉtudiant: {{etudiant}}\nMention: {{mention}}\nPV généré: {{pv_genere}}', '{\"mention\": \"string\", \"etudiant\": \"string\", \"pv_genere\": \"boolean\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(51, 'RESULTAT_SOUTENANCE', 'Email', 'Résultat de votre soutenance', 'Bonjour {{nom}},\n\nVoici le résultat de votre soutenance:\n\nNote finale: {{note}}/20\nMention: {{mention}}\nDécision: {{decision}}\n\n{{commentaires}}\n\nFélicitations!', '{\"nom\": \"string\", \"note\": \"decimal\", \"mention\": \"string\", \"decision\": \"string\", \"commentaires\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(52, 'WORKFLOW_TRANSITION', 'Email', 'Changement d\'état de votre dossier', 'Bonjour {{nom}},\n\nVotre dossier a changé d\'état.\n\nNouvel état: {{etat}}\nDate: {{date}}\n\nProchaine étape: {{prochaine_etape}}', '{\"nom\": \"string\", \"date\": \"string\", \"etat\": \"string\", \"prochaine_etape\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(53, 'WORKFLOW_SLA_50', 'Email', 'Alerte SLA - 50% du délai écoulé', 'Attention,\n\nUn dossier atteint 50% de son délai.\n\nÉtudiant: {{etudiant}}\nÉtat: {{etat}}\nDélai restant: {{jours_restants}} jours', '{\"etat\": \"string\", \"etudiant\": \"string\", \"jours_restants\": \"integer\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(54, 'WORKFLOW_SLA_80', 'Email', 'Alerte SLA urgente - 80% du délai', 'URGENT,\n\nUn dossier atteint 80% de son délai.\n\nÉtudiant: {{etudiant}}\nÉtat: {{etat}}\nDélai restant: {{jours_restants}} jours\n\nAction immédiate requise.', '{\"etat\": \"string\", \"etudiant\": \"string\", \"jours_restants\": \"integer\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(55, 'WORKFLOW_SLA_100', 'Email', 'CRITIQUE - Délai SLA dépassé', 'CRITIQUE,\n\nUn dossier a dépassé son délai SLA.\n\nÉtudiant: {{etudiant}}\nÉtat: {{etat}}\nDépassement: {{jours_depassement}} jours\n\nEscalade automatique effectuée.', '{\"etat\": \"string\", \"etudiant\": \"string\", \"jours_depassement\": \"integer\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(56, 'ESCALADE_NIVEAU_1', 'Email', 'Escalade niveau 1 - Responsable niveau', 'Bonjour,\n\nUn dossier vous a été escaladé.\n\nÉtudiant: {{etudiant}}\nProblème: {{probleme}}\nDélai de réponse: 3 jours', '{\"etudiant\": \"string\", \"probleme\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(57, 'ESCALADE_NIVEAU_2', 'Email', 'Escalade niveau 2 - Responsable filière', 'Bonjour,\n\nUn dossier vous a été escaladé (niveau 2).\n\nÉtudiant: {{etudiant}}\nProblème: {{probleme}}\nHistorique: {{historique}}', '{\"etudiant\": \"string\", \"probleme\": \"string\", \"historique\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(58, 'ESCALADE_NIVEAU_3', 'Email', 'Escalade niveau 3 - Directeur adjoint', 'Bonjour,\n\nUn dossier critique vous a été escaladé.\n\nÉtudiant: {{etudiant}}\nProblème: {{probleme}}\nNiveaux franchis: 2\nUrgence: HAUTE', '{\"etudiant\": \"string\", \"probleme\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(59, 'ESCALADE_DOYEN', 'Email', 'Escalade Doyen - Médiation requise', 'Monsieur le Doyen,\n\nVotre médiation est requise.\n\nÉtudiant: {{etudiant}}\nProblème: {{probleme}}\nNiveaux franchis: 3\nHistorique complet: {{historique}}', '{\"etudiant\": \"string\", \"probleme\": \"string\", \"historique\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(60, 'RECLAMATION_DEPOSEE', 'Email', 'Réclamation déposée', 'Bonjour {{nom}},\n\nVotre réclamation a été enregistrée.\n\nNuméro: {{numero}}\nObjet: {{objet}}\n\nVous recevrez une réponse sous 5 jours ouvrés.', '{\"nom\": \"string\", \"objet\": \"string\", \"numero\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(61, 'RECLAMATION_EN_COURS', 'Email', 'Réclamation en cours de traitement', 'Bonjour {{nom}},\n\nVotre réclamation est en cours de traitement.\n\nNuméro: {{numero}}\nAssignée à: {{responsable}}', '{\"nom\": \"string\", \"numero\": \"string\", \"responsable\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(62, 'RECLAMATION_REPONSE', 'Email', 'Réponse à votre réclamation', 'Bonjour {{nom}},\n\nVoici la réponse à votre réclamation:\n\nNuméro: {{numero}}\nRéponse:\n{{reponse}}\n\nSi vous n\'êtes pas satisfait, vous pouvez faire appel.', '{\"nom\": \"string\", \"numero\": \"string\", \"reponse\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(63, 'RECLAMATION_NOUVELLE', 'Email', 'Nouvelle réclamation à traiter', 'Bonjour,\n\nUne nouvelle réclamation nécessite votre attention.\n\nNuméro: {{numero}}\nÉtudiant: {{etudiant}}\nObjet: {{objet}}\nPriorité: {{priorite}}', '{\"objet\": \"string\", \"numero\": \"string\", \"etudiant\": \"string\", \"priorite\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(64, 'RECLAMATION_ESCALADEE', 'Email', 'Réclamation escaladée', 'Bonjour,\n\nUne réclamation vous a été escaladée.\n\nNuméro: {{numero}}\nÉtudiant: {{etudiant}}\nMotif escalade: {{motif}}', '{\"motif\": \"string\", \"numero\": \"string\", \"etudiant\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(65, 'ADMIN_MAINTENANCE', 'Email', 'Maintenance planifiée', 'Bonjour,\n\nUne maintenance est planifiée.\n\nDate: {{date}}\nDurée estimée: {{duree}}\nImpact: {{impact}}\n\nMerci de sauvegarder vos travaux en cours.', '{\"date\": \"string\", \"duree\": \"string\", \"impact\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(66, 'ADMIN_BACKUP_COMPLETE', 'Email', 'Sauvegarde complétée', 'Rapport de sauvegarde:\n\nDate: {{date}}\nTaille: {{taille}}\nStatut: {{statut}}\nEmplacement: {{emplacement}}', '{\"date\": \"string\", \"statut\": \"string\", \"taille\": \"string\", \"emplacement\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(67, 'ADMIN_BACKUP_ECHEC', 'Email', 'ALERTE - Échec de sauvegarde', 'ALERTE CRITIQUE\n\nLa sauvegarde a échoué.\n\nDate: {{date}}\nErreur: {{erreur}}\n\nAction immédiate requise.', '{\"date\": \"string\", \"erreur\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(68, 'ADMIN_INTEGRITY_ALERT', 'Email', 'Alerte intégrité des archives', 'ALERTE\n\nUne vérification d\'intégrité a échoué.\n\nDocument: {{document}}\nHash attendu: {{hash_attendu}}\nHash trouvé: {{hash_trouve}}', '{\"document\": \"string\", \"hash_trouve\": \"string\", \"hash_attendu\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(69, 'ADMIN_RAPPORT_QUOTIDIEN', 'Email', 'Rapport quotidien CheckMaster', 'Rapport du {{date}}:\n\nNouveaux dossiers: {{nouveaux}}\nSoutenances: {{soutenances}}\nPaiements: {{paiements}} FCFA\nAlertes: {{alertes}}', '{\"date\": \"string\", \"alertes\": \"integer\", \"nouveaux\": \"integer\", \"paiements\": \"decimal\", \"soutenances\": \"integer\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(70, 'ADMIN_NOUVELLE_ANNEE', 'Email', 'Nouvelle année académique', 'Information,\n\nL\'année académique {{annee}} a été activée.\n\nTous les paramètres ont été mis à jour.\n\nBonne rentrée!', '{\"annee\": \"string\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(71, 'ADMIN_IMPORT_TERMINE', 'Email', 'Import de données terminé', 'Import terminé:\n\nType: {{type}}\nLignes traitées: {{total}}\nRéussies: {{reussies}}\nErreurs: {{erreurs}}\n\nVoir le rapport détaillé.', '{\"type\": \"string\", \"total\": \"integer\", \"erreurs\": \"integer\", \"reussies\": \"integer\"}', 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

DROP TABLE IF EXISTS `paiements`;
CREATE TABLE IF NOT EXISTS `paiements` (
  `id_paiement` int NOT NULL AUTO_INCREMENT,
  `etudiant_id` int NOT NULL,
  `annee_acad_id` int NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `mode_paiement` enum('Especes','Carte','Virement','Cheque') COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_paiement` date NOT NULL,
  `recu_genere` tinyint(1) DEFAULT '0',
  `recu_chemin` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enregistre_par` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_paiement`),
  UNIQUE KEY `reference` (`reference`),
  KEY `enregistre_par` (`enregistre_par`),
  KEY `idx_etudiant` (`etudiant_id`),
  KEY `idx_annee` (`annee_acad_id`),
  KEY `idx_date` (`date_paiement`),
  KEY `idx_reference` (`reference`),
  KEY `idx_composite_etudiant_date` (`etudiant_id`,`date_paiement`),
  KEY `idx_composite_date_montant` (`date_paiement`,`montant`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `paiements`
--

INSERT INTO `paiements` (`id_paiement`, `etudiant_id`, `annee_acad_id`, `montant`, `mode_paiement`, `reference`, `date_paiement`, `recu_genere`, `recu_chemin`, `enregistre_par`, `created_at`) VALUES
(1, 1, 1, 550000.00, 'Virement', 'PAY-2024-001', '2024-09-15', 1, 'storage/recus/2024/recu_001.pdf', 30, '2026-01-16 10:27:05'),
(2, 2, 1, 550000.00, 'Carte', 'PAY-2024-002', '2024-09-16', 1, 'storage/recus/2024/recu_002.pdf', 30, '2026-01-16 10:27:05'),
(3, 3, 1, 275000.00, 'Especes', 'PAY-2024-003', '2024-09-17', 1, 'storage/recus/2024/recu_003.pdf', 31, '2026-01-16 10:27:05'),
(4, 3, 1, 275000.00, 'Especes', 'PAY-2024-004', '2024-10-20', 1, 'storage/recus/2024/recu_004.pdf', 31, '2026-01-16 10:27:05'),
(5, 4, 1, 550000.00, 'Cheque', 'PAY-2024-005', '2024-09-18', 1, 'storage/recus/2024/recu_005.pdf', 30, '2026-01-16 10:27:05'),
(6, 5, 1, 550000.00, 'Virement', 'PAY-2024-006', '2024-09-19', 1, 'storage/recus/2024/recu_006.pdf', 32, '2026-01-16 10:27:05'),
(7, 6, 1, 550000.00, 'Carte', 'PAY-2024-007', '2024-09-20', 1, 'storage/recus/2024/recu_007.pdf', 30, '2026-01-16 10:27:05'),
(8, 7, 1, 550000.00, 'Virement', 'PAY-2024-008', '2024-09-21', 1, 'storage/recus/2024/recu_008.pdf', 31, '2026-01-16 10:27:05'),
(9, 8, 1, 550000.00, 'Especes', 'PAY-2024-009', '2024-09-22', 1, 'storage/recus/2024/recu_009.pdf', 30, '2026-01-16 10:27:05'),
(10, 9, 1, 550000.00, 'Carte', 'PAY-2024-010', '2024-09-23', 1, 'storage/recus/2024/recu_010.pdf', 32, '2026-01-16 10:27:05'),
(11, 10, 1, 550000.00, 'Virement', 'PAY-2024-011', '2024-09-24', 1, 'storage/recus/2024/recu_011.pdf', 30, '2026-01-16 10:27:05'),
(12, 11, 1, 300000.00, 'Especes', 'PAY-2024-012', '2024-09-25', 1, 'storage/recus/2024/recu_012.pdf', 31, '2026-01-16 10:27:05'),
(13, 12, 1, 200000.00, 'Especes', 'PAY-2024-013', '2024-10-01', 1, 'storage/recus/2024/recu_013.pdf', 30, '2026-01-16 10:27:05'),
(14, 13, 1, 400000.00, 'Carte', 'PAY-2024-014', '2024-09-28', 1, 'storage/recus/2024/recu_014.pdf', 32, '2026-01-16 10:27:05'),
(15, 14, 1, 550000.00, 'Virement', 'PAY-2024-015', '2024-09-26', 1, 'storage/recus/2024/recu_015.pdf', 30, '2026-01-16 10:27:05'),
(16, 15, 1, 550000.00, 'Carte', 'PAY-2024-016', '2024-09-27', 1, 'storage/recus/2024/recu_016.pdf', 31, '2026-01-16 10:27:05'),
(17, 16, 1, 550000.00, 'Especes', 'PAY-2024-017', '2024-09-28', 1, 'storage/recus/2024/recu_017.pdf', 30, '2026-01-16 10:27:05'),
(18, 17, 1, 550000.00, 'Virement', 'PAY-2024-018', '2024-09-29', 1, 'storage/recus/2024/recu_018.pdf', 32, '2026-01-16 10:27:05'),
(19, 18, 1, 550000.00, 'Carte', 'PAY-2024-019', '2024-09-30', 1, 'storage/recus/2024/recu_019.pdf', 30, '2026-01-16 10:27:05'),
(20, 19, 1, 550000.00, 'Especes', 'PAY-2024-020', '2024-10-01', 1, 'storage/recus/2024/recu_020.pdf', 31, '2026-01-16 10:27:05'),
(21, 20, 1, 550000.00, 'Cheque', 'PAY-2024-021', '2024-10-02', 1, 'storage/recus/2024/recu_021.pdf', 30, '2026-01-16 10:27:05'),
(22, 21, 1, 550000.00, 'Virement', 'PAY-2024-022', '2024-10-03', 1, 'storage/recus/2024/recu_022.pdf', 32, '2026-01-16 10:27:05'),
(23, 22, 1, 550000.00, 'Carte', 'PAY-2024-023', '2024-10-04', 1, 'storage/recus/2024/recu_023.pdf', 30, '2026-01-16 10:27:05'),
(24, 23, 1, 275000.00, 'Especes', 'PAY-2024-024', '2024-10-05', 1, 'storage/recus/2024/recu_024.pdf', 31, '2026-01-16 10:27:05'),
(25, 24, 1, 550000.00, 'Virement', 'PAY-2024-025', '2024-10-06', 1, 'storage/recus/2024/recu_025.pdf', 30, '2026-01-16 10:27:05'),
(26, 25, 1, 550000.00, 'Carte', 'PAY-2024-026', '2024-10-07', 1, 'storage/recus/2024/recu_026.pdf', 32, '2026-01-16 10:27:05'),
(27, 26, 1, 550000.00, 'Especes', 'PAY-2024-027', '2024-10-08', 1, 'storage/recus/2024/recu_027.pdf', 30, '2026-01-16 10:27:05'),
(28, 27, 1, 550000.00, 'Virement', 'PAY-2024-028', '2024-10-09', 1, 'storage/recus/2024/recu_028.pdf', 31, '2026-01-16 10:27:05'),
(29, 28, 1, 400000.00, 'Carte', 'PAY-2024-029', '2024-10-10', 1, 'storage/recus/2024/recu_029.pdf', 30, '2026-01-16 10:27:05'),
(30, 29, 1, 550000.00, 'Cheque', 'PAY-2024-030', '2024-10-11', 1, 'storage/recus/2024/recu_030.pdf', 32, '2026-01-16 10:27:05'),
(31, 30, 1, 550000.00, 'Virement', 'PAY-2024-031', '2024-10-12', 1, 'storage/recus/2024/recu_031.pdf', 30, '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `participants_interventions`
--

DROP TABLE IF EXISTS `participants_interventions`;
CREATE TABLE IF NOT EXISTS `participants_interventions` (
  `id_intervention` int NOT NULL AUTO_INCREMENT,
  `session_id` int NOT NULL,
  `agenda_item_id` int DEFAULT NULL,
  `participant_id` int NOT NULL,
  `type_intervention` enum('presentation','question','reponse','commentaire','motion','point_ordre') COLLATE utf8mb4_unicode_ci NOT NULL,
  `contenu` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `duree_secondes` int DEFAULT NULL,
  `heure_intervention` time DEFAULT NULL,
  `enregistrement_audio` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transcription` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_intervention`),
  KEY `agenda_item_id` (`agenda_item_id`),
  KEY `idx_session` (`session_id`),
  KEY `idx_participant` (`participant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `participants_sessions_presences`
--

DROP TABLE IF EXISTS `participants_sessions_presences`;
CREATE TABLE IF NOT EXISTS `participants_sessions_presences` (
  `id_presence` int NOT NULL AUTO_INCREMENT,
  `session_id` int NOT NULL,
  `participant_id` int NOT NULL,
  `type_participant` enum('membre_commission','invite','observateur','rapporteur','secretaire') COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut_presence` enum('present','absent','retard','depart_anticipe') COLLATE utf8mb4_unicode_ci DEFAULT 'present',
  `heure_arrivee` time DEFAULT NULL,
  `heure_depart` time DEFAULT NULL,
  `duree_presence_minutes` int DEFAULT NULL,
  `justification_absence` text COLLATE utf8mb4_unicode_ci,
  `signature_presence` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verifie_par` int DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_presence`),
  UNIQUE KEY `uk_session_participant` (`session_id`,`participant_id`),
  KEY `verifie_par` (`verifie_par`),
  KEY `idx_session` (`session_id`),
  KEY `idx_participant` (`participant_id`),
  KEY `idx_statut` (`statut_presence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `penalites`
--

DROP TABLE IF EXISTS `penalites`;
CREATE TABLE IF NOT EXISTS `penalites` (
  `id_penalite` int NOT NULL AUTO_INCREMENT,
  `etudiant_id` int NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `motif` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_application` date NOT NULL,
  `payee` tinyint(1) DEFAULT '0',
  `date_paiement` date DEFAULT NULL,
  `recu_chemin` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_penalite`),
  KEY `idx_etudiant` (`etudiant_id`),
  KEY `idx_payee` (`payee`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `penalites`
--

INSERT INTO `penalites` (`id_penalite`, `etudiant_id`, `montant`, `motif`, `date_application`, `payee`, `date_paiement`, `recu_chemin`, `created_at`) VALUES
(1, 11, 25000.00, 'Retard de paiement - 10 jours', '2024-10-15', 1, '2024-10-20', 'storage/recus_penalites/2024/penalite_001.pdf', '2026-01-16 10:27:05'),
(2, 12, 50000.00, 'Retard de paiement - 20 jours', '2024-10-25', 0, NULL, NULL, '2026-01-16 10:27:05'),
(3, 23, 15000.00, 'Retard de paiement - 6 jours', '2024-10-20', 1, '2024-10-25', 'storage/recus_penalites/2024/penalite_003.pdf', '2026-01-16 10:27:05'),
(4, 28, 30000.00, 'Retard de paiement - 12 jours', '2024-10-28', 0, NULL, NULL, '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id_permission` int NOT NULL AUTO_INCREMENT,
  `groupe_id` int NOT NULL,
  `ressource_id` int NOT NULL,
  `peut_lire` tinyint(1) DEFAULT '0',
  `peut_creer` tinyint(1) DEFAULT '0',
  `peut_modifier` tinyint(1) DEFAULT '0',
  `peut_supprimer` tinyint(1) DEFAULT '0',
  `peut_exporter` tinyint(1) DEFAULT '0',
  `peut_valider` tinyint(1) DEFAULT '0',
  `conditions_json` json DEFAULT NULL,
  PRIMARY KEY (`id_permission`),
  UNIQUE KEY `unique_groupe_ressource` (`groupe_id`,`ressource_id`),
  KEY `ressource_id` (`ressource_id`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `permissions`
--

INSERT INTO `permissions` (`id_permission`, `groupe_id`, `ressource_id`, `peut_lire`, `peut_creer`, `peut_modifier`, `peut_supprimer`, `peut_exporter`, `peut_valider`, `conditions_json`) VALUES
(1, 1, 9, 1, 1, 1, 1, 1, 1, NULL),
(2, 1, 26, 1, 1, 1, 1, 1, 1, NULL),
(3, 1, 28, 1, 1, 1, 1, 1, 1, NULL),
(4, 1, 24, 1, 1, 1, 1, 1, 1, NULL),
(5, 1, 13, 1, 1, 1, 1, 1, 1, NULL),
(6, 1, 14, 1, 1, 1, 1, 1, 1, NULL),
(7, 1, 27, 1, 1, 1, 1, 1, 1, NULL),
(8, 1, 25, 1, 1, 1, 1, 1, 1, NULL),
(9, 1, 12, 1, 1, 1, 1, 1, 1, NULL),
(10, 1, 6, 1, 1, 1, 1, 1, 1, NULL),
(11, 1, 8, 1, 1, 1, 1, 1, 1, NULL),
(12, 1, 5, 1, 1, 1, 1, 1, 1, NULL),
(13, 1, 21, 1, 1, 1, 1, 1, 1, NULL),
(14, 1, 3, 1, 1, 1, 1, 1, 1, NULL),
(15, 1, 16, 1, 1, 1, 1, 1, 1, NULL),
(16, 1, 30, 1, 1, 1, 1, 1, 1, NULL),
(17, 1, 23, 1, 1, 1, 1, 1, 1, NULL),
(18, 1, 18, 1, 1, 1, 1, 1, 1, NULL),
(19, 1, 22, 1, 1, 1, 1, 1, 1, NULL),
(20, 1, 19, 1, 1, 1, 1, 1, 1, NULL),
(21, 1, 20, 1, 1, 1, 1, 1, 1, NULL),
(22, 1, 4, 1, 1, 1, 1, 1, 1, NULL),
(23, 1, 7, 1, 1, 1, 1, 1, 1, NULL),
(24, 1, 15, 1, 1, 1, 1, 1, 1, NULL),
(25, 1, 29, 1, 1, 1, 1, 1, 1, NULL),
(26, 1, 2, 1, 1, 1, 1, 1, 1, NULL),
(27, 1, 17, 1, 1, 1, 1, 1, 1, NULL),
(28, 1, 10, 1, 1, 1, 1, 1, 1, NULL),
(29, 1, 1, 1, 1, 1, 1, 1, 1, NULL),
(30, 1, 11, 1, 1, 1, 1, 1, 1, NULL),
(32, 4, 5, 1, 1, 1, 0, 1, 0, NULL),
(33, 4, 13, 1, 1, 1, 0, 1, 1, NULL),
(34, 4, 19, 1, 1, 1, 0, 1, 0, NULL),
(35, 4, 20, 1, 1, 1, 0, 1, 0, NULL),
(36, 4, 25, 1, 0, 0, 0, 1, 0, NULL),
(37, 3, 15, 1, 0, 0, 0, 0, 1, NULL),
(38, 3, 22, 1, 1, 1, 0, 0, 0, NULL),
(39, 7, 14, 1, 0, 1, 0, 0, 1, NULL),
(40, 7, 15, 1, 0, 1, 0, 0, 1, NULL),
(41, 8, 5, 1, 0, 0, 0, 0, 0, NULL),
(42, 8, 15, 1, 0, 1, 0, 0, 0, NULL),
(43, 8, 16, 1, 0, 1, 0, 0, 0, NULL),
(44, 8, 17, 1, 0, 0, 0, 0, 0, NULL),
(45, 8, 23, 1, 1, 1, 0, 0, 0, NULL),
(46, 9, 12, 1, 0, 0, 0, 0, 0, NULL),
(47, 9, 13, 1, 1, 1, 0, 0, 0, NULL),
(48, 9, 15, 1, 1, 1, 0, 0, 0, NULL),
(49, 9, 17, 1, 0, 0, 0, 0, 0, NULL),
(50, 9, 18, 1, 0, 0, 0, 0, 0, NULL),
(51, 9, 19, 1, 0, 0, 0, 1, 0, NULL),
(52, 9, 23, 1, 1, 1, 0, 0, 0, NULL),
(53, 9, 29, 1, 1, 0, 0, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `permissions_actions_details`
--

DROP TABLE IF EXISTS `permissions_actions_details`;
CREATE TABLE IF NOT EXISTS `permissions_actions_details` (
  `id_action_detail` int NOT NULL AUTO_INCREMENT,
  `action_id` int NOT NULL,
  `sous_action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `necessite_validation` tinyint(1) DEFAULT '0',
  `niveau_risque` enum('faible','moyen','eleve','critique') COLLATE utf8mb4_unicode_ci DEFAULT 'moyen',
  `log_obligatoire` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_action_detail`),
  UNIQUE KEY `uk_action_sous_action` (`action_id`,`sous_action`),
  KEY `idx_action` (`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `permissions_cache`
--

DROP TABLE IF EXISTS `permissions_cache`;
CREATE TABLE IF NOT EXISTS `permissions_cache` (
  `utilisateur_id` int NOT NULL,
  `ressource_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permissions_json` json NOT NULL,
  `genere_le` datetime DEFAULT CURRENT_TIMESTAMP,
  `expire_le` datetime DEFAULT NULL,
  PRIMARY KEY (`utilisateur_id`,`ressource_code`),
  KEY `idx_expire` (`expire_le`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `permissions_cache`
--

INSERT INTO `permissions_cache` (`utilisateur_id`, `ressource_code`, `permissions_json`, `genere_le`, `expire_le`) VALUES
(1, 'audit', '{\"peut_lire\": true, \"peut_creer\": true, \"peut_valider\": true, \"peut_exporter\": true, \"peut_modifier\": true, \"peut_supprimer\": true}', '2026-01-16 10:46:13', '2026-01-16 10:51:13'),
(1, 'candidatures', '{\"peut_lire\": true, \"peut_creer\": true, \"peut_valider\": true, \"peut_exporter\": true, \"peut_modifier\": true, \"peut_supprimer\": true}', '2026-01-16 09:30:43', '2026-01-16 09:35:43'),
(1, 'commission', '{\"peut_lire\": true, \"peut_creer\": true, \"peut_valider\": true, \"peut_exporter\": true, \"peut_modifier\": true, \"peut_supprimer\": true}', '2026-01-16 09:30:30', '2026-01-16 09:35:30'),
(1, 'configuration', '{\"peut_lire\": true, \"peut_creer\": true, \"peut_valider\": true, \"peut_exporter\": true, \"peut_modifier\": true, \"peut_supprimer\": true}', '2026-01-16 10:46:03', '2026-01-16 10:51:03'),
(1, 'etudiants', '{\"peut_lire\": true, \"peut_creer\": true, \"peut_valider\": true, \"peut_exporter\": true, \"peut_modifier\": true, \"peut_supprimer\": true}', '2026-01-16 10:45:37', '2026-01-16 10:50:37'),
(1, 'jury', '{\"peut_lire\": true, \"peut_creer\": true, \"peut_valider\": true, \"peut_exporter\": true, \"peut_modifier\": true, \"peut_supprimer\": true}', '2026-01-16 01:48:49', '2026-01-16 01:53:49'),
(1, 'paiements', '{\"peut_lire\": true, \"peut_creer\": true, \"peut_valider\": true, \"peut_exporter\": true, \"peut_modifier\": true, \"peut_supprimer\": true}', '2026-01-16 08:33:54', '2026-01-16 08:38:54'),
(1, 'rapports', '{\"peut_lire\": true, \"peut_creer\": true, \"peut_valider\": true, \"peut_exporter\": true, \"peut_modifier\": true, \"peut_supprimer\": true}', '2026-01-16 10:45:59', '2026-01-16 10:50:59'),
(1, 'soutenances', '{\"peut_lire\": true, \"peut_creer\": true, \"peut_valider\": true, \"peut_exporter\": true, \"peut_modifier\": true, \"peut_supprimer\": true}', '2026-01-16 10:46:08', '2026-01-16 10:51:08'),
(1, 'utilisateurs', '{\"peut_lire\": true, \"peut_creer\": true, \"peut_valider\": true, \"peut_exporter\": true, \"peut_modifier\": true, \"peut_supprimer\": true}', '2026-01-16 08:33:42', '2026-01-16 08:38:42');

-- --------------------------------------------------------

--
-- Structure de la table `permissions_conditions`
--

DROP TABLE IF EXISTS `permissions_conditions`;
CREATE TABLE IF NOT EXISTS `permissions_conditions` (
  `id_condition` int NOT NULL AUTO_INCREMENT,
  `permission_id` int NOT NULL,
  `type_condition` enum('temporelle','ip','role','custom') COLLATE utf8mb4_unicode_ci NOT NULL,
  `condition_json` json NOT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_condition`),
  KEY `idx_permission` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `permissions_delegations`
--

DROP TABLE IF EXISTS `permissions_delegations`;
CREATE TABLE IF NOT EXISTS `permissions_delegations` (
  `id_delegation` int NOT NULL AUTO_INCREMENT,
  `utilisateur_source_id` int NOT NULL,
  `utilisateur_cible_id` int NOT NULL,
  `permission_id` int NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `raison` text COLLATE utf8mb4_unicode_ci,
  `approuve_par` int DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_delegation`),
  KEY `permission_id` (`permission_id`),
  KEY `approuve_par` (`approuve_par`),
  KEY `idx_source` (`utilisateur_source_id`),
  KEY `idx_cible` (`utilisateur_cible_id`),
  KEY `idx_dates` (`date_debut`,`date_fin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personnel_admin`
--

DROP TABLE IF EXISTS `personnel_admin`;
CREATE TABLE IF NOT EXISTS `personnel_admin` (
  `id_pers_admin` int NOT NULL AUTO_INCREMENT,
  `nom_pers` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom_pers` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_pers` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone_pers` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fonction_id` int DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pers_admin`),
  UNIQUE KEY `email_pers` (`email_pers`),
  KEY `idx_nom` (`nom_pers`,`prenom_pers`),
  KEY `idx_email` (`email_pers`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `personnel_admin`
--

INSERT INTO `personnel_admin` (`id_pers_admin`, `nom_pers`, `prenom_pers`, `email_pers`, `telephone_pers`, `fonction_id`, `actif`, `created_at`, `updated_at`) VALUES
(1, 'ADMIN', 'System', 'admin@checkmaster.ufhb.ci', '+225 00 00 00 00', NULL, 1, '2026-01-15 13:45:18', '2026-01-15 13:45:18'),
(2, 'KOUAME', 'Amani Albert', 'kouame.amani@ufhb.edu.ci', '+225 20 21 00 01', 5, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(3, 'YAPI', 'Clarisse', 'yapi.clarisse@ufhb.edu.ci', '+225 20 21 00 02', 4, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(4, 'DOSSO', 'Aminata', 'dosso.aminata@ufhb.edu.ci', '+225 20 21 00 04', 7, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(5, 'TRAORE', 'Mamadou', 'traore.mamadou.admin@ufhb.edu.ci', '+225 20 21 00 05', 7, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(6, 'COULIBALY', 'Fatoumata', 'coulibaly.fatoumata.admin@ufhb.edu.ci', '+225 20 21 00 06', 7, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(7, 'BAMBA', 'Seydou', 'bamba.seydou@ufhb.edu.ci', '+225 20 21 00 07', 7, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(8, 'KOUASSI', 'Estelle', 'kouassi.estelle@ufhb.edu.ci', '+225 20 21 00 08', 8, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(9, 'DIALLO', 'Aissatou', 'diallo.aissatou.admin@ufhb.edu.ci', '+225 20 21 00 09', 8, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(10, 'N\'GUESSAN', 'Marie', 'nguessan.marie@ufhb.edu.ci', '+225 20 21 00 10', 6, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(11, 'KOFFI', 'Adjoua', 'koffi.adjoua@ufhb.edu.ci', '+225 20 21 00 11', 6, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(12, 'AKA', 'Berthe', 'aka.berthe@ufhb.edu.ci', '+225 20 21 00 12', 6, 1, '2026-01-16 10:27:05', '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `pister`
--

DROP TABLE IF EXISTS `pister`;
CREATE TABLE IF NOT EXISTS `pister` (
  `id_pister` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entite_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entite_id` int DEFAULT NULL,
  `donnees_snapshot` json DEFAULT NULL,
  `ip_adresse` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pister`),
  KEY `idx_utilisateur` (`utilisateur_id`),
  KEY `idx_entite` (`entite_type`,`entite_id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `pister`
--

INSERT INTO `pister` (`id_pister`, `utilisateur_id`, `action`, `entite_type`, `entite_id`, `donnees_snapshot`, `ip_adresse`, `user_agent`, `created_at`) VALUES
(1, NULL, 'echec_connexion', 'tentative', NULL, '{\"login\": \"admin@checkmaster.ufhb.ci\", \"raison\": \"Mot de passe incorrect (2 échecs)\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-15 22:54:04'),
(2, NULL, 'connexion', 'utilisateur', 1, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-15 22:58:47'),
(3, NULL, 'deconnexion', 'utilisateur', 1, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-16 01:27:48'),
(4, NULL, 'echec_connexion', 'tentative', NULL, '{\"login\": \"admin@checkmaster.ufhb.ci\", \"raison\": \"Mot de passe incorrect (1 échecs)\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-16 01:28:38'),
(5, NULL, 'echec_connexion', 'tentative', NULL, '{\"login\": \"admin@checkmaster.ufhb.ci\", \"raison\": \"Mot de passe incorrect (2 échecs)\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-16 01:28:50'),
(6, NULL, 'echec_connexion', 'tentative', NULL, '{\"login\": \"admin@checkmaster.ufhb.ci\", \"raison\": \"Délai 1 min après 3 échecs\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-16 01:29:03'),
(7, NULL, 'echec_connexion', 'tentative', NULL, '{\"login\": \"admin@checkmaster.ufhb.ci\", \"raison\": \"Compte verrouillé\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-16 01:29:18'),
(8, NULL, 'connexion', 'utilisateur', 1, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-16 01:32:17'),
(9, NULL, 'deconnexion', 'utilisateur', 1, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-16 01:32:52'),
(10, NULL, 'echec_connexion', 'tentative', NULL, '{\"login\": \"admin@checkmaster.ufhb.ci\", \"raison\": \"Mot de passe incorrect (1 échecs)\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-16 01:33:02'),
(11, NULL, 'echec_connexion', 'tentative', NULL, '{\"login\": \"admin@checkmaster.ufhb.ci\", \"raison\": \"Mot de passe incorrect (2 échecs)\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-16 01:41:56'),
(12, NULL, 'echec_connexion', 'tentative', NULL, '{\"login\": \"admin@checkmaster.ufhb.ci\", \"raison\": \"Délai 1 min après 3 échecs\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-16 01:46:12'),
(13, NULL, 'echec_connexion', 'tentative', NULL, '{\"login\": \"admin@checkmaster.ufhb.ci\", \"raison\": \"Compte verrouillé\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-16 01:46:25'),
(14, NULL, 'echec_connexion', 'tentative', NULL, '{\"login\": \"admin@checkmaster.ufhb.ci\", \"raison\": \"Compte verrouillé\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-16 01:47:00'),
(15, NULL, 'connexion', 'utilisateur', 1, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-16 01:48:15'),
(16, NULL, 'deconnexion', 'utilisateur', 1, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-16 01:48:59'),
(17, NULL, 'connexion', 'utilisateur', 1, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-16 01:49:09'),
(18, NULL, 'deconnexion', 'utilisateur', 1, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-16 01:51:37'),
(19, NULL, 'connexion', 'utilisateur', 1, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-16 01:51:49'),
(20, NULL, 'deconnexion', 'utilisateur', 1, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-16 08:33:58'),
(21, NULL, 'connexion', 'utilisateur', 1, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-16 09:30:16'),
(22, NULL, 'deconnexion', 'utilisateur', 1, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-16 10:12:57'),
(23, NULL, 'connexion', 'utilisateur', 1, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-16 10:13:04');

-- --------------------------------------------------------

--
-- Structure de la table `rapports_etudiants`
--

DROP TABLE IF EXISTS `rapports_etudiants`;
CREATE TABLE IF NOT EXISTS `rapports_etudiants` (
  `id_rapport` int NOT NULL AUTO_INCREMENT,
  `dossier_id` int NOT NULL,
  `titre` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contenu_html` longtext COLLATE utf8mb4_unicode_ci,
  `version` int DEFAULT '1',
  `statut` enum('Brouillon','Soumis','En_evaluation','Valide','Rejete') COLLATE utf8mb4_unicode_ci DEFAULT 'Brouillon',
  `date_depot` datetime DEFAULT NULL,
  `chemin_fichier` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hash_fichier` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_rapport`),
  KEY `idx_dossier` (`dossier_id`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `rapports_etudiants`
--

INSERT INTO `rapports_etudiants` (`id_rapport`, `dossier_id`, `titre`, `contenu_html`, `version`, `statut`, `date_depot`, `chemin_fichier`, `hash_fichier`) VALUES
(1, 1, 'Système de gestion de stock avec prédiction ML', '<h1>Introduction</h1><p>Ce mémoire présente...</p>', 3, 'Valide', '2024-10-15 00:00:00', 'storage/rapports/2024/rapport_001_v3.pdf', 'abc123def456'),
(2, 2, 'Plateforme e-banking sécurisée', '<h1>Introduction</h1><p>Ce mémoire aborde...</p>', 2, 'Valide', '2024-10-16 00:00:00', 'storage/rapports/2024/rapport_002_v2.pdf', 'abc123def457'),
(3, 3, 'Système de suivi de flotte GPS', '<h1>Introduction</h1><p>Ce projet traite...</p>', 2, 'Valide', '2024-10-17 00:00:00', 'storage/rapports/2024/rapport_003_v2.pdf', 'abc123def458'),
(4, 4, 'Chatbot intelligent NLP', '<h1>Introduction</h1><p>L\'objectif de ce travail...</p>', 1, 'Valide', '2024-10-18 00:00:00', 'storage/rapports/2024/rapport_004_v1.pdf', 'abc123def459'),
(5, 5, 'Application gestion portefeuille client', '<h1>Introduction</h1><p>Dans ce mémoire...</p>', 2, 'Valide', '2024-10-19 00:00:00', 'storage/rapports/2024/rapport_005_v2.pdf', 'abc123def460'),
(6, 6, 'Système de vote électronique blockchain', '<h1>Introduction</h1><p>Ce mémoire explore...</p>', 1, 'Valide', '2024-10-20 00:00:00', 'storage/rapports/2024/rapport_006_v1.pdf', 'abc123def461'),
(7, 7, 'Plateforme analyse réseaux sociaux', '<h1>Introduction</h1><p>Ce travail présente...</p>', 2, 'Valide', '2024-10-21 00:00:00', 'storage/rapports/2024/rapport_007_v2.pdf', 'abc123def462'),
(8, 8, 'Système détection fraude', '<h1>Introduction</h1><p>L\'objectif principal...</p>', 1, 'En_evaluation', '2024-10-22 00:00:00', 'storage/rapports/2024/rapport_008_v1.pdf', 'abc123def463'),
(9, 9, 'Application télémédecine mobile', '<h1>Introduction</h1><p>Ce mémoire a pour but...</p>', 1, 'Soumis', '2024-10-23 00:00:00', 'storage/rapports/2024/rapport_009_v1.pdf', 'abc123def464'),
(10, 10, 'Système gestion documentaire OCR', '<h1>Introduction</h1><p>Dans le cadre de...</p>', 1, 'Soumis', '2024-10-24 00:00:00', 'storage/rapports/2024/rapport_010_v1.pdf', 'abc123def465'),
(11, 11, 'ERP simplifié pour PME', '<h1>Introduction</h1><p>Ce projet vise à...</p>', 1, 'Brouillon', '2024-10-25 00:00:00', NULL, NULL),
(16, 16, 'Analyse prédictive consommation électrique', '<h1>Introduction</h1><p>Ce travail de recherche...</p>', 2, 'Valide', '2024-10-28 00:00:00', 'storage/rapports/2024/rapport_016_v2.pdf', 'abc123def470'),
(17, 17, 'Gestion chaîne logistique portuaire', '<h1>Introduction</h1><p>Ce mémoire aborde la problématique...</p>', 1, 'Valide', '2024-10-29 00:00:00', 'storage/rapports/2024/rapport_017_v1.pdf', 'abc123def471'),
(18, 18, 'Système réservation vols dynamique', '<h1>Introduction</h1><p>L\'industrie du transport aérien...</p>', 2, 'Valide', '2024-10-30 00:00:00', 'storage/rapports/2024/rapport_018_v2.pdf', 'abc123def472'),
(19, 19, 'Plateforme paiement mobile', '<h1>Introduction</h1><p>Le secteur des fintechs...</p>', 1, 'Valide', '2024-10-31 00:00:00', 'storage/rapports/2024/rapport_019_v1.pdf', 'abc123def473'),
(20, 20, 'Tableau de bord décisionnel', '<h1>Introduction</h1><p>La prise de décision...</p>', 2, 'Valide', '2024-11-01 00:00:00', 'storage/rapports/2024/rapport_020_v2.pdf', 'abc123def474');

-- --------------------------------------------------------

--
-- Structure de la table `rapport_annotations`
--

DROP TABLE IF EXISTS `rapport_annotations`;
CREATE TABLE IF NOT EXISTS `rapport_annotations` (
  `id_annotation` int NOT NULL AUTO_INCREMENT,
  `rapport_id` int NOT NULL,
  `annotateur_id` int NOT NULL,
  `section` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Section du rapport annotée',
  `ligne_debut` int DEFAULT NULL,
  `ligne_fin` int DEFAULT NULL,
  `texte_original` text COLLATE utf8mb4_unicode_ci,
  `commentaire` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_annotation` enum('correction','suggestion','remarque','validation') COLLATE utf8mb4_unicode_ci DEFAULT 'remarque',
  `statut` enum('en_attente','pris_en_compte','rejete','resolu') COLLATE utf8mb4_unicode_ci DEFAULT 'en_attente',
  `priorite` enum('basse','normale','haute','critique') COLLATE utf8mb4_unicode_ci DEFAULT 'normale',
  `resolu_par` int DEFAULT NULL,
  `resolu_le` datetime DEFAULT NULL,
  `reponse` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_annotation`),
  KEY `resolu_par` (`resolu_par`),
  KEY `idx_rapport` (`rapport_id`),
  KEY `idx_annotateur` (`annotateur_id`),
  KEY `idx_statut` (`statut`),
  KEY `idx_type` (`type_annotation`),
  KEY `idx_priorite` (`priorite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Annotations et corrections sur les rapports de commission';

-- --------------------------------------------------------

--
-- Structure de la table `rapport_fichiers_attaches`
--

DROP TABLE IF EXISTS `rapport_fichiers_attaches`;
CREATE TABLE IF NOT EXISTS `rapport_fichiers_attaches` (
  `id_fichier_attache` int NOT NULL AUTO_INCREMENT,
  `rapport_id` int NOT NULL,
  `type_fichier` enum('annexe','piece_justificative','document_support','correction') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chemin_fichier` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taille_octets` bigint NOT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hash_sha256` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `upload_par` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_fichier_attache`),
  KEY `upload_par` (`upload_par`),
  KEY `idx_rapport` (`rapport_id`),
  KEY `idx_type` (`type_fichier`),
  KEY `idx_hash` (`hash_sha256`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Fichiers attachés aux rapports de commission';

-- --------------------------------------------------------

--
-- Structure de la table `rapport_validations`
--

DROP TABLE IF EXISTS `rapport_validations`;
CREATE TABLE IF NOT EXISTS `rapport_validations` (
  `id_validation` int NOT NULL AUTO_INCREMENT,
  `rapport_id` int NOT NULL,
  `validateur_id` int NOT NULL,
  `niveau_validation` int NOT NULL COMMENT '1=Chef département, 2=Directeur études, 3=VP',
  `statut_validation` enum('en_attente','valide','refuse','demande_correction') COLLATE utf8mb4_unicode_ci NOT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `date_validation` datetime DEFAULT NULL,
  `ordre_validation` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_validation`),
  UNIQUE KEY `uk_rapport_validateur` (`rapport_id`,`validateur_id`),
  KEY `idx_rapport` (`rapport_id`),
  KEY `idx_validateur` (`validateur_id`),
  KEY `idx_statut` (`statut_validation`),
  KEY `idx_niveau` (`niveau_validation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Workflow de validation des rapports de commission';

-- --------------------------------------------------------

--
-- Structure de la table `rapport_versions`
--

DROP TABLE IF EXISTS `rapport_versions`;
CREATE TABLE IF NOT EXISTS `rapport_versions` (
  `id_version` int NOT NULL AUTO_INCREMENT,
  `rapport_id` int NOT NULL,
  `numero_version` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contenu_json` json NOT NULL COMMENT 'Snapshot complet du rapport',
  `modifie_par` int NOT NULL,
  `commentaire_version` text COLLATE utf8mb4_unicode_ci,
  `date_version` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_version`),
  UNIQUE KEY `uk_rapport_version` (`rapport_id`,`numero_version`),
  KEY `modifie_par` (`modifie_par`),
  KEY `idx_rapport` (`rapport_id`),
  KEY `idx_date` (`date_version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Versions historiques des rapports de commission';

-- --------------------------------------------------------

--
-- Structure de la table `rattacher`
--

DROP TABLE IF EXISTS `rattacher`;
CREATE TABLE IF NOT EXISTS `rattacher` (
  `id_rattacher` int NOT NULL AUTO_INCREMENT,
  `id_GU` int NOT NULL,
  `id_traitement` int NOT NULL,
  `id_action` int NOT NULL,
  PRIMARY KEY (`id_rattacher`),
  UNIQUE KEY `unique_permission` (`id_GU`,`id_traitement`,`id_action`),
  KEY `idx_groupe` (`id_GU`),
  KEY `idx_traitement` (`id_traitement`),
  KEY `idx_action` (`id_action`)
) ENGINE=InnoDB AUTO_INCREMENT=475 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `rattacher`
--

INSERT INTO `rattacher` (`id_rattacher`, `id_GU`, `id_traitement`, `id_action`) VALUES
(4, 1, 1, 1),
(6, 1, 1, 2),
(3, 1, 1, 3),
(2, 1, 1, 4),
(1, 1, 1, 5),
(5, 1, 1, 6),
(10, 1, 2, 1),
(12, 1, 2, 2),
(9, 1, 2, 3),
(8, 1, 2, 4),
(7, 1, 2, 5),
(11, 1, 2, 6),
(16, 1, 3, 1),
(18, 1, 3, 2),
(15, 1, 3, 3),
(14, 1, 3, 4),
(13, 1, 3, 5),
(17, 1, 3, 6),
(22, 1, 4, 1),
(24, 1, 4, 2),
(21, 1, 4, 3),
(20, 1, 4, 4),
(19, 1, 4, 5),
(23, 1, 4, 6),
(28, 1, 5, 1),
(30, 1, 5, 2),
(27, 1, 5, 3),
(26, 1, 5, 4),
(25, 1, 5, 5),
(29, 1, 5, 6),
(34, 1, 6, 1),
(36, 1, 6, 2),
(33, 1, 6, 3),
(32, 1, 6, 4),
(31, 1, 6, 5),
(35, 1, 6, 6),
(40, 1, 7, 1),
(42, 1, 7, 2),
(39, 1, 7, 3),
(38, 1, 7, 4),
(37, 1, 7, 5),
(41, 1, 7, 6),
(46, 1, 8, 1),
(48, 1, 8, 2),
(45, 1, 8, 3),
(44, 1, 8, 4),
(43, 1, 8, 5),
(47, 1, 8, 6),
(52, 1, 9, 1),
(54, 1, 9, 2),
(51, 1, 9, 3),
(50, 1, 9, 4),
(49, 1, 9, 5),
(53, 1, 9, 6),
(58, 1, 10, 1),
(60, 1, 10, 2),
(57, 1, 10, 3),
(56, 1, 10, 4),
(55, 1, 10, 5),
(59, 1, 10, 6),
(64, 1, 11, 1),
(66, 1, 11, 2),
(63, 1, 11, 3),
(62, 1, 11, 4),
(61, 1, 11, 5),
(65, 1, 11, 6),
(70, 1, 12, 1),
(72, 1, 12, 2),
(69, 1, 12, 3),
(68, 1, 12, 4),
(67, 1, 12, 5),
(71, 1, 12, 6),
(76, 1, 13, 1),
(78, 1, 13, 2),
(75, 1, 13, 3),
(74, 1, 13, 4),
(73, 1, 13, 5),
(77, 1, 13, 6),
(82, 1, 14, 1),
(84, 1, 14, 2),
(81, 1, 14, 3),
(80, 1, 14, 4),
(79, 1, 14, 5),
(83, 1, 14, 6),
(88, 1, 15, 1),
(90, 1, 15, 2),
(87, 1, 15, 3),
(86, 1, 15, 4),
(85, 1, 15, 5),
(89, 1, 15, 6),
(94, 1, 16, 1),
(96, 1, 16, 2),
(93, 1, 16, 3),
(92, 1, 16, 4),
(91, 1, 16, 5),
(95, 1, 16, 6),
(100, 1, 17, 1),
(102, 1, 17, 2),
(99, 1, 17, 3),
(98, 1, 17, 4),
(97, 1, 17, 5),
(101, 1, 17, 6),
(106, 1, 18, 1),
(108, 1, 18, 2),
(105, 1, 18, 3),
(104, 1, 18, 4),
(103, 1, 18, 5),
(107, 1, 18, 6),
(112, 1, 19, 1),
(114, 1, 19, 2),
(111, 1, 19, 3),
(110, 1, 19, 4),
(109, 1, 19, 5),
(113, 1, 19, 6),
(118, 1, 20, 1),
(120, 1, 20, 2),
(117, 1, 20, 3),
(116, 1, 20, 4),
(115, 1, 20, 5),
(119, 1, 20, 6),
(124, 1, 21, 1),
(126, 1, 21, 2),
(123, 1, 21, 3),
(122, 1, 21, 4),
(121, 1, 21, 5),
(125, 1, 21, 6),
(130, 1, 22, 1),
(132, 1, 22, 2),
(129, 1, 22, 3),
(128, 1, 22, 4),
(127, 1, 22, 5),
(131, 1, 22, 6),
(136, 1, 23, 1),
(138, 1, 23, 2),
(135, 1, 23, 3),
(134, 1, 23, 4),
(133, 1, 23, 5),
(137, 1, 23, 6),
(142, 1, 24, 1),
(144, 1, 24, 2),
(141, 1, 24, 3),
(140, 1, 24, 4),
(139, 1, 24, 5),
(143, 1, 24, 6),
(148, 1, 25, 1),
(150, 1, 25, 2),
(147, 1, 25, 3),
(146, 1, 25, 4),
(145, 1, 25, 5),
(149, 1, 25, 6),
(154, 1, 26, 1),
(156, 1, 26, 2),
(153, 1, 26, 3),
(152, 1, 26, 4),
(151, 1, 26, 5),
(155, 1, 26, 6),
(160, 1, 27, 1),
(162, 1, 27, 2),
(159, 1, 27, 3),
(158, 1, 27, 4),
(157, 1, 27, 5),
(161, 1, 27, 6),
(166, 1, 28, 1),
(168, 1, 28, 2),
(165, 1, 28, 3),
(164, 1, 28, 4),
(163, 1, 28, 5),
(167, 1, 28, 6),
(172, 1, 29, 1),
(174, 1, 29, 2),
(171, 1, 29, 3),
(170, 1, 29, 4),
(169, 1, 29, 5),
(173, 1, 29, 6),
(178, 1, 30, 1),
(180, 1, 30, 2),
(177, 1, 30, 3),
(176, 1, 30, 4),
(175, 1, 30, 5),
(179, 1, 30, 6),
(256, 2, 25, 1),
(257, 2, 25, 2),
(258, 2, 25, 6),
(259, 2, 26, 1),
(260, 2, 26, 6),
(261, 3, 15, 1),
(262, 3, 15, 5),
(263, 3, 22, 1),
(264, 3, 22, 2),
(265, 3, 22, 3),
(266, 4, 5, 1),
(267, 4, 5, 2),
(268, 4, 5, 3),
(269, 4, 5, 6),
(270, 4, 13, 1),
(271, 4, 13, 2),
(272, 4, 13, 3),
(273, 4, 13, 5),
(274, 4, 13, 6),
(275, 4, 19, 1),
(276, 4, 19, 2),
(277, 4, 19, 3),
(278, 4, 19, 6),
(279, 4, 20, 1),
(280, 4, 20, 2),
(281, 4, 20, 3),
(282, 4, 20, 6),
(283, 4, 25, 1),
(284, 4, 25, 6),
(285, 5, 5, 1),
(286, 5, 5, 6),
(287, 5, 6, 1),
(288, 5, 6, 6),
(289, 5, 12, 1),
(290, 5, 12, 6),
(291, 5, 15, 1),
(292, 5, 15, 6),
(293, 5, 16, 1),
(294, 5, 16, 5),
(295, 5, 17, 1),
(296, 5, 17, 5),
(297, 6, 5, 1),
(298, 6, 5, 6),
(299, 6, 12, 1),
(300, 6, 12, 3),
(301, 6, 12, 6),
(302, 6, 15, 1),
(303, 6, 15, 6),
(304, 7, 14, 1),
(305, 7, 14, 3),
(306, 7, 15, 1),
(307, 7, 15, 3),
(308, 7, 15, 5),
(309, 8, 5, 1),
(310, 8, 15, 1),
(311, 8, 15, 3),
(312, 8, 16, 1),
(313, 8, 16, 3),
(314, 8, 17, 1),
(315, 8, 23, 1),
(316, 8, 23, 2),
(317, 8, 23, 3),
(318, 9, 12, 1),
(319, 9, 13, 1),
(320, 9, 13, 2),
(321, 9, 13, 3),
(322, 9, 15, 1),
(323, 9, 15, 2),
(324, 9, 15, 3),
(325, 9, 17, 1),
(326, 9, 18, 1),
(327, 9, 19, 1),
(328, 9, 19, 6),
(329, 9, 23, 1),
(330, 9, 23, 2),
(331, 9, 23, 3),
(332, 9, 29, 1),
(333, 9, 29, 2),
(334, 10, 14, 1),
(335, 10, 14, 2),
(336, 10, 14, 3),
(337, 10, 14, 5),
(338, 10, 15, 1),
(339, 10, 15, 5),
(340, 10, 16, 1),
(341, 10, 16, 2),
(342, 10, 16, 3),
(343, 10, 16, 5),
(344, 11, 16, 1),
(345, 11, 17, 1),
(346, 11, 17, 3),
(347, 11, 18, 1),
(348, 11, 18, 2),
(349, 11, 18, 3),
(350, 12, 5, 1),
(351, 12, 12, 1),
(352, 12, 15, 1),
(353, 12, 15, 3),
(354, 12, 15, 5),
(355, 12, 23, 1),
(356, 12, 23, 2),
(357, 12, 23, 3),
(358, 13, 5, 1),
(359, 13, 12, 1),
(360, 13, 15, 1),
(361, 13, 15, 3),
(362, 13, 23, 1),
(363, 13, 23, 2),
(364, 13, 23, 3);

-- --------------------------------------------------------

--
-- Structure de la table `reclamations`
--

DROP TABLE IF EXISTS `reclamations`;
CREATE TABLE IF NOT EXISTS `reclamations` (
  `id_reclamation` int NOT NULL AUTO_INCREMENT,
  `etudiant_id` int NOT NULL,
  `type_reclamation` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sujet` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `priorite` enum('Basse','Normale','Haute','Critique') COLLATE utf8mb4_unicode_ci DEFAULT 'Normale',
  `entite_concernee_id` int DEFAULT NULL,
  `statut` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'En_attente',
  `resolution` text COLLATE utf8mb4_unicode_ci,
  `motif_rejet` text COLLATE utf8mb4_unicode_ci,
  `prise_en_charge_par` int DEFAULT NULL,
  `prise_en_charge_le` datetime DEFAULT NULL,
  `resolue_par` int DEFAULT NULL,
  `resolue_le` datetime DEFAULT NULL,
  `reponse` text COLLATE utf8mb4_unicode_ci,
  `traite_par` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_reclamation`),
  KEY `prise_en_charge_par` (`prise_en_charge_par`),
  KEY `resolue_par` (`resolue_par`),
  KEY `traite_par` (`traite_par`),
  KEY `idx_etudiant` (`etudiant_id`),
  KEY `idx_statut` (`statut`),
  KEY `idx_type` (`type_reclamation`),
  KEY `idx_priorite` (`priorite`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reclamations`
--

INSERT INTO `reclamations` (`id_reclamation`, `etudiant_id`, `type_reclamation`, `sujet`, `description`, `priorite`, `entite_concernee_id`, `statut`, `resolution`, `motif_rejet`, `prise_en_charge_par`, `prise_en_charge_le`, `resolue_par`, `resolue_le`, `reponse`, `traite_par`, `created_at`, `updated_at`) VALUES
(1, 12, 'Financiere', 'Erreur de calcul sur le montant des pénalités', 'Bonjour,\n\nJe constate que la pénalité de retard qui m\'a été appliquée est de 50,000 FCFA alors que mon retard n\'était que de 10 jours.\n\nSelon le règlement, le taux est de 0.5% par jour, ce qui devrait donner 27,500 FCFA et non 50,000 FCFA.\n\nMerci de vérifier ce calcul.\n\nCordialement,\nTAPE Didier', 'Haute', NULL, 'Resolue', NULL, NULL, 30, NULL, NULL, NULL, NULL, NULL, '2026-01-16 11:56:13', '2026-01-16 11:56:13'),
(2, 8, 'Academique', 'Demande de révision de la note de soutenance', 'Bonjour,\n\nJe souhaiterais avoir plus de détails sur l\'évaluation de ma soutenance, notamment sur les critères utilisés pour attribuer la note de forme.\n\nJe vous remercie.', 'Normale', NULL, 'Resolue', NULL, NULL, 80, NULL, NULL, NULL, NULL, NULL, '2026-01-16 11:56:13', '2026-01-16 11:56:13'),
(3, 11, 'Administrative', 'Retard dans le traitement de ma candidature', 'Bonjour,\n\nMa candidature a été soumise il y a plus de 15 jours et elle est toujours en attente de validation par le service scolarité.\n\nPourriez-vous me donner une estimation du délai de traitement?\n\nMerci.', 'Normale', NULL, 'En_cours', NULL, NULL, 31, NULL, NULL, NULL, NULL, NULL, '2026-01-16 11:56:13', '2026-01-16 11:56:13'),
(4, 23, 'Technique', 'Impossible de télécharger mon reçu de paiement', 'Bonjour,\n\nLorsque je clique sur \"Télécharger le reçu\" dans mon espace étudiant, j\'obtiens une erreur 500.\n\nCela fait 3 jours que le problème persiste.\n\nMerci de résoudre ce problème.', 'Haute', NULL, 'En_cours', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, '2026-01-16 11:56:13', '2026-01-16 11:56:13'),
(5, 28, 'Financiere', 'Demande d\'échelonnement de paiement', 'Bonjour,\n\nSuite à des difficultés financières temporaires, je souhaiterais solliciter un échelonnement pour le solde restant de mes frais de scolarité (150,000 FCFA).\n\nJe m\'engage à régler ce montant en 3 versements mensuels.\n\nMerci de considérer ma demande.', 'Basse', NULL, 'En_attente', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-16 11:56:13', '2026-01-16 11:56:13');

-- --------------------------------------------------------

--
-- Structure de la table `reclamation_reponses`
--

DROP TABLE IF EXISTS `reclamation_reponses`;
CREATE TABLE IF NOT EXISTS `reclamation_reponses` (
  `id_reponse` int NOT NULL AUTO_INCREMENT,
  `reclamation_id` int NOT NULL,
  `auteur_id` int NOT NULL,
  `contenu` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_reponse`),
  KEY `idx_reclamation` (`reclamation_id`),
  KEY `idx_auteur` (`auteur_id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reclamation_reponses`
--

INSERT INTO `reclamation_reponses` (`id_reponse`, `reclamation_id`, `auteur_id`, `contenu`, `created_at`) VALUES
(1, 1, 30, 'Bonjour Monsieur TAPE,\n\nAprès vérification, nous confirmons effectivement une erreur de calcul. La pénalité correcte est de 27,500 FCFA.\n\nNous avons procédé à la correction. Votre nouveau solde sera mis à jour dans les 24h.\n\nNous vous prions de nous excuser pour ce désagrément.\n\nService Scolarité', '2026-01-16 11:56:13'),
(2, 1, 12, 'Merci pour votre réactivité. Je confirme que le montant a bien été corrigé.', '2026-01-16 11:56:13'),
(3, 2, 80, 'Bonjour,\n\nSuite à votre demande, voici le détail de l\'évaluation:\n\n- Note de fond: 15/20 (analyse méthodologique perfectible)\n- Note de forme: 14/20 (mise en page à améliorer)\n- Note de soutenance: 15/20 (bonne présentation orale)\n\nCes notes ont été attribuées selon la grille de critères en vigueur.\n\nCordialement,\nPrésident de la Commission', '2026-01-16 11:56:13'),
(4, 3, 31, 'Bonjour,\n\nNous avons bien reçu votre réclamation. Le retard est dû à un afflux important de candidatures ce mois-ci.\n\nVotre dossier sera traité en priorité dans les 48h.\n\nService Scolarité', '2026-01-16 11:56:13');

-- --------------------------------------------------------

--
-- Structure de la table `ressources`
--

DROP TABLE IF EXISTS `ressources`;
CREATE TABLE IF NOT EXISTS `ressources` (
  `id_ressource` int NOT NULL AUTO_INCREMENT,
  `code_ressource` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_ressource` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `module` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_ressource`),
  UNIQUE KEY `code_ressource` (`code_ressource`),
  KEY `idx_code` (`code_ressource`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ressources`
--

INSERT INTO `ressources` (`id_ressource`, `code_ressource`, `nom_ressource`, `description`, `module`) VALUES
(1, 'utilisateurs', 'Gestion Utilisateurs', 'CRUD utilisateurs du système', 'authentification'),
(2, 'sessions', 'Sessions Actives', 'Gestion des sessions connectées', 'authentification'),
(3, 'groupes', 'Groupes Utilisateurs', 'Gestion des groupes et rôles', 'authentification'),
(4, 'permissions', 'Permissions', 'Configuration des permissions', 'authentification'),
(5, 'etudiants', 'Étudiants', 'Gestion des étudiants', 'academique'),
(6, 'enseignants', 'Enseignants', 'Gestion des enseignants', 'academique'),
(7, 'personnel', 'Personnel Admin', 'Gestion du personnel administratif', 'academique'),
(8, 'entreprises', 'Entreprises', 'Référentiel entreprises partenaires', 'academique'),
(9, 'annees', 'Années Académiques', 'Gestion des années académiques', 'academique'),
(10, 'ue', 'Unités Enseignement', 'Gestion UE/ECUE', 'academique'),
(11, 'workflow', 'Workflow', 'Gestion états et transitions', 'workflow'),
(12, 'dossiers', 'Dossiers Étudiants', 'Gestion dossiers étudiants', 'workflow'),
(13, 'candidatures', 'Candidatures', 'Gestion des candidatures', 'workflow'),
(14, 'commission', 'Commission', 'Sessions et votes commission', 'commission'),
(15, 'rapports', 'Rapports', 'Rapports étudiants', 'commission'),
(16, 'jury', 'Jury', 'Constitution des jurys', 'soutenance'),
(17, 'soutenances', 'Soutenances', 'Planification soutenances', 'soutenance'),
(18, 'notes', 'Notes', 'Saisie et consultation notes', 'soutenance'),
(19, 'paiements', 'Paiements', 'Enregistrement paiements', 'financier'),
(20, 'penalites', 'Pénalités', 'Gestion pénalités', 'financier'),
(21, 'exonerations', 'Exonérations', 'Gestion exonérations', 'financier'),
(22, 'notifications', 'Notifications', 'Gestion notifications', 'communication'),
(23, 'messages', 'Messages', 'Messagerie interne', 'communication'),
(24, 'calendrier', 'Calendrier', 'Calendrier académique', 'communication'),
(25, 'documents', 'Documents', 'Documents générés', 'documents'),
(26, 'archives', 'Archives', 'Archives et intégrité', 'documents'),
(27, 'configuration', 'Configuration', 'Paramètres système', 'administration'),
(28, 'audit', 'Audit', 'Logs et traçabilité', 'administration'),
(29, 'reclamations', 'Réclamations', 'Réclamations étudiantes', 'administration'),
(30, 'maintenance', 'Maintenance', 'Opérations maintenance', 'administration');

-- --------------------------------------------------------

--
-- Structure de la table `roles_jury`
--

DROP TABLE IF EXISTS `roles_jury`;
CREATE TABLE IF NOT EXISTS `roles_jury` (
  `id_role` int NOT NULL AUTO_INCREMENT,
  `code_role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle_role` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordre_affichage` int DEFAULT NULL,
  PRIMARY KEY (`id_role`),
  UNIQUE KEY `code_role` (`code_role`),
  KEY `idx_code` (`code_role`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `roles_jury`
--

INSERT INTO `roles_jury` (`id_role`, `code_role`, `libelle_role`, `ordre_affichage`) VALUES
(1, 'PRESIDENT', 'Président du jury', 1),
(2, 'DIRECTEUR', 'Directeur de mémoire', 2),
(3, 'RAPPORTEUR', 'Rapporteur', 3),
(4, 'EXAMINATEUR', 'Examinateur', 4),
(5, 'INVITE', 'Membre invité', 5),
(6, 'MAITRE_STAGE', 'Maître de stage', 6);

-- --------------------------------------------------------

--
-- Structure de la table `roles_temporaires`
--

DROP TABLE IF EXISTS `roles_temporaires`;
CREATE TABLE IF NOT EXISTS `roles_temporaires` (
  `id_role_temp` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `role_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contexte_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contexte_id` int DEFAULT NULL,
  `permissions_json` json NOT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `valide_de` datetime NOT NULL,
  `valide_jusqu_a` datetime NOT NULL,
  `cree_par` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_role_temp`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `cree_par` (`cree_par`),
  KEY `idx_validite` (`valide_de`,`valide_jusqu_a`),
  KEY `idx_actif` (`actif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `roles_temporaires_attributions`
--

DROP TABLE IF EXISTS `roles_temporaires_attributions`;
CREATE TABLE IF NOT EXISTS `roles_temporaires_attributions` (
  `id_attribution` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `type_role_temp_id` int NOT NULL,
  `contexte_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'dossier, commission, departement, etc',
  `contexte_id` int DEFAULT NULL COMMENT 'ID du contexte',
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `raison` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `demande_par` int NOT NULL,
  `approuve_par` int DEFAULT NULL,
  `date_approbation` datetime DEFAULT NULL,
  `statut` enum('en_attente','approuve','actif','expire','revoque') COLLATE utf8mb4_unicode_ci DEFAULT 'en_attente',
  `revoque_par` int DEFAULT NULL,
  `date_revocation` datetime DEFAULT NULL,
  `motif_revocation` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_attribution`),
  KEY `type_role_temp_id` (`type_role_temp_id`),
  KEY `demande_par` (`demande_par`),
  KEY `approuve_par` (`approuve_par`),
  KEY `revoque_par` (`revoque_par`),
  KEY `idx_utilisateur` (`utilisateur_id`),
  KEY `idx_dates` (`date_debut`,`date_fin`),
  KEY `idx_statut` (`statut`),
  KEY `idx_contexte` (`contexte_type`,`contexte_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `roles_temporaires_types`
--

DROP TABLE IF EXISTS `roles_temporaires_types`;
CREATE TABLE IF NOT EXISTS `roles_temporaires_types` (
  `id_type_role_temp` int NOT NULL AUTO_INCREMENT,
  `code_role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_role` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `permissions_incluses` json DEFAULT NULL,
  `duree_maximale_jours` int DEFAULT NULL,
  `necessite_approbation` tinyint(1) DEFAULT '1',
  `niveau_hierarchique` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_type_role_temp`),
  UNIQUE KEY `code_role` (`code_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `salles`
--

DROP TABLE IF EXISTS `salles`;
CREATE TABLE IF NOT EXISTS `salles` (
  `id_salle` int NOT NULL AUTO_INCREMENT,
  `nom_salle` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batiment` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacite` int DEFAULT NULL,
  `equipement_json` json DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_salle`),
  UNIQUE KEY `nom_salle` (`nom_salle`),
  KEY `idx_nom` (`nom_salle`),
  KEY `idx_actif` (`actif`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `salles`
--

INSERT INTO `salles` (`id_salle`, `nom_salle`, `batiment`, `capacite`, `equipement_json`, `actif`) VALUES
(1, 'Salle A101', 'Bâtiment A', 30, '{\"wifi\": true, \"video_projecteur\": true}', 1),
(2, 'Salle A102', 'Bâtiment A', 50, '{\"wifi\": true, \"visioconference\": true, \"video_projecteur\": true}', 1),
(3, 'Amphithéâtre 1', 'Bâtiment Principal', 200, '{\"micro\": true, \"video_projecteur\": true}', 1),
(4, 'Salle Informatique B201', 'Bâtiment B', 25, '{\"wifi\": true, \"ordinateurs\": 25}', 1);

-- --------------------------------------------------------

--
-- Structure de la table `semestre`
--

DROP TABLE IF EXISTS `semestre`;
CREATE TABLE IF NOT EXISTS `semestre` (
  `id_semestre` int NOT NULL AUTO_INCREMENT,
  `lib_semestre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `annee_acad_id` int NOT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  PRIMARY KEY (`id_semestre`),
  KEY `idx_annee` (`annee_acad_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `semestre`
--

INSERT INTO `semestre` (`id_semestre`, `lib_semestre`, `annee_acad_id`, `date_debut`, `date_fin`) VALUES
(1, 'Semestre 1', 1, '2024-09-01', '2025-01-31'),
(2, 'Semestre 2', 1, '2025-02-01', '2025-06-30');

-- --------------------------------------------------------

--
-- Structure de la table `sessions_actives`
--

DROP TABLE IF EXISTS `sessions_actives`;
CREATE TABLE IF NOT EXISTS `sessions_actives` (
  `id_session` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `token_session` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_adresse` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `derniere_activite` datetime DEFAULT CURRENT_TIMESTAMP,
  `expire_a` datetime NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_session`),
  UNIQUE KEY `token_session` (`token_session`),
  KEY `idx_token` (`token_session`),
  KEY `idx_expire` (`expire_a`),
  KEY `idx_utilisateur` (`utilisateur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions_actives`
--

INSERT INTO `sessions_actives` (`id_session`, `utilisateur_id`, `token_session`, `ip_adresse`, `user_agent`, `derniere_activite`, `expire_a`, `created_at`) VALUES
(1, 1, 'admin_token_abc123def456ghi789jkl012mno345pqr678stu901vwx234yz567abc890', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-01-16 11:56:13', '2026-01-16 19:56:13', '2026-01-16 11:56:13'),
(2, 30, 'scolarite_token_def456ghi789jkl012mno345pqr678stu901vwx234yz567abc890abc', '192.168.1.101', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2026-01-16 11:56:13', '2026-01-16 19:56:13', '2026-01-16 11:56:13'),
(3, 100, 'etudiant_token_ghi789jkl012mno345pqr678stu901vwx234yz567abc890abcdef456', '10.0.0.50', 'Mozilla/5.0 (Linux; Android 12)', '2026-01-16 11:56:13', '2026-01-16 19:56:13', '2026-01-16 11:56:13'),
(7, 1, '4c1258ae4f427a852a3899d685216584ae586978ac860b4db28b0b0e5a794c80922e26f652db5ee7a4cf1f6118a059f6453998f8c1bf8226cf3a926755486777', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-16 10:46:13', '2026-01-16 18:13:04', '2026-01-16 10:13:04');

-- --------------------------------------------------------

--
-- Structure de la table `sessions_commission`
--

DROP TABLE IF EXISTS `sessions_commission`;
CREATE TABLE IF NOT EXISTS `sessions_commission` (
  `id_session` int NOT NULL AUTO_INCREMENT,
  `date_session` datetime NOT NULL,
  `lieu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` enum('Planifiee','En_cours','Terminee','Annulee') COLLATE utf8mb4_unicode_ci DEFAULT 'Planifiee',
  `tour_vote` int DEFAULT '1',
  `pv_genere` tinyint(1) DEFAULT '0',
  `pv_chemin` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_session`),
  KEY `idx_date` (`date_session`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions_commission`
--

INSERT INTO `sessions_commission` (`id_session`, `date_session`, `lieu`, `statut`, `tour_vote`, `pv_genere`, `pv_chemin`, `created_at`) VALUES
(1, '2024-10-28 09:00:00', 'Salle de conférence UFR MI', 'Terminee', 1, 1, 'storage/pv/2024/pv_session_001.pdf', '2026-01-16 10:27:05'),
(2, '2024-11-04 09:00:00', 'Salle de conférence UFR MI', 'Terminee', 1, 1, 'storage/pv/2024/pv_session_002.pdf', '2026-01-16 10:27:05'),
(3, '2024-11-11 09:00:00', 'Salle de conférence UFR MI', 'Terminee', 2, 1, 'storage/pv/2024/pv_session_003.pdf', '2026-01-16 10:27:05'),
(4, '2024-11-18 09:00:00', 'Salle de conférence UFR MI', 'En_cours', 1, 0, NULL, '2026-01-16 10:27:05'),
(5, '2024-11-25 09:00:00', 'Salle de conférence UFR MI', 'Planifiee', 1, 0, NULL, '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `sessions_commission_absences`
--

DROP TABLE IF EXISTS `sessions_commission_absences`;
CREATE TABLE IF NOT EXISTS `sessions_commission_absences` (
  `id_absence` int NOT NULL AUTO_INCREMENT,
  `session_id` int NOT NULL,
  `membre_absent_id` int NOT NULL,
  `remplacant_id` int DEFAULT NULL,
  `type_absence` enum('justifiee','non_justifiee','excuse') COLLATE utf8mb4_unicode_ci NOT NULL,
  `motif` text COLLATE utf8mb4_unicode_ci,
  `document_justificatif` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approuve_par` int DEFAULT NULL,
  `date_approbation` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_absence`),
  KEY `remplacant_id` (`remplacant_id`),
  KEY `approuve_par` (`approuve_par`),
  KEY `idx_session` (`session_id`),
  KEY `idx_membre` (`membre_absent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sessions_commission_agendas`
--

DROP TABLE IF EXISTS `sessions_commission_agendas`;
CREATE TABLE IF NOT EXISTS `sessions_commission_agendas` (
  `id_agenda_item` int NOT NULL AUTO_INCREMENT,
  `session_id` int NOT NULL,
  `ordre` int NOT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type_item` enum('presentation','deliberation','vote','information','divers') COLLATE utf8mb4_unicode_ci NOT NULL,
  `dossier_id` int DEFAULT NULL COMMENT 'Si item lié à un dossier',
  `duree_estimee` int DEFAULT NULL COMMENT 'Durée en minutes',
  `heure_prevue` time DEFAULT NULL,
  `heure_effective` time DEFAULT NULL,
  `rapporteur_id` int DEFAULT NULL,
  `statut` enum('en_attente','en_cours','termine','reporte') COLLATE utf8mb4_unicode_ci DEFAULT 'en_attente',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_agenda_item`),
  KEY `rapporteur_id` (`rapporteur_id`),
  KEY `idx_session` (`session_id`),
  KEY `idx_ordre` (`ordre`),
  KEY `idx_dossier` (`dossier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sessions_commission_convocations`
--

DROP TABLE IF EXISTS `sessions_commission_convocations`;
CREATE TABLE IF NOT EXISTS `sessions_commission_convocations` (
  `id_convocation` int NOT NULL AUTO_INCREMENT,
  `session_id` int NOT NULL,
  `membre_id` int NOT NULL,
  `date_envoi` datetime NOT NULL,
  `methode_envoi` enum('email','sms','notification','courrier') COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut_lecture` enum('non_lu','lu','accuse_reception') COLLATE utf8mb4_unicode_ci DEFAULT 'non_lu',
  `date_lecture` datetime DEFAULT NULL,
  `confirmation_presence` enum('en_attente','present','absent','excuse') COLLATE utf8mb4_unicode_ci DEFAULT 'en_attente',
  `date_confirmation` datetime DEFAULT NULL,
  `motif_absence` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_convocation`),
  UNIQUE KEY `uk_session_membre` (`session_id`,`membre_id`),
  KEY `idx_session` (`session_id`),
  KEY `idx_membre` (`membre_id`),
  KEY `idx_statut` (`statut_lecture`),
  KEY `idx_confirmation` (`confirmation_presence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sessions_commission_documents`
--

DROP TABLE IF EXISTS `sessions_commission_documents`;
CREATE TABLE IF NOT EXISTS `sessions_commission_documents` (
  `id_document_session` int NOT NULL AUTO_INCREMENT,
  `session_id` int NOT NULL,
  `agenda_item_id` int DEFAULT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_document` enum('convocation','support','presentation','compte_rendu','annexe') COLLATE utf8mb4_unicode_ci NOT NULL,
  `chemin_fichier` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taille_octets` bigint DEFAULT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upload_par` int NOT NULL,
  `confidentialite` enum('public','restreint','confidentiel') COLLATE utf8mb4_unicode_ci DEFAULT 'restreint',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_document_session`),
  KEY `agenda_item_id` (`agenda_item_id`),
  KEY `upload_par` (`upload_par`),
  KEY `idx_session` (`session_id`),
  KEY `idx_type` (`type_document`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sessions_commission_votes`
--

DROP TABLE IF EXISTS `sessions_commission_votes`;
CREATE TABLE IF NOT EXISTS `sessions_commission_votes` (
  `id_vote` int NOT NULL AUTO_INCREMENT,
  `session_id` int NOT NULL,
  `agenda_item_id` int DEFAULT NULL,
  `objet_vote` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_vote` enum('simple','secret','nominal') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nb_pour` int DEFAULT '0',
  `nb_contre` int DEFAULT '0',
  `nb_abstention` int DEFAULT '0',
  `nb_presents` int NOT NULL,
  `quorum_requis` int DEFAULT NULL,
  `quorum_atteint` tinyint(1) DEFAULT '1',
  `resultat` enum('adopte','rejete','ajourne','invalide') COLLATE utf8mb4_unicode_ci NOT NULL,
  `details_vote` json DEFAULT NULL COMMENT 'Détails si vote nominal',
  `date_vote` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_vote`),
  KEY `agenda_item_id` (`agenda_item_id`),
  KEY `idx_session` (`session_id`),
  KEY `idx_resultat` (`resultat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sessions_enregistrements`
--

DROP TABLE IF EXISTS `sessions_enregistrements`;
CREATE TABLE IF NOT EXISTS `sessions_enregistrements` (
  `id_enregistrement` int NOT NULL AUTO_INCREMENT,
  `session_id` int NOT NULL,
  `type_media` enum('audio','video','screen_capture') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chemin_fichier` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duree_secondes` int DEFAULT NULL,
  `taille_octets` bigint DEFAULT NULL,
  `format` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qualite` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `confidentialite` enum('public','membres_only','confidentiel') COLLATE utf8mb4_unicode_ci DEFAULT 'membres_only',
  `transcription_disponible` tinyint(1) DEFAULT '0',
  `chemin_transcription` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enregistre_par` int DEFAULT NULL,
  `date_enregistrement` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_enregistrement`),
  KEY `enregistre_par` (`enregistre_par`),
  KEY `idx_session` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `soutenances`
--

DROP TABLE IF EXISTS `soutenances`;
CREATE TABLE IF NOT EXISTS `soutenances` (
  `id_soutenance` int NOT NULL AUTO_INCREMENT,
  `dossier_id` int NOT NULL,
  `date_soutenance` datetime NOT NULL,
  `lieu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salle_id` int DEFAULT NULL,
  `duree_minutes` int DEFAULT '60',
  `statut` enum('Planifiee','En_cours','Terminee','Annulee','Reportee') COLLATE utf8mb4_unicode_ci DEFAULT 'Planifiee',
  `pv_genere` tinyint(1) DEFAULT '0',
  `pv_chemin` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_soutenance`),
  KEY `idx_dossier` (`dossier_id`),
  KEY `idx_date` (`date_soutenance`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `soutenances`
--

INSERT INTO `soutenances` (`id_soutenance`, `dossier_id`, `date_soutenance`, `lieu`, `salle_id`, `duree_minutes`, `statut`, `pv_genere`, `pv_chemin`) VALUES
(1, 1, '2024-12-10 09:00:00', 'Salle A102', 2, 60, 'Terminee', 1, 'storage/pv_soutenance/2024/pv_soutenance_001.pdf'),
(2, 2, '2024-12-12 14:00:00', 'Salle A101', 1, 60, 'Terminee', 1, 'storage/pv_soutenance/2024/pv_soutenance_002.pdf'),
(3, 3, '2024-12-20 10:00:00', 'Amphithéâtre 1', 3, 60, 'Planifiee', 0, NULL),
(4, 4, '2024-12-22 09:00:00', 'Salle A102', 2, 60, 'Planifiee', 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `specialites`
--

DROP TABLE IF EXISTS `specialites`;
CREATE TABLE IF NOT EXISTS `specialites` (
  `id_specialite` int NOT NULL AUTO_INCREMENT,
  `lib_specialite` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `actif` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_specialite`),
  UNIQUE KEY `lib_specialite` (`lib_specialite`),
  KEY `idx_actif` (`actif`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `specialites`
--

INSERT INTO `specialites` (`id_specialite`, `lib_specialite`, `description`, `actif`) VALUES
(1, 'Informatique', 'Conception et développement de logiciels', 1),
(2, 'MIAGE', 'Administration et conception de bases de données', 1),
(3, 'Mathématiques', 'Infrastructure réseau et systèmes', 1),
(4, 'Statistiques', 'IA et Machine Learning', 1),
(5, 'Recherche Opérationnelle', 'Cybersécurité et protection des systèmes', 1);

-- --------------------------------------------------------

--
-- Structure de la table `stats_cache`
--

DROP TABLE IF EXISTS `stats_cache`;
CREATE TABLE IF NOT EXISTS `stats_cache` (
  `id_stat` int NOT NULL AUTO_INCREMENT,
  `cle_stat` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valeur_json` json NOT NULL,
  `expire_le` datetime NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_stat`),
  UNIQUE KEY `cle_stat` (`cle_stat`),
  KEY `idx_cle` (`cle_stat`),
  KEY `idx_expire` (`expire_le`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `stats_cache`
--

INSERT INTO `stats_cache` (`id_stat`, `cle_stat`, `valeur_json`, `expire_le`, `created_at`) VALUES
(1, 'dashboard_admin_global', '{\"total_etudiants\": 40, \"dossiers_en_cours\": 25, \"total_enseignants\": 30, \"soutenances_ce_mois\": 5}', '2026-01-16 10:42:05', '2026-01-16 10:27:05'),
(2, 'stats_workflow_etats', '{\"inscrit\": 5, \"en_evaluation\": 3, \"rapport_valide\": 8, \"diplome_delivre\": 1, \"candidature_soumise\": 2, \"soutenance_planifiee\": 2}', '2026-01-16 10:42:05', '2026-01-16 10:27:05'),
(3, 'stats_financieres', '{\"soldes_dus\": 800000, \"total_encaisse\": 18700000, \"total_penalites\": 120000}', '2026-01-16 10:57:05', '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `stats_dashboards`
--

DROP TABLE IF EXISTS `stats_dashboards`;
CREATE TABLE IF NOT EXISTS `stats_dashboards` (
  `id_dashboard` int NOT NULL AUTO_INCREMENT,
  `nom_dashboard` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `role_access` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `widgets_config` json NOT NULL,
  `layout_config` json DEFAULT NULL,
  `refresh_interval` int DEFAULT '300' COMMENT 'Secondes',
  `actif` tinyint(1) DEFAULT '1',
  `ordre_affichage` int DEFAULT '0',
  `created_by` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_dashboard`),
  UNIQUE KEY `nom_dashboard` (`nom_dashboard`),
  KEY `created_by` (`created_by`),
  KEY `idx_role` (`role_access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `stats_widgets`
--

DROP TABLE IF EXISTS `stats_widgets`;
CREATE TABLE IF NOT EXISTS `stats_widgets` (
  `id_widget` int NOT NULL AUTO_INCREMENT,
  `code_widget` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_widget` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type_widget` enum('chart','table','counter','gauge','map','timeline') COLLATE utf8mb4_unicode_ci NOT NULL,
  `query_sql` text COLLATE utf8mb4_unicode_ci,
  `transformation_json` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Fonction JS de transformation',
  `parametres_defaut` json DEFAULT NULL,
  `cache_duration` int DEFAULT '300',
  `categorie` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_widget`),
  UNIQUE KEY `code_widget` (`code_widget`),
  KEY `idx_type` (`type_widget`),
  KEY `idx_categorie` (`categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `statut_jury`
--

DROP TABLE IF EXISTS `statut_jury`;
CREATE TABLE IF NOT EXISTS `statut_jury` (
  `id_statut` int NOT NULL AUTO_INCREMENT,
  `lib_statut` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_statut`),
  UNIQUE KEY `lib_statut` (`lib_statut`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `statut_jury`
--

INSERT INTO `statut_jury` (`id_statut`, `lib_statut`, `description`) VALUES
(1, 'En constitution', 'Jury en cours de formation'),
(2, 'Complet', 'Jury complet et validé'),
(3, 'Actif', 'Jury prêt pour la soutenance'),
(4, 'Terminé', 'Jury ayant terminé sa mission');

-- --------------------------------------------------------

--
-- Structure de la table `systeme_messages`
--

DROP TABLE IF EXISTS `systeme_messages`;
CREATE TABLE IF NOT EXISTS `systeme_messages` (
  `id_message_systeme` int NOT NULL AUTO_INCREMENT,
  `type_message` enum('info','warning','error','success','maintenance') COLLATE utf8mb4_unicode_ci NOT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contenu` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `cible` enum('tous','groupe','role','utilisateur_specifique') COLLATE utf8mb4_unicode_ci DEFAULT 'tous',
  `cible_ids` json DEFAULT NULL COMMENT 'IDs des groupes/roles/utilisateurs ciblés',
  `priorite` enum('basse','normale','haute','urgente') COLLATE utf8mb4_unicode_ci DEFAULT 'normale',
  `affichage` enum('banner','popup','toast','notification') COLLATE utf8mb4_unicode_ci DEFAULT 'banner',
  `date_debut` datetime NOT NULL,
  `date_fin` datetime DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `cree_par` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_message_systeme`),
  KEY `cree_par` (`cree_par`),
  KEY `idx_dates` (`date_debut`,`date_fin`),
  KEY `idx_actif` (`actif`),
  KEY `idx_type` (`type_message`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `systeme_messages_lectures`
--

DROP TABLE IF EXISTS `systeme_messages_lectures`;
CREATE TABLE IF NOT EXISTS `systeme_messages_lectures` (
  `id_lecture` int NOT NULL AUTO_INCREMENT,
  `message_systeme_id` int NOT NULL,
  `utilisateur_id` int NOT NULL,
  `date_lecture` datetime DEFAULT CURRENT_TIMESTAMP,
  `accuse_reception` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_lecture`),
  UNIQUE KEY `uk_message_utilisateur` (`message_systeme_id`,`utilisateur_id`),
  KEY `idx_message` (`message_systeme_id`),
  KEY `idx_utilisateur` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `traitement`
--

DROP TABLE IF EXISTS `traitement`;
CREATE TABLE IF NOT EXISTS `traitement` (
  `id_traitement` int NOT NULL AUTO_INCREMENT,
  `lib_traitement` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ordre_traitement` int DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_traitement`),
  UNIQUE KEY `lib_traitement` (`lib_traitement`),
  KEY `idx_ordre` (`ordre_traitement`),
  KEY `idx_actif` (`actif`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `traitement`
--

INSERT INTO `traitement` (`id_traitement`, `lib_traitement`, `description`, `ordre_traitement`, `actif`) VALUES
(1, 'Gestion Utilisateurs', 'CRUD des comptes utilisateurs (module: authentification)', 1, 1),
(2, 'Gestion Sessions', 'Surveillance des sessions actives (module: authentification)', 2, 1),
(3, 'Gestion Groupes', 'Administration des groupes et rôles (module: authentification)', 3, 1),
(4, 'Gestion Permissions', 'Configuration des droits d\'accès (module: authentification)', 4, 1),
(5, 'Gestion Étudiants', 'Gestion des dossiers étudiants (module: entites_academiques)', 5, 1),
(6, 'Gestion Enseignants', 'Gestion des enseignants (module: entites_academiques)', 6, 1),
(7, 'Gestion Personnel', 'Gestion du personnel administratif (module: entites_academiques)', 7, 1),
(8, 'Gestion Entreprises', 'Référentiel des entreprises partenaires (module: entites_academiques)', 8, 1),
(9, 'Gestion Années Académiques', 'Configuration des années académiques (module: entites_academiques)', 9, 1),
(10, 'Gestion UE/ECUE', 'Gestion des unités d\'enseignement (module: entites_academiques)', 10, 1),
(11, 'Gestion Workflow', 'Configuration états et transitions (module: workflow)', 11, 1),
(12, 'Gestion Dossiers', 'Suivi des dossiers étudiants (module: workflow)', 12, 1),
(13, 'Gestion Candidatures', 'Traitement des candidatures (module: workflow)', 13, 1),
(14, 'Gestion Sessions Commission', 'Planification et gestion des sessions (module: commission)', 14, 1),
(15, 'Évaluation Rapports', 'Évaluation et vote sur les rapports (module: commission)', 15, 1),
(16, 'Gestion Jurys', 'Constitution des jurys (module: soutenance)', 16, 1),
(17, 'Planification Soutenances', 'Planification des soutenances (module: soutenance)', 17, 1),
(18, 'Gestion Notes', 'Saisie et consultation des notes (module: soutenance)', 18, 1),
(19, 'Gestion Paiements', 'Enregistrement des paiements (module: financier)', 19, 1),
(20, 'Gestion Pénalités', 'Gestion des pénalités de retard (module: financier)', 20, 1),
(21, 'Gestion Exonérations', 'Gestion des exonérations (module: financier)', 21, 1),
(22, 'Gestion Notifications', 'Envoi et suivi des notifications (module: communication)', 22, 1),
(23, 'Messagerie Interne', 'Messages entre utilisateurs (module: communication)', 23, 1),
(24, 'Gestion Calendrier', 'Calendrier académique (module: communication)', 24, 1),
(25, 'Génération Documents', 'Génération de PDFs et documents (module: documents)', 25, 1),
(26, 'Gestion Archives', 'Archivage et intégrité (module: documents)', 26, 1),
(27, 'Configuration Système', 'Paramètres système (module: administration)', 27, 1),
(28, 'Consultation Audit', 'Logs et traçabilité (module: administration)', 28, 1),
(29, 'Gestion Réclamations', 'Traitement des réclamations (module: administration)', 29, 1),
(30, 'Maintenance', 'Opérations de maintenance (module: administration)', 30, 1);

-- --------------------------------------------------------

--
-- Structure de la table `type_utilisateur`
--

DROP TABLE IF EXISTS `type_utilisateur`;
CREATE TABLE IF NOT EXISTS `type_utilisateur` (
  `id_type_utilisateur` int NOT NULL AUTO_INCREMENT,
  `lib_type_utilisateur` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_type_utilisateur`),
  UNIQUE KEY `lib_type_utilisateur` (`lib_type_utilisateur`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `type_utilisateur`
--

INSERT INTO `type_utilisateur` (`id_type_utilisateur`, `lib_type_utilisateur`, `description`) VALUES
(1, 'Administrateur', 'Administrateur système avec contrôle total'),
(2, 'Personnel Administratif', 'Personnel administratif (Secrétaire, Scolarité, Communication)'),
(3, 'Enseignant', 'Enseignant (Commission, Jury, Encadreur)'),
(4, 'Étudiant', 'Étudiant inscrit au programme');

-- --------------------------------------------------------

--
-- Structure de la table `ue`
--

DROP TABLE IF EXISTS `ue`;
CREATE TABLE IF NOT EXISTS `ue` (
  `id_ue` int NOT NULL AUTO_INCREMENT,
  `code_ue` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lib_ue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `credits` int DEFAULT NULL,
  `niveau_id` int DEFAULT NULL,
  `semestre_id` int DEFAULT NULL,
  PRIMARY KEY (`id_ue`),
  UNIQUE KEY `code_ue` (`code_ue`),
  KEY `semestre_id` (`semestre_id`),
  KEY `idx_code` (`code_ue`),
  KEY `idx_niveau` (`niveau_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ue`
--

INSERT INTO `ue` (`id_ue`, `code_ue`, `lib_ue`, `credits`, `niveau_id`, `semestre_id`) VALUES
(1, 'UE-M1S1-01', 'Conception et Programmation Orientée Objet', 6, 4, 1),
(2, 'UE-M1S1-02', 'Bases de Données Avancées', 6, 4, 1),
(3, 'UE-M1S1-03', 'Réseaux et Sécurité', 5, 4, 1),
(4, 'UE-M1S1-04', 'Mathématiques pour l\'Informatique', 5, 4, 1),
(5, 'UE-M1S1-05', 'Gestion de Projet', 4, 4, 1),
(6, 'UE-M1S1-06', 'Anglais Professionnel', 4, 4, 1),
(7, 'UE-M1S2-01', 'Architecture Logicielle', 6, 4, 2),
(8, 'UE-M1S2-02', 'Intelligence Artificielle et Machine Learning', 6, 4, 2),
(9, 'UE-M1S2-03', 'Systèmes d\'Information', 5, 4, 2),
(10, 'UE-M1S2-04', 'Économie Numérique', 5, 4, 2),
(11, 'UE-M1S2-05', 'Stage M1', 8, 4, 2),
(12, 'UE-M2S1-01', 'Visualisation et Analyse de Données', 6, 5, 1),
(13, 'UE-M2S1-02', 'Cloud Computing et DevOps', 6, 5, 1),
(14, 'UE-M2S1-03', 'Gouvernance des SI', 5, 5, 1),
(15, 'UE-M2S1-04', 'Entrepreneuriat et Innovation', 5, 5, 1),
(16, 'UE-M2S1-05', 'Audit et Sécurité des SI', 4, 5, 1),
(17, 'UE-M2S1-06', 'Méthodologie de Recherche', 4, 5, 1),
(18, 'UE-M2S2-01', 'Stage et Mémoire de Fin d\'Études', 30, 5, 2);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id_utilisateur` int NOT NULL AUTO_INCREMENT,
  `nom_utilisateur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_utilisateur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mdp_utilisateur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_type_utilisateur` int NOT NULL,
  `id_GU` int NOT NULL,
  `id_niv_acces_donnee` int DEFAULT NULL,
  `statut_utilisateur` enum('Actif','Inactif','Suspendu') COLLATE utf8mb4_unicode_ci DEFAULT 'Actif',
  `doit_changer_mdp` tinyint(1) DEFAULT '1',
  `derniere_connexion` datetime DEFAULT NULL,
  `tentatives_echec` int DEFAULT '0',
  `verrouille_jusqu_a` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `login_utilisateur` (`login_utilisateur`),
  KEY `idx_login` (`login_utilisateur`),
  KEY `idx_statut` (`statut_utilisateur`),
  KEY `idx_type` (`id_type_utilisateur`),
  KEY `idx_groupe` (`id_GU`),
  KEY `idx_utilisateurs_actifs` (`statut_utilisateur`,`login_utilisateur`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_utilisateur`, `nom_utilisateur`, `login_utilisateur`, `mdp_utilisateur`, `id_type_utilisateur`, `id_GU`, `id_niv_acces_donnee`, `statut_utilisateur`, `doit_changer_mdp`, `derniere_connexion`, `tentatives_echec`, `verrouille_jusqu_a`, `created_at`, `updated_at`) VALUES
(1, 'Administrateur Système', 'admin@checkmaster.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$c1pNbndsWE1JNU9YUmVSOA$hMyeWEc8LmKyNoJ5Elh6EkNKlwe11A1Ww+wUbjrpd9k', 1, 1, NULL, 'Actif', 1, '2026-01-16 10:13:04', 0, NULL, '2026-01-15 13:45:18', '2026-01-16 10:13:04'),
(2, 'KOUAME Amani Albert', 'kouame.amani@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 1, 1, 4, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(10, 'N\'GUESSAN Marie', 'nguessan.marie@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 2, 2, 2, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(11, 'KOFFI Adjoua', 'koffi.adjoua@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 2, 2, 2, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(20, 'KOUASSI Estelle', 'kouassi.estelle@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 2, 3, 2, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(21, 'DIALLO Aissatou', 'diallo.aissatou.admin@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 2, 3, 2, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(30, 'DOSSO Aminata', 'dosso.aminata@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 2, 4, 3, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(31, 'TRAORE Mamadou', 'traore.mamadou.admin@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 2, 4, 3, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(32, 'COULIBALY Fatoumata', 'coulibaly.fatoumata.admin@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 2, 4, 3, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(40, 'Prof. DIALLO Mamadou', 'diallo.mamadou@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 5, 3, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(50, 'Dr. YAO Konan Pierre', 'yao.konan@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 6, 3, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(60, 'Dr. KOUASSI Aya Marie', 'kouassi.aya@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 7, 2, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(61, 'Dr. DIABATE Fatoumata', 'diabate.fatoumata@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 7, 2, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(62, 'Dr. N\'GUESSAN Ahou Christelle', 'nguessan.ahou@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 7, 2, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(63, 'Dr. COULIBALY Abdoulaye', 'coulibaly.abdoulaye@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 7, 2, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(64, 'Dr. DOSSO Mohamed', 'dosso.mohamed@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 7, 2, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(70, 'Dr. SANOGO Mariam', 'sanogo.mariam@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 8, 2, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(71, 'M. GBAGBO Eric', 'gbagbo.eric@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 8, 2, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(72, 'Mme SORO Aminata', 'soro.aminata@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 8, 2, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(73, 'M. TOURE Issouf', 'toure.issouf@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 8, 2, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(74, 'Mme KONAN Sylvie', 'konan.sylvie@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 8, 2, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(75, 'M. FOFANA Bakary', 'fofana.bakary@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 8, 2, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(80, 'Prof. KOFFI Kouamé Jean (Président Commission)', 'koffi.kouame@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 10, 3, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(100, 'KONE Adama', 'kone.adama@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(101, 'SANGARE Fatou', 'sangare.fatou@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(102, 'BROU Jean-Pierre', 'brou.jeanpierre@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(103, 'ASSI Marie-Claire', 'assi.marieclaire@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(104, 'KONAN Yves', 'konan.yves@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(105, 'OUATTARA Mariam', 'ouattara.mariam@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(106, 'ZADI Emmanuel', 'zadi.emmanuel@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(107, 'AKA Cynthia', 'aka.cynthia@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(108, 'GNAMBA Patrick', 'gnamba.patrick@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(109, 'N\'DRI Adjoua', 'ndri.adjoua@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(110, 'LAGO Constant', 'lago.constant@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(111, 'EHUI Sandrine', 'ehui.sandrine@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(112, 'TAPE Didier', 'tape.didier@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(113, 'GBADJE Félicité', 'gbadje.felicite@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(114, 'YAPI Serge', 'yapi.serge@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(115, 'DAGO Estelle', 'dago.estelle@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(116, 'ASSEMIAN Rodrigue', 'assemian.rodrigue@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(117, 'ANOH Prisca', 'anoh.prisca@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(118, 'GNAGNE Martial', 'gnagne.martial@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05'),
(119, 'AMON Esther', 'amon.esther@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', 1, NULL, 0, NULL, '2026-01-16 10:27:05', '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs_groupes`
--

DROP TABLE IF EXISTS `utilisateurs_groupes`;
CREATE TABLE IF NOT EXISTS `utilisateurs_groupes` (
  `utilisateur_id` int NOT NULL,
  `groupe_id` int NOT NULL,
  `attribue_par` int DEFAULT NULL,
  `attribue_le` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`utilisateur_id`,`groupe_id`),
  KEY `attribue_par` (`attribue_par`),
  KEY `idx_groupe` (`groupe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs_groupes`
--

INSERT INTO `utilisateurs_groupes` (`utilisateur_id`, `groupe_id`, `attribue_par`, `attribue_le`) VALUES
(1, 1, 1, '2026-01-16 10:27:05'),
(2, 1, 1, '2026-01-16 10:27:05'),
(10, 2, 1, '2026-01-16 10:27:05'),
(11, 2, 1, '2026-01-16 10:27:05'),
(20, 3, 1, '2026-01-16 10:27:05'),
(21, 3, 1, '2026-01-16 10:27:05'),
(30, 4, 1, '2026-01-16 10:27:05'),
(31, 4, 1, '2026-01-16 10:27:05'),
(32, 4, 1, '2026-01-16 10:27:05'),
(40, 5, 1, '2026-01-16 10:27:05'),
(50, 6, 1, '2026-01-16 10:27:05'),
(60, 7, 1, '2026-01-16 10:27:05'),
(61, 7, 1, '2026-01-16 10:27:05'),
(62, 7, 1, '2026-01-16 10:27:05'),
(63, 7, 1, '2026-01-16 10:27:05'),
(64, 7, 1, '2026-01-16 10:27:05'),
(70, 8, 1, '2026-01-16 10:27:05'),
(71, 8, 1, '2026-01-16 10:27:05'),
(72, 8, 1, '2026-01-16 10:27:05'),
(73, 8, 1, '2026-01-16 10:27:05'),
(74, 8, 1, '2026-01-16 10:27:05'),
(75, 8, 1, '2026-01-16 10:27:05'),
(80, 10, 1, '2026-01-16 10:27:05'),
(100, 9, 1, '2026-01-16 10:27:05'),
(101, 9, 1, '2026-01-16 10:27:05'),
(102, 9, 1, '2026-01-16 10:27:05'),
(103, 9, 1, '2026-01-16 10:27:05'),
(104, 9, 1, '2026-01-16 10:27:05'),
(105, 9, 1, '2026-01-16 10:27:05'),
(106, 9, 1, '2026-01-16 10:27:05'),
(107, 9, 1, '2026-01-16 10:27:05'),
(108, 9, 1, '2026-01-16 10:27:05'),
(109, 9, 1, '2026-01-16 10:27:05'),
(110, 9, 1, '2026-01-16 10:27:05'),
(111, 9, 1, '2026-01-16 10:27:05'),
(112, 9, 1, '2026-01-16 10:27:05'),
(113, 9, 1, '2026-01-16 10:27:05'),
(114, 9, 1, '2026-01-16 10:27:05'),
(115, 9, 1, '2026-01-16 10:27:05'),
(116, 9, 1, '2026-01-16 10:27:05'),
(117, 9, 1, '2026-01-16 10:27:05'),
(118, 9, 1, '2026-01-16 10:27:05'),
(119, 9, 1, '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `votes_commission`
--

DROP TABLE IF EXISTS `votes_commission`;
CREATE TABLE IF NOT EXISTS `votes_commission` (
  `id_vote` int NOT NULL AUTO_INCREMENT,
  `session_id` int NOT NULL,
  `rapport_id` int NOT NULL,
  `membre_id` int NOT NULL,
  `tour` int NOT NULL,
  `decision` enum('Valider','A_revoir','Rejeter') COLLATE utf8mb4_unicode_ci NOT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_vote`),
  UNIQUE KEY `unique_vote` (`session_id`,`rapport_id`,`membre_id`,`tour`),
  KEY `membre_id` (`membre_id`),
  KEY `idx_session` (`session_id`),
  KEY `idx_rapport` (`rapport_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `votes_commission`
--

INSERT INTO `votes_commission` (`id_vote`, `session_id`, `rapport_id`, `membre_id`, `tour`, `decision`, `commentaire`, `created_at`) VALUES
(1, 1, 1, 6, 1, 'Valider', 'Excellent travail, méthodologie solide', '2026-01-16 10:27:05'),
(2, 1, 1, 7, 1, 'Valider', 'Rapport bien structuré', '2026-01-16 10:27:05'),
(3, 1, 1, 8, 1, 'Valider', 'Analyse pertinente', '2026-01-16 10:27:05'),
(4, 1, 1, 10, 1, 'Valider', 'Contribution significative', '2026-01-16 10:27:05'),
(5, 1, 1, 11, 1, 'Valider', 'Recommandé pour soutenance', '2026-01-16 10:27:05'),
(6, 1, 2, 6, 1, 'Valider', 'Très bon travail', '2026-01-16 10:27:05'),
(7, 1, 2, 7, 1, 'Valider', 'Sujet innovant', '2026-01-16 10:27:05'),
(8, 1, 2, 8, 1, 'Valider', 'Bien documenté', '2026-01-16 10:27:05'),
(9, 1, 2, 10, 1, 'Valider', 'Approche intéressante', '2026-01-16 10:27:05'),
(10, 1, 2, 11, 1, 'Valider', 'Prêt pour soutenance', '2026-01-16 10:27:05'),
(11, 2, 3, 6, 1, 'Valider', 'Travail complet', '2026-01-16 10:27:05'),
(12, 2, 3, 7, 1, 'Valider', 'Bonne maîtrise technique', '2026-01-16 10:27:05'),
(13, 2, 3, 8, 1, 'Valider', 'Résultats probants', '2026-01-16 10:27:05'),
(14, 2, 3, 10, 1, 'Valider', 'Innovation technologique', '2026-01-16 10:27:05'),
(15, 2, 3, 11, 1, 'Valider', 'Excellent', '2026-01-16 10:27:05'),
(16, 2, 4, 6, 1, 'Valider', 'NLP bien maîtrisé', '2026-01-16 10:27:05'),
(17, 2, 4, 7, 1, 'Valider', 'Application pratique', '2026-01-16 10:27:05'),
(18, 2, 4, 8, 1, 'Valider', 'Méthodologie rigoureuse', '2026-01-16 10:27:05'),
(19, 2, 4, 10, 1, 'Valider', 'Impact business clair', '2026-01-16 10:27:05'),
(20, 2, 4, 11, 1, 'Valider', 'Prêt', '2026-01-16 10:27:05'),
(21, 3, 5, 6, 1, 'Valider', 'Bien', '2026-01-16 10:27:05'),
(22, 3, 5, 7, 1, 'A_revoir', 'Quelques corrections mineures', '2026-01-16 10:27:05'),
(23, 3, 5, 8, 1, 'Valider', 'OK', '2026-01-16 10:27:05'),
(24, 3, 5, 10, 1, 'Valider', 'Bon travail', '2026-01-16 10:27:05'),
(25, 3, 5, 11, 1, 'A_revoir', 'Préciser la méthodologie', '2026-01-16 10:27:05'),
(26, 3, 5, 6, 2, 'Valider', 'Corrections apportées', '2026-01-16 10:27:05'),
(27, 3, 5, 7, 2, 'Valider', 'OK maintenant', '2026-01-16 10:27:05'),
(28, 3, 5, 8, 2, 'Valider', 'Validé', '2026-01-16 10:27:05'),
(29, 3, 5, 10, 2, 'Valider', 'Approuvé', '2026-01-16 10:27:05'),
(30, 3, 5, 11, 2, 'Valider', 'Méthodologie clarifiée', '2026-01-16 10:27:05'),
(31, 3, 6, 6, 1, 'Valider', 'Blockchain innovant', '2026-01-16 10:27:05'),
(32, 3, 6, 7, 1, 'Valider', 'Approche sécuritaire solide', '2026-01-16 10:27:05'),
(33, 3, 6, 8, 1, 'Valider', 'Très pertinent', '2026-01-16 10:27:05'),
(34, 3, 6, 10, 1, 'Valider', 'Recommandé', '2026-01-16 10:27:05'),
(35, 3, 6, 11, 1, 'Valider', 'Excellent', '2026-01-16 10:27:05'),
(36, 3, 7, 6, 1, 'Valider', 'Analyse marketing bien faite', '2026-01-16 10:27:05'),
(37, 3, 7, 7, 1, 'Valider', 'Data analysis rigoureuse', '2026-01-16 10:27:05'),
(38, 3, 7, 8, 1, 'Valider', 'Bon travail', '2026-01-16 10:27:05'),
(39, 3, 7, 10, 1, 'Valider', 'Validé', '2026-01-16 10:27:05'),
(40, 3, 7, 11, 1, 'Valider', 'OK', '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `workflow_alertes`
--

DROP TABLE IF EXISTS `workflow_alertes`;
CREATE TABLE IF NOT EXISTS `workflow_alertes` (
  `id_alerte` int NOT NULL AUTO_INCREMENT,
  `dossier_id` int NOT NULL,
  `etat_id` int NOT NULL,
  `type_alerte` enum('50_pourcent','80_pourcent','100_pourcent') COLLATE utf8mb4_unicode_ci NOT NULL,
  `envoyee` tinyint(1) DEFAULT '0',
  `envoyee_le` datetime DEFAULT NULL,
  PRIMARY KEY (`id_alerte`),
  KEY `idx_dossier` (`dossier_id`),
  KEY `idx_envoyee` (`envoyee`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `workflow_alertes`
--

INSERT INTO `workflow_alertes` (`id_alerte`, `dossier_id`, `etat_id`, `type_alerte`, `envoyee`, `envoyee_le`) VALUES
(1, 6, 8, '50_pourcent', 1, '2024-11-27 09:00:00'),
(2, 10, 4, '50_pourcent', 1, '2024-11-06 10:00:00'),
(3, 11, 3, '80_pourcent', 1, '2024-11-04 14:00:00'),
(4, 12, 2, '100_pourcent', 1, '2024-11-04 14:00:00'),
(5, 6, 8, '80_pourcent', 0, NULL),
(6, 18, 8, '50_pourcent', 0, NULL),
(7, 4, 10, '50_pourcent', 0, NULL),
(8, 20, 10, '50_pourcent', 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `workflow_blocages`
--

DROP TABLE IF EXISTS `workflow_blocages`;
CREATE TABLE IF NOT EXISTS `workflow_blocages` (
  `id_blocage` int NOT NULL AUTO_INCREMENT,
  `dossier_id` int NOT NULL,
  `etat_actuel` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `raison_blocage` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_blocage` enum('technique','administratif','validation','document_manquant') COLLATE utf8mb4_unicode_ci NOT NULL,
  `severite` enum('faible','moyenne','haute','bloquant') COLLATE utf8mb4_unicode_ci DEFAULT 'moyenne',
  `detecte_le` datetime DEFAULT CURRENT_TIMESTAMP,
  `resolu_le` datetime DEFAULT NULL,
  `resolu_par` int DEFAULT NULL,
  `actions_entreprises` text COLLATE utf8mb4_unicode_ci,
  `statut` enum('actif','en_cours','resolu') COLLATE utf8mb4_unicode_ci DEFAULT 'actif',
  PRIMARY KEY (`id_blocage`),
  KEY `resolu_par` (`resolu_par`),
  KEY `idx_dossier` (`dossier_id`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `workflow_etats`
--

DROP TABLE IF EXISTS `workflow_etats`;
CREATE TABLE IF NOT EXISTS `workflow_etats` (
  `id_etat` int NOT NULL AUTO_INCREMENT,
  `code_etat` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_etat` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phase` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delai_max_jours` int DEFAULT NULL,
  `ordre_affichage` int DEFAULT NULL,
  `couleur_hex` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_etat`),
  UNIQUE KEY `code_etat` (`code_etat`),
  KEY `idx_code` (`code_etat`),
  KEY `idx_phase` (`phase`),
  KEY `idx_ordre` (`ordre_affichage`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `workflow_etats`
--

INSERT INTO `workflow_etats` (`id_etat`, `code_etat`, `nom_etat`, `phase`, `delai_max_jours`, `ordre_affichage`, `couleur_hex`, `description`) VALUES
(1, 'INSCRIT', 'Inscrit', 'Inscription', NULL, 1, '#6c757d', 'Étudiant inscrit, dossier créé'),
(2, 'CANDIDATURE_SOUMISE', 'Candidature soumise', 'Candidature', 7, 2, '#007bff', 'Candidature déposée, en attente vérification'),
(3, 'VERIFICATION_SCOLARITE', 'Vérification scolarité', 'Candidature', 5, 3, '#17a2b8', 'Vérification paiement et documents par scolarité'),
(4, 'FILTRE_COMMUNICATION', 'Filtre communication', 'Candidature', 3, 4, '#20c997', 'Vérification format rapport par communication'),
(5, 'EN_ATTENTE_COMMISSION', 'En attente commission', 'Commission', NULL, 5, '#ffc107', 'Rapport prêt pour évaluation commission'),
(6, 'EN_EVALUATION_COMMISSION', 'En évaluation commission', 'Commission', 1, 6, '#fd7e14', 'Rapport en cours d\'évaluation'),
(7, 'RAPPORT_VALIDE', 'Rapport validé', 'Commission', NULL, 7, '#28a745', 'Rapport validé par la commission'),
(8, 'ATTENTE_AVIS_ENCADREUR', 'Attente avis encadreur', 'Encadrement', 7, 8, '#6610f2', 'En attente avis encadreur pédagogique'),
(9, 'PRET_POUR_JURY', 'Prêt pour jury', 'Soutenance', NULL, 9, '#e83e8c', 'Dossier prêt pour constitution jury'),
(10, 'JURY_EN_CONSTITUTION', 'Jury en constitution', 'Soutenance', 14, 10, '#6f42c1', 'Jury en cours de constitution'),
(11, 'SOUTENANCE_PLANIFIEE', 'Soutenance planifiée', 'Soutenance', NULL, 11, '#17a2b8', 'Date de soutenance fixée'),
(12, 'SOUTENANCE_EN_COURS', 'Soutenance en cours', 'Soutenance', 1, 12, '#fd7e14', 'Soutenance en cours'),
(13, 'SOUTENANCE_TERMINEE', 'Soutenance terminée', 'Finalisation', NULL, 13, '#28a745', 'Soutenance terminée, notes saisies'),
(14, 'DIPLOME_DELIVRE', 'Diplôme délivré', 'Finalisation', NULL, 14, '#198754', 'Diplôme délivré, dossier archivé');

-- --------------------------------------------------------

--
-- Structure de la table `workflow_historique`
--

DROP TABLE IF EXISTS `workflow_historique`;
CREATE TABLE IF NOT EXISTS `workflow_historique` (
  `id_historique` int NOT NULL AUTO_INCREMENT,
  `dossier_id` int NOT NULL,
  `etat_source_id` int DEFAULT NULL,
  `etat_cible_id` int NOT NULL,
  `transition_id` int DEFAULT NULL,
  `utilisateur_id` int DEFAULT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `snapshot_json` json DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_historique`),
  KEY `etat_source_id` (`etat_source_id`),
  KEY `etat_cible_id` (`etat_cible_id`),
  KEY `transition_id` (`transition_id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `idx_dossier` (`dossier_id`),
  KEY `idx_created` (`created_at`),
  KEY `idx_composite_dossier_date` (`dossier_id`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `workflow_historique`
--

INSERT INTO `workflow_historique` (`id_historique`, `dossier_id`, `etat_source_id`, `etat_cible_id`, `transition_id`, `utilisateur_id`, `commentaire`, `snapshot_json`, `created_at`) VALUES
(1, 1, NULL, 1, NULL, 30, 'Inscription initiale', '{\"date\": \"2024-09-15\", \"etudiant\": \"KONE Adama\"}', '2026-01-16 10:27:05'),
(2, 1, 1, 2, 1, 100, 'Candidature soumise', '{\"theme\": \"Système de gestion de stock avec ML\"}', '2026-01-16 10:27:05'),
(3, 1, 2, 3, 2, 30, 'Candidature validée par scolarité', '{\"paiement\": \"complet\"}', '2026-01-16 10:27:05'),
(4, 1, 3, 4, 4, 30, 'Paiement validé', '{\"montant\": 550000}', '2026-01-16 10:27:05'),
(5, 1, 4, 5, 6, 20, 'Format validé par communication', '{\"pages\": 85, \"format\": \"conforme\"}', '2026-01-16 10:27:05'),
(6, 1, 5, 6, 8, 80, 'Évaluation commission démarrée', '{\"session_id\": 1}', '2026-01-16 10:27:05'),
(7, 1, 6, 7, 9, 80, 'Rapport validé par la commission', '{\"tour\": 1, \"vote\": \"unanime\"}', '2026-01-16 10:27:05'),
(8, 1, 7, 8, 12, 30, 'Demande avis encadreur', '{\"encadreur\": \"Dr. KOUASSI Aya Marie\"}', '2026-01-16 10:27:05'),
(9, 1, 8, 9, 13, 70, 'Avis favorable encadreur', '{\"commentaire\": \"Prêt pour soutenance\"}', '2026-01-16 10:27:05'),
(10, 1, 9, 10, 15, 80, 'Jury en constitution', '{\"president\": \"Prof. KOFFI Kouamé\"}', '2026-01-16 10:27:05'),
(11, 1, 10, 11, 16, 30, 'Soutenance planifiée', '{\"date\": \"2024-12-10\", \"salle\": \"A102\"}', '2026-01-16 10:27:05'),
(12, 1, 11, 12, 18, 80, 'Soutenance démarrée', '{\"code_valide\": true}', '2026-01-16 10:27:05'),
(13, 1, 12, 13, 20, 80, 'Soutenance terminée', '{\"note_moyenne\": 16.83}', '2026-01-16 10:27:05'),
(14, 1, 13, 14, 21, 30, 'Diplôme délivré', '{\"mention\": \"Très Bien\"}', '2026-01-16 10:27:05'),
(15, 2, NULL, 1, NULL, 30, 'Inscription initiale', '{\"etudiant\": \"SANGARE Fatou\"}', '2026-01-16 10:27:05'),
(16, 2, 1, 2, 1, 101, 'Candidature soumise', '{\"theme\": \"Plateforme e-banking sécurisée\"}', '2026-01-16 10:27:05'),
(17, 2, 2, 3, 2, 31, 'Candidature validée', '{}', '2026-01-16 10:27:05'),
(18, 2, 3, 4, 4, 31, 'Paiement validé', '{}', '2026-01-16 10:27:05'),
(19, 2, 4, 5, 6, 20, 'Format validé', '{}', '2026-01-16 10:27:05'),
(20, 2, 5, 6, 8, 80, 'Évaluation démarrée', '{}', '2026-01-16 10:27:05'),
(21, 2, 6, 7, 9, 80, 'Rapport validé', '{}', '2026-01-16 10:27:05'),
(22, 2, 7, 8, 12, 30, 'Demande avis', '{}', '2026-01-16 10:27:05'),
(23, 2, 8, 9, 13, 71, 'Avis favorable', '{}', '2026-01-16 10:27:05'),
(24, 2, 9, 10, 15, 80, 'Jury en constitution', '{}', '2026-01-16 10:27:05'),
(25, 2, 10, 11, 16, 30, 'Soutenance planifiée', '{}', '2026-01-16 10:27:05'),
(26, 2, 11, 12, 18, 80, 'Soutenance démarrée', '{}', '2026-01-16 10:27:05'),
(27, 2, 12, 13, 20, 80, 'Soutenance terminée', '{\"note_moyenne\": 15.03}', '2026-01-16 10:27:05'),
(28, 3, NULL, 1, NULL, 30, 'Inscription initiale', '{\"etudiant\": \"BROU Jean-Pierre\"}', '2026-01-16 10:27:05'),
(29, 3, 1, 2, 1, 102, 'Candidature soumise', '{}', '2026-01-16 10:27:05'),
(30, 3, 2, 3, 2, 30, 'Candidature validée', '{}', '2026-01-16 10:27:05'),
(31, 3, 3, 4, 4, 30, 'Paiement validé', '{}', '2026-01-16 10:27:05'),
(32, 3, 4, 5, 6, 21, 'Format validé', '{}', '2026-01-16 10:27:05'),
(33, 3, 5, 6, 8, 80, 'Évaluation démarrée', '{}', '2026-01-16 10:27:05'),
(34, 3, 6, 7, 9, 80, 'Rapport validé', '{}', '2026-01-16 10:27:05'),
(35, 3, 7, 8, 12, 30, 'Demande avis', '{}', '2026-01-16 10:27:05'),
(36, 3, 8, 9, 13, 72, 'Avis favorable', '{}', '2026-01-16 10:27:05'),
(37, 3, 9, 10, 15, 80, 'Jury en constitution', '{}', '2026-01-16 10:27:05'),
(38, 3, 10, 11, 16, 30, 'Soutenance planifiée', '{\"date\": \"2024-12-20\"}', '2026-01-16 10:27:05'),
(39, 4, NULL, 1, NULL, 30, 'Inscription', '{\"etudiant\": \"ASSI Marie-Claire\"}', '2026-01-16 10:27:05'),
(40, 4, 1, 2, 1, 103, 'Candidature', '{}', '2026-01-16 10:27:05'),
(41, 4, 2, 3, 2, 31, 'Validée scolarité', '{}', '2026-01-16 10:27:05'),
(42, 4, 3, 4, 4, 31, 'Paiement OK', '{}', '2026-01-16 10:27:05'),
(43, 4, 4, 5, 6, 20, 'Format OK', '{}', '2026-01-16 10:27:05'),
(44, 4, 5, 6, 8, 80, 'Évaluation', '{}', '2026-01-16 10:27:05'),
(45, 4, 6, 7, 9, 80, 'Rapport validé', '{}', '2026-01-16 10:27:05'),
(46, 4, 7, 8, 12, 30, 'Demande avis', '{}', '2026-01-16 10:27:05'),
(47, 4, 8, 9, 13, 73, 'Avis favorable', '{}', '2026-01-16 10:27:05'),
(48, 4, 9, 10, 15, 80, 'Jury en constitution', '{}', '2026-01-16 10:27:05');

-- --------------------------------------------------------

--
-- Structure de la table `workflow_sla_tracking`
--

DROP TABLE IF EXISTS `workflow_sla_tracking`;
CREATE TABLE IF NOT EXISTS `workflow_sla_tracking` (
  `id_sla_track` int NOT NULL AUTO_INCREMENT,
  `dossier_id` int NOT NULL,
  `etat_workflow` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sla_deadline` datetime NOT NULL,
  `date_entree` datetime NOT NULL,
  `date_sortie` datetime DEFAULT NULL,
  `duree_reelle` int DEFAULT NULL COMMENT 'En minutes',
  `sla_respecte` tinyint(1) DEFAULT NULL,
  `depassement_minutes` int DEFAULT NULL,
  `alerte_envoyee` tinyint(1) DEFAULT '0',
  `escalade_declenchee` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_sla_track`),
  KEY `idx_dossier` (`dossier_id`),
  KEY `idx_deadline` (`sla_deadline`),
  KEY `idx_respecte` (`sla_respecte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `workflow_transitions`
--

DROP TABLE IF EXISTS `workflow_transitions`;
CREATE TABLE IF NOT EXISTS `workflow_transitions` (
  `id_transition` int NOT NULL AUTO_INCREMENT,
  `etat_source_id` int NOT NULL,
  `etat_cible_id` int NOT NULL,
  `code_transition` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_transition` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles_autorises` json DEFAULT NULL,
  `conditions_json` json DEFAULT NULL,
  `notifier` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_transition`),
  UNIQUE KEY `code_transition` (`code_transition`),
  KEY `idx_source` (`etat_source_id`),
  KEY `idx_cible` (`etat_cible_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `workflow_transitions`
--

INSERT INTO `workflow_transitions` (`id_transition`, `etat_source_id`, `etat_cible_id`, `code_transition`, `nom_transition`, `roles_autorises`, `conditions_json`, `notifier`) VALUES
(1, 1, 2, 'SOUMETTRE_CANDIDATURE', 'Soumettre candidature', '[\"etudiant\"]', '{\"paiement_effectue\": false}', 1),
(2, 2, 3, 'VALIDER_CANDIDATURE', 'Valider candidature', '[\"scolarite\", \"admin\"]', '{}', 1),
(3, 2, 1, 'REJETER_CANDIDATURE', 'Rejeter candidature', '[\"scolarite\", \"admin\"]', '{}', 1),
(4, 3, 4, 'VALIDER_PAIEMENT', 'Valider paiement', '[\"scolarite\", \"admin\"]', '{\"paiement_complet\": true}', 1),
(5, 3, 2, 'RETOUR_CANDIDATURE', 'Retour candidature', '[\"scolarite\", \"admin\"]', '{}', 1),
(6, 4, 5, 'VALIDER_FORMAT', 'Valider format rapport', '[\"communication\", \"admin\"]', '{}', 1),
(7, 4, 3, 'REJETER_FORMAT', 'Rejeter format rapport', '[\"communication\", \"admin\"]', '{}', 1),
(8, 5, 6, 'DEMARRER_EVALUATION', 'Démarrer évaluation', '[\"commission\", \"president_commission\"]', '{}', 1),
(9, 6, 7, 'VALIDER_RAPPORT', 'Valider rapport', '[\"commission\"]', '{\"vote_unanime\": true}', 1),
(10, 6, 5, 'DEMANDER_REVISION', 'Demander révision', '[\"commission\"]', '{}', 1),
(11, 6, 4, 'RETOUR_COMMUNICATION', 'Retour communication', '[\"commission\"]', '{}', 1),
(12, 7, 8, 'DEMANDER_AVIS_ENCADREUR', 'Demander avis encadreur', '[\"scolarite\", \"admin\"]', '{}', 1),
(13, 8, 9, 'AVIS_FAVORABLE', 'Avis favorable encadreur', '[\"encadreur\", \"admin\"]', '{}', 1),
(14, 8, 7, 'AVIS_DEFAVORABLE', 'Avis défavorable', '[\"encadreur\", \"admin\"]', '{}', 1),
(15, 9, 10, 'CONSTITUER_JURY', 'Constituer jury', '[\"president_commission\", \"admin\"]', '{}', 1),
(16, 10, 11, 'PLANIFIER_SOUTENANCE', 'Planifier soutenance', '[\"scolarite\", \"admin\"]', '{\"jury_complet\": true}', 1),
(17, 10, 9, 'ANNULER_JURY', 'Annuler jury', '[\"president_commission\", \"admin\"]', '{}', 1),
(18, 11, 12, 'DEMARRER_SOUTENANCE', 'Démarrer soutenance', '[\"president_jury\"]', '{\"code_valide\": true}', 1),
(19, 11, 10, 'REPORTER_SOUTENANCE', 'Reporter soutenance', '[\"scolarite\", \"admin\"]', '{}', 1),
(20, 12, 13, 'TERMINER_SOUTENANCE', 'Terminer soutenance', '[\"president_jury\"]', '{\"notes_saisies\": true}', 1),
(21, 13, 14, 'DELIVRER_DIPLOME', 'Délivrer diplôme', '[\"scolarite\", \"admin\"]', '{\"pv_genere\": true}', 1);

-- --------------------------------------------------------

--
-- Structure de la table `workflow_transitions_metadata`
--

DROP TABLE IF EXISTS `workflow_transitions_metadata`;
CREATE TABLE IF NOT EXISTS `workflow_transitions_metadata` (
  `id_metadata` int NOT NULL AUTO_INCREMENT,
  `historique_workflow_id` int NOT NULL,
  `cle` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valeur` text COLLATE utf8mb4_unicode_ci,
  `type_donnee` enum('string','int','float','boolean','json') COLLATE utf8mb4_unicode_ci DEFAULT 'string',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_metadata`),
  KEY `idx_historique` (`historique_workflow_id`),
  KEY `idx_cle` (`cle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `candidatures`
--
ALTER TABLE `candidatures` ADD FULLTEXT KEY `idx_theme` (`theme`);

--
-- Index pour la table `documents_generes_historique`
--
ALTER TABLE `documents_generes_historique` ADD FULLTEXT KEY `ft_documents_search` (`nom_fichier`);

--
-- Index pour la table `enseignants`
--
ALTER TABLE `enseignants` ADD FULLTEXT KEY `idx_fulltext` (`nom_ens`,`prenom_ens`,`email_ens`);
ALTER TABLE `enseignants` ADD FULLTEXT KEY `ft_enseignants_search` (`nom_ens`,`prenom_ens`,`email_ens`);

--
-- Index pour la table `entreprises`
--
ALTER TABLE `entreprises` ADD FULLTEXT KEY `ft_entreprises_search` (`nom_entreprise`,`secteur_activite`);

--
-- Index pour la table `etudiants`
--
ALTER TABLE `etudiants` ADD FULLTEXT KEY `idx_fulltext` (`nom_etu`,`prenom_etu`,`email_etu`);
ALTER TABLE `etudiants` ADD FULLTEXT KEY `ft_etudiants_search` (`nom_etu`,`prenom_etu`,`email_etu`,`num_etu`);

--
-- Index pour la table `imports_sessions`
--
ALTER TABLE `imports_sessions` ADD FULLTEXT KEY `ft_imports_search` (`nom_fichier`,`commentaire`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications` ADD FULLTEXT KEY `ft_notifications_search` (`titre`,`contenu`);

--
-- Index pour la table `pister`
--
ALTER TABLE `pister` ADD FULLTEXT KEY `ft_audit_search` (`action`,`entite_type`);

--
-- Index pour la table `rapports_etudiants`
--
ALTER TABLE `rapports_etudiants` ADD FULLTEXT KEY `idx_titre` (`titre`);
ALTER TABLE `rapports_etudiants` ADD FULLTEXT KEY `ft_rapports_search` (`titre`,`contenu_html`);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `annotations_rapport`
--
ALTER TABLE `annotations_rapport`
  ADD CONSTRAINT `annotations_rapport_ibfk_1` FOREIGN KEY (`rapport_id`) REFERENCES `rapports_etudiants` (`id_rapport`) ON DELETE CASCADE,
  ADD CONSTRAINT `annotations_rapport_ibfk_2` FOREIGN KEY (`auteur_id`) REFERENCES `enseignants` (`id_enseignant`) ON DELETE CASCADE;

--
-- Contraintes pour la table `archives`
--
ALTER TABLE `archives`
  ADD CONSTRAINT `archives_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents_generes` (`id_document`) ON DELETE CASCADE;

--
-- Contraintes pour la table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `candidatures`
--
ALTER TABLE `candidatures`
  ADD CONSTRAINT `candidatures_ibfk_1` FOREIGN KEY (`dossier_id`) REFERENCES `dossiers_etudiants` (`id_dossier`) ON DELETE CASCADE,
  ADD CONSTRAINT `candidatures_ibfk_2` FOREIGN KEY (`entreprise_id`) REFERENCES `entreprises` (`id_entreprise`) ON DELETE SET NULL;

--
-- Contraintes pour la table `codes_temporaires`
--
ALTER TABLE `codes_temporaires`
  ADD CONSTRAINT `codes_temporaires_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `decisions_jury`
--
ALTER TABLE `decisions_jury`
  ADD CONSTRAINT `decisions_jury_ibfk_1` FOREIGN KEY (`soutenance_id`) REFERENCES `soutenances` (`id_soutenance`) ON DELETE CASCADE;

--
-- Contraintes pour la table `delegations_actions_log`
--
ALTER TABLE `delegations_actions_log`
  ADD CONSTRAINT `delegations_actions_log_ibfk_1` FOREIGN KEY (`delegation_id`) REFERENCES `delegations_fonctions` (`id_delegation`) ON DELETE CASCADE,
  ADD CONSTRAINT `delegations_actions_log_ibfk_2` FOREIGN KEY (`effectue_par`) REFERENCES `utilisateurs` (`id_utilisateur`),
  ADD CONSTRAINT `delegations_actions_log_ibfk_3` FOREIGN KEY (`au_nom_de`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `delegations_fonctions`
--
ALTER TABLE `delegations_fonctions`
  ADD CONSTRAINT `delegations_fonctions_ibfk_1` FOREIGN KEY (`delegant_id`) REFERENCES `utilisateurs` (`id_utilisateur`),
  ADD CONSTRAINT `delegations_fonctions_ibfk_2` FOREIGN KEY (`delegataire_id`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `demandes_exoneration`
--
ALTER TABLE `demandes_exoneration`
  ADD CONSTRAINT `demandes_exoneration_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE,
  ADD CONSTRAINT `demandes_exoneration_ibfk_2` FOREIGN KEY (`type_exoneration_id`) REFERENCES `exonerations_types` (`id_type_exoneration`),
  ADD CONSTRAINT `demandes_exoneration_ibfk_3` FOREIGN KEY (`traite_par`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `documents_generes`
--
ALTER TABLE `documents_generes`
  ADD CONSTRAINT `documents_generes_ibfk_1` FOREIGN KEY (`genere_par`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `documents_generes_historique`
--
ALTER TABLE `documents_generes_historique`
  ADD CONSTRAINT `documents_generes_historique_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `documents_templates` (`id_template`),
  ADD CONSTRAINT `documents_generes_historique_ibfk_2` FOREIGN KEY (`genere_par`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `documents_signatures_electroniques`
--
ALTER TABLE `documents_signatures_electroniques`
  ADD CONSTRAINT `documents_signatures_electroniques_ibfk_1` FOREIGN KEY (`document_genere_id`) REFERENCES `documents_generes_historique` (`id_document_genere`) ON DELETE CASCADE,
  ADD CONSTRAINT `documents_signatures_electroniques_ibfk_2` FOREIGN KEY (`signataire_id`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `documents_templates`
--
ALTER TABLE `documents_templates`
  ADD CONSTRAINT `documents_templates_ibfk_1` FOREIGN KEY (`cree_par`) REFERENCES `utilisateurs` (`id_utilisateur`),
  ADD CONSTRAINT `documents_templates_ibfk_2` FOREIGN KEY (`modifie_par`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `dossiers_etudiants`
--
ALTER TABLE `dossiers_etudiants`
  ADD CONSTRAINT `dossiers_etudiants_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE,
  ADD CONSTRAINT `dossiers_etudiants_ibfk_2` FOREIGN KEY (`annee_acad_id`) REFERENCES `annee_academique` (`id_annee_acad`) ON DELETE RESTRICT,
  ADD CONSTRAINT `dossiers_etudiants_ibfk_3` FOREIGN KEY (`etat_actuel_id`) REFERENCES `workflow_etats` (`id_etat`) ON DELETE RESTRICT;

--
-- Contraintes pour la table `ecue`
--
ALTER TABLE `ecue`
  ADD CONSTRAINT `ecue_ibfk_1` FOREIGN KEY (`ue_id`) REFERENCES `ue` (`id_ue`) ON DELETE CASCADE;

--
-- Contraintes pour la table `escalades`
--
ALTER TABLE `escalades`
  ADD CONSTRAINT `escalades_ibfk_1` FOREIGN KEY (`dossier_id`) REFERENCES `dossiers_etudiants` (`id_dossier`) ON DELETE CASCADE,
  ADD CONSTRAINT `escalades_ibfk_2` FOREIGN KEY (`cree_par`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL,
  ADD CONSTRAINT `escalades_ibfk_3` FOREIGN KEY (`assignee_a`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `escalades_actions`
--
ALTER TABLE `escalades_actions`
  ADD CONSTRAINT `escalades_actions_ibfk_1` FOREIGN KEY (`escalade_id`) REFERENCES `escalades` (`id_escalade`) ON DELETE CASCADE,
  ADD CONSTRAINT `escalades_actions_ibfk_2` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `exonerations`
--
ALTER TABLE `exonerations`
  ADD CONSTRAINT `exonerations_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE,
  ADD CONSTRAINT `exonerations_ibfk_2` FOREIGN KEY (`annee_acad_id`) REFERENCES `annee_academique` (`id_annee_acad`) ON DELETE RESTRICT,
  ADD CONSTRAINT `exonerations_ibfk_3` FOREIGN KEY (`approuve_par`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `exonerations_appliquees`
--
ALTER TABLE `exonerations_appliquees`
  ADD CONSTRAINT `exonerations_appliquees_ibfk_1` FOREIGN KEY (`demande_exoneration_id`) REFERENCES `demandes_exoneration` (`id_demande_exoneration`),
  ADD CONSTRAINT `exonerations_appliquees_ibfk_2` FOREIGN KEY (`paiement_id`) REFERENCES `paiements` (`id_paiement`),
  ADD CONSTRAINT `exonerations_appliquees_ibfk_3` FOREIGN KEY (`applique_par`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `historique_entites`
--
ALTER TABLE `historique_entites`
  ADD CONSTRAINT `historique_entites_ibfk_1` FOREIGN KEY (`modifie_par`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `imports_configurations`
--
ALTER TABLE `imports_configurations`
  ADD CONSTRAINT `imports_configurations_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `imports_historiques`
--
ALTER TABLE `imports_historiques`
  ADD CONSTRAINT `imports_historiques_ibfk_1` FOREIGN KEY (`importe_par`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `imports_lignes_details`
--
ALTER TABLE `imports_lignes_details`
  ADD CONSTRAINT `imports_lignes_details_ibfk_1` FOREIGN KEY (`session_import_id`) REFERENCES `imports_sessions` (`id_session_import`) ON DELETE CASCADE;

--
-- Contraintes pour la table `imports_rollback_data`
--
ALTER TABLE `imports_rollback_data`
  ADD CONSTRAINT `imports_rollback_data_ibfk_1` FOREIGN KEY (`session_import_id`) REFERENCES `imports_sessions` (`id_session_import`) ON DELETE CASCADE;

--
-- Contraintes pour la table `imports_sessions`
--
ALTER TABLE `imports_sessions`
  ADD CONSTRAINT `imports_sessions_ibfk_1` FOREIGN KEY (`config_import_id`) REFERENCES `imports_configurations` (`id_config_import`),
  ADD CONSTRAINT `imports_sessions_ibfk_2` FOREIGN KEY (`importe_par`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `jury_membres`
--
ALTER TABLE `jury_membres`
  ADD CONSTRAINT `jury_membres_ibfk_1` FOREIGN KEY (`dossier_id`) REFERENCES `dossiers_etudiants` (`id_dossier`) ON DELETE CASCADE,
  ADD CONSTRAINT `jury_membres_ibfk_2` FOREIGN KEY (`enseignant_id`) REFERENCES `enseignants` (`id_enseignant`) ON DELETE CASCADE;

--
-- Contraintes pour la table `maintenance_planifiee`
--
ALTER TABLE `maintenance_planifiee`
  ADD CONSTRAINT `maintenance_planifiee_ibfk_1` FOREIGN KEY (`planifie_par`) REFERENCES `utilisateurs` (`id_utilisateur`),
  ADD CONSTRAINT `maintenance_planifiee_ibfk_2` FOREIGN KEY (`annule_par`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `messages_internes`
--
ALTER TABLE `messages_internes`
  ADD CONSTRAINT `messages_internes_ibfk_1` FOREIGN KEY (`expediteur_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_internes_ibfk_2` FOREIGN KEY (`destinataire_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notes_soutenance`
--
ALTER TABLE `notes_soutenance`
  ADD CONSTRAINT `notes_soutenance_ibfk_1` FOREIGN KEY (`soutenance_id`) REFERENCES `soutenances` (`id_soutenance`) ON DELETE CASCADE,
  ADD CONSTRAINT `notes_soutenance_ibfk_2` FOREIGN KEY (`membre_jury_id`) REFERENCES `jury_membres` (`id_membre_jury`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`destinataire_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notifications_queue`
--
ALTER TABLE `notifications_queue`
  ADD CONSTRAINT `notifications_queue_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `notification_templates` (`id_template`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_queue_ibfk_2` FOREIGN KEY (`destinataire_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD CONSTRAINT `paiements_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE,
  ADD CONSTRAINT `paiements_ibfk_2` FOREIGN KEY (`annee_acad_id`) REFERENCES `annee_academique` (`id_annee_acad`) ON DELETE RESTRICT,
  ADD CONSTRAINT `paiements_ibfk_3` FOREIGN KEY (`enregistre_par`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `participants_interventions`
--
ALTER TABLE `participants_interventions`
  ADD CONSTRAINT `participants_interventions_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions_commission` (`id_session`) ON DELETE CASCADE,
  ADD CONSTRAINT `participants_interventions_ibfk_2` FOREIGN KEY (`agenda_item_id`) REFERENCES `sessions_commission_agendas` (`id_agenda_item`),
  ADD CONSTRAINT `participants_interventions_ibfk_3` FOREIGN KEY (`participant_id`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `participants_sessions_presences`
--
ALTER TABLE `participants_sessions_presences`
  ADD CONSTRAINT `participants_sessions_presences_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions_commission` (`id_session`) ON DELETE CASCADE,
  ADD CONSTRAINT `participants_sessions_presences_ibfk_2` FOREIGN KEY (`participant_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `participants_sessions_presences_ibfk_3` FOREIGN KEY (`verifie_par`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `penalites`
--
ALTER TABLE `penalites`
  ADD CONSTRAINT `penalites_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE;

--
-- Contraintes pour la table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_ibfk_1` FOREIGN KEY (`groupe_id`) REFERENCES `groupes` (`id_groupe`) ON DELETE CASCADE,
  ADD CONSTRAINT `permissions_ibfk_2` FOREIGN KEY (`ressource_id`) REFERENCES `ressources` (`id_ressource`) ON DELETE CASCADE;

--
-- Contraintes pour la table `permissions_actions_details`
--
ALTER TABLE `permissions_actions_details`
  ADD CONSTRAINT `permissions_actions_details_ibfk_1` FOREIGN KEY (`action_id`) REFERENCES `actions` (`id_action`) ON DELETE CASCADE;

--
-- Contraintes pour la table `permissions_cache`
--
ALTER TABLE `permissions_cache`
  ADD CONSTRAINT `permissions_cache_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `permissions_conditions`
--
ALTER TABLE `permissions_conditions`
  ADD CONSTRAINT `permissions_conditions_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `utilisateurs_permissions` (`id_permission`) ON DELETE CASCADE;

--
-- Contraintes pour la table `permissions_delegations`
--
ALTER TABLE `permissions_delegations`
  ADD CONSTRAINT `permissions_delegations_ibfk_1` FOREIGN KEY (`utilisateur_source_id`) REFERENCES `utilisateurs` (`id_utilisateur`),
  ADD CONSTRAINT `permissions_delegations_ibfk_2` FOREIGN KEY (`utilisateur_cible_id`) REFERENCES `utilisateurs` (`id_utilisateur`),
  ADD CONSTRAINT `permissions_delegations_ibfk_3` FOREIGN KEY (`permission_id`) REFERENCES `utilisateurs_permissions` (`id_permission`),
  ADD CONSTRAINT `permissions_delegations_ibfk_4` FOREIGN KEY (`approuve_par`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `pister`
--
ALTER TABLE `pister`
  ADD CONSTRAINT `pister_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `rapports_etudiants`
--
ALTER TABLE `rapports_etudiants`
  ADD CONSTRAINT `rapports_etudiants_ibfk_1` FOREIGN KEY (`dossier_id`) REFERENCES `dossiers_etudiants` (`id_dossier`) ON DELETE CASCADE;

--
-- Contraintes pour la table `rapport_annotations`
--
ALTER TABLE `rapport_annotations`
  ADD CONSTRAINT `rapport_annotations_ibfk_1` FOREIGN KEY (`rapport_id`) REFERENCES `rapports_commission` (`id_rapport`) ON DELETE CASCADE,
  ADD CONSTRAINT `rapport_annotations_ibfk_2` FOREIGN KEY (`annotateur_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE RESTRICT,
  ADD CONSTRAINT `rapport_annotations_ibfk_3` FOREIGN KEY (`resolu_par`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `rapport_fichiers_attaches`
--
ALTER TABLE `rapport_fichiers_attaches`
  ADD CONSTRAINT `rapport_fichiers_attaches_ibfk_1` FOREIGN KEY (`rapport_id`) REFERENCES `rapports_commission` (`id_rapport`) ON DELETE CASCADE,
  ADD CONSTRAINT `rapport_fichiers_attaches_ibfk_2` FOREIGN KEY (`upload_par`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE RESTRICT;

--
-- Contraintes pour la table `rapport_validations`
--
ALTER TABLE `rapport_validations`
  ADD CONSTRAINT `rapport_validations_ibfk_1` FOREIGN KEY (`rapport_id`) REFERENCES `rapports_commission` (`id_rapport`) ON DELETE CASCADE,
  ADD CONSTRAINT `rapport_validations_ibfk_2` FOREIGN KEY (`validateur_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE RESTRICT;

--
-- Contraintes pour la table `rapport_versions`
--
ALTER TABLE `rapport_versions`
  ADD CONSTRAINT `rapport_versions_ibfk_1` FOREIGN KEY (`rapport_id`) REFERENCES `rapports_commission` (`id_rapport`) ON DELETE CASCADE,
  ADD CONSTRAINT `rapport_versions_ibfk_2` FOREIGN KEY (`modifie_par`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE RESTRICT;

--
-- Contraintes pour la table `reclamations`
--
ALTER TABLE `reclamations`
  ADD CONSTRAINT `reclamations_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE,
  ADD CONSTRAINT `reclamations_ibfk_2` FOREIGN KEY (`prise_en_charge_par`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL,
  ADD CONSTRAINT `reclamations_ibfk_3` FOREIGN KEY (`resolue_par`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL,
  ADD CONSTRAINT `reclamations_ibfk_4` FOREIGN KEY (`traite_par`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `reclamation_reponses`
--
ALTER TABLE `reclamation_reponses`
  ADD CONSTRAINT `reclamation_reponses_ibfk_1` FOREIGN KEY (`reclamation_id`) REFERENCES `reclamations` (`id_reclamation`) ON DELETE CASCADE,
  ADD CONSTRAINT `reclamation_reponses_ibfk_2` FOREIGN KEY (`auteur_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `roles_temporaires`
--
ALTER TABLE `roles_temporaires`
  ADD CONSTRAINT `roles_temporaires_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_temporaires_ibfk_2` FOREIGN KEY (`cree_par`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `roles_temporaires_attributions`
--
ALTER TABLE `roles_temporaires_attributions`
  ADD CONSTRAINT `roles_temporaires_attributions_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_temporaires_attributions_ibfk_2` FOREIGN KEY (`type_role_temp_id`) REFERENCES `roles_temporaires_types` (`id_type_role_temp`),
  ADD CONSTRAINT `roles_temporaires_attributions_ibfk_3` FOREIGN KEY (`demande_par`) REFERENCES `utilisateurs` (`id_utilisateur`),
  ADD CONSTRAINT `roles_temporaires_attributions_ibfk_4` FOREIGN KEY (`approuve_par`) REFERENCES `utilisateurs` (`id_utilisateur`),
  ADD CONSTRAINT `roles_temporaires_attributions_ibfk_5` FOREIGN KEY (`revoque_par`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `semestre`
--
ALTER TABLE `semestre`
  ADD CONSTRAINT `semestre_ibfk_1` FOREIGN KEY (`annee_acad_id`) REFERENCES `annee_academique` (`id_annee_acad`) ON DELETE CASCADE;

--
-- Contraintes pour la table `sessions_actives`
--
ALTER TABLE `sessions_actives`
  ADD CONSTRAINT `sessions_actives_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `sessions_commission_absences`
--
ALTER TABLE `sessions_commission_absences`
  ADD CONSTRAINT `sessions_commission_absences_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions_commission` (`id_session`) ON DELETE CASCADE,
  ADD CONSTRAINT `sessions_commission_absences_ibfk_2` FOREIGN KEY (`membre_absent_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `sessions_commission_absences_ibfk_3` FOREIGN KEY (`remplacant_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL,
  ADD CONSTRAINT `sessions_commission_absences_ibfk_4` FOREIGN KEY (`approuve_par`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `sessions_commission_agendas`
--
ALTER TABLE `sessions_commission_agendas`
  ADD CONSTRAINT `sessions_commission_agendas_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions_commission` (`id_session`) ON DELETE CASCADE,
  ADD CONSTRAINT `sessions_commission_agendas_ibfk_2` FOREIGN KEY (`dossier_id`) REFERENCES `dossiers_etudiants` (`id_dossier`) ON DELETE SET NULL,
  ADD CONSTRAINT `sessions_commission_agendas_ibfk_3` FOREIGN KEY (`rapporteur_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `sessions_commission_convocations`
--
ALTER TABLE `sessions_commission_convocations`
  ADD CONSTRAINT `sessions_commission_convocations_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions_commission` (`id_session`) ON DELETE CASCADE,
  ADD CONSTRAINT `sessions_commission_convocations_ibfk_2` FOREIGN KEY (`membre_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `sessions_commission_documents`
--
ALTER TABLE `sessions_commission_documents`
  ADD CONSTRAINT `sessions_commission_documents_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions_commission` (`id_session`) ON DELETE CASCADE,
  ADD CONSTRAINT `sessions_commission_documents_ibfk_2` FOREIGN KEY (`agenda_item_id`) REFERENCES `sessions_commission_agendas` (`id_agenda_item`) ON DELETE SET NULL,
  ADD CONSTRAINT `sessions_commission_documents_ibfk_3` FOREIGN KEY (`upload_par`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE RESTRICT;

--
-- Contraintes pour la table `sessions_commission_votes`
--
ALTER TABLE `sessions_commission_votes`
  ADD CONSTRAINT `sessions_commission_votes_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions_commission` (`id_session`) ON DELETE CASCADE,
  ADD CONSTRAINT `sessions_commission_votes_ibfk_2` FOREIGN KEY (`agenda_item_id`) REFERENCES `sessions_commission_agendas` (`id_agenda_item`) ON DELETE SET NULL;

--
-- Contraintes pour la table `sessions_enregistrements`
--
ALTER TABLE `sessions_enregistrements`
  ADD CONSTRAINT `sessions_enregistrements_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions_commission` (`id_session`) ON DELETE CASCADE,
  ADD CONSTRAINT `sessions_enregistrements_ibfk_2` FOREIGN KEY (`enregistre_par`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `soutenances`
--
ALTER TABLE `soutenances`
  ADD CONSTRAINT `soutenances_ibfk_1` FOREIGN KEY (`dossier_id`) REFERENCES `dossiers_etudiants` (`id_dossier`) ON DELETE CASCADE;

--
-- Contraintes pour la table `stats_dashboards`
--
ALTER TABLE `stats_dashboards`
  ADD CONSTRAINT `stats_dashboards_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `systeme_messages`
--
ALTER TABLE `systeme_messages`
  ADD CONSTRAINT `systeme_messages_ibfk_1` FOREIGN KEY (`cree_par`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `systeme_messages_lectures`
--
ALTER TABLE `systeme_messages_lectures`
  ADD CONSTRAINT `systeme_messages_lectures_ibfk_1` FOREIGN KEY (`message_systeme_id`) REFERENCES `systeme_messages` (`id_message_systeme`) ON DELETE CASCADE,
  ADD CONSTRAINT `systeme_messages_lectures_ibfk_2` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `ue`
--
ALTER TABLE `ue`
  ADD CONSTRAINT `ue_ibfk_1` FOREIGN KEY (`niveau_id`) REFERENCES `niveau_etude` (`id_niveau`) ON DELETE SET NULL,
  ADD CONSTRAINT `ue_ibfk_2` FOREIGN KEY (`semestre_id`) REFERENCES `semestre` (`id_semestre`) ON DELETE SET NULL;

--
-- Contraintes pour la table `utilisateurs_groupes`
--
ALTER TABLE `utilisateurs_groupes`
  ADD CONSTRAINT `utilisateurs_groupes_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `utilisateurs_groupes_ibfk_2` FOREIGN KEY (`groupe_id`) REFERENCES `groupes` (`id_groupe`) ON DELETE CASCADE,
  ADD CONSTRAINT `utilisateurs_groupes_ibfk_3` FOREIGN KEY (`attribue_par`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `votes_commission`
--
ALTER TABLE `votes_commission`
  ADD CONSTRAINT `votes_commission_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions_commission` (`id_session`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_commission_ibfk_2` FOREIGN KEY (`rapport_id`) REFERENCES `rapports_etudiants` (`id_rapport`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_commission_ibfk_3` FOREIGN KEY (`membre_id`) REFERENCES `enseignants` (`id_enseignant`) ON DELETE CASCADE;

--
-- Contraintes pour la table `workflow_blocages`
--
ALTER TABLE `workflow_blocages`
  ADD CONSTRAINT `workflow_blocages_ibfk_1` FOREIGN KEY (`dossier_id`) REFERENCES `dossiers_etudiants` (`id_dossier`) ON DELETE CASCADE,
  ADD CONSTRAINT `workflow_blocages_ibfk_2` FOREIGN KEY (`resolu_par`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `workflow_historique`
--
ALTER TABLE `workflow_historique`
  ADD CONSTRAINT `workflow_historique_ibfk_1` FOREIGN KEY (`etat_source_id`) REFERENCES `workflow_etats` (`id_etat`) ON DELETE SET NULL,
  ADD CONSTRAINT `workflow_historique_ibfk_2` FOREIGN KEY (`etat_cible_id`) REFERENCES `workflow_etats` (`id_etat`) ON DELETE RESTRICT,
  ADD CONSTRAINT `workflow_historique_ibfk_3` FOREIGN KEY (`transition_id`) REFERENCES `workflow_transitions` (`id_transition`) ON DELETE SET NULL,
  ADD CONSTRAINT `workflow_historique_ibfk_4` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `workflow_sla_tracking`
--
ALTER TABLE `workflow_sla_tracking`
  ADD CONSTRAINT `workflow_sla_tracking_ibfk_1` FOREIGN KEY (`dossier_id`) REFERENCES `dossiers_etudiants` (`id_dossier`) ON DELETE CASCADE;

--
-- Contraintes pour la table `workflow_transitions`
--
ALTER TABLE `workflow_transitions`
  ADD CONSTRAINT `workflow_transitions_ibfk_1` FOREIGN KEY (`etat_source_id`) REFERENCES `workflow_etats` (`id_etat`) ON DELETE RESTRICT,
  ADD CONSTRAINT `workflow_transitions_ibfk_2` FOREIGN KEY (`etat_cible_id`) REFERENCES `workflow_etats` (`id_etat`) ON DELETE RESTRICT;

--
-- Contraintes pour la table `workflow_transitions_metadata`
--
ALTER TABLE `workflow_transitions_metadata`
  ADD CONSTRAINT `workflow_transitions_metadata_ibfk_1` FOREIGN KEY (`historique_workflow_id`) REFERENCES `historique_workflow` (`id_historique`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
