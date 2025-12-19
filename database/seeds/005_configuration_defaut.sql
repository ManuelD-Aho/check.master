-- =====================================================
-- Seed: 005_configuration_defaut.sql
-- Purpose: Configuration système par défaut
-- Date: 2025-12-19
-- =====================================================

-- Création table configuration si elle n'existe pas déjà
-- (normalement dans migration, mais sécurité)

-- Configuration Workflow
INSERT INTO configuration_systeme (cle, valeur, type_valeur, module, description) VALUES
('workflow.escalade.enabled', 'true', 'boolean', 'workflow', 'Activer l''escalade automatique vers le Doyen'),
('workflow.sla.jours_defaut', '7', 'integer', 'workflow', 'Délai SLA par défaut en jours'),
('workflow.alerte.50_pourcent', 'true', 'boolean', 'workflow', 'Alerte à 50% du délai'),
('workflow.alerte.80_pourcent', 'true', 'boolean', 'workflow', 'Alerte à 80% du délai'),
('workflow.alerte.100_pourcent', 'true', 'boolean', 'workflow', 'Alerte à 100% du délai')
ON DUPLICATE KEY UPDATE valeur = VALUES(valeur);

-- Configuration Commission
INSERT INTO configuration_systeme (cle, valeur, type_valeur, module, description) VALUES
('commission.max_tours', '3', 'integer', 'commission', 'Nombre maximum de tours de vote'),
('commission.unanimite_requise', 'true', 'boolean', 'commission', 'Unanimité requise pour validation'),
('escalade.mediation.enabled', 'true', 'boolean', 'commission', 'Activer médiation par le Doyen')
ON DUPLICATE KEY UPDATE valeur = VALUES(valeur);

-- Configuration Finance
INSERT INTO configuration_systeme (cle, valeur, type_valeur, module, description) VALUES
('finance.scolarite.montant', '500000', 'integer', 'finance', 'Montant scolarité annuelle (FCFA)'),
('finance.penalite.taux_jour', '0.5', 'float', 'finance', 'Taux pénalité par jour de retard (%)'),
('finance.penalite.plafond', '50', 'integer', 'finance', 'Plafond maximum pénalité (%)'),
('finance.penalite.grace_jours', '7', 'integer', 'finance', 'Jours de grâce avant pénalité')
ON DUPLICATE KEY UPDATE valeur = VALUES(valeur);

-- Configuration Notifications
INSERT INTO configuration_systeme (cle, valeur, type_valeur, module, description) VALUES
('notifications.email.enabled', 'true', 'boolean', 'notifications', 'Activer envoi emails'),
('notifications.sms.enabled', 'false', 'boolean', 'notifications', 'Activer envoi SMS'),
('notifications.queue.batch_size', '50', 'integer', 'notifications', 'Taille batch envoi'),
('notifications.retry.max', '3', 'integer', 'notifications', 'Tentatives max en cas d''échec')
ON DUPLICATE KEY UPDATE valeur = VALUES(valeur);

-- Configuration Documents
INSERT INTO configuration_systeme (cle, valeur, type_valeur, module, description) VALUES
('documents.signatures.enabled', 'false', 'boolean', 'documents', 'Activer signatures électroniques'),
('documents.archive.duree_jours', '10950', 'integer', 'documents', 'Durée conservation archives (30 ans)'),
('documents.verification.frequence', 'weekly', 'string', 'documents', 'Fréquence vérification intégrité')
ON DUPLICATE KEY UPDATE valeur = VALUES(valeur);

-- Configuration Authentification
INSERT INTO configuration_systeme (cle, valeur, type_valeur, module, description) VALUES
('auth.session.duree_heures', '8', 'integer', 'auth', 'Durée session en heures'),
('auth.password.min_length', '8', 'integer', 'auth', 'Longueur minimum mot de passe'),
('auth.bruteforce.seuil_1', '3', 'integer', 'auth', 'Échecs avant délai 1 min'),
('auth.bruteforce.seuil_2', '5', 'integer', 'auth', 'Échecs avant délai 15 min'),
('auth.bruteforce.seuil_verrouillage', '10', 'integer', 'auth', 'Échecs avant verrouillage')
ON DUPLICATE KEY UPDATE valeur = VALUES(valeur);

-- Configuration Application
INSERT INTO configuration_systeme (cle, valeur, type_valeur, module, description) VALUES
('app.nom', 'CheckMaster UFHB', 'string', 'app', 'Nom de l''application'),
('app.institution', 'Université Félix Houphouët-Boigny', 'string', 'app', 'Nom de l''institution'),
('app.logo', '/assets/images/logo.png', 'string', 'app', 'Chemin logo'),
('app.annee_academique_active', '1', 'integer', 'app', 'ID année académique active'),
('app.timezone', 'Africa/Abidjan', 'string', 'app', 'Fuseau horaire'),
('app.maintenance.enabled', 'false', 'boolean', 'app', 'Mode maintenance activé')
ON DUPLICATE KEY UPDATE valeur = VALUES(valeur);

-- Configuration Jury/Soutenance
INSERT INTO configuration_systeme (cle, valeur, type_valeur, module, description) VALUES
('jury.membres_internes', '5', 'integer', 'soutenance', 'Nombre membres jury internes'),
('jury.membres_externes', '1', 'integer', 'soutenance', 'Nombre membres jury externes'),
('soutenance.duree_defaut', '60', 'integer', 'soutenance', 'Durée soutenance par défaut (min)'),
('soutenance.code.validite_heures', '18', 'integer', 'soutenance', 'Validité code président (06h-23h59)')
ON DUPLICATE KEY UPDATE valeur = VALUES(valeur);

-- Mentions (notes soutenance)
INSERT INTO mentions (code_mention, libelle_mention, note_min, note_max, ordre_affichage) VALUES
('AJOURNÉ', 'Ajourné', 0.00, 9.99, 1),
('PASSABLE', 'Passable', 10.00, 11.99, 2),
('ASSEZ_BIEN', 'Assez Bien', 12.00, 13.99, 3),
('BIEN', 'Bien', 14.00, 15.99, 4),
('TRES_BIEN', 'Très Bien', 16.00, 17.99, 5),
('EXCELLENT', 'Excellent', 18.00, 20.00, 6)
ON DUPLICATE KEY UPDATE note_min = VALUES(note_min), note_max = VALUES(note_max);

-- Critères d'évaluation
INSERT INTO critere_evaluation (code_critere, libelle, description, ponderation, actif) VALUES
('FOND', 'Qualité du Fond', 'Pertinence du contenu, méthodologie, résultats', 40.00, TRUE),
('FORME', 'Qualité de la Forme', 'Rédaction, mise en page, orthographe', 20.00, TRUE),
('ORAL', 'Présentation Orale', 'Clarté, maîtrise, support visuel', 25.00, TRUE),
('REPONSES', 'Réponses aux Questions', 'Pertinence et maîtrise des réponses', 15.00, TRUE)
ON DUPLICATE KEY UPDATE ponderation = VALUES(ponderation);
