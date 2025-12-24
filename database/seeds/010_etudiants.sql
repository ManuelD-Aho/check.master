-- =====================================================
-- Seed: 010_etudiants.sql
-- Purpose: Étudiants Master 2 MIAGE (promotion 2024-2025)
-- Date: 2025-12-24
-- Ref: Synthèse.txt - Dossiers étudiants
-- =====================================================

-- Étudiants Master 2 MIAGE - Promotion 2024-2025
INSERT INTO etudiants (id_etudiant, num_etu, nom_etu, prenom_etu, email_etu, telephone_etu, date_naiss_etu, lieu_naiss_etu, genre_etu, promotion_etu, actif) VALUES
-- Groupe A (10 étudiants)
(1, 'CI01552852', 'KONE', 'Adama', 'kone.adama@etudiant.ufhb.ci', '+225 05 06 07 01', '1999-03-15', 'Abidjan', 'Homme', '2024-2025', TRUE),
(2, 'CI01552853', 'SANGARE', 'Fatou', 'sangare.fatou@etudiant.ufhb.ci', '+225 05 06 07 02', '2000-07-22', 'Bouaké', 'Femme', '2024-2025', TRUE),
(3, 'CI01552854', 'BROU', 'Jean-Pierre', 'brou.jeanpierre@etudiant.ufhb.ci', '+225 05 06 07 03', '1999-11-08', 'Yamoussoukro', 'Homme', '2024-2025', TRUE),
(4, 'CI01552855', 'ASSI', 'Marie-Claire', 'assi.marieclaire@etudiant.ufhb.ci', '+225 05 06 07 04', '2000-01-30', 'Abidjan', 'Femme', '2024-2025', TRUE),
(5, 'CI01552856', 'KONAN', 'Yves', 'konan.yves@etudiant.ufhb.ci', '+225 05 06 07 05', '1998-09-12', 'San-Pédro', 'Homme', '2024-2025', TRUE),
(6, 'CI01552857', 'OUATTARA', 'Mariam', 'ouattara.mariam@etudiant.ufhb.ci', '+225 05 06 07 06', '1999-05-25', 'Korhogo', 'Femme', '2024-2025', TRUE),
(7, 'CI01552858', 'ZADI', 'Emmanuel', 'zadi.emmanuel@etudiant.ufhb.ci', '+225 05 06 07 07', '2000-02-14', 'Daloa', 'Homme', '2024-2025', TRUE),
(8, 'CI01552859', 'AKA', 'Cynthia', 'aka.cynthia@etudiant.ufhb.ci', '+225 05 06 07 08', '1999-08-03', 'Abidjan', 'Femme', '2024-2025', TRUE),
(9, 'CI01552860', 'GNAMBA', 'Patrick', 'gnamba.patrick@etudiant.ufhb.ci', '+225 05 06 07 09', '1998-12-19', 'Man', 'Homme', '2024-2025', TRUE),
(10, 'CI01552861', 'N''DRI', 'Adjoua', 'ndri.adjoua@etudiant.ufhb.ci', '+225 05 06 07 10', '2000-04-07', 'Abidjan', 'Femme', '2024-2025', TRUE),

-- Groupe B (10 étudiants)
(11, 'CI01552862', 'LAGO', 'Constant', 'lago.constant@etudiant.ufhb.ci', '+225 05 06 07 11', '1999-06-28', 'Gagnoa', 'Homme', '2024-2025', TRUE),
(12, 'CI01552863', 'EHUI', 'Sandrine', 'ehui.sandrine@etudiant.ufhb.ci', '+225 05 06 07 12', '2000-10-11', 'Abidjan', 'Femme', '2024-2025', TRUE),
(13, 'CI01552864', 'TAPE', 'Didier', 'tape.didier@etudiant.ufhb.ci', '+225 05 06 07 13', '1998-07-04', 'Divo', 'Homme', '2024-2025', TRUE),
(14, 'CI01552865', 'GBADJE', 'Félicité', 'gbadje.felicite@etudiant.ufhb.ci', '+225 05 06 07 14', '1999-02-17', 'Abidjan', 'Femme', '2024-2025', TRUE),
(15, 'CI01552866', 'YAPI', 'Serge', 'yapi.serge@etudiant.ufhb.ci', '+225 05 06 07 15', '2000-09-23', 'Abengourou', 'Homme', '2024-2025', TRUE),
(16, 'CI01552867', 'DAGO', 'Estelle', 'dago.estelle@etudiant.ufhb.ci', '+225 05 06 07 16', '1999-04-01', 'Abidjan', 'Femme', '2024-2025', TRUE),
(17, 'CI01552868', 'ASSEMIAN', 'Rodrigue', 'assemian.rodrigue@etudiant.ufhb.ci', '+225 05 06 07 17', '1998-11-29', 'Bondoukou', 'Homme', '2024-2025', TRUE),
(18, 'CI01552869', 'ANOH', 'Prisca', 'anoh.prisca@etudiant.ufhb.ci', '+225 05 06 07 18', '2000-06-16', 'Abidjan', 'Femme', '2024-2025', TRUE),
(19, 'CI01552870', 'GNAGNE', 'Martial', 'gnagne.martial@etudiant.ufhb.ci', '+225 05 06 07 19', '1999-01-09', 'Dabou', 'Homme', '2024-2025', TRUE),
(20, 'CI01552871', 'AMON', 'Esther', 'amon.esther@etudiant.ufhb.ci', '+225 05 06 07 20', '2000-08-21', 'Abidjan', 'Femme', '2024-2025', TRUE),

