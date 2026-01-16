-- =====================================================
-- Migration: 008_add_maintenance_mode.sql
-- Date: 2025-01-16
-- Purpose: Gestion du mode maintenance et notifications système
-- =====================================================

-- Table: maintenance_planifiee
CREATE TABLE IF NOT EXISTS maintenance_planifiee (
    id_maintenance INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    type_maintenance ENUM('complete', 'partielle', 'urgente') DEFAULT 'complete',
    services_affectes JSON COMMENT 'Liste des services/modules affectés',
    message_utilisateurs TEXT,
    statut ENUM('planifiee', 'en_cours', 'terminee', 'annulee') DEFAULT 'planifiee',
    notification_envoyee BOOLEAN DEFAULT FALSE,
    planifie_par INT NOT NULL,
    annule_par INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (planifie_par) REFERENCES utilisateurs(id_utilisateur),
    FOREIGN KEY (annule_par) REFERENCES utilisateurs(id_utilisateur),
    INDEX idx_dates (date_debut, date_fin),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: systeme_messages
CREATE TABLE IF NOT EXISTS systeme_messages (
    id_message_systeme INT PRIMARY KEY AUTO_INCREMENT,
    type_message ENUM('info', 'warning', 'error', 'success', 'maintenance') NOT NULL,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    cible ENUM('tous', 'groupe', 'role', 'utilisateur_specifique') DEFAULT 'tous',
    cible_ids JSON COMMENT 'IDs des groupes/roles/utilisateurs ciblés',
    priorite ENUM('basse', 'normale', 'haute', 'urgente') DEFAULT 'normale',
    affichage ENUM('banner', 'popup', 'toast', 'notification') DEFAULT 'banner',
    date_debut DATETIME NOT NULL,
    date_fin DATETIME,
    actif BOOLEAN DEFAULT TRUE,
    cree_par INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (cree_par) REFERENCES utilisateurs(id_utilisateur),
    INDEX idx_dates (date_debut, date_fin),
    INDEX idx_actif (actif),
    INDEX idx_type (type_message)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: systeme_messages_lectures
CREATE TABLE IF NOT EXISTS systeme_messages_lectures (
    id_lecture INT PRIMARY KEY AUTO_INCREMENT,
    message_systeme_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    date_lecture DATETIME DEFAULT CURRENT_TIMESTAMP,
    accuse_reception BOOLEAN DEFAULT FALSE,
    
    FOREIGN KEY (message_systeme_id) REFERENCES systeme_messages(id_message_systeme) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    INDEX idx_message (message_systeme_id),
    INDEX idx_utilisateur (utilisateur_id),
    UNIQUE KEY uk_message_utilisateur (message_systeme_id, utilisateur_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO migrations (migration_name, executed_at) VALUES ('008_add_maintenance_mode', NOW());
