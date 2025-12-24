-- =====================================================
-- Seed: 020_documents_archives.sql
-- Purpose: Documents générés, archives, historique entités
-- Date: 2025-12-24
-- Ref: Synthèse.txt - Gestion documentaire
-- =====================================================

-- Documents générés
INSERT INTO documents_generes (id_document, type_document, entite_type, entite_id, chemin_fichier, nom_fichier, taille_octets, hash_sha256, genere_par, genere_le) VALUES
-- Reçus de paiement
(1, 'recu_paiement', 'paiement', 1, 'storage/recus/2024/recu_001.pdf', 'recu_paiement_KONE_Adama_001.pdf', 45678, 'a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2', 30, '2024-09-15 10:35:00'),
(2, 'recu_paiement', 'paiement', 2, 'storage/recus/2024/recu_002.pdf', 'recu_paiement_SANGARE_Fatou_002.pdf', 45890, 'b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3', 30, '2024-09-16 11:20:00'),
(3, 'recu_paiement', 'paiement', 3, 'storage/recus/2024/recu_003.pdf', 'recu_paiement_BROU_JeanPierre_003.pdf', 44567, 'c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4', 31, '2024-09-17 09:45:00'),

-- Reçus de pénalité
(4, 'recu_penalite', 'penalite', 1, 'storage/recus_penalites/2024/penalite_001.pdf', 'penalite_LAGO_Constant_001.pdf', 32456, 'd4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5', 30, '2024-10-20 14:30:00'),

-- PV de commission
(5, 'pv_commission', 'session_commission', 1, 'storage/pv/2024/pv_session_001.pdf', 'PV_Commission_2024-10-28.pdf', 156789, 'e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6', 80, '2024-10-28 17:00:00'),
(6, 'pv_commission', 'session_commission', 2, 'storage/pv/2024/pv_session_002.pdf', 'PV_Commission_2024-11-04.pdf', 167890, 'f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1', 80, '2024-11-04 17:00:00'),
(7, 'pv_commission', 'session_commission', 3, 'storage/pv/2024/pv_session_003.pdf', 'PV_Commission_2024-11-11.pdf', 178901, 'a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2', 80, '2024-11-11 17:00:00'),

-- PV de soutenance
(8, 'pv_soutenance', 'soutenance', 1, 'storage/pv_soutenance/2024/pv_soutenance_001.pdf', 'PV_Soutenance_KONE_Adama.pdf', 89012, 'b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3', 80, '2024-12-10 12:30:00'),
(9, 'pv_soutenance', 'soutenance', 2, 'storage/pv_soutenance/2024/pv_soutenance_002.pdf', 'PV_Soutenance_SANGARE_Fatou.pdf', 90123, 'c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4', 80, '2024-12-12 17:30:00'),

-- Bulletins de notes
(10, 'bulletin_soutenance', 'soutenance', 1, 'storage/bulletins/2024/bulletin_soutenance_001.pdf', 'Bulletin_Soutenance_KONE_Adama.pdf', 67890, 'd4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5', 30, '2024-12-10 14:00:00'),

-- Attestation de diplôme
(11, 'attestation_diplome', 'etudiant', 1, 'storage/diplomes/2024/attestation_001.pdf', 'Attestation_Diplome_KONE_Adama.pdf', 123456, 'e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6', 30, '2024-12-15 11:00:00'),

-- Convocations soutenance
(12, 'convocation_soutenance', 'soutenance', 3, 'storage/convocations/2024/convocation_003.pdf', 'Convocation_Soutenance_BROU_JeanPierre.pdf', 34567, 'f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1', 30, '2024-12-13 09:00:00')
ON DUPLICATE KEY UPDATE 
    hash_sha256 = VALUES(hash_sha256);

-- Archives
INSERT INTO archives (id_archive, document_id, hash_sha256, verifie, derniere_verification, verrouille) VALUES
(1, 1, 'a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2', TRUE, '2024-12-01 02:00:00', TRUE),
(2, 2, 'b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3', TRUE, '2024-12-01 02:00:00', TRUE),
(3, 5, 'e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6', TRUE, '2024-12-01 02:00:00', TRUE),
(4, 6, 'f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1', TRUE, '2024-12-01 02:00:00', TRUE),
(5, 7, 'a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2', TRUE, '2024-12-01 02:00:00', TRUE),
(6, 8, 'b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3', TRUE, '2024-12-15 02:00:00', TRUE),
(7, 11, 'e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6', TRUE, '2024-12-15 02:00:00', TRUE)
ON DUPLICATE KEY UPDATE 
    verifie = VALUES(verifie),
    derniere_verification = VALUES(derniere_verification);

-- Historique des entités (snapshots pour rollback)
INSERT INTO historique_entites (id_historique, entite_type, entite_id, version, snapshot_json, modifie_par) VALUES
(1, 'etudiant', 1, 1, '{"num_etu": "CI01552852", "nom_etu": "KONE", "prenom_etu": "Adama", "email_etu": "kone.adama@etudiant.ufhb.ci"}', 30),
(2, 'candidature', 1, 1, '{"theme": "Système de gestion de stock avec prédiction ML", "entreprise_id": 1, "date_soumission": "2024-10-01"}', 100),
(3, 'candidature', 1, 2, '{"theme": "Système de gestion de stock avec prédiction de la demande par Machine Learning", "entreprise_id": 1, "validee_scolarite": true}', 30),
(4, 'rapport', 1, 1, '{"titre": "Système de gestion de stock ML", "version": 1, "statut": "Brouillon"}', 100),
(5, 'rapport', 1, 2, '{"titre": "Système de gestion de stock avec prédiction ML", "version": 2, "statut": "Soumis"}', 100),
(6, 'rapport', 1, 3, '{"titre": "Système de gestion de stock avec prédiction ML", "version": 3, "statut": "Valide"}', 80),
(7, 'dossier', 1, 1, '{"etat_actuel_id": 1, "date_entree_etat": "2024-09-15"}', 30),
(8, 'dossier', 1, 2, '{"etat_actuel_id": 14, "date_entree_etat": "2024-12-15", "diplome_delivre": true}', 30)
ON DUPLICATE KEY UPDATE 
    snapshot_json = VALUES(snapshot_json);

-- Statistiques cache
INSERT INTO stats_cache (id_stat, cle_stat, valeur_json, expire_le) VALUES
(1, 'dashboard_admin_global', '{"total_etudiants": 40, "total_enseignants": 30, "soutenances_ce_mois": 5, "dossiers_en_cours": 25}', DATE_ADD(NOW(), INTERVAL 15 MINUTE)),
(2, 'stats_workflow_etats', '{"inscrit": 5, "candidature_soumise": 2, "en_evaluation": 3, "rapport_valide": 8, "soutenance_planifiee": 2, "diplome_delivre": 1}', DATE_ADD(NOW(), INTERVAL 15 MINUTE)),
(3, 'stats_financieres', '{"total_encaisse": 18700000, "total_penalites": 120000, "soldes_dus": 800000}', DATE_ADD(NOW(), INTERVAL 30 MINUTE))
ON DUPLICATE KEY UPDATE 
    valeur_json = VALUES(valeur_json),
    expire_le = VALUES(expire_le);

-- Mode maintenance
INSERT INTO maintenance_mode (id, actif, message, debut_maintenance, fin_maintenance) VALUES
(1, FALSE, NULL, NULL, NULL)
ON DUPLICATE KEY UPDATE actif = FALSE;
