-- =====================================================
-- Seed: 003_traitements_actions.sql
-- Purpose: Seed les actions, traitements et rattachements
-- Date: 2025-12-24
-- =====================================================

-- Les 6 actions système (table: action - singulier)
INSERT INTO action (id_action, lib_action, description) VALUES
(1, 'Lire', 'Consulter et visualiser les données'),
(2, 'Creer', 'Créer de nouvelles entrées'),
(3, 'Modifier', 'Modifier les entrées existantes'),
(4, 'Supprimer', 'Supprimer des entrées'),
(5, 'Valider', 'Valider et approuver des entrées'),
(6, 'Exporter', 'Exporter des données vers des fichiers')
ON DUPLICATE KEY UPDATE 
    lib_action = VALUES(lib_action),
    description = VALUES(description);

-- Les traitements par module (table: traitement - singulier)
-- Note: la table traitement n'a pas de colonne 'module', on utilise la description pour documenter le module
INSERT INTO traitement (id_traitement, lib_traitement, description, ordre_traitement, actif) VALUES
-- Module Authentification (ordre 1-4)
(1, 'Gestion Utilisateurs', 'CRUD des comptes utilisateurs (module: authentification)', 1, TRUE),
(2, 'Gestion Sessions', 'Surveillance des sessions actives (module: authentification)', 2, TRUE),
(3, 'Gestion Groupes', 'Administration des groupes et rôles (module: authentification)', 3, TRUE),
(4, 'Gestion Permissions', 'Configuration des droits d''accès (module: authentification)', 4, TRUE),
-- Module Entités Académiques (ordre 5-10)
(5, 'Gestion Étudiants', 'Gestion des dossiers étudiants (module: entites_academiques)', 5, TRUE),
(6, 'Gestion Enseignants', 'Gestion des enseignants (module: entites_academiques)', 6, TRUE),
(7, 'Gestion Personnel', 'Gestion du personnel administratif (module: entites_academiques)', 7, TRUE),
(8, 'Gestion Entreprises', 'Référentiel des entreprises partenaires (module: entites_academiques)', 8, TRUE),
(9, 'Gestion Années Académiques', 'Configuration des années académiques (module: entites_academiques)', 9, TRUE),
(10, 'Gestion UE/ECUE', 'Gestion des unités d''enseignement (module: entites_academiques)', 10, TRUE),
-- Module Workflow (ordre 11-13)
(11, 'Gestion Workflow', 'Configuration états et transitions (module: workflow)', 11, TRUE),
(12, 'Gestion Dossiers', 'Suivi des dossiers étudiants (module: workflow)', 12, TRUE),
(13, 'Gestion Candidatures', 'Traitement des candidatures (module: workflow)', 13, TRUE),
-- Module Commission (ordre 14-15)
(14, 'Gestion Sessions Commission', 'Planification et gestion des sessions (module: commission)', 14, TRUE),
(15, 'Évaluation Rapports', 'Évaluation et vote sur les rapports (module: commission)', 15, TRUE),
-- Module Soutenance (ordre 16-18)
(16, 'Gestion Jurys', 'Constitution des jurys (module: soutenance)', 16, TRUE),
(17, 'Planification Soutenances', 'Planification des soutenances (module: soutenance)', 17, TRUE),
(18, 'Gestion Notes', 'Saisie et consultation des notes (module: soutenance)', 18, TRUE),
-- Module Financier (ordre 19-21)
(19, 'Gestion Paiements', 'Enregistrement des paiements (module: financier)', 19, TRUE),
(20, 'Gestion Pénalités', 'Gestion des pénalités de retard (module: financier)', 20, TRUE),
(21, 'Gestion Exonérations', 'Gestion des exonérations (module: financier)', 21, TRUE),
-- Module Communication (ordre 22-24)
(22, 'Gestion Notifications', 'Envoi et suivi des notifications (module: communication)', 22, TRUE),
(23, 'Messagerie Interne', 'Messages entre utilisateurs (module: communication)', 23, TRUE),
(24, 'Gestion Calendrier', 'Calendrier académique (module: communication)', 24, TRUE),
-- Module Documents (ordre 25-26)
(25, 'Génération Documents', 'Génération de PDFs et documents (module: documents)', 25, TRUE),
(26, 'Gestion Archives', 'Archivage et intégrité (module: documents)', 26, TRUE),
-- Module Administration (ordre 27-30)
(27, 'Configuration Système', 'Paramètres système (module: administration)', 27, TRUE),
(28, 'Consultation Audit', 'Logs et traçabilité (module: administration)', 28, TRUE),
(29, 'Gestion Réclamations', 'Traitement des réclamations (module: administration)', 29, TRUE),
(30, 'Maintenance', 'Opérations de maintenance (module: administration)', 30, TRUE)
ON DUPLICATE KEY UPDATE 
    lib_traitement = VALUES(lib_traitement),
    description = VALUES(description),
    ordre_traitement = VALUES(ordre_traitement),
    actif = VALUES(actif);

