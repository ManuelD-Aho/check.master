-- =====================================================
-- Seed: 009_enseignants.sql
-- Purpose: Enseignants de l'UFR MI (commission, jury, encadrement)
-- Date: 2025-12-24
-- Ref: Synthèse.txt - Personnel enseignant
-- =====================================================

-- Enseignants - Professeurs Titulaires (Grade 4)
INSERT INTO enseignants (id_enseignant, nom_ens, prenom_ens, email_ens, telephone_ens, grade_id, fonction_id, specialite_id, actif) VALUES
(1, 'KOFFI', 'Kouamé Jean', 'koffi.kouame@ufhb.edu.ci', '+225 07 08 09 01', 4, 5, 1, TRUE),
(2, 'DIALLO', 'Mamadou', 'diallo.mamadou@ufhb.edu.ci', '+225 07 08 09 02', 4, 2, 2, TRUE),
(3, 'TRAORE', 'Seydou', 'traore.seydou@ufhb.edu.ci', '+225 07 08 09 03', 4, 1, 3, TRUE),
(4, 'OUATTARA', 'Brahima', 'ouattara.brahima@ufhb.edu.ci', '+225 07 08 09 04', 4, 1, 1, TRUE),
(5, 'BAMBA', 'Amadou', 'bamba.amadou@ufhb.edu.ci', '+225 07 08 09 05', 4, 1, 4, TRUE),

-- Maîtres de Conférences (Grade 3)
(6, 'KOUASSI', 'Aya Marie', 'kouassi.aya@ufhb.edu.ci', '+225 07 08 09 06', 3, 4, 1, TRUE),
(7, 'DIABATE', 'Fatoumata', 'diabate.fatoumata@ufhb.edu.ci', '+225 07 08 09 07', 3, 4, 2, TRUE),
(8, 'YAO', 'Konan Pierre', 'yao.konan@ufhb.edu.ci', '+225 07 08 09 08', 3, 3, 1, TRUE),
(9, 'N''GUESSAN', 'Ahou Christelle', 'nguessan.ahou@ufhb.edu.ci', '+225 07 08 09 09', 3, 4, 3, TRUE),
(10, 'COULIBALY', 'Abdoulaye', 'coulibaly.abdoulaye@ufhb.edu.ci', '+225 07 08 09 10', 3, 4, 2, TRUE),
(11, 'DOSSO', 'Mohamed', 'dosso.mohamed@ufhb.edu.ci', '+225 07 08 09 11', 3, 4, 5, TRUE),
(12, 'SANOGO', 'Mariam', 'sanogo.mariam@ufhb.edu.ci', '+225 07 08 09 12', 3, 4, 1, TRUE),

-- Maîtres-Assistants (Grade 2)
(13, 'GBAGBO', 'Eric', 'gbagbo.eric@ufhb.edu.ci', '+225 07 08 09 13', 2, 4, 1, TRUE),
(14, 'SORO', 'Aminata', 'soro.aminata@ufhb.edu.ci', '+225 07 08 09 14', 2, 4, 2, TRUE),
(15, 'TOURE', 'Issouf', 'toure.issouf@ufhb.edu.ci', '+225 07 08 09 15', 2, 4, 3, TRUE),
(16, 'KONAN', 'Sylvie', 'konan.sylvie@ufhb.edu.ci', '+225 07 08 09 16', 2, 4, 4, TRUE),
(17, 'FOFANA', 'Bakary', 'fofana.bakary@ufhb.edu.ci', '+225 07 08 09 17', 2, 4, 1, TRUE),
(18, 'CISSE', 'Rokiatou', 'cisse.rokiatou@ufhb.edu.ci', '+225 07 08 09 18', 2, 4, 2, TRUE),
(19, 'DEMBELE', 'Oumar', 'dembele.oumar@ufhb.edu.ci', '+225 07 08 09 19', 2, 4, 5, TRUE),
(20, 'MEITE', 'Kadiatou', 'meite.kadiatou@ufhb.edu.ci', '+225 07 08 09 20', 2, 4, 1, TRUE),

-- Assistants (Grade 1)
(21, 'CAMARA', 'Moussa', 'camara.moussa@ufhb.edu.ci', '+225 07 08 09 21', 1, 4, 1, TRUE),
(22, 'KONATE', 'Aicha', 'konate.aicha@ufhb.edu.ci', '+225 07 08 09 22', 1, 4, 2, TRUE),
(23, 'BERTHE', 'Souleymane', 'berthe.souleymane@ufhb.edu.ci', '+225 07 08 09 23', 1, 4, 3, TRUE),
(24, 'SYLLA', 'Mariam', 'sylla.mariam@ufhb.edu.ci', '+225 07 08 09 24', 1, 4, 1, TRUE),
(25, 'DIARRA', 'Ibrahima', 'diarra.ibrahima@ufhb.edu.ci', '+225 07 08 09 25', 1, 4, 4, TRUE),
(26, 'OUEDRAOGO', 'Pascaline', 'ouedraogo.pascaline@ufhb.edu.ci', '+225 07 08 09 26', 1, 4, 2, TRUE),
(27, 'KEITA', 'Lassana', 'keita.lassana@ufhb.edu.ci', '+225 07 08 09 27', 1, 4, 5, TRUE),
(28, 'SIDIBE', 'Fatoumata', 'sidibe.fatoumata@ufhb.edu.ci', '+225 07 08 09 28', 1, 4, 1, TRUE),

-- Vacataires / Professionnels (Grade 5)
(29, 'GNAGNE', 'Serge', 'gnagne.serge@quantech.ci', '+225 07 08 09 29', 5, 4, 1, TRUE),
(30, 'AHUI', 'Estelle', 'ahui.estelle@orange.ci', '+225 07 08 09 30', 5, 4, 2, TRUE)
ON DUPLICATE KEY UPDATE 
    nom_ens = VALUES(nom_ens),
    prenom_ens = VALUES(prenom_ens),
    email_ens = VALUES(email_ens),
    grade_id = VALUES(grade_id),
    fonction_id = VALUES(fonction_id),
    specialite_id = VALUES(specialite_id),
    actif = VALUES(actif);
