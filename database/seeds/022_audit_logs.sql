-- =====================================================
-- Seed: 022_audit_logs.sql
-- Purpose: Logs d'audit et traçabilité
-- Date: 2025-12-24
-- Ref: Synthèse.txt - Audit trail complet
-- =====================================================

-- Logs d'audit (echantillon des actions les plus importantes)
INSERT INTO audit_logs (id_log, utilisateur_id, action, entite_type, entite_id, description, donnees_avant_json, donnees_apres_json, ip_adresse, user_agent) VALUES
-- Connexions
(1, 1, 'CONNEXION', 'session', NULL, 'Connexion réussie', NULL, '{"session_id": "abc123", "remember": false}', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(2, 30, 'CONNEXION', 'session', NULL, 'Connexion réussie - Service Scolarité', NULL, '{"session_id": "def456"}', '192.168.1.101', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'),
(3, 100, 'CONNEXION', 'session', NULL, 'Connexion étudiant', NULL, '{"session_id": "ghi789"}', '10.0.0.50', 'Mozilla/5.0 (Linux; Android 12)'),

-- Création utilisateurs
(4, 1, 'CREATION', 'utilisateur', 100, 'Création compte étudiant KONE Adama', NULL, '{"nom_utilisateur": "KONE Adama", "login": "kone.adama@etudiant.ufhb.ci", "groupe": "Étudiant"}', '192.168.1.100', 'Mozilla/5.0'),
(5, 1, 'CREATION', 'utilisateur', 101, 'Création compte étudiant SANGARE Fatou', NULL, '{"nom_utilisateur": "SANGARE Fatou", "login": "sangare.fatou@etudiant.ufhb.ci"}', '192.168.1.100', 'Mozilla/5.0'),

-- Candidatures
(6, 100, 'CREATION', 'candidature', 1, 'Soumission de candidature', NULL, '{"theme": "Système de gestion de stock avec ML", "entreprise": "Orange CI"}', '10.0.0.50', 'Mozilla/5.0'),
(7, 30, 'VALIDATION', 'candidature', 1, 'Validation candidature par scolarité', '{"validee_scolarite": false}', '{"validee_scolarite": true, "validee_par": 30}', '192.168.1.101', 'Mozilla/5.0'),
(8, 20, 'VALIDATION', 'candidature', 1, 'Validation format par communication', '{"validee_communication": false}', '{"validee_communication": true, "validee_par": 20}', '192.168.1.102', 'Mozilla/5.0'),

-- Rapports
(9, 100, 'CREATION', 'rapport', 1, 'Création brouillon rapport', NULL, '{"titre": "Système de gestion de stock", "version": 1}', '10.0.0.50', 'Mozilla/5.0'),
(10, 100, 'MODIFICATION', 'rapport', 1, 'Mise à jour rapport v2', '{"version": 1, "statut": "Brouillon"}', '{"version": 2, "statut": "Brouillon"}', '10.0.0.50', 'Mozilla/5.0'),
(11, 100, 'SOUMISSION', 'rapport', 1, 'Soumission rapport pour évaluation', '{"statut": "Brouillon"}', '{"statut": "Soumis", "date_soumission": "2024-10-15"}', '10.0.0.50', 'Mozilla/5.0'),

-- Commission
(12, 80, 'CREATION', 'session_commission', 1, 'Création session commission', NULL, '{"date": "2024-10-28", "lieu": "Salle de conférence"}', '192.168.1.103', 'Mozilla/5.0'),
(13, 60, 'VOTE', 'vote_commission', 1, 'Vote sur rapport étudiant', NULL, '{"rapport_id": 1, "decision": "Valider", "tour": 1}', '192.168.1.104', 'Mozilla/5.0'),
(14, 61, 'VOTE', 'vote_commission', 2, 'Vote sur rapport étudiant', NULL, '{"rapport_id": 1, "decision": "Valider", "tour": 1}', '192.168.1.105', 'Mozilla/5.0'),
(15, 80, 'VALIDATION', 'rapport', 1, 'Validation finale rapport par commission', '{"statut": "En_evaluation"}', '{"statut": "Valide", "tour_validation": 1}', '192.168.1.103', 'Mozilla/5.0'),

-- Jury et soutenance
(16, 80, 'CREATION', 'jury', 1, 'Constitution du jury', NULL, '{"dossier_id": 1, "membres": ["KOFFI", "KOUASSI", "DIABATE"]}', '192.168.1.103', 'Mozilla/5.0'),
(17, 30, 'PLANIFICATION', 'soutenance', 1, 'Planification soutenance', NULL, '{"date": "2024-12-10", "salle": "A102", "heure": "09:00"}', '192.168.1.101', 'Mozilla/5.0'),
(18, 80, 'DEMARRAGE', 'soutenance', 1, 'Démarrage soutenance - code validé', NULL, '{"code_utilise": true, "heure_debut": "09:00"}', '192.168.1.106', 'Mozilla/5.0'),
(19, 80, 'SAISIE_NOTES', 'soutenance', 1, 'Saisie des notes de soutenance', NULL, '{"note_finale": 16.83, "mention": "Très Bien"}', '192.168.1.106', 'Mozilla/5.0'),

-- Paiements
(20, 30, 'CREATION', 'paiement', 1, 'Enregistrement paiement', NULL, '{"etudiant": "KONE Adama", "montant": 550000, "mode": "Virement"}', '192.168.1.101', 'Mozilla/5.0'),
(21, 30, 'GENERATION', 'document', 1, 'Génération reçu de paiement', NULL, '{"type": "recu_paiement", "fichier": "recu_001.pdf"}', '192.168.1.101', 'Mozilla/5.0'),

-- Administration
(22, 1, 'MODIFICATION', 'configuration', NULL, 'Modification paramètre système', '{"cle": "commission.max_tours", "valeur": "3"}', '{"cle": "commission.max_tours", "valeur": "5"}', '192.168.1.100', 'Mozilla/5.0'),
(23, 1, 'BACKUP', 'systeme', NULL, 'Lancement backup manuel', NULL, '{"type": "full", "fichier": "backup_2024-12-15.sql.gz"}', '192.168.1.100', 'Mozilla/5.0'),

-- Déconnexions
(24, 100, 'DECONNEXION', 'session', NULL, 'Déconnexion étudiant', '{"session_id": "ghi789"}', NULL, '10.0.0.50', 'Mozilla/5.0'),
(25, 1, 'FORCE_DECONNEXION', 'session', NULL, 'Déconnexion forcée session suspecte', NULL, '{"session_forcee": "xyz999", "raison": "Activité suspecte détectée"}', '192.168.1.100', 'Mozilla/5.0')
ON DUPLICATE KEY UPDATE 
    description = VALUES(description);

-- Sessions actives (pour démo)
INSERT INTO sessions_actives (id_session, utilisateur_id, token_session, ip_adresse, user_agent, derniere_activite, expire_a) VALUES
(1, 1, 'admin_token_abc123def456ghi789jkl012mno345pqr678stu901vwx234yz567abc890', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', NOW(), DATE_ADD(NOW(), INTERVAL 8 HOUR)),
(2, 30, 'scolarite_token_def456ghi789jkl012mno345pqr678stu901vwx234yz567abc890abc', '192.168.1.101', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', NOW(), DATE_ADD(NOW(), INTERVAL 8 HOUR)),
(3, 100, 'etudiant_token_ghi789jkl012mno345pqr678stu901vwx234yz567abc890abcdef456', '10.0.0.50', 'Mozilla/5.0 (Linux; Android 12)', NOW(), DATE_ADD(NOW(), INTERVAL 8 HOUR))
ON DUPLICATE KEY UPDATE 
    derniere_activite = NOW(),
    expire_a = DATE_ADD(NOW(), INTERVAL 8 HOUR);

-- Cache des permissions (pour démo)
INSERT INTO permissions_cache (utilisateur_id, ressource_code, permissions_json, expire_a) VALUES
(1, 'all', '{"peut_lire": true, "peut_creer": true, "peut_modifier": true, "peut_supprimer": true, "peut_exporter": true, "peut_valider": true}', DATE_ADD(NOW(), INTERVAL 5 MINUTE)),
(30, 'etudiants', '{"peut_lire": true, "peut_creer": true, "peut_modifier": true, "peut_supprimer": false, "peut_exporter": true, "peut_valider": false}', DATE_ADD(NOW(), INTERVAL 5 MINUTE)),
(30, 'candidatures', '{"peut_lire": true, "peut_creer": true, "peut_modifier": true, "peut_supprimer": false, "peut_exporter": true, "peut_valider": true}', DATE_ADD(NOW(), INTERVAL 5 MINUTE)),
(100, 'rapports', '{"peut_lire": true, "peut_creer": true, "peut_modifier": true, "peut_supprimer": false, "peut_exporter": false, "peut_valider": false}', DATE_ADD(NOW(), INTERVAL 5 MINUTE))
ON DUPLICATE KEY UPDATE 
    permissions_json = VALUES(permissions_json),
    expire_a = VALUES(expire_a);
