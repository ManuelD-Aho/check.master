-- =====================================================
-- Migration: 005_add_permissions_actions.sql
-- Date: 2025-01-16
-- Purpose: Extension du système de permissions avec actions granulaires
-- =====================================================

-- Table: permissions_actions_details
CREATE TABLE IF NOT EXISTS permissions_actions_details (
    id_action_detail INT PRIMARY KEY AUTO_INCREMENT,
    action_id INT NOT NULL,
    sous_action VARCHAR(100) NOT NULL,
    description TEXT,
    necessite_validation BOOLEAN DEFAULT FALSE,
    niveau_risque ENUM('faible', 'moyen', 'eleve', 'critique') DEFAULT 'moyen',
    log_obligatoire BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (action_id) REFERENCES actions(id_action) ON DELETE CASCADE,
    INDEX idx_action (action_id),
    UNIQUE KEY uk_action_sous_action (action_id, sous_action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: permissions_conditions
CREATE TABLE IF NOT EXISTS permissions_conditions (
    id_condition INT PRIMARY KEY AUTO_INCREMENT,
    permission_id INT NOT NULL,
    type_condition ENUM('temporelle', 'ip', 'role', 'custom') NOT NULL,
    condition_json JSON NOT NULL,
    actif BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (permission_id) REFERENCES utilisateurs_permissions(id_permission) ON DELETE CASCADE,
    INDEX idx_permission (permission_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: permissions_delegations
CREATE TABLE IF NOT EXISTS permissions_delegations (
    id_delegation INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_source_id INT NOT NULL,
    utilisateur_cible_id INT NOT NULL,
    permission_id INT NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    raison TEXT,
    approuve_par INT,
    actif BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (utilisateur_source_id) REFERENCES utilisateurs(id_utilisateur),
    FOREIGN KEY (utilisateur_cible_id) REFERENCES utilisateurs(id_utilisateur),
    FOREIGN KEY (permission_id) REFERENCES utilisateurs_permissions(id_permission),
    FOREIGN KEY (approuve_par) REFERENCES utilisateurs(id_utilisateur),
    
    INDEX idx_source (utilisateur_source_id),
    INDEX idx_cible (utilisateur_cible_id),
    INDEX idx_dates (date_debut, date_fin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO migrations (migration_name, executed_at) VALUES ('005_add_permissions_actions', NOW());
