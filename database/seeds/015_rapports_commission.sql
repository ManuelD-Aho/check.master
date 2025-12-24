-- =====================================================
-- Seed: 015_rapports_commission.sql
-- Purpose: Rapports étudiants et sessions de commission
-- Date: 2025-12-24
-- Ref: Synthèse.txt - Workflow commission
-- =====================================================

-- Rapports étudiants
INSERT INTO rapports_etudiants (id_rapport, dossier_id, titre, contenu_html, version, statut, date_depot, chemin_fichier, hash_fichier) VALUES
(1, 1, 'Système de gestion de stock avec prédiction ML', '<h1>Introduction</h1><p>Ce mémoire présente...</p>', 3, 'Valide', '2024-10-15', 'storage/rapports/2024/rapport_001_v3.pdf', 'abc123def456'),
(2, 2, 'Plateforme e-banking sécurisée', '<h1>Introduction</h1><p>Ce mémoire aborde...</p>', 2, 'Valide', '2024-10-16', 'storage/rapports/2024/rapport_002_v2.pdf', 'abc123def457'),
(3, 3, 'Système de suivi de flotte GPS', '<h1>Introduction</h1><p>Ce projet traite...</p>', 2, 'Valide', '2024-10-17', 'storage/rapports/2024/rapport_003_v2.pdf', 'abc123def458'),
(4, 4, 'Chatbot intelligent NLP', '<h1>Introduction</h1><p>L''objectif de ce travail...</p>', 1, 'Valide', '2024-10-18', 'storage/rapports/2024/rapport_004_v1.pdf', 'abc123def459'),
(5, 5, 'Application gestion portefeuille client', '<h1>Introduction</h1><p>Dans ce mémoire...</p>', 2, 'Valide', '2024-10-19', 'storage/rapports/2024/rapport_005_v2.pdf', 'abc123def460'),
(6, 6, 'Système de vote électronique blockchain', '<h1>Introduction</h1><p>Ce mémoire explore...</p>', 1, 'Valide', '2024-10-20', 'storage/rapports/2024/rapport_006_v1.pdf', 'abc123def461'),
(7, 7, 'Plateforme analyse réseaux sociaux', '<h1>Introduction</h1><p>Ce travail présente...</p>', 2, 'Valide', '2024-10-21', 'storage/rapports/2024/rapport_007_v2.pdf', 'abc123def462'),
(8, 8, 'Système détection fraude', '<h1>Introduction</h1><p>L''objectif principal...</p>', 1, 'En_evaluation', '2024-10-22', 'storage/rapports/2024/rapport_008_v1.pdf', 'abc123def463'),
(9, 9, 'Application télémédecine mobile', '<h1>Introduction</h1><p>Ce mémoire a pour but...</p>', 1, 'Soumis', '2024-10-23', 'storage/rapports/2024/rapport_009_v1.pdf', 'abc123def464'),
(10, 10, 'Système gestion documentaire OCR', '<h1>Introduction</h1><p>Dans le cadre de...</p>', 1, 'Soumis', '2024-10-24', 'storage/rapports/2024/rapport_010_v1.pdf', 'abc123def465'),
(11, 11, 'ERP simplifié pour PME', '<h1>Introduction</h1><p>Ce projet vise à...</p>', 1, 'Brouillon', '2024-10-25', NULL, NULL),
(16, 16, 'Analyse prédictive consommation électrique', '<h1>Introduction</h1><p>Ce travail de recherche...</p>', 2, 'Valide', '2024-10-28', 'storage/rapports/2024/rapport_016_v2.pdf', 'abc123def470'),
(17, 17, 'Gestion chaîne logistique portuaire', '<h1>Introduction</h1><p>Ce mémoire aborde la problématique...</p>', 1, 'Valide', '2024-10-29', 'storage/rapports/2024/rapport_017_v1.pdf', 'abc123def471'),
(18, 18, 'Système réservation vols dynamique', '<h1>Introduction</h1><p>L''industrie du transport aérien...</p>', 2, 'Valide', '2024-10-30', 'storage/rapports/2024/rapport_018_v2.pdf', 'abc123def472'),
(19, 19, 'Plateforme paiement mobile', '<h1>Introduction</h1><p>Le secteur des fintechs...</p>', 1, 'Valide', '2024-10-31', 'storage/rapports/2024/rapport_019_v1.pdf', 'abc123def473'),
(20, 20, 'Tableau de bord décisionnel', '<h1>Introduction</h1><p>La prise de décision...</p>', 2, 'Valide', '2024-11-01', 'storage/rapports/2024/rapport_020_v2.pdf', 'abc123def474')
ON DUPLICATE KEY UPDATE 
    titre = VALUES(titre),
    statut = VALUES(statut),
    version = VALUES(version);

-- Sessions de commission
INSERT INTO sessions_commission (id_session, date_session, lieu, statut, tour_vote, pv_genere, pv_chemin) VALUES
(1, '2024-10-28 09:00:00', 'Salle de conférence UFR MI', 'Terminee', 1, TRUE, 'storage/pv/2024/pv_session_001.pdf'),
(2, '2024-11-04 09:00:00', 'Salle de conférence UFR MI', 'Terminee', 1, TRUE, 'storage/pv/2024/pv_session_002.pdf'),
(3, '2024-11-11 09:00:00', 'Salle de conférence UFR MI', 'Terminee', 2, TRUE, 'storage/pv/2024/pv_session_003.pdf'),
(4, '2024-11-18 09:00:00', 'Salle de conférence UFR MI', 'En_cours', 1, FALSE, NULL),
(5, '2024-11-25 09:00:00', 'Salle de conférence UFR MI', 'Planifiee', 1, FALSE, NULL)
ON DUPLICATE KEY UPDATE 
    statut = VALUES(statut),
    tour_vote = VALUES(tour_vote);

