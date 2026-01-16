-- =====================================================
-- Migration: 009_add_stats_cache.sql
-- Date: 2025-01-16
-- Purpose: Cache des statistiques et métriques système
-- =====================================================

-- Table: stats_cache
CREATE TABLE IF NOT EXISTS stats_cache (
    id_stat INT PRIMARY KEY AUTO_INCREMENT,
    cle_stat VARCHAR(255) UNIQUE NOT NULL,
    valeur_stat JSON NOT NULL,
    categorie VARCHAR(100) NOT NULL,
    periode ENUM('realtime', 'horaire', 'journalier', 'hebdomadaire', 'mensuel', 'annuel') NOT NULL,
    date_reference DATE,
    calculee_le DATETIME DEFAULT CURRENT_TIMESTAMP,
    expire_le DATETIME,
    version INT DEFAULT 1,
    
    INDEX idx_cle (cle_stat),
    INDEX idx_categorie (categorie),
    INDEX idx_periode (periode),
    INDEX idx_date (date_reference),
    INDEX idx_expire (expire_le)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: stats_dashboards
CREATE TABLE IF NOT EXISTS stats_dashboards (
    id_dashboard INT PRIMARY KEY AUTO_INCREMENT,
    nom_dashboard VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    role_access VARCHAR(50),
    widgets_config JSON NOT NULL,
    layout_config JSON,
    refresh_interval INT DEFAULT 300 COMMENT 'Secondes',
    actif BOOLEAN DEFAULT TRUE,
    ordre_affichage INT DEFAULT 0,
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (created_by) REFERENCES utilisateurs(id_utilisateur),
    INDEX idx_role (role_access)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: stats_widgets
CREATE TABLE IF NOT EXISTS stats_widgets (
    id_widget INT PRIMARY KEY AUTO_INCREMENT,
    code_widget VARCHAR(100) UNIQUE NOT NULL,
    nom_widget VARCHAR(100) NOT NULL,
    description TEXT,
    type_widget ENUM('chart', 'table', 'counter', 'gauge', 'map', 'timeline') NOT NULL,
    query_sql TEXT,
    transformation_json VARCHAR(500) COMMENT 'Fonction JS de transformation',
    parametres_defaut JSON,
    cache_duration INT DEFAULT 300,
    categorie VARCHAR(50),
    actif BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_type (type_widget),
    INDEX idx_categorie (categorie)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: metriques_performance
CREATE TABLE IF NOT EXISTS metriques_performance (
    id_metrique INT PRIMARY KEY AUTO_INCREMENT,
    endpoint VARCHAR(255),
    methode VARCHAR(10),
    temps_reponse_ms INT NOT NULL,
    temps_db_ms INT,
    memoire_mb DECIMAL(10,2),
    statut_http INT,
    utilisateur_id INT,
    ip_adresse VARCHAR(45),
    user_agent TEXT,
    timestamp_metrique DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_endpoint (endpoint),
    INDEX idx_timestamp (timestamp_metrique),
    INDEX idx_statut (statut_http)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO migrations (migration_name, executed_at) VALUES ('009_add_stats_cache', NOW());
