-- =====================================================
-- Seed: 002_groupes_utilisateurs.sql
-- Purpose: Seed les 13 groupes utilisateurs du PRD
-- Date: 2025-12-19
-- =====================================================

-- Groupes principaux (avec niveaux hiérarchiques du PRD)
INSERT INTO groupes (id_groupe, nom_groupe, description, niveau_hierarchique, actif) VALUES
(1, 'Administrateur', 'Contrôle total du système, configuration, utilisateurs', 5, TRUE),
(2, 'Secrétaire', 'Gestion documentaire, archivage', 6, TRUE),
(3, 'Communication', 'Vérification format des rapports', 7, TRUE),
(4, 'Scolarité', 'Paiements, candidatures, inscriptions', 8, TRUE),
(5, 'Resp. Filière', 'Supervision filière MIAGE', 9, TRUE),
(6, 'Resp. Niveau', 'Gestion Master 2', 10, TRUE),
(7, 'Commission', 'Évaluation rapports, votes', 11, TRUE),
(8, 'Enseignant', 'Supervision, participation jury', 12, TRUE),
(9, 'Étudiant', 'Rédaction rapport, soumissions', 13, TRUE),
(10, 'Président Commission', 'Constitution des jurys', 14, TRUE),
(11, 'Président Jury', 'Saisie notes jour J (rôle temporaire)', 15, TRUE),
(12, 'Directeur Mémoire', 'Direction scientifique', 16, TRUE),
(13, 'Encadreur Pédagogique', 'Accompagnement étudiant', 17, TRUE)
ON DUPLICATE KEY UPDATE 
    nom_groupe = VALUES(nom_groupe),
    description = VALUES(description),
    niveau_hierarchique = VALUES(niveau_hierarchique);

-- Type Utilisateur (référencé par id_type_utilisateur)
INSERT INTO type_utilisateur (id_type, lib_type) VALUES
(1, 'Administrateur'),
(2, 'Personnel Administratif'),
(3, 'Enseignant'),
(4, 'Étudiant')
ON DUPLICATE KEY UPDATE lib_type = VALUES(lib_type);

-- Ressources système (pour le système de permissions)
INSERT INTO ressources (id_ressource, code_ressource, nom_ressource, description, module) VALUES
-- Module Authentification
(1, 'utilisateurs', 'Gestion Utilisateurs', 'CRUD utilisateurs du système', 'authentification'),
(2, 'sessions', 'Sessions Actives', 'Gestion des sessions connectées', 'authentification'),
(3, 'groupes', 'Groupes Utilisateurs', 'Gestion des groupes et rôles', 'authentification'),
(4, 'permissions', 'Permissions', 'Configuration des permissions', 'authentification'),
-- Module Académique
(5, 'etudiants', 'Étudiants', 'Gestion des étudiants', 'academique'),
(6, 'enseignants', 'Enseignants', 'Gestion des enseignants', 'academique'),
(7, 'personnel', 'Personnel Admin', 'Gestion du personnel administratif', 'academique'),
(8, 'entreprises', 'Entreprises', 'Référentiel entreprises partenaires', 'academique'),
(9, 'annees', 'Années Académiques', 'Gestion des années académiques', 'academique'),
(10, 'ue', 'Unités Enseignement', 'Gestion UE/ECUE', 'academique'),
-- Module Workflow
(11, 'workflow', 'Workflow', 'Gestion états et transitions', 'workflow'),
(12, 'dossiers', 'Dossiers Étudiants', 'Gestion dossiers étudiants', 'workflow'),
(13, 'candidatures', 'Candidatures', 'Gestion des candidatures', 'workflow'),
-- Module Commission
(14, 'commission', 'Commission', 'Sessions et votes commission', 'commission'),
(15, 'rapports', 'Rapports', 'Rapports étudiants', 'commission'),
-- Module Soutenance
(16, 'jury', 'Jury', 'Constitution des jurys', 'soutenance'),
(17, 'soutenances', 'Soutenances', 'Planification soutenances', 'soutenance'),
(18, 'notes', 'Notes', 'Saisie et consultation notes', 'soutenance'),
-- Module Financier
(19, 'paiements', 'Paiements', 'Enregistrement paiements', 'financier'),
(20, 'penalites', 'Pénalités', 'Gestion pénalités', 'financier'),
(21, 'exonerations', 'Exonérations', 'Gestion exonérations', 'financier'),
-- Module Communication
(22, 'notifications', 'Notifications', 'Gestion notifications', 'communication'),
(23, 'messages', 'Messages', 'Messagerie interne', 'communication'),
(24, 'calendrier', 'Calendrier', 'Calendrier académique', 'communication'),
-- Module Documents
(25, 'documents', 'Documents', 'Documents générés', 'documents'),
(26, 'archives', 'Archives', 'Archives et intégrité', 'documents'),
-- Module Administration
(27, 'configuration', 'Configuration', 'Paramètres système', 'administration'),
(28, 'audit', 'Audit', 'Logs et traçabilité', 'administration'),
(29, 'reclamations', 'Réclamations', 'Réclamations étudiantes', 'administration'),
(30, 'maintenance', 'Maintenance', 'Opérations maintenance', 'administration')
ON DUPLICATE KEY UPDATE 
    nom_ressource = VALUES(nom_ressource),
    description = VALUES(description);

