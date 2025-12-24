-- =====================================================
-- Seed: 017_paiements_finances.sql
-- Purpose: Paiements, pénalités et exonérations
-- Date: 2025-12-24
-- Ref: Synthèse.txt - Gestion financière
-- =====================================================

-- Paiements des étudiants
INSERT INTO paiements (id_paiement, etudiant_id, annee_acad_id, montant, mode_paiement, reference, date_paiement, recu_genere, recu_chemin, enregistre_par) VALUES
-- Étudiants avec paiement complet
(1, 1, 1, 550000.00, 'Virement', 'PAY-2024-001', '2024-09-15', TRUE, 'storage/recus/2024/recu_001.pdf', 30),
(2, 2, 1, 550000.00, 'Carte', 'PAY-2024-002', '2024-09-16', TRUE, 'storage/recus/2024/recu_002.pdf', 30),
(3, 3, 1, 275000.00, 'Especes', 'PAY-2024-003', '2024-09-17', TRUE, 'storage/recus/2024/recu_003.pdf', 31),
(4, 3, 1, 275000.00, 'Especes', 'PAY-2024-004', '2024-10-20', TRUE, 'storage/recus/2024/recu_004.pdf', 31),
(5, 4, 1, 550000.00, 'Cheque', 'PAY-2024-005', '2024-09-18', TRUE, 'storage/recus/2024/recu_005.pdf', 30),
(6, 5, 1, 550000.00, 'Virement', 'PAY-2024-006', '2024-09-19', TRUE, 'storage/recus/2024/recu_006.pdf', 32),
(7, 6, 1, 550000.00, 'Carte', 'PAY-2024-007', '2024-09-20', TRUE, 'storage/recus/2024/recu_007.pdf', 30),
(8, 7, 1, 550000.00, 'Virement', 'PAY-2024-008', '2024-09-21', TRUE, 'storage/recus/2024/recu_008.pdf', 31),
(9, 8, 1, 550000.00, 'Especes', 'PAY-2024-009', '2024-09-22', TRUE, 'storage/recus/2024/recu_009.pdf', 30),
(10, 9, 1, 550000.00, 'Carte', 'PAY-2024-010', '2024-09-23', TRUE, 'storage/recus/2024/recu_010.pdf', 32),
(11, 10, 1, 550000.00, 'Virement', 'PAY-2024-011', '2024-09-24', TRUE, 'storage/recus/2024/recu_011.pdf', 30),

-- Étudiants avec paiement partiel
(12, 11, 1, 300000.00, 'Especes', 'PAY-2024-012', '2024-09-25', TRUE, 'storage/recus/2024/recu_012.pdf', 31),
(13, 12, 1, 200000.00, 'Especes', 'PAY-2024-013', '2024-10-01', TRUE, 'storage/recus/2024/recu_013.pdf', 30),
(14, 13, 1, 400000.00, 'Carte', 'PAY-2024-014', '2024-09-28', TRUE, 'storage/recus/2024/recu_014.pdf', 32),

-- Paiements groupes suivants
(15, 14, 1, 550000.00, 'Virement', 'PAY-2024-015', '2024-09-26', TRUE, 'storage/recus/2024/recu_015.pdf', 30),
(16, 15, 1, 550000.00, 'Carte', 'PAY-2024-016', '2024-09-27', TRUE, 'storage/recus/2024/recu_016.pdf', 31),
(17, 16, 1, 550000.00, 'Especes', 'PAY-2024-017', '2024-09-28', TRUE, 'storage/recus/2024/recu_017.pdf', 30),
(18, 17, 1, 550000.00, 'Virement', 'PAY-2024-018', '2024-09-29', TRUE, 'storage/recus/2024/recu_018.pdf', 32),
(19, 18, 1, 550000.00, 'Carte', 'PAY-2024-019', '2024-09-30', TRUE, 'storage/recus/2024/recu_019.pdf', 30),
(20, 19, 1, 550000.00, 'Especes', 'PAY-2024-020', '2024-10-01', TRUE, 'storage/recus/2024/recu_020.pdf', 31),
(21, 20, 1, 550000.00, 'Cheque', 'PAY-2024-021', '2024-10-02', TRUE, 'storage/recus/2024/recu_021.pdf', 30),

-- Groupe C et D
(22, 21, 1, 550000.00, 'Virement', 'PAY-2024-022', '2024-10-03', TRUE, 'storage/recus/2024/recu_022.pdf', 32),
(23, 22, 1, 550000.00, 'Carte', 'PAY-2024-023', '2024-10-04', TRUE, 'storage/recus/2024/recu_023.pdf', 30),
(24, 23, 1, 275000.00, 'Especes', 'PAY-2024-024', '2024-10-05', TRUE, 'storage/recus/2024/recu_024.pdf', 31),
(25, 24, 1, 550000.00, 'Virement', 'PAY-2024-025', '2024-10-06', TRUE, 'storage/recus/2024/recu_025.pdf', 30),
(26, 25, 1, 550000.00, 'Carte', 'PAY-2024-026', '2024-10-07', TRUE, 'storage/recus/2024/recu_026.pdf', 32),
(27, 26, 1, 550000.00, 'Especes', 'PAY-2024-027', '2024-10-08', TRUE, 'storage/recus/2024/recu_027.pdf', 30),
(28, 27, 1, 550000.00, 'Virement', 'PAY-2024-028', '2024-10-09', TRUE, 'storage/recus/2024/recu_028.pdf', 31),
(29, 28, 1, 400000.00, 'Carte', 'PAY-2024-029', '2024-10-10', TRUE, 'storage/recus/2024/recu_029.pdf', 30),
(30, 29, 1, 550000.00, 'Cheque', 'PAY-2024-030', '2024-10-11', TRUE, 'storage/recus/2024/recu_030.pdf', 32),
(31, 30, 1, 550000.00, 'Virement', 'PAY-2024-031', '2024-10-12', TRUE, 'storage/recus/2024/recu_031.pdf', 30)
ON DUPLICATE KEY UPDATE 
    montant = VALUES(montant),
    recu_genere = VALUES(recu_genere);

-- Pénalités
INSERT INTO penalites (id_penalite, etudiant_id, montant, motif, date_application, payee, date_paiement, recu_chemin) VALUES
(1, 11, 25000.00, 'Retard de paiement - 10 jours', '2024-10-15', TRUE, '2024-10-20', 'storage/recus_penalites/2024/penalite_001.pdf'),
(2, 12, 50000.00, 'Retard de paiement - 20 jours', '2024-10-25', FALSE, NULL, NULL),
(3, 23, 15000.00, 'Retard de paiement - 6 jours', '2024-10-20', TRUE, '2024-10-25', 'storage/recus_penalites/2024/penalite_003.pdf'),
(4, 28, 30000.00, 'Retard de paiement - 12 jours', '2024-10-28', FALSE, NULL, NULL)
ON DUPLICATE KEY UPDATE 
    payee = VALUES(payee),
    date_paiement = VALUES(date_paiement);

-- Exonérations
INSERT INTO exonerations (id_exoneration, etudiant_id, annee_acad_id, montant_exonere, pourcentage_exonere, motif, date_attribution, approuve_par) VALUES
(1, 13, 1, 100000.00, 18.18, 'Bourse d''excellence académique', '2024-09-10', 2),
(2, 30, 1, 50000.00, 9.09, 'Situation sociale difficile - dossier validé', '2024-09-12', 2)
ON DUPLICATE KEY UPDATE 
    montant_exonere = VALUES(montant_exonere),
    motif = VALUES(motif);
