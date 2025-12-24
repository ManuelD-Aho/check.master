-- =====================================================
-- Seed: 001_referentiels_immuables.sql
-- Purpose: Référentiels immuables (données de base)
-- Date: 2025-12-24
-- =====================================================

-- Niveaux d'études
INSERT INTO niveau_etude (id_niveau, lib_niveau, description, ordre_niveau) VALUES
(1, 'Licence 1', 'Première année de licence', 1),
(2, 'Licence 2', 'Deuxième année de licence', 2),
(3, 'Licence 3', 'Troisième année de licence', 3),
(4, 'Master 1', 'Première année de master', 4),
(5, 'Master 2', 'Deuxième année de master', 5),
(6, 'Doctorat', 'Formation doctorale', 6)
ON DUPLICATE KEY UPDATE lib_niveau = VALUES(lib_niveau), ordre_niveau = VALUES(ordre_niveau);

-- Grades enseignants
INSERT INTO grades (id_grade, lib_grade, niveau_hierarchique, actif) VALUES
(1, 'Assistant', 1, TRUE),
(2, 'Maître-Assistant', 2, TRUE),
(3, 'Maître de Conférences', 3, TRUE),
(4, 'Professeur Titulaire', 4, TRUE),
(5, 'Professeur Émérite', 5, TRUE)
ON DUPLICATE KEY UPDATE lib_grade = VALUES(lib_grade), niveau_hierarchique = VALUES(niveau_hierarchique);

-- Fonctions
INSERT INTO fonctions (id_fonction, lib_fonction, description, actif) VALUES
(1, 'Enseignant', 'Enseignant permanent', TRUE),
(2, 'Responsable de filière', 'Responsable de filière/département', TRUE),
(3, 'Responsable de niveau', 'Responsable d''un niveau d''études', TRUE),
(4, 'Directeur adjoint', 'Directeur adjoint de l''UFR', TRUE),
(5, 'Doyen', 'Doyen de l''UFR', TRUE),
(6, 'Secrétaire', 'Personnel de secrétariat', TRUE),
(7, 'Agent de scolarité', 'Agent du service scolarité', TRUE),
(8, 'Agent communication', 'Agent du service communication', TRUE)
ON DUPLICATE KEY UPDATE lib_fonction = VALUES(lib_fonction), description = VALUES(description);

-- Spécialités
INSERT INTO specialites (id_specialite, lib_specialite, description, actif) VALUES
(1, 'Informatique', 'Sciences informatiques', TRUE),
(2, 'MIAGE', 'Méthodes Informatiques Appliquées à la Gestion des Entreprises', TRUE),
(3, 'Mathématiques', 'Sciences mathématiques', TRUE),
(4, 'Statistiques', 'Sciences statistiques', TRUE),
(5, 'Recherche Opérationnelle', 'Optimisation et aide à la décision', TRUE)
ON DUPLICATE KEY UPDATE lib_specialite = VALUES(lib_specialite), description = VALUES(description);

-- Niveau d'accès aux données
INSERT INTO niveau_acces_donnees (id_niv_acces_donnee, lib_niveau_acces, description) VALUES
(1, 'Lecture seule', 'Peut uniquement consulter les données'),
(2, 'Lecture/Écriture', 'Peut consulter et modifier les données'),
(3, 'Complet', 'Accès total incluant la suppression'),
(4, 'Administrateur', 'Accès système complet')
ON DUPLICATE KEY UPDATE lib_niveau_acces = VALUES(lib_niveau_acces), description = VALUES(description);

-- Statuts jury
INSERT INTO statut_jury (id_statut, lib_statut, description) VALUES
(1, 'En constitution', 'Jury en cours de formation'),
(2, 'Complet', 'Jury complet et validé'),
(3, 'Actif', 'Jury prêt pour la soutenance'),
(4, 'Terminé', 'Jury ayant terminé sa mission')
ON DUPLICATE KEY UPDATE lib_statut = VALUES(lib_statut), description = VALUES(description);

-- Salles par défaut
INSERT INTO salles (id_salle, nom_salle, batiment, capacite, equipement_json, actif) VALUES
(1, 'Salle A101', 'Bâtiment A', 30, '{"video_projecteur": true, "wifi": true}', TRUE),
(2, 'Salle A102', 'Bâtiment A', 50, '{"video_projecteur": true, "wifi": true, "visioconference": true}', TRUE),
(3, 'Amphithéâtre 1', 'Bâtiment Principal', 200, '{"video_projecteur": true, "micro": true}', TRUE),
(4, 'Salle Informatique B201', 'Bâtiment B', 25, '{"ordinateurs": 25, "wifi": true}', TRUE)
ON DUPLICATE KEY UPDATE nom_salle = VALUES(nom_salle), capacite = VALUES(capacite);

-- Année académique par défaut
INSERT INTO annee_academique (id_annee_acad, lib_annee_acad, date_debut, date_fin, est_active) VALUES
(1, '2024-2025', '2024-09-01', '2025-08-31', TRUE),
(2, '2025-2026', '2025-09-01', '2026-08-31', FALSE)
ON DUPLICATE KEY UPDATE lib_annee_acad = VALUES(lib_annee_acad), est_active = VALUES(est_active);