-- Rattachements: groupe_utilisateur → traitement → action
-- Administrateur (groupe 1) - accès total à tout
INSERT INTO rattacher (id_GU, id_traitement, id_action) 
SELECT 1, t.id_traitement, a.id_action 
FROM traitement t CROSS JOIN action a
ON DUPLICATE KEY UPDATE id_GU = 1;

-- Secrétaire (groupe 2) - Documents, Archives
INSERT INTO rattacher (id_GU, id_traitement, id_action) VALUES
(2, 25, 1), (2, 25, 2), (2, 25, 6), -- Documents: Lire, Créer, Exporter
(2, 26, 1), (2, 26, 6)              -- Archives: Lire, Exporter
ON DUPLICATE KEY UPDATE id_GU = VALUES(id_GU);

-- Communication (groupe 3) - Rapports (format), Notifications
INSERT INTO rattacher (id_GU, id_traitement, id_action) VALUES
(3, 15, 1), (3, 15, 5),             -- Rapports: Lire, Valider (format)
(3, 22, 1), (3, 22, 2), (3, 22, 3)  -- Notifications: Lire, Créer, Modifier
ON DUPLICATE KEY UPDATE id_GU = VALUES(id_GU);

-- Scolarité (groupe 4) - Étudiants, Candidatures, Paiements, Pénalités
INSERT INTO rattacher (id_GU, id_traitement, id_action) VALUES
(4, 5, 1), (4, 5, 2), (4, 5, 3), (4, 5, 6),   -- Étudiants: CRUD sauf supprimer
(4, 13, 1), (4, 13, 2), (4, 13, 3), (4, 13, 5), (4, 13, 6), -- Candidatures: tout sauf supprimer
(4, 19, 1), (4, 19, 2), (4, 19, 3), (4, 19, 6), -- Paiements: CRUD sauf supprimer
(4, 20, 1), (4, 20, 2), (4, 20, 3), (4, 20, 6), -- Pénalités: CRUD sauf supprimer
(4, 25, 1), (4, 25, 6)                          -- Documents: Lire, Exporter
ON DUPLICATE KEY UPDATE id_GU = VALUES(id_GU);

-- Responsable Filière (groupe 5) - Supervision filière
INSERT INTO rattacher (id_GU, id_traitement, id_action) VALUES
(5, 5, 1), (5, 5, 6),              -- Étudiants: Lire, Exporter
(5, 6, 1), (5, 6, 6),              -- Enseignants: Lire, Exporter
(5, 12, 1), (5, 12, 6),            -- Dossiers: Lire, Exporter
(5, 15, 1), (5, 15, 6),            -- Rapports: Lire, Exporter
(5, 16, 1), (5, 16, 5),            -- Jurys: Lire, Valider
(5, 17, 1), (5, 17, 5)             -- Soutenances: Lire, Valider
ON DUPLICATE KEY UPDATE id_GU = VALUES(id_GU);

-- Responsable Niveau (groupe 6) - Gestion niveau
INSERT INTO rattacher (id_GU, id_traitement, id_action) VALUES
(6, 5, 1), (6, 5, 6),              -- Étudiants: Lire, Exporter
(6, 12, 1), (6, 12, 3), (6, 12, 6), -- Dossiers: Lire, Modifier, Exporter
(6, 15, 1), (6, 15, 6)             -- Rapports: Lire, Exporter
ON DUPLICATE KEY UPDATE id_GU = VALUES(id_GU);

