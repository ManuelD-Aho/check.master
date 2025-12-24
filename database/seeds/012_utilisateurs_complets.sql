-- =====================================================
-- Seed: 012_utilisateurs_complets.sql
-- Purpose: Utilisateurs système avec tous les rôles
-- Date: 2025-12-24
-- Ref: Synthèse.txt - 13 groupes utilisateurs
-- Mot de passe par défaut: CheckMaster2024! (hashé Argon2id)
-- =====================================================

-- Utilisateurs Administrateurs (groupe 1)
INSERT INTO utilisateurs (id_utilisateur, nom_utilisateur, login_utilisateur, mdp_utilisateur, id_type_utilisateur, id_GU, id_niv_acces_donnee, statut_utilisateur, doit_changer_mdp) VALUES
(1, 'Administrateur Système', 'admin@checkmaster.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 1, 1, 4, 'Actif', FALSE),
(2, 'KOUAME Amani Albert', 'kouame.amani@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 1, 1, 4, 'Actif', TRUE)
ON DUPLICATE KEY UPDATE statut_utilisateur = 'Actif';

-- Utilisateurs Secrétariat (groupe 2)
INSERT INTO utilisateurs (id_utilisateur, nom_utilisateur, login_utilisateur, mdp_utilisateur, id_type_utilisateur, id_GU, id_niv_acces_donnee, statut_utilisateur, doit_changer_mdp) VALUES
(10, 'N''GUESSAN Marie', 'nguessan.marie@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 2, 2, 2, 'Actif', TRUE),
(11, 'KOFFI Adjoua', 'koffi.adjoua@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 2, 2, 2, 'Actif', TRUE)
ON DUPLICATE KEY UPDATE statut_utilisateur = 'Actif';

-- Utilisateurs Communication (groupe 3)
INSERT INTO utilisateurs (id_utilisateur, nom_utilisateur, login_utilisateur, mdp_utilisateur, id_type_utilisateur, id_GU, id_niv_acces_donnee, statut_utilisateur, doit_changer_mdp) VALUES
(20, 'KOUASSI Estelle', 'kouassi.estelle@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 2, 3, 2, 'Actif', TRUE),
(21, 'DIALLO Aissatou', 'diallo.aissatou.admin@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 2, 3, 2, 'Actif', TRUE)
ON DUPLICATE KEY UPDATE statut_utilisateur = 'Actif';

-- Utilisateurs Scolarité (groupe 4)
INSERT INTO utilisateurs (id_utilisateur, nom_utilisateur, login_utilisateur, mdp_utilisateur, id_type_utilisateur, id_GU, id_niv_acces_donnee, statut_utilisateur, doit_changer_mdp) VALUES
(30, 'DOSSO Aminata', 'dosso.aminata@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 2, 4, 3, 'Actif', TRUE),
(31, 'TRAORE Mamadou', 'traore.mamadou.admin@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 2, 4, 3, 'Actif', TRUE),
(32, 'COULIBALY Fatoumata', 'coulibaly.fatoumata.admin@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 2, 4, 3, 'Actif', TRUE)
ON DUPLICATE KEY UPDATE statut_utilisateur = 'Actif';

-- Utilisateurs Responsable Filière (groupe 5) - Enseignants
INSERT INTO utilisateurs (id_utilisateur, nom_utilisateur, login_utilisateur, mdp_utilisateur, id_type_utilisateur, id_GU, id_niv_acces_donnee, statut_utilisateur, doit_changer_mdp) VALUES
(40, 'Prof. DIALLO Mamadou', 'diallo.mamadou@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 5, 3, 'Actif', TRUE)
ON DUPLICATE KEY UPDATE statut_utilisateur = 'Actif';

-- Utilisateurs Responsable Niveau (groupe 6) - Enseignants
INSERT INTO utilisateurs (id_utilisateur, nom_utilisateur, login_utilisateur, mdp_utilisateur, id_type_utilisateur, id_GU, id_niv_acces_donnee, statut_utilisateur, doit_changer_mdp) VALUES
(50, 'Dr. YAO Konan Pierre', 'yao.konan@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 6, 3, 'Actif', TRUE)
ON DUPLICATE KEY UPDATE statut_utilisateur = 'Actif';

-- Utilisateurs Commission (groupe 7) - Enseignants membres commission
INSERT INTO utilisateurs (id_utilisateur, nom_utilisateur, login_utilisateur, mdp_utilisateur, id_type_utilisateur, id_GU, id_niv_acces_donnee, statut_utilisateur, doit_changer_mdp) VALUES
(60, 'Dr. KOUASSI Aya Marie', 'kouassi.aya@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 7, 2, 'Actif', TRUE),
(61, 'Dr. DIABATE Fatoumata', 'diabate.fatoumata@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 7, 2, 'Actif', TRUE),
(62, 'Dr. N''GUESSAN Ahou Christelle', 'nguessan.ahou@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 7, 2, 'Actif', TRUE),
(63, 'Dr. COULIBALY Abdoulaye', 'coulibaly.abdoulaye@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 7, 2, 'Actif', TRUE),
(64, 'Dr. DOSSO Mohamed', 'dosso.mohamed@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 7, 2, 'Actif', TRUE)
ON DUPLICATE KEY UPDATE statut_utilisateur = 'Actif';

-- Utilisateurs Enseignants simples (groupe 8)
INSERT INTO utilisateurs (id_utilisateur, nom_utilisateur, login_utilisateur, mdp_utilisateur, id_type_utilisateur, id_GU, id_niv_acces_donnee, statut_utilisateur, doit_changer_mdp) VALUES
(70, 'Dr. SANOGO Mariam', 'sanogo.mariam@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 8, 2, 'Actif', TRUE),
(71, 'M. GBAGBO Eric', 'gbagbo.eric@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 8, 2, 'Actif', TRUE),
(72, 'Mme SORO Aminata', 'soro.aminata@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 8, 2, 'Actif', TRUE),
(73, 'M. TOURE Issouf', 'toure.issouf@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 8, 2, 'Actif', TRUE),
(74, 'Mme KONAN Sylvie', 'konan.sylvie@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 8, 2, 'Actif', TRUE),
(75, 'M. FOFANA Bakary', 'fofana.bakary@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 8, 2, 'Actif', TRUE)
ON DUPLICATE KEY UPDATE statut_utilisateur = 'Actif';

-- Utilisateurs Étudiants (groupe 9) - Premiers 20 étudiants
INSERT INTO utilisateurs (id_utilisateur, nom_utilisateur, login_utilisateur, mdp_utilisateur, id_type_utilisateur, id_GU, id_niv_acces_donnee, statut_utilisateur, doit_changer_mdp) VALUES
(100, 'KONE Adama', 'kone.adama@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', TRUE),
(101, 'SANGARE Fatou', 'sangare.fatou@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', TRUE),
(102, 'BROU Jean-Pierre', 'brou.jeanpierre@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', TRUE),
(103, 'ASSI Marie-Claire', 'assi.marieclaire@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', TRUE),
(104, 'KONAN Yves', 'konan.yves@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', TRUE),
(105, 'OUATTARA Mariam', 'ouattara.mariam@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', TRUE),
(106, 'ZADI Emmanuel', 'zadi.emmanuel@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', TRUE),
(107, 'AKA Cynthia', 'aka.cynthia@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', TRUE),
(108, 'GNAMBA Patrick', 'gnamba.patrick@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', TRUE),
(109, 'N''DRI Adjoua', 'ndri.adjoua@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', TRUE),
(110, 'LAGO Constant', 'lago.constant@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', TRUE),
(111, 'EHUI Sandrine', 'ehui.sandrine@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', TRUE),
(112, 'TAPE Didier', 'tape.didier@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', TRUE),
(113, 'GBADJE Félicité', 'gbadje.felicite@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', TRUE),
(114, 'YAPI Serge', 'yapi.serge@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', TRUE),
(115, 'DAGO Estelle', 'dago.estelle@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', TRUE),
(116, 'ASSEMIAN Rodrigue', 'assemian.rodrigue@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', TRUE),
(117, 'ANOH Prisca', 'anoh.prisca@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', TRUE),
(118, 'GNAGNE Martial', 'gnagne.martial@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', TRUE),
(119, 'AMON Esther', 'amon.esther@etudiant.ufhb.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 4, 9, 1, 'Actif', TRUE)
ON DUPLICATE KEY UPDATE statut_utilisateur = 'Actif';

-- Utilisateur Président Commission (groupe 10)
INSERT INTO utilisateurs (id_utilisateur, nom_utilisateur, login_utilisateur, mdp_utilisateur, id_type_utilisateur, id_GU, id_niv_acces_donnee, statut_utilisateur, doit_changer_mdp) VALUES
(80, 'Prof. KOFFI Kouamé Jean (Président Commission)', 'koffi.kouame@ufhb.edu.ci', '$argon2id$v=19$m=65536,t=4,p=1$YWRtaW5jaGVja21hc3Rlcg$K8bVgCvH0m6Tz5TG0nQXvL+TS7F5kB2fS8mPv3rWJhE', 3, 10, 3, 'Actif', TRUE)
ON DUPLICATE KEY UPDATE statut_utilisateur = 'Actif';

-- Attribution des utilisateurs aux groupes
INSERT INTO utilisateurs_groupes (utilisateur_id, groupe_id, attribue_par, attribue_le) VALUES
-- Admin
(1, 1, 1, NOW()), (2, 1, 1, NOW()),
-- Secrétariat
(10, 2, 1, NOW()), (11, 2, 1, NOW()),
-- Communication
(20, 3, 1, NOW()), (21, 3, 1, NOW()),
-- Scolarité
(30, 4, 1, NOW()), (31, 4, 1, NOW()), (32, 4, 1, NOW()),
-- Resp Filière
(40, 5, 1, NOW()),
-- Resp Niveau
(50, 6, 1, NOW()),
-- Commission
(60, 7, 1, NOW()), (61, 7, 1, NOW()), (62, 7, 1, NOW()), (63, 7, 1, NOW()), (64, 7, 1, NOW()),
-- Enseignants
(70, 8, 1, NOW()), (71, 8, 1, NOW()), (72, 8, 1, NOW()), (73, 8, 1, NOW()), (74, 8, 1, NOW()), (75, 8, 1, NOW()),
-- Étudiants
(100, 9, 1, NOW()), (101, 9, 1, NOW()), (102, 9, 1, NOW()), (103, 9, 1, NOW()), (104, 9, 1, NOW()),
(105, 9, 1, NOW()), (106, 9, 1, NOW()), (107, 9, 1, NOW()), (108, 9, 1, NOW()), (109, 9, 1, NOW()),
(110, 9, 1, NOW()), (111, 9, 1, NOW()), (112, 9, 1, NOW()), (113, 9, 1, NOW()), (114, 9, 1, NOW()),
(115, 9, 1, NOW()), (116, 9, 1, NOW()), (117, 9, 1, NOW()), (118, 9, 1, NOW()), (119, 9, 1, NOW()),
-- Président Commission
(80, 10, 1, NOW())
ON DUPLICATE KEY UPDATE attribue_le = NOW();
