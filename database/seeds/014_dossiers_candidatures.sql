-- =====================================================
-- Seed: 014_dossiers_candidatures.sql
-- Purpose: Dossiers étudiants et candidatures de stage
-- Date: 2025-12-24
-- Ref: Synthèse.txt - Workflow candidature
-- =====================================================

-- Dossiers étudiants liés à l'année académique
INSERT INTO dossiers_etudiants (id_dossier, etudiant_id, annee_acad_id, etat_actuel_id, date_entree_etat, date_limite_etat) VALUES
-- Dossiers à différents états du workflow pour démo
(1, 1, 1, 14, '2024-12-15 10:00:00', NULL),  -- DIPLOME_DELIVRE (terminé)
(2, 2, 1, 13, '2024-12-10 14:30:00', NULL),  -- SOUTENANCE_TERMINEE
(3, 3, 1, 11, '2024-12-05 09:00:00', '2024-12-20 09:00:00'),  -- SOUTENANCE_PLANIFIEE
(4, 4, 1, 10, '2024-11-28 11:00:00', '2024-12-12 11:00:00'),  -- JURY_EN_CONSTITUTION
(5, 5, 1, 9, '2024-11-25 15:00:00', NULL),   -- PRET_POUR_JURY
(6, 6, 1, 8, '2024-11-20 10:30:00', '2024-12-04 10:30:00'),  -- ATTENTE_AVIS_ENCADREUR
(7, 7, 1, 7, '2024-11-15 14:00:00', NULL),   -- RAPPORT_VALIDE
(8, 8, 1, 6, '2024-11-10 16:00:00', '2024-11-11 16:00:00'),  -- EN_EVALUATION_COMMISSION
(9, 9, 1, 5, '2024-11-08 09:30:00', NULL),   -- EN_ATTENTE_COMMISSION
(10, 10, 1, 4, '2024-11-05 11:00:00', '2024-11-08 11:00:00'), -- FILTRE_COMMUNICATION
(11, 11, 1, 3, '2024-11-01 10:00:00', '2024-11-06 10:00:00'), -- VERIFICATION_SCOLARITE
(12, 12, 1, 2, '2024-10-28 14:00:00', '2024-11-04 14:00:00'), -- CANDIDATURE_SOUMISE
(13, 13, 1, 1, '2024-10-20 09:00:00', NULL),  -- INSCRIT (pas encore de candidature)
(14, 14, 1, 1, '2024-10-20 09:00:00', NULL),
(15, 15, 1, 1, '2024-10-20 09:00:00', NULL),
(16, 16, 1, 7, '2024-11-18 10:00:00', NULL),  -- RAPPORT_VALIDE
(17, 17, 1, 7, '2024-11-19 11:00:00', NULL),  -- RAPPORT_VALIDE
(18, 18, 1, 8, '2024-11-22 14:00:00', '2024-12-06 14:00:00'), -- ATTENTE_AVIS_ENCADREUR
(19, 19, 1, 9, '2024-11-26 16:00:00', NULL),  -- PRET_POUR_JURY
(20, 20, 1, 10, '2024-11-30 09:00:00', '2024-12-14 09:00:00') -- JURY_EN_CONSTITUTION
ON DUPLICATE KEY UPDATE 
    etat_actuel_id = VALUES(etat_actuel_id),
    date_entree_etat = VALUES(date_entree_etat);

