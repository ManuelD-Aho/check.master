-- =====================================================
-- Seed: 005_configuration_defaut.sql
-- Purpose: Configuration système par défaut (~170 paramètres)
-- Date: 2025-12-24
-- Updated to match migration schema
-- =====================================================

-- Configuration Workflow
INSERT INTO configuration_systeme (cle_config, valeur_config, type_valeur, groupe_config, description) VALUES
('workflow.escalade.enabled', 'true', 'boolean', 'workflow', 'Activer l''escalade automatique vers le Doyen'),
('workflow.sla.jours_defaut', '7', 'int', 'workflow', 'Délai SLA par défaut en jours'),
('workflow.alerte.50_pourcent', 'true', 'boolean', 'workflow', 'Alerte à 50% du délai'),
('workflow.alerte.80_pourcent', 'true', 'boolean', 'workflow', 'Alerte à 80% du délai'),
('workflow.alerte.100_pourcent', 'true', 'boolean', 'workflow', 'Alerte à 100% du délai'),
('workflow.gate.paiement_requis', 'true', 'boolean', 'workflow', 'Paiement requis avant commission'),
('workflow.gate.rapport_requis', 'true', 'boolean', 'workflow', 'Rapport requis avant commission'),
('workflow.notification.auto', 'true', 'boolean', 'workflow', 'Notifications automatiques sur transitions')
ON DUPLICATE KEY UPDATE valeur_config = VALUES(valeur_config);

-- Configuration Commission
INSERT INTO configuration_systeme (cle_config, valeur_config, type_valeur, groupe_config, description) VALUES
('commission.max_tours', '3', 'int', 'commission', 'Nombre maximum de tours de vote'),
('commission.unanimite_requise', 'true', 'boolean', 'commission', 'Unanimité requise pour validation'),
('commission.mediation.enabled', 'true', 'boolean', 'commission', 'Activer médiation par le Doyen'),
('commission.session.duree_min', '60', 'int', 'commission', 'Durée minimum session commission (minutes)'),
('commission.rapports.max_session', '15', 'int', 'commission', 'Nombre max rapports par session'),
('commission.pv.auto_generation', 'true', 'boolean', 'commission', 'Génération auto PV après session'),
('commission.rappel.jours_avant', '7', 'int', 'commission', 'Rappel X jours avant session')
ON DUPLICATE KEY UPDATE valeur_config = VALUES(valeur_config);

-- Configuration Finance
INSERT INTO configuration_systeme (cle_config, valeur_config, type_valeur, groupe_config, description) VALUES
('finance.scolarite.montant', '500000', 'int', 'finance', 'Montant scolarité annuelle (FCFA)'),
('finance.scolarite.frais_inscription', '50000', 'int', 'finance', 'Frais d''inscription (FCFA)'),
('finance.penalite.taux_jour', '0.5', 'float', 'finance', 'Taux pénalité par jour de retard (%)'),
('finance.penalite.plafond', '50', 'int', 'finance', 'Plafond maximum pénalité (%)'),
('finance.penalite.grace_jours', '7', 'int', 'finance', 'Jours de grâce avant pénalité'),
('finance.recu.auto_generation', 'true', 'boolean', 'finance', 'Génération automatique reçus'),
('finance.modes_paiement', '["Especes","Carte","Virement","Cheque"]', 'json', 'finance', 'Modes de paiement acceptés')
ON DUPLICATE KEY UPDATE valeur_config = VALUES(valeur_config);

