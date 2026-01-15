-- =====================================================
-- Seed: 003_traitements_actions.sql
-- Purpose: Seed les actions, traitements, ressources et permissions
-- Date: 2026-01-15 (Mise à jour selon checkmaster.sql)
-- =====================================================

-- --------------------------------------------------------
-- 1. Les 6 actions système (table: action)
-- --------------------------------------------------------
INSERT INTO `action` (`id_action`, `lib_action`, `description`) VALUES
(1, 'Lire', 'Consulter et visualiser les données'),
(2, 'Creer', 'Créer de nouvelles entrées'),
(3, 'Modifier', 'Modifier les entrées existantes'),
(4, 'Supprimer', 'Supprimer des entrées'),
(5, 'Valider', 'Valider et approuver des entrées'),
(6, 'Exporter', 'Exporter des données vers des fichiers')
ON DUPLICATE KEY UPDATE 
    lib_action = VALUES(lib_action),
    description = VALUES(description);

-- --------------------------------------------------------
-- 2. Les traitements par module (table: traitement - Legacy)
-- --------------------------------------------------------
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
(30, 'Maintenance', 'Opérations de maintenance (module: administration)', 30, 1)
ON DUPLICATE KEY UPDATE 
    lib_traitement = VALUES(lib_traitement),
    description = VALUES(description),
    ordre_traitement = VALUES(ordre_traitement),
    actif = VALUES(actif);

-- --------------------------------------------------------
-- 3. Les ressources par module (table: ressources - Nouveau système)
-- --------------------------------------------------------
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
(30, 'maintenance', 'Maintenance', 'Opérations maintenance', 'administration')
ON DUPLICATE KEY UPDATE 
    code_ressource = VALUES(code_ressource),
    nom_ressource = VALUES(nom_ressource),
    description = VALUES(description),
    module = VALUES(module);

-- --------------------------------------------------------
-- 4. Permissions granulaires (table: permissions)
-- --------------------------------------------------------
INSERT INTO `permissions` (`id_permission`, `groupe_id`, `ressource_id`, `peut_lire`, `peut_creer`, `peut_modifier`, `peut_supprimer`, `peut_exporter`, `peut_valider`) VALUES
(1, 1, 9, 1, 1, 1, 1, 1, 1),
(2, 1, 26, 1, 1, 1, 1, 1, 1),
(3, 1, 28, 1, 1, 1, 1, 1, 1),
(4, 1, 24, 1, 1, 1, 1, 1, 1),
(5, 1, 13, 1, 1, 1, 1, 1, 1),
(6, 1, 14, 1, 1, 1, 1, 1, 1),
(7, 1, 27, 1, 1, 1, 1, 1, 1),
(8, 1, 25, 1, 1, 1, 1, 1, 1),
(9, 1, 12, 1, 1, 1, 1, 1, 1),
(10, 1, 6, 1, 1, 1, 1, 1, 1),
(11, 1, 8, 1, 1, 1, 1, 1, 1),
(12, 1, 5, 1, 1, 1, 1, 1, 1),
(13, 1, 21, 1, 1, 1, 1, 1, 1),
(14, 1, 3, 1, 1, 1, 1, 1, 1),
(15, 1, 16, 1, 1, 1, 1, 1, 1),
(16, 1, 30, 1, 1, 1, 1, 1, 1),
(17, 1, 23, 1, 1, 1, 1, 1, 1),
(18, 1, 18, 1, 1, 1, 1, 1, 1),
(19, 1, 22, 1, 1, 1, 1, 1, 1),
(20, 1, 19, 1, 1, 1, 1, 1, 1),
(21, 1, 20, 1, 1, 1, 1, 1, 1),
(22, 1, 4, 1, 1, 1, 1, 1, 1),
(23, 1, 7, 1, 1, 1, 1, 1, 1),
(24, 1, 15, 1, 1, 1, 1, 1, 1),
(25, 1, 29, 1, 1, 1, 1, 1, 1),
(26, 1, 2, 1, 1, 1, 1, 1, 1),
(27, 1, 17, 1, 1, 1, 1, 1, 1),
(28, 1, 10, 1, 1, 1, 1, 1, 1),
(29, 1, 1, 1, 1, 1, 1, 1, 1),
(30, 1, 11, 1, 1, 1, 1, 1, 1),
(32, 4, 5, 1, 1, 1, 0, 1, 0),
(33, 4, 13, 1, 1, 1, 0, 1, 1),
(34, 4, 19, 1, 1, 1, 0, 1, 0),
(35, 4, 20, 1, 1, 1, 0, 1, 0),
(36, 4, 25, 1, 0, 0, 0, 1, 0),
(37, 3, 15, 1, 0, 0, 0, 0, 1),
(38, 3, 22, 1, 1, 1, 0, 0, 0),
(39, 7, 14, 1, 0, 1, 0, 0, 1),
(40, 7, 15, 1, 0, 1, 0, 0, 1),
(41, 8, 5, 1, 0, 0, 0, 0, 0),
(42, 8, 15, 1, 0, 1, 0, 0, 0),
(43, 8, 16, 1, 0, 1, 0, 0, 0),
(44, 8, 17, 1, 0, 0, 0, 0, 0),
(45, 8, 23, 1, 1, 1, 0, 0, 0),
(46, 9, 12, 1, 0, 0, 0, 0, 0),
(47, 9, 13, 1, 1, 1, 0, 0, 0),
(48, 9, 15, 1, 1, 1, 0, 0, 0),
(49, 9, 17, 1, 0, 0, 0, 0, 0),
(50, 9, 18, 1, 0, 0, 0, 0, 0),
(51, 9, 19, 1, 0, 0, 0, 1, 0),
(52, 9, 23, 1, 1, 1, 0, 0, 0),
(53, 9, 29, 1, 1, 0, 0, 0, 0)
ON DUPLICATE KEY UPDATE 
    peut_lire = VALUES(peut_lire),
    peut_creer = VALUES(peut_creer),
    peut_modifier = VALUES(peut_modifier),
    peut_supprimer = VALUES(peut_supprimer),
    peut_exporter = VALUES(peut_exporter),
    peut_valider = VALUES(peut_valider);