-- Groupe C (10 étudiants)
(21, 'CI01552872', 'ADJE', 'Boris', 'adje.boris@etudiant.ufhb.ci', '+225 05 06 07 21', '1998-10-05', 'Agboville', 'Homme', '2024-2025', TRUE),
(22, 'CI01552873', 'BONI', 'Rachelle', 'boni.rachelle@etudiant.ufhb.ci', '+225 05 06 07 22', '1999-12-12', 'Abidjan', 'Femme', '2024-2025', TRUE),
(23, 'CI01552874', 'KOUADIO', 'Franck', 'kouadio.franck@etudiant.ufhb.ci', '+225 05 06 07 23', '2000-03-27', 'Bouaké', 'Homme', '2024-2025', TRUE),
(24, 'CI01552875', 'NIAMKE', 'Christiane', 'niamke.christiane@etudiant.ufhb.ci', '+225 05 06 07 24', '1999-07-08', 'Abidjan', 'Femme', '2024-2025', TRUE),
(25, 'CI01552876', 'TANOH', 'Germain', 'tanoh.germain@etudiant.ufhb.ci', '+225 05 06 07 25', '1998-05-14', 'Dimbokro', 'Homme', '2024-2025', TRUE),
(26, 'CI01552877', 'AHOURE', 'Vanessa', 'ahoure.vanessa@etudiant.ufhb.ci', '+225 05 06 07 26', '2000-11-02', 'Abidjan', 'Femme', '2024-2025', TRUE),
(27, 'CI01552878', 'YEO', 'Ibrahim', 'yeo.ibrahim@etudiant.ufhb.ci', '+225 05 06 07 27', '1999-09-18', 'Korhogo', 'Homme', '2024-2025', TRUE),
(28, 'CI01552879', 'GBAHI', 'Viviane', 'gbahi.viviane@etudiant.ufhb.ci', '+225 05 06 07 28', '2000-01-25', 'Abidjan', 'Femme', '2024-2025', TRUE),
(29, 'CI01552880', 'BAMBA', 'Moussa', 'bamba.moussa@etudiant.ufhb.ci', '+225 05 06 07 29', '1998-08-07', 'Odienné', 'Homme', '2024-2025', TRUE),
(30, 'CI01552881', 'FANNY', 'Mariame', 'fanny.mariame@etudiant.ufhb.ci', '+225 05 06 07 30', '1999-04-20', 'Abidjan', 'Femme', '2024-2025', TRUE),

-- Groupe D (10 étudiants)
(31, 'CI01552882', 'DIOMANDE', 'Sekou', 'diomande.sekou@etudiant.ufhb.ci', '+225 05 06 07 31', '2000-07-13', 'Séguéla', 'Homme', '2024-2025', TRUE),
(32, 'CI01552883', 'KOFFI', 'Ange', 'koffi.ange@etudiant.ufhb.ci', '+225 05 06 07 32', '1999-02-28', 'Abidjan', 'Femme', '2024-2025', TRUE),
(33, 'CI01552884', 'EHOUMAN', 'Paterne', 'ehouman.paterne@etudiant.ufhb.ci', '+225 05 06 07 33', '1998-06-10', 'Adzopé', 'Homme', '2024-2025', TRUE),
(34, 'CI01552885', 'TIA', 'Nadège', 'tia.nadege@etudiant.ufhb.ci', '+225 05 06 07 34', '2000-12-03', 'Abidjan', 'Femme', '2024-2025', TRUE),
(35, 'CI01552886', 'GUEI', 'Sylvain', 'guei.sylvain@etudiant.ufhb.ci', '+225 05 06 07 35', '1999-10-22', 'Man', 'Homme', '2024-2025', TRUE),
(36, 'CI01552887', 'OKOU', 'Florence', 'okou.florence@etudiant.ufhb.ci', '+225 05 06 07 36', '2000-05-08', 'Abidjan', 'Femme', '2024-2025', TRUE),
(37, 'CI01552888', 'DJE', 'Wilfried', 'dje.wilfried@etudiant.ufhb.ci', '+225 05 06 07 37', '1998-03-16', 'Soubré', 'Homme', '2024-2025', TRUE),
(38, 'CI01552889', 'KACOU', 'Bernadette', 'kacou.bernadette@etudiant.ufhb.ci', '+225 05 06 07 38', '1999-11-30', 'Abidjan', 'Femme', '2024-2025', TRUE),
(39, 'CI01552890', 'TOURE', 'Mamadou', 'toure.mamadou@etudiant.ufhb.ci', '+225 05 06 07 39', '2000-02-06', 'Touba', 'Homme', '2024-2025', TRUE),
(40, 'CI01552891', 'ALLOU', 'Pascale', 'allou.pascale@etudiant.ufhb.ci', '+225 05 06 07 40', '1999-08-19', 'Abidjan', 'Femme', '2024-2025', TRUE)
ON DUPLICATE KEY UPDATE 
    nom_etu = VALUES(nom_etu),
    prenom_etu = VALUES(prenom_etu),
    email_etu = VALUES(email_etu),
    telephone_etu = VALUES(telephone_etu),
    genre_etu = VALUES(genre_etu),
    promotion_etu = VALUES(promotion_etu),
    actif = VALUES(actif);