-- Configuration Notifications
INSERT INTO configuration_systeme (cle_config, valeur_config, type_valeur, groupe_config, description) VALUES
('notifications.email.enabled', 'true', 'boolean', 'notifications', 'Activer envoi emails'),
('notifications.email.from', 'noreply@checkmaster.ufhb.ci', 'string', 'notifications', 'Adresse expéditeur'),
('notifications.email.from_name', 'CheckMaster UFHB', 'string', 'notifications', 'Nom expéditeur'),
('notifications.sms.enabled', 'false', 'boolean', 'notifications', 'Activer envoi SMS'),
('notifications.sms.provider', '', 'string', 'notifications', 'Provider SMS (orange, mtn, etc.)'),
('notifications.queue.enabled', 'true', 'boolean', 'notifications', 'Utiliser file d''attente'),
('notifications.queue.batch_size', '50', 'int', 'notifications', 'Taille batch envoi'),
('notifications.retry.max', '3', 'int', 'notifications', 'Tentatives max en cas d''échec'),
('notifications.retry.delay_minutes', '5', 'int', 'notifications', 'Délai entre tentatives (minutes)')
ON DUPLICATE KEY UPDATE valeur_config = VALUES(valeur_config);

-- Configuration Documents
INSERT INTO configuration_systeme (cle_config, valeur_config, type_valeur, groupe_config, description) VALUES
('documents.signatures.enabled', 'false', 'boolean', 'documents', 'Activer signatures électroniques'),
('documents.signatures.otp_enabled', 'false', 'boolean', 'documents', 'OTP pour signatures'),
('documents.archive.enabled', 'true', 'boolean', 'documents', 'Archivage automatique'),
('documents.archive.duree_jours', '10950', 'int', 'documents', 'Durée conservation archives (30 ans)'),
('documents.verification.enabled', 'true', 'boolean', 'documents', 'Vérification intégrité'),
('documents.verification.frequence', 'weekly', 'string', 'documents', 'Fréquence vérification intégrité'),
('documents.pdf.generator', 'tcpdf', 'string', 'documents', 'Générateur PDF par défaut (tcpdf/mpdf)'),
('documents.storage.path', 'storage/documents', 'string', 'documents', 'Chemin stockage documents'),
('documents.upload.max_size_mb', '10', 'int', 'documents', 'Taille max upload (Mo)')
ON DUPLICATE KEY UPDATE valeur_config = VALUES(valeur_config);

-- Configuration Authentification
INSERT INTO configuration_systeme (cle_config, valeur_config, type_valeur, groupe_config, description) VALUES
('auth.session.duree_heures', '8', 'int', 'auth', 'Durée session en heures'),
('auth.session.multi_device', 'true', 'boolean', 'auth', 'Autoriser sessions multi-appareils'),
('auth.session.max_actives', '5', 'int', 'auth', 'Nombre max sessions actives'),
('auth.password.min_length', '8', 'int', 'auth', 'Longueur minimum mot de passe'),
('auth.password.require_uppercase', 'true', 'boolean', 'auth', 'Exiger majuscule'),
('auth.password.require_lowercase', 'true', 'boolean', 'auth', 'Exiger minuscule'),
('auth.password.require_number', 'true', 'boolean', 'auth', 'Exiger chiffre'),
('auth.password.require_special', 'true', 'boolean', 'auth', 'Exiger caractère spécial'),
('auth.password.expiry_days', '0', 'int', 'auth', 'Expiration mot de passe (0=jamais)'),
('auth.bruteforce.enabled', 'true', 'boolean', 'auth', 'Protection brute-force activée'),
('auth.bruteforce.seuil_1', '3', 'int', 'auth', 'Échecs avant délai 1 min'),
('auth.bruteforce.seuil_2', '5', 'int', 'auth', 'Échecs avant délai 15 min'),
('auth.bruteforce.seuil_verrouillage', '10', 'int', 'auth', 'Échecs avant verrouillage 24h'),
('auth.2fa.enabled', 'false', 'boolean', 'auth', 'Double authentification activée')
ON DUPLICATE KEY UPDATE valeur_config = VALUES(valeur_config);