-- Candidatures avec informations de stage
INSERT INTO candidatures (id_candidature, dossier_id, theme, entreprise_id, maitre_stage_nom, maitre_stage_email, maitre_stage_tel, date_debut_stage, date_fin_stage, date_soumission, validee_scolarite, date_valid_scolarite, validee_communication, date_valid_communication) VALUES
-- Candidatures validées et avancées
(1, 1, 'Développement d''un système de gestion de stock avec prédiction de la demande par Machine Learning', 1, 'KOUAME Didier', 'd.kouame@orange.ci', '+225 07 01 02 03', '2024-03-01', '2024-08-31', '2024-10-01', TRUE, '2024-10-05', TRUE, '2024-10-08'),
(2, 2, 'Mise en place d''une plateforme de e-banking sécurisée avec authentification biométrique', 4, 'DIALLO Aminata', 'a.diallo@sgci.ci', '+225 07 04 05 06', '2024-03-15', '2024-09-15', '2024-10-02', TRUE, '2024-10-06', TRUE, '2024-10-09'),
(3, 3, 'Conception d''un système de suivi de flotte par GPS avec interface web et mobile', 7, 'BAMBA Youssouf', 'y.bamba@quantech.ci', '+225 07 07 08 09', '2024-04-01', '2024-09-30', '2024-10-03', TRUE, '2024-10-07', TRUE, '2024-10-10'),
(4, 4, 'Implémentation d''un chatbot intelligent pour le service client utilisant NLP', 2, 'KONE Seydou', 's.kone@mtn.ci', '+225 07 10 11 12', '2024-03-01', '2024-08-31', '2024-10-04', TRUE, '2024-10-08', TRUE, '2024-10-11'),
(5, 5, 'Développement d''une application de gestion de portefeuille client pour conseillers bancaires', 5, 'YAO Christelle', 'c.yao@bicici.com', '+225 07 13 14 15', '2024-04-15', '2024-10-15', '2024-10-05', TRUE, '2024-10-09', TRUE, '2024-10-12'),
(6, 6, 'Conception d''un système de vote électronique sécurisé par blockchain', 21, 'TRAORE Mamadou', 'm.traore@men.gouv.ci', '+225 07 16 17 18', '2024-03-15', '2024-09-15', '2024-10-06', TRUE, '2024-10-10', TRUE, '2024-10-13'),
(7, 7, 'Développement d''une plateforme d''analyse des réseaux sociaux pour études marketing', 17, 'SORO Fatou', 'f.soro@jumia.com', '+225 07 19 20 21', '2024-04-01', '2024-09-30', '2024-10-07', TRUE, '2024-10-11', TRUE, '2024-10-14'),
(8, 8, 'Mise en place d''un système de détection de fraude par analyse comportementale', 6, 'GBAGBO Eric', 'e.gbagbo@ecobank.ci', '+225 07 22 23 24', '2024-03-01', '2024-08-31', '2024-10-08', TRUE, '2024-10-12', TRUE, '2024-10-15'),
(9, 9, 'Conception d''une application mobile de télémédecine pour zones rurales', 8, 'CISSE Aïcha', 'a.cisse@nsia.ci', '+225 07 25 26 27', '2024-04-15', '2024-10-15', '2024-10-09', TRUE, '2024-10-13', TRUE, '2024-10-16'),
(10, 10, 'Développement d''un système de gestion documentaire avec OCR et indexation automatique', 9, 'DIABATE Moussa', 'm.diabate@deloitte.ci', '+225 07 28 29 30', '2024-03-15', '2024-09-15', '2024-10-10', TRUE, '2024-10-14', TRUE, NULL),
(11, 11, 'Implémentation d''un ERP simplifié pour PME ivoiriennes', 10, 'KOUASSI Aya', 'a.kouassi@pwc.com', '+225 07 31 32 33', '2024-04-01', '2024-09-30', '2024-10-11', TRUE, '2024-10-15', FALSE, NULL),
(12, 12, 'Conception d''une plateforme de crowdfunding adaptée au contexte africain', 19, 'OUATTARA Ibrahim', 'i.ouattara@afriland.ci', '+225 07 34 35 36', '2024-03-01', '2024-08-31', '2024-10-12', FALSE, NULL, FALSE, NULL),
-- Candidatures plus récentes
(16, 16, 'Analyse prédictive de la consommation électrique par Deep Learning', 12, 'SANOGO Pierre', 'p.sanogo@cie.ci', '+225 07 40 41 42', '2024-04-01', '2024-09-30', '2024-10-16', TRUE, '2024-10-20', TRUE, '2024-10-23'),
(17, 17, 'Développement d''un système de gestion de la chaîne logistique portuaire', 13, 'BROU Constant', 'c.brou@paa-ci.org', '+225 07 43 44 45', '2024-03-15', '2024-09-15', '2024-10-17', TRUE, '2024-10-21', TRUE, '2024-10-24'),
(18, 18, 'Implémentation d''un système de réservation de vols avec tarification dynamique', 14, 'KONAN Estelle', 'e.konan@aircotedivoire.com', '+225 07 46 47 48', '2024-04-15', '2024-10-15', '2024-10-18', TRUE, '2024-10-22', TRUE, '2024-10-25'),
(19, 19, 'Conception d''une plateforme de paiement mobile interopérable', 18, 'DIARRA Moussa', 'm.diarra@wave.com', '+225 07 49 50 51', '2024-03-01', '2024-08-31', '2024-10-19', TRUE, '2024-10-23', TRUE, '2024-10-26'),
(20, 20, 'Développement d''un tableau de bord décisionnel pour dirigeants', 15, 'FOFANA Aminata', 'a.fofana@sifca.com', '+225 07 52 53 54', '2024-04-01', '2024-09-30', '2024-10-20', TRUE, '2024-10-24', TRUE, '2024-10-27')
ON DUPLICATE KEY UPDATE 
    theme = VALUES(theme),
    entreprise_id = VALUES(entreprise_id),
    validee_scolarite = VALUES(validee_scolarite),
    validee_communication = VALUES(validee_communication);
