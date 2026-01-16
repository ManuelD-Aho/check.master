-- =====================================================
-- Migration: 011_add_sessions_commission_participants.sql
-- Date: 2025-01-16
-- Purpose: Gestion détaillée des participants aux sessions
-- =====================================================

-- Table: participants_sessions_presences
CREATE TABLE IF NOT EXISTS participants_sessions_presences (
    id_presence INT PRIMARY KEY AUTO_INCREMENT,
    session_id INT NOT NULL,
    participant_id INT NOT NULL,
    type_participant ENUM('membre_commission', 'invite', 'observateur', 'rapporteur', 'secretaire') NOT NULL,
    statut_presence ENUM('present', 'absent', 'retard', 'depart_anticipe') DEFAULT 'present',
    heure_arrivee TIME,
    heure_depart TIME,
    duree_presence_minutes INT,
    justification_absence TEXT,
    signature_presence VARCHAR(500),
    verifie_par INT,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (session_id) REFERENCES sessions_commission(id_session_commission) ON DELETE CASCADE,
    FOREIGN KEY (participant_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (verifie_par) REFERENCES utilisateurs(id_utilisateur),
    INDEX idx_session (session_id),
    INDEX idx_participant (participant_id),
    INDEX idx_statut (statut_presence),
    UNIQUE KEY uk_session_participant (session_id, participant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: participants_interventions
CREATE TABLE IF NOT EXISTS participants_interventions (
    id_intervention INT PRIMARY KEY AUTO_INCREMENT,
    session_id INT NOT NULL,
    agenda_item_id INT,
    participant_id INT NOT NULL,
    type_intervention ENUM('presentation', 'question', 'reponse', 'commentaire', 'motion', 'point_ordre') NOT NULL,
    contenu TEXT NOT NULL,
    duree_secondes INT,
    heure_intervention TIME,
    enregistrement_audio VARCHAR(500),
    transcription TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (session_id) REFERENCES sessions_commission(id_session_commission) ON DELETE CASCADE,
    FOREIGN KEY (agenda_item_id) REFERENCES sessions_commission_agendas(id_agenda_item),
    FOREIGN KEY (participant_id) REFERENCES utilisateurs(id_utilisateur),
    INDEX idx_session (session_id),
    INDEX idx_participant (participant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: sessions_enregistrements
CREATE TABLE IF NOT EXISTS sessions_enregistrements (
    id_enregistrement INT PRIMARY KEY AUTO_INCREMENT,
    session_id INT NOT NULL,
    type_media ENUM('audio', 'video', 'screen_capture') NOT NULL,
    nom_fichier VARCHAR(255) NOT NULL,
    chemin_fichier VARCHAR(500) NOT NULL,
    duree_secondes INT,
    taille_octets BIGINT,
    format VARCHAR(50),
    qualite VARCHAR(50),
    confidentialite ENUM('public', 'membres_only', 'confidentiel') DEFAULT 'membres_only',
    transcription_disponible BOOLEAN DEFAULT FALSE,
    chemin_transcription VARCHAR(500),
    enregistre_par INT,
    date_enregistrement DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (session_id) REFERENCES sessions_commission(id_session_commission) ON DELETE CASCADE,
    FOREIGN KEY (enregistre_par) REFERENCES utilisateurs(id_utilisateur),
    INDEX idx_session (session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO migrations (migration_name, executed_at) VALUES ('011_add_sessions_commission_participants', NOW());