-- Commission (groupe 7) - Évaluation rapports
INSERT INTO rattacher (id_GU, id_traitement, id_action) VALUES
(7, 14, 1), (7, 14, 3),            -- Sessions: Lire, Modifier
(7, 15, 1), (7, 15, 3), (7, 15, 5) -- Rapports: Lire, Modifier (annotation), Valider
ON DUPLICATE KEY UPDATE id_GU = VALUES(id_GU);

-- Enseignant (groupe 8) - Supervision, Participation jury
INSERT INTO rattacher (id_GU, id_traitement, id_action) VALUES
(8, 5, 1),                         -- Étudiants: Lire
(8, 15, 1), (8, 15, 3),            -- Rapports: Lire, Modifier (annotation)
(8, 16, 1), (8, 16, 3),            -- Jurys: Lire, Modifier
(8, 17, 1),                        -- Soutenances: Lire
(8, 23, 1), (8, 23, 2), (8, 23, 3) -- Messages: Lire, Créer, Modifier
ON DUPLICATE KEY UPDATE id_GU = VALUES(id_GU);

-- Étudiant (groupe 9) - Accès propre dossier
INSERT INTO rattacher (id_GU, id_traitement, id_action) VALUES
(9, 12, 1),                        -- Dossiers: Lire (propre)
(9, 13, 1), (9, 13, 2), (9, 13, 3), -- Candidatures: Lire, Créer, Modifier (propre)
(9, 15, 1), (9, 15, 2), (9, 15, 3), -- Rapports: Lire, Créer, Modifier (propre)
(9, 17, 1),                        -- Soutenances: Lire (propre)
(9, 18, 1),                        -- Notes: Lire (propre)
(9, 19, 1), (9, 19, 6),            -- Paiements: Lire, Exporter (reçus)
(9, 23, 1), (9, 23, 2), (9, 23, 3), -- Messages: Lire, Créer, Modifier
(9, 29, 1), (9, 29, 2)             -- Réclamations: Lire, Créer
ON DUPLICATE KEY UPDATE id_GU = VALUES(id_GU);

-- Président Commission (groupe 10) - Constitution jurys
INSERT INTO rattacher (id_GU, id_traitement, id_action) VALUES
(10, 14, 1), (10, 14, 2), (10, 14, 3), (10, 14, 5), -- Sessions: tout sauf supprimer
(10, 15, 1), (10, 15, 5),          -- Rapports: Lire, Valider
(10, 16, 1), (10, 16, 2), (10, 16, 3), (10, 16, 5) -- Jurys: tout sauf supprimer
ON DUPLICATE KEY UPDATE id_GU = VALUES(id_GU);

-- Président Jury (groupe 11) - Saisie notes jour J (rôle temporaire)
INSERT INTO rattacher (id_GU, id_traitement, id_action) VALUES
(11, 16, 1),                       -- Jurys: Lire
(11, 17, 1), (11, 17, 3),          -- Soutenances: Lire, Modifier
(11, 18, 1), (11, 18, 2), (11, 18, 3) -- Notes: Lire, Créer, Modifier
ON DUPLICATE KEY UPDATE id_GU = VALUES(id_GU);

-- Directeur Mémoire (groupe 12) - Direction scientifique
INSERT INTO rattacher (id_GU, id_traitement, id_action) VALUES
(12, 5, 1),                        -- Étudiants: Lire
(12, 12, 1),                       -- Dossiers: Lire
(12, 15, 1), (12, 15, 3), (12, 15, 5), -- Rapports: Lire, Modifier, Valider
(12, 23, 1), (12, 23, 2), (12, 23, 3)  -- Messages: Lire, Créer, Modifier
ON DUPLICATE KEY UPDATE id_GU = VALUES(id_GU);

-- Encadreur Pédagogique (groupe 13) - Accompagnement étudiant
INSERT INTO rattacher (id_GU, id_traitement, id_action) VALUES
(13, 5, 1),                        -- Étudiants: Lire
(13, 12, 1),                       -- Dossiers: Lire
(13, 15, 1), (13, 15, 3),          -- Rapports: Lire, Modifier (annotation)
(13, 23, 1), (13, 23, 2), (13, 23, 3)  -- Messages: Lire, Créer, Modifier
ON DUPLICATE KEY UPDATE id_GU = VALUES(id_GU);