-- --------------------------------------------------------
-- 5. Rattachements Legacy (table: rattacher)
-- --------------------------------------------------------
-- Administrateur (groupe 1) - accès total
INSERT IGNORE INTO `rattacher` (`id_GU`, `id_traitement`, `id_action`) 
SELECT 1, t.id_traitement, a.id_action 
FROM `traitement` t CROSS JOIN `action` a;

-- Autres groupes (selon configuration initiale)
INSERT IGNORE INTO `rattacher` (`id_GU`, `id_traitement`, `id_action`) VALUES
(2, 25, 1), (2, 25, 2), (2, 25, 6),
(2, 26, 1), (2, 26, 6),
(3, 15, 1), (3, 15, 5),
(3, 22, 1), (3, 22, 2), (3, 22, 3),
(4, 5, 1), (4, 5, 2), (4, 5, 3), (4, 5, 6),
(4, 13, 1), (4, 13, 2), (4, 13, 3), (4, 13, 5), (4, 13, 6),
(4, 19, 1), (4, 19, 2), (4, 19, 3), (4, 19, 6),
(4, 20, 1), (4, 20, 2), (4, 20, 3), (4, 20, 6),
(4, 25, 1), (4, 25, 6),
(5, 5, 1), (5, 5, 6),
(5, 6, 1), (5, 6, 6),
(5, 12, 1), (5, 12, 6),
(5, 15, 1), (5, 15, 6),
(5, 16, 1), (5, 16, 5),
(5, 17, 1), (5, 17, 5),
(6, 5, 1), (6, 5, 6),
(6, 12, 1), (6, 12, 3), (6, 12, 6),
(6, 15, 1), (6, 15, 6),
(7, 14, 1), (7, 14, 3),
(7, 15, 1), (7, 15, 3), (7, 15, 5),
(8, 5, 1),
(8, 15, 1), (8, 15, 3),
(8, 16, 1), (8, 16, 3),
(8, 17, 1),
(8, 23, 1), (8, 23, 2), (8, 23, 3),
(9, 12, 1),
(9, 13, 1), (9, 13, 2), (9, 13, 3),
(9, 15, 1), (9, 15, 2), (9, 15, 3),
(9, 17, 1),
(9, 18, 1),
(9, 19, 1), (9, 19, 6),
(9, 23, 1), (9, 23, 2), (9, 23, 3),
(9, 29, 1), (9, 29, 2),
(10, 14, 1), (10, 14, 2), (10, 14, 3), (10, 14, 5),
(10, 15, 1), (10, 15, 5),
(10, 16, 1), (10, 16, 2), (10, 16, 3), (10, 16, 5),
(11, 16, 1),
(11, 17, 1), (11, 17, 3),
(11, 18, 1), (11, 18, 2), (11, 18, 3),
(12, 5, 1),
(12, 12, 1),
(12, 15, 1), (12, 15, 3), (12, 15, 5),
(12, 23, 1), (12, 23, 2), (12, 23, 3),
(13, 5, 1),
(13, 12, 1),
(13, 15, 1), (13, 15, 3),
(13, 23, 1), (13, 23, 2), (13, 23, 3);
