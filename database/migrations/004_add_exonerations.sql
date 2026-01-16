-- =====================================================
-- Migration: 004_add_exoner ations.sql
-- Date: 2025-01-16
-- Purpose: Système de gestion des exonérations de frais
-- =====================================================

-- Table: exonerations_types
CREATE TABLE IF NOT EXISTS exonerations_types (
    id_type_exoneration INT PRIMARY KEY AUTO_INCREMENT,
    code_type VARCHAR(50) UNIQUE NOT NULL,
    libelle VARCHAR(255) NOT NULL,
    description TEXT,
    pourcentage_reduction DECIMAL(5,2),
    montant_fixe DECIMAL(10,2),
    conditions_eligibilite JSON,
    actif BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: demandes_exoneration
CREATE TABLE IF NOT EXISTS demandes_exoneration (
    id_demande_exoneration INT PRIMARY KEY AUTO_INCREMENT,
    etudiant_id INT NOT NULL,
    type_exoneration_id INT NOT NULL,
    annee_academique_id INT NOT NULL,
    motif TEXT NOT NULL,
    pieces_justificatives JSON,
    montant_demande DECIMAL(10,2),
    statut ENUM('en_attente', 'approuve', 'refuse', 'en_revision') DEFAULT 'en_attente',
    traite_par INT,
    date_traitement DATETIME,
    commentaire_traitement TEXT,
    decision_finale VARCHAR(500),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (etudiant_id) REFERENCES etudiants(id_etudiant) ON DELETE CASCADE,
    FOREIGN KEY (type_exoneration_id) REFERENCES exonerations_types(id_type_exoneration),
    FOREIGN KEY (traite_par) REFERENCES utilisateurs(id_utilisateur),
    
    INDEX idx_etudiant (etudiant_id),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: exonerations_appliquees
CREATE TABLE IF NOT EXISTS exonerations_appliquees (
    id_exoneration_appliquee INT PRIMARY KEY AUTO_INCREMENT,
    demande_exoneration_id INT NOT NULL,
    paiement_id INT,
    montant_exonere DECIMAL(10,2) NOT NULL,
    date_application DATETIME DEFAULT CURRENT_TIMESTAMP,
    applique_par INT NOT NULL,
    
    FOREIGN KEY (demande_exoneration_id) REFERENCES demandes_exoneration(id_demande_exoneration),
    FOREIGN KEY (paiement_id) REFERENCES paiements(id_paiement),
    FOREIGN KEY (applique_par) REFERENCES utilisateurs(id_utilisateur),
    
    INDEX idx_demande (demande_exoneration_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO migrations (migration_name, executed_at) VALUES ('004_add_exonerations', NOW());