-- Votes de commission
INSERT INTO votes_commission (id_vote, session_id, rapport_id, membre_id, tour, decision, commentaire) VALUES
-- Session 1 - Tous validés unanimement
(1, 1, 1, 6, 1, 'Valider', 'Excellent travail, méthodologie solide'),
(2, 1, 1, 7, 1, 'Valider', 'Rapport bien structuré'),
(3, 1, 1, 8, 1, 'Valider', 'Analyse pertinente'),
(4, 1, 1, 10, 1, 'Valider', 'Contribution significative'),
(5, 1, 1, 11, 1, 'Valider', 'Recommandé pour soutenance'),
(6, 1, 2, 6, 1, 'Valider', 'Très bon travail'),
(7, 1, 2, 7, 1, 'Valider', 'Sujet innovant'),
(8, 1, 2, 8, 1, 'Valider', 'Bien documenté'),
(9, 1, 2, 10, 1, 'Valider', 'Approche intéressante'),
(10, 1, 2, 11, 1, 'Valider', 'Prêt pour soutenance'),
-- Session 2
(11, 2, 3, 6, 1, 'Valider', 'Travail complet'),
(12, 2, 3, 7, 1, 'Valider', 'Bonne maîtrise technique'),
(13, 2, 3, 8, 1, 'Valider', 'Résultats probants'),
(14, 2, 3, 10, 1, 'Valider', 'Innovation technologique'),
(15, 2, 3, 11, 1, 'Valider', 'Excellent'),
(16, 2, 4, 6, 1, 'Valider', 'NLP bien maîtrisé'),
(17, 2, 4, 7, 1, 'Valider', 'Application pratique'),
(18, 2, 4, 8, 1, 'Valider', 'Méthodologie rigoureuse'),
(19, 2, 4, 10, 1, 'Valider', 'Impact business clair'),
(20, 2, 4, 11, 1, 'Valider', 'Prêt'),
-- Session 3 - Avec un tour 2 pour un rapport
(21, 3, 5, 6, 1, 'Valider', 'Bien'),
(22, 3, 5, 7, 1, 'A_revoir', 'Quelques corrections mineures'),
(23, 3, 5, 8, 1, 'Valider', 'OK'),
(24, 3, 5, 10, 1, 'Valider', 'Bon travail'),
(25, 3, 5, 11, 1, 'A_revoir', 'Préciser la méthodologie'),
-- Tour 2
(26, 3, 5, 6, 2, 'Valider', 'Corrections apportées'),
(27, 3, 5, 7, 2, 'Valider', 'OK maintenant'),
(28, 3, 5, 8, 2, 'Valider', 'Validé'),
(29, 3, 5, 10, 2, 'Valider', 'Approuvé'),
(30, 3, 5, 11, 2, 'Valider', 'Méthodologie clarifiée'),
-- Session 3 - Autres rapports
(31, 3, 6, 6, 1, 'Valider', 'Blockchain innovant'),
(32, 3, 6, 7, 1, 'Valider', 'Approche sécuritaire solide'),
(33, 3, 6, 8, 1, 'Valider', 'Très pertinent'),
(34, 3, 6, 10, 1, 'Valider', 'Recommandé'),
(35, 3, 6, 11, 1, 'Valider', 'Excellent'),
(36, 3, 7, 6, 1, 'Valider', 'Analyse marketing bien faite'),
(37, 3, 7, 7, 1, 'Valider', 'Data analysis rigoureuse'),
(38, 3, 7, 8, 1, 'Valider', 'Bon travail'),
(39, 3, 7, 10, 1, 'Valider', 'Validé'),
(40, 3, 7, 11, 1, 'Valider', 'OK')
ON DUPLICATE KEY UPDATE 
    decision = VALUES(decision),
    commentaire = VALUES(commentaire);

-- Annotations sur les rapports
INSERT INTO annotations_rapport (id_annotation, rapport_id, auteur_id, page_numero, position_json, contenu, type_annotation) VALUES
(1, 8, 6, 15, '{"x": 100, "y": 200}', 'Merci de préciser la source de ces données', 'Commentaire'),
(2, 8, 7, 22, '{"x": 150, "y": 300}', 'Formule incorrecte - vérifier le calcul', 'Correction'),
(3, 8, 8, 35, '{"x": 120, "y": 250}', 'Très bonne analyse, à développer davantage', 'Suggestion'),
(4, 9, 6, 10, '{"x": 80, "y": 180}', 'Introduction à étoffer', 'Suggestion'),
(5, 9, 10, 45, '{"x": 200, "y": 400}', 'Conclusion bien rédigée', 'Commentaire'),
(6, 10, 7, 8, '{"x": 90, "y": 150}', 'Référence bibliographique manquante', 'Correction'),
(7, 10, 11, 30, '{"x": 110, "y": 220}', 'Schéma à améliorer', 'Suggestion')
ON DUPLICATE KEY UPDATE 
    contenu = VALUES(contenu),
    type_annotation = VALUES(type_annotation);
