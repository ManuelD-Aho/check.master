-- =====================================================
-- Migration: 012_add_roles_temporaires.sql
-- Date: 2025-01-16
-- Purpose: Système de rôles temporaires et délégations
-- =====================================================

-- Table: roles_temporaires_types
CREATE TABLE IF NOT EXISTS roles_temporaires_types (
    id_type_role_temp INT PRIMARY KEY AUTO_INCREMENT,
    code_role VARCHAR(50) UNIQUE NOT NULL,
    nom_role VARCHAR(100) NOT NULL,
    description TEXT,
    permissions_incluses JSON,
    duree_maximale_jours INT,
    necessite_approbation BOOLEAN DEFAULT TRUE,
    niveau_hierarchique INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: roles_temporaires_attributions
CREATE TABLE IF NOT EXISTS roles_temporaires_attributions (
    id_attribution INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    type_role_temp_id INT NOT NULL,
    contexte_type VARCHAR(50) COMMENT 'dossier, commission, departement, etc',
    contexte_id INT COMMENT 'ID du contexte',
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    raison TEXT NOT NULL,
    demande_par INT NOT NULL,
    approuve_par INT,
    date_approbation DATETIME,
    statut ENUM('en_attente', 'approuve', 'actif', 'expire', 'revoque') DEFAULT 'en_attente',
    revoque_par INT,
    date_revocation DATETIME,
    motif_revocation TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (type_role_temp_id) REFERENCES roles_temporaires_types(id_type_role_temp),
    FOREIGN KEY (demande_par) REFERENCES utilisateurs(id_utilisateur),
    FOREIGN KEY (approuve_par) REFERENCES utilisateurs(id_utilisateur),
    FOREIGN KEY (revoque_par) REFERENCES utilisateurs(id_utilisateur),
    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_dates (date_debut, date_fin),
    INDEX idx_statut (statut),
    INDEX idx_contexte (contexte_type, contexte_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: delegations_fonctions
CREATE TABLE IF NOT EXISTS delegations_fonctions (
    id_delegation INT PRIMARY KEY AUTO_INCREMENT,
    delegant_id INT NOT NULL,
    delegataire_id INT NOT NULL,
    fonction VARCHAR(100) NOT NULL,
    scope_delegation ENUM('total', 'partiel', 'specifique') DEFAULT 'partiel',
    restrictions JSON COMMENT 'Restrictions et limites',
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    motif TEXT,
    document_officiel VARCHAR(500),
    statut ENUM('actif', 'suspendu', 'termine') DEFAULT 'actif',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (delegant_id) REFERENCES utilisateurs(id_utilisateur),
    FOREIGN KEY (delegataire_id) REFERENCES utilisateurs(id_utilisateur),
    INDEX idx_delegant (delegant_id),
    INDEX idx_delegataire (delegataire_id),
    INDEX idx_dates (date_debut, date_fin),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: delegations_actions_log
CREATE TABLE IF NOT EXISTS delegations_actions_log (
    id_log_delegation INT PRIMARY KEY AUTO_INCREMENT,
    delegation_id INT NOT NULL,
    action_effectuee VARCHAR(255) NOT NULL,
    details JSON,
    effectue_par INT NOT NULL COMMENT 'Le délégat aire',
    au_nom_de INT NOT NULL COMMENT 'Le délégant',
    date_action DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_adresse VARCHAR(45),
    
    FOREIGN KEY (delegation_id) REFERENCES delegations_fonctions(id_delegation) ON DELETE CASCADE,
    FOREIGN KEY (effectue_par) REFERENCES utilisateurs(id_utilisateur),
    FOREIGN KEY (au_nom_de) REFERENCES utilisateurs(id_utilisateur),
    INDEX idx_delegation (delegation_id),
    INDEX idx_date (date_action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO migrations (migration_name, executed_at) VALUES ('012_add_roles_temporaires', NOW());
