-- =====================================================
-- Migration: 013_add_fulltext_indexes.sql
-- Date: 2025-01-16
-- Purpose: Ajouter index FULLTEXT pour recherche performante
-- =====================================================

-- Index fulltext sur étudiants
ALTER TABLE etudiants 
ADD FULLTEXT INDEX ft_etudiants_search (nom_etu, prenom_etu, email_etu, num_etu);

-- Index fulltext sur enseignants
ALTER TABLE enseignants 
ADD FULLTEXT INDEX ft_enseignants_search (nom_ens, prenom_ens, email_ens);

-- Index fulltext sur entreprises
ALTER TABLE entreprises 
ADD FULLTEXT INDEX ft_entreprises_search (nom_entreprise, secteur_activite);

-- Index fulltext sur rapports commission
ALTER TABLE rapports_etudiants 
ADD FULLTEXT INDEX ft_rapports_search (titre, contenu_html);

-- Index fulltext sur notifications
ALTER TABLE notifications 
ADD FULLTEXT INDEX ft_notifications_search (titre, contenu);

-- Index fulltext sur pister (audit)
ALTER TABLE pister 
ADD FULLTEXT INDEX ft_audit_search (action, entite_type);

-- Index fulltext sur documents générés
ALTER TABLE documents_generes_historique 
ADD FULLTEXT INDEX ft_documents_search (nom_fichier);

-- Index fulltext sur imports
ALTER TABLE imports_sessions 
ADD FULLTEXT INDEX ft_imports_search (nom_fichier, commentaire);

-- Index fulltext sur paramètres

-- Index composites pour requêtes fréquentes
ALTER TABLE dossiers_etudiants 
ADD INDEX idx_composite_etudiant_annee (etudiant_id, annee_acad_id),
ADD INDEX idx_composite_etat_workflow (etat_actuel_id, date_entree_etat);

ALTER TABLE paiements 
ADD INDEX idx_composite_etudiant_date (etudiant_id, date_paiement),
ADD INDEX idx_composite_date_montant (date_paiement, montant);

ALTER TABLE notifications 
ADD INDEX idx_composite_destinataire_lue (destinataire_id, lue),
ADD INDEX idx_composite_date_type (created_at, type);

ALTER TABLE workflow_historique 
ADD INDEX idx_composite_dossier_date (dossier_id, created_at);

-- Index sur colonnes JSON fréquemment requêtées

-- Index partiels pour optimisation
ALTER TABLE utilisateurs 
ADD INDEX idx_utilisateurs_actifs (statut_utilisateur, login_utilisateur);

INSERT INTO migrations (migration_name, executed_at) VALUES ('013_add_fulltext_indexes', NOW());
