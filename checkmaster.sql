-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 15 jan. 2026 à 13:46
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  KEY `idx_date_limite` (`date_limite_etat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  KEY `idx_lue` (`lue`)
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  KEY `idx_reference` (`reference`)
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `personnel_admin`
--

INSERT INTO `personnel_admin` (`id_pers_admin`, `nom_pers`, `prenom_pers`, `email_pers`, `telephone_pers`, `fonction_id`, `actif`, `created_at`, `updated_at`) VALUES
(1, 'ADMIN', 'System', 'admin@checkmaster.ufhb.ci', '+225 00 00 00 00', NULL, 1, '2026-01-15 13:45:18', '2026-01-15 13:45:18');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  KEY `idx_groupe` (`id_GU`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_utilisateur`, `nom_utilisateur`, `login_utilisateur`, `mdp_utilisateur`, `id_type_utilisateur`, `id_GU`, `id_niv_acces_donnee`, `statut_utilisateur`, `doit_changer_mdp`, `derniere_connexion`, `tentatives_echec`, `verrouille_jusqu_a`, `created_at`, `updated_at`) VALUES
(1, 'Administrateur Système', 'admin@checkmaster.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 1, 1, NULL, 'Actif', 1, NULL, 0, NULL, '2026-01-15 13:45:18', '2026-01-15 13:45:18');

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
(1, 1, 1, '2026-01-15 13:45:18');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  KEY `idx_created` (`created_at`)
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

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `candidatures`
--
ALTER TABLE `candidatures` ADD FULLTEXT KEY `idx_theme` (`theme`);

--
-- Index pour la table `enseignants`
--
ALTER TABLE `enseignants` ADD FULLTEXT KEY `idx_fulltext` (`nom_ens`,`prenom_ens`,`email_ens`);

--
-- Index pour la table `etudiants`
--
ALTER TABLE `etudiants` ADD FULLTEXT KEY `idx_fulltext` (`nom_etu`,`prenom_etu`,`email_etu`);

--
-- Index pour la table `rapports_etudiants`
--
ALTER TABLE `rapports_etudiants` ADD FULLTEXT KEY `idx_titre` (`titre`);

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
-- Contraintes pour la table `documents_generes`
--
ALTER TABLE `documents_generes`
  ADD CONSTRAINT `documents_generes_ibfk_1` FOREIGN KEY (`genere_par`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

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
-- Contraintes pour la table `historique_entites`
--
ALTER TABLE `historique_entites`
  ADD CONSTRAINT `historique_entites_ibfk_1` FOREIGN KEY (`modifie_par`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `imports_historiques`
--
ALTER TABLE `imports_historiques`
  ADD CONSTRAINT `imports_historiques_ibfk_1` FOREIGN KEY (`importe_par`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `jury_membres`
--
ALTER TABLE `jury_membres`
  ADD CONSTRAINT `jury_membres_ibfk_1` FOREIGN KEY (`dossier_id`) REFERENCES `dossiers_etudiants` (`id_dossier`) ON DELETE CASCADE,
  ADD CONSTRAINT `jury_membres_ibfk_2` FOREIGN KEY (`enseignant_id`) REFERENCES `enseignants` (`id_enseignant`) ON DELETE CASCADE;

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
-- Contraintes pour la table `permissions_cache`
--
ALTER TABLE `permissions_cache`
  ADD CONSTRAINT `permissions_cache_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

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
-- Contraintes pour la table `roles_temporaires`
--
ALTER TABLE `roles_temporaires`
  ADD CONSTRAINT `roles_temporaires_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_temporaires_ibfk_2` FOREIGN KEY (`cree_par`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

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
-- Contraintes pour la table `soutenances`
--
ALTER TABLE `soutenances`
  ADD CONSTRAINT `soutenances_ibfk_1` FOREIGN KEY (`dossier_id`) REFERENCES `dossiers_etudiants` (`id_dossier`) ON DELETE CASCADE;

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
-- Contraintes pour la table `workflow_historique`
--
ALTER TABLE `workflow_historique`
  ADD CONSTRAINT `workflow_historique_ibfk_1` FOREIGN KEY (`etat_source_id`) REFERENCES `workflow_etats` (`id_etat`) ON DELETE SET NULL,
  ADD CONSTRAINT `workflow_historique_ibfk_2` FOREIGN KEY (`etat_cible_id`) REFERENCES `workflow_etats` (`id_etat`) ON DELETE RESTRICT,
  ADD CONSTRAINT `workflow_historique_ibfk_3` FOREIGN KEY (`transition_id`) REFERENCES `workflow_transitions` (`id_transition`) ON DELETE SET NULL,
  ADD CONSTRAINT `workflow_historique_ibfk_4` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `workflow_transitions`
--
ALTER TABLE `workflow_transitions`
  ADD CONSTRAINT `workflow_transitions_ibfk_1` FOREIGN KEY (`etat_source_id`) REFERENCES `workflow_etats` (`id_etat`) ON DELETE RESTRICT,
  ADD CONSTRAINT `workflow_transitions_ibfk_2` FOREIGN KEY (`etat_cible_id`) REFERENCES `workflow_etats` (`id_etat`) ON DELETE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
