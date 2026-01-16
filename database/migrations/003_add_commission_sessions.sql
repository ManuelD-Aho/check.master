-- =====================================================
-- Migration: 003_add_commission_sessions.sql
-- Date: 2025-01-16
-- Purpose: Gestion avancée des sessions de commission
-- =====================================================

-- Table: sessions_commission_convocations
-- Convocations individuelles pour membres commission
CREATE TABLE IF NOT EXISTS sessions_commission_convocations (
    id_convocation INT PRIMARY KEY AUTO_INCREMENT,
    session_id INT NOT NULL,
    membre_id INT NOT NULL,
    date_envoi DATETIME NOT NULL,
    methode_envoi ENUM('email', 'sms', 'notification', 'courrier') NOT NULL,
    statut_lecture ENUM('non_lu', 'lu', 'accuse_reception') DEFAULT 'non_lu',
    date_lecture DATETIME,
    confirmation_presence ENUM('en_attente', 'present', 'absent', 'excuse') DEFAULT 'en_attente',
    date_confirmation DATETIME,
    motif_absence TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (session_id) REFERENCES sessions_commission(id_session_commission) ON DELETE CASCADE,
    FOREIGN KEY (membre_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    
    INDEX idx_session (session_id),
    INDEX idx_membre (membre_id),
    INDEX idx_statut (statut_lecture),
    INDEX idx_confirmation (confirmation_presence),
    UNIQUE KEY uk_session_membre (session_id, membre_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: sessions_commission_agendas
-- Ordre du jour détaillé des sessions
CREATE TABLE IF NOT EXISTS sessions_commission_agendas (
    id_agenda_item INT PRIMARY KEY AUTO_INCREMENT,
    session_id INT NOT NULL,
    ordre INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    type_item ENUM('presentation', 'deliberation', 'vote', 'information', 'divers') NOT NULL,
    dossier_id INT COMMENT 'Si item lié à un dossier',
    duree_estimee INT COMMENT 'Durée en minutes',
    heure_prevue TIME,
    heure_effective TIME,
    rapporteur_id INT,
    statut ENUM('en_attente', 'en_cours', 'termine', 'reporte') DEFAULT 'en_attente',
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (session_id) REFERENCES sessions_commission(id_session_commission) ON DELETE CASCADE,
    FOREIGN KEY (dossier_id) REFERENCES dossiers_etudiants(id_dossier) ON DELETE SET NULL,
    FOREIGN KEY (rapporteur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    
    INDEX idx_session (session_id),
    INDEX idx_ordre (ordre),
    INDEX idx_dossier (dossier_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: sessions_commission_votes
-- Résultats de votes pendant les sessions
CREATE TABLE IF NOT EXISTS sessions_commission_votes (
    id_vote INT PRIMARY KEY AUTO_INCREMENT,
    session_id INT NOT NULL,
    agenda_item_id INT,
    objet_vote VARCHAR(255) NOT NULL,
    type_vote ENUM('simple', 'secret', 'nominal') NOT NULL,
    nb_pour INT DEFAULT 0,
    nb_contre INT DEFAULT 0,
    nb_abstention INT DEFAULT 0,
    nb_presents INT NOT NULL,
    quorum_requis INT,
    quorum_atteint BOOLEAN DEFAULT TRUE,
    resultat ENUM('adopte', 'rejete', 'ajourne', 'invalide') NOT NULL,
    details_vote JSON COMMENT 'Détails si vote nominal',
    date_vote DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (session_id) REFERENCES sessions_commission(id_session_commission) ON DELETE CASCADE,
    FOREIGN KEY (agenda_item_id) REFERENCES sessions_commission_agendas(id_agenda_item) ON DELETE SET NULL,
    
    INDEX idx_session (session_id),
    INDEX idx_resultat (resultat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: sessions_commission_absences
-- Gestion des absences et remplacements
CREATE TABLE IF NOT EXISTS sessions_commission_absences (
    id_absence INT PRIMARY KEY AUTO_INCREMENT,
    session_id INT NOT NULL,
    membre_absent_id INT NOT NULL,
    remplac ant_id INT,
    type_absence ENUM('justifiee', 'non_justifiee', 'excuse') NOT NULL,
    motif TEXT,
    document_justificatif VARCHAR(500),
    approuve_par INT,
    date_approbation DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (session_id) REFERENCES sessions_commission(id_session_commission) ON DELETE CASCADE,
    FOREIGN KEY (membre_absent_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (remplacant_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    FOREIGN KEY (approuve_par) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    
    INDEX idx_session (session_id),
    INDEX idx_membre (membre_absent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: sessions_commission_documents
-- Documents de session (supports, présentations)
CREATE TABLE IF NOT EXISTS sessions_commission_documents (
    id_document_session INT PRIMARY KEY AUTO_INCREMENT,
    session_id INT NOT NULL,
    agenda_item_id INT,
    titre VARCHAR(255) NOT NULL,
    type_document ENUM('convocation', 'support', 'presentation', 'compte_rendu', 'annexe') NOT NULL,
    chemin_fichier VARCHAR(500) NOT NULL,
    taille_octets BIGINT,
    mime_type VARCHAR(100),
    upload_par INT NOT NULL,
    confidentialite ENUM('public', 'restreint', 'confidentiel') DEFAULT 'restreint',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (session_id) REFERENCES sessions_commission(id_session_commission) ON DELETE CASCADE,
    FOREIGN KEY (agenda_item_id) REFERENCES sessions_commission_agendas(id_agenda_item) ON DELETE SET NULL,
    FOREIGN KEY (upload_par) REFERENCES utilisateurs(id_utilisateur) ON DELETE RESTRICT,
    
    INDEX idx_session (session_id),
    INDEX idx_type (type_document)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insérer dans suivi migrations
INSERT INTO migrations (migration_name, executed_at) 
VALUES ('003_add_commission_sessions', NOW());
