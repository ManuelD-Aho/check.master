-- =====================================================
-- Seed: 011_personnel_admin.sql
-- Purpose: Personnel administratif (scolarité, secrétariat, communication)
-- Date: 2025-12-24
-- Ref: Synthèse.txt - Personnel administratif
-- =====================================================

INSERT INTO personnel_admin (id_pers_admin, nom_pers, prenom_pers, email_pers, telephone_pers, fonction_id, actif) VALUES
-- Administration centrale (fonction_id: 5=Doyen, 4=Directeur adjoint)
(1, 'ADMIN', 'System', 'admin@checkmaster.ufhb.ci', '+225 00 00 00 00', NULL, TRUE),
(2, 'KOUAME', 'Amani Albert', 'kouame.amani@ufhb.edu.ci', '+225 20 21 00 01', 5, TRUE),
(3, 'YAPI', 'Clarisse', 'yapi.clarisse@ufhb.edu.ci', '+225 20 21 00 02', 4, TRUE),

-- Service Scolarité (fonction_id: 7=Agent scolarité)
(4, 'DOSSO', 'Aminata', 'dosso.aminata@ufhb.edu.ci', '+225 20 21 00 04', 7, TRUE),
(5, 'TRAORE', 'Mamadou', 'traore.mamadou.admin@ufhb.edu.ci', '+225 20 21 00 05', 7, TRUE),
(6, 'COULIBALY', 'Fatoumata', 'coulibaly.fatoumata.admin@ufhb.edu.ci', '+225 20 21 00 06', 7, TRUE),
(7, 'BAMBA', 'Seydou', 'bamba.seydou@ufhb.edu.ci', '+225 20 21 00 07', 7, TRUE),

-- Service Communication (fonction_id: 8=Agent communication)
(8, 'KOUASSI', 'Estelle', 'kouassi.estelle@ufhb.edu.ci', '+225 20 21 00 08', 8, TRUE),
(9, 'DIALLO', 'Aissatou', 'diallo.aissatou.admin@ufhb.edu.ci', '+225 20 21 00 09', 8, TRUE),

-- Secrétariat (fonction_id: 6=Secrétaire)
(10, 'N''GUESSAN', 'Marie', 'nguessan.marie@ufhb.edu.ci', '+225 20 21 00 10', 6, TRUE),
(11, 'KOFFI', 'Adjoua', 'koffi.adjoua@ufhb.edu.ci', '+225 20 21 00 11', 6, TRUE),
(12, 'AKA', 'Berthe', 'aka.berthe@ufhb.edu.ci', '+225 20 21 00 12', 6, TRUE)
ON DUPLICATE KEY UPDATE 
    nom_pers = VALUES(nom_pers),
    prenom_pers = VALUES(prenom_pers),
    email_pers = VALUES(email_pers),
    fonction_id = VALUES(fonction_id),
    actif = VALUES(actif);
