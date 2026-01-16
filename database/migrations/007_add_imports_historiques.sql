-- =====================================================
-- Migration: 007_add_imports_historiques.sql
-- Date: 2025-01-16
-- Purpose: Système avancé de gestion des imports de données
-- =====================================================

-- Table: imports_configurations
CREATE TABLE IF NOT EXISTS imports_configurations (
    id_config_import INT PRIMARY KEY AUTO_INCREMENT,
    nom_configuration VARCHAR(100) NOT NULL,
    type_import ENUM('etudiants', 'enseignants', 'notes', 'paiements', 'dossiers', 'entreprises') NOT NULL,
    format_fichier ENUM('csv', 'excel', 'xml', 'json') NOT NULL,
    mapping_colonnes JSON NOT NULL,
    regles_validation JSON,
    transformation_donnees JSON,
    actif BOOLEAN DEFAULT TRUE,
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (created_by) REFERENCES utilisateurs(id_utilisateur),
    INDEX idx_type (type_import),
    UNIQUE KEY uk_nom_type (nom_configuration, type_import)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: imports_sessions
CREATE TABLE IF NOT EXISTS imports_sessions (
    id_session_import INT PRIMARY KEY AUTO_INCREMENT,
    config_import_id INT,
    nom_fichier VARCHAR(255) NOT NULL,
    chemin_fichier VARCHAR(500) NOT NULL,
    taille_fichier BIGINT,
    hash_fichier VARCHAR(64),
    nombre_lignes_total INT,
    nombre_lignes_traitees INT DEFAULT 0,
    nombre_succes INT DEFAULT 0,
    nombre_erreurs INT DEFAULT 0,
    nombre_avertissements INT DEFAULT 0,
    statut ENUM('en_attente', 'en_cours', 'termine', 'erreur', 'annule') DEFAULT 'en_attente',
    progression_pourcent DECIMAL(5,2) DEFAULT 0,
    date_debut DATETIME,
    date_fin DATETIME,
    duree_secondes INT,
    importe_par INT NOT NULL,
    commentaire TEXT,
    erreur_globale TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (config_import_id) REFERENCES imports_configurations(id_config_import),
    FOREIGN KEY (importe_par) REFERENCES utilisateurs(id_utilisateur),
    INDEX idx_statut (statut),
    INDEX idx_date (date_debut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: imports_lignes_details
CREATE TABLE IF NOT EXISTS imports_lignes_details (
    id_ligne_detail INT PRIMARY KEY AUTO_INCREMENT,
    session_import_id INT NOT NULL,
    numero_ligne INT NOT NULL,
    donnees_brutes JSON,
    donnees_transformees JSON,
    statut ENUM('succes', 'erreur', 'avertissement', 'ignore') NOT NULL,
    messages JSON COMMENT 'Tableau de messages d erreur/warning',
    entite_id INT COMMENT 'ID de l entité créée/modifiée',
    entite_type VARCHAR(50) COMMENT 'Type d entité',
    date_traitement DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (session_import_id) REFERENCES imports_sessions(id_session_import) ON DELETE CASCADE,
    INDEX idx_session (session_import_id),
    INDEX idx_statut (statut),
    INDEX idx_entite (entite_type, entite_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: imports_rollback_data
CREATE TABLE IF NOT EXISTS imports_rollback_data (
    id_rollback INT PRIMARY KEY AUTO_INCREMENT,
    session_import_id INT NOT NULL,
    table_name VARCHAR(100) NOT NULL,
    record_id INT NOT NULL,
    operation ENUM('insert', 'update', 'delete') NOT NULL,
    ancien ne_valeur JSON,
    nouvelle_valeur JSON,
    rollback_effectue BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (session_import_id) REFERENCES imports_sessions(id_session_import) ON DELETE CASCADE,
    INDEX idx_session (session_import_id),
    INDEX idx_table (table_name, record_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO migrations (migration_name, executed_at) VALUES ('007_add_imports_historiques', NOW());
