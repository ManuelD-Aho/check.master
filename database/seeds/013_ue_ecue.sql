-- =====================================================
-- Seed: 013_ue_ecue.sql
-- Purpose: Unités d'enseignement et éléments constitutifs Master MIAGE
-- Date: 2025-12-24
-- Ref: Synthèse.txt - Structure académique
-- =====================================================

-- UE Master 1 - Semestre 1
INSERT INTO ue (id_ue, code_ue, lib_ue, credits, niveau_id, semestre_id) VALUES
(1, 'UE-M1S1-01', 'Conception et Programmation Orientée Objet', 6, 4, 1),
(2, 'UE-M1S1-02', 'Bases de Données Avancées', 6, 4, 1),
(3, 'UE-M1S1-03', 'Réseaux et Sécurité', 5, 4, 1),
(4, 'UE-M1S1-04', 'Mathématiques pour l''Informatique', 5, 4, 1),
(5, 'UE-M1S1-05', 'Gestion de Projet', 4, 4, 1),
(6, 'UE-M1S1-06', 'Anglais Professionnel', 4, 4, 1),

-- UE Master 1 - Semestre 2
(7, 'UE-M1S2-01', 'Architecture Logicielle', 6, 4, 2),
(8, 'UE-M1S2-02', 'Intelligence Artificielle et Machine Learning', 6, 4, 2),
(9, 'UE-M1S2-03', 'Systèmes d''Information', 5, 4, 2),
(10, 'UE-M1S2-04', 'Économie Numérique', 5, 4, 2),
(11, 'UE-M1S2-05', 'Stage M1', 8, 4, 2),

-- UE Master 2 - Semestre 3
(12, 'UE-M2S1-01', 'Visualisation et Analyse de Données', 6, 5, 1),
(13, 'UE-M2S1-02', 'Cloud Computing et DevOps', 6, 5, 1),
(14, 'UE-M2S1-03', 'Gouvernance des SI', 5, 5, 1),
(15, 'UE-M2S1-04', 'Entrepreneuriat et Innovation', 5, 5, 1),
(16, 'UE-M2S1-05', 'Audit et Sécurité des SI', 4, 5, 1),
(17, 'UE-M2S1-06', 'Méthodologie de Recherche', 4, 5, 1),

-- UE Master 2 - Semestre 4
(18, 'UE-M2S2-01', 'Stage et Mémoire de Fin d''Études', 30, 5, 2)
ON DUPLICATE KEY UPDATE 
    lib_ue = VALUES(lib_ue),
    credits = VALUES(credits),
    niveau_id = VALUES(niveau_id),
    semestre_id = VALUES(semestre_id);

-- ECUE Master 1 Semestre 1
INSERT INTO ecue (id_ecue, code_ecue, lib_ecue, ue_id, credits) VALUES
-- UE Conception POO
(1, 'ECUE-M1S1-01A', 'Programmation Java Avancée', 1, 3),
(2, 'ECUE-M1S1-01B', 'Design Patterns', 1, 3),
-- UE Bases de Données
(3, 'ECUE-M1S1-02A', 'SQL Avancé et Optimisation', 2, 3),
(4, 'ECUE-M1S1-02B', 'Bases de Données NoSQL', 2, 3),
-- UE Réseaux
(5, 'ECUE-M1S1-03A', 'Architecture Réseaux', 3, 2),
(6, 'ECUE-M1S1-03B', 'Sécurité des Réseaux', 3, 3),
-- UE Mathématiques
(7, 'ECUE-M1S1-04A', 'Algorithmique Avancée', 4, 3),
(8, 'ECUE-M1S1-04B', 'Probabilités et Statistiques', 4, 2),
-- UE Gestion de Projet
(9, 'ECUE-M1S1-05A', 'Méthodes Agiles', 5, 2),
(10, 'ECUE-M1S1-05B', 'Outils de Gestion de Projet', 5, 2),
-- UE Anglais
(11, 'ECUE-M1S1-06A', 'Anglais Technique', 6, 2),
(12, 'ECUE-M1S1-06B', 'Communication Professionnelle', 6, 2),

-- ECUE Master 1 Semestre 2
(13, 'ECUE-M1S2-01A', 'Architecture Microservices', 7, 3),
(14, 'ECUE-M1S2-01B', 'APIs et Web Services', 7, 3),
(15, 'ECUE-M1S2-02A', 'Machine Learning', 8, 3),
(16, 'ECUE-M1S2-02B', 'Deep Learning', 8, 3),
(17, 'ECUE-M1S2-03A', 'ERP et Progiciels', 9, 3),
(18, 'ECUE-M1S2-03B', 'Urbanisation des SI', 9, 2),
(19, 'ECUE-M1S2-04A', 'Marketing Digital', 10, 3),
(20, 'ECUE-M1S2-04B', 'E-Commerce', 10, 2),
(21, 'ECUE-M1S2-05A', 'Stage M1 en Entreprise', 11, 8),

-- ECUE Master 2 Semestre 3
(22, 'ECUE-M2S1-01A', 'Business Intelligence', 12, 3),
(23, 'ECUE-M2S1-01B', 'Data Visualization', 12, 3),
(24, 'ECUE-M2S1-02A', 'Cloud Computing AWS/Azure', 13, 3),
(25, 'ECUE-M2S1-02B', 'CI/CD et Containerisation', 13, 3),
(26, 'ECUE-M2S1-03A', 'Gouvernance IT', 14, 3),
(27, 'ECUE-M2S1-03B', 'ITIL et COBIT', 14, 2),
(28, 'ECUE-M2S1-04A', 'Création d''Entreprise', 15, 3),
(29, 'ECUE-M2S1-04B', 'Innovation et Stratégie', 15, 2),
(30, 'ECUE-M2S1-05A', 'Audit des SI', 16, 2),
(31, 'ECUE-M2S1-05B', 'Cybersécurité', 16, 2),
(32, 'ECUE-M2S1-06A', 'Méthodologie de Recherche', 17, 2),
(33, 'ECUE-M2S1-06B', 'Rédaction Scientifique', 17, 2),

-- ECUE Master 2 Semestre 4
(34, 'ECUE-M2S2-01A', 'Stage de Fin d''Études', 18, 15),
(35, 'ECUE-M2S2-01B', 'Mémoire et Soutenance', 18, 15)
ON DUPLICATE KEY UPDATE 
    lib_ecue = VALUES(lib_ecue),
    ue_id = VALUES(ue_id),
    credits = VALUES(credits);