-- Configuration Application
INSERT INTO configuration_systeme (cle_config, valeur_config, type_valeur, groupe_config, description) VALUES
('app.nom', 'CheckMaster UFHB', 'string', 'app', 'Nom de l''application'),
('app.version', '2.0.0', 'string', 'app', 'Version application'),
('app.institution', 'Université Félix Houphouët-Boigny', 'string', 'app', 'Nom de l''institution'),
('app.ufr', 'Mathématiques et Informatique', 'string', 'app', 'Nom de l''UFR'),
('app.logo', '/assets/images/logo.png', 'string', 'app', 'Chemin logo'),
('app.favicon', '/assets/images/favicon.ico', 'string', 'app', 'Chemin favicon'),
('app.annee_academique_active', '1', 'int', 'app', 'ID année académique active'),
('app.timezone', 'Africa/Abidjan', 'string', 'app', 'Fuseau horaire'),
('app.locale', 'fr_CI', 'string', 'app', 'Locale'),
('app.date_format', 'd/m/Y', 'string', 'app', 'Format date'),
('app.datetime_format', 'd/m/Y H:i', 'string', 'app', 'Format date/heure'),
('app.maintenance.enabled', 'false', 'boolean', 'app', 'Mode maintenance activé'),
('app.maintenance.message', '', 'string', 'app', 'Message maintenance'),
('app.debug', 'false', 'boolean', 'app', 'Mode debug'),
('app.registration.open', 'false', 'boolean', 'app', 'Inscriptions ouvertes')
ON DUPLICATE KEY UPDATE valeur_config = VALUES(valeur_config);

-- Configuration Jury/Soutenance
INSERT INTO configuration_systeme (cle_config, valeur_config, type_valeur, groupe_config, description) VALUES
('jury.membres_min', '3', 'int', 'soutenance', 'Nombre minimum membres jury'),
('jury.membres_max', '7', 'int', 'soutenance', 'Nombre maximum membres jury'),
('jury.externes_min', '1', 'int', 'soutenance', 'Nombre minimum membres externes'),
('jury.president.grade_min', '3', 'int', 'soutenance', 'Grade minimum président (3=MC)'),
('jury.invitation.delai_reponse', '7', 'int', 'soutenance', 'Délai réponse invitation (jours)'),
('soutenance.duree_defaut', '60', 'int', 'soutenance', 'Durée soutenance par défaut (min)'),
('soutenance.duree_min', '45', 'int', 'soutenance', 'Durée minimum soutenance (min)'),
('soutenance.duree_max', '90', 'int', 'soutenance', 'Durée maximum soutenance (min)'),
('soutenance.code.longueur', '8', 'int', 'soutenance', 'Longueur code président'),
('soutenance.code.validite_debut', '06:00', 'string', 'soutenance', 'Heure début validité code'),
('soutenance.code.validite_fin', '23:59', 'string', 'soutenance', 'Heure fin validité code'),
('soutenance.convocation.jours_avant', '7', 'int', 'soutenance', 'Convocation X jours avant'),
('soutenance.rappel.jours_avant', '1', 'int', 'soutenance', 'Rappel X jours avant')
ON DUPLICATE KEY UPDATE valeur_config = VALUES(valeur_config);

-- Configuration Rapports
INSERT INTO configuration_systeme (cle_config, valeur_config, type_valeur, groupe_config, description) VALUES
('rapport.format_acceptes', '["pdf"]', 'json', 'rapport', 'Formats fichiers acceptés'),
('rapport.taille_max_mb', '50', 'int', 'rapport', 'Taille max rapport (Mo)'),
('rapport.pages_min', '30', 'int', 'rapport', 'Nombre minimum pages'),
('rapport.pages_max', '100', 'int', 'rapport', 'Nombre maximum pages'),
('rapport.versioning', 'true', 'boolean', 'rapport', 'Versionning activé'),
('rapport.max_versions', '10', 'int', 'rapport', 'Nombre max versions'),
('rapport.annotation.enabled', 'true', 'boolean', 'rapport', 'Annotations activées'),
('rapport.page_garde.auto', 'true', 'boolean', 'rapport', 'Page de garde automatique')
ON DUPLICATE KEY UPDATE valeur_config = VALUES(valeur_config);

