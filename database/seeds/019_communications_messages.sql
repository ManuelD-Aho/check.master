-- =====================================================
-- Seed: 019_communications_messages.sql
-- Purpose: Messages internes, notifications queue et historique
-- Date: 2025-12-24
-- Ref: Synthèse.txt - Système de messagerie
-- =====================================================

-- Messages internes
INSERT INTO messages_internes (id_message, expediteur_id, destinataire_id, sujet, contenu, lu, date_lecture) VALUES
-- Messages système aux étudiants
(1, 1, 100, 'Bienvenue sur CheckMaster', 'Bonjour KONE Adama,\n\nBienvenue sur la plateforme CheckMaster. Votre compte a été créé avec succès.\n\nVous pouvez maintenant accéder à votre espace étudiant pour:\n- Soumettre votre candidature\n- Suivre l''avancement de votre dossier\n- Rédiger votre rapport\n\nCordialement,\nL''équipe CheckMaster', TRUE, '2024-09-16 08:30:00'),
(2, 1, 101, 'Bienvenue sur CheckMaster', 'Bonjour SANGARE Fatou,\n\nBienvenue sur la plateforme CheckMaster...', TRUE, '2024-09-16 09:15:00'),
(3, 1, 102, 'Bienvenue sur CheckMaster', 'Bonjour BROU Jean-Pierre,\n\nBienvenue sur la plateforme CheckMaster...', TRUE, '2024-09-16 10:00:00'),

-- Messages de notification de validation
(4, 30, 100, 'Candidature validée', 'Bonjour,\n\nVotre candidature a été validée par le service scolarité.\n\nVous pouvez maintenant accéder à la rédaction de votre rapport dans votre espace étudiant.\n\nCordialement,\nService Scolarité', TRUE, '2024-10-06 14:30:00'),
(5, 20, 100, 'Format rapport validé', 'Bonjour,\n\nLe format de votre rapport a été validé par le service communication.\n\nVotre dossier est transmis à la commission.\n\nCordialement,\nService Communication', TRUE, '2024-10-09 16:45:00'),

-- Messages entre encadreurs et étudiants
(6, 60, 100, 'Retour sur votre rapport', 'Bonjour Adama,\n\nJ''ai parcouru votre rapport. Dans l''ensemble, c''est un bon travail.\n\nQuelques points à améliorer:\n- Approfondir la partie méthodologique\n- Ajouter plus de références récentes\n\nÀ bientôt,\nDr. KOUASSI', TRUE, '2024-10-20 11:00:00'),
(7, 100, 60, 'Re: Retour sur votre rapport', 'Bonjour Dr. KOUASSI,\n\nMerci pour votre retour. J''ai pris note de vos remarques et je vais procéder aux modifications suggérées.\n\nCordialement,\nAdama KONE', TRUE, '2024-10-20 14:30:00'),
(8, 70, 101, 'Point sur votre avancement', 'Bonjour Fatou,\n\nPouvez-vous me faire un point sur l''avancement de votre stage et de votre rapport?\n\nCordialement,\nDr. SANOGO', TRUE, '2024-10-25 09:00:00'),

-- Messages de la commission
(9, 80, 100, 'Rapport validé par la Commission', 'Bonjour,\n\nNous avons le plaisir de vous informer que votre rapport a été validé par la Commission de validation.\n\nVotre dossier passe maintenant à l''étape suivante.\n\nFélicitations,\nLe Président de la Commission', TRUE, '2024-10-30 17:00:00'),

-- Messages récents non lus
(10, 30, 112, 'Rappel - Documents manquants', 'Bonjour,\n\nIl manque encore les documents suivants à votre dossier:\n- Attestation d''assurance\n- Convention de stage signée\n\nMerci de les transmettre au plus vite.\n\nService Scolarité', FALSE, NULL),
(11, 1, 110, 'Mise à jour disponible', 'Bonjour,\n\nUne nouvelle fonctionnalité est disponible sur CheckMaster: vous pouvez maintenant télécharger vos reçus directement depuis votre espace.\n\nL''équipe CheckMaster', FALSE, NULL),

