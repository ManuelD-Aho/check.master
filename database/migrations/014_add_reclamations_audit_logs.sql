-- =====================================================
-- Migration: 014_add_reclamations_audit_logs.sql
-- Date: 2025-01-16
-- Purpose: Ajouter tables reclamations et audit_logs
-- =====================================================

-- Table: reclamations
CREATE TABLE IF NOT EXISTS reclamations (
    id_reclamation INT PRIMARY KEY AUTO_INCREMENT,
    etudiant_id INT NOT NULL,
    type_reclamation VARCHAR(50) NOT NULL,
    sujet VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    priorite ENUM('Basse', 'Normale', 'Haute', 'Critique') DEFAULT 'Normale',
    entite_concernee_id INT,
    statut VARCHAR(20) DEFAULT 'En_attente',
    resolution TEXT,
    motif_rejet TEXT,
    prise_en_charge_par INT,
    prise_en_charge_le DATETIME,
    resolue_par INT,
    resolue_le DATETIME,
    reponse TEXT,
    traite_par INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (etudiant_id) REFERENCES etudiants(id_etudiant) ON DELETE CASCADE,
    FOREIGN KEY (prise_en_charge_par) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    FOREIGN KEY (resolue_par) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    FOREIGN KEY (traite_par) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    INDEX idx_etudiant (etudiant_id),
    INDEX idx_statut (statut),
    INDEX idx_type (type_reclamation),
    INDEX idx_priorite (priorite),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: reclamation_reponses
CREATE TABLE IF NOT EXISTS reclamation_reponses (
    id_reponse INT PRIMARY KEY AUTO_INCREMENT,
    reclamation_id INT NOT NULL,
    auteur_id INT NOT NULL,
    contenu TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reclamation_id) REFERENCES reclamations(id_reclamation) ON DELETE CASCADE,
    FOREIGN KEY (auteur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    INDEX idx_reclamation (reclamation_id),
    INDEX idx_auteur (auteur_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: audit_logs
CREATE TABLE IF NOT EXISTS audit_logs (
    id_log INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT,
    action VARCHAR(100) NOT NULL,
    entite_type VARCHAR(50),
    entite_id INT,
    description TEXT,
    donnees_avant_json JSON,
    donnees_apres_json JSON,
    ip_adresse VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_entite (entite_type, entite_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO migrations (migration_name, executed_at) VALUES ('014_add_reclamations_audit_logs', NOW());
