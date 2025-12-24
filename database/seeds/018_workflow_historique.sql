-- =====================================================
-- Seed: 018_workflow_historique.sql
-- Purpose: Historique des transitions workflow et alertes
-- Date: 2025-12-24
-- Ref: Synthèse.txt - Traçabilité workflow
-- =====================================================

-- Historique des transitions workflow
INSERT INTO workflow_historique (id_historique, dossier_id, etat_source_id, etat_cible_id, transition_id, utilisateur_id, commentaire, snapshot_json) VALUES
-- Dossier 1 - Parcours complet jusqu'au diplôme
(1, 1, NULL, 1, NULL, 30, 'Inscription initiale', '{"etudiant": "KONE Adama", "date": "2024-09-15"}'),
(2, 1, 1, 2, 1, 100, 'Candidature soumise', '{"theme": "Système de gestion de stock avec ML"}'),
(3, 1, 2, 3, 2, 30, 'Candidature validée par scolarité', '{"paiement": "complet"}'),
(4, 1, 3, 4, 4, 30, 'Paiement validé', '{"montant": 550000}'),
(5, 1, 4, 5, 6, 20, 'Format validé par communication', '{"pages": 85, "format": "conforme"}'),
(6, 1, 5, 6, 8, 80, 'Évaluation commission démarrée', '{"session_id": 1}'),
(7, 1, 6, 7, 9, 80, 'Rapport validé par la commission', '{"vote": "unanime", "tour": 1}'),
(8, 1, 7, 8, 12, 30, 'Demande avis encadreur', '{"encadreur": "Dr. KOUASSI Aya Marie"}'),
(9, 1, 8, 9, 13, 70, 'Avis favorable encadreur', '{"commentaire": "Prêt pour soutenance"}'),
(10, 1, 9, 10, 15, 80, 'Jury en constitution', '{"president": "Prof. KOFFI Kouamé"}'),
(11, 1, 10, 11, 16, 30, 'Soutenance planifiée', '{"date": "2024-12-10", "salle": "A102"}'),
(12, 1, 11, 12, 18, 80, 'Soutenance démarrée', '{"code_valide": true}'),
(13, 1, 12, 13, 20, 80, 'Soutenance terminée', '{"note_moyenne": 16.83}'),
(14, 1, 13, 14, 21, 30, 'Diplôme délivré', '{"mention": "Très Bien"}'),

-- Dossier 2 - Jusqu'à soutenance terminée
(15, 2, NULL, 1, NULL, 30, 'Inscription initiale', '{"etudiant": "SANGARE Fatou"}'),
(16, 2, 1, 2, 1, 101, 'Candidature soumise', '{"theme": "Plateforme e-banking sécurisée"}'),
(17, 2, 2, 3, 2, 31, 'Candidature validée', '{}'),
(18, 2, 3, 4, 4, 31, 'Paiement validé', '{}'),
(19, 2, 4, 5, 6, 20, 'Format validé', '{}'),
(20, 2, 5, 6, 8, 80, 'Évaluation démarrée', '{}'),
(21, 2, 6, 7, 9, 80, 'Rapport validé', '{}'),
(22, 2, 7, 8, 12, 30, 'Demande avis', '{}'),
(23, 2, 8, 9, 13, 71, 'Avis favorable', '{}'),
(24, 2, 9, 10, 15, 80, 'Jury en constitution', '{}'),
(25, 2, 10, 11, 16, 30, 'Soutenance planifiée', '{}'),
(26, 2, 11, 12, 18, 80, 'Soutenance démarrée', '{}'),
(27, 2, 12, 13, 20, 80, 'Soutenance terminée', '{"note_moyenne": 15.03}'),

-- Dossier 3 - Jusqu'à soutenance planifiée
(28, 3, NULL, 1, NULL, 30, 'Inscription initiale', '{"etudiant": "BROU Jean-Pierre"}'),
(29, 3, 1, 2, 1, 102, 'Candidature soumise', '{}'),
(30, 3, 2, 3, 2, 30, 'Candidature validée', '{}'),
(31, 3, 3, 4, 4, 30, 'Paiement validé', '{}'),
(32, 3, 4, 5, 6, 21, 'Format validé', '{}'),
(33, 3, 5, 6, 8, 80, 'Évaluation démarrée', '{}'),
(34, 3, 6, 7, 9, 80, 'Rapport validé', '{}'),
(35, 3, 7, 8, 12, 30, 'Demande avis', '{}'),
(36, 3, 8, 9, 13, 72, 'Avis favorable', '{}'),
(37, 3, 9, 10, 15, 80, 'Jury en constitution', '{}'),
(38, 3, 10, 11, 16, 30, 'Soutenance planifiée', '{"date": "2024-12-20"}'),

-- Dossiers 4-12 avec parcours partiels
(39, 4, NULL, 1, NULL, 30, 'Inscription', '{"etudiant": "ASSI Marie-Claire"}'),
(40, 4, 1, 2, 1, 103, 'Candidature', '{}'),
(41, 4, 2, 3, 2, 31, 'Validée scolarité', '{}'),
(42, 4, 3, 4, 4, 31, 'Paiement OK', '{}'),
(43, 4, 4, 5, 6, 20, 'Format OK', '{}'),
(44, 4, 5, 6, 8, 80, 'Évaluation', '{}'),
(45, 4, 6, 7, 9, 80, 'Rapport validé', '{}'),
(46, 4, 7, 8, 12, 30, 'Demande avis', '{}'),
(47, 4, 8, 9, 13, 73, 'Avis favorable', '{}'),
(48, 4, 9, 10, 15, 80, 'Jury en constitution', '{}')
ON DUPLICATE KEY UPDATE 
    commentaire = VALUES(commentaire),
    snapshot_json = VALUES(snapshot_json);

-- Alertes workflow
INSERT INTO workflow_alertes (id_alerte, dossier_id, etat_id, type_alerte, envoyee, envoyee_le) VALUES
-- Alertes déjà envoyées
(1, 6, 8, '50_pourcent', TRUE, '2024-11-27 09:00:00'),
(2, 10, 4, '50_pourcent', TRUE, '2024-11-06 10:00:00'),
(3, 11, 3, '80_pourcent', TRUE, '2024-11-04 14:00:00'),
(4, 12, 2, '100_pourcent', TRUE, '2024-11-04 14:00:00'),

-- Alertes en attente
(5, 6, 8, '80_pourcent', FALSE, NULL),
(6, 18, 8, '50_pourcent', FALSE, NULL),
(7, 4, 10, '50_pourcent', FALSE, NULL),
(8, 20, 10, '50_pourcent', FALSE, NULL)
ON DUPLICATE KEY UPDATE 
    envoyee = VALUES(envoyee),
    envoyee_le = VALUES(envoyee_le);
