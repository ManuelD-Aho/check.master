-- =====================================================
-- Migration: 013_add_fulltext_indexes.sql
-- Date: 2025-01-16
-- Purpose: Ajouter index FULLTEXT pour recherche performante
-- =====================================================

-- Index fulltext sur étudiants
ALTER TABLE etudiants 
ADD FULLTEXT INDEX ft_etudiants_search (nom_etu, prenoms_etu, email_etu, matricule_etu);

-- Index fulltext sur enseignants
ALTER TABLE enseignants 
ADD FULLTEXT INDEX ft_enseignants_search (nom_ens, prenoms_ens, email_ens);

-- Index fulltext sur entreprises
ALTER TABLE entreprises_partenaires 
ADD FULLTEXT INDEX ft_entreprises_search (nom_entreprise, secteur_activite);

-- Index fulltext sur rapports commission
ALTER TABLE rapports_commission 
ADD FULLTEXT INDEX ft_rapports_search (deliberations, recommandations);

-- Index fulltext sur notifications
ALTER TABLE notifications 
ADD FULLTEXT INDEX ft_notifications_search (titre, message);

-- Index fulltext sur pister (audit)
ALTER TABLE pister 
ADD FULLTEXT INDEX ft_audit_search (action, details);

-- Index fulltext sur documents générés
ALTER TABLE documents_generes_historique 
ADD FULLTEXT INDEX ft_documents_search (nom_fichier);

-- Index fulltext sur imports
ALTER TABLE imports_sessions 
ADD FULLTEXT INDEX ft_imports_search (nom_fichier, commentaire);

-- Index fulltext sur paramètres
ALTER TABLE parametres 
ADD FULLTEXT INDEX ft_parametres_search (cle, description);

-- Index composites pour requêtes fréquentes
ALTER TABLE dossiers_etudiants 
ADD INDEX idx_composite_etudiant_annee (etudiant_id, annee_academique_id),
ADD INDEX idx_composite_etat_workflow (etat_workflow_id, date_soumission);

ALTER TABLE paiements 
ADD INDEX idx_composite_etudiant_statut (etudiant_id, statut_paiement),
ADD INDEX idx_composite_date_montant (date_paiement, montant_paye);

ALTER TABLE notifications 
ADD INDEX idx_composite_utilisateur_statut (utilisateur_id, statut_lecture),
ADD INDEX idx_composite_date_priorite (date_envoi, priorite);

ALTER TABLE historique_workflow 
ADD INDEX idx_composite_dossier_date (dossier_id, date_transition);

-- Index sur colonnes JSON fréquemment requêtées
ALTER TABLE workflow_etats_config 
ADD INDEX idx_json_code ((CAST(JSON_EXTRACT(conditions_json, '$.code') AS CHAR(50))));

ALTER TABLE parametres 
ADD INDEX idx_json_type ((CAST(JSON_EXTRACT(valeur, '$.type') AS CHAR(50))));

-- Index partiels pour optimisation
ALTER TABLE utilisateurs 
ADD INDEX idx_utilisateurs_actifs (statut_utilisateur, login_utilisateur) 
WHERE statut_utilisateur = 'Actif';

ALTER TABLE jobs 
ADD INDEX idx_jobs_pending (queue, priority, available_at) 
WHERE status = 'pending';

INSERT INTO migrations (migration_name, executed_at) VALUES ('013_add_fulltext_indexes', NOW());
