-- =====================================================
-- Seed: 007_demo_data.sql
-- Purpose: Données de démonstration (admin, année académique)
-- Date: 2025-12-19
-- =====================================================

-- Année académique active
INSERT INTO annee_academique (id_annee_acad, lib_annee_acad, date_debut, date_fin, est_active) VALUES
(1, '2024-2025', '2024-09-01', '2025-08-31', TRUE)
ON DUPLICATE KEY UPDATE est_active = TRUE;

-- Semestres
INSERT INTO semestre (id_semestre, lib_semestre, annee_acad_id, date_debut, date_fin) VALUES
(1, 'Semestre 1', 1, '2024-09-01', '2025-01-31'),
(2, 'Semestre 2', 1, '2025-02-01', '2025-06-30')
ON DUPLICATE KEY UPDATE annee_acad_id = VALUES(annee_acad_id);

-- Niveaux d'étude
INSERT INTO niveau_etude (id_niveau, lib_niveau, description, ordre_niveau) VALUES
(1, 'Licence 1', 'Première année de licence', 1),
(2, 'Licence 2', 'Deuxième année de licence', 2),
(3, 'Licence 3', 'Troisième année de licence', 3),
(4, 'Master 1', 'Première année de master', 4),
(5, 'Master 2', 'Deuxième année de master (mémoire)', 5)
ON DUPLICATE KEY UPDATE description = VALUES(description);

-- Personnel Admin (entité requise avant création utilisateur admin)
INSERT INTO personnel_admin (id_pers_admin, nom_pers, prenom_pers, email_pers, telephone_pers, actif) VALUES
(1, 'ADMIN', 'System', 'admin@checkmaster.ufhb.ci', '+225 00 00 00 00', TRUE)
ON DUPLICATE KEY UPDATE email_pers = VALUES(email_pers);

-- Utilisateur Admin par défaut
-- Mot de passe: CheckMaster2024! (hashé avec Argon2id)
-- Note: En production, changez ce mot de passe immédiatement !
INSERT INTO utilisateurs (
    id_utilisateur, 
    nom_utilisateur, 
    login_utilisateur, 
    mdp_utilisateur, 
    id_type_utilisateur, 
    id_GU, 
    statut_utilisateur, 
    doit_changer_mdp
) VALUES (
    1,
    'Administrateur Système',
    'admin@checkmaster.ufhb.ci',
    '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE',
    1,  -- Type: Administrateur
    1,  -- Groupe: Administrateur
    'Actif',
    TRUE  -- Doit changer mot de passe à la première connexion
)
ON DUPLICATE KEY UPDATE 
    statut_utilisateur = 'Actif';

-- Lier l'utilisateur au groupe
INSERT INTO utilisateurs_groupes (utilisateur_id, groupe_id, attribue_par, attribue_le) VALUES
(1, 1, 1, NOW())
ON DUPLICATE KEY UPDATE attribue_le = NOW();

-- Quelques spécialités de base
INSERT INTO specialites (id_specialite, lib_specialite, description, actif) VALUES
(1, 'Génie Logiciel', 'Conception et développement de logiciels', TRUE),
(2, 'Bases de Données', 'Administration et conception de bases de données', TRUE),
(3, 'Réseaux et Systèmes', 'Infrastructure réseau et systèmes', TRUE),
(4, 'Intelligence Artificielle', 'IA et Machine Learning', TRUE),
(5, 'Sécurité Informatique', 'Cybersécurité et protection des systèmes', TRUE)
ON DUPLICATE KEY UPDATE description = VALUES(description);

-- Quelques grades enseignants
INSERT INTO grades (id_grade, lib_grade, niveau_hierarchique, actif) VALUES
(1, 'Professeur Titulaire', 1, TRUE),
(2, 'Maître de Conférences', 2, TRUE),
(3, 'Maître Assistant', 3, TRUE),
(4, 'Assistant', 4, TRUE),
(5, 'Vacataire', 5, TRUE)
ON DUPLICATE KEY UPDATE niveau_hierarchique = VALUES(niveau_hierarchique);

-- Fonctions
INSERT INTO fonctions (id_fonction, lib_fonction, description, actif) VALUES
(1, 'Directeur', 'Directeur de département', TRUE),
(2, 'Responsable Filière', 'Responsable d''une filière', TRUE),
(3, 'Responsable Niveau', 'Responsable d''un niveau', TRUE),
(4, 'Enseignant-Chercheur', 'Enseignant et recherche', TRUE),
(5, 'Secrétaire', 'Secrétariat administratif', TRUE),
(6, 'Agent Scolarité', 'Service scolarité', TRUE)
ON DUPLICATE KEY UPDATE description = VALUES(description);

-- Message de bienvenue
SELECT 'CheckMaster seeded successfully!' AS message;
SELECT 'Admin login: admin@checkmaster.ufhb.ci' AS credentials;
SELECT 'Password: CheckMaster2024! (MUST CHANGE ON FIRST LOGIN)' AS warning;
