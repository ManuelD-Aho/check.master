-- =====================================================
-- Migration: 010_add_documents_generes.sql
-- Date: 2025-01-16
-- Purpose: Gestion des documents générés automatiquement
-- =====================================================

-- Table: documents_templates
CREATE TABLE IF NOT EXISTS documents_templates (
    id_template INT PRIMARY KEY AUTO_INCREMENT,
    code_template VARCHAR(100) UNIQUE NOT NULL,
    nom_template VARCHAR(255) NOT NULL,
    description TEXT,
    type_document ENUM('pdf', 'word', 'excel', 'html', 'email') NOT NULL,
    categorie VARCHAR(50),
    contenu_template LONGTEXT NOT NULL,
    variables_disponibles JSON,
    engine ENUM('twig', 'blade', 'tcpdf', 'mpdf', 'phpword') DEFAULT 'twig',
    orientation ENUM('portrait', 'landscape'),
    format_papier VARCHAR(20) DEFAULT 'A4',
    header_template TEXT,
    footer_template TEXT,
    styles_css TEXT,
    version VARCHAR(20) DEFAULT '1.0',
    actif BOOLEAN DEFAULT TRUE,
    cree_par INT NOT NULL,
    modifie_par INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (cree_par) REFERENCES utilisateurs(id_utilisateur),
    FOREIGN KEY (modifie_par) REFERENCES utilisateurs(id_utilisateur),
    INDEX idx_code (code_template),
    INDEX idx_type (type_document),
    INDEX idx_categorie (categorie)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: documents_generes_historique
CREATE TABLE IF NOT EXISTS documents_generes_historique (
    id_document_genere INT PRIMARY KEY AUTO_INCREMENT,
    template_id INT NOT NULL,
    entite_type VARCHAR(50) NOT NULL COMMENT 'Type entité source (dossier, etudiant, etc)',
    entite_id INT NOT NULL,
    nom_fichier VARCHAR(255) NOT NULL,
    chemin_fichier VARCHAR(500) NOT NULL,
    taille_octets BIGINT,
    hash_sha256 VARCHAR(64),
    parametres_generation JSON,
    statut ENUM('genere', 'envoye', 'archive', 'supprime') DEFAULT 'genere',
    genere_par INT NOT NULL,
    date_generation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_expiration DATETIME,
    nombre_telechargements INT DEFAULT 0,
    derniere_lecture DATETIME,
    
    FOREIGN KEY (template_id) REFERENCES documents_templates(id_template),
    FOREIGN KEY (genere_par) REFERENCES utilisateurs(id_utilisateur),
    INDEX idx_template (template_id),
    INDEX idx_entite (entite_type, entite_id),
    INDEX idx_statut (statut),
    INDEX idx_hash (hash_sha256)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: documents_signatures_electroniques
CREATE TABLE IF NOT EXISTS documents_signatures_electroniques (
    id_signature INT PRIMARY KEY AUTO_INCREMENT,
    document_genere_id INT NOT NULL,
    signataire_id INT NOT NULL,
    role_signataire VARCHAR(100),
    ordre_signature INT NOT NULL,
    statut ENUM('en_attente', 'signe', 'refuse', 'expire') DEFAULT 'en_attente',
    date_demande DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_signature DATETIME,
    signature_data TEXT COMMENT 'Données cryptographiques',
    certificat_data TEXT,
    ip_signature VARCHAR(45),
    commentaire TEXT,
    
    FOREIGN KEY (document_genere_id) REFERENCES documents_generes_historique(id_document_genere) ON DELETE CASCADE,
    FOREIGN KEY (signataire_id) REFERENCES utilisateurs(id_utilisateur),
    INDEX idx_document (document_genere_id),
    INDEX idx_signataire (signataire_id),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO migrations (migration_name, executed_at) VALUES ('010_add_documents_generes', NOW());