-- Échanges entre enseignants
(12, 60, 61, 'Session commission du 18/11', 'Bonjour Fatoumata,\n\nAs-tu eu le temps de consulter les 3 rapports assignés pour la prochaine session?\n\nCordialement,\nAya', TRUE, '2024-11-15 10:30:00'),
(13, 61, 60, 'Re: Session commission du 18/11', 'Bonjour Aya,\n\nOui, j''ai terminé la lecture. On pourra en discuter avant la session si tu veux.\n\nFatoumata', TRUE, '2024-11-15 11:45:00')
ON DUPLICATE KEY UPDATE 
    lu = VALUES(lu),
    date_lecture = VALUES(date_lecture);

-- File d'attente des notifications
INSERT INTO notifications_queue (id_queue, template_id, destinataire_id, canal, variables_json, priorite, statut, tentatives, erreur_message, envoye_le) VALUES
-- Notifications envoyées
(1, 1, 100, 'Email', '{"nom": "KONE Adama", "email": "kone.adama@etudiant.ufhb.ci", "lien_activation": "https://checkmaster.ufhb.ci/activer/abc123"}', 5, 'Envoye', 1, NULL, '2024-09-15 10:30:00'),
(2, 1, 101, 'Email', '{"nom": "SANGARE Fatou", "email": "sangare.fatou@etudiant.ufhb.ci", "lien_activation": "https://checkmaster.ufhb.ci/activer/def456"}', 5, 'Envoye', 1, NULL, '2024-09-15 10:31:00'),
(3, 18, 100, 'Email', '{"nom": "KONE Adama", "theme": "Système de gestion de stock avec ML"}', 5, 'Envoye', 1, NULL, '2024-10-01 15:00:00'),

-- Notifications en attente
(4, 65, 102, 'Email', '{"nom": "BROU Jean-Pierre", "date": "2024-12-20", "heure": "10:00", "salle": "Amphithéâtre 1"}', 3, 'En_attente', 0, NULL, NULL),
(5, 66, 103, 'Email', '{"nom": "ASSI Marie-Claire", "date": "2024-12-22", "heure": "09:00", "salle": "A102"}', 3, 'En_attente', 0, NULL, NULL),

-- Notification en échec
(6, 19, 112, 'Email', '{"nom": "TAPE Didier"}', 5, 'Echec', 3, 'Connection timeout after 30s', NULL)
ON DUPLICATE KEY UPDATE 
    statut = VALUES(statut),
    tentatives = VALUES(tentatives);

-- Historique des notifications
INSERT INTO notifications_historique (id_historique, template_code, destinataire_id, canal, sujet, statut, erreur_message) VALUES
(1, 'AUTH_BIENVENUE', 100, 'Email', 'Bienvenue sur CheckMaster', 'Envoye', NULL),
(2, 'AUTH_BIENVENUE', 101, 'Email', 'Bienvenue sur CheckMaster', 'Envoye', NULL),
(3, 'AUTH_BIENVENUE', 102, 'Email', 'Bienvenue sur CheckMaster', 'Envoye', NULL),
(4, 'CANDIDATURE_SOUMISE', 100, 'Email', 'Candidature soumise', 'Envoye', NULL),
(5, 'CANDIDATURE_VALIDEE', 100, 'Email', 'Candidature validée', 'Envoye', NULL),
(6, 'COMMISSION_RAPPORT_VALIDE', 100, 'Email', 'Rapport validé par la commission', 'Envoye', NULL),
(7, 'SOUTENANCE_PLANIFIEE', 100, 'Email', 'Soutenance planifiée', 'Envoye', NULL),
(8, 'RESULTAT_SOUTENANCE', 100, 'Email', 'Résultat de votre soutenance', 'Envoye', NULL),
(9, 'CANDIDATURE_SOUMISE', 112, 'Email', 'Candidature soumise', 'Echec', 'Connection refused'),
(10, 'CANDIDATURE_SOUMISE', 112, 'Email', 'Candidature soumise (retry)', 'Echec', 'Timeout')
ON DUPLICATE KEY UPDATE 
    statut = VALUES(statut);

-- Bounces email
INSERT INTO email_bounces (id_bounce, email, type_bounce, raison, compteur, bloque) VALUES
(1, 'ancien.etudiant@ufhb.edu.ci', 'Hard', 'Mailbox does not exist', 3, TRUE),
(2, 'temp.mail@example.com', 'Soft', 'Mailbox full', 1, FALSE)
ON DUPLICATE KEY UPDATE 
    compteur = VALUES(compteur),
    bloque = VALUES(bloque);