-- Configuration Escalade
INSERT INTO configuration_systeme (cle_config, valeur_config, type_valeur, groupe_config, description) VALUES
('escalade.niveau_1.delai', '3', 'int', 'escalade', 'Délai niveau 1 (jours)'),
('escalade.niveau_2.delai', '5', 'int', 'escalade', 'Délai niveau 2 (jours)'),
('escalade.niveau_3.delai', '7', 'int', 'escalade', 'Délai niveau 3 (jours)'),
('escalade.niveau_4.delai', '10', 'int', 'escalade', 'Délai niveau 4 - Doyen (jours)'),
('escalade.auto.enabled', 'true', 'boolean', 'escalade', 'Escalade automatique'),
('escalade.notification.immediate', 'true', 'boolean', 'escalade', 'Notification immédiate escalade')
ON DUPLICATE KEY UPDATE valeur_config = VALUES(valeur_config);

-- Configuration Import/Export
INSERT INTO configuration_systeme (cle_config, valeur_config, type_valeur, groupe_config, description) VALUES
('import.etudiants.enabled', 'true', 'boolean', 'import', 'Import étudiants activé'),
('import.etudiants.format', 'xlsx', 'string', 'import', 'Format import étudiants'),
('import.validation.strict', 'true', 'boolean', 'import', 'Validation stricte imports'),
('export.format_defaut', 'xlsx', 'string', 'export', 'Format export par défaut'),
('export.limite_lignes', '10000', 'int', 'export', 'Limite lignes export')
ON DUPLICATE KEY UPDATE valeur_config = VALUES(valeur_config);

-- Configuration Audit
INSERT INTO configuration_systeme (cle_config, valeur_config, type_valeur, groupe_config, description) VALUES
('audit.enabled', 'true', 'boolean', 'audit', 'Audit activé'),
('audit.file.enabled', 'true', 'boolean', 'audit', 'Audit fichier activé'),
('audit.db.enabled', 'true', 'boolean', 'audit', 'Audit base données activé'),
('audit.retention_jours', '365', 'int', 'audit', 'Rétention logs (jours)'),
('audit.sensitive_fields', '["mdp_utilisateur","code_hash"]', 'json', 'audit', 'Champs sensibles à masquer')
ON DUPLICATE KEY UPDATE valeur_config = VALUES(valeur_config);

-- Configuration Cache
INSERT INTO configuration_systeme (cle_config, valeur_config, type_valeur, groupe_config, description) VALUES
('cache.enabled', 'true', 'boolean', 'cache', 'Cache activé'),
('cache.permissions.duree', '300', 'int', 'cache', 'Durée cache permissions (sec)'),
('cache.config.duree', '3600', 'int', 'cache', 'Durée cache config (sec)'),
('cache.stats.duree', '900', 'int', 'cache', 'Durée cache stats (sec)')
ON DUPLICATE KEY UPDATE valeur_config = VALUES(valeur_config);

-- Configuration Backup
INSERT INTO configuration_systeme (cle_config, valeur_config, type_valeur, groupe_config, description) VALUES
('backup.auto.enabled', 'true', 'boolean', 'backup', 'Backup automatique'),
('backup.auto.frequence', 'daily', 'string', 'backup', 'Fréquence backup auto'),
('backup.auto.heure', '02:00', 'string', 'backup', 'Heure backup auto'),
('backup.retention.jours', '30', 'int', 'backup', 'Rétention backups (jours)'),
('backup.compression', 'true', 'boolean', 'backup', 'Compression backups'),
('backup.notification', 'true', 'boolean', 'backup', 'Notification après backup')
ON DUPLICATE KEY UPDATE valeur_config = VALUES(valeur_config);

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