-- Permissions Administrateur (accès total)
INSERT INTO permissions (groupe_id, ressource_id, peut_lire, peut_creer, peut_modifier, peut_supprimer, peut_exporter, peut_valider) 
SELECT 1, id_ressource, TRUE, TRUE, TRUE, TRUE, TRUE, TRUE FROM ressources
ON DUPLICATE KEY UPDATE 
    peut_lire = TRUE, peut_creer = TRUE, peut_modifier = TRUE, 
    peut_supprimer = TRUE, peut_exporter = TRUE, peut_valider = TRUE;

-- Permissions Scolarité
INSERT INTO permissions (groupe_id, ressource_id, peut_lire, peut_creer, peut_modifier, peut_supprimer, peut_exporter, peut_valider) VALUES
(4, 5, TRUE, TRUE, TRUE, FALSE, TRUE, FALSE),   -- etudiants: CRUD sauf supprimer
(4, 13, TRUE, TRUE, TRUE, FALSE, TRUE, TRUE),   -- candidatures: lecture + validation
(4, 19, TRUE, TRUE, TRUE, FALSE, TRUE, FALSE),  -- paiements: CRUD sauf supprimer
(4, 20, TRUE, TRUE, TRUE, FALSE, TRUE, FALSE),  -- penalites
(4, 25, TRUE, FALSE, FALSE, FALSE, TRUE, FALSE) -- documents: lecture + export
ON DUPLICATE KEY UPDATE 
    peut_lire = VALUES(peut_lire), peut_creer = VALUES(peut_creer),
    peut_modifier = VALUES(peut_modifier), peut_exporter = VALUES(peut_exporter);

-- Permissions Communication (vérification format)
INSERT INTO permissions (groupe_id, ressource_id, peut_lire, peut_creer, peut_modifier, peut_supprimer, peut_exporter, peut_valider) VALUES
(3, 15, TRUE, FALSE, FALSE, FALSE, FALSE, TRUE), -- rapports: lecture + validation format
(3, 22, TRUE, TRUE, TRUE, FALSE, FALSE, FALSE)   -- notifications: gestion
ON DUPLICATE KEY UPDATE 
    peut_lire = VALUES(peut_lire), peut_valider = VALUES(peut_valider);

-- Permissions Commission
INSERT INTO permissions (groupe_id, ressource_id, peut_lire, peut_creer, peut_modifier, peut_supprimer, peut_exporter, peut_valider) VALUES
(7, 14, TRUE, FALSE, TRUE, FALSE, FALSE, TRUE),  -- commission: vote
(7, 15, TRUE, FALSE, TRUE, FALSE, FALSE, TRUE)   -- rapports: évaluation
ON DUPLICATE KEY UPDATE 
    peut_lire = VALUES(peut_lire), peut_valider = VALUES(peut_valider);

-- Permissions Enseignant
INSERT INTO permissions (groupe_id, ressource_id, peut_lire, peut_creer, peut_modifier, peut_supprimer, peut_exporter, peut_valider) VALUES
(8, 5, TRUE, FALSE, FALSE, FALSE, FALSE, FALSE),  -- etudiants: lecture
(8, 15, TRUE, FALSE, TRUE, FALSE, FALSE, FALSE),  -- rapports: annotation
(8, 16, TRUE, FALSE, TRUE, FALSE, FALSE, FALSE),  -- jury: participation
(8, 17, TRUE, FALSE, FALSE, FALSE, FALSE, FALSE), -- soutenances: consultation
(8, 23, TRUE, TRUE, TRUE, FALSE, FALSE, FALSE)    -- messages
ON DUPLICATE KEY UPDATE peut_lire = VALUES(peut_lire);

-- Permissions Étudiant
INSERT INTO permissions (groupe_id, ressource_id, peut_lire, peut_creer, peut_modifier, peut_supprimer, peut_exporter, peut_valider) VALUES
(9, 12, TRUE, FALSE, FALSE, FALSE, FALSE, FALSE),  -- dossiers: son propre dossier
(9, 13, TRUE, TRUE, TRUE, FALSE, FALSE, FALSE),    -- candidatures: soumettre
(9, 15, TRUE, TRUE, TRUE, FALSE, FALSE, FALSE),    -- rapports: rédiger
(9, 17, TRUE, FALSE, FALSE, FALSE, FALSE, FALSE),  -- soutenances: consulter
(9, 18, TRUE, FALSE, FALSE, FALSE, FALSE, FALSE),  -- notes: consulter
(9, 19, TRUE, FALSE, FALSE, FALSE, TRUE, FALSE),   -- paiements: consulter + télécharger reçus
(9, 23, TRUE, TRUE, TRUE, FALSE, FALSE, FALSE),    -- messages
(9, 29, TRUE, TRUE, FALSE, FALSE, FALSE, FALSE)    -- reclamations: déposer
ON DUPLICATE KEY UPDATE peut_lire = VALUES(peut_lire), peut_creer = VALUES(peut_creer);
