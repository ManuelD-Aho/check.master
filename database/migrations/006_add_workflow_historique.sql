-- =====================================================
-- Migration: 006_add_workflow_historique.sql
-- Date: 2025-01-16
-- Purpose: Enrichissement de l'historique workflow
-- =====================================================

-- Table: workflow_transitions_metadata
CREATE TABLE IF NOT EXISTS workflow_transitions_metadata (
    id_metadata INT PRIMARY KEY AUTO_INCREMENT,
    historique_workflow_id INT NOT NULL,
    cle VARCHAR(100) NOT NULL,
    valeur TEXT,
    type_donnee ENUM('string', 'int', 'float', 'boolean', 'json') DEFAULT 'string',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (historique_workflow_id) REFERENCES historique_workflow(id_historique) ON DELETE CASCADE,
    INDEX idx_historique (historique_workflow_id),
    INDEX idx_cle (cle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: workflow_sla_tracking
CREATE TABLE IF NOT EXISTS workflow_sla_tracking (
    id_sla_track INT PRIMARY KEY AUTO_INCREMENT,
    dossier_id INT NOT NULL,
    etat_workflow VARCHAR(50) NOT NULL,
    sla_deadline DATETIME NOT NULL,
    date_entree DATETIME NOT NULL,
    date_sortie DATETIME,
    duree_reelle INT COMMENT 'En minutes',
    sla_respecte BOOLEAN,
    depassement_minutes INT,
    alerte_envoyee BOOLEAN DEFAULT FALSE,
    escalade_declenchee BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (dossier_id) REFERENCES dossiers_etudiants(id_dossier) ON DELETE CASCADE,
    INDEX idx_dossier (dossier_id),
    INDEX idx_deadline (sla_deadline),
    INDEX idx_respecte (sla_respecte)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: workflow_blocages
CREATE TABLE IF NOT EXISTS workflow_blocages (
    id_blocage INT PRIMARY KEY AUTO_INCREMENT,
    dossier_id INT NOT NULL,
    etat_actuel VARCHAR(50) NOT NULL,
    raison_blocage TEXT NOT NULL,
    type_blocage ENUM('technique', 'administratif', 'validation', 'document_manquant') NOT NULL,
    severite ENUM('faible', 'moyenne', 'haute', 'bloquant') DEFAULT 'moyenne',
    detecte_le DATETIME DEFAULT CURRENT_TIMESTAMP,
    resolu_le DATETIME,
    resolu_par INT,
    actions_entreprises TEXT,
    statut ENUM('actif', 'en_cours', 'resolu') DEFAULT 'actif',
    
    FOREIGN KEY (dossier_id) REFERENCES dossiers_etudiants(id_dossier) ON DELETE CASCADE,
    FOREIGN KEY (resolu_par) REFERENCES utilisateurs(id_utilisateur),
    INDEX idx_dossier (dossier_id),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO migrations (migration_name, executed_at) VALUES ('006_add_workflow_historique', NOW());
