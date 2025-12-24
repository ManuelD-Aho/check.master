-- =====================================================
-- Seed: 016_jury_soutenances.sql
-- Purpose: Jurys, soutenances et notes
-- Date: 2025-12-24
-- Ref: Synthèse.txt - Workflow soutenance
-- =====================================================

-- Membres de jury
INSERT INTO jury_membres (id_membre_jury, dossier_id, enseignant_id, role_jury, statut_acceptation, date_invitation, date_reponse) VALUES
-- Jury dossier 1 (KONE Adama) - Soutenance terminée, diplôme délivré
(1, 1, 1, 'PRESIDENT', 'Accepte', '2024-11-20', '2024-11-21'),
(2, 1, 6, 'DIRECTEUR', 'Accepte', '2024-11-20', '2024-11-21'),
(3, 1, 7, 'RAPPORTEUR', 'Accepte', '2024-11-20', '2024-11-22'),
(4, 1, 13, 'EXAMINATEUR', 'Accepte', '2024-11-20', '2024-11-21'),
(5, 1, 29, 'MAITRE_STAGE', 'Accepte', '2024-11-20', '2024-11-23'),

-- Jury dossier 2 (SANGARE Fatou) - Soutenance terminée
(6, 2, 2, 'PRESIDENT', 'Accepte', '2024-11-22', '2024-11-23'),
(7, 2, 8, 'DIRECTEUR', 'Accepte', '2024-11-22', '2024-11-23'),
(8, 2, 9, 'RAPPORTEUR', 'Accepte', '2024-11-22', '2024-11-24'),
(9, 2, 14, 'EXAMINATEUR', 'Accepte', '2024-11-22', '2024-11-23'),
(10, 2, 30, 'MAITRE_STAGE', 'Accepte', '2024-11-22', '2024-11-25'),

-- Jury dossier 3 (BROU Jean-Pierre) - Soutenance planifiée
(11, 3, 3, 'PRESIDENT', 'Accepte', '2024-11-25', '2024-11-26'),
(12, 3, 10, 'DIRECTEUR', 'Accepte', '2024-11-25', '2024-11-26'),
(13, 3, 11, 'RAPPORTEUR', 'Accepte', '2024-11-25', '2024-11-27'),
(14, 3, 15, 'EXAMINATEUR', 'Accepte', '2024-11-25', '2024-11-26'),
(15, 3, 29, 'MAITRE_STAGE', 'Accepte', '2024-11-25', '2024-11-28'),

-- Jury dossier 4 (ASSI Marie-Claire) - Jury en constitution
(16, 4, 4, 'PRESIDENT', 'Accepte', '2024-11-28', '2024-11-29'),
(17, 4, 12, 'DIRECTEUR', 'Accepte', '2024-11-28', '2024-11-29'),
(18, 4, 6, 'RAPPORTEUR', 'Invite', '2024-11-28', NULL),
(19, 4, 16, 'EXAMINATEUR', 'Accepte', '2024-11-28', '2024-11-30'),
(20, 4, 30, 'MAITRE_STAGE', 'Invite', '2024-11-28', NULL),

-- Jury dossier 5 (KONAN Yves) - Prêt pour jury (pas encore constitué)
(21, 5, 5, 'PRESIDENT', 'Invite', '2024-12-01', NULL),
(22, 5, 7, 'DIRECTEUR', 'Accepte', '2024-12-01', '2024-12-02'),
(23, 5, 8, 'RAPPORTEUR', 'Invite', '2024-12-01', NULL),
(24, 5, 17, 'EXAMINATEUR', 'Invite', '2024-12-01', NULL)
ON DUPLICATE KEY UPDATE 
    role_jury = VALUES(role_jury),
    statut_acceptation = VALUES(statut_acceptation);

-- Soutenances
INSERT INTO soutenances (id_soutenance, dossier_id, date_soutenance, lieu, salle_id, duree_minutes, statut, pv_genere, pv_chemin) VALUES
(1, 1, '2024-12-10 09:00:00', 'Salle A102', 2, 60, 'Terminee', TRUE, 'storage/pv_soutenance/2024/pv_soutenance_001.pdf'),
(2, 2, '2024-12-12 14:00:00', 'Salle A101', 1, 60, 'Terminee', TRUE, 'storage/pv_soutenance/2024/pv_soutenance_002.pdf'),
(3, 3, '2024-12-20 10:00:00', 'Amphithéâtre 1', 3, 60, 'Planifiee', FALSE, NULL),
(4, 4, '2024-12-22 09:00:00', 'Salle A102', 2, 60, 'Planifiee', FALSE, NULL)
ON DUPLICATE KEY UPDATE 
    statut = VALUES(statut),
    pv_genere = VALUES(pv_genere);

-- Notes de soutenance
INSERT INTO notes_soutenance (id_note, soutenance_id, membre_jury_id, note_fond, note_forme, note_soutenance, note_finale, mention, commentaire) VALUES
-- Soutenance 1 - KONE Adama (Mention Très Bien)
(1, 1, 1, 17.00, 16.50, 17.00, 16.83, 'Très Bien', 'Excellent travail de recherche appliquée'),
(2, 1, 2, 16.50, 16.00, 17.50, 16.67, 'Très Bien', 'Bonne maîtrise du sujet'),
(3, 1, 3, 17.50, 17.00, 16.50, 17.00, 'Très Bien', 'Analyse pertinente'),
(4, 1, 4, 16.00, 15.50, 16.00, 15.83, 'Bien', 'Présentation claire'),
(5, 1, 5, 17.00, 16.50, 17.00, 16.83, 'Très Bien', 'Bon lien théorie-pratique'),

-- Soutenance 2 - SANGARE Fatou (Mention Bien)
(6, 2, 6, 15.50, 15.00, 15.50, 15.33, 'Bien', 'Travail solide'),
(7, 2, 7, 14.50, 15.00, 15.00, 14.83, 'Bien', 'Méthodologie à améliorer'),
(8, 2, 8, 15.00, 14.50, 16.00, 15.17, 'Bien', 'Bonne présentation orale'),
(9, 2, 9, 14.00, 14.50, 14.50, 14.33, 'Bien', 'Contenu pertinent'),
(10, 2, 10, 15.50, 15.50, 15.50, 15.50, 'Bien', 'Perspective professionnelle claire')
ON DUPLICATE KEY UPDATE 
    note_finale = VALUES(note_finale),
    mention = VALUES(mention);

-- Décisions du jury
INSERT INTO decisions_jury (id_decision, soutenance_id, decision, delai_corrections, commentaires) VALUES
(1, 1, 'Admis', NULL, 'Admis avec mention Très Bien. Félicitations du jury pour la qualité du travail.'),
(2, 2, 'Corrections_mineures', 15, 'Admis sous réserve de corrections mineures à apporter dans un délai de 15 jours.')
ON DUPLICATE KEY UPDATE 
    decision = VALUES(decision),
    commentaires = VALUES(commentaires);
