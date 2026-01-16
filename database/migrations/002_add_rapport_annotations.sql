-- =====================================================
-- Migration: 002_add_rapport_annotations.sql
-- Date: 2025-01-16
-- Purpose: Ajouter système d'annotations pour rapports de commission
-- =====================================================

-- Table: rapport_annotations
-- Annotations sur les rapports de commission pour corrections/commentaires
CREATE TABLE IF NOT EXISTS rapport_annotations (
    id_annotation INT PRIMARY KEY AUTO_INCREMENT,
    rapport_id INT NOT NULL,
    annotateur_id INT NOT NULL,
    section VARCHAR(100) NOT NULL COMMENT 'Section du rapport annotée',
    ligne_debut INT,
    ligne_fin INT,
    texte_original TEXT,
    commentaire TEXT NOT NULL,
    type_annotation ENUM('correction', 'suggestion', 'remarque', 'validation') DEFAULT 'remarque',
    statut ENUM('en_attente', 'pris_en_compte', 'rejete', 'resolu') DEFAULT 'en_attente',
    priorite ENUM('basse', 'normale', 'haute', 'critique') DEFAULT 'normale',
    resolu_par INT,
    resolu_le DATETIME,
    reponse TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (rapport_id) REFERENCES rapports_etudiants(id_rapport) ON DELETE CASCADE,
    FOREIGN KEY (annotateur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE RESTRICT,
    FOREIGN KEY (resolu_par) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    
    INDEX idx_rapport (rapport_id),
    INDEX idx_annotateur (annotateur_id),
    INDEX idx_statut (statut),
    INDEX idx_type (type_annotation),
    INDEX idx_priorite (priorite)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Annotations et corrections sur les rapports de commission';

-- Table: rapport_fichiers_attaches
-- Fichiers attachés aux rapports (pièces justificatives, annexes)
CREATE TABLE IF NOT EXISTS rapport_fichiers_attaches (
    id_fichier_attache INT PRIMARY KEY AUTO_INCREMENT,
    rapport_id INT NOT NULL,
    type_fichier ENUM('annexe', 'piece_justificative', 'document_support', 'correction') NOT NULL,
    nom_fichier VARCHAR(255) NOT NULL,
    chemin_fichier VARCHAR(500) NOT NULL,
    taille_octets BIGINT NOT NULL,
    mime_type VARCHAR(100),
    hash_sha256 VARCHAR(64),
    description TEXT,
    upload_par INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (rapport_id) REFERENCES rapports_etudiants(id_rapport) ON DELETE CASCADE,
    FOREIGN KEY (upload_par) REFERENCES utilisateurs(id_utilisateur) ON DELETE RESTRICT,
    
    INDEX idx_rapport (rapport_id),
    INDEX idx_type (type_fichier),
    INDEX idx_hash (hash_sha256)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Fichiers attachés aux rapports de commission';

-- Table: rapport_versions
-- Historique des versions des rapports pour traçabilité
CREATE TABLE IF NOT EXISTS rapport_versions (
    id_version INT PRIMARY KEY AUTO_INCREMENT,
    rapport_id INT NOT NULL,
    numero_version VARCHAR(20) NOT NULL,
    contenu_json JSON NOT NULL COMMENT 'Snapshot complet du rapport',
    modifie_par INT NOT NULL,
    commentaire_version TEXT,
    date_version DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (rapport_id) REFERENCES rapports_etudiants(id_rapport) ON DELETE CASCADE,
    FOREIGN KEY (modifie_par) REFERENCES utilisateurs(id_utilisateur) ON DELETE RESTRICT,
    
    INDEX idx_rapport (rapport_id),
    INDEX idx_date (date_version),
    UNIQUE KEY uk_rapport_version (rapport_id, numero_version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Versions historiques des rapports de commission';

-- Table: rapport_validations
-- Processus de validation des rapports (workflow de validation multi-niveaux)
CREATE TABLE IF NOT EXISTS rapport_validations (
    id_validation INT PRIMARY KEY AUTO_INCREMENT,
    rapport_id INT NOT NULL,
    validateur_id INT NOT NULL,
    niveau_validation INT NOT NULL COMMENT '1=Chef département, 2=Directeur études, 3=VP',
    statut_validation ENUM('en_attente', 'valide', 'refuse', 'demande_correction') NOT NULL,
    commentaire TEXT,
    date_validation DATETIME,
    ordre_validation INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (rapport_id) REFERENCES rapports_etudiants(id_rapport) ON DELETE CASCADE,
    FOREIGN KEY (validateur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE RESTRICT,
    
    INDEX idx_rapport (rapport_id),
    INDEX idx_validateur (validateur_id),
    INDEX idx_statut (statut_validation),
    INDEX idx_niveau (niveau_validation),
    UNIQUE KEY uk_rapport_validateur (rapport_id, validateur_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Workflow de validation des rapports de commission';

-- Insérer dans suivi migrations
INSERT INTO migrations (migration_name, executed_at) 
VALUES ('002_add_rapport_annotations', NOW());
